<?php
/**
 * Telegram Discussion Widget via /tg/ reverse proxy on krivoshein.site.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'KRV_MAX_DISCUSSION_URL' ) ) {
	define( 'KRV_MAX_DISCUSSION_URL', 'https://max.ru/join/m0x_nGGpbnSFDPLvSXZWIksmgZaf13bzKvNIBaqkz78' );
}

/**
 * Base URL for the on-domain Telegram proxy (no separate subdomain).
 */
function krv_tg_proxy_origin(): string {
	return untrailingslashit( home_url( '/tg' ) );
}

/**
 * postMessage origin for resize events (scheme + host only).
 */
function krv_tg_post_message_origin(): string {
	$parts = wp_parse_url( home_url() );

	if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) ) {
		return 'https://krivoshein.site';
	}

	$origin = $parts['scheme'] . '://' . $parts['host'];

	if ( ! empty( $parts['port'] ) ) {
		$origin .= ':' . $parts['port'];
	}

	return $origin;
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

	return krv_tg_proxy_origin() . '/' . KRV_TG_DISCUSSION . '?' . http_build_query( $query, '', '&', PHP_QUERY_RFC3986 );
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
	krv_render_tg_discuss_links();
}

/**
 * Messenger links shown under the read-only embed (no on-site OAuth).
 */
function krv_tg_discuss_links_html(): string {
	return sprintf(
		'<span class="krv-tg-discuss-links__label">%s</span>'
		. '<a class="krv-tg-discuss-links__link" href="%s" target="_blank" rel="noopener noreferrer">Telegram — @%s</a>'
		. '<span class="krv-tg-discuss-links__sep" aria-hidden="true">/</span>'
		. '<a class="krv-tg-discuss-links__link" href="%s" target="_blank" rel="noopener noreferrer">MAX — группа обсуждения</a>',
		'Обсудить в',
		esc_url( 'https://t.me/' . KRV_TG_DISCUSSION ),
		esc_html( KRV_TG_DISCUSSION ),
		esc_url( KRV_MAX_DISCUSSION_URL )
	);
}

/**
 * Persistent block under the Telegram embed.
 */
function krv_render_tg_discuss_links(): void {
	?>
	<div class="krv-tg-discuss-links">
		<?php echo krv_tg_discuss_links_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
	</div>
	<?php
}

/**
 * Inline layout for messenger discuss links.
 */
function krv_tg_discuss_links_styles(): void {
	if ( ! function_exists( 'krv_is_single_content' ) || ! krv_is_single_content() ) {
		return;
	}
	?>
	<style>
		.krv-tg-discuss-links {
			display: flex;
			flex-wrap: wrap;
			align-items: center;
			gap: 6px 10px;
			margin-top: 12px;
			padding: 11px 16px;
			border: 1px solid #e5e7eb;
			border-radius: 10px;
			background: #f9fafb;
			font-size: 14px;
			line-height: 1.45;
			color: #374151;
		}
		.krv-tg-discuss-links__label {
			font-weight: 600;
			white-space: nowrap;
		}
		.krv-tg-discuss-links__sep {
			color: #9ca3af;
			user-select: none;
		}
		.krv-tg-discuss-links__link {
			color: #2563eb;
			text-decoration: none;
			white-space: nowrap;
		}
		.krv-tg-discuss-links__link:hover {
			text-decoration: underline;
		}
		@media (max-width: 640px) {
			.krv-tg-discuss-links__link {
				white-space: normal;
			}
		}
	</style>
	<?php
}

add_action( 'wp_head', 'krv_tg_discuss_links_styles', 16 );

/**
 * Short notice when the embed iframe fails to load.
 */
function krv_tg_embed_error_html(): string {
	return '<p style="margin:0;color:#6b7280;font-size:14px;">Комментарии не загрузились. Обсудите статью в мессенджерах ниже.</p>';
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
		var origin = <?php echo wp_json_encode( krv_tg_post_message_origin() ); ?>;
		var errorHtml = <?php echo wp_json_encode( krv_tg_embed_error_html() ); ?>;
		var mounted = false;
		var iframe = null;

		function showEmbedError() {
			if (wrap.querySelector('.krv-tg-embed-error')) return;
			var el = document.createElement('div');
			el.className = 'krv-tg-embed-error';
			el.style.cssText = 'margin-top:8px;padding:12px 14px;border:1px solid #fde68a;border-radius:8px;background:#fffbeb;';
			el.innerHTML = errorHtml;
			var discuss = wrap.querySelector('.krv-tg-discuss-links');
			if (discuss) {
				wrap.insertBefore(el, discuss);
			} else {
				wrap.appendChild(el);
			}
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
				if (!iframe || iframe.offsetHeight <= 80) showEmbedError();
			}, 20000);

			function hideEmbedLogin(doc) {
				if (!doc) return;

				var css = '.tgme_post_discussion_login,.tgme_post_discussion_login_btn,.js-login_btn{display:none!important;height:0!important;margin:0!important;padding:0!important;overflow:hidden!important;visibility:hidden!important;pointer-events:none!important}';

				if (!doc.getElementById('krv-hide-tg-login')) {
					var style = doc.createElement('style');
					style.id = 'krv-hide-tg-login';
					style.textContent = css;
					(doc.head || doc.documentElement).appendChild(style);
				}

				doc.querySelectorAll('.tgme_post_discussion_login,.js-login_btn').forEach(function (el) {
					el.remove();
				});
			}

			iframe.addEventListener('load', function () {
				window.clearTimeout(failTimer);

				try {
					var doc = iframe.contentDocument;
					if (!doc) return;

					hideEmbedLogin(doc);

					var loginObserver = new MutationObserver(function () {
						hideEmbedLogin(doc);
					});

					if (doc.body) {
						loginObserver.observe(doc.body, { childList: true, subtree: true });
					}
				} catch (e) {}
			});

			iframe.addEventListener('error', function () {
				window.clearTimeout(failTimer);
				showEmbedError();
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
			var ioStarted = false;

			if ('IntersectionObserver' in window) {
				ioStarted = true;
				var observer = new IntersectionObserver(function (entries) {
					entries.forEach(function (entry) {
						if (!entry.isIntersecting) return;
						observer.disconnect();
						mountIframe();
					});
				}, { rootMargin: '320px 0px' });
				observer.observe(slot);
			}

			window.addEventListener('load', function () {
				window.setTimeout(function () {
					if (!mounted) mountIframe();
				}, ioStarted ? 2500 : 600);
			});
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