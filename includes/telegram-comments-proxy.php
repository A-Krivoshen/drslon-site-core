<?php
/**
 * Telegram Discussion Widget via tg.krivoshein.site reverse proxy.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'KRV_TG_PROXY_ORIGIN' ) ) {
	define( 'KRV_TG_PROXY_ORIGIN', 'https://tg.krivoshein.site' );
}

if ( ! defined( 'KRV_MAX_DISCUSSION_URL' ) ) {
	define( 'KRV_MAX_DISCUSSION_URL', 'https://max.ru/join/m0x_nGGpbnSFDPLvSXZWIksmgZaf13bzKvNIBaqkz78' );
}

/**
 * Build proxied Telegram discussion embed URL.
 */
function krv_tg_discussion_embed_url( ?string $page_url = null ): string {
	$page_url = $page_url ?: get_permalink();

	$query = array(
		'embed'          => '1',
		'discussion'     => '1',
		'page_url'       => $page_url,
		'comments_limit' => '30',
		'color'          => '5282FF',
		'dark'           => '0',
	);

	return trailingslashit( KRV_TG_PROXY_ORIGIN ) . KRV_TG_DISCUSSION . '?' . http_build_query( $query, '', '&', PHP_QUERY_RFC3986 );
}

/**
 * Placeholder slot — iframe is injected later so it does not block page load.
 */
function krv_render_telegram_discussion_widget(): void {
	$channel_slug = preg_replace( '/[^a-z0-9_]/i', '-', KRV_TG_DISCUSSION );
	$slot_id      = 'telegram-discussion-' . $channel_slug . '-slot';
	?>
	<div
		id="<?php echo esc_attr( $slot_id ); ?>"
		class="krv-tg-embed-slot"
		data-embed-url="<?php echo esc_url( krv_tg_discussion_embed_url() ); ?>"
		data-iframe-id="<?php echo esc_attr( 'telegram-discussion-' . $channel_slug . '-1' ); ?>"
		style="min-height:120px;position:relative;"
	>
		<div class="krv-tg-embed-loading" style="padding:18px 16px;color:#6b7280;font-size:14px;">
			Комментарии загружаются…
		</div>
	</div>
	<?php
}

/**
 * Build fallback HTML for messenger links.
 */
function krv_tg_fallback_html(): string {
	return sprintf(
		'<p style="margin:0 0 10px;font-weight:600;">%s</p>'
		. '<p style="margin:0 0 8px;">%s <a href="%s" target="_blank" rel="noopener noreferrer">@%s</a></p>'
		. '<p style="margin:0;">%s <a href="%s" target="_blank" rel="noopener noreferrer">%s</a></p>',
		'Обсуждение также доступно в мессенджерах:',
		'Telegram:',
		esc_url( 'https://t.me/' . KRV_TG_DISCUSSION ),
		esc_html( KRV_TG_DISCUSSION ),
		'MAX:',
		esc_url( KRV_MAX_DISCUSSION_URL ),
		'группа обсуждения'
	);
}

/**
 * Deferred iframe loader — does not keep the browser tab spinner active.
 */
function krv_tg_comments_loader_script(): void {
	if ( ! function_exists( 'krv_is_single_content' ) || ! krv_is_single_content() ) {
		return;
	}
	?>
	<script>
	(function () {
		var wrap = document.getElementById('telegram-comments');
		if (!wrap) return;

		var slot = wrap.querySelector('.krv-tg-embed-slot');
		if (!slot || slot.dataset.krvMounted === '1') return;

		var embedUrl = slot.getAttribute('data-embed-url');
		var iframeId = slot.getAttribute('data-iframe-id');
		var origin = <?php echo wp_json_encode( KRV_TG_PROXY_ORIGIN ); ?>;
		var fallbackHtml = <?php echo wp_json_encode( krv_tg_fallback_html() ); ?>;
		var mounted = false;
		var iframe = null;

		function showFallback() {
			if (wrap.querySelector('.krv-tg-fallback')) return;
			var el = document.createElement('div');
			el.className = 'krv-tg-fallback';
			el.style.cssText = 'margin-top:12px;padding:16px 18px;border:1px solid #e5e7eb;border-radius:12px;background:#f9fafb;';
			el.innerHTML = fallbackHtml;
			wrap.appendChild(el);
		}

		function mountIframe() {
			if (mounted || !embedUrl) return;
			mounted = true;
			slot.dataset.krvMounted = '1';

			var loading = slot.querySelector('.krv-tg-embed-loading');
			if (loading) loading.remove();

			iframe = document.createElement('iframe');
			iframe.id = iframeId;
			iframe.src = embedUrl;
			iframe.width = '100%';
			iframe.height = '120';
			iframe.setAttribute('frameborder', '0');
			iframe.setAttribute('scrolling', 'no');
			iframe.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
			iframe.setAttribute('title', 'Комментарии Telegram');
			iframe.style.cssText = 'border:none;min-width:320px;width:100%;overflow:hidden;color-scheme:light dark;';

			var failTimer = window.setTimeout(function () {
				if (!iframe || iframe.offsetHeight <= 80) showFallback();
			}, 15000);

			iframe.addEventListener('load', function () {
				window.clearTimeout(failTimer);
			});

			slot.appendChild(iframe);
		}

		window.addEventListener('message', function (event) {
			if (!iframe || event.origin !== origin) return;
			if (event.source !== iframe.contentWindow) return;
			try {
				var data = JSON.parse(event.data);
				if (data.event === 'resize') {
					if (data.height) iframe.style.height = data.height + 'px';
					if (data.width) iframe.style.width = data.width + 'px';
				}
			} catch (e) {}
		});

		function scheduleMount() {
			if ('IntersectionObserver' in window) {
				var observer = new IntersectionObserver(function (entries) {
					entries.forEach(function (entry) {
						if (!entry.isIntersecting) return;
						observer.disconnect();
						mountIframe();
					});
				}, { rootMargin: '240px 0px' });
				observer.observe(slot);
				return;
			}
			if (document.readyState === 'complete') {
				window.setTimeout(mountIframe, 400);
			} else {
				window.addEventListener('load', function () {
					window.setTimeout(mountIframe, 400);
				});
			}
		}

		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', scheduleMount);
		} else {
			scheduleMount();
		}
	})();
	</script>
	<?php
}

add_action( 'wp_footer', 'krv_tg_comments_loader_script', 50 );