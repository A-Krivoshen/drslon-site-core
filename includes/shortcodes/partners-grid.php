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
/**
 * Preferred display order for partner_category (lower = higher on page).
 * ИИ выше банков; «Прочее» в конце.
 *
 * @return array<string, int> slug => weight
 */
function krv_partners_category_order_map(): array {
	return [
		'hosting'      => 10, // Хостинг
		'cloud'        => 20, // Cloud
		'ai'           => 30, // ИИ (выше банков)
		'platezhi'     => 40, // Платежи
		'banki'        => 50, // Банки
		'reklama'      => 60, // Реклама
		'obuchenie'    => 70, // Обучение
		'instrumenty'  => 80, // Инструменты
		'domeny'       => 90, // Домены
		'prochee'      => 100, // Прочее
	];
}

/**
 * Sort partner_category terms by preferred map, then by name.
 *
 * @param WP_Term[] $terms Terms list.
 * @return WP_Term[]
 */
function krv_partners_sort_category_terms( array $terms ): array {
	$order = krv_partners_category_order_map();

	usort(
		$terms,
		static function ( $a, $b ) use ( $order ) {
			$wa = $order[ $a->slug ] ?? 500;
			$wb = $order[ $b->slug ] ?? 500;

			if ( $wa !== $wb ) {
				return $wa <=> $wb;
			}

			return strcasecmp( (string) $a->name, (string) $b->name );
		}
	);

	return $terms;
}

