<?php
/**
 * Cache-independent public view beacon for posts and projects.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** The companion plugin's server-render counter is incompatible with page caches. */
add_action( 'plugins_loaded', function () {
	if ( get_stylesheet() !== 'arkai-child' ) {
		remove_action( 'wp_head', 'arkai_track_postgrid_views' );
	}
}, 100 );

/**
 * Reject obvious crawlers and automated page checks.
 */
function krv_view_beacon_is_bot(): bool {
	$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] )
		? substr( (string) wp_unslash( $_SERVER['HTTP_USER_AGENT'] ), 0, 512 )
		: '';

	if ( $user_agent === '' ) {
		return true;
	}

	return (bool) preg_match(
		'/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|telegrambot|whatsapp|yandex(?:bot|images)|baiduspider|duckduckbot|applebot|semrush|ahrefs|mj12bot|headlesschrome|lighthouse|pagespeed|uptimerobot|pingdom/i',
		$user_agent
	);
}

/**
 * Hash the visitor address with a site secret; the raw address is never stored.
 */
function krv_view_beacon_visitor_hash( int $post_id ): string {
	$remote_address = isset( $_SERVER['REMOTE_ADDR'] )
		? (string) wp_unslash( $_SERVER['REMOTE_ADDR'] )
		: '';

	if ( ! filter_var( $remote_address, FILTER_VALIDATE_IP ) ) {
		return '';
	}

	return hash_hmac( 'sha256', $post_id . '|' . $remote_address, wp_salt( 'auth' ) );
}

/**
 * Build a cache-safe post token for the public beacon.
 */
function krv_view_beacon_token( int $post_id ): string {
	return hash_hmac( 'sha256', (string) $post_id, wp_salt( 'nonce' ) );
}

/**
 * Require the browser to submit the beacon from this site's origin.
 */
function krv_view_beacon_has_valid_source(): bool {
	$expected = wp_parse_url( home_url( '/' ) );
	$source   = '';

	if ( ! empty( $_SERVER['HTTP_ORIGIN'] ) ) {
		$source = (string) wp_unslash( $_SERVER['HTTP_ORIGIN'] );
	} elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		$source = (string) wp_unslash( $_SERVER['HTTP_REFERER'] );
	}

	if ( $source === '' || ! is_array( $expected ) ) {
		return false;
	}

	$actual = wp_parse_url( $source );

	if ( ! is_array( $actual ) || empty( $expected['host'] ) || empty( $actual['host'] ) ) {
		return false;
	}

	$expected_scheme = strtolower( (string) ( $expected['scheme'] ?? '' ) );
	$actual_scheme   = strtolower( (string) ( $actual['scheme'] ?? '' ) );
	$expected_host   = strtolower( (string) $expected['host'] );
	$actual_host     = strtolower( (string) $actual['host'] );
	$expected_port   = (int) ( $expected['port'] ?? 0 );
	$actual_port     = (int) ( $actual['port'] ?? 0 );

	return $actual_scheme === $expected_scheme
		&& $actual_host === $expected_host
		&& $actual_port === $expected_port;
}

/**
 * Claim the visitor throttle while holding a database advisory lock.
 */
function krv_view_beacon_claim_throttle( string $visitor_hash ): bool {
	global $wpdb;

	$throttle_key = 'krv_view_' . substr( $visitor_hash, 0, 40 );
	$lock_name    = 'drslon_view_rate_' . substr( $visitor_hash, 0, 40 );
	$has_lock     = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 1)', $lock_name ) );

	if ( 1 !== $has_lock ) {
		return false;
	}

	try {
		if ( false !== get_transient( $throttle_key ) ) {
			return false;
		}

		return set_transient( $throttle_key, 1, HOUR_IN_SECONDS );
	} finally {
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock_name ) );
	}
}

/**
 * Normalize duplicate rows and optionally increment the canonical value.
 */
function krv_normalize_post_view_meta( int $post_id, bool $increment ): bool {
	global $wpdb;

	$meta_key  = 'arkai_post_views';
	$lock_name = 'drslon_view_' . (int) $wpdb->blogid . '_' . $post_id;
	$has_lock  = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 1)', $lock_name ) );

	if ( 1 !== $has_lock ) {
		return false;
	}

	try {
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_id, meta_value
				FROM {$wpdb->postmeta}
				WHERE post_id = %d AND meta_key = %s
				ORDER BY meta_id ASC",
				$post_id,
				$meta_key
			),
			ARRAY_A
		);

		if ( empty( $rows ) ) {
			return ! $increment || false !== add_post_meta( $post_id, $meta_key, 1, true );
		}

		$keep_id = (int) $rows[0]['meta_id'];
		$value   = 0;

		foreach ( $rows as $row ) {
			$value = max( $value, absint( $row['meta_value'] ) );
		}

		if ( $increment ) {
			++$value;
		}

		$updated = $wpdb->update(
			$wpdb->postmeta,
			array( 'meta_value' => (string) $value ),
			array( 'meta_id' => $keep_id ),
			array( '%s' ),
			array( '%d' )
		);

		if ( false === $updated ) {
			return false;
		}

		if ( count( $rows ) > 1 ) {
			$deleted = $wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$wpdb->postmeta}
					WHERE post_id = %d AND meta_key = %s AND meta_id <> %d",
					$post_id,
					$meta_key,
					$keep_id
				)
			);

			if ( false === $deleted ) {
				return false;
			}
		}

		wp_cache_delete( $post_id, 'post_meta' );
		return true;
	} finally {
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock_name ) );
	}
}

