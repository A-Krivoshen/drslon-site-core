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

	ob_start();
	?>
	<div class="krv-service-pages-wrap">
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

add_action( 'wp_head', function () {
	if ( is_admin() || ! krv_page_has_ui_shortcode( [ 'krv_services_pages_showcase' ] ) ) {
		return;
	}
	?>
	<style>
		.krv-service-pages-wrap {
			--krv-accent: #5181fe;
			--krv-accent-hover: #4169d4;
			--krv-card-bg: #fff;
			--krv-text-main: #333;
			--krv-text-soft: #666;
			--krv-card-radius: 10px;
			--krv-card-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
			--krv-card-shadow-hover: 0 6px 15px rgba(0, 0, 0, 0.15);
			max-width: 1200px;
			margin: 0 auto;
			padding: 15px;
			font-family: "Poppins", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
		}

		.krv-service-pages-wrap,
		.krv-service-pages-wrap * {
			box-sizing: border-box;
		}

		.krv-service-pages-group + .krv-service-pages-group {
			margin-top: 34px;
		}

		.krv-service-pages-group-title {
			margin: 0 0 16px;
			font-size: 1.55rem;
			color: var(--krv-text-main);
		}

		.krv-service-pages-grid {
			display: grid;
			grid-template-columns: repeat(4, minmax(0, 1fr));
			gap: 18px;
			width: 100%;
		}

		.krv-service-page-card {
			display: block;
			width: 100%;
			text-decoration: none;
			color: inherit;
			background: var(--krv-card-bg);
			border: 1px solid var(--krv-accent);
			border-radius: var(--krv-card-radius);
			box-shadow: var(--krv-card-shadow);
			transition: box-shadow 0.25s ease, transform 0.25s ease, border-color 0.25s ease, background 0.25s ease;
			overflow: hidden;
			height: 100%;
		}

		.krv-service-page-card:hover {
			transform: translateY(-2px);
			border-color: var(--krv-accent-hover);
			box-shadow: var(--krv-card-shadow-hover);
			background: #fcfdff;
		}

		.krv-service-page-card-inner {
			display: flex;
			flex-direction: column;
			min-height: 250px;
			padding: 12px;
			text-align: center;
		}

		.krv-service-page-image-wrap {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 100%;
			aspect-ratio: 16 / 9;
			margin-bottom: 12px;
			border-radius: 8px;
			overflow: hidden;
			background: #f7faff;
		}

		.krv-service-page-image {
			display: block !important;
			width: 100% !important;
			height: 100% !important;
			object-fit: cover;
			object-position: center;
			border: 0 !important;
			box-shadow: none !important;
		}

		.krv-service-page-no-image {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 58px;
			height: 58px;
			border-radius: 50%;
			background: #eaf1ff;
			color: var(--krv-accent);
			font-size: 1.4rem;
			font-weight: 700;
		}

		.krv-service-page-title {
			margin: 0;
			font-size: 0.98rem;
			line-height: 1.35;
			color: var(--krv-text-main);
			word-break: break-word;
			display: -webkit-box;
			-webkit-line-clamp: 2;
			-webkit-box-orient: vertical;
			overflow: hidden;
			min-height: 2.7em;
		}

		.krv-service-page-description {
			margin: 8px 0 0;
			font-size: 0.84rem;
			line-height: 1.45;
			color: var(--krv-text-soft);
			display: -webkit-box;
			-webkit-line-clamp: 3;
			-webkit-box-orient: vertical;
			overflow: hidden;
		}

		@media (max-width: 1100px) {
			.krv-service-pages-grid {
				grid-template-columns: repeat(3, minmax(0, 1fr));
			}
		}

		@media (max-width: 768px) {
			.krv-service-pages-wrap {
				padding: 12px 8px;
			}

			.krv-service-pages-group-title {
				font-size: 1.3rem;
			}

			.krv-service-pages-grid {
				grid-template-columns: repeat(2, minmax(0, 1fr));
				gap: 12px;
			}

			.krv-service-page-card-inner {
				min-height: 205px;
				padding: 10px 8px;
			}

			.krv-service-page-title {
				font-size: 0.88rem;
				min-height: 2.5em;
			}

			.krv-service-page-description {
				display: none;
			}
		}

		@media (max-width: 380px) {
			.krv-service-pages-wrap {
				padding: 10px 5px;
			}

			.krv-service-pages-grid {
				grid-template-columns: repeat(2, minmax(0, 1fr));
				gap: 10px;
			}

			.krv-service-page-card-inner {
				min-height: 190px;
				padding: 9px 7px;
			}

			.krv-service-page-title {
				font-size: 0.82rem;
			}
		}
	</style>
	<?php
}, 102 );