function krv_partners_grid_get_grouped_data(): ?array {
	// v2: custom category order (ИИ above Банки, etc.)
	$cache_key = 'krv_partners_grid_v2';
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

	$terms = krv_partners_sort_category_terms( $terms );

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

	// SEO defaults if ACF intro empty.
	if ( $intro_heading === '' ) {
		$intro_heading = 'Сервисы, с которыми работаю';
	}
	if ( $intro_text === '' ) {
		$intro_text = 'Хостинг, облака, платежи, банки, реклама, обучение и AI-инструменты. Часть ссылок партнёрские: я ими пользуюсь сам или проверял на проектах.';
	}

	$page_h1 = 'Партнёры и рекомендованные сервисы';
	// Build ItemList for JSON-LD (SEO).
	$schema_items = [];
	$pos          = 0;

	ob_start();
	?>
	<div class="krv-partners-wrap">
		<header class="krv-partners-seo-header">
			<h1 class="krv-partners-h1"><?php echo esc_html( $page_h1 ); ?></h1>
			<p class="krv-partners-lead">
				Подборка сервисов для сайтов и бизнеса: от хостинга и VPS до эквайринга, Директа и AI-агентов.
				Список живой - пополняю то, чем реально пользуюсь. Полный каталог ниже по категориям.
			</p>
		</header>

		<?php if ( $intro_heading !== '' || $intro_text !== '' ) : ?>
			<div class="krv-partners-header">
				<?php if ( $intro_heading !== '' ) : ?>
					<h2 class="krv-partners-intro-title"><?php echo esc_html( $intro_heading ); ?></h2>
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

			<section class="krv-partners-group" id="partners-<?php echo esc_attr( $term->slug ); ?>" aria-labelledby="partners-heading-<?php echo esc_attr( $term->slug ); ?>">
				<h2 class="krv-partners-group-title" id="partners-heading-<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></h2>

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

						// Schema.org ItemList entry (prefer external partner URL).
						$pos++;
						$schema_items[] = [
							'@type'    => 'ListItem',
							'position' => $pos,
							'name'     => $title,
							'url'      => $url !== '' ? $url : get_permalink( $post_id ),
							'description' => $description,
						];
						?>
						<?php echo $tag_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<div class="krv-partner-card-inner">
								<div class="krv-partner-logo-wrap">
									<?php if ( $thumb ) : ?>
										<?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php else : ?>
										<div class="krv-partner-no-logo"><?php echo esc_html( mb_substr( $title, 0, 1, 'UTF-8' ) ); ?></div>
									<?php endif; ?>
								</div>

								<?php if ( $badge !== '' ) : ?>
									<div class="krv-partner-badge"><?php echo esc_html( $badge ); ?></div>
								<?php endif; ?>

								<h3 class="krv-partner-title"><?php echo esc_html( $title ); ?></h3>

								<?php if ( $description !== '' ) : ?>
									<p class="krv-partner-description"><?php echo esc_html( $description ); ?></p>
								<?php endif; ?>
							</div>
						<?php echo $tag_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endforeach; ?>

		<section class="krv-partners-faq" aria-labelledby="partners-faq-heading">
			<h2 id="partners-faq-heading">Частые вопросы</h2>
			<div class="krv-partners-faq-list">
				<details class="krv-partners-faq-item">
					<summary>Это реклама или личные рекомендации?</summary>
					<p>И то и другое честно: часть ссылок партнёрские. В список попадают сервисы, которыми я пользуюсь сам или проверял на проектах. Полный каталог - на этой странице.</p>
				</details>
				<details class="krv-partners-faq-item">
					<summary>Поможете с настройкой хостинга, VPS или Директа?</summary>
					<p>Да. Сами сервисы - у партнёров, а настройку WordPress, Linux/VPS, Nginx, SSL, Директ и ботов можно заказать у меня. Ориентиры по ценам - в <a href="https://krivoshein.site/prays-list/">прайс-листе</a>, связь - через <a href="https://krivoshein.site/contacts/">контакты</a>.</p>
				</details>
				<details class="krv-partners-faq-item">
					<summary>Какой хостинг или VPS выбрать для WordPress?</summary>
					<p>Для типичного WP-сайта обычно хватает Beget, SpaceWeb или Reg.ru. Если нужен свой сервер под Docker/Nginx - CLO, UltraVDS или FirstVDS (в том числе с GPU). Если сомневаетесь - напишите задачу, подскажу связку.</p>
				</details>
			</div>
		</section>
	</div>
	<?php

	// JSON-LD: ItemList + FAQPage (indexable structured data).
	$schema = [
		'@context' => 'https://schema.org',
		'@graph'   => [
			[
				'@type'       => 'CollectionPage',
				'@id'         => home_url( '/partnery/' ) . '#page',
				'url'         => home_url( '/partnery/' ),
				'name'        => $page_h1,
				'description' => 'Подборка партнёрских сервисов Dr.Slon: хостинг, облако, платежи, банки, реклама, обучение и AI-инструменты.',
				'isPartOf'    => [ '@id' => home_url( '/' ) . '#website' ],
				'inLanguage'  => 'ru-RU',
			],
			[
				'@type'           => 'ItemList',
				'@id'             => home_url( '/partnery/' ) . '#list',
				'name'            => $page_h1,
				'numberOfItems'   => count( $schema_items ),
				'itemListElement' => $schema_items,
			],
			[
				'@type' => 'FAQPage',
				'@id'   => home_url( '/partnery/' ) . '#faq',
				'mainEntity' => [
					[
						'@type' => 'Question',
						'name'  => 'Это реклама или личные рекомендации?',
						'acceptedAnswer' => [
							'@type' => 'Answer',
							'text'  => 'Часть ссылок партнёрские. В список попадают сервисы, которыми Алексей пользуется сам или проверял на проектах.',
						],
					],
					[
						'@type' => 'Question',
						'name'  => 'Поможете с настройкой хостинга, VPS или Директа?',
						'acceptedAnswer' => [
							'@type' => 'Answer',
							'text'  => 'Да. Сервисы - у партнёров, настройку WordPress, Linux/VPS, Nginx, SSL, Директ и ботов можно заказать у ИП Кривошеин. Прайс: https://krivoshein.site/prays-list/',
						],
					],
					[
						'@type' => 'Question',
						'name'  => 'Какой хостинг или VPS выбрать для WordPress?',
						'acceptedAnswer' => [
							'@type' => 'Answer',
							'text'  => 'Для типичного WordPress обычно хватает Beget, SpaceWeb или Reg.ru. Для своего сервера под Docker/Nginx - CLO, UltraVDS или FirstVDS.',
						],
					],
				],
			],
		],
	];

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	return ob_get_clean();
} );

function krv_partners_grid_invalidate_cache(): void {
	delete_transient( 'krv_partners_grid_v1' );
	delete_transient( 'krv_partners_grid_v2' );

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
