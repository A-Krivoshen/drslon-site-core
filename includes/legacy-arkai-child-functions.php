<?php
/**
 * Krivoshein.site — functions.php
 * TSF SEO + Telegram comments + RSYA + CPT/ACF + shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** =========================
 *  SETTINGS
 *  ========================= */
define( 'KRV_TG_DISCUSSION', 'drslon_channel' );

/*
 * RSYA constants and on/off options live in includes/ads-settings.php
 * (Настройки → Реклама РСЯ). Use the krv_rsya_*() getters below.
 */

/** =========================
 *  HELPERS
 *  ========================= */
function krv_is_single_content(): bool {
	return ! is_admin() && ( is_singular( 'post' ) || is_singular( 'project' ) );
}

function krv_page_has_ui_shortcode( array $shortcodes ): bool {
	if ( is_admin() || ( ! is_singular() && ! is_page() ) ) {
		return false;
	}

	if ( function_exists( 'krv_page_resolves_shortcode' ) ) {
		foreach ( $shortcodes as $shortcode ) {
			if ( krv_page_resolves_shortcode( (string) $shortcode ) ) {
				return true;
			}
		}
	}

	global $post;

	if ( ! $post instanceof WP_Post ) {
		$queried_id = get_queried_object_id();
		if ( $queried_id ) {
			$post = get_post( $queried_id );
		}
	}

	if ( ! $post instanceof WP_Post || $post->post_content === '' ) {
		return false;
	}

	$content = $post->post_content;

	// Parent shortcodes that render nested UI shortcodes via do_shortcode().
	$nested_shortcode_hosts = [
		'krv_clients_grid' => [ 'krv_services_landing' ],
	];

	foreach ( $shortcodes as $shortcode ) {
		if ( has_shortcode( $content, $shortcode ) ) {
			return true;
		}

		if ( isset( $nested_shortcode_hosts[ $shortcode ] ) ) {
			foreach ( $nested_shortcode_hosts[ $shortcode ] as $host_shortcode ) {
				if ( has_shortcode( $content, $host_shortcode ) ) {
					return true;
				}
			}
		}
	}

	return false;
}

/** =========================
 *  Layout safety for post extras
 *  ========================= */
add_action( 'wp_head', function () {
	if ( ! krv_is_single_content() ) {
		return;
	}
	?>
	<style>
		.krv-post-extras,
		#telegram-comments,
		.krv-rsya-reco {
			clear: both;
			display: block;
			width: 100%;
		}
	</style>
	<?php
}, 15 );

/**
 *  0) Dropcap
 *
 * Disabled during migration to drslon-blog-theme.
 * The legacy script modified the first paragraph inside .entry-content.
 * In the block theme it also affects shortcode landing sections, so pages get
 * a wrong decorative first letter.
 */

/** =========================
 *  1) Yandex loader
 *  ========================= */
add_action( 'wp_head', function () {
	if (
		is_admin() ||
		! krv_is_single_content() ||
		( ! krv_rsya_reco_enabled() && ! krv_rsya_inimage_enabled() )
	) {
		return;
	}

	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;

	echo "<script>window.yaContextCb=window.yaContextCb||[];</script>\n";
	echo "<script async src=\"https://yandex.ru/ads/system/context.js\"></script>\n";
}, 30 );

/** =========================
 *  2) RSYA metatags
 *  ========================= */
