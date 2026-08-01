<?php
/**
 * Services landing shortcode [krv_services_landing] with ACF options page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** ACF options page slug. */
function krv_services_landing_option_id(): string {
	return 'krv-services-landing';
}

/**
 * Hardcoded defaults — exact copy of the legacy shortcode content.
 *
 * @return array<string, mixed>
 */
function krv_services_landing_get_defaults(): array {
	return array(
		'profile_avatar'         => 10321,
		'profile_name'           => 'Алексей Кривошеин',
		// No em/en dash: people write a period or drop the second clause.
		'profile_tagline'        => 'WordPress, VPS, боты MAX, Директ и AI-ready',
		'profile_lead'           => 'Личный бренд и ИП: сайты, серверы, реклама и автоматизация без менеджеров и агентской наценки. Договор, безнал, закрывающие.',
		'profile_meta_lines'       => array(
			array( 'line' => 'ИП Кривошеин Алексей Сергеевич · договор и безналичный расчёт' ),
			array( 'line' => 'ОГРН 321774600479249 · ИНН 770603253213', 'class' => 'is-legal' ),
		),
		'hero_cta_primary_text'    => 'Смотреть прайс',
		'hero_cta_primary_url'     => 'https://krivoshein.site/prays-list/',
		// Same-page jump; social icons under photo cover messengers.
		'hero_cta_secondary_text'  => 'К услугам',
		'hero_cta_secondary_url'   => '#uslugi',
		'social_links'           => array(
			array(
				'url'      => 'https://t.me/DrSlon',
				'label'    => 'Telegram',
				'icon_key' => 'telegram',
				'icon_svg' => '',
			),
			array(
				'url'      => 'https://github.com/A-Krivoshen',
				'label'    => 'GitHub',
				'icon_key' => 'github',
				'icon_svg' => '',
			),
			array(
				'url'      => 'https://vk.com/drslon',
				'label'    => 'VK',
				'icon_key' => 'vk',
				'icon_svg' => '',
			),
			array(
				'url'      => 'https://mastodon.ml/@krivoshein',
				'label'    => 'Mastodon',
				'icon_key' => 'mastodon',
				'icon_svg' => '',
			),
			array(
				'url'      => 'https://www.linkedin.com/in/krivosheinaleksey',
				'label'    => 'LinkedIn',
				'icon_key' => 'linkedin',
				'icon_svg' => '',
			),
			array(
				'url'      => 'https://krivoshein.site/max',
				'label'    => 'MAX',
				'icon_key' => 'max',
				'icon_svg' => '',
			),
		),
		'services_header_title'    => 'Услуги',
		'services_header_subtitle' => 'Флагманские направления: WordPress, VPS, боты MAX, Директ и AI-ready. Цены «от» в карточках, детали в прайсе.',
		'services_items'           => array(
			array(
				'title'       => 'WordPress',
				'description' => 'Сборка и доработка сайтов: ACF-блоки, скорость, безопасность. От точечных правок до корпоративного проекта.',
				'icon_key'    => 'web-dev',
				'icon_svg'    => '',
				'url'         => 'https://wordpress.krivoshein.site/',
				'price_label' => 'от 5 000 ₽',
			),
			array(
				'title'       => 'VPS и DevOps',
				'description' => 'Linux, Nginx, Docker, SSL, бэкапы и перенос. Сервер под задачу, без лишней сложности.',
				'icon_key'    => 'vps',
				'icon_svg'    => '',
				'url'         => 'https://vps.krivoshein.site/',
				'price_label' => 'от 10 000 ₽',
			),
			array(
				'title'       => 'Боты для MAX',
				'description' => 'Заявки, CRM, уведомления и сценарии поддержки в MAX. С нуля или с интеграциями.',
				'icon_key'    => 'max-bot',
				'icon_svg'    => '',
				'url'         => 'https://bots.krivoshein.site/',
				'price_label' => 'от 40 000 ₽',
			),
			array(
				'title'       => 'Яндекс.Директ',
				'description' => 'Аудит, запуск и ведение. Где сливается бюджет и как крутить кампании без воды.',
				'icon_key'    => 'ads',
				'icon_svg'    => '',
				'url'         => 'https://direct.krivoshein.site/',
				'price_label' => 'аудит от 10 000 ₽',
			),
			array(
				'title'       => 'Лендинги',
				'description' => 'Одностраничник под заявку: визитка, SEO-лендинг или WordPress. Без конструкторной каши.',
				'icon_key'    => 'landing',
				'icon_svg'    => '',
				'url'         => 'https://landing.krivoshein.site/',
				'price_label' => 'от 25 000 ₽',
			),
			array(
				'title'       => 'AI-ready',
				'description' => 'Сайт для нейропоиска и агентов: schema, FAQ, структура. Без обещаний «топ-1».',
				'icon_key'    => 'ai',
				'icon_svg'    => '',
				'url'         => 'https://ai-ready.krivoshein.site/',
				'price_label' => 'от 10 000 ₽',
			),
			array(
				'title'       => 'Техподдержка',
				'description' => 'Обновления, бэкапы, мониторинг и реакция на сбои. Пакеты и условия в прайсе.',
				'icon_key'    => 'support',
				'icon_svg'    => '',
				// Same-site anchor on price list (honest: no fake landing).
				'url'         => 'https://krivoshein.site/prays-list/#krv-package-support',
				'price_label' => 'от 20 000 ₽/мес',
			),
		),
		'pricing_title'       => 'Стоимость услуг',
		'pricing_lead'        => "Ориентиры «от» в карточках выше и в прайс-листе.\nБазовая ставка:",
		'pricing_rate'        => '2000 ₽/час',
		'pricing_bullets'     => array(
			array(
				'text'     => 'Чем точнее описана задача, тем быстрее и дешевле оценка.',
				'icon_key' => 'focus',
			),
			array(
				'text'     => 'Финальная сумма зависит от объёма, интеграций и сроков.',
				'icon_key' => 'scope',
			),
			array(
				'text'     => 'Первичная консультация бесплатно.',
				'icon_key' => 'chat',
			),
		),
		'pricing_button_text' => 'Смотреть прайс',
		'pricing_button_url'  => 'https://krivoshein.site/prays-list/',
		'pricing_secondary_text' => 'Обсудить проект',
		'pricing_secondary_url'  => 'https://krivoshein.site/contacts/',
	);
}

