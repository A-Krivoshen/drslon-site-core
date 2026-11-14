<?php
/** Browser-only contact composer. No endpoint or persistent personal data. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function krv_contact_brief_markup( bool $en = false ): string {
	ob_start();
	?>
	<section id="brief" class="krv-services-landing-section" aria-label="<?php echo $en ? 'Contact and brief' : 'Связь и короткий бриф'; ?>">
		<div class="krv-qual" data-krv-brief data-service="site" data-counter="51907757">
			<h2 data-krv-text="title"><?php echo $en ? 'Tell me about your task' : 'Коротко расскажите о задаче'; ?></h2>
			<p data-krv-text="lead"><?php echo $en ? 'No technical terms needed. Contact me directly or prepare a message below.' : 'Можно без технических терминов. Напишите сразу или подготовьте текст ниже.'; ?></p>
			<div class="krv-brief-channels">
				<a class="krv-brief-channel" href="https://t.me/DrSlon" target="_blank" rel="noopener noreferrer">Telegram</a>
				<a class="krv-brief-channel" href="https://krivoshein.site/max" target="_blank" rel="noopener noreferrer">MAX</a>
			</div>
			<p><a href="mailto:aleksey@krivoshein.site">aleksey@krivoshein.site</a> · <a href="tel:+79636641615">+7 (963) 664-16-15</a></p>
			<label for="krv-main-need" data-krv-text="need"><?php echo $en ? 'What do you need?' : 'Что требуется?'; ?></label>
			<select id="krv-main-need"><option><?php echo $en ? 'Discuss a task' : 'Обсудить задачу'; ?></option></select>
			<label for="krv-main-note" data-krv-text="note"><?php echo $en ? 'Short description (optional)' : 'Краткое описание (необязательно)'; ?></label>
			<textarea id="krv-main-note" class="ym-disable-keys" rows="3" maxlength="2000" autocomplete="off"></textarea>
			<div class="krv-qual__actions"></div>
			<noscript><p><?php echo $en ? 'Preparing a draft requires JavaScript. Use the direct contact links above.' : 'Подготовка текста требует JavaScript. Напишите напрямую удобным способом выше.'; ?></p></noscript>
			<p class="krv-brief-privacy" data-krv-text="privacy"><?php echo $en ? 'The draft stays in your browser until copied. Nothing is sent automatically. Do not enter passwords or secrets.' : 'Текст остаётся в браузере до копирования. Ничего не отправляется автоматически. Не указывайте пароли и секреты.'; ?></p>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}

add_action( 'wp_enqueue_scripts', static function (): void {
	if ( is_admin() ) {
		return;
	}
	$has_ui = function_exists( 'krv_page_has_ui_shortcode' ) && krv_page_has_ui_shortcode(
		array( 'krv_services_landing', 'krv_en_home', 'krv_contact_block', 'krv_en_contacts' )
	);
	if ( ! is_front_page() && ! is_page( 'contacts' ) && ! $has_ui ) {
		return;
	}
	$plugin = DRSLON_SITE_CORE_DIR . 'drslon-site-core.php';
	wp_enqueue_style( 'krv-contact-brief', plugins_url( 'assets/css/contact-brief.css', $plugin ), array(), (string) filemtime( DRSLON_SITE_CORE_DIR . 'assets/css/contact-brief.css' ) );
	wp_enqueue_script( 'krv-contact-brief', plugins_url( 'assets/js/contact-brief.js', $plugin ), array(), (string) filemtime( DRSLON_SITE_CORE_DIR . 'assets/js/contact-brief.js' ), true );
}, 30 );

add_filter( 'krv_content_widget_html', static function ( string $html, string $name ): string {
	if ( 'contacts' !== $name || false !== strpos( $html, 'data-krv-brief' ) ) {
		return $html;
	}
	$en = function_exists( 'drslon_is_en_page' ) && drslon_is_en_page();
	return $html . krv_contact_brief_markup( $en );
}, 20, 2 );