add_action( 'wp_head', function () {
	if ( ! krv_rsya_reco_enabled() || ! krv_is_single_content() ) {
		return;
	}

	$id = get_queried_object_id();
	if ( ! $id ) {
		return;
	}

	$title = wp_strip_all_tags( get_the_title( $id ) );

	$img = get_the_post_thumbnail_url( $id, 'full' );
	if ( ! $img ) {
		$img = get_site_icon_url( 512 );
	}

	$cats     = get_the_category( $id );
	$cat_name = ( ! empty( $cats ) && ! is_wp_error( $cats ) ) ? (string) $cats[0]->name : '';

	echo '<meta property="yandex_recommendations_title" content="' . esc_attr( $title ) . '">' . "\n";

	if ( $cat_name !== '' ) {
		echo '<meta property="yandex_recommendations_category" content="' . esc_attr( $cat_name ) . '">' . "\n";
	}

	if ( $img ) {
		echo '<meta property="yandex_recommendations_image" content="' . esc_url( $img ) . '">' . "\n";
	}

	if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
		foreach ( $cats as $c ) {
			echo '<meta property="yandex_recommendations_tag" content="cat_' . (int) $c->term_id . '">' . "\n";
		}
	}

	$tags = get_the_tags( $id );
	if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
		foreach ( $tags as $t ) {
			echo '<meta property="yandex_recommendations_tag" content="tag_' . (int) $t->term_id . '">' . "\n";
		}
	}
}, 40 );

/** =========================
 *  3) TSF title/description without duplicates
 *  ========================= */
