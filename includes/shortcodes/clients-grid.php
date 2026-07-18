<?php
/**
 * Clients grid shortcode + styles + client-side shuffle.
 * Usage: [krv_clients_grid]
 * Extracted from legacy-arkai-child-functions.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode( 'krv_clients_grid', function () {
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

	ob_start();
	?>
	<div class="krv-clients-grid-wrap">
		<div class="krv-clients-grid-header">
			<h2>Клиенты</h2>
			<p>Компании и проекты, с которыми я работал</p>
		</div>

		<div class="krv-clients-grid" data-random-grid="1">
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
					'alt'     => esc_attr( $title ),
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

add_action( 'wp_head', function () {
	if ( is_admin() || ! krv_page_has_ui_shortcode( [ 'krv_clients_grid', 'krv_services_landing' ] ) ) {
		return;
	}
	?>
	<style>
		.krv-clients-grid-wrap {
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

		.krv-clients-grid-wrap,
		.krv-clients-grid-wrap * {
			box-sizing: border-box;
		}

		.krv-clients-grid-header {
			text-align: center;
			margin-bottom: 20px;
		}

		.krv-clients-grid-header h2 {
			margin: 0 0 10px;
			font-size: 2rem;
			color: var(--krv-text-main);
		}

		.krv-clients-grid-header p {
			margin: 0;
			font-size: 1rem;
			color: var(--krv-text-soft);
		}

		.krv-clients-grid {
			display: grid;
			grid-template-columns: repeat(4, minmax(0, 1fr));
			gap: 18px;
			width: 100%;
		}

		.krv-client-card {
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
		}

		.krv-client-card:hover {
			transform: translateY(-2px);
			border-color: var(--krv-accent-hover);
			box-shadow: var(--krv-card-shadow-hover);
			background: #fcfdff;
		}

		.krv-client-card--static:hover {
			transform: none;
			border-color: var(--krv-accent);
			box-shadow: var(--krv-card-shadow);
			background: var(--krv-card-bg);
		}

		.krv-client-card-inner {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: flex-start;
			min-height: 168px;
			padding: 14px 12px;
			text-align: center;
		}

		.krv-client-logo-wrap {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 100%;
			height: 76px;
			margin-bottom: 12px;
			padding: 8px;
			overflow: hidden;
		}

		.krv-client-logo {
			display: block !important;
			width: auto !important;
			height: auto !important;
			max-width: 100% !important;
			max-height: 60px !important;
			margin: 0 auto !important;
			object-fit: contain;
			object-position: center;
			border: 0 !important;
			box-shadow: none !important;
		}

		.krv-client-no-logo {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 56px;
			height: 56px;
			border-radius: 50%;
			background: #eaf1ff;
			color: var(--krv-accent);
			font-size: 1.4rem;
			font-weight: 700;
		}

		.krv-client-title {
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

		.krv-client-description {
			margin: 8px 0 0;
			font-size: 0.84rem;
			line-height: 1.4;
			color: var(--krv-text-soft);
			display: -webkit-box;
			-webkit-line-clamp: 2;
			-webkit-box-orient: vertical;
			overflow: hidden;
			min-height: 2.8em;
		}

		@media (max-width: 1100px) {
			.krv-clients-grid {
				grid-template-columns: repeat(3, minmax(0, 1fr));
			}
		}

		@media (max-width: 768px) {
			.krv-clients-grid-wrap {
				padding: 12px 8px;
			}

			.krv-clients-grid-header h2 {
				font-size: 1.8rem;
			}

			.krv-clients-grid-header p {
				font-size: 0.95rem;
			}

			.krv-clients-grid {
				grid-template-columns: repeat(2, minmax(0, 1fr));
				gap: 12px;
			}

			.krv-client-card-inner {
				min-height: 146px;
				padding: 10px 8px;
			}

			.krv-client-logo-wrap {
				height: 54px;
				margin-bottom: 10px;
				padding: 4px;
			}

			.krv-client-title {
				font-size: 0.88rem;
				min-height: 2.5em;
			}

			.krv-client-description {
				display: none;
			}
		}

		@media (max-width: 380px) {
			.krv-clients-grid-wrap {
				padding: 10px 5px;
			}

			.krv-clients-grid-header h2 {
				font-size: 1.5rem;
			}

			.krv-clients-grid-header p {
				font-size: 0.9rem;
			}

			.krv-clients-grid {
				grid-template-columns: repeat(2, minmax(0, 1fr));
				gap: 10px;
			}

			.krv-client-card-inner {
				min-height: 136px;
				padding: 9px 7px;
			}

			.krv-client-logo-wrap {
				height: 52px;
			}

			.krv-client-title {
				font-size: 0.82rem;
			}
		}
	</style>
	<?php
}, 99 );

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
