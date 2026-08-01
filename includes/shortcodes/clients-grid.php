<?php
/**
 * Clients grid shortcode [krv_clients_grid]
 *
 * Integrator UX: stable order, logo-first cards, quiet external affordance,
 * no layout thrash from random shuffle by default.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Short display name for client cards (grid-friendly).
 */
function krv_client_display_title( int $post_id, string $title ): string {
	$map = array(
		9168 => 'Стоматолог Мурашова',
		8450 => 'Храм в Рогово',
		8423 => 'Соловьи.Ай.Ти',
		8081 => 'Технолидер',
		6671 => 'СКД',
		6115 => 'Форновогаз KZ',
		6113 => 'АГНКС',
		6111 => 'НАКС ЦНИИТМАШ',
		5702 => 'Fornovo Gas',
		5701 => 'АЦ ЦНИИТМАШ',
		6151 => 'Метком-Калуга',
		6147 => 'GroupST',
		9184 => 'Softcomlan',
		9244 => 'LUMISTEK',
		7693 => 'Cambobiz',
		6112 => 'Designers from Russia',
	);

	if ( isset( $map[ $post_id ] ) ) {
		return $map[ $post_id ];
	}

	$short = preg_replace( '/^ООО\s*[«"]?\s*/u', '', $title );
	$short = preg_replace( '/\s*[»"]\s*$/u', '', (string) $short );
	$short = trim( (string) $short );

	if ( $short !== '' && mb_strlen( $short, 'UTF-8' ) <= 42 ) {
		return $short;
	}

	if ( mb_strlen( $title, 'UTF-8' ) > 42 ) {
		return rtrim( mb_substr( $title, 0, 40, 'UTF-8' ) ) . '…';
	}

	return $title;
}

/**
 * Two-letter monogram fallback when logo is missing.
 */
function krv_client_monogram( string $title ): string {
	$title = trim( preg_replace( '/\s+/u', ' ', $title ) ?? '' );
	if ( $title === '' ) {
		return '?';
	}

	$skip  = array( 'ООО', 'ИП', 'АО', 'ЗАО', 'ОАО', 'LLC', 'LTD', 'S.P.A.', 'SPA', 'THE', 'FROM' );
	$parts = preg_split( '/[\s«»"“”\'\.\,\/\-–—]+/u', $title, -1, PREG_SPLIT_NO_EMPTY );
	$letters = array();
	if ( is_array( $parts ) ) {
		foreach ( $parts as $part ) {
			$up = mb_strtoupper( $part, 'UTF-8' );
			if ( in_array( $up, $skip, true ) ) {
				continue;
			}
			if ( ! preg_match( '/[\p{L}\p{N}]/u', $part ) ) {
				continue;
			}
			$letters[] = mb_strtoupper( mb_substr( $part, 0, 1, 'UTF-8' ), 'UTF-8' );
			if ( count( $letters ) >= 2 ) {
				break;
			}
		}
	}

	if ( count( $letters ) >= 2 ) {
		return $letters[0] . $letters[1];
	}
	if ( count( $letters ) === 1 ) {
		$rest = mb_substr( $title, 1, 1, 'UTF-8' );
		if ( preg_match( '/[\p{L}\p{N}]/u', (string) $rest ) ) {
			return $letters[0] . mb_strtoupper( $rest, 'UTF-8' );
		}
		return $letters[0];
	}

	return mb_strtoupper( mb_substr( $title, 0, 1, 'UTF-8' ), 'UTF-8' );
}

/**
 * Whether client thumbnail is usable as a logo.
 */
function krv_client_logo_is_usable( int $post_id ): bool {
	$thumb_id = (int) get_post_thumbnail_id( $post_id );
	if ( $thumb_id <= 0 ) {
		return false;
	}

	$src = wp_get_attachment_image_src( $thumb_id, 'full' );
	if ( ! is_array( $src ) || empty( $src[0] ) ) {
		return false;
	}

	$w = (int) ( $src[1] ?? 0 );
	$h = (int) ( $src[2] ?? 0 );
	if ( $w > 0 && $h > 0 && ( $w < 48 || $h < 24 ) ) {
		return false;
	}

	$file = strtolower( basename( (string) $src[0] ) );
	if ( preg_match( '/screenshot|favicon|sign\.png|dummy|placeholder|kandinsky/i', $file ) ) {
		return false;
	}

	if ( $w === 1 && $h === 1 ) {
		return false;
	}

	return true;
}

/**
 * Showcase order for homepage (logo strength + industry mix).
 *
 * @return array<int,int> post_id => rank
 */
function krv_clients_showcase_order(): array {
	return array(
		9244 => 10,  // LUMISTEK
		6113 => 20,  // АГНКС
		5702 => 30,  // Fornovo Gas
		8081 => 40,  // Технолидер
		8423 => 50,  // Соловьи
		9184 => 60,  // Softcomlan
		5701 => 70,  // АЦ ЦНИИТМАШ
		6111 => 80,  // НАКС
		6115 => 90,  // Форновогаз KZ
		6147 => 100, // GroupST
		7693 => 110, // Cambobiz
		9168 => 120, // Мурашова
		8450 => 130, // Храм
		6671 => 140, // СКД
		6112 => 150, // DFR
		6151 => 160, // Метком (static, last)
	);
}

