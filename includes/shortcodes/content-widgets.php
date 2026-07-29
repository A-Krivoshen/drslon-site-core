<?php
/**
 * Content page widgets as shortcodes (moved out of post HTML + inline CSS).
 *
 * [krv_contact_block]  — /contacts/
 * [krv_resume]         — /resume/
 * [krv_consult]        — /konsultatsii/
 *
 * Light CSS: assets/css/content-*.css (enqueued via assets-loader).
 * Dark: theme 10-theme-dark.css (single owner for day/night).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load a shortcode HTML partial from includes/shortcodes/partials/.
 */
function krv_content_widget_partial( string $name ): string {
	$name = preg_replace( '/[^a-z0-9_-]/', '', strtolower( $name ) );
	$path = DRSLON_SITE_CORE_DIR . 'includes/shortcodes/partials/' . $name . '.html';

	if ( ! is_readable( $path ) ) {
		return '';
	}

	$html = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local partial.
	if ( ! is_string( $html ) || $html === '' ) {
		return '';
	}

	/**
	 * Filter rendered content-widget HTML before shortcode returns it.
	 *
	 * @param string $html Partial HTML.
	 * @param string $name Partial name (contacts|resume|consult).
	 */
	return (string) apply_filters( 'krv_content_widget_html', $html, $name );
}

/**
 * Shortcode: contact block.
 */
function krv_contact_block_shortcode( $atts = [] ): string {
	return krv_content_widget_partial( 'contacts' );
}
add_shortcode( 'krv_contact_block', 'krv_contact_block_shortcode' );

/**
 * Shortcode: resume widget.
 */
function krv_resume_shortcode( $atts = [] ): string {
	return krv_content_widget_partial( 'resume' );
}
add_shortcode( 'krv_resume', 'krv_resume_shortcode' );

/**
 * Shortcode: consultations widget.
 */
function krv_consult_shortcode( $atts = [] ): string {
	return krv_content_widget_partial( 'consult' );
}
add_shortcode( 'krv_consult', 'krv_consult_shortcode' );
// Alias for readability.
add_shortcode( 'krv_consultations', 'krv_consult_shortcode' );
