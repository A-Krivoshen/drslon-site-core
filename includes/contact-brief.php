<?php
/** Direct Telegram and MAX contacts. No form, endpoint or stored message. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function krv_contact_talk_telegram_url( bool $en = false ): string {
	// One line: WordPress esc_url() removes %0A from href values.
	$text = $en
		? 'Hello! I want to discuss a task. Service: Websites, ads and automation'
		: 'Здравствуйте! Хочу обсудить задачу. Услуга: Сайты, реклама и автоматизация';
	return 'https://t.me/DrSlon?text=' . rawurlencode( $text );
}

function krv_contact_talk_markup( bool $en = false ): string {
	$title    = $en ? 'Have a task? Let\'s discuss it' : 'Есть задача? Давайте обсудим';
	$lead     = $en ? 'Describe your task in a sentence or two. I will reply with timing and price.' : 'Опишите задачу в двух словах — отвечу и назову сроки и цену.';
	$telegram = $en ? 'Message on Telegram' : 'Написать в Telegram';
	$max      = $en ? 'Message on MAX' : 'Написать в MAX';
	$label    = $en ? 'Contact' : 'Связь';
	ob_start();
	?>
	<section id="brief" class="krv-services-landing-section krv-qual-section" aria-label="<?php echo esc_attr( $label ); ?>">
		<div class="krv-qual" data-krv-talk data-service="site" data-counter="51907757">
			<h2><?php echo esc_html( $title ); ?></h2>
			<p class="krv-qual__lead"><?php echo esc_html( $lead ); ?></p>
			<div class="krv-talk__actions">
				<a class="krv-talk__btn krv-talk__btn--max" href="<?php echo esc_url( home_url( '/max' ) ); ?>"><?php echo esc_html( $max ); ?></a>
				<a class="krv-talk__btn krv-talk__btn--secondary" data-krv-telegram href="<?php echo esc_url( krv_contact_talk_telegram_url( $en ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $telegram ); ?></a>
			</div>
			<p class="krv-talk__note" data-krv-talk-note hidden></p>
			<p class="krv-talk__alt"><a href="mailto:aleksey@krivoshein.site">aleksey@krivoshein.site</a><a href="tel:+79636641615">+7 (963) 664-16-15</a></p>
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
	wp_enqueue_style( 'krv-contact-talk', plugins_url( 'assets/css/contact-talk.css', $plugin ), array(), (string) filemtime( DRSLON_SITE_CORE_DIR . 'assets/css/contact-talk.css' ) );
	wp_enqueue_script( 'krv-contact-talk', plugins_url( 'assets/js/contact-talk.js', $plugin ), array(), (string) filemtime( DRSLON_SITE_CORE_DIR . 'assets/js/contact-talk.js' ), true );
}, 30 );

add_filter( 'krv_content_widget_html', static function ( string $html, string $name ): string {
	if ( 'contacts' !== $name || false !== strpos( $html, 'data-krv-talk' ) ) {
		return $html;
	}
	$en = function_exists( 'drslon_is_en_page' ) && drslon_is_en_page();
	return $html . krv_contact_talk_markup( $en );
}, 20, 2 );

// Published contacts can contain legacy blocks rather than the widget shortcode.
add_filter( 'the_content', static function ( string $html ): string {
	if ( is_admin() || ! is_page( 'contacts' ) || get_the_ID() !== get_queried_object_id() || false !== strpos( $html, 'data-krv-talk' ) ) {
		return $html;
	}
	$en = function_exists( 'drslon_is_en_page' ) && drslon_is_en_page();
	return $html . krv_contact_talk_markup( $en );
}, 20 );
