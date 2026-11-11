#!/usr/bin/env bash

set -uo pipefail

MODE="check"
WORDPRESS_ROOT="${WORDPRESS_ROOT:-/var/www/krivoshein.site/htdocs}"

usage() {
	cat <<'EOF'
Usage:
  apply-third-party-hotfixes.sh --check [--wordpress-root PATH]
  apply-third-party-hotfixes.sh --apply [--wordpress-root PATH]

Modes:
  --check  Verify plugin versions and patch state without changing files.
  --apply  Apply missing compatible patches, then run PHP lint.
EOF
}

while [ "$#" -gt 0 ]; do
	case "$1" in
		--check)
			MODE="check"
			;;
		--apply)
			MODE="apply"
			;;
		--wordpress-root)
			if [ "$#" -lt 2 ]; then
				usage >&2
				exit 2
			fi
			WORDPRESS_ROOT="$2"
			shift
			;;
		-h|--help)
			usage
			exit 0
			;;
		*)
			printf 'Unknown argument: %s\n' "$1" >&2
			usage >&2
			exit 2
			;;
	esac
	shift
done

for command_name in patch php cp mktemp rm stat chmod; do
	if ! command -v "$command_name" >/dev/null 2>&1; then
		printf '[ERROR] Required command is unavailable: %s\n' "$command_name" >&2
		exit 2
	fi
done

if [ "${EUID}" -eq 0 ] && ! command -v chown >/dev/null 2>&1; then
	printf '[ERROR] Required command is unavailable: chown\n' >&2
	exit 2
fi

SCRIPT_DIR="$(CDPATH= cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_ROOT="${WORDPRESS_ROOT%/}/wp-content/plugins"
PATCH_ROOT="${SCRIPT_DIR}/patches"
FAILURES=0
MISSING=0
CHANGES=0
APPLY_ERROR=""
declare -A HOTFIX_STATE=()
declare -a BACKUP_FILES=()
declare -a TARGET_FILES=()
declare -a BACKUP_LABELS=()

read_plugin_version() {
	php -r '
		$file = $argv[1];
		$contents = @file_get_contents( $file );
		if ( false === $contents || ! preg_match( "/^[ \\t\\/*#@]*Version:\\s*(.+)$/mi", $contents, $matches ) ) {
			exit( 1 );
		}
		echo trim( $matches[1] );
	' "$1"
}

record_failure() {
	printf '[ERROR] %s\n' "$1" >&2
	FAILURES=$((FAILURES + 1))
}

inspect_hotfix() {
	local label="$1"
	local plugin_slug="$2"
	local expected_version="$3"
	local main_relative="$4"
	local target_relative="$5"
	local patch_name="$6"
	local plugin_dir="${PLUGIN_ROOT}/${plugin_slug}"
	local main_file="${plugin_dir}/${main_relative}"
	local target_file="${plugin_dir}/${target_relative}"
	local patch_file="${PATCH_ROOT}/${patch_name}"
	local actual_version

	if [ ! -f "$main_file" ] || [ ! -f "$target_file" ] || [ ! -f "$patch_file" ]; then
		record_failure "${label}: required plugin or patch file is missing."
		return
	fi

	if ! actual_version="$(read_plugin_version "$main_file")"; then
		record_failure "${label}: could not read the installed plugin version."
		return
	fi

	if [ "$actual_version" != "$expected_version" ]; then
		record_failure "${label}: installed version ${actual_version}, expected ${expected_version}; review the upstream update before changing files."
		return
	fi

	if patch --dry-run --forward --batch --silent --fuzz=0 --no-backup-if-mismatch -p1 -d "$plugin_dir" < "$patch_file" >/dev/null 2>&1; then
		HOTFIX_STATE["$plugin_slug"]="pending"
		MISSING=$((MISSING + 1))
		printf '[PENDING] %s %s: compatible hotfix is not installed.\n' "$label" "$actual_version"
		return
	fi

	if patch --dry-run --reverse --batch --silent --fuzz=0 --no-backup-if-mismatch -p1 -d "$plugin_dir" < "$patch_file" >/dev/null 2>&1; then
		if php -l "$target_file" >/dev/null; then
			HOTFIX_STATE["$plugin_slug"]="installed"
			printf '[OK] %s %s: hotfix is installed and PHP lint passes.\n' "$label" "$actual_version"
		else
			record_failure "${label}: hotfix is present, but PHP lint failed."
		fi
		return
	fi

	record_failure "${label}: source does not match either the original or patched state."
}

restore_all_backups() {
	local restore_failed=0
	local index

	for index in "${!BACKUP_FILES[@]}"; do
		if cp -p "${BACKUP_FILES[$index]}" "${TARGET_FILES[$index]}"; then
			rm -f "${BACKUP_FILES[$index]}"
			printf '[ROLLED BACK] %s\n' "${BACKUP_LABELS[$index]}"
		else
			printf '[ERROR] Could not restore %s; backup retained at %s\n' \
				"${BACKUP_LABELS[$index]}" "${BACKUP_FILES[$index]}" >&2
			restore_failed=1
		fi
	done

	return "$restore_failed"
}

