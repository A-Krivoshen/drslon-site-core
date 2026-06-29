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
		'embed'            => '1',
		'discussion'       => '1',
		'page_url'         => $page_url,
		'comments_limit'   => '30',
		'color'            => '5282FF',
		'dark'             => '0',
	);

	return trailingslashit( KRV_TG_PROXY_ORIGIN ) . KRV_TG_DISCUSSION . '?' . http_build_query( $query, '', '&', PHP_QUERY_RFC3986 );
}

/**
 * Render Telegram discussion iframe (no external widget script required).
 */
function krv_render_telegram_discussion_widget(): void {
	$channel_slug = preg_replace( '/[^a-z0-9_]/i', '-', KRV_TG_DISCUSSION );
	$iframe_id    = 'telegram-discussion-' . $channel_slug . '-1';
	$embed_url    = krv_tg_discussion_embed_url();
	$proxy_origin = KRV_TG_PROXY_ORIGIN;
	?>
	<iframe
		id="<?php echo esc_attr( $iframe_id ); ?>"
		src="<?php echo esc_url( $embed_url ); ?>"
		width="100%"
		height="120"
		frameborder="0"
		scrolling="no"
		referrerpolicy="strict-origin-when-cross-origin"
		title="<?php echo esc_attr__( 'Комментарии Telegram', 'drslon-site-core' ); ?>"
		style="border:none;min-width:320px;width:100%;overflow:hidden;color-scheme:light dark;"
	></iframe>
	<script>
	(function () {
		var origin = <?php echo wp_json_encode( $proxy_origin ); ?>;
		var iframe = document.getElementById(<?php echo wp_json_encode( $iframe_id ); ?>);
		if (!iframe) return;

		window.addEventListener('message', function (event) {
			if (event.origin !== origin) return;
			if (event.source !== iframe.contentWindow) return;
			try {
				var data = JSON.parse(event.data);
				if (data.event === 'resize') {
					if (data.height) iframe.style.height = data.height + 'px';
					if (data.width) iframe.style.width = data.width + 'px';
				}
			} catch (e) {}
		});
	})();
	</script>
	<?php
}

/**
 * Fallback links when the iframe stays empty (blocked network, embed error).
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
			var iframe = wrap.querySelector('iframe');
			if (!iframe) return;
			if (iframe.offsetHeight > 80) return;

			if (wrap.querySelector('.krv-tg-fallback')) return;

			var fallback = document.createElement('div');
			fallback.className = 'krv-tg-fallback';
			fallback.style.cssText = 'margin-top:12px;padding:16px 18px;border:1px solid #e5e7eb;border-radius:12px;background:#f9fafb;';
			fallback.innerHTML = <?php echo wp_json_encode( $fallback_html ); ?>;
			wrap.appendChild(fallback);
		}, 12000);
	})();
	</script>
	<?php
}

add_action( 'wp_footer', 'krv_tg_comments_fallback_script', 50 );