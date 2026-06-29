<?php
/**
 * Shortcode resolution helpers for krivoshein.site service pages.
 *
 * Depends on krv_service_page_registry() and related functions from
 * includes/service-page-registry.php (must be loaded first).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inner tool shortcode tag for asset detection on service pages.
 */
function krv_service_page_nested_shortcode( ?int $page_id = null ): ?string {
	$config = krv_service_page_get_config( $page_id );

	if ( ! is_array( $config ) ) {
		return null;
	}

	$shortcode = $config['shortcode'] ?? '';

	return $shortcode !== '' ? (string) $shortcode : null;
}

/**
 * True when page content contains the tag or the registry maps the page to it.
 */
function krv_page_resolves_shortcode( string $tag, ?int $page_id = null ): bool {
	$tag = trim( $tag );

	if ( $tag === '' ) {
		return false;
	}

	$page_id = krv_service_page_resolve_page_id( $page_id );

	if ( ! $page_id ) {
		return false;
	}

	$post = get_post( $page_id );

	if ( $post instanceof WP_Post && $post->post_content !== '' && has_shortcode( $post->post_content, $tag ) ) {
		return true;
	}

	$config = krv_service_page_get_config( $page_id );

	return is_array( $config ) && ( $config['shortcode'] ?? '' ) === $tag;
}