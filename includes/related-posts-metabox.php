<?php
/**
 * Related posts meta box for project CPT.
 *
 * Primary save path: admin-ajax (krv_save_related_posts) on every add/remove.
 * This avoids Gutenberg REST / classic meta-box form issues entirely.
 * Secondary: register_post_meta + editPost, classic save_post fallback.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register meta (REST + sanitize). Gutenberg still benefits when custom-fields is on.
 */
add_action( 'init', function () {
	add_post_type_support( 'project', 'custom-fields' );

	register_post_meta( 'project', 'related_posts', [
		'type'              => 'array',
		'single'            => true,
		'default'           => [],
		'show_in_rest'      => [
			'schema' => [
				'type'  => 'array',
				'items' => [
					'type' => 'integer',
				],
			],
		],
		'auth_callback'     => function ( $allowed, $meta_key, $post_id ) {
			$post_id = (int) $post_id;
			if ( $post_id <= 0 ) {
				return current_user_can( 'edit_posts' );
			}
			return current_user_can( 'edit_post', $post_id );
		},
		'sanitize_callback' => 'krv_related_posts_normalize_ids',
	] );
} );

add_action( 'add_meta_boxes', function () {
	remove_meta_box( 'postcustom', 'project', 'normal' );
}, 99 );

add_action( 'add_meta_boxes_project', function () {
	add_meta_box(
		'krv_related_posts',
		'Связанные статьи',
		'krv_related_posts_metabox_render',
		'project',
		'side',
		'default',
		[
			'__block_editor_compatible_meta_box' => true,
		]
	);
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'project' !== $screen->post_type ) {
		return;
	}

	$post_id = 0;
	if ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_id = absint( $_GET['post'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	$dir = plugin_dir_url( __FILE__ );
	wp_enqueue_style( 'krv-related-posts', $dir . 'related-posts-metabox.css', [], '1.3' );
	wp_enqueue_script(
		'krv-related-posts',
		$dir . 'related-posts-metabox.js',
		[ 'wp-data' ],
		'1.3',
		true
	);

	wp_localize_script( 'krv-related-posts', 'krvRP', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'krv_related_posts' ),
		'metaKey' => 'related_posts',
		'postId'  => $post_id,
		'i18n'    => [
			'searching'   => 'Поиск...',
			'none'        => 'Ничего не найдено',
			'error'       => 'Ошибка',
			'saving'      => 'Сохранение…',
			'saved'       => 'Сохранено',
			'saveError'   => 'Не удалось сохранить',
			'needSave'    => 'Сначала сохраните проект (получите ID), потом привязывайте статьи.',
			'configError' => 'Ошибка конфигурации',
		],
	] );
} );

/**
 * Normalize stored meta to a list of positive ints.
 *
 * @param mixed $raw Raw meta value.
 * @return int[]
 */
function krv_related_posts_normalize_ids( $raw ) {
	if ( is_string( $raw ) && $raw !== '' ) {
		$maybe = maybe_unserialize( $raw );
		if ( is_array( $maybe ) ) {
			$raw = $maybe;
		} else {
			$raw = explode( ',', $raw );
		}
	} elseif ( is_numeric( $raw ) ) {
		$raw = [ (int) $raw ];
	} elseif ( ! is_array( $raw ) ) {
		$raw = [];
	}

	// ACF sometimes returns array of post objects.
	$ids = [];
	foreach ( (array) $raw as $item ) {
		if ( is_object( $item ) && isset( $item->ID ) ) {
			$ids[] = (int) $item->ID;
		} else {
			$ids[] = absint( $item );
		}
	}

	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Persist related post IDs for a project. Returns normalized IDs.
 *
 * @param int   $post_id Project ID.
 * @param int[] $ids     Post IDs.
 * @return int[]|WP_Error
 */
function krv_related_posts_save( $post_id, $ids ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || get_post_type( $post_id ) !== 'project' ) {
		return new WP_Error( 'invalid_project', 'Некорректный проект' );
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return new WP_Error( 'forbidden', 'Недостаточно прав' );
	}

	$ids = krv_related_posts_normalize_ids( $ids );
	update_post_meta( $post_id, 'related_posts', $ids );

	// Drop object-cache for this key just in case a persistent cache is stale.
	wp_cache_delete( $post_id, 'post_meta' );
	clean_post_cache( $post_id );

	/**
	 * Allow cache plugins / bridges to purge the project page.
	 *
	 * @param int   $post_id Project ID.
	 * @param int[] $ids     Related post IDs.
	 */
	do_action( 'krv_related_posts_saved', $post_id, $ids );

	return $ids;
}

