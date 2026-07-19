<?php
/**
 * Partners grid shortcode [krv_partners_grid]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/** =========================
 *  11) Partners grid shortcode
 *  ========================= */
/**
 * Build partners grouped by category for the partners grid shortcode.
 *
 * @return array{terms: WP_Term[], partners_by_term: array<int, WP_Post[]>}|null
 */
function krv_partners_grid_get_grouped_data(): ?array {
	$cache_key = 'krv_partners_grid_v1';
	$cached    = get_transient( $cache_key );

	if ( is_array( $cached ) && isset( $cached['terms'], $cached['partners_by_term'] ) ) {
		return $cached;
	}

	$terms = get_terms( [
		'taxonomy'   => 'partner_category',
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	] );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return null;
	}

	$q = new WP_Query( [
		'post_type'      => 'partner',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => [
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		],
		'no_found_rows'  => true,
	] );

	if ( ! $q->have_posts() ) {
		return null;
	}

	$partner_ids = wp_list_pluck( $q->posts, 'ID' );
	update_postmeta_cache( $partner_ids );

	$partners_by_term = [];

	foreach ( $terms as $term ) {
		$partners_by_term[ $term->term_id ] = [];
	}

	while ( $q->have_posts() ) {
		$q->the_post();

		$post_id = get_the_ID();
		$terms_for_post = get_the_terms( $post_id, 'partner_category' );

		if ( is_wp_error( $terms_for_post ) || empty( $terms_for_post ) ) {
			continue;
		}

		foreach ( $terms_for_post as $term ) {
			if ( ! isset( $partners_by_term[ $term->term_id ] ) ) {
				continue;
			}

			$partners_by_term[ $term->term_id ][] = get_post( $post_id );
		}
	}

	wp_reset_postdata();

	$grouped = [
		'terms'            => $terms,
		'partners_by_term' => $partners_by_term,
	];

	set_transient( $cache_key, $grouped, HOUR_IN_SECONDS );

	return $grouped;
}

