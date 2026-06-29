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
		'profile_lead'           => 'Специализируюсь на разработке и продвижении веб-сайтов, администрировании серверов и поддержке существующих проектов.',
		'profile_meta_lines'       => array(
			array( 'line' => 'Работаю по договору и принимаю безналичный расчёт.' ),
			array( 'line' => 'ОГРН 321774600479249' ),
			array( 'line' => 'ИНН 770603253213' ),
		),
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
		'services_header_subtitle' => 'Индивидуальный подход и профессиональная реализация для вашего бизнеса',
		'services_items'           => array(
			array(
				'title'       => 'Веб-разработка',
				'description' => 'Создание и доработка сайтов на современных технологиях. Индивидуальные решения для любых задач.',
				'icon_key'    => 'web-dev',
				'icon_svg'    => '',
			),
			array(
				'title'       => 'Интеграция сервисов в Docker',
				'description' => 'Создание и оптимизация контейнеров, упрощающих развертывание и масштабирование. Минимизация конфликтов окружения и ускорение разработки.',
				'icon_key'    => 'docker',
				'icon_svg'    => '',
			),
			array(
				'title'       => 'Настройка VPS',
				'description' => 'Установка и оптимизация виртуальных серверов под конкретные задачи. Гарантия стабильной и безопасной работы вашего проекта.',
				'icon_key'    => 'vps',
				'icon_svg'    => '',
			),
			array(
				'title'       => 'Разработка ботов для MAX',
				'description' => 'Создание чат-ботов для мессенджера MAX под задачи бизнеса и поддержки клиентов. Настройка сценариев, интеграций и автоматизации коммуникаций.',
				'icon_key'    => 'max-bot',
				'icon_svg'    => '',
			),
			array(
				'title'       => 'Услуги по регистрации домена',
				'description' => 'Подбор оптимального доменного имени и помощь в регистрации. Сопровождение и поддержка DNS-записей для корректной работы вашего сайта.',
				'icon_key'    => 'domain',
				'icon_svg'    => '',
			),
			array(
				'title'       => 'Настройки и миграция в облако',
				'description' => 'Анализ текущей инфраструктуры и безопасный перенос в облачные сервисы. Оптимизация ресурсов и снижение расходов на ИТ.',
				'icon_key'    => 'cloud',
				'icon_svg'    => '',
			),
			array(
				'title'       => 'Безопасность сайта',
				'description' => 'Комплексные меры защиты: от регулярных аудитов и установки SSL до нейтрализации вредоносного кода и настройки систем обнаружения вторжений.',
				'icon_key'    => 'security',
				'icon_svg'    => '',
			),
			array(
				'title'       => 'Оптимизация скорости сайта',
				'description' => 'Ускорение загрузки за счёт оптимизации кода, баз данных и изображений. Повышение показателей производительности и удобства для пользователей.',
				'icon_key'    => 'speed',
				'icon_svg'    => '',
			),
			array(
				'title'       => 'Подключение к CDN',
				'description' => 'Интеграция с сетью доставки контента для быстрой загрузки и надёжности при высоких нагрузках. Настройка кэширования и балансировки.',
				'icon_key'    => 'cdn',
				'icon_svg'    => '',
			),
			array(
				'title'       => 'Контекстная реклама',
				'description' => 'Создание и управление кампаниями в поисковых системах и соцсетях. Анализ эффективности и оптимизация бюджета для увеличения конверсий.',
				'icon_key'    => 'ads',
				'icon_svg'    => '',
			),
			array(
				'title'       => 'Техническая поддержка сайта',
				'description' => 'Оперативное реагирование на любые сбои, плановые обновления и контроль стабильности. Гарантия бесперебойной работы вашего ресурса.',
				'icon_key'    => 'support',
				'icon_svg'    => '',
			),
			array(
				'title'       => 'SEO аудит сайта',
				'description' => 'Детальный анализ структуры, контента и технических параметров. Рекомендации по улучшению позиций сайта в поисковой выдаче.',
				'icon_key'    => 'seo',
				'icon_svg'    => '',
			),
		),
		'pricing_title'       => 'Стоимость услуг',
		'pricing_lead'        => "Индивидуальный подход к каждой задаче.\nМоя базовая ставка:",
		'pricing_rate'        => '2000 ₽/час',
		'pricing_bullets'     => array(
			array(
				'text'     => 'Чем точнее описана задача, тем быстрее она будет выполнена.',
				'icon_key' => 'focus',
			),
			array(
				'text'     => 'Финальная стоимость зависит от сложности проекта и ваших ожиданий.',
				'icon_key' => 'scope',
			),
			array(
				'text'     => 'Первичная консультация — бесплатно.',
				'icon_key' => 'chat',
			),
		),
		'pricing_button_text' => 'Обсудить проект',
		'pricing_button_url'  => 'https://krivoshein.site/contacts/',
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
			'inner'   => '<path d="M18.1,28.3c-2,0-2.9-0.3-4.4-1.5c-1,1.3-4.2,2.3-4.3,0.6c0-1.3-0.3-2.4-0.6-3.6C8.4,22.4,8,20.8,8,18.4c0-5.7,4.7-10,10.2-10S28,13,28,18.4C27.9,23.9,23.6,28.3,18.1,28.3z M18.2,13.3c-2.7-0.1-4.8,1.7-5.2,4.7c-0.4,2.4,0.3,5.4,0.9,5.5c0.3,0.1,0.9-0.5,1.4-0.9c0.7,0.5,1.5,0.8,2.4,0.9c2.8,0.1,5.2-2,5.4-4.8C23.1,15.9,20.9,13.5,18.2,13.3L18.2,13.3z"/>',
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
	$icons = array(
		'web-dev'  => '<polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline><line x1="14" y1="4" x2="10" y2="20"></line>',
		'docker'   => '<rect x="3" y="4" width="8" height="7" rx="1"></rect><rect x="13" y="4" width="8" height="7" rx="1"></rect><rect x="8" y="13" width="8" height="7" rx="1"></rect><line x1="7" y1="11" x2="12" y2="13"></line><line x1="17" y1="11" x2="12" y2="13"></line>',
		'vps'      => '<rect x="4" y="4" width="16" height="6" rx="1"></rect><rect x="4" y="14" width="16" height="6" rx="1"></rect><circle cx="8" cy="7" r="0.8" style="fill:currentColor;stroke:none"></circle><circle cx="8" cy="17" r="0.8" style="fill:currentColor;stroke:none"></circle>',
		'max-bot'  => '<path d="M21 15a2 2 0 0 1-2 2H8l-4 4V5a2 2 0 0 1 2-2h13a2 2 0 0 1 2 2Z"></path><circle cx="9" cy="10" r="0.8" style="fill:currentColor;stroke:none"></circle><circle cx="12" cy="10" r="0.8" style="fill:currentColor;stroke:none"></circle><circle cx="15" cy="10" r="0.8" style="fill:currentColor;stroke:none"></circle>',
		'domain'   => '<circle cx="12" cy="12" r="9"></circle><path d="M3 12h18"></path><path d="M12 3a14 14 0 0 1 0 18"></path><path d="M12 3a14 14 0 0 0 0 18"></path>',
		'cloud'    => '<path d="M7 18h10a4 4 0 0 0 .4-8A6 6 0 0 0 6 11a3.5 3.5 0 0 0 1 7Z"></path>',
		'security' => '<path d="M12 3l7 3v5c0 4.5-2.9 8.1-7 10-4.1-1.9-7-5.5-7-10V6l7-3Z"></path><path d="M9.5 12.5l1.8 1.8 3.7-4.1"></path>',
		'speed'    => '<circle cx="12" cy="12" r="8"></circle><line x1="12" y1="12" x2="16.5" y2="9.5"></line><line x1="12" y1="12" x2="12" y2="7"></line>',
		'cdn'      => '<rect x="3" y="6" width="7" height="5" rx="1"></rect><rect x="14" y="4" width="7" height="5" rx="1"></rect><rect x="14" y="15" width="7" height="5" rx="1"></rect><line x1="10" y1="8.5" x2="14" y2="6.5"></line><line x1="10" y1="8.5" x2="14" y2="17.5"></line>',
		'ads'      => '<rect x="4" y="6" width="16" height="12" rx="2"></rect><path d="M8 10.5h8"></path><path d="M8 13.5h5"></path>',
		'support'  => '<circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1 1 0 0 0 .2 1.1l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1 1 0 0 0-1.1-.2 1 1 0 0 0-.6.9V20a2 2 0 1 1-4 0v-.2a1 1 0 0 0-.6-.9 1 1 0 0 0-1.1.2l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1 1 0 0 0 .2-1.1 1 1 0 0 0-.9-.6H4a2 2 0 1 1 0-4h.2a1 1 0 0 0 .9-.6 1 1 0 0 0-.2-1.1l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1 1 0 0 0 1.1.2 1 1 0 0 0 .6-.9V4a2 2 0 1 1 4 0v.2a1 1 0 0 0 .6.9 1 1 0 0 0 1.1-.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1 1 0 0 0-.2 1.1 1 1 0 0 0 .9.6H20a2 2 0 1 1 0 4h-.2a1 1 0 0 0-.9.6Z"></path>',
		'seo'      => '<circle cx="11" cy="11" r="6"></circle><line x1="16" y1="16" x2="21" y2="21"></line>',
	);

	return $icons[ $key ] ?? '';
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
		'docker'   => 'Docker',
		'vps'      => 'VPS',
		'max-bot'  => 'MAX бот',
		'domain'   => 'Домен',
		'cloud'    => 'Облако',
		'security' => 'Безопасность',
		'speed'    => 'Скорость',
		'cdn'      => 'CDN',
		'ads'      => 'Реклама',
		'support'  => 'Поддержка',
		'seo'      => 'SEO',
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
							'instructions' => 'Внутреннее содержимое SVG. Если заполнено — перекрывает пресет.',
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

		if ( $value === null || $value === false || $value === '' || $value === array() ) {
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

	return sprintf(
		'<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true">%s</svg>',
		wp_kses( $inner, krv_services_landing_svg_kses() )
	);
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
					<img class="krv-landing-avatar" src="<?php echo esc_url( krv_services_landing_resolve_avatar_url( $data['profile_avatar'] ?? '' ) ); ?>" alt="<?php echo esc_attr( (string) $data['profile_name'] ); ?>">
				</div>

				<h2 class="krv-landing-title"><?php echo esc_html( (string) $data['profile_name'] ); ?></h2>

				<p class="krv-landing-lead"><?php echo esc_html( (string) $data['profile_lead'] ); ?></p>

				<?php if ( ! empty( $data['profile_meta_lines'] ) && is_array( $data['profile_meta_lines'] ) ) : ?>
					<div class="krv-landing-meta">
						<?php foreach ( $data['profile_meta_lines'] as $meta_line ) : ?>
							<?php
							$line = is_array( $meta_line ) ? (string) ( $meta_line['line'] ?? '' ) : (string) $meta_line;
							if ( $line === '' ) {
								continue;
							}
							?>
							<span class="krv-landing-meta-line"><?php echo esc_html( $line ); ?></span>
						<?php endforeach; ?>
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
					<div class="krv-landing-services-grid">
						<?php foreach ( $data['services_items'] as $service_item ) : ?>
							<?php
							if ( ! is_array( $service_item ) ) {
								continue;
							}

							$title = (string) ( $service_item['title'] ?? '' );
							if ( $title === '' ) {
								continue;
							}
							?>
							<div class="krv-landing-service-item">
								<?php echo krv_services_landing_render_service_icon( $service_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<h3><?php echo esc_html( $title ); ?></h3>
								<p><?php echo esc_html( (string) ( $service_item['description'] ?? '' ) ); ?></p>
							</div>
						<?php endforeach; ?>
					</div>
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

				<a class="krv-landing-pricing-button" href="<?php echo esc_url( (string) $data['pricing_button_url'] ); ?>" rel="noopener">
					<?php echo esc_html( (string) $data['pricing_button_text'] ); ?>
				</a>
			</div>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

add_shortcode( 'krv_services_landing', 'krv_services_landing_render' );