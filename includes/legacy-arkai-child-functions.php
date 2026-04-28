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

define( 'KRV_RSYA_RECO_BLOCK_ID', 'C-A-6903522-1' );
define( 'KRV_RSYA_RECO_RENDER_TO', 'yandex_rtb_C-A-6903522-1' );

define( 'KRV_RSYA_INIMAGE_BLOCK_ID', 'R-A-6903522-2' );

/** =========================
 *  HELPERS
 *  ========================= */
function krv_is_single_content(): bool {
	return ! is_admin() && ( is_singular( 'post' ) || is_singular( 'project' ) );
}

function krv_page_has_ui_shortcode( array $shortcodes ): bool {
	if ( is_admin() || ! is_singular() ) {
		return false;
	}

	$post = get_post();
	if ( ! $post || empty( $post->post_content ) ) {
		return false;
	}

	foreach ( $shortcodes as $shortcode ) {
		if ( has_shortcode( $post->post_content, $shortcode ) ) {
			return true;
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
	if ( is_admin() || ! krv_is_single_content() ) {
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
	if ( ! krv_is_single_content() ) {
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

	add_filter( 'the_seo_framework_title_from_custom_field', function( $title, $args ) {
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

	add_filter( 'the_seo_framework_custom_field_description', function( $description, $args ) {
		if ( null !== $args ) {
			return $description;
		}

		$new = krv_build_meta_description();
		return $new !== '' ? $new : $description;
	}, 10, 2 );

	add_filter( 'the_seo_framework_robots_meta_array', function( $meta, $args, $options ) {
		$taxonomy = null === $args ? tsf()->query()->get_current_taxonomy() : ( $args['taxonomy'] ?? null );

		if ( 'post_tag' === $taxonomy ) {
			$meta['noindex']  = 'noindex';
			$meta['nofollow'] = 'nofollow';
		}

		if ( null === $args && is_paged() && ! is_singular() ) {
			$meta['noindex'] = 'noindex';
		}

		return $meta;
	}, 10, 3 );
}

/** Disable WP core sitemap */
add_filter( 'wp_sitemaps_enabled', '__return_false' );

/** =========================
 *  Telegram comments + RSYA block renderer
 *  IMPORTANT: called manually from single.php
 *  ========================= */
function krv_render_post_extras(): void {
	if ( is_admin() || ! krv_is_single_content() ) {
		return;
	}
	?>
	<div class="krv-post-extras" style="clear:both;display:block;width:100%;margin-top:40px;">
		<div id="telegram-comments" style="clear:both;display:block;width:100%;margin:0 0 20px;">
			<script async
				src="https://telegram.org/js/telegram-widget.js?21"
				data-telegram-discussion="<?php echo esc_attr( KRV_TG_DISCUSSION ); ?>"
				data-comments-limit="30"
				data-color="5282FF"
				data-dark="0"></script>
		</div>

		<div class="krv-rsya-reco" style="clear:both;display:block;width:100%;margin-top:24px;">
			<div id="<?php echo esc_attr( KRV_RSYA_RECO_RENDER_TO ); ?>"></div>
		</div>
	</div>
	<?php
}

/** =========================
 *  4) Render RSYA recommendations
 *  ========================= */
add_action( 'wp_footer', function () {
	if ( ! krv_is_single_content() ) {
		return;
	}
	?>
	<style>
		#<?php echo esc_html( KRV_RSYA_RECO_RENDER_TO ); ?> {
			min-height: 320px;
		}
	</style>
	<script>
	(function () {
		var renderTo = <?php echo wp_json_encode( KRV_RSYA_RECO_RENDER_TO ); ?>;
		var blockId  = <?php echo wp_json_encode( KRV_RSYA_RECO_BLOCK_ID ); ?>;

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
	if ( ! krv_is_single_content() ) {
		return;
	}
	?>
	<script>
	(function () {
		window.yaContextCb = window.yaContextCb || [];
		var blockId = <?php echo wp_json_encode( KRV_RSYA_INIMAGE_BLOCK_ID ); ?>;

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
add_filter( 'unzip_file_use_ziparchive', '__return_false' );
add_filter( 'auto_update_theme', '__return_false' );

/** Views counter */
add_action( 'wp_head', function () {
	if ( ! ( is_singular( 'post' ) || is_singular( 'project' ) ) ) {
		return;
	}

	global $post;
	$post_id = $post ? (int) $post->ID : 0;

	if ( $post_id && function_exists( 'arkaiSetPostViews' ) ) {
		arkaiSetPostViews( $post_id );
	}
} );

/** Reading time */
function arkaiReadingTime() {
	global $post;

	if ( ! $post ) {
		return '—';
	}

	$words   = str_word_count( strip_tags( $post->post_content ) );
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
		echo '<meta property="telegram:channel" content="@drslon_channel">' . "\n";
	}
}, 50 );

/** Disable built-in comments */
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );
add_filter( 'get_comments_number', '__return_zero' );

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

	$req = trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );

	if ( $req === 'kalkulyator-setevyh-masok-ip' ) {
		wp_redirect( home_url( '/kalkulyator-setevyh-masok/' ), 301 );
		exit;
	}
} );

/** =========================
 *  7) Custom post types + taxonomy
 *  ========================= */
add_action( 'init', function () {
	$post_types = [
		'client'  => [
			'name'          => 'Клиенты',
			'singular_name' => 'Клиент',
			'menu_name'     => 'Клиенты',
			'supports'      => [ 'title', 'thumbnail', 'page-attributes' ],
		],
		'project' => [
			'name'          => 'Проекты',
			'singular_name' => 'Проект',
			'menu_name'     => 'Проекты',
			'supports'      => [ 'title', 'editor', 'thumbnail' ],
			'has_archive'   => 'project',
		],
		'usluga'  => [
			'name'          => 'Услуги',
			'singular_name' => 'Услуга',
			'menu_name'     => 'Услуги',
			'supports'      => [ 'title', 'thumbnail' ],
		],
		'price'   => [
			'name'          => 'Цены',
			'singular_name' => 'Цена',
			'menu_name'     => 'Цены',
			'supports'      => [ 'title', 'thumbnail' ],
		],
		'partner' => [
			'name'          => 'Партнёры',
			'singular_name' => 'Партнёр',
			'menu_name'     => 'Партнёры',
			'supports'      => [ 'title', 'thumbnail', 'page-attributes' ],
		],
	];

	foreach ( $post_types as $slug => $args ) {
		register_post_type( $slug, [
			'labels'       => [
				'name'          => $args['name'],
				'singular_name' => $args['singular_name'],
				'menu_name'     => $args['menu_name'],
				'all_items'     => "Все {$args['name']}",
				'edit_item'     => "Изменить {$args['singular_name']}",
				'add_new_item'  => "Добавить новое {$args['singular_name']}",
				'new_item'      => "Новый {$args['singular_name']}",
				'view_item'     => "Посмотреть {$args['singular_name']}",
				'not_found'     => "Не найдено {$args['name']}",
			],
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-admin-post',
			'supports'     => $args['supports'],
			'has_archive'  => $args['has_archive'] ?? false,
			'rewrite'      => [ 'slug' => $slug ],
		] );
	}

	register_taxonomy( 'partner_category', [ 'partner' ], [
		'labels' => [
			'name'          => 'Категории партнёров',
			'singular_name' => 'Категория партнёра',
			'search_items'  => 'Искать категории',
			'all_items'     => 'Все категории',
			'edit_item'     => 'Редактировать категорию',
			'update_item'   => 'Обновить категорию',
			'add_new_item'  => 'Добавить категорию',
			'new_item_name' => 'Новая категория',
			'menu_name'     => 'Категории партнёров',
		],
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => [ 'slug' => 'partner-category' ],
	] );
} );

/** =========================
 *  8) ACF local groups + options page
 *  ========================= */
add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( [
		'key'      => 'group_clients',
		'title'    => 'Клиенты',
		'fields'   => [
			[
				'key'   => 'field_client_url',
				'label' => 'Ссылка на клиента',
				'name'  => 'client_url',
				'type'  => 'url',
			],
			[
				'key'   => 'field_client_description',
				'label' => 'Описание клиента',
				'name'  => 'client_description',
				'type'  => 'text',
			],
		],
		'location' => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'client',
				],
			],
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_projects',
		'title'    => 'Проекты',
		'fields'   => [
			[
				'key'   => 'field_project_url',
				'label' => 'URL проекта',
				'name'  => 'project_url',
				'type'  => 'url',
			],
		],
		'location' => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'project',
				],
			],
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_services',
		'title'    => 'Услуги',
		'fields'   => [
			[
				'key'   => 'field_service_description',
				'label' => 'Описание услуги',
				'name'  => 'service_description',
				'type'  => 'text',
			],
			[
				'key'         => 'field_service_icon',
				'label'       => 'Иконка',
				'name'        => 'service_icon',
				'type'        => 'font-awesome',
				'icon_sets'   => [ 'fas', 'far', 'fab' ],
				'save_format' => 'element',
			],
		],
		'location' => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'usluga',
				],
			],
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_prices',
		'title'    => 'Цены',
		'fields'   => [
			[
				'key'   => 'field_price_description',
				'label' => 'Описание',
				'name'  => 'price_description',
				'type'  => 'text',
			],
			[
				'key'    => 'field_price_value',
				'label'  => 'Стоимость',
				'name'   => 'price_value',
				'type'   => 'text',
				'append' => '₽',
			],
		],
		'location' => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'price',
				],
			],
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_partners',
		'title'    => 'Партнёры',
		'fields'   => [
			[
				'key'   => 'field_partner_url',
				'label' => 'Ссылка партнёра',
				'name'  => 'partner_url',
				'type'  => 'url',
			],
			[
				'key'   => 'field_partner_description',
				'label' => 'Описание партнёра',
				'name'  => 'partner_description',
				'type'  => 'textarea',
				'rows'  => 3,
			],
			[
				'key'           => 'field_partner_button_text',
				'label'         => 'Текст кнопки',
				'name'          => 'partner_button_text',
				'type'          => 'text',
				'default_value' => 'Перейти',
			],
			[
				'key'   => 'field_partner_badge',
				'label' => 'Метка',
				'name'  => 'partner_badge',
				'type'  => 'text',
			],
			[
				'key'           => 'field_partner_is_featured',
				'label'         => 'Выделить партнёра',
				'name'          => 'partner_is_featured',
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 0,
			],
			[
				'key'           => 'field_partner_nofollow',
				'label'         => 'Добавить nofollow',
				'name'          => 'partner_nofollow',
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 1,
			],
			[
				'key'           => 'field_partner_sponsored',
				'label'         => 'Добавить sponsored',
				'name'          => 'partner_sponsored',
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 1,
			],
		],
		'location' => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'partner',
				],
			],
		],
	] );

	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page( [
			'page_title' => 'Витрина сервисов',
			'menu_title' => 'Витрина сервисов',
			'menu_slug'  => 'krv-services-showcase',
			'capability' => 'edit_posts',
			'redirect'   => false,
			'position'   => 61,
			'icon_url'   => 'dashicons-screenoptions',
		] );

		acf_add_local_field_group( [
			'key'    => 'group_krv_services_pages_showcase',
			'title'  => 'Витрина сервисных страниц',
			'fields' => [
				[
					'key'          => 'field_krv_services_sections',
					'label'        => 'Секции',
					'name'         => 'krv_services_sections',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Добавить секцию',
					'sub_fields'   => [
						[
							'key'   => 'field_krv_services_section_title',
							'label' => 'Заголовок секции',
							'name'  => 'section_title',
							'type'  => 'text',
						],
						[
							'key'           => 'field_krv_services_section_pages',
							'label'         => 'Страницы',
							'name'          => 'section_pages',
							'type'          => 'relationship',
							'post_type'     => [ 'page' ],
							'filters'       => [ 'search' ],
							'elements'      => [ 'featured_image' ],
							'return_format' => 'id',
						],
					],
				],
			],
			'location' => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'krv-services-showcase',
					],
				],
			],
		] );
	}
} );

