<?php
/**
 * Telegram Discussion Widget via same-origin reverse proxy (nginx telegram-proxy.conf).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'KRV_TG_PROXY_ORIGIN' ) ) {
	define( 'KRV_TG_PROXY_ORIGIN', 'https://tg.krivoshein.site' );
}

/**
 * Script URL for the proxied Telegram discussion widget.
 */
function krv_tg_widget_script_url(): string {
	return trailingslashit( KRV_TG_PROXY_ORIGIN ) . 'js/telegram-widget.js?21';
}

/**
 * Fallback when the widget iframe does not render (blocked network, embed error).
 */
function krv_tg_comments_fallback_script(): void {
	if ( ! krv_is_single_content() ) {
		return;
	}
	?>
	<script>
	(function () {
		var wrap = document.getElementById('telegram-comments');
		if (!wrap) return;

		window.setTimeout(function () {
			var iframe = wrap.querySelector('iframe');
			if (iframe && iframe.offsetHeight > 80) return;

			var fallback = document.createElement('div');
			fallback.className = 'krv-tg-fallback';
			fallback.style.cssText = 'padding:16px 18px;border:1px solid #e5e7eb;border-radius:12px;background:#f9fafb;';
			fallback.innerHTML = <?php echo wp_json_encode(
				sprintf(
					'<p style="margin:0 0 10px;font-weight:600;">%s</p><p style="margin:0;"><a href="%s" target="_blank" rel="noopener noreferrer">%s</a></p>',
					'Комментарии обсуждаются в Telegram-канале.',
					esc_url( 'https://t.me/' . KRV_TG_DISCUSSION ),
					'@' . KRV_TG_DISCUSSION
				)
			); ?>;

			if (iframe) {
				iframe.style.display = 'none';
			}
			wrap.appendChild(fallback);
		}, 9000);
	})();
	</script>
	<?php
}

add_action( 'wp_footer', 'krv_tg_comments_fallback_script', 50 );