/**
 * Render the meta box HTML.
 *
 * @param WP_Post $post Current post.
 */
function krv_related_posts_metabox_render( $post ) {
	$ids = krv_related_posts_normalize_ids( get_post_meta( $post->ID, 'related_posts', true ) );

	wp_nonce_field( 'krv_related_posts_' . $post->ID, 'krv_related_posts_nonce' );
	?>
	<div class="krv-rp-wrap" data-post-id="<?php echo esc_attr( (string) (int) $post->ID ); ?>">
		<input type="text" class="krv-rp-search" placeholder="Поиск статей..." autocomplete="off" />
		<div class="krv-rp-results" style="display:none;"></div>
		<ul class="krv-rp-selected">
			<?php foreach ( $ids as $id ) :
				$title = get_the_title( $id );
				if ( ! $title ) {
					continue;
				}
			?>
				<li data-id="<?php echo esc_attr( (string) $id ); ?>">
					<span><?php echo esc_html( $title ); ?></span>
					<a href="#" class="krv-rp-remove" title="Убрать" aria-label="Убрать">&times;</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<input type="hidden" name="krv_related_posts_ids" id="krv_related_posts_ids" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>" />
		<p class="krv-rp-status" aria-live="polite" style="margin:8px 0 0;font-size:12px;color:#646970;"></p>
		<p class="description" style="margin-top:6px;">Статьи сохраняются сразу при выборе (AJAX), без кнопки «Обновить».</p>
	</div>
	<?php
}

/**
 * Classic form fallback (non-Gutenberg / secondary meta-box POST).
 */
add_action( 'save_post_project', function ( $post_id, $post = null ) {
	if ( ! $post instanceof WP_Post ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// AJAX and REST have their own paths.
	if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || wp_doing_ajax() ) {
		return;
	}
	if ( ! isset( $_POST['krv_related_posts_nonce'] ) ||
	     ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['krv_related_posts_nonce'] ) ), 'krv_related_posts_' . $post_id ) ) {
		return;
	}
	if ( ! array_key_exists( 'krv_related_posts_ids', $_POST ) ) {
		return;
	}

	$raw = sanitize_text_field( wp_unslash( $_POST['krv_related_posts_ids'] ) );
	$ids = krv_related_posts_normalize_ids( $raw === '' ? [] : explode( ',', $raw ) );

	// Avoid wiping good AJAX data with an empty classic meta-box serialisation.
	$existing = krv_related_posts_normalize_ids( get_post_meta( $post_id, 'related_posts', true ) );
	if ( empty( $ids ) && ! empty( $existing ) && $raw === '' ) {
		return;
	}

	krv_related_posts_save( $post_id, $ids );
}, 20, 2 );

/**
 * AJAX: search published posts by title.
 */
add_action( 'wp_ajax_krv_search_posts', function () {
	check_ajax_referer( 'krv_related_posts' );

	$q = isset( $_REQUEST['q'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['q'] ) ) : '';
	if ( mb_strlen( $q ) < 2 ) {
		wp_send_json_success( [] );
	}

	$results = get_posts( [
		's'                      => $q,
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => 15,
		'orderby'                => 'relevance',
		'suppress_filters'       => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	] );

	$data = array_map( function ( $p ) {
		return [
			'id'    => (int) $p->ID,
			'title' => $p->post_title,
		];
	}, $results );

	wp_send_json_success( $data );
} );

/**
 * AJAX: save related post IDs immediately.
 */
add_action( 'wp_ajax_krv_save_related_posts', function () {
	check_ajax_referer( 'krv_related_posts' );

	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$raw     = isset( $_POST['ids'] ) ? sanitize_text_field( wp_unslash( $_POST['ids'] ) ) : '';
	$ids     = $raw === '' ? [] : array_map( 'absint', explode( ',', $raw ) );

	$result = krv_related_posts_save( $post_id, $ids );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( [ 'message' => $result->get_error_message() ], 400 );
	}

	wp_send_json_success( [
		'ids'     => $result,
		'message' => 'ok',
	] );
} );

/**
 * Purge page cache when related posts change.
 */
add_action( 'krv_related_posts_saved', function ( $post_id ) {
	$post_id = (int) $post_id;

	if ( class_exists( 'DrSlon_Cache_Purge_Bridge' ) && method_exists( 'DrSlon_Cache_Purge_Bridge', 'purge_page_cache' ) ) {
		DrSlon_Cache_Purge_Bridge::purge_page_cache( $post_id );
		return;
	}

	if ( function_exists( 'wpfc_clear_post_cache_by_id' ) ) {
		wpfc_clear_post_cache_by_id( $post_id );
	}
}, 10, 1 );
