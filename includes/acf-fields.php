<?php
/**
 * Module extracted from legacy-arkai-child-functions.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/** =========================
 *  8) ACF local groups + options page
 *  ========================= */
add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( [
		'key'      => 'group_clients',
		'title'    => 'Клиенты',
		'fields'   => [
			[
				'key'   => 'field_client_url',
				'label' => 'Ссылка на клиента',
				'name'  => 'client_url',
				'type'  => 'url',
			],
			[
				'key'   => 'field_client_description',
				'label' => 'Описание клиента',
				'name'  => 'client_description',
				'type'  => 'text',
			],
		],
		'location' => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'client',
				],
			],
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_projects',
		'title'    => 'Проекты',
		'fields'   => [
			[
				'key'   => 'field_project_url',
				'label' => 'URL проекта',
				'name'  => 'project_url',
				'type'  => 'url',
			],
		],
		'location' => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'project',
				],
			],
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_services',
		'title'    => 'Услуги',
		'fields'   => [
			[
				'key'   => 'field_service_description',
				'label' => 'Описание услуги',
				'name'  => 'service_description',
				'type'  => 'text',
			],
			[
				'key'         => 'field_service_icon',
				'label'       => 'Иконка',
				'name'        => 'service_icon',
				'type'        => 'font-awesome',
				'icon_sets'   => [ 'fas', 'far', 'fab' ],
				'save_format' => 'element',
			],
		],
		'location' => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'usluga',
				],
			],
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_prices',
		'title'    => 'Цены',
		'fields'   => [
			[
				'key'   => 'field_price_description',
				'label' => 'Описание',
				'name'  => 'price_description',
				'type'  => 'text',
			],
			[
				'key'    => 'field_price_value',
				'label'  => 'Стоимость',
				'name'   => 'price_value',
				'type'   => 'text',
				'append' => '₽',
			],
		],
		'location' => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'price',
				],
			],
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_partners',
		'title'    => 'Партнёры',
		'fields'   => [
			[
				'key'   => 'field_partner_url',
				'label' => 'Ссылка партнёра',
				'name'  => 'partner_url',
				'type'  => 'url',
			],
			[
				'key'   => 'field_partner_description',
				'label' => 'Описание партнёра',
				'name'  => 'partner_description',
				'type'  => 'textarea',
				'rows'  => 3,
			],
			[
				'key'           => 'field_partner_button_text',
				'label'         => 'Текст кнопки',
				'name'          => 'partner_button_text',
				'type'          => 'text',
				'default_value' => 'Перейти',
			],
			[
				'key'   => 'field_partner_badge',
				'label' => 'Метка',
				'name'  => 'partner_badge',
				'type'  => 'text',
			],
			[
				'key'           => 'field_partner_is_featured',
				'label'         => 'Выделить партнёра',
				'name'          => 'partner_is_featured',
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 0,
			],
			[
				'key'           => 'field_partner_nofollow',
				'label'         => 'Добавить nofollow',
				'name'          => 'partner_nofollow',
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 1,
			],
			[
				'key'           => 'field_partner_sponsored',
				'label'         => 'Добавить sponsored',
				'name'          => 'partner_sponsored',
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 1,
			],
		],
		'location' => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'partner',
				],
			],
		],
	] );

	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page(
			krv_acf_options_page_args(
				'krv-services-showcase',
				[
					'page_title' => 'Витрина сервисов',
					'menu_title' => 'Витрина сервисов',
					'capability' => 'edit_theme_options',
					'redirect'   => false,
					'position'   => 61,
					'icon_url'   => 'dashicons-screenoptions',
				]
			)
		);

		acf_add_local_field_group( [
			'key'    => 'group_krv_services_pages_showcase',
			'title'  => 'Витрина сервисных страниц',
			'fields' => [
				[
					'key'           => 'field_krv_showcase_intro_heading',
					'label'         => 'Вводный заголовок',
					'name'          => 'showcase_intro_heading',
					'type'          => 'text',
					'default_value' => 'На странице «Сервисы» представлены все услуги, которые мы предоставляем онлайн:',
				],
				[
					'key'          => 'field_krv_services_sections',
					'label'        => 'Секции',
					'name'         => 'krv_services_sections',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Добавить секцию',
					'sub_fields'   => [
						[
							'key'          => 'field_krv_services_section_title',
							'label'        => 'Заголовок секции',
							'name'         => 'section_title',
							'type'         => 'text',
							'instructions' => 'Необязательно. Нужен только если секций несколько. При одной секции заголовок не выводится.',
						],
						[
							'key'           => 'field_krv_services_section_pages',
							'label'         => 'Страницы',
							'name'          => 'section_pages',
							'type'          => 'relationship',
							'post_type'     => [ 'page' ],
							'filters'       => [ 'search' ],
							'elements'      => [ 'featured_image' ],
							'return_format' => 'id',
						],
					],
				],
			],
			'location' => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'krv-services-showcase',
					],
				],
			],
		] );

		acf_add_options_page(
			krv_acf_options_page_args(
				'krv-partners',
				[
					'page_title' => 'Партнёры — настройки',
					'menu_title' => 'Партнёры',
					'capability' => 'edit_theme_options',
					'redirect'   => false,
					'position'   => 64,
					'icon_url'   => 'dashicons-groups',
				]
			)
		);

		acf_add_local_field_group( [
			'key'    => 'group_krv_partners_options',
			'title'  => 'Вводный блок партнёров',
			'fields' => [
				[
					'key'   => 'field_krv_partners_intro_heading',
					'label' => 'Заголовок',
					'name'  => 'partners_intro_heading',
					'type'  => 'text',
				],
				[
					'key'   => 'field_krv_partners_intro_text',
					'label' => 'Текст',
					'name'  => 'partners_intro_text',
					'type'  => 'textarea',
					'rows'  => 3,
				],
			],
			'location' => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'krv-partners',
					],
				],
			],
		] );
	}
} );

/**
 * Seed showcase intro heading on first run when ACF option is empty.
 */
function krv_services_showcase_seed_intro_heading(): void {
	if ( get_option( 'krv_services_showcase_intro_seeded_v1' ) ) {
		return;
	}

	if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	$option_id = 'krv-services-showcase';
	$current   = get_field( 'showcase_intro_heading', $option_id );

	if ( is_string( $current ) && trim( $current ) !== '' ) {
		update_option( 'krv_services_showcase_intro_seeded_v1', DRSLON_SITE_CORE_VERSION, false );
		return;
	}

	update_field(
		'showcase_intro_heading',
		'На странице «Сервисы» представлены все услуги, которые мы предоставляем онлайн:',
		$option_id
	);

	update_option( 'krv_services_showcase_intro_seeded_v1', DRSLON_SITE_CORE_VERSION, false );
}

add_action( 'acf/init', 'krv_services_showcase_seed_intro_heading', 25 );
