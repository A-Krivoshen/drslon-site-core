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

/**
 * Script URL for the proxied Telegram discussion widget.
 */
function krv_tg_widget_script_url(): string {
	return trailingslashit( KRV_TG_PROXY_ORIGIN ) . 'js/telegram-widget.js?21';
}