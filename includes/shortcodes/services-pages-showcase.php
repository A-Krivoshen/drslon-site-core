<?php
/**
 * Services pages showcase shortcode [krv_services_pages_showcase]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/** =========================
 *  12) Services pages showcase
 *  ========================= */
function krv_services_showcase_normalize_sections( array $sections ): array {
	$normalized = [];

	foreach ( $sections as $section ) {
		if ( ! is_array( $section ) ) {
			continue;
		}

		$page_ids = isset( $section['section_pages'] ) && is_array( $section['section_pages'] )
			? array_values( array_filter( array_map( 'intval', $section['section_pages'] ) ) )
			: [];

		if ( empty( $page_ids ) ) {
			continue;
		}

		$normalized[] = [
			'section_title' => isset( $section['section_title'] ) ? trim( (string) $section['section_title'] ) : '',
			'section_pages' => $page_ids,
		];
	}

	return $normalized;
}

/**
 * Cached normalized services showcase sections from ACF options.
 *
 * @return array<int, array{section_title: string, section_pages: int[]}>
 */
function krv_services_showcase_get_sections(): array {
	$cache_key = 'krv_services_showcase_v1';
	$cached    = get_transient( $cache_key );

	if ( is_array( $cached ) ) {
		return $cached;
	}

	if ( ! function_exists( 'get_field' ) ) {
		return [];
	}

	$sections = get_field( 'krv_services_sections', 'option' );

	if ( empty( $sections ) || ! is_array( $sections ) ) {
		return [];
	}

	$normalized = krv_services_showcase_normalize_sections( $sections );

	set_transient( $cache_key, $normalized, HOUR_IN_SECONDS );

	return $normalized;
}

/**
 * Returns a section title only when it should be visible on the frontend.
 */
function krv_services_showcase_visible_section_title( string $title, int $section_count ): string {
	if ( $title === '' ) {
		return '';
	}

	// One section: page intro already acts as the heading.
	if ( $section_count < 2 ) {
		return '';
	}

	// Skip broken/partial titles like "кон".
	if ( mb_strlen( $title, 'UTF-8' ) < 4 ) {
		return '';
	}

	return $title;
}

add_shortcode( 'krv_services_pages_showcase', function () {
	$sections      = krv_services_showcase_get_sections();
	$section_count = count( $sections );

	if ( $section_count === 0 ) {
		return '';
	}

	$all_page_ids = [];

	foreach ( $sections as $section ) {
		foreach ( $section['section_pages'] as $page_id ) {
			$all_page_ids[] = (int) $page_id;
		}
	}

	$all_page_ids = array_values( array_unique( $all_page_ids ) );
	$pages_by_id  = [];

	if ( ! empty( $all_page_ids ) ) {
		$page_query = new WP_Query( [
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'post__in'       => $all_page_ids,
			'orderby'        => 'post__in',
			'posts_per_page' => count( $all_page_ids ),
			'no_found_rows'  => true,
		] );

		while ( $page_query->have_posts() ) {
			$page_query->the_post();
			$pages_by_id[ get_the_ID() ] = get_post();
		}

		wp_reset_postdata();
	}

	$intro_heading = '';
	if ( function_exists( 'get_field' ) ) {
		$intro_heading = trim( (string) get_field( 'showcase_intro_heading', 'krv-services-showcase' ) );
	}

	ob_start();
	?>
	<div class="krv-service-pages-wrap">
		<?php if ( $intro_heading !== '' ) : ?>
			<h2 class="krv-showcase-intro"><?php echo esc_html( $intro_heading ); ?></h2>
		<?php endif; ?>

		<?php foreach ( $sections as $section ) : ?>
			<?php
			$section_title = krv_services_showcase_visible_section_title(
				$section['section_title'],
				$section_count
			);
			$page_ids = $section['section_pages'];
			?>
			<section class="krv-service-pages-group">
				<?php if ( $section_title !== '' ) : ?>
					<h2 class="krv-service-pages-group-title"><?php echo esc_html( $section_title ); ?></h2>
				<?php endif; ?>

				<div class="krv-service-pages-grid">
					<?php foreach ( $page_ids as $page_id ) : ?>
						<?php
						$page = $pages_by_id[ $page_id ] ?? null;

						if ( ! $page || $page->post_status !== 'publish' ) {
							continue;
						}

						$title = get_the_title( $page );
						$url   = get_permalink( $page );

						$description = trim( wp_strip_all_tags( get_the_excerpt( $page ) ) );
						if ( $description === '' ) {
							$description = wp_trim_words(
								wp_strip_all_tags( strip_shortcodes( (string) $page->post_content ) ),
								18,
								'…'
							);
						}

						$thumb = get_the_post_thumbnail( $page, 'medium_large', [
							'class'   => 'krv-service-page-image',
							'loading' => 'lazy',
							'alt'     => esc_attr( $title ),
						] );
						?>
						<a class="krv-service-page-card" href="<?php echo esc_url( $url ); ?>">
							<div class="krv-service-page-card-inner">
								<div class="krv-service-page-image-wrap">
									<?php if ( $thumb ) : ?>
										<?php echo $thumb; ?>
									<?php else : ?>
										<div class="krv-service-page-no-image"><?php echo esc_html( mb_substr( $title, 0, 1, 'UTF-8' ) ); ?></div>
									<?php endif; ?>
								</div>

								<h3 class="krv-service-page-title"><?php echo esc_html( $title ); ?></h3>

								<?php if ( $description !== '' ) : ?>
									<p class="krv-service-page-description"><?php echo esc_html( $description ); ?></p>
								<?php endif; ?>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
} );