add_shortcode( 'krv_clients_grid', function ( $atts = [] ) {
	$atts = shortcode_atts(
		[
			// Stable order by default (integrator UX). random=1 for variety.
			'random' => '0',
		],
		$atts,
		'krv_clients_grid'
	);

	$q = new WP_Query(
		[
			'post_type'      => 'client',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => [
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			],
			'no_found_rows'  => true,
		]
	);

	if ( ! $q->have_posts() ) {
		return '';
	}

	$posts = $q->posts;
	$order = krv_clients_showcase_order();
	usort(
		$posts,
		static function ( $a, $b ) use ( $order ) {
			$ra = $order[ (int) $a->ID ] ?? 500;
			$rb = $order[ (int) $b->ID ] ?? 500;
			if ( $ra === $rb ) {
				return (int) $b->ID <=> (int) $a->ID;
			}
			return $ra <=> $rb;
		}
	);

	$post_count = count( $posts );
	$randomize  = $atts['random'] === '1';

	ob_start();
	?>
	<section class="krv-clients-grid-wrap" id="klienty" aria-labelledby="krv-clients-heading">
		<header class="krv-clients-grid-header">
			<h2 id="krv-clients-heading">Клиенты</h2>
			<p class="krv-clients-grid-lead">Реальные заказчики: промышленность, e&#8209;commerce, медиа и сервисный бизнес</p>
			<p class="krv-clients-grid-meta"><?php echo esc_html( (string) $post_count ); ?> компаний · проекты, которые можно открыть</p>
		</header>

		<div class="krv-clients-grid"<?php echo $randomize ? ' data-random-grid="1"' : ''; ?>>
			<?php
			foreach ( $posts as $post ) :
				$post_id       = (int) $post->ID;
				$title_raw     = get_the_title( $post );
				$title_display = krv_client_display_title( $post_id, $title_raw );
				$url           = trim( (string) get_post_meta( $post_id, 'client_url', true ) );
				$description   = trim( wp_strip_all_tags( (string) get_post_meta( $post_id, 'client_description', true ) ) );
				$monogram      = krv_client_monogram( $title_display );
				$show_logo     = krv_client_logo_is_usable( $post_id );

				$thumb = $show_logo
					? get_the_post_thumbnail(
						$post_id,
						'medium',
						[
							'class'          => 'krv-client-logo',
							'loading'        => 'lazy',
							'decoding'       => 'async',
							'alt'            => $title_display,
							'data-no-lazy'   => '1',
							'data-skip-lazy' => '1',
						]
					)
					: '';

				$is_link    = $url !== '';
				$card_class = 'krv-client-card' . ( $is_link ? ' krv-client-card--link' : ' krv-client-card--static' );
				$aria       = $is_link
					? $title_display . '. Открыть сайт клиента в новой вкладке'
					: $title_display;

				if ( $is_link ) {
					echo '<a class="' . esc_attr( $card_class ) . '" href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $aria ) . '">';
				} else {
					echo '<div class="' . esc_attr( $card_class ) . '" role="group" aria-label="' . esc_attr( $aria ) . '">';
				}
				?>
					<div class="krv-client-card-inner">
						<?php if ( $is_link ) : ?>
							<span class="krv-client-ext" aria-hidden="true" title="Внешняя ссылка">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
									<path d="M14 5h5v5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M10 14L19 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M19 13v5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
						<?php endif; ?>

						<div class="krv-client-logo-wrap">
							<?php if ( $thumb ) : ?>
								<?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php else : ?>
								<div class="krv-client-no-logo" aria-hidden="true"><?php echo esc_html( $monogram ); ?></div>
							<?php endif; ?>
						</div>

						<h3 class="krv-client-title"><?php echo esc_html( $title_display ); ?></h3>

						<?php if ( $description !== '' ) : ?>
							<p class="krv-client-description"><?php echo esc_html( $description ); ?></p>
						<?php endif; ?>
					</div>
				<?php
				echo $is_link ? '</a>' : '</div>';
			endforeach;
			?>
		</div>
	</section>
	<?php
	wp_reset_postdata();

	return ob_get_clean();
} );

add_action(
	'save_post_client',
	function ( $post_id ) {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}
		if ( class_exists( 'DrSlon_Cache_Purge_Bridge' ) ) {
			DrSlon_Cache_Purge_Bridge::purge_page_cache( DRSLON_HOME_PAGE_ID );
		}
	},
	20
);

add_action(
	'deleted_post',
	function ( $post_id, $post ) {
		if ( $post instanceof WP_Post && 'client' === $post->post_type && class_exists( 'DrSlon_Cache_Purge_Bridge' ) ) {
			DrSlon_Cache_Purge_Bridge::purge_page_cache( DRSLON_HOME_PAGE_ID );
		}
	},
	20,
	2
);

add_action(
	'wp_footer',
	function () {
		if ( is_admin() || ! krv_page_has_ui_shortcode( [ 'krv_clients_grid', 'krv_services_landing' ] ) ) {
			return;
		}
		?>
	<script>
	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.krv-clients-grid[data-random-grid="1"]').forEach(function (grid) {
			var items = Array.from(grid.children);
			for (var i = items.length - 1; i > 0; i--) {
				var j = Math.floor(Math.random() * (i + 1));
				var t = items[i]; items[i] = items[j]; items[j] = t;
			}
			items.forEach(function (item) { grid.appendChild(item); });
		});
	});
	</script>
		<?php
	},
	120
);
