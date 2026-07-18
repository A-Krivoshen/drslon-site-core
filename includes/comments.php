<?php
/**
 * Telegram comments + post extras renderer, telegram meta,
 * built-in comments disabled.
 * Extracted from legacy-arkai-child-functions.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** =========================
 *  Telegram comments + RSYA block renderer
 *  IMPORTANT: called manually from the theme (drslon_post_extras shortcode)
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

		<?php if ( krv_rsya_reco_enabled() ) : ?>
			<div class="krv-rsya-reco" style="clear:both;display:block;width:100%;margin-top:24px;">
				<div id="<?php echo esc_attr( krv_rsya_reco_render_to() ); ?>"></div>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/** Telegram meta */
add_action( 'wp_head', function () {
	if ( is_singular( 'post' ) || is_singular( 'project' ) ) {
		echo '<meta property="telegram:channel" content="@' . esc_attr( KRV_TG_DISCUSSION ) . '">' . "\n";
	}
}, 50 );

/** Disable built-in comments */
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );
add_filter( 'get_comments_number', '__return_zero' );
