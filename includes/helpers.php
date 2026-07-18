<?php
/**
 * Shared constants and helpers.
 * Extracted from legacy-arkai-child-functions.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** =========================
 *  SETTINGS
 *  ========================= */
define( 'KRV_TG_DISCUSSION', 'drslon_channel' );

define( 'KRV_RSYA_RECO_BLOCK_ID', 'C-A-9861013-1' );
define( 'KRV_RSYA_RECO_RENDER_TO', 'yandex_rtb_C-A-9861013-1' );

define( 'KRV_RSYA_INIMAGE_BLOCK_ID', 'R-A-6903522-2' );

/** =========================
 *  HELPERS
 *  ========================= */

/**
 * Ad settings (Настройки → Реклама РСЯ).
 * Options override the constants above; defaults keep legacy behavior,
 * except InImage which is disabled by default.
 */
function krv_rsya_reco_enabled(): bool {
	return (bool) get_option( 'krv_rsya_reco_enabled', 1 );
}

function krv_rsya_reco_block_id(): string {
	$id = trim( (string) get_option( 'krv_rsya_reco_block_id', '' ) );
	return $id !== '' ? $id : KRV_RSYA_RECO_BLOCK_ID;
}

function krv_rsya_reco_render_to(): string {
	return 'yandex_rtb_' . krv_rsya_reco_block_id();
}

function krv_rsya_inimage_enabled(): bool {
	return (bool) get_option( 'krv_rsya_inimage_enabled', 0 );
}

function krv_rsya_inimage_block_id(): string {
	$id = trim( (string) get_option( 'krv_rsya_inimage_block_id', '' ) );
	return $id !== '' ? $id : KRV_RSYA_INIMAGE_BLOCK_ID;
}

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