/**
 * Allowed SVG tags for inline icon markup.
 *
 * @return array<string, array<string, bool>>
 */
function krv_services_landing_svg_kses(): array {
	return array(
		'svg'      => array(
			'class'       => true,
			'viewbox'     => true,
			'viewBox'     => true,
			'aria-hidden' => true,
			'focusable'   => true,
		),
		'path'     => array(
			'd'     => true,
			'style' => true,
		),
		'polyline' => array(
			'points' => true,
		),
		'line'     => array(
			'x1' => true,
			'y1' => true,
			'x2' => true,
			'y2' => true,
		),
		'rect'     => array(
			'x'    => true,
			'y'    => true,
			'width'  => true,
			'height' => true,
			'rx'   => true,
		),
		'circle'   => array(
			'cx' => true,
			'cy' => true,
			'r'  => true,
			'style' => true,
		),
	);
}

/**
 * Preset social icon inner SVG and viewBox.
 *
 * @param string $key Icon preset key.
 * @return array{inner: string, viewbox: string}
 */
function krv_services_landing_social_icon_preset( string $key ): array {
	$icons = array(
		'telegram' => array(
			'viewbox' => '0 0 24 24',
			'inner'   => '<path d="M21.543 2.498a1.53 1.53 0 0 0-1.58-.26L3.55 8.617a1.54 1.54 0 0 0 .08 2.893l4.11 1.353 1.59 5.01a1.54 1.54 0 0 0 2.52.66l2.29-2.21 3.78 2.78a1.54 1.54 0 0 0 2.42-.9L21.98 4.01a1.53 1.53 0 0 0-.437-1.512ZM9.33 11.97l8.09-4.98-6.7 6.46-.26 2.76-1.13-4.24Z"/>',
		),
		'github'   => array(
			'viewbox' => '0 0 24 24',
			'inner'   => '<path d="M12 2C6.48 2 2 6.59 2 12.25c0 4.53 2.87 8.37 6.84 9.73.5.1.68-.22.68-.49 0-.24-.01-1.03-.01-1.87-2.78.62-3.37-1.21-3.37-1.21-.45-1.18-1.11-1.49-1.11-1.49-.91-.64.07-.63.07-.63 1 .07 1.53 1.06 1.53 1.06.9 1.57 2.35 1.12 2.92.85.09-.67.35-1.12.64-1.38-2.22-.26-4.56-1.15-4.56-5.1 0-1.13.39-2.06 1.03-2.79-.1-.26-.45-1.31.1-2.74 0 0 .84-.28 2.75 1.07A9.32 9.32 0 0 1 12 6.84c.85 0 1.71.12 2.51.36 1.9-1.35 2.74-1.07 2.74-1.07.55 1.43.2 2.48.1 2.74.64.73 1.03 1.66 1.03 2.79 0 3.96-2.34 4.83-4.57 5.09.36.32.68.95.68 1.92 0 1.39-.01 2.5-.01 2.84 0 .27.18.59.69.49A10.25 10.25 0 0 0 22 12.25C22 6.59 17.52 2 12 2Z"/>',
		),
		'vk'       => array(
			'viewbox' => '0 0 24 24',
			'inner'   => '<path d="M3.61 5.18c.13 6.32 3.3 10.12 8.86 10.12h.32v-3.62c2.04.21 3.58 1.7 4.2 3.62H20c-.79-2.88-2.87-4.47-4.17-5.08 1.3-.76 3.12-2.59 3.56-5.04h-2.74c-.57 1.99-2.25 3.82-3.86 3.99V5.18H10.1v7c-1.63-.42-3.7-2.39-3.79-7H3.61Z"/>',
		),
		'mastodon' => array(
			'viewbox' => '0 0 24 24',
			'inner'   => '<path d="M20.94 14c-.28 1.41-2.45 2.96-4.95 3.25-1.3.15-2.58.3-3.95.24-2.24-.1-4-.5-4-.5v.62c.32 2.22 2.25 2.35 4.03 2.41 1.8.05 3.4-.43 3.4-.43l.08 1.65s-1.26.69-3.5.82c-1.23.07-2.76-.03-4.54-.48-3.86-.95-4.52-4.78-4.63-8.67-.03-1.16-.01-2.25-.01-3.16 0-3.98 2.61-5.15 2.61-5.15C6.8 3.9 9.03 3.6 11.23 3.58h.05c2.2.02 4.43.32 5.75.99 0 0 2.61 1.17 2.61 5.15 0 0 .03 2.93-.7 4.28Zm-3.1-4.39c0-.98-.25-1.76-.77-2.33-.54-.57-1.24-.87-2.12-.87-1.01 0-1.78.39-2.3 1.18l-.5.83-.5-.83c-.52-.79-1.29-1.18-2.3-1.18-.88 0-1.58.3-2.12.87-.52.57-.77 1.35-.77 2.33v4.79h1.9V9.75c0-.98.41-1.48 1.24-1.48.91 0 1.37.59 1.37 1.75v2.56h1.88v-2.56c0-1.16.46-1.75 1.37-1.75.83 0 1.24.5 1.24 1.48v4.65h1.9V9.61Z"/>',
		),
		'linkedin' => array(
			'viewbox' => '0 0 24 24',
			'inner'   => '<path d="M6.94 8.5H3.56V20h3.38V8.5ZM5.25 3A2.06 2.06 0 0 0 3.2 5.06c0 1.13.92 2.06 2.05 2.06A2.07 2.07 0 0 0 7.31 5.06 2.07 2.07 0 0 0 5.25 3Zm6.84 5.5H8.83V20h3.26v-6.05c0-1.6.3-3.14 2.25-3.14 1.92 0 1.95 1.8 1.95 3.24V20h3.27v-6.62c0-3.25-.7-5.74-4.5-5.74-1.82 0-3.04 1.02-3.54 1.99h-.05V8.5Z"/>',
		),
		'max'      => array(
			'viewbox' => '7 7 22 22',
			'inner'   => '<path d="' . krv_max_messenger_icon_path() . '"/>',
		),
	);

	return $icons[ $key ] ?? array(
		'viewbox' => '0 0 24 24',
		'inner'   => '',
	);
}

