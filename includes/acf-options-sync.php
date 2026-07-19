<?php
/**
 * ACF options page storage alignment + admin hints.
 *
 * Data must be stored under the same post_id the options page admin UI uses.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Standard ACF options page args: post_id matches menu_slug.
 *
 * @param string               $slug Menu slug and storage key.
 * @param array<string, mixed> $args Options page args.
 * @return array<string, mixed>
 */
function krv_acf_options_page_args( string $slug, array $args ): array {
	return array_merge(
		array(
			'menu_slug' => $slug,
			'post_id'   => $slug,
		),
		$args
	);
}

/**
 * One-time migration: copy fields saved under legacy keys into slug post_id.
 */
function krv_acf_migrate_options_storage(): void {
	if ( get_option( 'krv_acf_options_storage_migrated_v1' ) ) {
		return;
	}

	if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	$showcase_id = 'krv-services-showcase';
	$sections    = get_field( 'krv_services_sections', 'option' );
	$on_showcase = get_field( 'krv_services_sections', $showcase_id );

	if ( is_array( $sections ) && ! empty( $sections ) && ( ! is_array( $on_showcase ) || empty( $on_showcase ) ) ) {
		update_field( 'krv_services_sections', $sections, $showcase_id );
	}

	$intro_on_option = get_field( 'showcase_intro_heading', 'option' );
	$intro_on_slug   = get_field( 'showcase_intro_heading', $showcase_id );

	if ( is_string( $intro_on_option ) && trim( $intro_on_option ) !== '' && ( ! is_string( $intro_on_slug ) || trim( $intro_on_slug ) === '' ) ) {
		update_field( 'showcase_intro_heading', $intro_on_option, $showcase_id );
	}

	update_option( 'krv_acf_options_storage_migrated_v1', 1, false );
}

add_action( 'acf/init', 'krv_acf_migrate_options_storage', 15 );

/**
 * Copy legacy Cyrillic post meta into the registered ACF fields once.
 * Existing canonical values, including intentional empty values, win.
 */
function krv_acf_migrate_legacy_post_meta(): void {
	if ( get_option( 'krv_acf_post_meta_migrated_v1' ) ) {
		return;
	}

	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	global $wpdb;

	$lock_name = 'drslon_acf_meta_migration_v1';
	$has_lock  = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 1)', $lock_name ) );

	if ( 1 !== $has_lock ) {
		return;
	}

	try {
		if ( get_option( 'krv_acf_post_meta_migrated_v1' ) ) {
			return;
		}

		$mappings = array(
			'price'  => array(
				'legacy'    => 'стоимость',
				'canonical' => 'price_value',
				'field_key' => 'field_price_value',
			),
			'client' => array(
				'legacy'    => 'ссылка_на_клиента',
				'canonical' => 'client_url',
				'field_key' => 'field_client_url',
			),
			'usluga' => array(
				'legacy'    => 'иконка_поля',
				'canonical' => 'service_icon',
				'field_key' => 'field_service_icon',
			),
		);
		$migration_complete = true;

		foreach ( $mappings as $post_type => $mapping ) {
			$post_ids = get_posts(
				array(
					'post_type'      => $post_type,
					'post_status'    => array_keys( get_post_stati() ),
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'no_found_rows'  => true,
				)
			);

			if ( $wpdb->last_error !== '' ) {
				$migration_complete = false;
				break;
			}

			foreach ( $post_ids as $post_id ) {
				$post_id = (int) $post_id;

				if (
					metadata_exists( 'post', $post_id, $mapping['canonical'] ) ||
					! metadata_exists( 'post', $post_id, $mapping['legacy'] )
				) {
					continue;
				}

				$value = get_post_meta( $post_id, $mapping['legacy'], true );

				if ( '' === $value ) {
					continue;
				}

				update_field( $mapping['field_key'], $value, $post_id );

				if ( ! metadata_exists( 'post', $post_id, $mapping['canonical'] ) ) {
					$migration_complete = false;
				}
			}
		}

		if ( $migration_complete ) {
			update_option( 'krv_acf_post_meta_migrated_v1', 1, false );
		}
	} finally {
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock_name ) );
	}
}

add_action( 'init', 'krv_acf_migrate_legacy_post_meta', 30 );

