<?php
/**
 * Custom meta box for linking posts to project CPT.
 * Works with both Gutenberg and classic editor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register meta box + enqueue JS on project edit screen.
 */
add_action( 'add_meta_boxes_project', function () {
	add_meta_box(
		'krv_related_posts',
		'Связанные статьи',
		'krv_related_posts_metabox_render',
		'project',
		'side',
		'default'
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

	$dir = plugin_dir_url( __FILE__ );
	wp_enqueue_style( 'krv-related-posts', $dir . 'related-posts-metabox.css', [], '1.0' );
	wp_enqueue_script( 'krv-related-posts', $dir . 'related-posts-metabox.js', [], '1.0', true );
	wp_localize_script( 'krv-related-posts', 'krvRP', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'krv_search_posts' ),
	] );
} );

/**
 * Render the meta box HTML.
 */
function krv_related_posts_metabox_render( $post ) {
	$ids = get_post_meta( $post->ID, 'related_posts', true );
	$ids = array_filter( array_map( 'intval', (array) $ids ) );

	wp_nonce_field( 'krv_related_posts_' . $post->ID, 'krv_related_posts_nonce' );
	?>
	<div class="krv-rp-wrap">
		<input type="text" class="krv-rp-search" placeholder="Поиск статей..." />
		<div class="krv-rp-results"></div>
		<ul class="krv-rp-selected">
			<?php foreach ( $ids as $id ) :
				$title = get_the_title( $id );
				if ( ! $title ) {
					continue;
				}
			?>
				<li data-id="<?php echo esc_attr( $id ); ?>">
					<span><?php echo esc_html( $title ); ?></span>
					<a class="krv-rp-remove" title="Убрать">&times;</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<input type="hidden" name="krv_related_posts_ids" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>" />
	</div>
	<?php
}

/**
 * Save meta box data.
 */
add_action( 'save_post_project', function ( $post_id, $post = null, $update = false ) {
	if ( ! $update || ! $post ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['krv_related_posts_nonce'] ) ||
	     ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['krv_related_posts_nonce'] ) ), 'krv_related_posts_' . $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$raw = isset( $_POST['krv_related_posts_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['krv_related_posts_ids'] ) ) : '';
	$ids = array_filter( array_map( 'intval', explode( ',', $raw ) ) );

	update_post_meta( $post_id, 'related_posts', $ids );
}, 20, 3 );

/**
 * AJAX handler: search published posts by title.
 */
add_action( 'wp_ajax_krv_search_posts', function () {
	check_ajax_referer( 'krv_search_posts' );

	$q = isset( $_REQUEST['q'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['q'] ) ) : '';
	if ( mb_strlen( $q ) < 2 ) {
		wp_send_json_success( [] );
	}

	$results = get_posts( [
		's'              => $q,
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 15,
		'orderby'        => 'relevance',
		'suppress_filters' => true,
		'no_found_rows'  => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	] );

	$data = array_map( function ( $p ) {
		return [
			'id'    => $p->ID,
			'title' => $p->post_title,
		];
	}, $results );

	wp_send_json_success( $data );
} );