cleanup_backups() {
	local backup_file

	for backup_file in "${BACKUP_FILES[@]}"; do
		if ! rm -f "$backup_file"; then
			printf '[WARN] Applied patch, but could not remove temporary backup: %s\n' "$backup_file" >&2
		fi
	done
}

apply_hotfix() {
	local label="$1"
	local plugin_slug="$2"
	local expected_version="$3"
	local main_relative="$4"
	local target_relative="$5"
	local patch_name="$6"
	local plugin_dir="${PLUGIN_ROOT}/${plugin_slug}"
	local target_file="${plugin_dir}/${target_relative}"
	local patch_file="${PATCH_ROOT}/${patch_name}"
	local backup_file
	local original_mode
	local original_owner

	if [ "${HOTFIX_STATE[$plugin_slug]:-}" != "pending" ]; then
		return 0
	fi

	if ! original_mode="$(stat -c '%a' "$target_file")" || ! original_owner="$(stat -c '%u:%g' "$target_file")"; then
		APPLY_ERROR="${label}: could not read original file metadata."
		return 1
	fi

	backup_file="$(mktemp "${TMPDIR:-/tmp}/drslon-hotfix.XXXXXX")"
	if [ -z "$backup_file" ]; then
		APPLY_ERROR="${label}: could not create a temporary backup path."
		return 1
	fi

	if ! cp -p "$target_file" "$backup_file"; then
		rm -f "$backup_file"
		APPLY_ERROR="${label}: could not create a temporary backup."
		return 1
	fi

	BACKUP_FILES+=( "$backup_file" )
	TARGET_FILES+=( "$target_file" )
	BACKUP_LABELS+=( "${label} ${expected_version}: ${target_relative}" )

	if ! patch --forward --batch --silent --fuzz=0 --no-backup-if-mismatch -p1 -d "$plugin_dir" < "$patch_file"; then
		APPLY_ERROR="${label}: patch command failed."
		return 1
	fi

	if ! chmod "$original_mode" "$target_file"; then
		APPLY_ERROR="${label}: could not restore the original file mode."
		return 1
	fi

	if [ "${EUID}" -eq 0 ] && ! chown "$original_owner" "$target_file"; then
		APPLY_ERROR="${label}: could not restore the original file owner."
		return 1
	fi

	if ! php -l "$target_file" >/dev/null; then
		APPLY_ERROR="${label}: PHP lint failed after patching."
		return 1
	fi

	if ! patch --dry-run --reverse --batch --silent --fuzz=0 --no-backup-if-mismatch -p1 -d "$plugin_dir" < "$patch_file" >/dev/null 2>&1; then
		APPLY_ERROR="${label}: patched file failed the final state verification."
		return 1
	fi

	CHANGES=$((CHANGES + 1))
	printf '[PATCHED] %s %s: %s\n' "$label" "$expected_version" "$target_relative"
	return 0
}

run_all_hotfixes() {
	local callback="$1"

	"$callback" \
		"Zen Feed" \
		"mihdan-mailru-pulse-feed" \
		"0.8.5" \
		"mihdan-mailru-pulse-feed.php" \
		"src/class-main.php" \
		"mihdan-mailru-pulse-feed-0.8.5-null-video-parent.patch" || return 1

	"$callback" \
		"WP Fastest Cache Premium" \
		"wp-fastest-cache-premium" \
		"1.7.7" \
		"wpFastestCachePremium.php" \
		"pro/library/lazy-load.php" \
		"wp-fastest-cache-premium-1.7.7-lazy-load-regex.patch" || return 1
}

run_all_hotfixes inspect_hotfix

if [ "$FAILURES" -gt 0 ]; then
	printf '\nHotfix preflight failed for %d item(s); no files were changed.\n' "$FAILURES" >&2
	exit 1
fi

if [ "$MODE" = "check" ]; then
	if [ "$MISSING" -gt 0 ]; then
		printf '\n%d compatible hotfix(es) are missing; run with --apply.\n' "$MISSING" >&2
		exit 1
	fi

	printf '\nAll third-party hotfixes are installed and valid.\n'
	exit 0
fi

if ! run_all_hotfixes apply_hotfix; then
	record_failure "$APPLY_ERROR"
	if ! restore_all_backups; then
		record_failure "Rollback was incomplete; retained backup paths are listed above."
	fi
	printf '\nHotfix application failed.\n' >&2
	exit 1

fi

cleanup_backups

if [ "$CHANGES" -gt 0 ]; then
	printf '\nApplied %d hotfix(es). Clear page caches and run the feed/page smoke tests.\n' "$CHANGES"
else
	printf '\nAll third-party hotfixes are installed and valid.\n'
fi
