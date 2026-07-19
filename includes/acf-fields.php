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
		'key'           => 'group_projects',
		'title'         => 'Проекты',
		'show_in_rest'  => true,
		'fields'        => [
			[
				'key'   => 'field_project_url',
				'label' => 'URL проекта',
				'name'  => 'project_url',
				'type'  => 'url',
			],
			[
				'key'          => 'field_related_posts',
				'label'        => 'Связанные статьи',
				'name'         => 'related_posts',
				'type'         => 'relationship',
				'post_types'   => [ 'post' ],
				'return_format' => 'id',
				'min'          => 0,
				'max'          => 20,
				'show_in_rest' => true,
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

/**
 * Fallback: save related_posts from REST API body when ACF doesn't handle it.
 * Gutenberg sends ACF data as JSON in the request body, not as $_POST.
 */
add_action( 'save_post_project', function ( $post_id, $post = null, $update = false ) {
	if ( ! $update ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$value = [];

	// Classic editor: $_POST['acf'].
	$raw_post = filter_input( INPUT_POST, 'acf', FILTER_UNSAFE_RAW );
	if ( is_string( $raw_post ) && ! empty( $raw_post ) ) {
		$acf_data = json_decode( $raw_post, true );
		if ( is_array( $acf_data ) && isset( $acf_data['field_related_posts'] ) ) {
			$value = (array) $acf_data['field_related_posts'];
		}
	}

	// Gutenberg REST API: JSON body.
	if ( empty( $value ) ) {
	$body = file_get_contents( 'php://input' );
	if ( is_string( $body ) && ! empty( $body ) ) {
		$json = json_decode( $body, true );
		if ( is_array( $json ) ) {
			error_log( 'krv_save project=' . $post_id . ' keys=' . implode( ',', array_keys( $json ) ) );
			if ( isset( $json['acf'] ) ) {
				error_log( 'krv_save acf_keys=' . implode( ',', array_keys( (array) $json['acf'] ) ) );
			}
			if ( isset( $json['meta'] ) ) {
				error_log( 'krv_save meta_keys=' . implode( ',', array_keys( (array) $json['meta'] ) ) );
			}
				// Gutenberg sends as {"acf":{"field_related_posts":[...]}} or flat {"related_posts":[...]}.
				if ( isset( $json['acf']['field_related_posts'] ) ) {
					$value = (array) $json['acf']['field_related_posts'];
				} elseif ( isset( $json['acf']['related_posts'] ) ) {
					$value = (array) $json['acf']['related_posts'];
				} elseif ( isset( $json['meta']['related_posts'] ) ) {
					$value = (array) $json['meta']['related_posts'];
				} elseif ( isset( $json['related_posts'] ) ) {
					$value = (array) $json['related_posts'];
				}
			}
		}
	}

	if ( empty( $value ) ) {
		return;
	}

	$value = array_map( 'intval', $value );
	$value = array_filter( $value );

	update_post_meta( $post_id, 'related_posts', $value );
	update_post_meta( $post_id, '_related_posts', 'field_related_posts' );
}, 20, 3 );