add_shortcode( 'krv_partners_grid', function ( $atts = [] ) {
	$atts = shortcode_atts( [
		'category' => '',
	], $atts, 'krv_partners_grid' );

	$grouped = krv_partners_grid_get_grouped_data();

	if ( $grouped === null ) {
		return '';
	}

	$terms            = $grouped['terms'];
	$partners_by_term = $grouped['partners_by_term'];

	if ( $atts['category'] !== '' ) {
		$category_slug = sanitize_title( $atts['category'] );
		$terms         = array_values( array_filter(
			$terms,
			static function ( $term ) use ( $category_slug ) {
				return $term->slug === $category_slug;
			}
		) );
	}

	if ( empty( $terms ) ) {
		return '';
	}

	$intro_heading = '';
	$intro_text    = '';

	if ( function_exists( 'get_field' ) ) {
		$intro_heading = trim( (string) get_field( 'partners_intro_heading', 'krv-partners' ) );
		$intro_text    = trim( (string) get_field( 'partners_intro_text', 'krv-partners' ) );
	}

	ob_start();
	?>
	<div class="krv-partners-wrap">
		<?php if ( $intro_heading !== '' || $intro_text !== '' ) : ?>
			<div class="krv-partners-header">
				<?php if ( $intro_heading !== '' ) : ?>
					<h2><?php echo esc_html( $intro_heading ); ?></h2>
				<?php endif; ?>
				<?php if ( $intro_text !== '' ) : ?>
					<p><?php echo esc_html( $intro_text ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php foreach ( $terms as $term ) : ?>
			<?php
			$partners = $partners_by_term[ $term->term_id ] ?? [];

			if ( empty( $partners ) ) {
				continue;
			}
			?>

			<section class="krv-partners-group">
				<h3 class="krv-partners-group-title"><?php echo esc_html( $term->name ); ?></h3>

				<div class="krv-partners-grid">
					<?php foreach ( $partners as $partner_post ) : ?>
						<?php
						$post_id      = $partner_post->ID;
						$title        = get_the_title( $partner_post );
						$url          = trim( (string) get_post_meta( $post_id, 'partner_url', true ) );
						$description  = trim( wp_strip_all_tags( (string) get_post_meta( $post_id, 'partner_description', true ) ) );
						$badge        = trim( (string) get_post_meta( $post_id, 'partner_badge', true ) );
						$is_featured  = (bool) get_post_meta( $post_id, 'partner_is_featured', true );
						$is_nofollow  = (bool) get_post_meta( $post_id, 'partner_nofollow', true );
						$is_sponsored = (bool) get_post_meta( $post_id, 'partner_sponsored', true );

						$rel = [ 'noopener', 'noreferrer' ];

						if ( $is_nofollow ) {
							$rel[] = 'nofollow';
						}

						if ( $is_sponsored ) {
							$rel[] = 'sponsored';
						}

						$thumb = get_the_post_thumbnail( $post_id, 'medium', [
							'class'   => 'krv-partner-logo',
							'loading' => 'lazy',
							'alt'     => $title,
						] );

						$card_classes = 'krv-partner-card';
						if ( $is_featured ) {
							$card_classes .= ' krv-partner-card--featured';
						}

						$tag_open = $url
							? '<a class="' . esc_attr( $card_classes ) . '" href="' . esc_url( $url ) . '" target="_blank" rel="' . esc_attr( implode( ' ', array_unique( $rel ) ) ) . '">'
							: '<div class="' . esc_attr( $card_classes ) . ' krv-partner-card--static">';

						$tag_close = $url ? '</a>' : '</div>';
						?>
						<?php echo $tag_open; ?>
							<div class="krv-partner-card-inner">
								<div class="krv-partner-logo-wrap">
									<?php if ( $thumb ) : ?>
										<?php echo $thumb; ?>
									<?php else : ?>
										<div class="krv-partner-no-logo"><?php echo esc_html( mb_substr( $title, 0, 1, 'UTF-8' ) ); ?></div>
									<?php endif; ?>
								</div>

								<?php if ( $badge !== '' ) : ?>
									<div class="krv-partner-badge"><?php echo esc_html( $badge ); ?></div>
								<?php endif; ?>

								<h4 class="krv-partner-title"><?php echo esc_html( $title ); ?></h4>

								<?php if ( $description !== '' ) : ?>
									<p class="krv-partner-description"><?php echo esc_html( $description ); ?></p>
								<?php endif; ?>
							</div>
						<?php echo $tag_close; ?>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endforeach; ?>
	</div>
	<?php

	return ob_get_clean();
} );

function krv_partners_grid_invalidate_cache(): void {
	delete_transient( 'krv_partners_grid_v1' );

	if ( class_exists( 'DrSlon_Cache_Purge_Bridge' ) ) {
		DrSlon_Cache_Purge_Bridge::purge_page_cache( DRSLON_PARTNERS_PAGE_ID );
	}
}

add_action( 'save_post_partner', function ( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	krv_partners_grid_invalidate_cache();
}, 20 );

add_action( 'deleted_post', function ( $post_id, $post ) {
	unset( $post_id );

	if ( $post instanceof WP_Post && 'partner' === $post->post_type ) {
		krv_partners_grid_invalidate_cache();
	}
}, 20, 2 );

add_action( 'set_object_terms', function ( $object_id, $terms, $tt_ids, $taxonomy ) {
	unset( $terms, $tt_ids );

	if ( 'partner_category' === $taxonomy && 'partner' === get_post_type( $object_id ) ) {
		krv_partners_grid_invalidate_cache();
	}
}, 20, 4 );

add_action( 'created_partner_category', 'krv_partners_grid_invalidate_cache', 20, 0 );
add_action( 'edited_partner_category', 'krv_partners_grid_invalidate_cache', 20, 0 );
add_action( 'delete_partner_category', 'krv_partners_grid_invalidate_cache', 20, 0 );
