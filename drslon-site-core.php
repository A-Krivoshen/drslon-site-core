<?php
/**
 * Plugin Name: DrSlon Site Core
 * Description: Site core for krivoshein.site: CPT, ACF fields, shortcodes, ads and legacy compatibility moved out of the old Arkai child theme.
 * Version: 0.2.0
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

define( 'DRSLON_SITE_CORE_VERSION', '0.2.0' );
define( 'DRSLON_SITE_CORE_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Schedule a one-time rewrite flush after activation.
 * The flush itself runs on init, right after CPT registration (see includes/cpt.php).
 */
register_activation_hook( __FILE__, function () {
	add_option( 'drslon_site_core_flush_rewrite', 1, '', 'no' );
} );

register_deactivation_hook( __FILE__, function () {
	flush_rewrite_rules();
} );

/**
 * Load site modules.
 *
 * The old arkai-child theme keeps the same logic inside its own functions.php,
 * so we skip loading when it is active (by stylesheet and by function sentinel).
 *
 * Runs on after_setup_theme: by that time the theme's functions.php is already
 * loaded, so function_exists() is a reliable duplicate guard.
 */
add_action( 'after_setup_theme', function () {
	if ( get_stylesheet() === 'arkai-child' ) {
		return;
	}

	if ( function_exists( 'krv_page_has_ui_shortcode' ) ) {
		return;
	}

	$modules = [
		'includes/helpers.php',
		'includes/seo.php',
		'includes/ads-settings.php',
		'includes/ads.php',
		'includes/comments.php',
		'includes/site-tweaks.php',
		'includes/cpt.php',
		'includes/acf-fields.php',
		'includes/shortcodes/translator-menu.php',
		'includes/shortcodes/services-landing.php',
		'includes/shortcodes/clients-grid.php',
		'includes/shortcodes/partners-grid.php',
		'includes/shortcodes/services-pages-showcase.php',
	];

	foreach ( $modules as $module ) {
		$path = DRSLON_SITE_CORE_DIR . $module;

		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
}, 20 );
