<?php
/**
 * Services pages showcase shortcode [krv_services_pages_showcase]
 * Page: /servisy/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Curated card meta: theme icon + living description (pages are often shortcode-only).
 *
 * @return array<int|string, array{icon:string, description:string, title?:string}>
 */
function krv_services_showcase_card_catalog(): array {
	return array(
		// by page ID
		7304 => array(
			'icon'        => 'dns',
			'description' => 'Проверка DNS-записей домена: A, AAAA, MX, NS, TXT и другие. Быстрый онлайн-lookup.',
		),
		7323 => array(
			'icon'        => 'whois',
			'description' => 'WHOIS по домену: регистратор, даты, статус и контакты (где открыты).',
		),
		9051 => array(
			'icon'        => 'wp',
			'description' => 'Экспресс-проверка WordPress-сайта: доступность, типичные сигналы и базовая гигиена.',
		),
		7369 => array(
			'icon'        => 'firewall',
			'description' => 'Конструктор правил файрвола: готовые шаблоны под типовые сценарии на сервере.',
		),
		7529 => array(
			'icon'        => 'speed',
			'description' => 'Измеритель скорости интернета прямо в браузере. Удобно для диагностики канала.',
		),
		6204 => array(
			'icon'        => 'domain',
			'description' => 'Сводка по домену: DNS, WHOIS и связанные проверки в одном месте.',
		),
		7459 => array(
			'icon'        => 'subnet',
			'description' => 'Калькулятор сетевых масок: CIDR, диапазон адресов, число хостов.',
		),
		7352 => array(
			'icon'        => 'cron',
			'description' => 'Онлайн-генератор crontab: расписание на человеческом языке и в cron-синтаксисе.',
		),
		7287 => array(
			'icon'        => 'punycode',
			'description' => 'Конвертер Punycode: кириллические и IDN-домены в ASCII и обратно.',
		),
		9072 => array(
			'icon'        => 'chat',
			'description' => 'Разовые и проектные консультации по сайтам, VPS, Директу и автоматизации.',
		),
		9060 => array(
			'icon'        => 'doc',
			'description' => 'Публичная оферта на информационно-консультационные услуги ИП.',
		),
		// by slug fallback
		'easy-dns-lookup'              => array( 'icon' => 'dns', 'description' => 'Проверка DNS-записей домена онлайн.' ),
		'whois-lookup'                 => array( 'icon' => 'whois', 'description' => 'WHOIS-информация по домену.' ),
		'wp-site-checker'              => array( 'icon' => 'wp', 'description' => 'Экспресс-проверка WordPress-сайта.' ),
		'konfigurator-fayrvola'        => array( 'icon' => 'firewall', 'description' => 'Генератор правил файрвола.' ),
		'izmeritel-skorosti-internetak'=> array( 'icon' => 'speed', 'description' => 'Измеритель скорости интернета.' ),
		'informatsiya-o-domene'        => array( 'icon' => 'domain', 'description' => 'Информация и проверки по домену.' ),
		'kalkulyator-setevyh-masok'    => array( 'icon' => 'subnet', 'description' => 'Калькулятор CIDR и сетевых масок.' ),
		'generator-crontab'            => array( 'icon' => 'cron', 'description' => 'Генератор расписаний crontab.' ),
		'konverter-punycode-dlya-domena'=> array( 'icon' => 'punycode', 'description' => 'Конвертер Punycode для IDN-доменов.' ),
		'konsultatsii'                 => array( 'icon' => 'chat', 'description' => 'Консультации по IT и проектам.' ),
		'oferta'                       => array( 'icon' => 'doc', 'description' => 'Публичная оферта на услуги.' ),
		'rag-demo'                     => array(
			'icon'        => 'rag',
			'description' => 'Живое демо: GigaChat отвечает по блогу и прайсу со ссылками.',
		),
	);
}

/**
 * @param int    $page_id Page ID.
 * @param string $slug    Post slug.
 * @param string $title   Title.
 * @return array{icon:string, description:string}
 */
function krv_services_showcase_resolve_meta( int $page_id, string $slug, string $title ): array {
	$cat = krv_services_showcase_card_catalog();

	if ( isset( $cat[ $page_id ] ) ) {
		return $cat[ $page_id ];
	}
	if ( $slug !== '' && isset( $cat[ $slug ] ) ) {
		return $cat[ $slug ];
	}

	// Last resort: short clean line, never pull garbage excerpts from other pages.
	return array(
		'icon'        => 'tool',
		'description' => 'Онлайн-сервис на сайте krivoshein.site.',
	);
}

