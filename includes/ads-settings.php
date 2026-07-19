<?php
/**
 * Yandex RSYA ad settings: defaults, option getters,
 * and admin page Настройки → Реклама РСЯ.
 *
 * The KRV_RSYA_* constants are the fallback defaults; the actual values
 * are managed in the admin page and stored as options.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KRV_RSYA_RECO_BLOCK_ID', 'C-A-19616264-12' );
define( 'KRV_RSYA_RECO_RENDER_TO', 'yandex_rtb_C-A-19616264-12' );

/**
 * Option getters (Настройки → Реклама РСЯ).
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

/**
 * Optional raw Yandex code that overrides the generated render script.
 * Only manage_options users can set it; stored as-is (no KSES stripping).
 */
function krv_rsya_reco_code(): string {
	$code = trim( (string) get_option( 'krv_rsya_reco_code', '' ) );
	return $code;
}

/**
 * Sanitize the raw ad code: trim only, preserve HTML and <script> tags.
 * Access is restricted to manage_options via the settings page capability.
 *
 * @param string $value Raw user input.
 */
function krv_sanitize_rsya_code( $value ): string {
	return trim( (string) $value );
}

add_action( 'admin_init', function () {
	register_setting( 'krv_ads', 'krv_rsya_reco_enabled', [
		'type'              => 'integer',
		'default'           => 1,
		'sanitize_callback' => 'absint',
	] );

	register_setting( 'krv_ads', 'krv_rsya_reco_block_id', [
		'type'              => 'string',
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	] );

	register_setting( 'krv_ads', 'krv_rsya_reco_code', [
		'type'              => 'string',
		'default'           => '',
		'sanitize_callback' => 'krv_sanitize_rsya_code',
	] );
} );

add_action( 'admin_menu', function () {
	add_options_page(
		'Реклама РСЯ',
		'Реклама РСЯ',
		'manage_options',
		'krv-ads-settings',
		'krv_ads_settings_page'
	);
} );

function krv_ads_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<form method="post" action="options.php">
			<?php settings_fields( 'krv_ads' ); ?>

			<h2>Рекомендательный виджет (после поста)</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">Включён</th>
					<td>
						<label>
							<input type="checkbox" name="krv_rsya_reco_enabled" value="1" <?php checked( krv_rsya_reco_enabled() ); ?>>
							Показывать рекомендательный блок под постами
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="krv_rsya_reco_block_id">ID блока</label>
					</th>
					<td>
						<input type="text" id="krv_rsya_reco_block_id" name="krv_rsya_reco_block_id"
							value="<?php echo esc_attr( get_option( 'krv_rsya_reco_block_id', '' ) ); ?>"
							placeholder="<?php echo esc_attr( KRV_RSYA_RECO_BLOCK_ID ); ?>"
							class="regular-text">
						<p class="description">Например, <?php echo esc_html( KRV_RSYA_RECO_BLOCK_ID ); ?>. Пусто = значение по умолчанию.</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="krv_rsya_reco_code">Код виджета (опционально)</label>
					</th>
					<td>
						<textarea id="krv_rsya_reco_code" name="krv_rsya_reco_code"
							rows="10" cols="80" class="large-text code"
							placeholder="<!-- Yandex.RTB ... -->&#10;&lt;div id=&quot;yandex_rtb_...&quot;&gt;&lt;/div&gt;&#10;&lt;script&gt;window.yaContextCb.push(()=&gt;{&#10;  Ya.Context.AdvManager.renderWidget({&#10;    renderTo: '...',&#10;    blockId: '...'&#10;  })&#10;})&lt;/script&gt;"><?php echo esc_textarea( krv_rsya_reco_code() ); ?></textarea>
						<p class="description">
							Вставьте полный код виджета из кабинета РСЯ — он выведется как есть.<br>
							Пусто = код генерируется автоматически из ID блока выше.
						</p>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