add_filter( 'acf/settings/show_admin', '__return_false' );

/** Translator in menu */
add_filter( 'wp_nav_menu_items', function ( $items, $args ) {
	if ( isset( $args->theme_location ) && $args->theme_location === 'headermenu' ) {
		$items .= '<li class="menu-item">' . do_shortcode( '[translator-revolution]' ) . '</li>';
	}
	return $items;
}, 10, 2 );

/**
 * Combined landing shortcode
 * Usage: [krv_services_landing]
 */
add_shortcode( 'krv_services_landing', function () {
	ob_start();
	?>
	<div class="krv-services-landing">
		<style>
			.krv-services-landing {
				--accent: #5181fe;
				--accent-hover: #4169d4;
				--accent-bg: #eaf1ff;
				--text-main: #333;
				--text-soft: #666;
				--card-bg: #fff;
				--card-radius: 20px;
				--card-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
				--card-shadow-hover: 0 6px 15px rgba(0, 0, 0, 0.15);
				--service-radius: 10px;
				max-width: 1200px;
				margin: 0 auto;
				padding: 20px 15px;
				font-family: "Poppins", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
				color: var(--text-main);
			}

			.krv-services-landing,
			.krv-services-landing * {
				box-sizing: border-box;
			}

			.krv-services-landing-section + .krv-services-landing-section {
				margin-top: 22px;
			}

			.krv-landing-contact-card {
				width: 100%;
				padding: 24px;
				text-align: center;
				background: var(--card-bg);
				border-radius: var(--card-radius);
				box-shadow: var(--card-shadow);
			}

			.krv-landing-avatar-wrap {
				width: 126px;
				height: 126px;
				margin: 0 auto 20px;
				border: 3px solid var(--accent);
				border-radius: 50%;
				overflow: hidden;
				background: #fff;
			}

			.krv-landing-avatar {
				display: block;
				width: 100%;
				height: 100%;
				object-fit: cover;
				object-position: center;
			}

			.krv-landing-title {
				margin: 0 0 15px;
				font-size: 1.8rem;
				line-height: 1.2;
				color: var(--text-main);
			}

			.krv-landing-lead,
			.krv-landing-meta {
				margin: 0 0 14px;
				font-size: 1rem;
				line-height: 1.5;
				color: var(--text-soft);
			}

			.krv-landing-meta {
				display: flex;
				flex-direction: column;
				gap: 4px;
				align-items: center;
			}

			.krv-landing-meta-line {
				display: block;
			}

			.krv-landing-contacts {
				display: flex;
				justify-content: center;
				align-items: center;
				flex-wrap: wrap;
				gap: 15px;
				margin-top: 10px;
			}

			.krv-landing-contacts a {
				display: flex;
				justify-content: center;
				align-items: center;
				width: 50px;
				height: 50px;
				text-decoration: none;
				color: var(--accent);
				background: #f9f9f9;
				border: 3px solid var(--accent);
				border-radius: 50%;
				transition: border-color 0.25s ease, background 0.25s ease, box-shadow 0.25s ease;
			}

			.krv-landing-contacts a:hover {
				border-color: var(--accent-hover);
				background: var(--accent-bg);
				box-shadow: var(--card-shadow);
			}

			.krv-landing-social-icon {
				display: block;
				width: 22px;
				height: 22px;
				color: currentColor;
				flex-shrink: 0;
			}

			.krv-landing-social-icon path,
			.krv-landing-social-icon circle,
			.krv-landing-social-icon rect {
				fill: currentColor;
			}

			.krv-landing-services {
				padding: 15px 0 0;
			}

			.krv-landing-services-header {
				margin-bottom: 20px;
				text-align: center;
			}

			.krv-landing-services-header h2 {
				margin: 0 0 10px;
				font-size: 2rem;
				color: var(--text-main);
			}

			.krv-landing-services-header p {
				margin: 0;
				font-size: 1rem;
				color: var(--text-soft);
			}

			.krv-landing-services-grid {
				display: grid;
				grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
				gap: 20px;
				width: 100%;
			}

			.krv-landing-service-item {
				width: 100%;
				padding: 15px;
				text-align: center;
				background: var(--card-bg);
				border-radius: var(--service-radius);
				box-shadow: var(--card-shadow);
				transition: box-shadow 0.25s ease;
			}

			.krv-landing-service-item:hover {
				box-shadow: var(--card-shadow-hover);
			}

			.krv-landing-service-icon {
				display: inline-block;
				width: 34px;
				height: 34px;
				margin-bottom: 15px;
				color: var(--accent);
			}

			.krv-landing-service-icon path,
			.krv-landing-service-icon circle,
			.krv-landing-service-icon rect,
			.krv-landing-service-icon polyline,
			.krv-landing-service-icon line {
				fill: none;
				stroke: currentColor;
				stroke-width: 1.8;
				stroke-linecap: round;
				stroke-linejoin: round;
			}

			.krv-landing-service-item h3 {
				margin: 0 0 10px;
				font-size: 1.25rem;
				color: var(--text-main);
			}

			.krv-landing-service-item p {
				margin: 0;
				font-size: 0.9rem;
				line-height: 1.5;
				color: var(--text-soft);
			}

			.krv-landing-pricing {
				width: 100%;
				padding: 20px;
				text-align: center;
				background: var(--card-bg);
				border-radius: var(--card-radius);
				box-shadow: var(--card-shadow);
			}

			.krv-landing-pricing-title {
				margin: 0 0 15px;
				font-size: 2rem;
				line-height: 1.2;
				color: var(--text-main);
			}

			.krv-landing-pricing-lead {
				margin: 0 0 18px;
				font-size: 1.1rem;
				line-height: 1.6;
				color: var(--text-soft);
			}

			.krv-landing-pricing-rate {
				display: inline-block;
				margin-top: 6px;
				font-size: 2rem;
				font-weight: 700;
				line-height: 1.2;
				color: var(--accent);
			}

			.krv-landing-pricing-list {
				list-style: none;
				margin: 0 0 22px;
				padding: 0;
				display: grid;
				gap: 12px;
			}

			.krv-landing-pricing-list li {
				display: flex;
				align-items: flex-start;
				justify-content: center;
				gap: 10px;
				font-size: 1rem;
				line-height: 1.5;
				color: var(--text-soft);
				text-align: left;
			}

			.krv-landing-pricing-icon {
				flex: 0 0 20px;
				width: 20px;
				height: 20px;
				margin-top: 2px;
				color: var(--accent);
			}

			.krv-landing-pricing-icon path,
			.krv-landing-pricing-icon circle,
			.krv-landing-pricing-icon rect,
			.krv-landing-pricing-icon polyline,
			.krv-landing-pricing-icon line {
				fill: none;
				stroke: currentColor;
				stroke-width: 1.8;
				stroke-linecap: round;
				stroke-linejoin: round;
			}

			.krv-landing-pricing-button {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				min-height: 46px;
				padding: 12px 22px;
				border-radius: 10px;
				background: var(--accent);
				color: #fff;
				text-decoration: none;
				font-size: 1rem;
				font-weight: 600;
				line-height: 1;
				transition: background 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
			}

			.krv-landing-pricing-button:hover {
				background: var(--accent-hover);
				box-shadow: var(--card-shadow-hover);
			}

			.krv-landing-pricing-button:active {
				transform: translateY(1px);
			}

			@media (max-width: 890px) {
				.krv-landing-services-grid {
					grid-template-columns: 1fr;
				}
			}

			@media (max-width: 768px) {
				.krv-services-landing {
					padding: 16px 10px;
				}

				.krv-landing-contact-card,
				.krv-landing-pricing {
					padding: 15px;
				}

				.krv-landing-avatar-wrap {
					width: 112px;
					height: 112px;
				}

				.krv-landing-title,
				.krv-landing-services-header h2,
				.krv-landing-pricing-title {
					font-size: 1.5rem;
				}

				.krv-landing-lead,
				.krv-landing-meta,
				.krv-landing-services-header p,
				.krv-landing-pricing-lead {
					font-size: 0.95rem;
				}

				.krv-landing-contacts {
					gap: 10px;
				}

				.krv-landing-contacts a {
					width: 40px;
					height: 40px;
				}

				.krv-landing-social-icon {
					width: 20px;
					height: 20px;
				}

				.krv-landing-service-icon {
					width: 30px;
					height: 30px;
				}

				.krv-landing-pricing-rate {
					font-size: 1.8rem;
				}

				.krv-landing-pricing-list li {
					font-size: 0.95rem;
				}
			}

			@media (max-width: 480px) {
				.krv-services-landing {
					padding: 12px 8px;
				}

				.krv-landing-contact-card,
				.krv-landing-pricing {
					padding: 10px;
				}

				.krv-landing-avatar-wrap {
					width: 104px;
					height: 104px;
				}

				.krv-landing-contacts {
					gap: 8px;
				}

				.krv-landing-contacts a {
					width: 36px;
					height: 36px;
				}

				.krv-landing-social-icon {
					width: 18px;
					height: 18px;
				}

				.krv-landing-service-icon {
					width: 28px;
					height: 28px;
				}

				.krv-landing-title,
				.krv-landing-services-header h2,
				.krv-landing-pricing-title {
					font-size: 1.5rem;
				}

				.krv-landing-pricing-rate {
					font-size: 1.6rem;
				}

				.krv-landing-pricing-list li {
					gap: 8px;
					font-size: 0.9rem;
				}

				.krv-landing-pricing-button {
					width: 100%;
					max-width: 280px;
					padding: 10px 16px;
					font-size: 0.9rem;
				}
			}
		</style>

		<div class="krv-services-landing-section">
			<div class="krv-landing-contact-card">
				<div class="krv-landing-avatar-wrap">
					<img class="krv-landing-avatar" src="https://krivoshein.site/wp-content/uploads/2025/09/20b24d82a128f6065685a27f843a684a9e7789ad.jpg" alt="Алексей Кривошеин">
				</div>

				<h2 class="krv-landing-title">Алексей Кривошеин</h2>

				<p class="krv-landing-lead">Специализируюсь на разработке и продвижении веб-сайтов, администрировании серверов и поддержке существующих проектов.</p>

				<div class="krv-landing-meta">
					<span class="krv-landing-meta-line">Работаю по договору и принимаю безналичный расчёт.</span>
					<span class="krv-landing-meta-line">ОГРН 321774600479249</span>
					<span class="krv-landing-meta-line">ИНН 770603253213</span>
				</div>

				<div class="krv-landing-contacts">
					<a href="https://t.me/DrSlon" target="_blank" title="Telegram" rel="noopener noreferrer" aria-label="Telegram">
						<svg class="krv-landing-social-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21.543 2.498a1.53 1.53 0 0 0-1.58-.26L3.55 8.617a1.54 1.54 0 0 0 .08 2.893l4.11 1.353 1.59 5.01a1.54 1.54 0 0 0 2.52.66l2.29-2.21 3.78 2.78a1.54 1.54 0 0 0 2.42-.9L21.98 4.01a1.53 1.53 0 0 0-.437-1.512ZM9.33 11.97l8.09-4.98-6.7 6.46-.26 2.76-1.13-4.24Z"/></svg>
					</a>

					<a href="https://github.com/A-Krivoshen" target="_blank" title="GitHub" rel="noopener noreferrer" aria-label="GitHub">
						<svg class="krv-landing-social-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 2C6.48 2 2 6.59 2 12.25c0 4.53 2.87 8.37 6.84 9.73.5.1.68-.22.68-.49 0-.24-.01-1.03-.01-1.87-2.78.62-3.37-1.21-3.37-1.21-.45-1.18-1.11-1.49-1.11-1.49-.91-.64.07-.63.07-.63 1 .07 1.53 1.06 1.53 1.06.9 1.57 2.35 1.12 2.92.85.09-.67.35-1.12.64-1.38-2.22-.26-4.56-1.15-4.56-5.1 0-1.13.39-2.06 1.03-2.79-.1-.26-.45-1.31.1-2.74 0 0 .84-.28 2.75 1.07A9.32 9.32 0 0 1 12 6.84c.85 0 1.71.12 2.51.36 1.9-1.35 2.74-1.07 2.74-1.07.55 1.43.2 2.48.1 2.74.64.73 1.03 1.66 1.03 2.79 0 3.96-2.34 4.83-4.57 5.09.36.32.68.95.68 1.92 0 1.39-.01 2.5-.01 2.84 0 .27.18.59.69.49A10.25 10.25 0 0 0 22 12.25C22 6.59 17.52 2 12 2Z"/></svg>
					</a>

					<a href="https://vk.com/drslon" target="_blank" title="VK" rel="noopener noreferrer" aria-label="VK">
						<svg class="krv-landing-social-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3.61 5.18c.13 6.32 3.3 10.12 8.86 10.12h.32v-3.62c2.04.21 3.58 1.7 4.2 3.62H20c-.79-2.88-2.87-4.47-4.17-5.08 1.3-.76 3.12-2.59 3.56-5.04h-2.74c-.57 1.99-2.25 3.82-3.86 3.99V5.18H10.1v7c-1.63-.42-3.7-2.39-3.79-7H3.61Z"/></svg>
					</a>

					<a href="https://mastodon.ml/@krivoshein" target="_blank" title="Mastodon" rel="noopener noreferrer" aria-label="Mastodon">
						<svg class="krv-landing-social-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20.94 14c-.28 1.41-2.45 2.96-4.95 3.25-1.3.15-2.58.3-3.95.24-2.24-.1-4-.5-4-.5v.62c.32 2.22 2.25 2.35 4.03 2.41 1.8.05 3.4-.43 3.4-.43l.08 1.65s-1.26.69-3.5.82c-1.23.07-2.76-.03-4.54-.48-3.86-.95-4.52-4.78-4.63-8.67-.03-1.16-.01-2.25-.01-3.16 0-3.98 2.61-5.15 2.61-5.15C6.8 3.9 9.03 3.6 11.23 3.58h.05c2.2.02 4.43.32 5.75.99 0 0 2.61 1.17 2.61 5.15 0 0 .03 2.93-.7 4.28Zm-3.1-4.39c0-.98-.25-1.76-.77-2.33-.54-.57-1.24-.87-2.12-.87-1.01 0-1.78.39-2.3 1.18l-.5.83-.5-.83c-.52-.79-1.29-1.18-2.3-1.18-.88 0-1.58.3-2.12.87-.52.57-.77 1.35-.77 2.33v4.79h1.9V9.75c0-.98.41-1.48 1.24-1.48.91 0 1.37.59 1.37 1.75v2.56h1.88v-2.56c0-1.16.46-1.75 1.37-1.75.83 0 1.24.5 1.24 1.48v4.65h1.9V9.61Z"/></svg>
					</a>

					<a href="https://www.linkedin.com/in/krivosheinaleksey" target="_blank" title="LinkedIn" rel="noopener noreferrer" aria-label="LinkedIn">
						<svg class="krv-landing-social-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6.94 8.5H3.56V20h3.38V8.5ZM5.25 3A2.06 2.06 0 0 0 3.2 5.06c0 1.13.92 2.06 2.05 2.06A2.07 2.07 0 0 0 7.31 5.06 2.07 2.07 0 0 0 5.25 3Zm6.84 5.5H8.83V20h3.26v-6.05c0-1.6.3-3.14 2.25-3.14 1.92 0 1.95 1.8 1.95 3.24V20h3.27v-6.62c0-3.25-.7-5.74-4.5-5.74-1.82 0-3.04 1.02-3.54 1.99h-.05V8.5Z"/></svg>
					</a>

					<a href="https://krivoshein.site/max" target="_blank" title="MAX" rel="noopener noreferrer" aria-label="MAX">
						<svg class="krv-landing-social-icon" viewBox="7 7 22 22" aria-hidden="true" focusable="false"><path d="M18.1,28.3c-2,0-2.9-0.3-4.4-1.5c-1,1.3-4.2,2.3-4.3,0.6c0-1.3-0.3-2.4-0.6-3.6C8.4,22.4,8,20.8,8,18.4c0-5.7,4.7-10,10.2-10S28,13,28,18.4C27.9,23.9,23.6,28.3,18.1,28.3z M18.2,13.3c-2.7-0.1-4.8,1.7-5.2,4.7c-0.4,2.4,0.3,5.4,0.9,5.5c0.3,0.1,0.9-0.5,1.4-0.9c0.7,0.5,1.5,0.8,2.4,0.9c2.8,0.1,5.2-2,5.4-4.8C23.1,15.9,20.9,13.5,18.2,13.3L18.2,13.3z"/></svg>
					</a>
				</div>
			</div>
		</div>

		<div class="krv-services-landing-section">
			<div class="krv-landing-services" id="uslugi">
				<div class="krv-landing-services-header">
					<h2>Услуги</h2>
					<p>Индивидуальный подход и профессиональная реализация для вашего бизнеса</p>
				</div>

				<div class="krv-landing-services-grid">
					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline><line x1="14" y1="4" x2="10" y2="20"></line></svg>
						<h3>Веб-разработка</h3>
						<p>Создание и доработка сайтов на современных технологиях. Индивидуальные решения для любых задач.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="8" height="7" rx="1"></rect><rect x="13" y="4" width="8" height="7" rx="1"></rect><rect x="8" y="13" width="8" height="7" rx="1"></rect><line x1="7" y1="11" x2="12" y2="13"></line><line x1="17" y1="11" x2="12" y2="13"></line></svg>
						<h3>Интеграция сервисов в Docker</h3>
						<p>Создание и оптимизация контейнеров, упрощающих развертывание и масштабирование. Минимизация конфликтов окружения и ускорение разработки.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="6" rx="1"></rect><rect x="4" y="14" width="16" height="6" rx="1"></rect><circle cx="8" cy="7" r="0.8" style="fill:currentColor;stroke:none"></circle><circle cx="8" cy="17" r="0.8" style="fill:currentColor;stroke:none"></circle></svg>
						<h3>Настройка VPS</h3>
						<p>Установка и оптимизация виртуальных серверов под конкретные задачи. Гарантия стабильной и безопасной работы вашего проекта.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"></circle><circle cx="5" cy="12" r="2"></circle><circle cx="19" cy="12" r="2"></circle><circle cx="12" cy="5" r="2"></circle><circle cx="12" cy="19" r="2"></circle><line x1="7" y1="12" x2="10" y2="12"></line><line x1="14" y1="12" x2="17" y2="12"></line><line x1="12" y1="7" x2="12" y2="10"></line><line x1="12" y1="14" x2="12" y2="17"></line></svg>
						<h3>Настройка сетевых служб</h3>
						<p>Профессиональная конфигурация сетевых сервисов DNS, DHCP, VPN и других. Обеспечение высокой доступности и надёжности инфраструктуры.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18"></path><path d="M12 3a14 14 0 0 1 0 18"></path><path d="M12 3a14 14 0 0 0 0 18"></path></svg>
						<h3>Услуги по регистрации домена</h3>
						<p>Подбор оптимального доменного имени и помощь в регистрации. Сопровождение и поддержка DNS-записей для корректной работы вашего сайта.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18h10a4 4 0 0 0 .4-8A6 6 0 0 0 6 11a3.5 3.5 0 0 0 1 7Z"></path></svg>
						<h3>Настройки и миграция в облако</h3>
						<p>Анализ текущей инфраструктуры и безопасный перенос в облачные сервисы. Оптимизация ресурсов и снижение расходов на ИТ.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 3v5c0 4.5-2.9 8.1-7 10-4.1-1.9-7-5.5-7-10V6l7-3Z"></path><path d="M9.5 12.5l1.8 1.8 3.7-4.1"></path></svg>
						<h3>Безопасность сайта</h3>
						<p>Комплексные меры защиты: от регулярных аудитов и установки SSL до нейтрализации вредоносного кода и настройки систем обнаружения вторжений.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"></circle><line x1="12" y1="12" x2="16.5" y2="9.5"></line><line x1="12" y1="12" x2="12" y2="7"></line></svg>
						<h3>Оптимизация скорости сайта</h3>
						<p>Ускорение загрузки за счёт оптимизации кода, баз данных и изображений. Повышение показателей производительности и удобства для пользователей.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="7" height="5" rx="1"></rect><rect x="14" y="4" width="7" height="5" rx="1"></rect><rect x="14" y="15" width="7" height="5" rx="1"></rect><line x1="10" y1="8.5" x2="14" y2="6.5"></line><line x1="10" y1="8.5" x2="14" y2="17.5"></line></svg>
						<h3>Подключение к CDN</h3>
						<p>Интеграция с сетью доставки контента для быстрой загрузки и надёжности при высоких нагрузках. Настройка кэширования и балансировки.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="6" width="16" height="12" rx="2"></rect><path d="M8 10.5h8"></path><path d="M8 13.5h5"></path></svg>
						<h3>Контекстная реклама</h3>
						<p>Создание и управление кампаниями в поисковых системах и соцсетях. Анализ эффективности и оптимизация бюджета для увеличения конверсий.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1 1 0 0 0 .2 1.1l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1 1 0 0 0-1.1-.2 1 1 0 0 0-.6.9V20a2 2 0 1 1-4 0v-.2a1 1 0 0 0-.6-.9 1 1 0 0 0-1.1.2l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1 1 0 0 0 .2-1.1 1 1 0 0 0-.9-.6H4a2 2 0 1 1 0-4h.2a1 1 0 0 0 .9-.6 1 1 0 0 0-.2-1.1l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1 1 0 0 0 1.1.2 1 1 0 0 0 .6-.9V4a2 2 0 1 1 4 0v.2a1 1 0 0 0 .6.9 1 1 0 0 0 1.1-.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1 1 0 0 0-.2 1.1 1 1 0 0 0 .9.6H20a2 2 0 1 1 0 4h-.2a1 1 0 0 0-.9.6Z"></path></svg>
						<h3>Техническая поддержка сайта</h3>
						<p>Оперативное реагирование на любые сбои, плановые обновления и контроль стабильности. Гарантия бесперебойной работы вашего ресурса.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6"></circle><line x1="16" y1="16" x2="21" y2="21"></line></svg>
						<h3>SEO аудит сайта</h3>
						<p>Детальный анализ структуры, контента и технических параметров. Рекомендации по улучшению позиций сайта в поисковой выдаче.</p>
					</div>
				</div>
			</div>
		</div>

		<div class="krv-services-landing-section">
			<?php echo do_shortcode( '[krv_clients_grid]' ); ?>
		</div>

		<div class="krv-services-landing-section">
			<div class="krv-landing-pricing">
				<h2 class="krv-landing-pricing-title">Стоимость услуг</h2>

				<p class="krv-landing-pricing-lead">
					Индивидуальный подход к каждой задаче.<br>
					Моя базовая ставка:<br>
					<span class="krv-landing-pricing-rate">2000 ₽/час</span>
				</p>

				<ul class="krv-landing-pricing-list">
					<li>
						<svg class="krv-landing-pricing-icon" viewBox="0 0 24 24" aria-hidden="true">
							<line x1="12" y1="2" x2="12" y2="8"></line>
							<line x1="12" y1="16" x2="12" y2="22"></line>
							<line x1="4.93" y1="4.93" x2="9.17" y2="9.17"></line>
							<line x1="14.83" y1="14.83" x2="19.07" y2="19.07"></line>
							<line x1="2" y1="12" x2="8" y2="12"></line>
							<line x1="16" y1="12" x2="22" y2="12"></line>
							<line x1="4.93" y1="19.07" x2="9.17" y2="14.83"></line>
							<line x1="14.83" y1="9.17" x2="19.07" y2="4.93"></line>
						</svg>
						<span>Чем точнее описана задача, тем быстрее она будет выполнена.</span>
					</li>

					<li>
						<svg class="krv-landing-pricing-icon" viewBox="0 0 24 24" aria-hidden="true">
							<path d="M14 3h7v7"></path>
							<path d="M10 14L21 3"></path>
							<path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"></path>
						</svg>
						<span>Финальная стоимость зависит от сложности проекта и ваших ожиданий.</span>
					</li>

					<li>
						<svg class="krv-landing-pricing-icon" viewBox="0 0 24 24" aria-hidden="true">
							<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
						</svg>
						<span>Первичная консультация — бесплатно.</span>
					</li>
				</ul>

				<a class="krv-landing-pricing-button" href="https://krivoshein.site/contacts/" rel="noopener">
					Обсудить проект
				</a>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
} );

