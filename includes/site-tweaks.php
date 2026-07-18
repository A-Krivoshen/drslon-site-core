<?php
/**
 * Misc site tweaks: views counter, reading time, author slug cleanup,
 * code blocks, legacy redirect, update/unzip preferences.
 * Extracted from legacy-arkai-child-functions.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** =========================
 *  6) Other site tweaks
 *  ========================= */

/** Force PclZip instead of ZipArchive (legacy hosting workaround). */
add_filter( 'unzip_file_use_ziparchive', '__return_false' );

/**
 * Disable auto-updates for own themes only.
 * They have no update server anyway; this keeps per-theme settings
 * (e.g. security updates for default themes) untouched.
 */
add_filter( 'auto_update_theme', function ( $update, $item ) {
	$own_themes = [ 'arkai', 'arkai-child', 'drslon-blog-theme' ];

	if ( isset( $item->theme ) && in_array( $item->theme, $own_themes, true ) ) {
		return false;
	}

	return $update;
}, 10, 2 );

/**
 * Views counter.
 *
 * Old stack: arkaiSetPostViews() from the Arkai parent theme did the counting.
 * New stack: we count here into the same 'arkai_post_views' meta key,
 * which the drslon-blog-theme reads via the drslon_post_views shortcode.
 */
add_action( 'wp_head', function () {
	if ( ! ( is_singular( 'post' ) || is_singular( 'project' ) ) ) {
		return;
	}

	global $post;
	$post_id = $post ? (int) $post->ID : 0;

	if ( ! $post_id ) {
		return;
	}

	if ( function_exists( 'arkaiSetPostViews' ) ) {
		arkaiSetPostViews( $post_id );
		return;
	}

	$count = (int) get_post_meta( $post_id, 'arkai_post_views', true );
	update_post_meta( $post_id, 'arkai_post_views', $count + 1 );
} );

/**
 * Reading time (legacy name kept for template compatibility).
 * Word count is Cyrillic-safe: str_word_count() does not count Russian words.
 */
if ( ! function_exists( 'arkaiReadingTime' ) ) {
	function arkaiReadingTime() {
		global $post;

		if ( ! $post ) {
			return '—';
		}

		$content = wp_strip_all_tags( (string) $post->post_content );
		preg_match_all( '/[\p{L}\p{N}_-]+/u', $content, $matches );

		$words   = ! empty( $matches[0] ) ? count( $matches[0] ) : 0;
		$minutes = (int) floor( $words / 120 );

		if ( $minutes < 1 ) {
			$minutes = 1;
		}

		if ( $minutes === 1 ) {
			return $minutes . ' минута чтения';
		}

		if ( $minutes >= 2 && $minutes <= 4 ) {
			return $minutes . ' минуты чтения';
		}

		return $minutes . ' минут чтения';
	}
}

/** Clean author slug "-2" */
add_action( 'profile_update', function ( $user_id ) {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return;
	}

	$user_slug  = $user->user_nicename;
	$clean_slug = preg_replace( '/(-2)+$/', '', $user_slug );

	if ( $clean_slug !== $user_slug ) {
		wp_update_user( [
			'ID'            => $user_id,
			'user_nicename' => $clean_slug,
		] );
	}
} );

/** Code blocks: set default language + hljs */
add_action( 'wp_enqueue_scripts', function () {
	if ( is_admin() || ! is_singular( 'post' ) ) {
		return;
	}

	wp_register_script( 'code-auto-lang', '', [], null, true );
	wp_enqueue_script( 'code-auto-lang' );

	$js = <<<JS
document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('pre.wp-block-code code:not([class])').forEach(function(c) {
		c.classList.add('language-bash');
	});

	if (window.hljs) {
		document.querySelectorAll('pre code').forEach(function(el) {
			hljs.highlightElement(el);
		});
	}
});
JS;

	wp_add_inline_script( 'code-auto-lang', $js );
} );

/** Legacy redirect: old calculator URL */
add_action( 'template_redirect', function () {
	if ( is_admin() ) {
		return;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] )
		? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
		: '';

	$req = trim( (string) parse_url( $request_uri, PHP_URL_PATH ), '/' );

	if ( $req === 'kalkulyator-setevyh-masok-ip' ) {
		wp_redirect( home_url( '/kalkulyator-setevyh-masok/' ), 301 );
		exit;
	}
} );