/**
 * Increment the canonical legacy meta key without a read-modify-write race.
 */
function krv_increment_post_view_atomic( int $post_id ): bool {
	return krv_normalize_post_view_meta( $post_id, true );
}

/** Collapse legacy duplicate rows once, preserving the highest stored value. */
function krv_deduplicate_post_view_meta(): void {
	if ( get_option( 'krv_view_meta_deduplicated_v1' ) ) {
		return;
	}

	global $wpdb;

	$post_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT post_id
			FROM {$wpdb->postmeta}
			WHERE meta_key = %s
			GROUP BY post_id
			HAVING COUNT(*) > 1",
			'arkai_post_views'
		)
	);

	if ( $wpdb->last_error !== '' ) {
		return;
	}

	$complete = true;

	foreach ( $post_ids as $post_id ) {
		if ( ! krv_normalize_post_view_meta( (int) $post_id, false ) ) {
			$complete = false;
		}
	}

	if ( $complete ) {
		update_option( 'krv_view_meta_deduplicated_v1', 1, false );
	}
}

add_action( 'init', 'krv_deduplicate_post_view_meta', 25 );

/**
 * Handle the public, throttled AJAX beacon. A nonce is deliberately omitted
 * because post HTML can remain in full-page caches longer than a nonce lives.
 */
function krv_handle_view_beacon(): void {
	nocache_headers();

	$request_method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) : '';

	if ( $request_method !== 'POST' ) {
		status_header( 405 );
		exit;
	}

	if ( is_user_logged_in() || krv_view_beacon_is_bot() ) {
		status_header( 204 );
		exit;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- public throttled analytics beacon.
	$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
	$post    = $post_id ? get_post( $post_id ) : null;

	if (
		! $post instanceof WP_Post ||
		$post->post_status !== 'publish' ||
		! in_array( $post->post_type, array( 'post', 'project' ), true )
	) {
		status_header( 400 );
		exit;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- cache-safe HMAC replaces an expiring nonce.
	$token = isset( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : '';

	if ( ! krv_view_beacon_has_valid_source() || ! hash_equals( krv_view_beacon_token( $post_id ), $token ) ) {
		status_header( 403 );
		exit;
	}

	$visitor_hash = krv_view_beacon_visitor_hash( $post_id );

	if ( $visitor_hash === '' ) {
		status_header( 400 );
		exit;
	}

	if ( ! krv_view_beacon_claim_throttle( $visitor_hash ) ) {
		status_header( 204 );
		exit;
	}

	if ( ! krv_increment_post_view_atomic( $post_id ) ) {
		delete_transient( 'krv_view_' . substr( $visitor_hash, 0, 40 ) );
		status_header( 503 );
		exit;
	}

	status_header( 204 );
	exit;
}

add_action( 'wp_ajax_nopriv_krv_count_view', 'krv_handle_view_beacon' );
add_action( 'wp_ajax_krv_count_view', 'krv_handle_view_beacon' );

/**
 * Enqueue the beacon only for public post/project singular requests.
 */
function krv_enqueue_view_beacon(): void {
	if (
		is_admin() ||
		is_user_logged_in() ||
		get_stylesheet() === 'arkai-child' ||
		! ( is_singular( 'post' ) || is_singular( 'project' ) )
	) {
		return;
	}

	$post_id = get_queried_object_id();
	$post    = $post_id ? get_post( $post_id ) : null;

	if ( ! $post instanceof WP_Post || $post->post_status !== 'publish' ) {
		return;
	}

	$relative_path = 'assets/js/view-counter.js';
	$path          = DRSLON_SITE_CORE_DIR . $relative_path;

	if ( ! file_exists( $path ) ) {
		return;
	}

	$handle = 'drslon-view-counter';

	wp_enqueue_script(
		$handle,
		plugins_url( $relative_path, DRSLON_SITE_CORE_DIR . 'drslon-site-core.php' ),
		array(),
		(string) filemtime( $path ),
		true
	);

	wp_add_inline_script(
		$handle,
		'window.drslonViewBeacon=' . wp_json_encode(
			array(
				'url'     => admin_url( 'admin-ajax.php' ),
				'action'  => 'krv_count_view',
				'post_id' => (int) $post_id,
				'token'   => krv_view_beacon_token( (int) $post_id ),
			)
		) . ';',
		'before'
	);
}

add_action( 'wp_enqueue_scripts', 'krv_enqueue_view_beacon', 30 );