/**
 * Preset service icon inner SVG.
 *
 * @param string $key Icon preset key.
 * @return string
 */
function krv_services_landing_service_icon_preset( string $key ): string {
	// Clean 24×24 stroke icons (Lucide-like), theme-colored via CSS.
	$icons = array(
		// Layout + code — WordPress / sites.
		'web-dev'  => '<path d="M4 5h16v14H4z"></path><path d="M4 9h16"></path><path d="M9 13l-2 2 2 2"></path><path d="M15 13l2 2-2 2"></path><path d="M12.5 13l-1 4"></path>',
		// Server rack.
		'vps'      => '<rect x="3" y="3" width="18" height="7" rx="1.5"></rect><rect x="3" y="14" width="18" height="7" rx="1.5"></rect><circle cx="7" cy="6.5" r="1" fill="currentColor" stroke="none"></circle><circle cx="7" cy="17.5" r="1" fill="currentColor" stroke="none"></circle><path d="M11 6.5h6"></path><path d="M11 17.5h6"></path>',
		// Bot head.
		'max-bot'  => '<rect x="5" y="8" width="14" height="11" rx="3"></rect><path d="M12 8V5"></path><circle cx="12" cy="4" r="1" fill="currentColor" stroke="none"></circle><circle cx="9" cy="13" r="1" fill="currentColor" stroke="none"></circle><circle cx="15" cy="13" r="1" fill="currentColor" stroke="none"></circle><path d="M9 17h6"></path>',
		// Target / ads.
		'ads'      => '<circle cx="12" cy="12" r="8"></circle><circle cx="12" cy="12" r="4"></circle><circle cx="12" cy="12" r="1.2" fill="currentColor" stroke="none"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="M2 12h2"></path><path d="M20 12h2"></path>',
		// Landing layout.
		'landing'  => '<rect x="5" y="3" width="14" height="18" rx="2"></rect><path d="M5 8h14"></path><rect x="8" y="11" width="8" height="2.5" rx="0.5"></rect><path d="M8 16h5"></path>',
		// Spark / AI.
		'ai'       => '<path d="M12 2v4"></path><path d="M12 18v4"></path><path d="M4.93 4.93l2.83 2.83"></path><path d="M16.24 16.24l2.83 2.83"></path><path d="M2 12h4"></path><path d="M18 12h4"></path><path d="M4.93 19.07l2.83-2.83"></path><path d="M16.24 7.76l2.83-2.83"></path><circle cx="12" cy="12" r="3"></circle>',
		// Database / RAG.
		'rag'      => '<ellipse cx="12" cy="6" rx="7" ry="2.8"></ellipse><path d="M5 6v4c0 1.5 3.1 2.8 7 2.8s7-1.3 7-2.8V6"></path><path d="M5 10v4c0 1.5 3.1 2.8 7 2.8s7-1.3 7-2.8v-4"></path><path d="M5 14v4c0 1.5 3.1 2.8 7 2.8s7-1.3 7-2.8v-4"></path>',
		// Headset / support.
		'support'  => '<path d="M4 14v-2a8 8 0 0 1 16 0v2"></path><path d="M4 14v3a2 2 0 0 0 2 2h1v-7H6a2 2 0 0 0-2 2Z"></path><path d="M20 14v3a2 2 0 0 1-2 2h-1v-7h1a2 2 0 0 1 2 2Z"></path><path d="M14 21h-2a2 2 0 0 1 0-4h1"></path>',
		// Search / SEO.
		'seo'      => '<circle cx="11" cy="11" r="6.5"></circle><path d="M16.5 16.5L21 21"></path>',
		'docker'   => '<rect x="3" y="4" width="8" height="7" rx="1"></rect><rect x="13" y="4" width="8" height="7" rx="1"></rect><rect x="8" y="13" width="8" height="7" rx="1"></rect><path d="M7 11l5 2"></path><path d="M17 11l-5 2"></path>',
		'domain'   => '<circle cx="12" cy="12" r="9"></circle><path d="M3 12h18"></path><path d="M12 3a15 15 0 0 1 0 18"></path><path d="M12 3a15 15 0 0 0 0 18"></path>',
		'cloud'    => '<path d="M7 18h10a4 4 0 0 0 .5-8 6 6 0 0 0-11.5 1.5A3.5 3.5 0 0 0 7 18Z"></path>',
		'security' => '<path d="M12 3l7 3v5c0 4.5-2.9 8.1-7 10-4.1-1.9-7-5.5-7-10V6l7-3Z"></path><path d="M9.5 12.2l1.8 1.8 3.7-4"></path>',
		'speed'    => '<circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3.5 2"></path>',
		'cdn'      => '<rect x="3" y="6" width="7" height="5" rx="1"></rect><rect x="14" y="4" width="7" height="5" rx="1"></rect><rect x="14" y="15" width="7" height="5" rx="1"></rect><path d="M10 8.5h4"></path><path d="M10 8.5l4 9"></path>',
	);

	return $icons[ $key ] ?? $icons['web-dev'];
}