/** =========================
 *  10) Clients grid shortcode + styles + random
 *  ========================= */
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

/** =========================
 *  11) Partners grid shortcode
 *  ========================= */
add_shortcode( 'krv_partners_grid', function ( $atts = [] ) {
	$atts = shortcode_atts( [
		'category' => '',
	], $atts, 'krv_partners_grid' );

	$terms_args = [
		'taxonomy'   => 'partner_category',
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	];

	if ( $atts['category'] !== '' ) {
		$terms_args['slug'] = sanitize_title( $atts['category'] );
	}

	$terms = get_terms( $terms_args );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="krv-partners-wrap">
		<div class="krv-partners-header">
			<h2>Партнёры</h2>
			<p>Полезные сервисы, хостинги, инструменты и платформы, которые я использую или могу рекомендовать.</p>
		</div>

		<?php foreach ( $terms as $term ) : ?>
			<?php
			$q = new WP_Query( [
				'post_type'      => 'partner',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => [
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				],
				'tax_query'      => [
					[
						'taxonomy' => 'partner_category',
						'field'    => 'term_id',
						'terms'    => $term->term_id,
					],
				],
				'no_found_rows'  => true,
			] );

			if ( ! $q->have_posts() ) {
				continue;
			}
			?>

			<section class="krv-partners-group">
				<h3 class="krv-partners-group-title"><?php echo esc_html( $term->name ); ?></h3>

				<div class="krv-partners-grid">
					<?php while ( $q->have_posts() ) : $q->the_post(); ?>
						<?php
						$post_id      = get_the_ID();
						$title        = get_the_title();
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
							'alt'     => esc_attr( $title ),
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
					<?php endwhile; ?>
				</div>
			</section>

			<?php wp_reset_postdata(); ?>
		<?php endforeach; ?>
	</div>
	<?php

	return ob_get_clean();
} );

