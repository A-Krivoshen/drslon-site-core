<?php
/**
 * ACF options and canonical content for the /prays-list/ shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACF storage key for price list options.
 */
function krv_price_list_option_id(): string {
	return 'krv-price-list';
}

/**
 * Convert a list of strings into ACF repeater rows.
 *
 * @param array<int, string> $items Text values.
 * @return array<int, array{text: string}>
 */
function krv_price_list_text_rows( array $items ): array {
	return array_map(
		static function ( string $text ): array {
			return array( 'text' => $text );
		},
		$items
	);
}

/**
 * Canonical price list content used for ACF seeding and fallback rendering.
 *
 * @return array<string, mixed>
 */
function krv_price_list_get_defaults(): array {
	return array(
		'hero_badge'                    => 'Прозрачный прайс • сайты / серверы / реклама / боты / AI',
		'hero_title'                    => 'Стоимость сайтов, поддержки, рекламы и технических работ',
		'hero_subtitle'                 => 'Лендинги • WordPress • VPS • Яндекс Директ • Боты • AI-ready',
		'hero_lead'                     => 'Ниже — базовые ориентиры по стоимости. Для простой задачи с понятным объёмом можно отталкиваться от указанной цены; интеграции, срочность и нестандартная логика считаются отдельно.',
		'hero_phone'                    => '+7 (963) 664-16-15',
		'hero_email'                    => 'aleksey@krivoshein.site',
		'hero_panel_title'              => 'Не знаете, что именно нужно?',
		'hero_panel_text'               => 'Это нормально. Иногда нужен новый сайт, а иногда достаточно починить формы, цели в Метрике, скорость, рекламу или сервер.',
		'hero_panel_note'               => 'Коротко в <strong>Telegram</strong> или <strong>MAX</strong> — бесплатно. Полный технический разбор сайта — <strong>от 5 000 ₽</strong>.',
		'hero_panel_quick_label'        => 'Бесплатно уточнить задачу',
		'hero_panel_diagnostic_label'   => 'Заказать диагностику',
		'hero_panel_diagnostic_meta'    => 'от 5 000 ₽ · WordPress, формы, Метрика, скорость, рекомендации',
		'hero_panel_anchor_label'       => 'Что входит в диагностику ↓',
		'hero_panel_anchor'             => '#krv-package-diagnostic',
		'trust_items'                  => krv_price_list_text_rows(
			array(
				'Один специалист — без менеджеров и агентской наценки',
				'Ответ обычно в течение рабочего дня',
				'Работа по договору, безнал и закрывающие документы',
			)
		),
		'show_scenarios'               => 1,
		'scenarios_title'              => 'Выберите направление',
		'scenarios_text'               => 'Шесть отдельных лендингов подробно объясняют формат работы, цены и результат по каждому направлению.',
		'routes'                       => array(
			array(
				'kicker'         => 'One-page',
				'title'          => 'Нужен лендинг',
				'text'           => 'Быстрый статический one-page или WordPress-лендинг под услугу, продукт, B2B и рекламу.',
				'link_mode'      => 'url',
				'url'            => 'https://landing.krivoshein.site/',
				'contacts_topic' => 'general',
				'new_tab'        => 1,
				'go_label'       => 'Лендинги под ключ ↗',
			),
			array(
				'kicker'         => 'WordPress',
				'title'          => 'Нужны доработки или поддержка',
				'text'           => 'Правки, ACF-блоки, скорость, безопасность и ежемесячное сопровождение сайта.',
				'link_mode'      => 'url',
				'url'            => 'https://wordpress.krivoshein.site/',
				'contacts_topic' => 'support',
				'new_tab'        => 1,
				'go_label'       => 'WordPress и поддержка ↗',
			),
			array(
				'kicker'         => 'Инфраструктура',
				'title'          => 'Нужен VPS под ключ',
				'text'           => 'Подбор сервера, Linux, Nginx, Docker, безопасность, бэкапы и запуск проекта.',
				'link_mode'      => 'url',
				'url'            => 'https://vps.krivoshein.site/',
				'contacts_topic' => 'general',
				'new_tab'        => 1,
				'go_label'       => 'VPS и серверы ↗',
			),
			array(
				'kicker'         => 'Реклама',
				'title'          => 'Нужны заявки из Директа',
				'text'           => 'Аудит, настройка и ведение Яндекс Директа с понятными отчётами и аналитикой.',
				'link_mode'      => 'url',
				'url'            => 'https://direct.krivoshein.site/',
				'contacts_topic' => 'general',
				'new_tab'        => 1,
				'go_label'       => 'Яндекс Директ ↗',
			),
			array(
				'kicker'         => 'Автоматизация',
				'title'          => 'Нужен бот для заявок',
				'text'           => 'MAX-боты, сценарии, уведомления и интеграции с таблицами, сайтом или CRM.',
				'link_mode'      => 'url',
				'url'            => 'https://bots.krivoshein.site/',
				'contacts_topic' => 'general',
				'new_tab'        => 1,
				'go_label'       => 'Боты для MAX ↗',
			),
			array(
				'kicker'         => 'AI и поиск',
				'title'          => 'Подготовить сайт к нейропоиску',
				'text'           => 'Структура, Schema.org, FAQ, llms.txt, техническое SEO и интеграции с ботами.',
				'link_mode'      => 'url',
				'url'            => 'https://ai-ready.krivoshein.site/',
				'contacts_topic' => 'general',
				'new_tab'        => 1,
				'go_label'       => 'AI-ready сайт ↗',
			),
		),
		'landings_label'                => 'Все направления:',
		'landings'                     => array(
			array( 'label' => 'landing', 'url' => 'https://landing.krivoshein.site/' ),
			array( 'label' => 'wordpress', 'url' => 'https://wordpress.krivoshein.site/' ),
			array( 'label' => 'vps', 'url' => 'https://vps.krivoshein.site/' ),
			array( 'label' => 'direct', 'url' => 'https://direct.krivoshein.site/' ),
			array( 'label' => 'bots', 'url' => 'https://bots.krivoshein.site/' ),
			array( 'label' => 'ai-ready', 'url' => 'https://ai-ready.krivoshein.site/' ),
		),
		'show_packages'                => 1,
		'packages_title'               => 'Популярные форматы',
		'packages_text'                => 'Быстрые ориентиры для самых частых задач. Детальная разбивка — ниже по направлениям.',
		'packages'                     => array(
			array(
				'featured'       => 1,
				'anchor_id'      => 'krv-package-diagnostic',
				'kicker'         => 'Быстрый старт',
				'title'          => 'Диагностика сайта',
				'price'          => 'от 5 000 ₽',
				'text'           => 'Подходит, если сайт уже есть, но непонятно, почему нет заявок, всё тормозит или реклама работает странно.',
				'features'       => krv_price_list_text_rows(
					array(
						'проверка WordPress и плагинов',
						'формы, почта, заявки',
						'Метрика и цели',
						'скорость и базовые ошибки',
						'список рекомендаций',
					)
				),
				'cta_label'      => 'Заказать диагностику',
				'cta_type'       => 'diagnostic',
				'cta_custom_url' => '',
				'cta_new_tab'    => 0,
				'cta_style'      => 'primary',
			),
			array(
				'featured'       => 0,
				'anchor_id'      => '',
				'kicker'         => 'Самый частый вариант',
				'title'          => 'Ремонт и доработка сайта',
				'price'          => 'от 10 000 ₽',
				'text'           => 'Когда сайт не надо переделывать с нуля, но пора привести его в нормальный визуальный и технический порядок.',
				'features'       => krv_price_list_text_rows(
					array(
						'исправление ошибок',
						'доработка блоков и страниц',
						'адаптация под мобильные',
						'ускорение WordPress',
						'настройка форм и аналитики',
					)
				),
				'cta_label'      => 'Обсудить ремонт',
				'cta_type'       => 'repair',
				'cta_custom_url' => '',
				'cta_new_tab'    => 0,
				'cta_style'      => 'secondary',
			),
			array(
				'featured'       => 0,
				'anchor_id'      => 'krv-package-support',
				'kicker'         => 'Регулярно',
				'title'          => 'Техническая поддержка',
				'price'          => 'от 20 000 ₽ / мес',
				'text'           => 'Для проектов, где сайт должен спокойно работать без постоянного режима «ой, всё упало».',
				'features'       => krv_price_list_text_rows(
					array(
						'обновления WordPress',
						'резервные копии',
						'мелкие правки',
						'контроль ошибок',
						'консультации по развитию',
					)
				),
				'cta_label'      => 'Запросить поддержку',
				'cta_type'       => 'support',
				'cta_custom_url' => '',
				'cta_new_tab'    => 0,
				'cta_style'      => 'secondary',
			),
		),
		'show_prices'                  => 1,
		'prices_title'                 => 'Услуги и цены',
		'prices_text'                  => 'Табы повторяют реальные направления и цены на шести специализированных лендингах.',
		'price_tabs'                   => array(
			array(
				'tab_label'             => 'Лендинги',
				'tab_id'                => 'landings',
				'heading'               => 'Лендинги под ключ',
				'intro'                 => 'Статика — когда важны скорость и простой деплой. WordPress — когда нужен редактор контента.',
				'landing_label'         => 'Лендинг landing.krivoshein.site ↗',
				'landing_url'           => 'https://landing.krivoshein.site/',
				'items'                 => array(
					array(
						'style'       => 'card',
						'kicker'      => 'Базовый',
						'title'       => 'Статический лендинг-визитка',
						'price'       => 'от 25 000 ₽',
						'price_note'  => '',
						'text'        => '4–6 смысловых блоков, адаптив, базовое SEO, контакты и деплой на хостинг.',
						'cta_label'   => '',
						'cta_url'     => '',
						'cta_new_tab' => 0,
					),
					array(
						'style'       => 'card',
						'kicker'      => 'Полный',
						'title'       => 'Статический лендинг с SEO и блоками',
						'price'       => 'от 45 000 ₽',
						'price_note'  => '',
						'text'        => 'Оффер, кейсы, цены, FAQ, JSON-LD, sitemap, PWA, cookie и аналитика.',
						'cta_label'   => '',
						'cta_url'     => '',
						'cta_new_tab' => 0,
					),
					array(
						'style'       => 'card',
						'kicker'      => 'С редактором',
						'title'       => 'Лендинг на WordPress',
						'price'       => 'от 50 000 ₽',
						'price_note'  => '',
						'text'        => 'WordPress, управляемые блоки, формы, базовая аналитика и удобное редактирование контента.',
						'cta_label'   => '',
						'cta_url'     => '',
						'cta_new_tab' => 0,
					),
				),
				'panel_features'       => array(),
				'panel_note'           => 'Мультиязык, сложные анимации и интеграции считаются отдельно.',
				'panel_note_link_label'=> '',
				'panel_note_link_url'  => '',
				'panel_note_new_tab'   => 0,
			),
			array(
				'tab_label'             => 'WordPress',
				'tab_id'                => 'wordpress',
				'heading'               => 'WordPress: разработка и поддержка',
				'intro'                 => 'Разовые доработки и регулярное сопровождение существующих проектов.',
				'landing_label'         => 'Лендинг wordpress.krivoshein.site ↗',
				'landing_url'           => 'https://wordpress.krivoshein.site/',
				'items'                 => array(
					array( 'style' => 'card', 'kicker' => 'Точечные задачи', 'title' => 'Доработки существующего сайта', 'price' => 'от 5 000 ₽', 'price_note' => 'минимальный чек', 'text' => 'Правки, новые блоки, формы, переносы, ускорение и настройка плагинов.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
					array( 'style' => 'card', 'kicker' => 'Удобная админка', 'title' => 'ACF и кастомные блоки', 'price' => 'от 15 000 ₽', 'price_note' => '', 'text' => 'Повторяемые блоки, карточки, каталоги и управляемая структура контента.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
					array( 'style' => 'card', 'kicker' => 'Регулярно', 'title' => 'Техническая поддержка', 'price' => 'от 20 000 ₽ / месяц', 'price_note' => '', 'text' => 'Обновления, бэкапы, безопасность, мелкие правки и контроль ошибок.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
					array( 'style' => 'card', 'kicker' => 'Сайт компании', 'title' => 'Корпоративный сайт', 'price' => 'от 90 000 ₽', 'price_note' => '', 'text' => 'Услуги, контакты, формы, блог, базовая SEO-структура и понятная админка.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
					array( 'style' => 'card', 'kicker' => 'Расширенный проект', 'title' => 'Сайт со сложной логикой', 'price' => 'от 180 000 ₽', 'price_note' => '', 'text' => 'Каталог, CPT, фильтры, интеграции и нестандартные пользовательские сценарии.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
				),
				'panel_features'       => array(),
				'panel_note'           => '',
				'panel_note_link_label'=> '',
				'panel_note_link_url'  => '',
				'panel_note_new_tab'   => 0,
			),
			array(
				'tab_label'             => 'VPS',
				'tab_id'                => 'vps',
				'heading'               => 'VPS и серверы',
				'intro'                 => 'Настройка сервера под WordPress, Docker, ботов и другие сервисы.',
				'landing_label'         => 'Лендинг vps.krivoshein.site ↗',
				'landing_url'           => 'https://vps.krivoshein.site/',
				'items'                 => array(
					array( 'style' => 'card', 'kicker' => 'Под ключ', 'title' => 'Настройка VPS', 'price' => 'от 10 000 ₽', 'price_note' => '', 'text' => 'Linux, Nginx, PHP, Docker, SSL, файрвол, бэкапы и запуск проекта.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
				),
				'panel_features'       => array(),
				'panel_note'           => 'Стоимость самого VPS оплачивается провайдеру отдельно; подходящие варианты начинаются примерно от 249 ₽ в месяц.',
				'panel_note_link_label'=> 'Сравнить VPS ↗',
				'panel_note_link_url'  => 'https://vps.krivoshein.site/',
				'panel_note_new_tab'   => 1,
			),
			array(
				'tab_label'             => 'Реклама',
				'tab_id'                => 'direct',
				'heading'               => 'Яндекс Директ и реклама',
				'intro'                 => 'Аудит, запуск и регулярная оптимизация рекламы для малого бизнеса.',
				'landing_label'         => 'Лендинг direct.krivoshein.site ↗',
				'landing_url'           => 'https://direct.krivoshein.site/',
				'items'                 => array(
					array( 'style' => 'card', 'kicker' => 'Почасовой формат', 'title' => 'Консультация по рекламе', 'price' => 'от 2 000 ₽ / час', 'price_note' => '', 'text' => 'Разбор кампании, объявлений, бюджета, аналитики и точек роста.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
					array( 'style' => 'card', 'kicker' => 'Разовая услуга', 'title' => 'Аудит Яндекс Директ', 'price' => 'от 10 000 ₽', 'price_note' => '', 'text' => 'Проверка ставок, минус-слов, объявлений, целей и логики кампаний.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
					array( 'style' => 'card', 'kicker' => 'Запуск', 'title' => 'Настройка Яндекс Директ', 'price' => 'от 20 000 ₽', 'price_note' => '', 'text' => 'Подготовка и запуск кампаний под заявки, звонки, трафик или тест спроса.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
					array( 'style' => 'card', 'kicker' => 'Регулярно', 'title' => 'Ведение и оптимизация рекламы', 'price' => 'от 25 000 ₽ / месяц', 'price_note' => '', 'text' => 'Корректировки, чистка, тесты, контроль расходов и понятные отчёты.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
				),
				'panel_features'       => array(),
				'panel_note'           => '',
				'panel_note_link_label'=> '',
				'panel_note_link_url'  => '',
				'panel_note_new_tab'   => 0,
			),
			array(
				'tab_label'             => 'Боты',
				'tab_id'                => 'bots',
				'heading'               => 'Боты для MAX и автоматизация',
				'intro'                 => 'Сбор заявок, уведомления и интеграции с таблицами, сайтом или CRM.',
				'landing_label'         => 'Лендинг bots.krivoshein.site ↗',
				'landing_url'           => 'https://bots.krivoshein.site/',
				'items'                 => array(
					array( 'style' => 'card', 'kicker' => 'MVP', 'title' => 'Простой бот для заявок', 'price' => 'от 40 000 ₽', 'price_note' => '', 'text' => 'Сценарий до 5–7 шагов, сбор контактов и уведомления администратору.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
					array( 'style' => 'card', 'kicker' => 'С интеграциями', 'title' => 'Бот с таблицами, сайтом или CRM', 'price' => 'от 70 000 ₽', 'price_note' => '', 'text' => 'Сложный сценарий, передача данных, статистика и автоматизация процессов.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
					array( 'style' => 'card', 'kicker' => 'Поддержка', 'title' => 'Сопровождение бота', 'price' => 'от 5 000 ₽ / месяц', 'price_note' => '', 'text' => 'Контроль работы, исправления и развитие сценариев после запуска.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
				),
				'panel_features'       => array(),
				'panel_note'           => '',
				'panel_note_link_label'=> '',
				'panel_note_link_url'  => '',
				'panel_note_new_tab'   => 0,
			),
			array(
				'tab_label'             => 'AI-ready',
				'tab_id'                => 'ai-ready',
				'heading'               => 'AI-ready сайт и нейропоиск',
				'intro'                 => 'Техническая и содержательная подготовка без обещаний «топ-1».',
				'landing_label'         => 'Лендинг ai-ready.krivoshein.site ↗',
				'landing_url'           => 'https://ai-ready.krivoshein.site/',
				'items'                 => array(
					array( 'style' => 'card', 'kicker' => 'Start', 'title' => 'Базовая подготовка сайта', 'price' => 'от 10 000 ₽', 'price_note' => '', 'text' => 'Мини-аудит, robots.txt, sitemap.xml, мета-данные и рекомендации по структуре.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
					array( 'style' => 'card', 'kicker' => 'Pro', 'title' => 'Расширенная подготовка', 'price' => 'от 20 000 ₽', 'price_note' => '', 'text' => 'Schema.org, FAQ, доработка страниц услуг и структуры контента.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
					array( 'style' => 'card', 'kicker' => 'Bot-ready', 'title' => 'Сайт + автоматизация', 'price' => 'от 30 000 ₽', 'price_note' => '', 'text' => 'Подготовка сайта и интеграция с ботом, формой заявок, Telegram, MAX или CRM.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
				),
				'panel_features'       => array(),
				'panel_note'           => '',
				'panel_note_link_label'=> '',
				'panel_note_link_url'  => '',
				'panel_note_new_tab'   => 0,
			),
			array(
				'tab_label'             => 'Дополнительно',
				'tab_id'                => 'additional',
				'heading'               => 'Дополнительные работы',
				'intro'                 => 'Отдельные задачи, которые можно добавить к основному проекту.',
				'landing_label'         => '',
				'landing_url'           => '',
				'items'                 => array(
					array( 'style' => 'clean', 'kicker' => '', 'title' => 'Готовая тема и настройка', 'price' => '3 000–10 000 ₽', 'price_note' => '', 'text' => 'Быстрый старт без отдельного дизайна с нуля.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
					array( 'style' => 'clean', 'kicker' => '', 'title' => 'Уникальный дизайн', 'price' => 'от 30 000 ₽', 'price_note' => '', 'text' => 'Когда проекту нужен свой внешний вид, а не аккуратный шаблон.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
					array( 'style' => 'clean', 'kicker' => '', 'title' => 'Базовая SEO-подготовка', 'price' => '10 000–20 000 ₽', 'price_note' => '', 'text' => 'Мета-данные, индексация, карта сайта, robots.txt и базовая структура.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
					array( 'style' => 'mini', 'kicker' => 'Формы и документы', 'title' => 'Подготовка сайта под сбор заявок', 'price' => 'от 12 000 ₽', 'price_note' => '', 'text' => 'Политика обработки данных, согласия, cookie, формы и аналитика.', 'cta_label' => '', 'cta_url' => '', 'cta_new_tab' => 0 ),
				),
				'panel_features'       => krv_price_list_text_rows(
					array(
						'политика обработки персональных данных',
						'согласия и привязка к формам',
						'cookie-уведомление',
						'проверка логики сбора заявок',
					)
				),
				'panel_note'           => 'Отдельная юридическая экспертиза под конкретный бизнес при необходимости считается отдельно.',
				'panel_note_link_label'=> '',
				'panel_note_link_url'  => '',
				'panel_note_new_tab'   => 0,
			),
		),
		'show_stack'                   => 1,
		'stack_title'                  => 'Что обычно входит в работу',
		'stack_items'                  => krv_price_list_text_rows(
			array( 'WordPress', 'Linux', 'Nginx', 'Docker', 'Redis', 'VPS', 'Адаптивность', 'Формы', 'SSL', 'Метрика', 'Яндекс Директ', 'SEO', 'Schema.org', 'MAX-боты', 'Cookies', 'Техподдержка' )
		),
		'show_process'                 => 1,
		'process_title'                => 'Как проходит работа',
		'process_text'                 => 'Сначала разбираем задачу, потом фиксируем понятный объём работ.',
		'process_steps'                => array(
			array( 'title' => 'Коротко обсуждаем задачу', 'text' => 'Что есть сейчас, что не работает, какой результат нужен и какие ограничения по срокам или бюджету.' ),
			array( 'title' => 'Смотрю сайт или проект', 'text' => 'Проверяю WordPress, сервер, формы, аналитику, рекламу или сценарий будущего бота.' ),
			array( 'title' => 'Предлагаю план', 'text' => 'Что делаем сразу, что можно отложить, где есть риски и сколько это будет стоить.' ),
			array( 'title' => 'Делаю и проверяю', 'text' => 'Вношу правки, настраиваю, тестирую и объясняю, что было сделано.' ),
		),
		'show_faq'                     => 1,
		'faq_title'                    => 'Частые вопросы',
		'faq_items'                    => array(
			array( 'question' => 'Почему статический лендинг стоит от 25 000 ₽, а WordPress-лендинг — от 50 000 ₽?', 'answer' => 'Это разные форматы. Статика проще, быстрее и не требует CMS. WordPress нужен, когда важны редактор контента, управляемые блоки, плагины и дальнейшее развитие сайта.' ),
			array( 'question' => 'Можно ли просто посмотреть сайт и сказать, что с ним не так?', 'answer' => 'Да. Для этого подходит диагностика или консультация. После неё становится понятно, нужен новый сайт или достаточно привести в порядок текущий.' ),
			array( 'question' => 'Вы берёте небольшие задачи?', 'answer' => 'Да, если задача понятная и её можно нормально оценить: форма, SSL, блок, Метрика, ошибка WordPress или небольшая доработка.' ),
			array( 'question' => 'Можно ли заказать только рекламу?', 'answer' => 'Можно. Но перед запуском лучше проверить сайт, формы и цели, чтобы реклама не сжигала бюджет из-за технических проблем.' ),
			array( 'question' => 'Бот для MAX или Telegram можно связать с сайтом?', 'answer' => 'Да. Можно настроить сбор заявок, уведомления, таблицы, CRM и передачу данных между сайтом и ботом.' ),
		),
		'show_cta'                     => 1,
		'cta_title'                    => 'Есть задача, но непонятно, с чего начать?',
		'cta_text'                     => 'Напишите коротко, что есть сейчас и какой результат нужен. Я предложу первый нормальный шаг: консультацию, диагностику, доработку, поддержку или отдельную смету.',
		'cta_label'                    => 'Написать по задаче',
		'disclaimer'                   => 'Цены указаны как стартовые ориентиры. Простые задачи с понятным объёмом считаются от указанной суммы; интеграции, срочность, длительная отладка и большой объём правок оцениваются отдельно.',
	);
}

/**
 * Read all settings. Once v2 is seeded, blank fields are respected instead
 * of silently restoring defaults, so sections can be intentionally cleared.
 *
 * @return array<string, mixed>
 */
function krv_price_list_get_settings(): array {
	$defaults = krv_price_list_get_defaults();

	if ( ! function_exists( 'get_field' ) ) {
		return $defaults;
	}

	$settings  = array();
	$is_seeded = (bool) get_option( 'krv_price_list_seeded_v2' );
	$option_id = krv_price_list_option_id();

	foreach ( $defaults as $field => $default ) {
		$value = get_field( $field, $option_id );

		if ( ! $is_seeded && ( $value === false || $value === null || $value === '' ) ) {
			$value = $default;
		}

		$settings[ $field ] = $value;
	}

	return $settings;
}

/**
 * Build a compact ACF field definition.
 *
 * @param string               $key   Field key.
 * @param string               $label Admin label.
 * @param string               $name  Storage name.
 * @param string               $type  ACF field type.
 * @param array<string, mixed> $args  Extra ACF arguments.
 * @return array<string, mixed>
 */
function krv_price_list_acf_field( string $key, string $label, string $name, string $type = 'text', array $args = array() ): array {
	return array_merge(
		array(
			'key'   => $key,
			'label' => $label,
			'name'  => $name,
			'type'  => $type,
		),
		$args
	);
}

/**
 * Resolve a top-level field name to its registered ACF field key.
 * Using field keys during first-time seeding creates the required ACF
 * reference options, including for nested repeaters.
 */
function krv_price_list_acf_key( string $name ): string {
	$overrides = array(
		'hero_panel_quick_label' => 'field_krv_pl_hero_panel_quick',
	);

	return $overrides[ $name ] ?? 'field_krv_pl_' . $name;
}

/**
 * Shared location rule for all price-list field groups.
 *
 * @return array<int, array<int, array<string, string>>>
 */
function krv_price_list_acf_location(): array {
	return array(
		array(
			array(
				'param'    => 'options_page',
				'operator' => '==',
				'value'    => krv_price_list_option_id(),
			),
		),
	);
}

/**
 * Register the options page and grouped ACF controls.
 */
function krv_price_list_register_acf(): void {
	if ( ! function_exists( 'acf_add_options_page' ) || ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_options_page(
		krv_acf_options_page_args(
			krv_price_list_option_id(),
			array(
				'page_title' => 'Прайс-лист',
				'menu_title' => 'Прайс-лист',
				'capability' => 'edit_theme_options',
				'redirect'   => false,
				'position'   => 63,
				'icon_url'   => 'dashicons-money-alt',
			)
		)
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_krv_price_list',
			'title'    => 'Прайс-лист: обложка и контакты',
			'fields'   => array(
				krv_price_list_acf_field( 'field_krv_pl_hero_badge', 'Hero: бейдж', 'hero_badge' ),
				krv_price_list_acf_field( 'field_krv_pl_hero_title', 'Hero: заголовок', 'hero_title' ),
				krv_price_list_acf_field( 'field_krv_pl_hero_subtitle', 'Hero: подзаголовок', 'hero_subtitle' ),
				krv_price_list_acf_field( 'field_krv_pl_hero_lead', 'Hero: вводный текст', 'hero_lead', 'textarea', array( 'rows' => 4 ) ),
				krv_price_list_acf_field( 'field_krv_pl_hero_phone', 'Телефон', 'hero_phone' ),
				krv_price_list_acf_field( 'field_krv_pl_hero_email', 'Email', 'hero_email', 'email' ),
				krv_price_list_acf_field( 'field_krv_pl_hero_panel_title', 'Диагностика: заголовок', 'hero_panel_title' ),
				krv_price_list_acf_field( 'field_krv_pl_hero_panel_text', 'Диагностика: текст', 'hero_panel_text', 'textarea', array( 'rows' => 3 ) ),
				krv_price_list_acf_field( 'field_krv_pl_hero_panel_note', 'Диагностика: выделенный текст', 'hero_panel_note', 'textarea', array( 'rows' => 3, 'instructions' => 'Допустимы безопасные теги, например <strong>.' ) ),
				krv_price_list_acf_field( 'field_krv_pl_hero_panel_quick', 'Диагностика: подпись мессенджеров', 'hero_panel_quick_label' ),
				krv_price_list_acf_field( 'field_krv_pl_hero_panel_diagnostic_label', 'Диагностика: текст кнопки', 'hero_panel_diagnostic_label' ),
				krv_price_list_acf_field( 'field_krv_pl_hero_panel_diagnostic_meta', 'Диагностика: цена под кнопкой', 'hero_panel_diagnostic_meta' ),
				krv_price_list_acf_field( 'field_krv_pl_hero_panel_anchor_label', 'Диагностика: текст якорной ссылки', 'hero_panel_anchor_label' ),
				krv_price_list_acf_field( 'field_krv_pl_hero_panel_anchor', 'Диагностика: якорь', 'hero_panel_anchor', 'text', array( 'instructions' => 'Например: #krv-package-diagnostic' ) ),
				krv_price_list_acf_field(
					'field_krv_pl_trust_items',
					'Пункты доверия',
					'trust_items',
					'repeater',
					array(
						'layout'       => 'table',
						'button_label' => 'Добавить пункт',
						'sub_fields'   => array(
							krv_price_list_acf_field( 'field_krv_pl_trust_text', 'Текст', 'text' ),
						),
					)
				),
				krv_price_list_acf_field( 'field_krv_pl_disclaimer', 'Дисклеймер внизу страницы', 'disclaimer', 'textarea', array( 'rows' => 4 ) ),
			),
			'location' => krv_price_list_acf_location(),
		)
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_krv_price_list_routes',
			'title'    => 'Прайс-лист: 6 направлений',
			'fields'   => array(
				krv_price_list_acf_field( 'field_krv_pl_show_scenarios', 'Показывать секцию', 'show_scenarios', 'true_false', array( 'ui' => 1, 'default_value' => 1 ) ),
				krv_price_list_acf_field( 'field_krv_pl_scenarios_title', 'Заголовок секции', 'scenarios_title' ),
				krv_price_list_acf_field( 'field_krv_pl_scenarios_text', 'Вводный текст', 'scenarios_text', 'textarea', array( 'rows' => 3 ) ),
				krv_price_list_acf_field(
					'field_krv_pl_routes',
					'Карточки направлений',
					'routes',
					'repeater',
					array(
						'layout'       => 'block',
						'button_label' => 'Добавить направление',
						'collapsed'    => 'field_krv_pl_route_title',
						'sub_fields'   => array(
							krv_price_list_acf_field( 'field_krv_pl_route_kicker', 'Метка', 'kicker' ),
							krv_price_list_acf_field( 'field_krv_pl_route_title', 'Заголовок', 'title' ),
							krv_price_list_acf_field( 'field_krv_pl_route_text', 'Описание', 'text', 'textarea', array( 'rows' => 3 ) ),
							krv_price_list_acf_field( 'field_krv_pl_route_link_mode', 'Тип ссылки', 'link_mode', 'select', array( 'choices' => array( 'url' => 'URL / лендинг', 'contacts' => 'Страница контактов' ), 'default_value' => 'url', 'ui' => 1 ) ),
							krv_price_list_acf_field( 'field_krv_pl_route_url', 'URL', 'url', 'url', array( 'conditional_logic' => array( array( array( 'field' => 'field_krv_pl_route_link_mode', 'operator' => '==', 'value' => 'url' ) ) ) ) ),
							krv_price_list_acf_field( 'field_krv_pl_route_contacts_topic', 'Тема обращения', 'contacts_topic', 'select', array( 'choices' => array( 'general' => 'Общая', 'diagnostic' => 'Диагностика', 'repair' => 'Ремонт', 'support' => 'Поддержка' ), 'default_value' => 'general', 'conditional_logic' => array( array( array( 'field' => 'field_krv_pl_route_link_mode', 'operator' => '==', 'value' => 'contacts' ) ) ) ) ),
							krv_price_list_acf_field( 'field_krv_pl_route_new_tab', 'Открывать в новой вкладке', 'new_tab', 'true_false', array( 'ui' => 1, 'default_value' => 1 ) ),
							krv_price_list_acf_field( 'field_krv_pl_route_go_label', 'Текст ссылки', 'go_label' ),
						),
					)
				),
				krv_price_list_acf_field( 'field_krv_pl_landings_label', 'Подпись списка ссылок', 'landings_label' ),
				krv_price_list_acf_field(
					'field_krv_pl_landings',
					'Быстрые ссылки на лендинги',
					'landings',
					'repeater',
					array(
						'layout'       => 'table',
						'button_label' => 'Добавить лендинг',
						'sub_fields'   => array(
							krv_price_list_acf_field( 'field_krv_pl_landing_label', 'Название', 'label' ),
							krv_price_list_acf_field( 'field_krv_pl_landing_url', 'URL', 'url', 'url' ),
						),
					)
				),
			),
			'location' => krv_price_list_acf_location(),
		)
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_krv_price_list_packages',
			'title'    => 'Прайс-лист: популярные пакеты',
			'fields'   => array(
				krv_price_list_acf_field( 'field_krv_pl_show_packages', 'Показывать секцию', 'show_packages', 'true_false', array( 'ui' => 1, 'default_value' => 1 ) ),
				krv_price_list_acf_field( 'field_krv_pl_packages_title', 'Заголовок секции', 'packages_title' ),
				krv_price_list_acf_field( 'field_krv_pl_packages_text', 'Вводный текст', 'packages_text', 'textarea', array( 'rows' => 3 ) ),
				krv_price_list_acf_field(
					'field_krv_pl_packages',
					'Пакеты',
					'packages',
					'repeater',
					array(
						'layout'       => 'block',
						'button_label' => 'Добавить пакет',
						'collapsed'    => 'field_krv_pl_package_title',
						'sub_fields'   => array(
							krv_price_list_acf_field( 'field_krv_pl_package_featured', 'Выделить пакет', 'featured', 'true_false', array( 'ui' => 1 ) ),
							krv_price_list_acf_field( 'field_krv_pl_package_anchor', 'HTML-якорь', 'anchor_id', 'text', array( 'instructions' => 'Без #, например krv-package-support.' ) ),
							krv_price_list_acf_field( 'field_krv_pl_package_kicker', 'Метка', 'kicker' ),
							krv_price_list_acf_field( 'field_krv_pl_package_title', 'Название', 'title' ),
							krv_price_list_acf_field( 'field_krv_pl_package_price', 'Цена', 'price' ),
							krv_price_list_acf_field( 'field_krv_pl_package_text', 'Описание', 'text', 'textarea', array( 'rows' => 3 ) ),
							krv_price_list_acf_field( 'field_krv_pl_package_features', 'Что входит', 'features', 'repeater', array( 'layout' => 'table', 'button_label' => 'Добавить пункт', 'sub_fields' => array( krv_price_list_acf_field( 'field_krv_pl_package_feature_text', 'Текст', 'text' ) ) ) ),
							krv_price_list_acf_field( 'field_krv_pl_package_cta_label', 'Текст кнопки', 'cta_label' ),
							krv_price_list_acf_field( 'field_krv_pl_package_cta_type', 'Ссылка кнопки', 'cta_type', 'select', array( 'choices' => array( 'diagnostic' => 'Контакты: диагностика', 'repair' => 'Контакты: ремонт', 'support' => 'Контакты: поддержка', 'general' => 'Контакты: общая', 'custom' => 'Свой URL', 'none' => 'Без кнопки' ), 'default_value' => 'general', 'ui' => 1 ) ),
							krv_price_list_acf_field( 'field_krv_pl_package_cta_custom', 'Свой URL', 'cta_custom_url', 'url', array( 'conditional_logic' => array( array( array( 'field' => 'field_krv_pl_package_cta_type', 'operator' => '==', 'value' => 'custom' ) ) ) ) ),
							krv_price_list_acf_field( 'field_krv_pl_package_cta_new_tab', 'Открывать в новой вкладке', 'cta_new_tab', 'true_false', array( 'ui' => 1 ) ),
							krv_price_list_acf_field( 'field_krv_pl_package_cta_style', 'Стиль кнопки', 'cta_style', 'select', array( 'choices' => array( 'primary' => 'Основная', 'secondary' => 'Вторичная' ), 'default_value' => 'secondary' ) ),
						),
					)
				),
			),
			'location' => krv_price_list_acf_location(),
		)
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_krv_price_list_tabs',
			'title'    => 'Прайс-лист: услуги и цены',
			'fields'   => array(
				krv_price_list_acf_field( 'field_krv_pl_show_prices', 'Показывать секцию', 'show_prices', 'true_false', array( 'ui' => 1, 'default_value' => 1 ) ),
				krv_price_list_acf_field( 'field_krv_pl_prices_title', 'Заголовок секции', 'prices_title' ),
				krv_price_list_acf_field( 'field_krv_pl_prices_text', 'Вводный текст', 'prices_text', 'textarea', array( 'rows' => 3 ) ),
				krv_price_list_acf_field(
					'field_krv_pl_price_tabs',
					'Табы',
					'price_tabs',
					'repeater',
					array(
						'layout'       => 'block',
						'button_label' => 'Добавить таб',
						'collapsed'    => 'field_krv_pl_tab_heading',
						'sub_fields'   => array(
							krv_price_list_acf_field( 'field_krv_pl_tab_label', 'Название кнопки таба', 'tab_label' ),
							krv_price_list_acf_field( 'field_krv_pl_tab_id', 'ID таба', 'tab_id', 'text', array( 'instructions' => 'Латиницей, например wordpress. Дубликаты будут исправлены автоматически.' ) ),
							krv_price_list_acf_field( 'field_krv_pl_tab_heading', 'Заголовок', 'heading' ),
							krv_price_list_acf_field( 'field_krv_pl_tab_intro', 'Вводный текст', 'intro', 'textarea', array( 'rows' => 3 ) ),
							krv_price_list_acf_field( 'field_krv_pl_tab_landing_label', 'Текст ссылки на лендинг', 'landing_label' ),
							krv_price_list_acf_field( 'field_krv_pl_tab_landing_url', 'URL лендинга', 'landing_url', 'url' ),
							krv_price_list_acf_field(
								'field_krv_pl_tab_items',
								'Услуги',
								'items',
								'repeater',
								array(
									'layout'       => 'block',
									'button_label' => 'Добавить услугу',
									'collapsed'    => 'field_krv_pl_item_title',
									'sub_fields'   => array(
										krv_price_list_acf_field( 'field_krv_pl_item_style', 'Тип карточки', 'style', 'select', array( 'choices' => array( 'card' => 'Карточка', 'clean' => 'Строка без фона', 'mini' => 'Компактная карточка' ), 'default_value' => 'card', 'ui' => 1 ) ),
										krv_price_list_acf_field( 'field_krv_pl_item_kicker', 'Метка', 'kicker' ),
										krv_price_list_acf_field( 'field_krv_pl_item_title', 'Название', 'title' ),
										krv_price_list_acf_field( 'field_krv_pl_item_price', 'Цена', 'price' ),
										krv_price_list_acf_field( 'field_krv_pl_item_price_note', 'Примечание к цене', 'price_note' ),
										krv_price_list_acf_field( 'field_krv_pl_item_text', 'Описание', 'text', 'textarea', array( 'rows' => 3 ) ),
										krv_price_list_acf_field( 'field_krv_pl_item_cta_label', 'Текст дополнительной ссылки', 'cta_label' ),
										krv_price_list_acf_field( 'field_krv_pl_item_cta_url', 'URL дополнительной ссылки', 'cta_url', 'text', array( 'instructions' => 'Можно указать полный URL или якорь #section.' ) ),
										krv_price_list_acf_field( 'field_krv_pl_item_cta_new_tab', 'Открывать ссылку в новой вкладке', 'cta_new_tab', 'true_false', array( 'ui' => 1 ) ),
									),
								)
							),
							krv_price_list_acf_field( 'field_krv_pl_tab_features', 'Список после услуг', 'panel_features', 'repeater', array( 'layout' => 'table', 'button_label' => 'Добавить пункт', 'sub_fields' => array( krv_price_list_acf_field( 'field_krv_pl_tab_feature_text', 'Текст', 'text' ) ) ) ),
							krv_price_list_acf_field( 'field_krv_pl_tab_note', 'Примечание', 'panel_note', 'textarea', array( 'rows' => 3 ) ),
							krv_price_list_acf_field( 'field_krv_pl_tab_note_link_label', 'Текст ссылки в примечании', 'panel_note_link_label' ),
							krv_price_list_acf_field( 'field_krv_pl_tab_note_link_url', 'URL ссылки в примечании', 'panel_note_link_url', 'text', array( 'instructions' => 'Полный URL или якорь.' ) ),
							krv_price_list_acf_field( 'field_krv_pl_tab_note_new_tab', 'Открывать ссылку в новой вкладке', 'panel_note_new_tab', 'true_false', array( 'ui' => 1 ) ),
						),
					)
				),
			),
			'location' => krv_price_list_acf_location(),
		)
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_krv_price_list_extras',
			'title'    => 'Прайс-лист: процесс, FAQ и CTA',
			'fields'   => array(
				krv_price_list_acf_field( 'field_krv_pl_show_stack', 'Показывать технологии', 'show_stack', 'true_false', array( 'ui' => 1, 'default_value' => 1 ) ),
				krv_price_list_acf_field( 'field_krv_pl_stack_title', 'Заголовок технологий', 'stack_title' ),
				krv_price_list_acf_field( 'field_krv_pl_stack_items', 'Технологии', 'stack_items', 'repeater', array( 'layout' => 'table', 'button_label' => 'Добавить технологию', 'sub_fields' => array( krv_price_list_acf_field( 'field_krv_pl_stack_item_text', 'Название', 'text' ) ) ) ),
				krv_price_list_acf_field( 'field_krv_pl_show_process', 'Показывать процесс', 'show_process', 'true_false', array( 'ui' => 1, 'default_value' => 1 ) ),
				krv_price_list_acf_field( 'field_krv_pl_process_title', 'Заголовок процесса', 'process_title' ),
				krv_price_list_acf_field( 'field_krv_pl_process_text', 'Вводный текст процесса', 'process_text', 'textarea', array( 'rows' => 2 ) ),
				krv_price_list_acf_field( 'field_krv_pl_process_steps', 'Шаги', 'process_steps', 'repeater', array( 'layout' => 'block', 'button_label' => 'Добавить шаг', 'collapsed' => 'field_krv_pl_process_step_title', 'sub_fields' => array( krv_price_list_acf_field( 'field_krv_pl_process_step_title', 'Заголовок', 'title' ), krv_price_list_acf_field( 'field_krv_pl_process_step_text', 'Описание', 'text', 'textarea', array( 'rows' => 2 ) ) ) ) ),
				krv_price_list_acf_field( 'field_krv_pl_show_faq', 'Показывать FAQ', 'show_faq', 'true_false', array( 'ui' => 1, 'default_value' => 1 ) ),
				krv_price_list_acf_field( 'field_krv_pl_faq_title', 'Заголовок FAQ', 'faq_title' ),
				krv_price_list_acf_field( 'field_krv_pl_faq_items', 'Вопросы', 'faq_items', 'repeater', array( 'layout' => 'block', 'button_label' => 'Добавить вопрос', 'collapsed' => 'field_krv_pl_faq_question', 'sub_fields' => array( krv_price_list_acf_field( 'field_krv_pl_faq_question', 'Вопрос', 'question' ), krv_price_list_acf_field( 'field_krv_pl_faq_answer', 'Ответ', 'answer', 'textarea', array( 'rows' => 3 ) ) ) ) ),
				krv_price_list_acf_field( 'field_krv_pl_show_cta', 'Показывать финальный CTA', 'show_cta', 'true_false', array( 'ui' => 1, 'default_value' => 1 ) ),
				krv_price_list_acf_field( 'field_krv_pl_cta_title', 'CTA: заголовок', 'cta_title' ),
				krv_price_list_acf_field( 'field_krv_pl_cta_text', 'CTA: текст', 'cta_text', 'textarea', array( 'rows' => 3 ) ),
				krv_price_list_acf_field( 'field_krv_pl_cta_label', 'CTA: текст кнопки', 'cta_label' ),
			),
			'location' => krv_price_list_acf_location(),
		)
	);
}

add_action( 'acf/init', 'krv_price_list_register_acf' );

/**
 * Seed editable defaults. v1 values are preserved; v2 only migrates the old
 * stock "Антикризисный" copy and fills controls that did not exist before.
 */
function krv_price_list_seed_defaults(): void {
	if (
		! function_exists( 'acf_add_options_page' ) ||
		! function_exists( 'acf_add_local_field_group' ) ||
		! function_exists( 'update_field' ) ||
		! function_exists( 'get_field' )
	) {
		return;
	}

	$defaults  = krv_price_list_get_defaults();
	$option_id = krv_price_list_option_id();

	if ( ! get_option( 'krv_price_list_seeded_v1' ) ) {
		foreach ( array( 'hero_badge', 'hero_title', 'hero_subtitle', 'hero_lead', 'trust_items', 'disclaimer' ) as $field ) {
			update_field( krv_price_list_acf_key( $field ), $defaults[ $field ], $option_id );
		}

		update_option( 'krv_price_list_seeded_v1', DRSLON_SITE_CORE_VERSION, false );
	}

	if ( get_option( 'krv_price_list_seeded_v2' ) ) {
		return;
	}

	$old_badge = 'Антикризисный прайс • WordPress / Linux / реклама / боты';
	$old_subtitle = 'Разработка сайтов • Поддержка • Linux / DevOps • Яндекс Директ • Автоматизация заявок';
	$old_lead  = 'Сайты на WordPress, доработка проектов, серверы, реклама, аналитика и боты для заявок. Ниже — базовые ориентиры по цене: антикризисный прайс без агентской наценки, финальная смета зависит от задачи и состояния проекта.';
	$old_disclaimer = 'Цены на странице указаны как стартовый антикризисный ориентир. Простые задачи с понятным объемом считаются от указанной суммы. Если проект требует сложной логики, интеграций, срочности, длительной отладки или большого количества правок, стоимость считается отдельно.';

	$current_badge = get_field( 'hero_badge', $option_id );
	$current_subtitle = get_field( 'hero_subtitle', $option_id );
	$current_lead  = get_field( 'hero_lead', $option_id );
	$current_disclaimer = get_field( 'disclaimer', $option_id );

	if ( ! is_string( $current_badge ) || trim( $current_badge ) === $old_badge ) {
		update_field( krv_price_list_acf_key( 'hero_badge' ), $defaults['hero_badge'], $option_id );
	}

	if ( ! is_string( $current_lead ) || trim( $current_lead ) === $old_lead ) {
		update_field( krv_price_list_acf_key( 'hero_lead' ), $defaults['hero_lead'], $option_id );
	}

	if ( ! is_string( $current_subtitle ) || trim( $current_subtitle ) === $old_subtitle ) {
		update_field( krv_price_list_acf_key( 'hero_subtitle' ), $defaults['hero_subtitle'], $option_id );
	}

	if ( ! is_string( $current_disclaimer ) || trim( $current_disclaimer ) === $old_disclaimer ) {
		update_field( krv_price_list_acf_key( 'disclaimer' ), $defaults['disclaimer'], $option_id );
	}

	$v2_fields = array_diff(
		array_keys( $defaults ),
		array( 'hero_badge', 'hero_title', 'hero_subtitle', 'hero_lead', 'trust_items', 'disclaimer' )
	);

	foreach ( $v2_fields as $field ) {
		update_field( krv_price_list_acf_key( $field ), $defaults[ $field ], $option_id );
	}

	update_option( 'krv_price_list_seeded_v2', DRSLON_SITE_CORE_VERSION, false );
}

add_action( 'acf/init', 'krv_price_list_seed_defaults', 25 );

/**
 * Render trust strip HTML from settings.
 *
 * @param array<string, mixed> $settings Widget settings.
 */
function krv_price_list_render_trust_strip( array $settings ): string {
	$items = $settings['trust_items'] ?? array();

	if ( ! is_array( $items ) || empty( $items ) ) {
		return '';
	}

	$out = '<div class="krv-trust-strip" role="list">';

	foreach ( $items as $item ) {
		$text = is_array( $item ) ? trim( (string) ( $item['text'] ?? '' ) ) : '';

		if ( $text !== '' ) {
			$out .= '<span class="krv-trust-item" role="listitem">' . esc_html( $text ) . '</span>';
		}
	}

	$out .= '</div>';

	return $out;
}
