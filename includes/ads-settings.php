<?php
/**
 * Admin settings page: Настройки → Реклама РСЯ.
 * Toggle and block IDs for Yandex RSYA widgets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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

	register_setting( 'krv_ads', 'krv_rsya_inimage_enabled', [
		'type'              => 'integer',
		'default'           => 0,
		'sanitize_callback' => 'absint',
	] );

	register_setting( 'krv_ads', 'krv_rsya_inimage_block_id', [
		'type'              => 'string',
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
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
						<p class="description">Например, C-A-6903522-1. Пусто = значение по умолчанию из кода.</p>
					</td>
				</tr>
			</table>

			<h2>InImage (реклама поверх изображений)</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">Включён</th>
					<td>
						<label>
							<input type="checkbox" name="krv_rsya_inimage_enabled" value="1" <?php checked( krv_rsya_inimage_enabled() ); ?>>
							Показывать InImage-рекламу на картинках в постах
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="krv_rsya_inimage_block_id">ID блока</label>
					</th>
					<td>
						<input type="text" id="krv_rsya_inimage_block_id" name="krv_rsya_inimage_block_id"
							value="<?php echo esc_attr( get_option( 'krv_rsya_inimage_block_id', '' ) ); ?>"
							placeholder="<?php echo esc_attr( KRV_RSYA_INIMAGE_BLOCK_ID ); ?>"
							class="regular-text">
						<p class="description">Например, R-A-6903522-2. Пусто = значение по умолчанию из кода.</p>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