add_action( 'wp_head', function () {
	if ( is_admin() || ! krv_page_has_ui_shortcode( [ 'krv_partners_grid' ] ) ) {
		return;
	}
	?>
<style>
	.krv-partners-wrap {
		--krv-accent: #5181fe;
		--krv-accent-hover: #4169d4;
		--krv-card-bg: #fff;
		--krv-text-main: #333;
		--krv-text-soft: #666;
		--krv-card-radius: 10px;
		--krv-card-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
		--krv-card-shadow-hover: 0 6px 15px rgba(0, 0, 0, 0.15);
		max-width: 1320px;
		margin: 0 auto;
		padding: 20px 15px;
		font-family: "Poppins", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
	}

	.krv-partners-wrap,
	.krv-partners-wrap * {
		box-sizing: border-box;
	}

	.krv-partners-header {
		text-align: center;
		margin-bottom: 30px;
	}

	.krv-partners-header h2 {
		margin: 0 0 12px;
		font-size: 2.2rem;
		line-height: 1.2;
		color: var(--krv-text-main);
	}

	.krv-partners-header p {
		margin: 0 auto;
		max-width: 760px;
		font-size: 1.02rem;
		line-height: 1.7;
		color: var(--krv-text-soft);
	}

	.krv-partners-group + .krv-partners-group {
		margin-top: 36px;
	}

	.krv-partners-group-title {
		margin: 0 0 18px;
		font-size: 1.6rem;
		line-height: 1.3;
		color: var(--krv-text-main);
		text-align: center;
	}

	.krv-partners-grid {
		display: grid;
		grid-template-columns: repeat(4, minmax(0, 1fr));
		gap: 20px;
		width: 100%;
	}

	.krv-partner-card {
		display: block;
		width: 100%;
		height: 100%;
		text-decoration: none;
		color: inherit;
		background: var(--krv-card-bg);
		border: 1px solid var(--krv-accent);
		border-radius: var(--krv-card-radius);
		box-shadow: var(--krv-card-shadow);
		transition: box-shadow 0.25s ease, transform 0.25s ease, border-color 0.25s ease, background 0.25s ease;
		overflow: hidden;
	}

	.krv-partner-card:hover {
		transform: translateY(-2px);
		border-color: var(--krv-accent-hover);
		box-shadow: var(--krv-card-shadow-hover);
		background: #fcfdff;
	}

	.krv-partner-card--featured {
		border-color: var(--krv-accent);
		box-shadow: 0 6px 18px rgba(81, 129, 254, 0.14);
	}

	.krv-partner-card--static:hover {
		transform: none;
		border-color: var(--krv-accent);
		box-shadow: var(--krv-card-shadow);
		background: var(--krv-card-bg);
	}

	.krv-partner-card-inner {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: flex-start;
		min-height: 228px;
		padding: 16px 14px;
		text-align: center;
	}

	.krv-partner-logo-wrap {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 100%;
		height: 82px;
		margin-bottom: 14px;
		padding: 8px;
		overflow: hidden;
	}

	.krv-partner-logo {
		display: block !important;
		width: auto !important;
		height: auto !important;
		max-width: 100% !important;
		max-height: 66px !important;
		margin: 0 auto !important;
		object-fit: contain;
		object-position: center;
		border: 0 !important;
		box-shadow: none !important;
	}

	.krv-partner-no-logo {
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

	.krv-partner-badge {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		margin: 0 0 10px;
		padding: 4px 10px;
		border-radius: 999px;
		background: #eef4ff;
		color: var(--krv-accent-hover);
		font-size: 0.76rem;
		font-weight: 600;
		line-height: 1.2;
	}

	.krv-partner-title {
		margin: 0;
		font-size: 1rem;
		line-height: 1.35;
		color: var(--krv-text-main);
		word-break: break-word;
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
		overflow: hidden;
		min-height: 2.7em;
	}

	.krv-partner-description {
		margin: 10px 0 0;
		font-size: 0.86rem;
		line-height: 1.45;
		color: var(--krv-text-soft);
		display: -webkit-box;
		-webkit-line-clamp: 3;
		-webkit-box-orient: vertical;
		overflow: hidden;
		min-height: 4.1em;
	}

	@media (max-width: 1180px) {
		.krv-partners-wrap {
			max-width: 1180px;
		}

		.krv-partners-grid {
			grid-template-columns: repeat(3, minmax(0, 1fr));
		}
	}

	@media (max-width: 768px) {
		.krv-partners-wrap {
			padding: 14px 8px;
		}

		.krv-partners-header {
			margin-bottom: 24px;
		}

		.krv-partners-header h2 {
			font-size: 1.9rem;
		}

		.krv-partners-header p {
			font-size: 0.95rem;
		}

		.krv-partners-group-title {
			font-size: 1.3rem;
			margin-bottom: 14px;
			text-align: center;
		}

		.krv-partners-grid {
			grid-template-columns: repeat(2, minmax(0, 1fr));
			gap: 12px;
		}

		.krv-partner-card-inner {
			min-height: 182px;
			padding: 10px 8px;
		}

		.krv-partner-logo-wrap {
			height: 58px;
			margin-bottom: 10px;
			padding: 4px;
		}

		.krv-partner-logo {
			max-height: 44px !important;
		}

		.krv-partner-title {
			font-size: 0.88rem;
			min-height: 2.5em;
		}

		.krv-partner-description {
			display: none;
		}
	}

	@media (max-width: 380px) {
		.krv-partners-wrap {
			padding: 10px 5px;
		}

		.krv-partners-header h2 {
			font-size: 1.55rem;
		}

		.krv-partners-header p {
			font-size: 0.9rem;
		}

		.krv-partners-grid {
			grid-template-columns: repeat(2, minmax(0, 1fr));
			gap: 10px;
		}

		.krv-partner-card-inner {
			min-height: 168px;
			padding: 9px 7px;
		}

		.krv-partner-logo-wrap {
			height: 52px;
		}

		.krv-partner-logo {
			max-height: 40px !important;
		}

		.krv-partner-title {
			font-size: 0.82rem;
		}
	}
</style>
	<?php
}, 101 );

/** =========================
 *  12) Services pages showcase
 *  ========================= */
add_shortcode( 'krv_services_pages_showcase', function () {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	$sections = get_field( 'krv_services_sections', 'option' );

	if ( empty( $sections ) || ! is_array( $sections ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="krv-service-pages-wrap">
		<?php foreach ( $sections as $section ) : ?>
			<?php
			$section_title = isset( $section['section_title'] ) ? trim( (string) $section['section_title'] ) : '';
			$page_ids      = isset( $section['section_pages'] ) && is_array( $section['section_pages'] ) ? $section['section_pages'] : [];

			if ( empty( $page_ids ) ) {
				continue;
			}
			?>
			<section class="krv-service-pages-group">
				<?php if ( $section_title !== '' ) : ?>
					<h2 class="krv-service-pages-group-title"><?php echo esc_html( $section_title ); ?></h2>
				<?php endif; ?>

				<div class="krv-service-pages-grid">
					<?php foreach ( $page_ids as $page_id ) : ?>
						<?php
						$page = get_post( $page_id );

						if ( ! $page || $page->post_status !== 'publish' ) {
							continue;
						}

						$title = get_the_title( $page_id );
						$url   = get_permalink( $page_id );

						$description = trim( wp_strip_all_tags( get_the_excerpt( $page_id ) ) );
						if ( $description === '' ) {
							$description = wp_trim_words(
								wp_strip_all_tags( strip_shortcodes( (string) $page->post_content ) ),
								18,
								'…'
							);
						}

						$thumb = get_the_post_thumbnail( $page_id, 'medium_large', [
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