if ( function_exists( 'tsf' ) ) {

	function krv_tsf_clean_text( $text ): string {
		$text = (string) $text;
		$text = strip_shortcodes( $text );
		$text = wp_strip_all_tags( $text );
		$text = preg_replace( '~https?://\S+~u', '', $text );
		$text = preg_replace( '/\s+/u', ' ', $text );
		return trim( $text );
	}

	function krv_tsf_trim( $text, $max = 155 ): string {
		$text = trim( preg_replace( '/\s+/u', ' ', (string) $text ) );
		if ( $text === '' ) {
			return '';
		}

		if ( mb_strlen( $text, 'UTF-8' ) <= $max ) {
			return rtrim( $text );
		}

		$cut = mb_substr( $text, 0, $max, 'UTF-8' );
		$cut = preg_replace( '/\s+\S*$/u', '', $cut );

		return rtrim( $cut, " \t\n\r\0\x0B.,;:!-" ) . '…';
	}

	function krv_build_meta_title(): string {
		$paged        = (int) get_query_var( 'paged' );
		$paged_suffix = $paged > 1 ? ' — страница ' . $paged : '';

		if ( is_front_page() ) {
			return 'IT Решения — ИП Кривошеин Алексей Сергеевич' . $paged_suffix;
		}

		if ( is_home() ) {
			return 'Блог — krivoshein.site' . $paged_suffix;
		}

		if ( is_singular() ) {
			$id = get_queried_object_id();
			$pt = get_post_type( $id );

			if ( $pt === 'price' ) {
				$price = trim( (string) get_post_meta( $id, 'price_value', true ) );
				$t     = 'Цены: ' . get_the_title( $id );

				if ( $price !== '' ) {
					$t .= ' — ' . $price . ' ₽';
				}

				return $t . $paged_suffix;
			}

			if ( $pt === 'usluga' ) {
				return 'Услуга: ' . get_the_title( $id ) . $paged_suffix;
			}

			if ( $pt === 'project' ) {
				return 'Проект: ' . get_the_title( $id ) . $paged_suffix;
			}

			if ( $pt === 'client' ) {
				return 'Клиент: ' . get_the_title( $id ) . $paged_suffix;
			}

			if ( $pt === 'partner' ) {
				return 'Партнёр: ' . get_the_title( $id ) . $paged_suffix;
			}

			return get_the_title( $id ) . $paged_suffix;
		}

		if ( is_category() ) {
			$term = get_queried_object();
			return 'Категория: ' . ( $term->name ?? '' ) . $paged_suffix;
		}

		if ( is_tag() ) {
			$term = get_queried_object();
			return 'Тег: ' . ( $term->name ?? '' ) . $paged_suffix;
		}

		if ( is_tax() ) {
			$term      = get_queried_object();
			$tax       = $term->taxonomy ?? '';
			$tax_obj   = $tax ? get_taxonomy( $tax ) : null;
			$tax_label = ( $tax_obj && ! empty( $tax_obj->labels->singular_name ) )
				? $tax_obj->labels->singular_name
				: 'Раздел';

			return $tax_label . ': ' . ( $term->name ?? '' ) . $paged_suffix;
		}

		if ( is_post_type_archive() ) {
			$pt    = get_query_var( 'post_type' );
			$pt    = is_array( $pt ) ? reset( $pt ) : $pt;
			$obj   = $pt ? get_post_type_object( $pt ) : null;
			$label = $obj ? $obj->labels->name : 'Архив';

			return $label . $paged_suffix;
		}

		if ( is_author() ) {
			$u    = get_queried_object();
			$name = $u->display_name ?? '';
			return 'Статьи автора: ' . $name . $paged_suffix;
		}

		if ( is_search() ) {
			return 'Поиск: ' . get_search_query() . $paged_suffix;
		}

		if ( is_404() ) {
			return 'Страница не найдена' . $paged_suffix;
		}

		return '';
	}

	function krv_build_meta_description(): string {
		$paged        = (int) get_query_var( 'paged' );
		$paged_suffix = $paged > 1 ? ' Страница ' . $paged . '.' : '';

		if ( is_front_page() ) {
			return trim( 'Разработка и продвижение сайтов, WordPress, Linux, DevOps, настройка серверов и практические разборы.' . $paged_suffix );
		}

		if ( is_home() ) {
			return trim( 'Блог о Linux, WordPress, DevOps, серверах, хостинге, безопасности и веб-разработке.' . $paged_suffix );
		}

		if ( is_search() ) {
			$q = trim( (string) get_search_query() );
			return trim(
				( $q !== ''
					? 'Результаты поиска по запросу «' . $q . '» на сайте krivoshein.site.'
					: 'Поиск по материалам сайта krivoshein.site.'
				) . $paged_suffix
			);
		}

		if ( is_404() ) {
			return trim( 'Страница не найдена. Возможно, материал был удалён, перенесён или ссылка устарела.' . $paged_suffix );
		}

		if ( is_singular() ) {
			$id   = get_queried_object_id();
			$post = get_post( $id );

			if ( ! $post ) {
				return '';
			}

			$ex = krv_tsf_clean_text( get_the_excerpt( $post ) );
			$pt = get_post_type( $id );

			if ( $ex === '' && $pt === 'usluga' ) {
				$ex = krv_tsf_clean_text( get_post_meta( $id, 'service_description', true ) );
			}

			if ( $ex === '' && $pt === 'price' ) {
				$pd = krv_tsf_clean_text( get_post_meta( $id, 'price_description', true ) );
				$pv = trim( (string) get_post_meta( $id, 'price_value', true ) );
				$ex = ( $pv !== '' ? 'Стоимость: ' . $pv . ' ₽. ' : '' ) . $pd;
				$ex = krv_tsf_clean_text( $ex );
			}

			if ( $ex === '' && $pt === 'partner' ) {
				$ex = krv_tsf_clean_text( get_post_meta( $id, 'partner_description', true ) );
			}

			if ( $ex === '' ) {
				$ex = krv_tsf_clean_text( $post->post_content );
			}

			return trim( krv_tsf_trim( $ex, 155 ) . $paged_suffix );
		}

		if ( is_category() || is_tag() || is_tax() ) {
			$term   = get_queried_object();
			$name   = $term->name ?? '';
			$t_desc = isset( $term->description ) ? krv_tsf_clean_text( $term->description ) : '';

			if ( $t_desc !== '' ) {
				return trim( krv_tsf_trim( $t_desc, 155 ) . $paged_suffix );
			}

			if ( is_category() ) {
				return trim( 'Категория «' . $name . '» — статьи, инструкции, конфиги и разборы ошибок.' . $paged_suffix );
			}

			if ( is_tag() ) {
				return trim( 'Тег «' . $name . '» — подборка материалов по теме.' . $paged_suffix );
			}

			$tax       = $term->taxonomy ?? '';
			$tax_obj   = $tax ? get_taxonomy( $tax ) : null;
			$tax_label = ( $tax_obj && ! empty( $tax_obj->labels->singular_name ) )
				? $tax_obj->labels->singular_name
				: 'Раздел';

			return trim( $tax_label . ' «' . $name . '» — подборка материалов и заметок.' . $paged_suffix );
		}

		if ( is_post_type_archive() ) {
			$pt    = get_query_var( 'post_type' );
			$pt    = is_array( $pt ) ? reset( $pt ) : $pt;
			$obj   = $pt ? get_post_type_object( $pt ) : null;
			$label = $obj ? $obj->labels->name : 'Архив';

			return trim( $label . ': подборка материалов и заметок на сайте.' . $paged_suffix );
		}

		if ( is_author() ) {
			$u    = get_queried_object();
			$name = $u->display_name ?? '';
			return trim( 'Публикации автора ' . $name . ': Linux, WordPress, DevOps и практические разборы.' . $paged_suffix );
		}

		return '';
	}

	add_filter( 'the_seo_framework_title_from_generation', function( $title, $args ) {
		if ( null !== $args ) {
			return $title;
		}

		$new = krv_build_meta_title();
		return $new !== '' ? $new : $title;
	}, 10, 2 );

	add_filter( 'the_seo_framework_generated_description', function( $description, $args, $type = null ) {
		if ( null !== $args ) {
			return $description;
		}

		$new = krv_build_meta_description();
		return $new !== '' ? $new : $description;
	}, 10, 3 );

	add_filter( 'the_seo_framework_robots_meta_array', function( $meta, $args, $options = null ) {
		if ( null === $args ) {
			$queried_object = get_queried_object();
			$taxonomy       = $queried_object instanceof WP_Term ? $queried_object->taxonomy : null;
		} else {
			$taxonomy = is_array( $args ) ? ( $args['tax'] ?? null ) : null;
		}

		if ( 'post_tag' === $taxonomy ) {
			$meta['noindex'] = 'noindex';
		}

		if ( null === $args && is_paged() && ! is_singular() ) {
			$meta['noindex'] = 'noindex';
		}

		return $meta;
	}, 10, 3 );
}

