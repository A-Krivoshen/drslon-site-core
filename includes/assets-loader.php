<?php
/**
 * Conditionally enqueue shortcode UI stylesheets.
 *
 * When 2+ UI styles are needed on a page, serve one combined bundle
 * (plus dark overrides) to cut waterfall requests without changing CSS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_admin() || ! function_exists( 'krv_page_has_ui_shortcode' ) ) {
			return;
		}

		$plugin_file = DRSLON_SITE_CORE_DIR . 'drslon-site-core.php';

		$styles = [
			'drslon-services-landing'  => [
				'file'      => 'assets/css/services-landing.css',
				'shortcode' => [ 'krv_services_landing' ],
			],
			'drslon-clients-grid'      => [
				'file'      => 'assets/css/clients-grid.css',
				'shortcode' => [ 'krv_clients_grid' ],
			],
			'drslon-partners-grid'     => [
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
			'drslon-price-list-widget-js' => [
				'file'      => 'assets/js/price-list-widget.js',
				'shortcode' => [ 'krv_price_list' ],
				'type'      => 'script',
			],
			'drslon-service-page-shell' => [
				'file'      => 'assets/css/service-page-shell.css',
				'shortcode' => [ 'krv_service_page' ],
			],
			// Content widgets (ex-inline post HTML); dark lives in theme 10-theme-dark.css.
			'drslon-content-contacts' => [
				'file'      => 'assets/css/content-contacts.css',
				'shortcode' => [ 'krv_contact_block' ],
			],
			'drslon-content-resume'   => [
				'file'      => 'assets/css/content-resume.css',
				'shortcode' => [ 'krv_resume' ],
			],
			'drslon-content-consult'  => [
				'file'      => 'assets/css/content-consult.css',
				'shortcode' => [ 'krv_consult', 'krv_consultations' ],
			],
		];

		$assets = [
			'drslon-services-landing'     => [ 'type' => 'style' ],
			'drslon-clients-grid'         => [ 'type' => 'style' ],
			'drslon-partners-grid'        => [ 'type' => 'style' ],
			'drslon-services-showcase'    => [ 'type' => 'style' ],
			'drslon-price-list-widget'    => [ 'type' => 'style' ],
			'drslon-price-list-widget-js' => [ 'type' => 'script' ],
			'drslon-service-page-shell'   => [ 'type' => 'style' ],
			'drslon-content-contacts'     => [ 'type' => 'style' ],
			'drslon-content-resume'       => [ 'type' => 'style' ],
			'drslon-content-consult'      => [ 'type' => 'style' ],
		];

		$style_queue = [];
		$any_ui      = false;

		foreach ( $assets as $handle => $meta ) {
			if ( ! isset( $styles[ $handle ] ) ) {
				continue;
			}

			$config = $styles[ $handle ];

			if ( ! krv_page_has_ui_shortcode( $config['shortcode'] ) ) {
				continue;
			}

			$path = DRSLON_SITE_CORE_DIR . $config['file'];

			if ( ! file_exists( $path ) ) {
				continue;
			}

			$url     = plugins_url( $config['file'], $plugin_file );
			$version = (string) filemtime( $path );

			if ( $meta['type'] === 'script' ) {
				wp_enqueue_script( $handle, $url, [], $version, true );
				$any_ui = true;
				continue;
			}

			$style_queue[] = [
				'handle' => $handle,
				'file'   => $config['file'],
				'path'   => $path,
				'url'    => $url,
				'ver'    => $version,
			];
			$any_ui = true;
		}

		// Dark overrides for shortcode UIs (html[data-theme=dark] from theme).
		$dark_rel  = 'assets/css/krv-ui-dark.css';
		$dark_path = DRSLON_SITE_CORE_DIR . $dark_rel;
		$dark_ok   = $any_ui && file_exists( $dark_path );

		if ( count( $style_queue ) >= 2 ) {
			$bundle = drslon_core_ensure_ui_css_bundle( $style_queue, $dark_ok ? $dark_path : null );
			if ( is_array( $bundle ) ) {
				wp_enqueue_style(
					'drslon-ui-css-bundle',
					$bundle['url'],
					[],
					$bundle['ver']
				);
				return;
			}
		}

		foreach ( $style_queue as $item ) {
			wp_enqueue_style( $item['handle'], $item['url'], [], $item['ver'] );
		}

		if ( $dark_ok ) {
			wp_enqueue_style(
				'drslon-krv-ui-dark',
				plugins_url( $dark_rel, $plugin_file ),
				[],
				(string) filemtime( $dark_path )
			);
		}
	},
	20
);

/**
 * Build or refresh a combined CSS file for the active shortcode styles (+ optional dark).
 *
 * @param list<array{handle:string,file:string,path:string,url:string,ver:string}> $style_queue Styles to include.
 * @param string|null                                                                $dark_path   Absolute path to dark CSS or null.
 * @return array{url:string,ver:string}|null
 */
function drslon_core_ensure_ui_css_bundle( array $style_queue, ?string $dark_path ): ?array {
	$parts = [];
	$hash  = [];

	foreach ( $style_queue as $item ) {
		if ( ! is_readable( $item['path'] ) ) {
			return null;
		}
		$parts[] = $item['path'];
		$hash[]  = $item['file'] . ':' . (string) filemtime( $item['path'] );
	}

	if ( $dark_path && is_readable( $dark_path ) ) {
		$parts[] = $dark_path;
		$hash[]  = 'krv-ui-dark.css:' . (string) filemtime( $dark_path );
	}

	$id         = substr( md5( implode( '|', $hash ) ), 0, 12 );
	$bundle_rel = 'assets/css/generated/ui-bundle-' . $id . '.css';
	$bundle_abs = DRSLON_SITE_CORE_DIR . $bundle_rel;
	$bundle_dir = dirname( $bundle_abs );

	if ( ! is_dir( $bundle_dir ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
		@mkdir( $bundle_dir, 0775, true );
	}

	if ( ! is_readable( $bundle_abs ) ) {
		$out = "/* drslon-site-core UI bundle — auto-built, do not edit */\n";
		foreach ( $parts as $path ) {
			$label = basename( $path );
			$css   = (string) file_get_contents( $path );
			$out  .= "\n/* >>> {$label} */\n" . $css . "\n";
		}

		$tmp = $bundle_abs . '.tmp.' . getmypid();
		if ( false === file_put_contents( $tmp, $out ) ) {
			return null;
		}
		if ( ! @rename( $tmp, $bundle_abs ) ) {
			@unlink( $tmp );
			if ( false === file_put_contents( $bundle_abs, $out ) ) {
				return null;
			}
		}
	}

	$plugin_file = DRSLON_SITE_CORE_DIR . 'drslon-site-core.php';

	return [
		'url' => plugins_url( $bundle_rel, $plugin_file ),
		'ver' => (string) filemtime( $bundle_abs ),
	];
}
