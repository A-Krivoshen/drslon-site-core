<?php
/**
 * Plugin Name: DrSlon Site Core
 * Description: Compatibility layer for krivoshein.site legacy CPT, ACF fields and shortcodes moved out of the old Arkai child theme.
 * Version: 0.3.8
 * Author: Алексей Кривошеин
 * Text Domain: drslon-site-core
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * Update URI: false
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DRSLON_SITE_CORE_VERSION', '0.3.8' );
define( 'DRSLON_SITE_CORE_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Schedule a one-time rewrite flush after activation.
 * The flush itself runs on init, after CPT registration.
 */
register_activation_hook( __FILE__, function () {
	add_option( 'drslon_site_core_flush_rewrite', 1, '', 'no' );
} );

register_deactivation_hook( __FILE__, function () {
	flush_rewrite_rules();
} );

add_action( 'init', function () {
	if ( get_option( 'drslon_site_core_flush_rewrite' ) ) {
		flush_rewrite_rules();
		delete_option( 'drslon_site_core_flush_rewrite' );
	}
}, 20 );

require_once DRSLON_SITE_CORE_DIR . 'includes/ads-settings.php';
require_once DRSLON_SITE_CORE_DIR . 'includes/acf-options-sync.php';
require_once DRSLON_SITE_CORE_DIR . 'includes/max-shortlink-bridge.php';
require_once DRSLON_SITE_CORE_DIR . 'includes/service-page-registry.php';
require_once DRSLON_SITE_CORE_DIR . 'includes/icons/max-messenger.php';
require_once DRSLON_SITE_CORE_DIR . 'includes/helpers/shortcode-resolve.php';
require_once DRSLON_SITE_CORE_DIR . 'includes/assets-loader.php';
require_once DRSLON_SITE_CORE_DIR . 'includes/cache-purge-bridge.php';
require_once DRSLON_SITE_CORE_DIR . 'includes/telegram-comments-proxy.php';
require_once DRSLON_SITE_CORE_DIR . 'includes/price-list-acf.php';
require_once DRSLON_SITE_CORE_DIR . 'includes/price-list-widget.php';
require_once DRSLON_SITE_CORE_DIR . 'includes/contacts-topic-banner.php';
require_once DRSLON_SITE_CORE_DIR . 'includes/shortcodes/blog-shortcodes.php';
require_once DRSLON_SITE_CORE_DIR . 'includes/shortcodes/services-landing.php';
require_once DRSLON_SITE_CORE_DIR . 'includes/shortcodes/service-page-shell.php';

/**
 * Temporary legacy bridge.
 *
 * The old arkai-child theme stored site logic inside functions.php:
 * CPT, ACF local groups, partners, service showcase and shortcodes.
 * We load it only when arkai-child is NOT the active theme.
 *
 * Runs on after_setup_theme: by that time the theme's functions.php is
 * already loaded, so function_exists() is a reliable duplicate guard.
 */
add_action( 'after_setup_theme', function () {
	if ( get_stylesheet() === 'arkai-child' ) {
		return;
	}

	$legacy_file = DRSLON_SITE_CORE_DIR . 'includes/legacy-arkai-child-functions.php';

	if ( file_exists( $legacy_file ) && ! function_exists( 'krv_page_has_ui_shortcode' ) ) {
		require_once $legacy_file;
	}
}, 20 );
