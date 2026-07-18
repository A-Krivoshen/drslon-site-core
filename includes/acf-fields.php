<?php
/**
 * ACF local field groups + services showcase options page.
 * Requires Advanced Custom Fields; the 'font-awesome' field type
 * additionally requires the ACF Font Awesome plugin.
 * Extracted from legacy-arkai-child-functions.php
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
		acf_add_options_page( [
			'page_title' => 'Витрина сервисов',
			'menu_title' => 'Витрина сервисов',
			'menu_slug'  => 'krv-services-showcase',
			'capability' => 'edit_posts',
			'redirect'   => false,
			'position'   => 61,
			'icon_url'   => 'dashicons-screenoptions',
		] );

		acf_add_local_field_group( [
			'key'    => 'group_krv_services_pages_showcase',
			'title'  => 'Витрина сервисных страниц',
			'fields' => [
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
	}
} );

add_filter( 'acf/settings/show_admin', '__return_false' );