/** =========================
 *  Telegram comments + RSYA block renderer
 *  IMPORTANT: called manually from single.php
 *  ========================= */
function krv_render_post_extras(): void {
	if ( is_admin() || ! krv_is_single_content() ) {
		return;
	}

	$project_url = is_singular( 'project' )
		? trim( (string) get_post_meta( get_queried_object_id(), 'project_url', true ) )
		: '';
	?>
	<div class="krv-post-extras" style="clear:both;display:block;width:100%;margin-top:40px;">
		<?php if ( $project_url !== '' ) : ?>
			<p class="krv-project-link"><a href="<?php echo esc_url( $project_url ); ?>" target="_blank" rel="noopener noreferrer">Открыть сайт проекта</a></p>
		<?php endif; ?>

		<div id="telegram-comments" style="clear:both;display:block;width:100%;min-height:120px;margin:0 0 20px;">
			<?php krv_render_telegram_discussion_widget(); ?>
		</div>

		<?php if ( krv_rsya_reco_enabled() ) : ?>
			<div class="krv-rsya-reco" style="clear:both;display:block;width:100%;margin-top:24px;">
				<div id="<?php echo esc_attr( krv_rsya_reco_render_to() ); ?>"></div>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/** =========================
 *  4) Render RSYA recommendations
 *  ========================= */
add_action( 'wp_footer', function () {
	if ( ! krv_rsya_reco_enabled() || ! krv_is_single_content() ) {
		return;
	}
	?>
	<style>
		#<?php echo esc_html( krv_rsya_reco_render_to() ); ?> {
			min-height: 320px;
		}
	</style>
	<script>
	(function () {
		var renderTo = <?php echo wp_json_encode( krv_rsya_reco_render_to() ); ?>;
		var blockId  = <?php echo wp_json_encode( krv_rsya_reco_block_id() ); ?>;

		var el = document.getElementById(renderTo);
		if (!el) return;

		function hasFill() {
			return !!el.querySelector('iframe');
		}

		function renderOnce() {
			if (el.dataset.krvRecoInit === '1') return;
			el.dataset.krvRecoInit = '1';

			window.yaContextCb = window.yaContextCb || [];
			window.yaContextCb.push(function () {
				try {
					if (!window.Ya || !Ya.Context || !Ya.Context.AdvManager) return;
					if (hasFill()) return;

					Ya.Context.AdvManager.renderWidget({
						renderTo: renderTo,
						blockId: blockId
					});
				} catch (e) {}
			});
		}

		renderOnce();

		setTimeout(function () {
			if (hasFill()) return;
			el.innerHTML = '';
			el.dataset.krvRecoInit = '0';
			renderOnce();
		}, 9000);
	})();
	</script>
	<?php
}, 200 );

