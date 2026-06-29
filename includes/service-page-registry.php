<?php
/**
 * Registry of krivoshein.site tool pages wrapped by [krv_service_page].
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map of page_id => shell configuration.
 *
 * @return array<int, array<string, mixed>>
 */
function krv_service_page_registry(): array {
	return array(
		6186 => array(
			'shortcode' => 'wpdomainwhois',
			'shell'     => 'tool',
		),
		6204 => array(
			'shortcode' => 'wpdomainchecker',
			'atts'      => 'button="Поиск домена"',
			'shell'     => 'tool',
		),
		7287 => array(
			'shortcode' => 'punycode_converter',
			'shell'     => 'tool',
		),
		7304 => array(
			'shortcode'        => 'easy_dns_lookup',
			'shell'            => 'tool',
			'show_dns_types'   => true,
		),
		7323 => array(
			'shortcode' => 'whois_lookup',
			'shell'     => 'tool',
		),
		7352 => array(
			'shortcode' => 'crontab_generator',
			'shell'     => 'tool',
		),
		7369 => array(
			'shortcode' => 'firewall_configurator',
			'shell'     => 'tool',
		),
		7459 => array(
			'shortcode' => 'network_mask_calculator',
			'shell'     => 'minimal',
		),
		7529 => array(
			'shortcode' => 'internet_speed_test',
			'shell'     => 'tool',
		),
		9051 => array(
			'shortcode' => 'site_checker_form',
			'shell'     => 'minimal',
		),
	);
}

/**
 * Resolve a page ID from an explicit value or the current query.
 */
function krv_service_page_resolve_page_id( ?int $page_id = null ): ?int {
	if ( $page_id ) {
		return $page_id;
	}

	if ( is_admin() ) {
		return null;
	}

	$queried_id = get_queried_object_id();
	if ( $queried_id ) {
		return (int) $queried_id;
	}

	global $post;

	if ( $post instanceof WP_Post ) {
		return (int) $post->ID;
	}

	return null;
}

/**
 * Return registry config for a page, or null when unmapped.
 *
 * @return array<string, mixed>|null
 */
function krv_service_page_get_config( ?int $page_id = null ): ?array {
	$page_id = krv_service_page_resolve_page_id( $page_id );

	if ( ! $page_id ) {
		return null;
	}

	$registry = krv_service_page_registry();

	if ( ! isset( $registry[ $page_id ] ) ) {
		return null;
	}

	$config            = $registry[ $page_id ];
	$config['page_id'] = $page_id;

	return $config;
}