/**
 * Theme SVG icon for showcase card.
 */
function krv_services_showcase_icon_svg( string $key ): string {
	$icons = array(
		'dns'      => '<circle cx="12" cy="12" r="3"></circle><path d="M12 2v3"></path><path d="M12 19v3"></path><path d="M4.93 4.93l2.12 2.12"></path><path d="M16.95 16.95l2.12 2.12"></path><path d="M2 12h3"></path><path d="M19 12h3"></path><path d="M4.93 19.07l2.12-2.12"></path><path d="M16.95 7.05l2.12-2.12"></path>',
		'whois'    => '<circle cx="11" cy="11" r="6.5"></circle><path d="M16.5 16.5L21 21"></path><path d="M8.5 11h5"></path><path d="M11 8.5v5"></path>',
		'wp'       => '<circle cx="12" cy="12" r="9"></circle><path d="M8 15.5l1.8-9h1.6l1.1 5.2L14 6.5h1.6l1.8 9h-1.5l-1.1-5.5L13.5 15.5h-1.3l-1.1-5.5L10 15.5H8.5z"></path>',
		'firewall' => '<path d="M12 3l8 4v5c0 5-3.4 9-8 11-4.6-2-8-6-8-11V7l8-4z"></path><path d="M9 12h6"></path><path d="M12 9v6"></path>',
		'speed'    => '<circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3.2 2"></path>',
		'domain'   => '<circle cx="12" cy="12" r="9"></circle><path d="M3 12h18"></path><path d="M12 3a15 15 0 0 1 0 18"></path><path d="M12 3a15 15 0 0 0 0 18"></path>',
		'subnet'   => '<rect x="3" y="4" width="7" height="7" rx="1.2"></rect><rect x="14" y="4" width="7" height="7" rx="1.2"></rect><rect x="8.5" y="13" width="7" height="7" rx="1.2"></rect><path d="M10 8h4"></path><path d="M12 11v2"></path>',
		'cron'     => '<circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path><path d="M7 3.5l1.5 1.5"></path>',
		'punycode' => '<path d="M4 7h16"></path><path d="M4 12h10"></path><path d="M4 17h16"></path><path d="M16 10l4 2-4 2"></path>',
		'chat'     => '<path d="M21 15a2 2 0 0 1-2 2H8l-4 4V5a2 2 0 0 1 2-2h13a2 2 0 0 1 2 2z"></path>',
		'doc'      => '<path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"></path><path d="M14 3v5h5"></path><path d="M9 13h6"></path><path d="M9 17h6"></path>',
		'rag'      => '<ellipse cx="12" cy="6" rx="7" ry="2.6"></ellipse><path d="M5 6v4c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6V6"></path><path d="M5 10v4c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6v-4"></path><path d="M5 14v4c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6v-4"></path>',
		'tool'     => '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L4 17l3 3 5.3-5.3a4 4 0 0 0 5.4-5.4l-2.5 2.5-3-3 2.5-2.5z"></path>',
	);

	$inner = $icons[ $key ] ?? $icons['tool'];

	return '<span class="krv-service-page-icon-wrap" aria-hidden="true">'
		. '<svg class="krv-service-page-icon" viewBox="0 0 24 24" focusable="false">'
		. $inner
		. '</svg></span>';
}

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
 * @return array<int, array{section_title: string, section_pages: int[]}>
 */
function krv_services_showcase_get_sections(): array {
	$cache_key = 'krv_services_showcase_v2';
	$cached    = get_transient( $cache_key );

	if ( is_array( $cached ) ) {
		return $cached;
	}

	if ( ! function_exists( 'get_field' ) ) {
		return [];
	}

	$sections = get_field( 'krv_services_sections', 'krv-services-showcase' );

	if ( empty( $sections ) || ! is_array( $sections ) ) {
		$sections = get_field( 'krv_services_sections', 'option' );
	}

	if ( empty( $sections ) || ! is_array( $sections ) ) {
		return [];
	}

	$normalized = krv_services_showcase_normalize_sections( $sections );
	set_transient( $cache_key, $normalized, HOUR_IN_SECONDS );

	return $normalized;
}

