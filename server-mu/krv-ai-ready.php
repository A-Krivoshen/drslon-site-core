<?php
/**
 * Plugin Name: KRV AI-ready
 * Description: English discovery helpers, Markdown negotiation, pulse feeds, robots notes, and one-h1 cleanup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$krv_ai_uri = (string) parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH );
$krv_ai_accept = (string) ( $_SERVER['HTTP_ACCEPT'] ?? '' );
$krv_ai_pulse = str_contains( $krv_ai_uri, '/feed/drslon-pulse-feed' ) || str_contains( $krv_ai_uri, '/feed/ai-pulse.md' );
$krv_ai_md    = stripos( $krv_ai_accept, 'text/markdown' ) !== false;
if ( ( $krv_ai_pulse || $krv_ai_md ) && ! defined( 'DONOTCACHEPAGE' ) ) {
	define( 'DONOTCACHEPAGE', true );
}

/**
 * Quality value for one Accept type. Missing type returns 0.
 */
function krv_ai_accept_q( string $accept, string $type ): float {
	$parts = preg_split( '/\s*,\s*/', $accept );
	$found = 0.0;
	if ( ! is_array( $parts ) ) {
		return 0.0;
	}
	foreach ( $parts as $part ) {
		$bits = array_map( 'trim', explode( ';', $part ) );
		$name = strtolower( (string) ( $bits[0] ?? '' ) );
		if ( $name !== strtolower( $type ) ) {
			continue;
		}
		$q = 1.0;
		foreach ( $bits as $bit ) {
			if ( preg_match( '/^q=([0-9.]+)$/', $bit, $m ) ) {
				$q = (float) $m[1];
			}
		}
		$found = $q;
	}
	return $found;
}

function krv_ai_wants_markdown(): bool {
	$accept = (string) ( $_SERVER['HTTP_ACCEPT'] ?? '' );
	if ( stripos( $accept, 'text/markdown' ) === false ) {
		return false;
	}
	if ( stripos( $accept, 'text/html' ) === false ) {
		return true;
	}
	return krv_ai_accept_q( $accept, 'text/markdown' ) > krv_ai_accept_q( $accept, 'text/html' );
}

function krv_ai_yaml( string $value ): string {
	$value = str_replace( array( "\r", "\n" ), ' ', $value );
	$value = str_replace( '"', '\\"', $value );
	return '"' . $value . '"';
}

function krv_ai_html_to_text( string $html ): string {
	$html = preg_replace_callback(
		'/<h([1-6])[^>]*>(.*?)<\/h\1>/is',
		static function ( array $m ): string {
			$level = max( 1, min( 6, (int) $m[1] ) );
			$text  = trim( wp_strip_all_tags( $m[2] ) );
			return "\n\n" . str_repeat( '#', $level ) . ' ' . $text . "\n\n";
		},
		$html
	);
	$html = preg_replace( '/<li[^>]*>/i', "\n- ", (string) $html );
	$html = preg_replace( '/<(br|\/p|\/div|\/tr)\b[^>]*>/i', "\n", (string) $html );
	$text = wp_strip_all_tags( (string) $html );
	$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$text = preg_replace( "/[ \t]+\n/u", "\n", (string) $text );
	$text = preg_replace( "/\n{3,}/u", "\n\n", (string) $text );
	$text = trim( (string) $text );
	if ( mb_strlen( $text, 'UTF-8' ) > 12000 ) {
		$text = mb_substr( $text, 0, 12000, 'UTF-8' ) . "\n\n[truncated]\n";
	}
	return $text;
}

function krv_ai_send_markdown( string $body, int $status = 200 ): void {
	$body   = wp_check_invalid_utf8( $body );
	$tokens = (int) max( 1, ceil( mb_strlen( $body, 'UTF-8' ) / 4 ) );
	status_header( $status );
	header( 'Content-Type: text/markdown; charset=utf-8' );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Vary: Accept' );
	header( 'X-Markdown-Tokens: ' . $tokens );
	header( 'Cache-Control: private, no-store' );
	echo $body;
	exit;
}

