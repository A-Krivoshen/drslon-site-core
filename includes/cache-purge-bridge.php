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