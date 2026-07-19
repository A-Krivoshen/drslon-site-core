<?php
/**
 * Clients grid shortcode [krv_clients_grid]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/** =========================
 *  10) Clients grid shortcode + styles + random
 *  ========================= */
add_shortcode( 'krv_clients_grid', function ( $atts = [] ) {
	$atts = shortcode_atts( [
		'random' => '1',
	], $atts, 'krv_clients_grid' );

	$q = new WP_Query( [
		'post_type'      => 'client',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => [
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		],
		'no_found_rows'  => true,
	] );

	if ( ! $q->have_posts() ) {
		return '';
	}

	$post_count = (int) $q->post_count;
	$grid_rows  = max( 1, (int) ceil( $post_count / 4 ) );
	$grid_min_h = ( $grid_rows * 186 ) + ( max( 0, $grid_rows - 1 ) * 18 );
	$randomize  = $atts['random'] !== '0';

	ob_start();
	?>
	<div class="krv-clients-grid-wrap">
		<div class="krv-clients-grid-header">
			<h2>Клиенты</h2>
			<p>Компании и проекты, с которыми я работал</p>
		</div>

		<div class="krv-clients-grid"<?php echo $randomize ? ' data-random-grid="1"' : ''; ?> style="min-height: <?php echo esc_attr( (string) $grid_min_h ); ?>px;">
			<?php
			while ( $q->have_posts() ) :
				$q->the_post();

				$post_id     = get_the_ID();
				$title       = get_the_title();
				$url         = trim( (string) get_post_meta( $post_id, 'client_url', true ) );
				$description = trim( wp_strip_all_tags( (string) get_post_meta( $post_id, 'client_description', true ) ) );

				$thumb = get_the_post_thumbnail( $post_id, 'medium', [
					'class'   => 'krv-client-logo',
					'loading' => 'lazy',
					'alt'     => $title,
				] );

				$tag_open = $url
					? '<a class="krv-client-card" href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">'
					: '<div class="krv-client-card krv-client-card--static">';

				$tag_close = $url ? '</a>' : '</div>';
				?>
				<?php echo $tag_open; ?>
					<div class="krv-client-card-inner">
						<div class="krv-client-logo-wrap">
							<?php if ( $thumb ) : ?>
								<?php echo $thumb; ?>
							<?php else : ?>
								<div class="krv-client-no-logo"><?php echo esc_html( mb_substr( $title, 0, 1, 'UTF-8' ) ); ?></div>
							<?php endif; ?>
						</div>

						<h3 class="krv-client-title"><?php echo esc_html( $title ); ?></h3>

						<?php if ( $description !== '' ) : ?>
							<p class="krv-client-description"><?php echo esc_html( $description ); ?></p>
						<?php endif; ?>
					</div>
				<?php echo $tag_close; ?>
			<?php endwhile; ?>
		</div>
	</div>
	<?php
	wp_reset_postdata();

	return ob_get_clean();
} );

add_action( 'save_post_client', function ( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	if ( class_exists( 'DrSlon_Cache_Purge_Bridge' ) ) {
		DrSlon_Cache_Purge_Bridge::purge_page_cache( DRSLON_HOME_PAGE_ID );
	}
}, 20 );

add_action( 'deleted_post', function ( $post_id, $post ) {
	if ( $post instanceof WP_Post && 'client' === $post->post_type && class_exists( 'DrSlon_Cache_Purge_Bridge' ) ) {
		DrSlon_Cache_Purge_Bridge::purge_page_cache( DRSLON_HOME_PAGE_ID );
	}
}, 20, 2 );


add_action( 'wp_footer', function () {
	if ( is_admin() || ! krv_page_has_ui_shortcode( [ 'krv_clients_grid', 'krv_services_landing' ] ) ) {
		return;
	}
	?>
	<script>
	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.krv-clients-grid[data-random-grid="1"]').forEach(function (grid) {
			const items = Array.from(grid.children);
			for (let i = items.length - 1; i > 0; i--) {
				const j = Math.floor(Math.random() * (i + 1));
				[items[i], items[j]] = [items[j], items[i]];
			}
			items.forEach(function (item) {
				grid.appendChild(item);
			});
		});
	});
	</script>
	<?php
}, 120 );
