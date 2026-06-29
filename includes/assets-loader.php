<?php
/**
 * Conditionally enqueue shortcode UI stylesheets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', function () {
	if ( is_admin() || ! function_exists( 'krv_page_has_ui_shortcode' ) ) {
		return;
	}

	$plugin_file = DRSLON_SITE_CORE_DIR . 'drslon-site-core.php';
	$version     = DRSLON_SITE_CORE_VERSION;

	$styles = [
		'drslon-services-landing' => [
			'file'      => 'assets/css/services-landing.css',
			'shortcode' => [ 'krv_services_landing' ],
		],
		'drslon-clients-grid'     => [
			'file'      => 'assets/css/clients-grid.css',
			'shortcode' => [ 'krv_clients_grid' ],
		],
		'drslon-partners-grid'    => [
			'file'      => 'assets/css/partners-grid.css',
			'shortcode' => [ 'krv_partners_grid' ],
		],
		'drslon-services-showcase' => [
			'file'      => 'assets/css/services-showcase.css',
			'shortcode' => [ 'krv_services_pages_showcase' ],
		],
		'drslon-price-list-widget' => [
			'file'      => 'assets/css/price-list-widget.css',
			'shortcode' => [ 'krv_price_list' ],
		],
	];

	foreach ( $styles as $handle => $config ) {
		if ( ! krv_page_has_ui_shortcode( $config['shortcode'] ) ) {
			continue;
		}

		wp_enqueue_style(
			$handle,
			plugins_url( $config['file'], $plugin_file ),
			[],
			$version
		);
	}
}, 20 );