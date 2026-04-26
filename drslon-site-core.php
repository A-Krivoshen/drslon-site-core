<?php
/**
 * Plugin Name: DrSlon Site Core
 * Description: Compatibility layer for krivoshein.site legacy CPT, ACF fields and shortcodes moved out of the old Arkai child theme.
 * Version: 0.1.0
 * Author: Алексей Кривошеин
 * Text Domain: drslon-site-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DRSLON_SITE_CORE_VERSION', '0.1.0' );
define( 'DRSLON_SITE_CORE_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Temporary legacy bridge.
 *
 * The old arkai-child theme stored site logic inside functions.php:
 * CPT, ACF local groups, partners, service showcase and shortcodes.
 * We load it only when arkai-child is NOT the active theme.
 */
add_action( 'plugins_loaded', function () {
	if ( get_stylesheet() === 'arkai-child' ) {
		return;
	}

	$legacy_file = DRSLON_SITE_CORE_DIR . 'includes/legacy-arkai-child-functions.php';

	if ( file_exists( $legacy_file ) && ! function_exists( 'krv_page_has_ui_shortcode' ) ) {
		require_once $legacy_file;
	}
}, 1 );
