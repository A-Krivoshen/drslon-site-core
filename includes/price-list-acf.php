<?php
/**
 * ACF options page for /prays-list/ hero, trust strip and disclaimer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACF storage key for price list options.
 *
 * @return string
 */
function krv_price_list_option_id(): string {
	return 'krv-price-list';
}

/**
 * Default editable copy for the price list widget.
 *
 * @return array<string, mixed>
 */
function krv_price_list_get_defaults(): array {
	return array(
		'hero_badge'     => 'Антикризисный прайс • WordPress / Linux / реклама / боты',
		'hero_title'     => 'Стоимость сайтов, поддержки, рекламы и технических работ',
		'hero_subtitle'  => 'Разработка сайтов • Поддержка • Linux / DevOps • Яндекс Директ • Автоматизация заявок',
		'hero_lead'      => 'Сайты на WordPress, доработка проектов, серверы, реклама, аналитика и боты для заявок. Ниже — базовые ориентиры по цене: антикризисный прайс без агентской наценки, финальная смета зависит от задачи и состояния проекта.',
		'trust_items'    => array(
			array( 'text' => 'Один специалист — без менеджеров и агентской наценки' ),
			array( 'text' => 'Ответ обычно в течение рабочего дня' ),
			array( 'text' => 'Салоны, клиники, B2B и сервисные сайты на WordPress' ),
		),
		'disclaimer'     => 'Цены на странице указаны как стартовый антикризисный ориентир. Простые задачи с понятным объемом считаются от указанной суммы. Если проект требует сложной логики, интеграций, срочности, длительной отладки или большого количества правок, стоимость считается отдельно.',
	);
}

/**
 * Merge ACF values with defaults.
 *
 * @return array<string, mixed>
 */
function krv_price_list_get_settings(): array {
	$settings = krv_price_list_get_defaults();

	if ( ! function_exists( 'get_field' ) ) {
		return $settings;
	}

	$option_id = krv_price_list_option_id();

	$scalar_fields = array( 'hero_badge', 'hero_title', 'hero_subtitle', 'hero_lead', 'disclaimer' );

	foreach ( $scalar_fields as $field ) {
		$value = get_field( $field, $option_id );

		if ( is_string( $value ) && trim( $value ) !== '' ) {
			$settings[ $field ] = trim( $value );
		}
	}

	$trust_items = get_field( 'trust_items', $option_id );

	if ( is_array( $trust_items ) && ! empty( $trust_items ) ) {
		$settings['trust_items'] = $trust_items;
	}

	return $settings;
}

/**
 * Register ACF options page and field group.
 */
function krv_price_list_register_acf(): void {
	if ( ! function_exists( 'acf_add_options_page' ) || ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_options_page(
		krv_acf_options_page_args(
			krv_price_list_option_id(),
			array(
				'page_title' => 'Прайс-лист',
				'menu_title' => 'Прайс-лист',
				'capability' => 'edit_theme_options',
				'redirect'   => false,
				'position'   => 63,
				'icon_url'   => 'dashicons-money-alt',
			)
		)
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_krv_price_list',
			'title'    => 'Прайс-лист (/prays-list/)',
			'fields'   => array(
				array(
					'key'   => 'field_krv_pl_hero_badge',
					'label' => 'Hero: бейдж',
					'name'  => 'hero_badge',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_krv_pl_hero_title',
					'label' => 'Hero: заголовок',
					'name'  => 'hero_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_krv_pl_hero_subtitle',
					'label' => 'Hero: подзаголовок',
					'name'  => 'hero_subtitle',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_krv_pl_hero_lead',
					'label' => 'Hero: вводный текст',
					'name'  => 'hero_lead',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				array(
					'key'          => 'field_krv_pl_trust_items',
					'label'        => 'Trust strip: пункты доверия',
					'name'         => 'trust_items',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Добавить пункт',
					'sub_fields'   => array(
						array(
							'key'   => 'field_krv_pl_trust_text',
							'label' => 'Текст',
							'name'  => 'text',
							'type'  => 'text',
						),
					),
				),
				array(
					'key'   => 'field_krv_pl_disclaimer',
					'label' => 'Дисклеймер внизу страницы',
					'name'  => 'disclaimer',
					'type'  => 'textarea',
					'rows'  => 3,
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => krv_price_list_option_id(),
					),
				),
			),
		)
	);
}

add_action( 'acf/init', 'krv_price_list_register_acf' );

/**
 * Seed default ACF values once.
 */
function krv_price_list_seed_defaults(): void {
	if ( get_option( 'krv_price_list_seeded_v1' ) ) {
		return;
	}

	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	$defaults  = krv_price_list_get_defaults();
	$option_id = krv_price_list_option_id();

	update_field( 'hero_badge', $defaults['hero_badge'], $option_id );
	update_field( 'hero_title', $defaults['hero_title'], $option_id );
	update_field( 'hero_subtitle', $defaults['hero_subtitle'], $option_id );
	update_field( 'hero_lead', $defaults['hero_lead'], $option_id );
	update_field( 'trust_items', $defaults['trust_items'], $option_id );
	update_field( 'disclaimer', $defaults['disclaimer'], $option_id );

	update_option( 'krv_price_list_seeded_v1', DRSLON_SITE_CORE_VERSION, false );
}

add_action( 'acf/init', 'krv_price_list_seed_defaults', 25 );

/**
 * Build trust strip HTML from settings.
 *
 * @param array<string, mixed> $settings Widget settings.
 * @return string
 */
function krv_price_list_render_trust_strip( array $settings ): string {
	$items = $settings['trust_items'] ?? array();

	if ( ! is_array( $items ) || empty( $items ) ) {
		return '';
	}

	$out = '<div class="krv-trust-strip" role="list">';

	foreach ( $items as $item ) {
		$text = '';

		if ( is_array( $item ) && isset( $item['text'] ) ) {
			$text = trim( (string) $item['text'] );
		}

		if ( $text === '' ) {
			continue;
		}

		$out .= '<span class="krv-trust-item" role="listitem">' . esc_html( $text ) . '</span>';
	}

	$out .= '</div>';

	return $out;
}