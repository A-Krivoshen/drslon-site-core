<?php
/**
 * Module extracted from legacy-arkai-child-functions.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/** =========================
 *  7) Custom post types + taxonomy
 *  ========================= */
add_action( 'init', function () {
	$post_types = [
		'client'  => [
			'name'          => 'Клиенты',
			'singular_name' => 'Клиент',
			'menu_name'     => 'Клиенты',
			'supports'      => [ 'title', 'thumbnail', 'page-attributes' ],
		],
		'project' => [
			'name'          => 'Проекты',
			'singular_name' => 'Проект',
			'menu_name'     => 'Проекты',
			'supports'      => [ 'title', 'thumbnail' ],
			'has_archive'   => 'project',
		],
		'usluga'  => [
			'name'          => 'Услуги',
			'singular_name' => 'Услуга',
			'menu_name'     => 'Услуги',
			'supports'      => [ 'title', 'thumbnail' ],
		],
		'price'   => [
			'name'          => 'Цены',
			'singular_name' => 'Цена',
			'menu_name'     => 'Цены',
			'supports'      => [ 'title', 'thumbnail' ],
		],
		'partner' => [
			'name'          => 'Партнёры',
			'singular_name' => 'Партнёр',
			'menu_name'     => 'Партнёры',
			'supports'      => [ 'title', 'thumbnail', 'page-attributes' ],
		],
	];

	foreach ( $post_types as $slug => $args ) {
		register_post_type( $slug, [
			'labels'       => [
				'name'          => $args['name'],
				'singular_name' => $args['singular_name'],
				'menu_name'     => $args['menu_name'],
				'all_items'     => "Все {$args['name']}",
				'edit_item'     => "Изменить {$args['singular_name']}",
				'add_new_item'  => "Добавить новое {$args['singular_name']}",
				'new_item'      => "Новый {$args['singular_name']}",
				'view_item'     => "Посмотреть {$args['singular_name']}",
				'not_found'     => "Не найдено {$args['name']}",
			],
			'public'       => true,
			'show_in_rest' => 'project' !== $slug,
			'menu_icon'    => 'dashicons-admin-post',
			'supports'     => $args['supports'],
			'has_archive'  => $args['has_archive'] ?? false,
			'rewrite'      => [ 'slug' => $slug ],
		] );
	}

	register_taxonomy( 'partner_category', [ 'partner' ], [
		'labels' => [
			'name'          => 'Категории партнёров',
			'singular_name' => 'Категория партнёра',
			'search_items'  => 'Искать категории',
			'all_items'     => 'Все категории',
			'edit_item'     => 'Редактировать категорию',
			'update_item'   => 'Обновить категорию',
			'add_new_item'  => 'Добавить категорию',
			'new_item_name' => 'Новая категория',
			'menu_name'     => 'Категории партнёров',
		],
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => [ 'slug' => 'partner-category' ],
	] );
} );
