<?php
/**
 * Yandex RSYA ads: loader, recommendation metatags, reco widget, InImage.
 * Extracted from legacy-arkai-child-functions.php
 *
 * Note: the dropcap script from the old theme is intentionally disabled.
 * The legacy script modified the first paragraph inside .entry-content.
 * In the block theme it also affects shortcode landing sections, so pages
 * get a wrong decorative first letter.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