/** =========================
 *  5) RSYA InImage
 *  ========================= */
add_action( 'wp_footer', function () {
	if ( ! krv_rsya_inimage_enabled() || ! krv_is_single_content() ) {
		return;
	}
	?>
	<script>
	(function () {
		window.yaContextCb = window.yaContextCb || [];
		var blockId = <?php echo wp_json_encode( krv_rsya_inimage_block_id() ); ?>;

		function waitYa(maxMs, cb) {
			var t0 = Date.now();
			(function tick() {
				if (window.Ya && Ya.Context && Ya.Context.AdvManager) return cb();
				if (Date.now() - t0 > maxMs) return;
				setTimeout(tick, 150);
			})();
		}

		function isLightboxCandidate(img) {
			return !!img.closest('a, figure, .wp-block-gallery, .gallery, .kadence-gallery, .kt-blocks-gallery, [data-fancybox], [data-lightbox], [data-lg], .lg-item, .lightbox, .pswp');
		}

		function renderTo(slotId) {
			window.yaContextCb.push(function () {
				try {
					if (!window.Ya || !Ya.Context || !Ya.Context.AdvManager) return;
					Ya.Context.AdvManager.render({
						renderTo: slotId,
						blockId: blockId,
						type: 'inImage'
					});
				} catch (e) {}
			});
		}

		function start() {
			waitYa(7000, function () {
				var root =
					document.querySelector('.entry-content') ||
					document.querySelector('.post-single-content') ||
					document.querySelector('article') ||
					document;

				var images = Array.from(root.querySelectorAll('img'));
				if (!images.length) return;

				images.forEach(function (img) {
					if (!img || img.dataset.krvInimageDone === '1') return;

					var w = img.naturalWidth || img.width || 0;
					var h = img.naturalHeight || img.height || 0;

					if (w < 320 || h < 200) return;
					if (isLightboxCandidate(img)) return;

					img.dataset.krvInimageDone = '1';

					var slot = document.createElement('div');
					slot.id = 'yandex_rtb_' + blockId + '-' + Math.random().toString(16).slice(2);
					slot.className = 'krv-rsya-inimage-slot';
					slot.style.cssText = 'display:block;margin:8px 0 16px;';

					img.insertAdjacentElement('afterend', slot);

					if (!img.complete) {
						img.addEventListener('load', function () {
							renderTo(slot.id);
						}, { once: true });
					} else {
						renderTo(slot.id);
					}
				});
			});
		}

		if (document.readyState === 'complete') {
			start();
		} else {
			window.addEventListener('load', start, { once: true });
		}
	})();
	</script>
	<?php
}, 220 );

/** =========================
 *  6) Other site tweaks
 *  ========================= */

/**
 * Reading time (legacy name kept for template compatibility).
 * Word count is Cyrillic-safe: str_word_count() does not count Russian words.
 */
if ( ! function_exists( 'arkaiReadingTime' ) ) {
	function arkaiReadingTime() {
		global $post;

		if ( ! $post ) {
			return '—';
		}

		$content = wp_strip_all_tags( (string) $post->post_content );
		preg_match_all( '/[\p{L}\p{N}_-]+/u', $content, $matches );

		$words   = ! empty( $matches[0] ) ? count( $matches[0] ) : 0;
		$minutes = (int) floor( $words / 120 );

		if ( $minutes < 1 ) {
			$minutes = 1;
		}

		if ( $minutes === 1 ) {
			return $minutes . ' минута чтения';
		}

		if ( $minutes >= 2 && $minutes <= 4 ) {
			return $minutes . ' минуты чтения';
		}

		return $minutes . ' минут чтения';
	}
}

