<?php
/**
 * Translator widget in the nav menu (legacy Arkai 'headermenu' location).
 * Extracted from legacy-arkai-child-functions.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'wp_nav_menu_items', function ( $items, $args ) {
	if ( ! isset( $args->theme_location ) || $args->theme_location !== 'headermenu' ) {
		return $items;
	}

	if ( ! shortcode_exists( 'translator-revolution' ) ) {
		return $items;
	}

	$items .= '<li class="menu-item">' . do_shortcode( '[translator-revolution]' ) . '</li>';

	return $items;
}, 10, 2 );
