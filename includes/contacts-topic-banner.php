<?php
/**
 * Context banner on /contacts/ when ?topic= is set from prays-list funnel.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Page ID for /contacts/. */
define( 'DRSLON_CONTACTS_PAGE_ID', 75 );

/**
 * Topic presets for the contacts context banner.
 *
 * @return array<string, array{title: string, text: string}>
 */
function krv_contacts_topic_presets(): array {
	return array(
		'diagnostic' => array(
			'title' => 'Заявка на диагностику сайта',
			'text'  => 'Кратко опишите сайт и что беспокоит: формы, скорость, Метрика, реклама или ошибки WordPress. Я отвечу с планом и ориентиром по смете.',
		),
		'repair'     => array(
			'title' => 'Доработка или ремонт сайта',
			'text'  => 'Пришлите ссылку на сайт и что именно нужно исправить или улучшить. Если задача точечная — укажите это сразу.',
		),
		'support'    => array(
			'title' => 'Техническая поддержка сайта',
			'text'  => 'Опишите проект и что нужно в сопровождении: обновления, резервные копии, мелкие правки, контроль ошибок.',
		),
	);
}

/**
 * Enqueue banner styles on contacts page with topic param.
 */
function krv_contacts_topic_banner_assets(): void {
	if ( ! is_page( DRSLON_CONTACTS_PAGE_ID ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$topic = isset( $_GET['topic'] ) ? sanitize_key( wp_unslash( $_GET['topic'] ) ) : '';

	if ( $topic === '' || ! isset( krv_contacts_topic_presets()[ $topic ] ) ) {
		return;
	}

	wp_enqueue_style(
		'drslon-contacts-topic-banner',
		plugins_url( 'assets/css/contacts-topic-banner.css', DRSLON_SITE_CORE_DIR . 'drslon-site-core.php' ),
		array(),
		DRSLON_SITE_CORE_VERSION
	);
}

add_action( 'wp_enqueue_scripts', 'krv_contacts_topic_banner_assets', 25 );

/**
 * Build topic context banner markup.
 *
 * @return string
 */
function krv_contacts_topic_banner_html(): string {
	static $rendered = false;

	if ( $rendered || ! is_page( DRSLON_CONTACTS_PAGE_ID ) ) {
		return '';
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$topic   = isset( $_GET['topic'] ) ? sanitize_key( wp_unslash( $_GET['topic'] ) ) : '';
	$presets = krv_contacts_topic_presets();

	if ( $topic === '' || ! isset( $presets[ $topic ] ) ) {
		return '';
	}

	$preset = $presets[ $topic ];

	$html  = '<div class="krv-contact-topic-banner" id="krv-contact-topic-banner" role="status">';
	$html .= '<strong class="krv-contact-topic-banner-title">' . esc_html( $preset['title'] ) . '</strong>';
	$html .= '<p class="krv-contact-topic-banner-text">' . esc_html( $preset['text'] ) . '</p>';
	$html .= '</div>';

	$rendered = true;

	return $html;
}

/**
 * Render topic context banner before main content.
 */
function krv_contacts_topic_banner_render(): void {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo krv_contacts_topic_banner_html();
}

add_action( 'wp_body_open', 'krv_contacts_topic_banner_render', 5 );

/**
 * Fallback when the theme does not call wp_body_open().
 *
 * @param string $content Post content.
 * @return string
 */
function krv_contacts_topic_banner_prepend_content( string $content ): string {
	if ( ! is_page( DRSLON_CONTACTS_PAGE_ID ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$banner = krv_contacts_topic_banner_html();

	if ( $banner === '' ) {
		return $content;
	}

	return $banner . $content;
}

add_filter( 'the_content', 'krv_contacts_topic_banner_prepend_content', 5 );