function krv_services_showcase_visible_section_title( string $title, int $section_count ): string {
	if ( $title === '' ) {
		return '';
	}
	if ( $section_count < 2 ) {
		return '';
	}
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
	$intro_lead    = '';
	if ( function_exists( 'get_field' ) ) {
		$intro_heading = trim( (string) get_field( 'showcase_intro_heading', 'krv-services-showcase' ) );
		$intro_lead    = trim( (string) get_field( 'showcase_intro_lead', 'krv-services-showcase' ) );
	}
	if ( $intro_heading === '' ) {
		$intro_heading = 'Онлайн-сервисы и утилиты';
	}
	if ( $intro_lead === '' ) {
		$intro_lead = 'Бесплатные инструменты для доменов, DNS, сети и WordPress. Плюс консультации и демо AI-базы.';
	}

	ob_start();
	?>
	<div class="krv-service-pages-wrap">
		<header class="krv-showcase-header">
			<h1 class="krv-showcase-intro"><?php echo esc_html( $intro_heading ); ?></h1>
			<?php if ( $intro_lead !== '' ) : ?>
				<p class="krv-showcase-lead"><?php echo esc_html( $intro_lead ); ?></p>
			<?php endif; ?>
		</header>

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
						$slug  = (string) $page->post_name;
						$meta  = krv_services_showcase_resolve_meta( (int) $page_id, $slug, $title );
						$desc  = $meta['description'];
						$icon  = $meta['icon'];
						?>
						<a class="krv-service-page-card" href="<?php echo esc_url( $url ); ?>">
							<div class="krv-service-page-card-inner">
								<span class="krv-service-page-go" aria-hidden="true">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" focusable="false">
										<path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
										<path d="M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</span>
								<?php echo krv_services_showcase_icon_svg( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<h3 class="krv-service-page-title"><?php echo esc_html( $title ); ?></h3>
								<?php if ( $desc !== '' ) : ?>
									<p class="krv-service-page-description"><?php echo esc_html( $desc ); ?></p>
								<?php endif; ?>
							</div>
						</a>
					<?php endforeach; ?>

					<?php
					$extra_cards = apply_filters( 'krv_services_showcase_extra_cards', [], 0 );
					if ( $section === $sections[0] && is_array( $extra_cards ) ) :
						foreach ( $extra_cards as $card ) :
							if ( ! is_array( $card ) ) {
								continue;
							}
							$c_title = trim( (string) ( $card['title'] ?? '' ) );
							$c_url   = trim( (string) ( $card['url'] ?? '' ) );
							$c_desc  = trim( (string) ( $card['description'] ?? '' ) );
							$c_icon  = trim( (string) ( $card['icon'] ?? $card['icon_key'] ?? 'rag' ) );
							if ( $c_title === '' || $c_url === '' ) {
								continue;
							}
							if ( $c_desc === '' ) {
								$c_desc = 'Живое демо AI-базы по материалам сайта.';
							}
							?>
							<a class="krv-service-page-card krv-service-page-card--external" href="<?php echo esc_url( $c_url ); ?>">
								<div class="krv-service-page-card-inner">
									<span class="krv-service-page-go" aria-hidden="true">
										<svg width="14" height="14" viewBox="0 0 24 24" fill="none" focusable="false">
											<path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
											<path d="M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</span>
									<?php echo krv_services_showcase_icon_svg( $c_icon !== '' ? $c_icon : 'rag' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<h3 class="krv-service-page-title"><?php echo esc_html( $c_title ); ?></h3>
									<p class="krv-service-page-description"><?php echo esc_html( $c_desc ); ?></p>
								</div>
							</a>
							<?php
						endforeach;
					endif;
					?>
				</div>
			</section>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
} );

function krv_services_showcase_purge_for_page( int $post_id ): void {
	delete_transient( 'krv_services_showcase_v1' );
	delete_transient( 'krv_services_showcase_v2' );

	foreach ( krv_services_showcase_get_sections() as $section ) {
		if ( in_array( $post_id, $section['section_pages'], true ) ) {
			if ( class_exists( 'DrSlon_Cache_Purge_Bridge' ) ) {
				DrSlon_Cache_Purge_Bridge::purge_page_cache( DRSLON_SERVICES_PAGE_ID );
			}
			break;
		}
	}
}

add_action( 'save_post_page', function ( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	krv_services_showcase_purge_for_page( (int) $post_id );
}, 20 );

add_action( 'deleted_post', function ( $post_id, $post ) {
	if ( $post instanceof WP_Post && 'page' === $post->post_type ) {
		krv_services_showcase_purge_for_page( (int) $post_id );
	}
}, 20, 2 );