function krv_ai_recent_posts( int $limit = 20 ): array {
	$q = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	$posts = $q->posts;
	wp_reset_postdata();
	return is_array( $posts ) ? $posts : array();
}

function krv_ai_pulse_markdown(): string {
	$lines   = array();
	$lines[] = '# AI Pulse · krivoshein.site';
	$lines[] = 'Generated: ' . gmdate( 'c' );
	$lines[] = 'Home: https://krivoshein.site/';
	$lines[] = 'English brief: https://krivoshein.site/en/llms.txt';
	$lines[] = '';
	$lines[] = 'Titles stay in the original language (Russian).';
	$lines[] = '';
	foreach ( krv_ai_recent_posts( 20 ) as $post ) {
		if ( ! $post instanceof WP_Post ) {
			continue;
		}
		$title   = html_entity_decode( get_the_title( $post ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$url     = get_permalink( $post );
		$date    = get_the_date( 'Y-m-d', $post );
		$excerpt = krv_ai_html_to_text( get_the_excerpt( $post ) );
		$excerpt = mb_substr( $excerpt, 0, 280, 'UTF-8' );
		$lines[] = '## ' . $title;
		$lines[] = '- Date: ' . $date;
		$lines[] = '- URL: ' . $url;
		if ( $excerpt !== '' ) {
			$lines[] = '';
			$lines[] = $excerpt;
		}
		$lines[] = '';
	}
	return implode( "\n", $lines ) . "\n";
}

function krv_ai_pulse_rss(): void {
	status_header( 200 );
	header( 'Content-Type: application/rss+xml; charset=utf-8' );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Cache-Control: public, max-age=600' );
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<rss version="2.0"><channel>';
	echo '<title>Dr.Slon Pulse</title>';
	echo '<link>https://krivoshein.site/</link>';
	echo '<description>Recent posts from krivoshein.site</description>';
	echo '<language>ru-RU</language>';
	echo '<lastBuildDate>' . esc_html( gmdate( 'r' ) ) . '</lastBuildDate>';
	foreach ( krv_ai_recent_posts( 20 ) as $post ) {
		if ( ! $post instanceof WP_Post ) {
			continue;
		}
		$title   = html_entity_decode( get_the_title( $post ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$excerpt = krv_ai_html_to_text( get_the_excerpt( $post ) );
		echo '<item>';
		$xml = static function ( string $value ): string {
			return htmlspecialchars( $value, ENT_XML1 | ENT_QUOTES, 'UTF-8' );
		};
		echo '<title>' . $xml( $title ) . '</title>';
		echo '<link>' . $xml( (string) get_permalink( $post ) ) . '</link>';
		echo '<pubDate>' . $xml( (string) get_the_date( 'r', $post ) ) . '</pubDate>';
		echo '<description>' . $xml( mb_substr( $excerpt, 0, 400, 'UTF-8' ) ) . '</description>';
		echo '</item>';
	}
	echo '</channel></rss>';
	exit;
}

add_filter(
	'redirect_canonical',
	static function ( $redirect_url ) {
		$uri = (string) ( $_SERVER['REQUEST_URI'] ?? '' );
		if ( isset( $_GET['krv_md'] ) || str_contains( $uri, 'krv_md=1' ) ) {
			return false;
		}
		return $redirect_url;
	}
);

add_action(
	'template_redirect',
	static function (): void {
		if ( is_admin() ) {
			return;
		}
		$method = strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) );
		if ( $method !== 'GET' && $method !== 'HEAD' ) {
			return;
		}
		$path = (string) parse_url( $_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH );
		$path = $path === '' ? '/' : $path;
		$trim = rtrim( $path, '/' );
		if ( $trim === '' ) {
			$trim = '/';
		}

		if ( $trim === '/feed/drslon-pulse-feed' ) {
			krv_ai_pulse_rss();
		}
		if ( $trim === '/feed/ai-pulse.md' ) {
			krv_ai_send_markdown( krv_ai_pulse_markdown() );
		}

		if ( ! krv_ai_wants_markdown() ) {
			return;
		}
		if ( str_starts_with( $trim, '/wp-json' ) || str_starts_with( $trim, '/wp-admin' ) || $trim === '/wp-cron.php' ) {
			return;
		}

		if ( is_404() ) {
			krv_ai_send_markdown( "# Not found\n\nhttps://krivoshein.site/llms.txt\n", 404 );
		}

		if ( is_front_page() ) {
			$file = trailingslashit( ABSPATH ) . 'llms.txt';
			$body = is_readable( $file ) ? (string) file_get_contents( $file ) : '';
			$md   = "---\n";
			$md  .= 'title: ' . krv_ai_yaml( 'Алексей Кривошеин: WordPress, Flask, VPS и AI-ready' ) . "\n";
			$md  .= "url: https://krivoshein.site/\n";
			$md  .= "lang: ru\n";
			$md  .= "description: \"Сайты на WordPress и Flask, VPS и DevOps, Яндекс.Директ, боты MAX и подготовка AI-ready.\"\n";
			$md  .= "---\n\n" . $body;
			krv_ai_send_markdown( $md );
		}

		if ( is_singular() ) {
			$post = get_queried_object();
			if ( ! $post instanceof WP_Post ) {
				return;
			}
			$title = html_entity_decode( get_the_title( $post ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
			$url   = get_permalink( $post );
			if ( post_password_required( $post ) ) {
				krv_ai_send_markdown( "---\ntitle: " . krv_ai_yaml( $title ) . "\nurl: {$url}\n---\n\nPassword protected.\n" );
			}
			$text = krv_ai_html_to_text( apply_filters( 'the_content', $post->post_content ) );
			$md   = "---\n";
			$md  .= 'title: ' . krv_ai_yaml( $title ) . "\n";
			$md  .= 'url: ' . $url . "\n";
			$md  .= "lang: ru\n";
			$md  .= "---\n\n# " . $title . "\n\n" . $text . "\n";
			krv_ai_send_markdown( $md );
		}

		$title = wp_get_document_title();
		krv_ai_send_markdown( "---\ntitle: " . krv_ai_yaml( $title ) . "\nurl: " . esc_url_raw( home_url( $path ) ) . "\nlang: ru\n---\n\n# " . $title . "\n\nSee https://krivoshein.site/llms.txt\n" );
	},
	0
);

add_filter(
	'robots_txt',
	static function ( $output, $public ) {
		unset( $public );
		$output = (string) $output;
		if ( str_contains( $output, '# AI discovery' ) ) {
			return $output;
		}
		$block = <<<'TXT'


# AI discovery (ru + en)
Content-Signal: search=yes, ai-input=yes, ai-train=yes
Allow: /llms.txt
Allow: /llms-full.txt
Allow: /ai.txt
Allow: /agents.md
Allow: /agents.txt
Allow: /agents.json
Allow: /auth.md
Allow: /humans.txt
Allow: /openapi.json
Allow: /en/
Allow: /en/llms.txt
Allow: /en/llms-full.txt
Allow: /en/ai.txt
Allow: /en/agents.md
Allow: /feed/ai-pulse.md
Allow: /feed/drslon-pulse-feed/
Allow: /.well-known/api-catalog
Allow: /.well-known/agent.json
Allow: /.well-known/agent-index.json

# 1. https://krivoshein.site/ai.txt
# 2. https://krivoshein.site/llms.txt
# 3. https://krivoshein.site/llms-full.txt
# 4. https://krivoshein.site/agents.md
# 5. https://krivoshein.site/en/ai.txt
# 6. https://krivoshein.site/en/llms.txt
# 7. https://krivoshein.site/en/agents.md
# 8. https://krivoshein.site/feed/ai-pulse.md
# 9. https://krivoshein.site/wp-sitemap.xml
TXT;
		return rtrim( $output ) . "\n" . $block . "\n";
	},
	20,
	2
);

add_action(
	'wp_head',
	static function (): void {
		if ( is_admin() ) {
			return;
		}
		$links = array(
			array( 'rel' => 'describedby', 'href' => home_url( '/en/llms.txt' ), 'type' => 'text/plain', 'title' => 'llms.txt (en)' ),
			array( 'rel' => 'describedby', 'href' => home_url( '/en/agents.md' ), 'type' => 'text/markdown', 'title' => 'agents.md (en)' ),
			array( 'rel' => 'alternate', 'href' => home_url( '/feed/ai-pulse.md' ), 'type' => 'text/markdown', 'title' => 'AI pulse' ),
		);
		if ( is_front_page() ) {
			echo '<link rel="alternate" hreflang="ru" href="' . esc_url( home_url( '/' ) ) . "\" />\n";
			echo '<link rel="alternate" hreflang="en" href="' . esc_url( home_url( '/en/' ) ) . "\" />\n";
			echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( home_url( '/' ) ) . "\" />\n";
		}
		foreach ( $links as $link ) {
			$attrs = array();
			foreach ( $link as $key => $value ) {
				$attrs[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( $value ) );
			}
			echo '<link ' . implode( ' ', $attrs ) . " />\n";
		}
	},
	2
);

add_filter(
	'wp_headers',
	static function ( $headers ) {
		if ( is_admin() ) {
			return $headers;
		}
		$items = array(
			'<https://krivoshein.site/sitemap.xml>; rel="sitemap"',
			'<https://krivoshein.site/llms.txt>; rel="describedby"; type="text/plain"',
			'<https://krivoshein.site/ai.txt>; rel="describedby"; type="text/plain"',
			'<https://krivoshein.site/en/llms.txt>; rel="alternate"; type="text/plain"; title="llms.txt (en)"',
			'<https://krivoshein.site/.well-known/api-catalog>; rel="api-catalog"; type="application/linkset+json"',
			'<https://krivoshein.site/feed/>; rel="alternate"; type="application/rss+xml"',
		);
		$existing         = isset( $headers['Link'] ) ? (string) $headers['Link'] : '';
		$headers['Link']  = trim( $existing . ( $existing !== '' ? ', ' : '' ) . implode( ', ', $items ) );
		if ( empty( $headers['Content-Language'] ) ) {
			$headers['Content-Language'] = 'ru';
		}
		return $headers;
	}
);

add_filter(
	'the_content',
	static function ( $content ) {
		if ( ! is_page( array( 'oferta', 'resume' ) ) ) {
			return $content;
		}
		$replaced = preg_replace( '/<h1\b([^>]*)>/i', '<h2$1>', (string) $content, 1 );
		if ( ! is_string( $replaced ) ) {
			return $content;
		}
		return preg_replace( '/<\/h1>/i', '</h2>', $replaced, 1 );
	},
	12
);

add_filter(
	'wp_sitemaps_add_provider',
	static function ( $provider, $name ) {
		if ( $name === 'users' ) {
			return false;
		}
		return $provider;
	},
	10,
	2
);

add_action(
	'init',
	static function (): void {
		if ( ! function_exists( 'wp_sitemaps_get_server' ) || ! class_exists( 'WP_Sitemaps_Provider' ) ) {
			return;
		}
		if ( ! class_exists( 'KRV_En_Sitemap_Provider' ) ) {
			class KRV_En_Sitemap_Provider extends WP_Sitemaps_Provider {
				public function __construct() {
					$this->name        = 'krven';
					$this->object_type = 'krven';
				}

				public function get_max_num_pages( $object_subtype = '' ) {
					unset( $object_subtype );
					return 1;
				}

				public function get_url_list( $page_num, $object_subtype = '' ) {
					unset( $object_subtype );
					if ( (int) $page_num > 1 ) {
						return array();
					}
					return array(
						array( 'loc' => home_url( '/en/' ) ),
					);
				}
			}
		}
		$server = wp_sitemaps_get_server();
		if ( ! is_object( $server ) || ! isset( $server->registry ) ) {
			return;
		}
		$server->registry->add_provider( 'krven', new KRV_En_Sitemap_Provider() );
	},
	20
);