/**
 * Preset pricing bullet icon inner SVG.
 *
 * @param string $key Icon preset key.
 * @return string
 */
function krv_services_landing_pricing_icon_preset( string $key ): string {
	$icons = array(
		'focus' => '<line x1="12" y1="2" x2="12" y2="8"></line><line x1="12" y1="16" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="9.17" y2="9.17"></line><line x1="14.83" y1="14.83" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="8" y2="12"></line><line x1="16" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="9.17" y2="14.83"></line><line x1="14.83" y1="9.17" x2="19.07" y2="4.93"></line>',
		'scope' => '<path d="M14 3h7v7"></path><path d="M10 14L21 3"></path><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"></path>',
		'chat'  => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>',
	);

	return $icons[ $key ] ?? $icons['focus'];
}

/**
 * Register ACF options page and local field group.
 */
function krv_services_landing_register_acf(): void {
	if ( ! function_exists( 'acf_add_options_page' ) || ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_options_page(
		krv_acf_options_page_args(
			krv_services_landing_option_id(),
			array(
				'page_title' => 'Лендинг услуг',
				'menu_title' => 'Лендинг услуг',
				'capability' => 'edit_theme_options',
				'redirect'   => false,
				'position'   => 62,
				'icon_url'   => 'dashicons-id-alt',
			)
		)
	);

	$social_icon_choices = array(
		'telegram' => 'Telegram',
		'github'   => 'GitHub',
		'vk'       => 'VK',
		'mastodon' => 'Mastodon',
		'linkedin' => 'LinkedIn',
		'max'      => 'MAX',
	);

	$service_icon_choices = array(
		'web-dev'  => 'Веб-разработка',
		'vps'      => 'VPS / серверы',
		'max-bot'  => 'MAX бот',
		'ads'      => 'Реклама',
		'landing'  => 'Лендинг',
		'ai'       => 'AI',
		'rag'      => 'RAG / база',
		'support'  => 'Поддержка',
		'seo'      => 'SEO / поиск',
		'docker'   => 'Docker',
		'domain'   => 'Домен',
		'cloud'    => 'Облако',
		'security' => 'Безопасность',
		'speed'    => 'Скорость',
		'cdn'      => 'CDN',
	);

	$pricing_icon_choices = array(
		'focus' => 'Фокус / точность',
		'scope' => 'Масштаб / ссылка',
		'chat'  => 'Консультация',
	);

	acf_add_local_field_group(
		array(
			'key'    => 'group_krv_services_landing',
			'title'  => 'Лендинг услуг',
			'fields' => array(
				array(
					'key'           => 'field_krv_sl_profile_avatar',
					'label'         => 'Аватар',
					'name'          => 'profile_avatar',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'thumbnail',
					'library'       => 'all',
					'instructions'  => 'Выберите фото из медиатеки или загрузите новое.',
				),
				array(
					'key'   => 'field_krv_sl_profile_name',
					'label' => 'Имя',
					'name'  => 'profile_name',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_krv_sl_profile_lead',
					'label' => 'Описание',
					'name'  => 'profile_lead',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				array(
					'key'          => 'field_krv_sl_profile_meta_lines',
					'label'        => 'Строки под описанием',
					'name'         => 'profile_meta_lines',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Добавить строку',
					'sub_fields'   => array(
						array(
							'key'   => 'field_krv_sl_profile_meta_line',
							'label' => 'Строка',
							'name'  => 'line',
							'type'  => 'text',
						),
					),
				),
				array(
					'key'          => 'field_krv_sl_social_links',
					'label'        => 'Соцсети',
					'name'         => 'social_links',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Добавить ссылку',
					'sub_fields'   => array(
						array(
							'key'   => 'field_krv_sl_social_url',
							'label' => 'URL',
							'name'  => 'url',
							'type'  => 'url',
						),
						array(
							'key'   => 'field_krv_sl_social_label',
							'label' => 'Подпись (title / aria-label)',
							'name'  => 'label',
							'type'  => 'text',
						),
						array(
							'key'     => 'field_krv_sl_social_icon_key',
							'label'   => 'Иконка (пресет)',
							'name'    => 'icon_key',
							'type'    => 'select',
							'choices' => $social_icon_choices,
							'ui'      => 1,
						),
						array(
							'key'          => 'field_krv_sl_social_icon_svg',
							'label'        => 'Иконка (SVG path, необязательно)',
							'name'         => 'icon_svg',
							'type'         => 'textarea',
							'rows'         => 3,
							'instructions' => 'Внутреннее содержимое SVG (path, circle и т.д.). Если заполнено — перекрывает пресет.',
						),
					),
				),
				array(
					'key'           => 'field_krv_sl_services_header_title',
					'label'         => 'Заголовок блока услуг',
					'name'          => 'services_header_title',
					'type'          => 'text',
					'default_value' => 'Услуги',
				),
				array(
					'key'   => 'field_krv_sl_services_header_subtitle',
					'label' => 'Подзаголовок блока услуг',
					'name'  => 'services_header_subtitle',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_krv_sl_services_items',
					'label'        => 'Услуги',
					'name'         => 'services_items',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Добавить услугу',
					'sub_fields'   => array(
						array(
							'key'   => 'field_krv_sl_service_title',
							'label' => 'Заголовок',
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_krv_sl_service_description',
							'label' => 'Описание',
							'name'  => 'description',
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'   => 'field_krv_sl_service_url',
							'label' => 'Ссылка карточки',
							'name'  => 'url',
							'type'  => 'url',
							'instructions' => 'Лендинг услуги, прайс или демо. Пусто = карточка некликабельна.',
						),
						array(
							'key'   => 'field_krv_sl_service_price_label',
							'label' => 'Цена (бейдж)',
							'name'  => 'price_label',
							'type'  => 'text',
							'instructions' => 'Коротко, например: от 10 000 ₽',
						),
						array(
							'key'     => 'field_krv_sl_service_icon_key',
							'label'   => 'Иконка (пресет)',
							'name'    => 'icon_key',
							'type'    => 'select',
							'choices' => $service_icon_choices,
							'ui'      => 1,
						),
						array(
							'key'          => 'field_krv_sl_service_icon_svg',
							'label'        => 'Иконка (SVG path, необязательно)',
							'name'         => 'icon_svg',
							'type'         => 'textarea',
							'rows'         => 3,
							'instructions' => 'Внутреннее содержимое SVG. Если заполнено - перекрывает пресет.',
						),
					),
				),
				array(
					'key'   => 'field_krv_sl_pricing_title',
					'label' => 'Заголовок блока цен',
					'name'  => 'pricing_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_krv_sl_pricing_lead',
					'label' => 'Текст перед ставкой',
					'name'  => 'pricing_lead',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				array(
					'key'   => 'field_krv_sl_pricing_rate',
					'label' => 'Ставка',
					'name'  => 'pricing_rate',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_krv_sl_pricing_bullets',
					'label'        => 'Пункты списка',
					'name'         => 'pricing_bullets',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Добавить пункт',
					'sub_fields'   => array(
						array(
							'key'   => 'field_krv_sl_pricing_bullet_text',
							'label' => 'Текст',
							'name'  => 'text',
							'type'  => 'text',
						),
						array(
							'key'     => 'field_krv_sl_pricing_bullet_icon_key',
							'label'   => 'Иконка',
							'name'    => 'icon_key',
							'type'    => 'select',
							'choices' => $pricing_icon_choices,
							'ui'      => 1,
						),
					),
				),
				array(
					'key'   => 'field_krv_sl_pricing_button_text',
					'label' => 'Текст кнопки',
					'name'  => 'pricing_button_text',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_krv_sl_pricing_button_url',
					'label' => 'URL кнопки',
					'name'  => 'pricing_button_url',
					'type'  => 'url',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => krv_services_landing_option_id(),
					),
				),
			),
		)
	);
}

/**
 * One-time seed of ACF options from hardcoded defaults.
 */
function krv_services_landing_seed_defaults(): void {
	if ( get_option( 'krv_services_landing_seeded_v1' ) ) {
		return;
	}

	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	$defaults  = krv_services_landing_get_defaults();
	$option_id = krv_services_landing_option_id();

	foreach ( $defaults as $field_name => $value ) {
		update_field( $field_name, $value, $option_id );
	}

	update_option( 'krv_services_landing_seeded_v1', DRSLON_SITE_CORE_VERSION, false );
}

add_action(
	'acf/init',
	function () {
		krv_services_landing_register_acf();
		krv_services_landing_seed_defaults();
	},
	20
);

/**
 * Resolve avatar field value to a public image URL.
 *
 * @param mixed $value Attachment ID, ACF image array, or legacy URL string.
 * @return string
 */
function krv_services_landing_resolve_avatar_url( $value ): string {
	$fallback = 'https://krivoshein.site/wp-content/uploads/2026/06/drslon_avatar.png';

	if ( is_array( $value ) ) {
		if ( ! empty( $value['url'] ) ) {
			return (string) $value['url'];
		}
		if ( ! empty( $value['ID'] ) ) {
			$value = (int) $value['ID'];
		}
	}

	if ( is_numeric( $value ) ) {
		$url = wp_get_attachment_image_url( (int) $value, 'full' );
		return is_string( $url ) && $url !== '' ? $url : $fallback;
	}

	if ( is_string( $value ) && $value !== '' ) {
		return $value;
	}

	return $fallback;
}

/**
 * Convert legacy URL avatar value to attachment ID for the media picker.
 */
function krv_services_landing_migrate_avatar_attachment(): void {
	if ( get_option( 'krv_services_landing_avatar_migrated_v1' ) ) {
		return;
	}

	if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	$option_id = krv_services_landing_option_id();
	$current   = get_field( 'profile_avatar', $option_id );

	if ( is_numeric( $current ) && (int) $current > 0 ) {
		update_option( 'krv_services_landing_avatar_migrated_v1', DRSLON_SITE_CORE_VERSION, false );
		return;
	}

	$url = is_string( $current ) ? trim( $current ) : '';

	if ( $url === '' ) {
		update_field( 'profile_avatar', 10321, $option_id );
		update_option( 'krv_services_landing_avatar_migrated_v1', DRSLON_SITE_CORE_VERSION, false );
		return;
	}

	$attachment_id = attachment_url_to_postid( $url );

	if ( ! $attachment_id ) {
		$attachment_id = 10321;
	}

	update_field( 'profile_avatar', (int) $attachment_id, $option_id );
	update_option( 'krv_services_landing_avatar_migrated_v1', DRSLON_SITE_CORE_VERSION, false );
}

add_action( 'acf/init', 'krv_services_landing_migrate_avatar_attachment', 28 );

/**
 * Load landing data from ACF with hardcoded fallbacks.
 *
 * @return array<string, mixed>
 */
function krv_services_landing_get_data(): array {
	$defaults  = krv_services_landing_get_defaults();
	$option_id = krv_services_landing_option_id();

	if ( ! function_exists( 'get_field' ) ) {
		return $defaults;
	}

	$data = array();

	foreach ( $defaults as $field_name => $default_value ) {
		$value = get_field( $field_name, $option_id );

		if ( null === get_option( $option_id . '_' . $field_name, null ) ) {
			$data[ $field_name ] = $default_value;
			continue;
		}

		// Empty ACF text → keep sensible default (avoids blank hero tagline).
		if ( ( null === $value || '' === $value ) && is_string( $default_value ) && $default_value !== '' ) {
			$data[ $field_name ] = $default_value;
			continue;
		}

		$data[ $field_name ] = $value;
	}

	return $data;
}


/**
 * Render social link icon markup.
 *
 * @param array<string, string> $link Social link row.
 * @return string
 */
function krv_services_landing_render_social_icon( array $link ): string {
	$icon_key = (string) ( $link['icon_key'] ?? '' );
	$icon_svg = trim( (string) ( $link['icon_svg'] ?? '' ) );
	$preset   = krv_services_landing_social_icon_preset( $icon_key );

	$viewbox = $preset['viewbox'];
	$inner   = $icon_svg !== '' ? $icon_svg : $preset['inner'];

	$svg = sprintf(
		'<svg class="krv-landing-social-icon" viewBox="%s" aria-hidden="true" focusable="false">%s</svg>',
		esc_attr( $viewbox ),
		wp_kses( $inner, krv_services_landing_svg_kses() )
	);

	return $svg;
}

/**
 * Render service item icon markup.
 *
 * @param array<string, string> $item Service row.
 * @return string
 */
function krv_services_landing_render_service_icon( array $item ): string {
	$icon_key = (string) ( $item['icon_key'] ?? '' );
	$icon_svg = trim( (string) ( $item['icon_svg'] ?? '' ) );
	$inner    = $icon_svg !== '' ? $icon_svg : krv_services_landing_service_icon_preset( $icon_key );

	$svg = sprintf(
		'<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">%s</svg>',
		wp_kses( $inner, krv_services_landing_svg_kses() )
	);

	return '<span class="krv-landing-service-icon-wrap" aria-hidden="true">' . $svg . '</span>';
}

/**
 * Render pricing bullet icon markup.
 *
 * @param array<string, string> $bullet Pricing bullet row.
 * @return string
 */
function krv_services_landing_render_pricing_icon( array $bullet ): string {
	$icon_key = (string) ( $bullet['icon_key'] ?? 'focus' );
	$inner    = krv_services_landing_pricing_icon_preset( $icon_key );

	return sprintf(
		'<svg class="krv-landing-pricing-icon" viewBox="0 0 24 24" aria-hidden="true">%s</svg>',
		wp_kses( $inner, krv_services_landing_svg_kses() )
	);
}

/**
 * Render the services landing shortcode.
 *
 * @return string
 */
function krv_services_landing_render(): string {
	$data = krv_services_landing_get_data();

	ob_start();
	?>
	<div class="krv-services-landing">
		<div class="krv-services-landing-section">
			<div class="krv-landing-contact-card">
				<div class="krv-landing-avatar-wrap">
					<img class="krv-landing-avatar" src="<?php echo esc_url( krv_services_landing_resolve_avatar_url( $data['profile_avatar'] ?? '' ) ); ?>" alt="<?php echo esc_attr( (string) $data['profile_name'] ); ?>" width="160" height="160" decoding="async" fetchpriority="high" data-no-lazy="1">
				</div>

				<?php if ( is_front_page() ) : ?>
					<h1 class="krv-landing-title"><?php echo esc_html( (string) $data['profile_name'] ); ?></h1>
				<?php else : ?>
					<h2 class="krv-landing-title"><?php echo esc_html( (string) $data['profile_name'] ); ?></h2>
				<?php endif; ?>

				<?php
				$tagline = trim( (string) ( $data['profile_tagline'] ?? '' ) );
				// Never show AI-looking long dashes in the hero line.
				$tagline = str_replace( array( '—', '–', '&#8212;', '&#8211;', ' - ', ' – ', ' — ' ), array( '. ', '. ', '. ', '. ', '. ', '. ', '. ' ), $tagline );
				$tagline = preg_replace( '/\.\s*\./u', '.', $tagline ) ?? $tagline;
				$tagline = trim( $tagline, " \t\n\r\0\x0B." );
				if ( $tagline !== '' ) :
					?>
					<p class="krv-landing-tagline"><?php echo esc_html( $tagline ); ?></p>
				<?php endif; ?>

				<p class="krv-landing-lead"><?php echo esc_html( (string) $data['profile_lead'] ); ?></p>

				<?php if ( ! empty( $data['profile_meta_lines'] ) && is_array( $data['profile_meta_lines'] ) ) : ?>
					<div class="krv-landing-meta">
						<?php foreach ( $data['profile_meta_lines'] as $meta_line ) : ?>
							<?php
							$line  = is_array( $meta_line ) ? (string) ( $meta_line['line'] ?? '' ) : (string) $meta_line;
							$class = is_array( $meta_line ) ? trim( (string) ( $meta_line['class'] ?? '' ) ) : '';
							if ( $line === '' ) {
								continue;
							}
							// Quieter legal ids (OGRN / INN) even if ACF row has no class field.
							if ( $class === '' && ( false !== stripos( $line, 'ОГРН' ) || false !== stripos( $line, 'ИНН' ) ) ) {
								$class = 'is-legal';
							}
							$cls = 'krv-landing-meta-line' . ( $class !== '' ? ' ' . sanitize_html_class( $class ) : '' );
							?>
							<span class="<?php echo esc_attr( $cls ); ?>"><?php echo esc_html( $line ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php
				// Honest social proof: only facts already on the page (clients count, OGRN year, offers).
				$proof_clients = 0;
				if ( post_type_exists( 'client' ) ) {
					$proof_clients = (int) wp_count_posts( 'client' )->publish;
				}
				?>
				<ul class="krv-landing-proof" aria-label="Коротко о работе">
					<?php if ( $proof_clients > 0 ) : ?>
						<li class="krv-landing-proof-item">
							<strong class="krv-landing-proof-value"><?php echo esc_html( (string) $proof_clients ); ?></strong>
							<span class="krv-landing-proof-label">клиентов в витрине</span>
						</li>
					<?php endif; ?>
					<li class="krv-landing-proof-item">
						<strong class="krv-landing-proof-value">с 2021</strong>
						<span class="krv-landing-proof-label">ИП в реестре</span>
					</li>
					<li class="krv-landing-proof-item">
						<strong class="krv-landing-proof-value">8</strong>
						<span class="krv-landing-proof-label">флагманских услуг</span>
					</li>
					<li class="krv-landing-proof-item">
						<strong class="krv-landing-proof-value">договор</strong>
						<span class="krv-landing-proof-label">и безнал</span>
					</li>
				</ul>

				<?php
				$cta1_text = trim( (string) ( $data['hero_cta_primary_text'] ?? '' ) );
				$cta1_url  = trim( (string) ( $data['hero_cta_primary_url'] ?? '' ) );
				$cta2_text = trim( (string) ( $data['hero_cta_secondary_text'] ?? '' ) );
				$cta2_url  = trim( (string) ( $data['hero_cta_secondary_url'] ?? '' ) );
				if ( ( $cta1_text !== '' && $cta1_url !== '' ) || ( $cta2_text !== '' && $cta2_url !== '' ) ) :
					?>
					<div class="krv-landing-hero-cta">
						<?php if ( $cta1_text !== '' && $cta1_url !== '' ) : ?>
							<a class="krv-landing-pricing-button" href="<?php echo esc_url( $cta1_url ); ?>"><?php echo esc_html( $cta1_text ); ?></a>
						<?php endif; ?>
						<?php if ( $cta2_text !== '' && $cta2_url !== '' ) : ?>
							<?php
							$is_hash   = isset( $cta2_url[0] ) && $cta2_url[0] === '#';
							$is_ext    = 0 === strpos( $cta2_url, 'http' ) && false === strpos( $cta2_url, home_url() );
							$sec_attrs = $is_ext ? ' target="_blank" rel="noopener noreferrer"' : '';
							?>
							<a class="krv-landing-hero-cta-secondary" href="<?php echo esc_url( $cta2_url ); ?>"<?php echo $sec_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $cta2_text ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $data['social_links'] ) && is_array( $data['social_links'] ) ) : ?>
					<div class="krv-landing-contacts">
						<?php foreach ( $data['social_links'] as $social_link ) : ?>
							<?php
							if ( ! is_array( $social_link ) ) {
								continue;
							}

							$url   = (string) ( $social_link['url'] ?? '' );
							$label = (string) ( $social_link['label'] ?? '' );

							if ( $url === '' ) {
								continue;
							}
							?>
							<a href="<?php echo esc_url( $url ); ?>" target="_blank" title="<?php echo esc_attr( $label ); ?>" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $label ); ?>">
								<?php echo krv_services_landing_render_social_icon( $social_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="krv-services-landing-section">
			<div class="krv-landing-services" id="uslugi">
				<div class="krv-landing-services-header">
					<h2><?php echo esc_html( (string) $data['services_header_title'] ); ?></h2>
					<p><?php echo esc_html( (string) $data['services_header_subtitle'] ); ?></p>
				</div>

				<?php if ( ! empty( $data['services_items'] ) && is_array( $data['services_items'] ) ) : ?>
					<?php
					/**
					 * Allow injecting/altering homepage service tiles.
					 *
					 * @param array<int, array<string, mixed>> $items Service items.
					 */
					$services_items = apply_filters( 'krv_services_landing_items', $data['services_items'] );
					if ( ! is_array( $services_items ) ) {
						$services_items = $data['services_items'];
					}
					?>
					<div class="krv-landing-services-grid">
						<?php foreach ( $services_items as $service_item ) : ?>
							<?php
							if ( ! is_array( $service_item ) ) {
								continue;
							}

							$title = (string) ( $service_item['title'] ?? '' );
							if ( $title === '' ) {
								continue;
							}
							$url         = trim( (string) ( $service_item['url'] ?? '' ) );
							$price_label = trim( (string) ( $service_item['price_label'] ?? '' ) );
							$is_demo     = ( false !== stripos( $title, 'RAG' ) || false !== stripos( $title, 'демо' ) );
							$tag         = $url !== '' ? 'a' : 'div';

							// External flagship landings open in a new tab; same-site (prays-list, rag-demo) stay here.
							$home_host   = (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST );
							$link_host   = (string) wp_parse_url( $url, PHP_URL_HOST );
							$is_external = $url !== '' && $link_host !== '' && strcasecmp( $link_host, $home_host ) !== 0;

							$href_attr = '';
							if ( $url !== '' ) {
								if ( $is_external ) {
									$href_attr = ' href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" title="' . esc_attr( $title . ' (откроется в новой вкладке)' ) . '"';
								} else {
									$href_attr = ' href="' . esc_url( $url ) . '" title="' . esc_attr( $title ) . '"';
								}
							}

							$extra_class = $url !== '' ? ' krv-landing-service-item--link' : '';
							if ( $is_demo ) {
								$extra_class .= ' krv-landing-service-item--demo';
							}
							if ( $url !== '' && ! $is_external ) {
								$extra_class .= ' krv-landing-service-item--onsite';
							}
							$badge_text  = $is_demo ? ( $price_label !== '' ? $price_label : 'демо' ) : $price_label;
							$badge_class = $is_demo ? 'krv-landing-service-price krv-landing-service-badge' : 'krv-landing-service-price';
							?>
							<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="krv-landing-service-item<?php echo esc_attr( $extra_class ); ?>"<?php echo $href_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<div class="krv-landing-service-inner">
									<?php if ( $badge_text !== '' ) : ?>
										<span class="<?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $badge_text ); ?></span>
									<?php endif; ?>
									<?php if ( $url !== '' ) : ?>
										<span class="krv-landing-service-ext" aria-hidden="true">
											<?php if ( $is_external ) : ?>
												<svg width="14" height="14" viewBox="0 0 24 24" fill="none" focusable="false">
													<path d="M14 5h5v5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
													<path d="M10 14L19 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
													<path d="M19 13v5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											<?php else : ?>
												<svg width="14" height="14" viewBox="0 0 24 24" fill="none" focusable="false">
													<path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
													<path d="M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											<?php endif; ?>
										</span>
									<?php endif; ?>
									<?php echo krv_services_landing_render_service_icon( $service_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<h3 class="krv-landing-service-title"><?php echo esc_html( $title ); ?></h3>
									<p class="krv-landing-service-desc"><?php echo esc_html( (string) ( $service_item['description'] ?? '' ) ); ?></p>
								</div>
							</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php endforeach; ?>
					</div>
					<p class="krv-landing-services-note">
						Также: домены, CDN, SSL, Docker, миграции и точечный SEO
						<span class="krv-landing-services-note-sep">·</span>
						обычно внутри WordPress&nbsp;/&nbsp;VPS&#8209;проекта.<br>
						Полный список цен в
						<a href="<?php echo esc_url( home_url( '/prays-list/' ) ); ?>">прайс&#8209;листе</a>.
					</p>
					<p class="krv-landing-services-crosslink">
						Нужен хостинг, VPS или платёжка?
						<a href="<?php echo esc_url( home_url( '/partnery/' ) ); ?>">Партнёры и сервисы, которыми пользуюсь</a>
					</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="krv-services-landing-section">
			<?php echo do_shortcode( '[krv_clients_grid]' ); ?>
		</div>

		<div class="krv-services-landing-section">
			<div class="krv-landing-pricing">
				<h2 class="krv-landing-pricing-title"><?php echo esc_html( (string) $data['pricing_title'] ); ?></h2>

				<p class="krv-landing-pricing-lead">
					<?php echo wp_kses_post( nl2br( esc_html( (string) $data['pricing_lead'] ) ) ); ?><br>
					<span class="krv-landing-pricing-rate"><?php echo esc_html( (string) $data['pricing_rate'] ); ?></span>
				</p>

				<?php if ( ! empty( $data['pricing_bullets'] ) && is_array( $data['pricing_bullets'] ) ) : ?>
					<ul class="krv-landing-pricing-list">
						<?php foreach ( $data['pricing_bullets'] as $bullet ) : ?>
							<?php
							if ( ! is_array( $bullet ) ) {
								continue;
							}

							$text = (string) ( $bullet['text'] ?? '' );
							if ( $text === '' ) {
								continue;
							}
							?>
							<li>
								<?php echo krv_services_landing_render_pricing_icon( $bullet ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span><?php echo esc_html( $text ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<div class="krv-landing-pricing-actions">
					<a class="krv-landing-pricing-button" href="<?php echo esc_url( (string) $data['pricing_button_url'] ); ?>">
						<?php echo esc_html( (string) $data['pricing_button_text'] ); ?>
					</a>
					<?php
					$sec_url  = trim( (string) ( $data['pricing_secondary_url'] ?? '' ) );
					$sec_text = trim( (string) ( $data['pricing_secondary_text'] ?? '' ) );
					if ( $sec_url !== '' && $sec_text !== '' ) :
						?>
						<a class="krv-landing-pricing-secondary" href="<?php echo esc_url( $sec_url ); ?>">
							<?php echo esc_html( $sec_text ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<nav class="krv-landing-next" aria-label="Дальше по сайту">
			<span class="krv-landing-next-label">Дальше:</span>
			<a href="<?php echo esc_url( home_url( '/partnery/' ) ); ?>">Партнёры</a>
			<span class="krv-landing-next-sep" aria-hidden="true">·</span>
			<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Блог</a>
			<span class="krv-landing-next-sep" aria-hidden="true">·</span>
			<a href="<?php echo esc_url( home_url( '/servisy/' ) ); ?>">Сервисы</a>
			<span class="krv-landing-next-sep" aria-hidden="true">·</span>
			<a href="<?php echo esc_url( home_url( '/contacts/' ) ); ?>">Контакты</a>
		</nav>
	</div>
	<?php

	// Homepage Service/Offer schema (helps rich results; prices are "from").
	if ( is_front_page() && ! empty( $data['services_items'] ) && is_array( $data['services_items'] ) ) {
		$offers = array();
		foreach ( $data['services_items'] as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$t = trim( (string) ( $item['title'] ?? '' ) );
			if ( $t === '' ) {
				continue;
			}
			$offer = array(
				'@type'       => 'Offer',
				'itemOffered' => array(
					'@type'       => 'Service',
					'name'        => $t,
					'description' => trim( (string) ( $item['description'] ?? '' ) ),
					'provider'    => array(
						'@type' => 'Person',
						'name'  => 'Алексей Кривошеин',
					),
				),
			);
			$url = trim( (string) ( $item['url'] ?? '' ) );
			if ( $url !== '' ) {
				$offer['url'] = $url;
				$offer['itemOffered']['url'] = $url;
			}
			$price_label = trim( (string) ( $item['price_label'] ?? '' ) );
			if ( $price_label !== '' ) {
				$offer['description'] = $price_label;
			}
			$offers[] = $offer;
		}
		if ( $offers ) {
			$graph = array(
				'@context' => 'https://schema.org',
				'@type'    => 'ItemList',
				'name'     => 'Услуги Dr.Slon',
				'itemListElement' => array(),
			);
			foreach ( $offers as $i => $offer ) {
				$graph['itemListElement'][] = array(
					'@type'    => 'ListItem',
					'position' => $i + 1,
					'item'     => $offer['itemOffered'],
				);
			}
			echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	return (string) ob_get_clean();
}

add_shortcode( 'krv_services_landing', 'krv_services_landing_render' );
