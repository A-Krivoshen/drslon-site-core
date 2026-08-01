<?php
/**
 * AI agent discovery: HTML link tags, Person JSON-LD, llms-full refresh.
 *
 * @package DrSlon_Site_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Emit discovery <link> tags for AI agents and crawlers.
 */
function drslon_ai_agent_head_links() {
	$base = home_url( '/' );
	$base = untrailingslashit( $base );

	$links = array(
		array(
			'rel'  => 'describedby',
			'href' => $base . '/llms.txt',
			'type' => 'text/markdown',
			'title'=> 'llms.txt',
		),
		array(
			'rel'  => 'describedby',
			'href' => $base . '/llms-full.txt',
			'type' => 'text/markdown',
			'title'=> 'llms-full.txt',
		),
		array(
			'rel'  => 'describedby',
			'href' => $base . '/agents.md',
			'type' => 'text/markdown',
			'title'=> 'agents.md',
		),
		array(
			'rel'  => 'alternate',
			'href' => $base . '/ai.txt',
			'type' => 'text/plain',
			'title'=> 'ai.txt',
		),
		array(
			'rel'  => 'alternate',
			'href' => $base . '/openapi.json',
			'type' => 'application/json',
			'title'=> 'OpenAPI',
		),
		array(
			'rel'  => 'api-catalog',
			'href' => $base . '/.well-known/api-catalog',
			'type' => 'application/linkset+json',
		),
		array(
			'rel'  => 'alternate',
			'href' => $base . '/.well-known/agent.json',
			'type' => 'application/json',
			'title'=> 'agent.json',
		),
		array(
			'rel'  => 'alternate',
			'href' => $base . '/feed/drslon-pulse-feed/',
			'type' => 'application/rss+xml',
			'title'=> 'Dr.Slon Pulse Feed',
		),
	);

	foreach ( $links as $link ) {
		$attrs = array();
		foreach ( $link as $key => $value ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}
		echo '<link ' . implode( ' ', $attrs ) . " />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'drslon_ai_agent_head_links', 3 );

/**
 * Person + ProfessionalService JSON-LD for agent/entity clarity.
 */
function drslon_ai_agent_json_ld() {
	if ( is_admin() ) {
		return;
	}

	$data = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			array(
				'@type'       => 'Person',
				'@id'         => home_url( '/#/schema/Person' ),
				'name'        => 'Алексей Кривошеин',
				'alternateName' => array( 'Dr.Slon', 'Aleksey Krivoshein' ),
				'url'         => home_url( '/' ),
				'email'       => 'aleksey@krivoshein.site',
				'telephone'   => '+7-963-664-16-15',
				'jobTitle'    => 'WordPress-разработчик, Linux/DevOps, техническое SEO',
				'sameAs'      => array(
					'https://t.me/DrSlon',
					'https://github.com/A-Krivoshen',
					'https://krivoshein.site/max/',
				),
				'worksFor'    => array( '@id' => home_url( '/#/schema/Organization' ) ),
			),
			array(
				'@type'       => 'ProfessionalService',
				'@id'         => home_url( '/#/schema/ProfessionalService' ),
				'name'        => 'ИТ Решения — ИП Кривошеин Алексей Сергеевич',
				'url'         => home_url( '/' ),
				'description' => 'Разработка и поддержка сайтов на WordPress, Linux/VPS, техническое SEO, Яндекс.Директ, боты MAX, AI-ready подготовка сайтов.',
				'priceRange'  => '₽₽',
				'areaServed'  => 'RU',
				'availableLanguage' => array( 'Russian' ),
				'provider'    => array( '@id' => home_url( '/#/schema/Person' ) ),
				'hasOfferCatalog' => array(
					'@type'           => 'OfferCatalog',
					'name'            => 'IT-услуги',
					'itemListElement' => array(
						array(
							'@type' => 'Offer',
							'itemOffered' => array(
								'@type' => 'Service',
								'name'  => 'Диагностика сайта',
							),
							'priceSpecification' => array(
								'@type'         => 'PriceSpecification',
								'priceCurrency' => 'RUB',
								'minPrice'      => 5000,
							),
						),
						array(
							'@type' => 'Offer',
							'itemOffered' => array(
								'@type' => 'Service',
								'name'  => 'Техническая поддержка WordPress',
							),
							'priceSpecification' => array(
								'@type'         => 'UnitPriceSpecification',
								'priceCurrency' => 'RUB',
								'minPrice'      => 20000,
								'unitText'      => 'MONTH',
							),
						),
						array(
							'@type' => 'Offer',
							'itemOffered' => array(
								'@type' => 'Service',
								'name'  => 'AI-ready подготовка сайта',
							),
							'priceSpecification' => array(
								'@type'         => 'PriceSpecification',
								'priceCurrency' => 'RUB',
								'minPrice'      => 10000,
							),
						),
					),
				),
			),
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "</script>\n";
}
add_action( 'wp_head', 'drslon_ai_agent_json_ld', 4 );

/**
 * Absolute path to document root llms-full.txt.
 *
 * @return string
 */
function drslon_ai_llms_full_path() {
	return trailingslashit( ABSPATH ) . 'llms-full.txt';
}

/**
 * Build markdown body for recent posts section.
 *
 * @param int $limit Number of posts.
 * @return string
 */
function drslon_ai_build_recent_posts_markdown( $limit = 25 ) {
	$q = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => (int) $limit,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	$lines = array();
	if ( $q->have_posts() ) {
		while ( $q->have_posts() ) {
			$q->the_post();
			$title   = get_the_title();
			$url     = get_permalink();
			$date    = get_the_date( 'Y-m-d' );
			$excerpt = get_the_excerpt();
			$excerpt = wp_strip_all_tags( $excerpt );
			$excerpt = preg_replace( '/\s+/u', ' ', $excerpt );
			$excerpt = mb_substr( $excerpt, 0, 220 );
			$lines[] = sprintf( '- [%s](%s) — %s', $title, $url, $date );
			if ( $excerpt !== '' ) {
				$lines[] = '  ' . $excerpt;
			}
		}
		wp_reset_postdata();
	}

	if ( empty( $lines ) ) {
		return "_Пока нет опубликованных записей._\n";
	}

	return implode( "\n", $lines ) . "\n";
}

/**
 * Replace or append the «Свежие публикации» section inside llms-full.txt.
 *
 * Keeps the static skeleton; refreshes only the posts index so prices stay hand-curated.
 *
 * @return bool True if file written.
 */
function drslon_ai_refresh_llms_full_posts() {
	$path = drslon_ai_llms_full_path();
	if ( ! file_exists( $path ) || ! is_readable( $path ) ) {
		return false;
	}

	$current = file_get_contents( $path );
	if ( $current === false ) {
		return false;
	}

	$posts_md = drslon_ai_build_recent_posts_markdown( 25 );
	$stamp    = gmdate( 'Y-m-d' );

	// Update generation stamp in header if present.
	$current = preg_replace(
		'/> Сгенерировано\/обновлено: \d{4}-\d{2}-\d{2}\./u',
		'> Сгенерировано/обновлено: ' . $stamp . '.',
		$current,
		1
	);

	$section_header = "## 7. Свежие публикации (индекс)";
	$intro          = "Полные тексты — по URL статьи или через REST API. Ниже — заголовки и краткие выдержки для навигации.\n\n";
	$footer_marker  = "\nБольше статей:";

	$pos = strpos( $current, $section_header );
	if ( $pos === false ) {
		// Append section if missing.
		$current = rtrim( $current ) . "\n\n" . $section_header . "\n\n" . $intro . $posts_md . "\nБольше статей: https://krivoshein.site/blog/ · https://krivoshein.site/wp-json/wp/v2/posts\n";
	} else {
		$after   = substr( $current, $pos );
		$end_rel = strpos( $after, $footer_marker );
		if ( $end_rel === false ) {
			// Cut until next ## or end.
			$next = strpos( $after, "\n## ", strlen( $section_header ) );
			if ( $next === false ) {
				$before = substr( $current, 0, $pos );
				$current = $before . $section_header . "\n\n" . $intro . $posts_md . "\nБольше статей: https://krivoshein.site/blog/ · https://krivoshein.site/wp-json/wp/v2/posts\n";
			} else {
				$before  = substr( $current, 0, $pos );
				$rest    = substr( $after, $next );
				$current = $before . $section_header . "\n\n" . $intro . $posts_md . $rest;
			}
		} else {
			$before  = substr( $current, 0, $pos );
			$rest    = substr( $after, $end_rel );
			$current = $before . $section_header . "\n\n" . $intro . $posts_md . $rest;
		}
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	$result = @file_put_contents( $path, $current );

	return $result !== false;
}

/**
 * Schedule a soft refresh of llms-full posts index after content changes.
 *
 * @param int $post_id Post ID.
 */
function drslon_ai_maybe_schedule_llms_full_refresh( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	$post = get_post( $post_id );
	if ( ! $post || $post->post_type !== 'post' ) {
		return;
	}
	if ( ! in_array( $post->post_status, array( 'publish', 'trash', 'private' ), true ) ) {
		return;
	}

	// Debounce: single event within a short window.
	if ( ! wp_next_scheduled( 'drslon_ai_refresh_llms_full_event' ) ) {
		wp_schedule_single_event( time() + 30, 'drslon_ai_refresh_llms_full_event' );
	}
}
add_action( 'save_post_post', 'drslon_ai_maybe_schedule_llms_full_refresh', 20 );
add_action( 'drslon_ai_refresh_llms_full_event', 'drslon_ai_refresh_llms_full_posts' );

/**
 * WP-CLI / manual: refresh immediately.
 */
function drslon_ai_cli_refresh_llms_full() {
	$ok = drslon_ai_refresh_llms_full_posts();
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		if ( $ok ) {
			WP_CLI::success( 'llms-full.txt posts section refreshed.' );
		} else {
			WP_CLI::error( 'Failed to refresh llms-full.txt' );
		}
	}
	return $ok;
}
