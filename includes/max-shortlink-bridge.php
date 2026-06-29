<?php
/**
 * Short links /max and /max_drslon — redirect before WP canonical rewrite.
 *
 * Clearfy redirect_manager matches REQUEST_URI exactly, so ?utm_* breaks /max
 * and WordPress sends users to a MAX-related project post instead of max.ru.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, string> Path slug => destination URL.
 */
function krv_max_shortlink_map(): array {
	return array(
		'/max'        => 'https://max.ru/u/f9LHodD0cOIqIRU6OuxH014jeF14zE2lE7BZGIDLzyY2ODo-T2Q8nwK9eno',
		'/max_drslon' => 'https://max.ru/join/aa3v8rWdS_5M0156Q3ulFCsfJGeCMThryOOY2SrlyJM',
	);
}

/**
 * Redirect /max requests (with or without query string) to max.ru profile.
 */
function krv_max_shortlink_redirect(): void {
	if ( is_admin() ) {
		return;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
	$path        = wp_parse_url( $request_uri, PHP_URL_PATH );

	if ( ! is_string( $path ) || $path === '' ) {
		return;
	}

	$slug = strtolower( rtrim( $path, '/' ) );
	$map  = krv_max_shortlink_map();

	if ( ! isset( $map[ $slug ] ) ) {
		return;
	}

	wp_redirect( $map[ $slug ], 301 ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- external max.ru profile.
	exit;
}

add_action( 'plugins_loaded', 'krv_max_shortlink_redirect', 1 );