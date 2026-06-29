<?php
/**
 * Bridge WP Fastest Cache purge events to Nginx Helper (Redis srcache).
 *
 * WPFC autopurge clears its own page cache but leaves Nginx Redis entries intact.
 * This module mirrors WPFC purge scope into nginx-helper so visitors never see
 * stale HTML from the edge cache.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Page ID for /servisy/ — services showcase shortcode destination. */
define( 'DRSLON_SERVICES_PAGE_ID', 6202 );

/** Page ID for /partnery/ — partners grid shortcode destination. */
define( 'DRSLON_PARTNERS_PAGE_ID', 9584 );

/** Page ID for home / «Обо мне» — [krv_services_landing] destination. */
define( 'DRSLON_HOME_PAGE_ID', 17 );

/** Page ID for /prays-list/ — [krv_price_list] destination. */
define( 'DRSLON_PRICE_LIST_PAGE_ID', 9772 );

/**
 * Sync WPFC cache invalidation with Nginx Helper Redis purge.
 */
final class DrSlon_Cache_Purge_Bridge {

	/**
	 * Register hooks after plugins are loaded (nginx-helper must be ready).
	 */
	public static function init(): void {
		// Fires at the end of WpFastestCache::deleteCache() — toolbar, autopurge "all", admin.
		add_action( 'wpfc_delete_cache', array( __CLASS__, 'purge_nginx_all' ), 20 );

		// Public API: wpfc_clear_post_cache_by_id( $id ).
		add_action( 'wpfc_clear_post_cache_by_id', array( __CLASS__, 'purge_nginx_post' ), 20, 2 );

		// Logo / site icon live in every cached page; customizer does not hit post hooks.
		add_action( 'customize_save_after', array( __CLASS__, 'purge_nginx_all' ), 20 );

		// Services showcase ACF options affect cached /servisy/ HTML.
		add_action( 'acf/save_post', array( __CLASS__, 'on_acf_save_post' ), 20 );
	}

	/**
	 * Purge the entire Nginx Redis cache.
	 */
	public static function purge_nginx_all(): void {
		if ( ! self::is_nginx_purge_enabled() ) {
			return;
		}

		do_action( 'rt_nginx_helper_purge_all' );
	}

	/**
	 * Purge WP Fastest Cache and Nginx Redis for a single page/post.
	 *
	 * @param int $post_id Post or page ID.
	 */
	public static function purge_page_cache( int $post_id ): void {
		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			return;
		}

		if ( function_exists( 'wpfc_clear_post_cache_by_id' ) ) {
			wpfc_clear_post_cache_by_id( $post_id );
		}

		self::purge_nginx_post( false, $post_id );
	}

	/**
	 * Bust services showcase transient and purge /servisy/ when ACF options are saved.
	 *
	 * @param int|string $post_id ACF context ID (options page slug or "options").
	 */
	public static function on_acf_save_post( $post_id ): void {
		$post_id = (string) $post_id;

		if ( $post_id === 'krv-services-showcase' ) {
			delete_transient( 'krv_services_showcase_v1' );
			self::purge_page_cache( DRSLON_SERVICES_PAGE_ID );
			return;
		}

		if ( $post_id === 'krv-partners' ) {
			delete_transient( 'krv_partners_grid_v1' );
			self::purge_page_cache( DRSLON_PARTNERS_PAGE_ID );
			return;
		}

		if ( $post_id === 'krv-services-landing' ) {
			self::purge_page_cache( DRSLON_HOME_PAGE_ID );
			return;
		}

		if ( $post_id === 'krv-price-list' ) {
			self::purge_page_cache( DRSLON_PRICE_LIST_PAGE_ID );
		}
	}

	/**
	 * Purge Nginx cache for a single post and its related URLs.
	 *
	 * @param mixed $unused   First argument from wpfc_clear_post_cache_by_id (always false).
	 * @param int   $post_id  Post ID whose cache was cleared by WPFC.
	 */
	public static function purge_nginx_post( $unused, $post_id ): void {
		unset( $unused );

		if ( ! self::is_nginx_purge_enabled() ) {
			return;
		}

		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			return;
		}

		global $nginx_purger;

		if ( ! isset( $nginx_purger ) || ! is_object( $nginx_purger ) ) {
			return;
		}

		if ( ! method_exists( $nginx_purger, 'purge_post' ) ) {
			return;
		}

		$nginx_purger->purge_post( $post_id );
	}

	/**
	 * Check whether nginx-helper purge is configured and enabled.
	 */
	private static function is_nginx_purge_enabled(): bool {
		if ( ! class_exists( 'Nginx_Helper' ) ) {
			return false;
		}

		global $nginx_helper_admin;

		if ( ! isset( $nginx_helper_admin->options['enable_purge'] ) ) {
			return false;
		}

		return (int) $nginx_helper_admin->options['enable_purge'] === 1;
	}
}

add_action( 'plugins_loaded', array( 'DrSlon_Cache_Purge_Bridge', 'init' ), 20 );