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
 * Script URL for the proxied Telegram discussion widget.
 */
function krv_tg_widget_script_url(): string {
	return trailingslashit( KRV_TG_PROXY_ORIGIN ) . 'js/telegram-widget.js?21';
}

/**
 * Fallback links when the Telegram widget iframe never appears.
 */
function krv_tg_comments_fallback_script(): void {
	if ( ! function_exists( 'krv_is_single_content' ) || ! krv_is_single_content() ) {
		return;
	}

	$fallback_html = sprintf(
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
	?>
	<script>
	(function () {
		var wrap = document.getElementById('telegram-comments');
		if (!wrap) return;

		window.setTimeout(function () {
			if (wrap.querySelector('iframe')) return;

			var fallback = document.createElement('div');
			fallback.className = 'krv-tg-fallback';
			fallback.style.cssText = 'padding:16px 18px;border:1px solid #e5e7eb;border-radius:12px;background:#f9fafb;';
			fallback.innerHTML = <?php echo wp_json_encode( $fallback_html ); ?>;
			wrap.appendChild(fallback);
		}, 12000);
	})();
	</script>
	<?php
}

add_action( 'wp_footer', 'krv_tg_comments_fallback_script', 50 );