/** Clean author slug "-2" */
add_action( 'profile_update', function ( $user_id ) {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return;
	}

	$user_slug  = $user->user_nicename;
	$clean_slug = preg_replace( '/(-2)+$/', '', $user_slug );

	if ( $clean_slug !== $user_slug ) {
		wp_update_user( [
			'ID'            => $user_id,
			'user_nicename' => $clean_slug,
		] );
	}
} );

/** Telegram meta */
add_action( 'wp_head', function () {
	if ( is_singular( 'post' ) || is_singular( 'project' ) ) {
		echo '<meta property="telegram:channel" content="@' . esc_attr( KRV_TG_DISCUSSION ) . '">' . "\n";
	}
}, 50 );

/** Disable built-in comments only where Telegram comments replace them. */
function krv_uses_telegram_comments( int $post_id = 0 ): bool {
	$post_type = get_post_type( $post_id ?: get_queried_object_id() );

	return in_array( $post_type, array( 'post', 'project' ), true );
}

add_filter( 'comments_open', function ( $open, $post_id ) {
	return krv_uses_telegram_comments( (int) $post_id ) ? false : $open;
}, 20, 2 );

add_filter( 'pings_open', function ( $open, $post_id ) {
	return krv_uses_telegram_comments( (int) $post_id ) ? false : $open;
}, 20, 2 );

add_filter( 'get_comments_number', function ( $count, $post_id ) {
	return krv_uses_telegram_comments( (int) $post_id ) ? 0 : $count;
}, 20, 2 );

/** Code blocks: set default language + hljs */
add_action( 'wp_enqueue_scripts', function () {
	if ( is_admin() || ! is_singular( 'post' ) ) {
		return;
	}

	wp_register_script( 'code-auto-lang', '', [], null, true );
	wp_enqueue_script( 'code-auto-lang' );

	$js = <<<JS
document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('pre.wp-block-code code:not([class])').forEach(function(c) {
		c.classList.add('language-bash');
	});

	if (window.hljs) {
		document.querySelectorAll('pre code').forEach(function(el) {
			hljs.highlightElement(el);
		});
	}
});
JS;

	wp_add_inline_script( 'code-auto-lang', $js );
} );

/** Redirect */
add_action( 'template_redirect', function () {
	if ( is_admin() ) {
		return;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] )
		? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
		: '';

	$req = trim( (string) parse_url( $request_uri, PHP_URL_PATH ), '/' );

	if ( $req === 'kalkulyator-setevyh-masok-ip' ) {
		wp_redirect( home_url( '/kalkulyator-setevyh-masok/' ), 301 );
		exit;
	}
} );

/** Translator in menu */
add_filter( 'wp_nav_menu_items', function ( $items, $args ) {
	if ( ! isset( $args->theme_location ) || $args->theme_location !== 'headermenu' ) {
		return $items;
	}

	if ( ! shortcode_exists( 'translator-revolution' ) ) {
		return $items;
	}

	$items .= '<li class="menu-item">' . do_shortcode( '[translator-revolution]' ) . '</li>';

	return $items;
}, 10, 2 );


/** =========================
 *  Modular includes (split from monolith)
 *  ========================= */
$krv_core_dir = dirname( __FILE__ );

require_once $krv_core_dir . '/cpt.php';
require_once $krv_core_dir . '/acf-fields.php';
// [krv_services_landing] — includes/shortcodes/services-landing.php (loaded from drslon-site-core.php).
require_once $krv_core_dir . '/shortcodes/clients-grid.php';
require_once $krv_core_dir . '/shortcodes/partners-grid.php';
require_once $krv_core_dir . '/shortcodes/services-pages-showcase.php';