/**
 * Map page IDs to ACF options screens for editor hints.
 *
 * @return array<int, array{label: string, slug: string}>
 */
function krv_acf_page_editor_hints(): array {
	$hints = array(
		17   => array(
			'label' => 'Лендинг услуг',
			'slug'  => 'krv-services-landing',
		),
		6202 => array(
			'label' => 'Витрина сервисов',
			'slug'  => 'krv-services-showcase',
		),
		9584 => array(
			'label' => 'Партнёры',
			'slug'  => 'krv-partners',
		),
		9772 => array(
			'label' => 'Прайс-лист',
			'slug'  => 'krv-price-list',
		),
	);

	$tool_pages = array(
		6186 => 'Whois',
		6204 => 'Информация о домене',
		7287 => 'Punycode',
		7304 => 'DNS Lookup',
		7323 => 'Whois lookup',
		7352 => 'Crontab',
		7369 => 'Firewall',
		7459 => 'Сетевые маски',
		7529 => 'Speed test',
		9051 => 'Site checker',
	);

	foreach ( $tool_pages as $page_id => $label ) {
		$hints[ $page_id ] = array(
			'label' => 'Сервисные страницы → ' . $label,
			'slug'  => 'krv-service-pages',
		);
	}

	return $hints;
}

/**
 * Force re-seed landing defaults if admin storage was empty (one-time repair).
 */
function krv_acf_repair_landing_seed(): void {
	if ( get_option( 'krv_acf_landing_repair_v1' ) ) {
		return;
	}

	if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	$option_id   = 'krv-services-landing';
	$stored_name = get_option( $option_id . '_profile_name', null );

	if ( null !== $stored_name ) {
		update_option( 'krv_acf_landing_repair_v1', DRSLON_SITE_CORE_VERSION, false );
		return;
	}

	if ( function_exists( 'krv_services_landing_get_defaults' ) ) {
		delete_option( 'krv_services_landing_seeded_v1' );
		if ( function_exists( 'krv_services_landing_seed_defaults' ) ) {
			krv_services_landing_seed_defaults();
		}
	}

	update_option( 'krv_acf_landing_repair_v1', DRSLON_SITE_CORE_VERSION, false );
}

add_action( 'acf/init', 'krv_acf_repair_landing_seed', 30 );

add_action(
	'edit_form_after_title',
	function ( $post ) {
		if ( ! $post instanceof WP_Post || $post->post_type !== 'page' ) {
			return;
		}

		$hints = krv_acf_page_editor_hints();

		if ( ! isset( $hints[ (int) $post->ID ] ) ) {
			return;
		}

		$hint = $hints[ (int) $post->ID ];
		$url  = $hint['slug'] !== '' ? admin_url( 'admin.php?page=' . $hint['slug'] ) : '';

		echo '<div class="notice notice-info" style="margin:12px 0;padding:12px 16px;">';
		echo '<p><strong>Контент этой страницы не в редакторе.</strong> ';
		echo 'На странице только шорткод — весь текст и блоки редактируются отдельно: ';
		if ( $url !== '' ) {
			echo '<a href="' . esc_url( $url ) . '"><strong>' . esc_html( $hint['label'] ) . '</strong></a>.';
		} else {
			echo '<strong>' . esc_html( $hint['label'] ) . '</strong> — <code>includes/price-list-widget.php</code>.';
		}
		echo ' <a href="' . esc_url( get_permalink( $post ) ) . '" target="_blank" rel="noopener">Открыть страницу ↗</a>';
		echo '</p></div>';
	}
);

add_action(
	'acf/options_page/submitbox_before_major_actions',
	function () {
		global $plugin_page;

		if ( ! is_string( $plugin_page ) || $plugin_page === '' ) {
			return;
		}

		$labels = array(
			'krv-services-landing'  => 'главная (Обо мне)',
			'krv-services-showcase' => 'страница Сервисы',
			'krv-service-pages'     => 'инструменты (DNS, speed test и др.)',
			'krv-partners'          => 'страница Партнёры',
			'krv-price-list'        => 'страница Прайс-лист',
		);

		if ( ! isset( $labels[ $plugin_page ] ) ) {
			return;
		}

		echo '<p class="description" style="margin-bottom:12px;">Изменения применяются на: <strong>' . esc_html( $labels[ $plugin_page ] ) . '</strong>.</p>';
	}
);
