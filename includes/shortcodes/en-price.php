<?php
/**
 * English copy for the price list widget.
 *
 * krv_en_price_settings() is a translated copy of krv_price_list_get_settings().
 * Load this file from en-pages.php. Do not load it from the plugin bootstrap.
 *
 * @package drslon-site-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * English price-list settings. Same keys, numbers, URLs, anchors and flags as the saved list.
 *
 * @return array<string, mixed>
 */
function krv_en_price_settings(): array {
	if ( ! function_exists( 'krv_price_list_get_settings' ) ) {
		return array();
	}

	$copy = krv_en_price_translate_copy( krv_price_list_get_settings(), krv_en_price_string_map() );

	return is_array( $copy ) ? $copy : array();
}

/**
 * Switch price-list UI labels to English when the locale helper is available.
 */
function krv_en_price_prepare(): void {
	if ( function_exists( 'krv_ui_locale_set' ) ) {
		krv_ui_locale_set( 'en' );
	}
}

/**
 * Exact Russian strings from the live price list, replaced in full.
 *
 * @return array<string, string>
 */
function krv_en_price_string_map(): array {
	return array(
		'Прозрачный прайс • сайты / серверы / реклама / боты / AI' => 'Clear prices • sites / servers / ads / bots / AI',
		'Стоимость сайтов, поддержки, рекламы и технических работ' => 'Prices for sites, care, ads and technical work',
		'Лендинги • WordPress • Flask • VPS • Яндекс Директ • Боты • AI-ready' => 'Landings • WordPress • Flask • VPS • Yandex Direct • Bots • AI-ready',
		'Ниже — стартовые ориентиры по стоимости. Простая задача с понятным объёмом обычно укладывается в указанную цену; интеграции, срочность и нестандартная логика считаются отдельно. Финальная смета фиксируется до начала работ — бюджет не растёт в процессе.' => 'Starting prices are below. A simple job with a clear scope usually fits the listed price. Integrations, rush work and custom logic are priced separately. The final estimate is fixed before the work starts, so the budget does not grow mid-job.',
		'Не знаете, что именно нужно?' => 'Not sure what you need?',
		'Это нормально. Иногда нужен новый сайт, а иногда достаточно починить формы, цели в Метрике, скорость, рекламу или сервер.' => 'That is normal. Sometimes you need a new site. Sometimes it is enough to fix forms, Yandex Metrica goals, speed, ads or the server.',
		'Коротко в <strong>Telegram</strong> или <strong>MAX</strong> — бесплатно. Полный технический разбор сайта — <strong>от 5 000 ₽</strong>.' => 'A short question in <strong>Telegram</strong> or <strong>MAX</strong> is free. A full review of the site is <strong>from 5 000 ₽</strong>.',
		'Бесплатно уточнить задачу' => 'Clarify the task for free',
		'Заказать диагностику' => 'Order a diagnostic',
		'от 5 000 ₽ · WordPress, формы, Метрика, скорость, рекомендации' => 'from 5 000 ₽ · WordPress, forms, Yandex Metrica, speed, recommendations',
		'Что входит в диагностику ↓' => 'What the diagnostic includes ↓',
		'Один специалист — без менеджеров и агентской наценки' => 'One specialist. No account managers and no agency markup.',
		'Ответ обычно в течение рабочего дня' => 'A reply usually comes within one working day.',
		'Работа по договору, безнал и закрывающие документы' => 'A contract, a bank transfer and closing documents.',
		'Выберите направление' => 'Choose a direction',
		'Семь отдельных лендингов подробно объясняют формат работы, цены и результат по каждому направлению.' => 'Seven separate landing pages explain the format, the prices and the result for each direction.',
		'Нужен лендинг' => 'Need a landing page',
		'Быстрый статический one-page, лендинг на WordPress или сайт на Flask (Python) под услугу, продукт, B2B и рекламу.' => 'A fast static one-page, a WordPress landing, or a Flask (Python) site for a service, a product, B2B or ads.',
		'Лендинги под ключ ↗' => 'Landing pages ↗',
		'Нужен сайт на Flask' => 'Need a Flask site',
		'Страницы, форма заявок и серверная логика на Python без CMS. Кабинет, оплата и интеграции — отдельно.' => 'Pages, a lead form and server logic in Python, with no CMS. An account area, payments and integrations are separate.',
		'Сайт на Flask ↗' => 'Flask site ↗',
		'Нужны доработки или поддержка' => 'Need changes or care',
		'Правки, ACF-блоки, скорость, безопасность и ежемесячное сопровождение сайта.' => 'Fixes, ACF blocks, speed, security and monthly care for the site.',
		'WordPress и поддержка ↗' => 'WordPress and care ↗',
		'Инфраструктура' => 'Infrastructure',
		'Нужен VPS под ключ' => 'Need a VPS set up',
		'Подбор сервера, Linux, Nginx, Docker, безопасность, бэкапы и запуск проекта.' => 'Server choice, Linux, Nginx, Docker, security, backups and a launch.',
		'VPS и серверы ↗' => 'VPS and servers ↗',
		'Реклама' => 'Ads',
		'Нужны заявки из Директа' => 'Need leads from Yandex Direct',
		'Аудит, настройка и ведение Яндекс Директа с понятными отчётами и аналитикой.' => 'Audit, setup and ongoing Yandex Direct work, with clear reports and analytics.',
		'Яндекс Директ ↗' => 'Yandex Direct ↗',
		'Автоматизация' => 'Automation',
		'Нужен бот для заявок' => 'Need a lead bot',
		'MAX-боты, сценарии, уведомления и интеграции с таблицами, сайтом или CRM.' => 'MAX bots, flows, notices and links to spreadsheets, a site or a CRM.',
		'Боты для MAX ↗' => 'Bots for MAX ↗',
		'AI и поиск' => 'AI and search',
		'Подготовить сайт к нейропоиску' => 'Prepare a site for AI search',
		'Структура, Schema.org, FAQ, llms.txt, техническое SEO и интеграции с ботами.' => 'Structure, Schema.org, FAQ, llms.txt, technical SEO and bot integrations.',
		'AI-ready сайт ↗' => 'AI-ready site ↗',
		'Все направления:' => 'All directions:',
		'Популярные форматы' => 'Common packages',
		'Быстрые ориентиры для самых частых задач. Детальная разбивка — ниже по направлениям.' => 'Quick starting points for the usual jobs. The breakdown by direction is below.',
		'Быстрый старт' => 'Quick start',
		'Диагностика сайта' => 'Site diagnostic',
		'от 5 000 ₽' => 'from 5 000 ₽',
		'Подходит, если сайт уже есть, но непонятно, почему нет заявок, всё тормозит или реклама работает странно.' => 'For a site that already exists, when leads are missing, the site is slow, or the ads behave oddly.',
		'проверка WordPress и плагинов' => 'WordPress and plugin check',
		'формы, почта, заявки' => 'Forms, mail and leads',
		'Метрика и цели' => 'Yandex Metrica and goals',
		'скорость и базовые ошибки' => 'Speed and basic errors',
		'список рекомендаций' => 'A list of recommendations',
		'Самый частый вариант' => 'The usual choice',
		'Ремонт и доработка сайта' => 'Site repair and changes',
		'от 10 000 ₽' => 'from 10 000 ₽',
		'Когда сайт не надо переделывать с нуля, но пора привести его в нормальный визуальный и технический порядок.' => 'When the site does not need a rebuild, but the look and the technical side need to be put in order.',
		'исправление ошибок' => 'Bug fixes',
		'доработка блоков и страниц' => 'Changes to blocks and pages',
		'адаптация под мобильные' => 'Mobile layout',
		'ускорение WordPress' => 'WordPress speed',
		'настройка форм и аналитики' => 'Forms and analytics setup',
		'Обсудить ремонт' => 'Discuss a repair',
		'Регулярно' => 'Ongoing',
		'Техническая поддержка' => 'Technical care',
		'Для проектов, где сайт должен спокойно работать без постоянного режима «ой, всё упало».' => 'For a site that should keep running, without a constant outage.',
		'обновления WordPress' => 'WordPress updates',
		'резервные копии' => 'Backups',
		'мелкие правки' => 'Small fixes',
		'контроль ошибок' => 'Error checks',
		'консультации по развитию' => 'Advice on next steps',
		'Запросить поддержку' => 'Ask about care',
		'Услуги и цены' => 'Services and prices',
		'Направления и цены синхронизированы с семью специализированными лендингами — на каждом направление разобрано подробнее.' => 'The directions and prices match the seven service landing pages, where each direction is explained in detail.',
		'Лендинги' => 'Landings',
		'Лендинги под ключ' => 'Landing pages',
		'Статика — когда важны скорость и простой деплой. WordPress — когда нужен редактор контента. Flask на Python — когда нужна своя серверная логика без CMS.' => 'Static, when speed and a simple deploy matter. WordPress, when you need a content editor. Flask on Python, when you need your own server logic and no CMS.',
		'Лендинг landing.krivoshein.site ↗' => 'Landing page landing.krivoshein.site ↗',
		'Базовый' => 'Basic',
		'Статический лендинг-визитка' => 'Static calling-card landing',
		'от 25 000 ₽' => 'from 25 000 ₽',
		'4–6 смысловых блоков, адаптив, базовое SEO, контакты и деплой на хостинг.' => '4-6 blocks, a mobile layout, basic SEO, contacts and deploy to hosting.',
		'Полный' => 'Full',
		'Статический лендинг с SEO и блоками' => 'Static landing with SEO and blocks',
		'от 45 000 ₽' => 'from 45 000 ₽',
		'Оффер, кейсы, цены, FAQ, JSON-LD, sitemap, PWA, cookie и аналитика.' => 'Offer, cases, prices, FAQ, JSON-LD, sitemap, PWA, a cookie notice and analytics.',
		'С редактором' => 'With an editor',
		'Лендинг на WordPress' => 'WordPress landing',
		'от 50 000 ₽' => 'from 50 000 ₽',
		'WordPress, управляемые блоки, формы, базовая аналитика и удобное редактирование контента.' => 'WordPress, editable blocks, forms, basic analytics and a content editor.',
		'Свой бэкенд' => 'Your own backend',
		'Сайт на Flask (Python)' => 'Flask (Python) site',
		'Страницы, форма заявок и серверная логика на Python и Flask. Без CMS, когда редактор WordPress не нужен.' => 'Pages, a lead form and server logic in Python and Flask. No CMS, when a WordPress editor is not needed.',
		'Мультиязык, сложные анимации и интеграции считаются отдельно.' => 'Extra languages, complex animation and integrations are priced separately.',
		'WordPress: разработка и поддержка' => 'WordPress: build and care',
		'Разовые доработки и регулярное сопровождение существующих проектов.' => 'One-off changes and ongoing care for existing projects.',
		'Лендинг wordpress.krivoshein.site ↗' => 'Landing page wordpress.krivoshein.site ↗',
		'Точечные задачи' => 'Small jobs',
		'Доработки существующего сайта' => 'Changes to an existing site',
		'минимальный чек' => 'Minimum charge',
		'Правки, новые блоки, формы, переносы, ускорение и настройка плагинов.' => 'Fixes, new blocks, forms, moves, speed and plugin setup.',
		'Удобная админка' => 'A clearer admin',
		'ACF и кастомные блоки' => 'ACF and custom blocks',
		'от 15 000 ₽' => 'from 15 000 ₽',
		'Повторяемые блоки, карточки, каталоги и управляемая структура контента.' => 'Repeatable blocks, cards, catalogs and a content structure you can edit.',
		'от 20 000 ₽ / месяц' => 'from 20 000 ₽/month',
		'Обновления, бэкапы, безопасность, мелкие правки и контроль ошибок.' => 'Updates, backups, security, small fixes and error checks.',
		'Конструктор страниц' => 'Page builder',
		'Сайт с ACF Flexible Content' => 'Site with ACF Flexible Content',
		'от 60 000 ₽' => 'from 60 000 ₽',
		'WordPress без Elementor: набор секций в теме, клиент сам добавляет, переставляет и выключает блоки в админке.' => 'WordPress without Elementor. A set of sections in the theme. You add, reorder and turn blocks off in the admin.',
		'Сайт компании' => 'Company',
		'Корпоративный сайт' => 'Company site',
		'от 100 000 ₽' => 'from 100 000 ₽',
		'Услуги, контакты, формы, блог, базовая SEO-структура и понятная админка.' => 'Services, contacts, forms, a blog, a basic SEO structure and a clear admin.',
		'Расширенный проект' => 'A larger project',
		'Сайт со сложной логикой' => 'Site with complex logic',
		'от 180 000 ₽' => 'from 180 000 ₽',
		'Каталог, CPT, фильтры, интеграции и нестандартные пользовательские сценарии.' => 'A catalog, CPT, filters, integrations and custom user flows.',
		'VPS и серверы' => 'VPS and servers',
		'Настройка сервера под WordPress, Docker, ботов и другие сервисы.' => 'Server setup for WordPress, Docker, bots and other services.',
		'Лендинг vps.krivoshein.site ↗' => 'Landing page vps.krivoshein.site ↗',
		'Под ключ' => 'Turnkey',
		'Настройка VPS' => 'VPS setup',
		'Linux, Nginx, PHP, Docker, SSL, файрвол, бэкапы и запуск проекта.' => 'Linux, Nginx, PHP, Docker, SSL, a firewall, backups and a launch.',
		'Миграция' => 'Migration',
		'Перенос сайта на VPS' => 'Moving a site to a VPS',
		'Переезд с хостинга или другого сервера без потери данных и с минимальным простоем.' => 'A move from hosting or another server, with no data loss and minimal downtime.',
		'Разбор' => 'Review',
		'Аудит и спасение сервера' => 'Server audit and rescue',
		'Проверка настроек, поиск причин сбоев, восстановление после взлома и стабилизация.' => 'A settings check, finding the causes of failures, recovery after a hack, and stabilization.',
		'Стоимость самого VPS оплачивается провайдеру отдельно; подходящие варианты начинаются примерно от 249 ₽ в месяц.' => 'The VPS itself is paid to the provider. Suitable options start at about 249 ₽/month. That is the provider\'s price.',
		'Сравнить VPS ↗' => 'Compare VPS ↗',
		'Яндекс Директ и реклама' => 'Yandex Direct and ads',
		'Аудит, запуск и регулярная оптимизация рекламы для малого бизнеса.' => 'Audit, launch and ongoing ad optimization for a small business.',
		'Лендинг direct.krivoshein.site ↗' => 'Landing page direct.krivoshein.site ↗',
		'Почасовой формат' => 'Hourly',
		'Консультация по рекламе' => 'Ads consultation',
		'от 2 000 ₽ / час' => 'from 2 000 ₽/hour',
		'Разбор кампании, объявлений, бюджета, аналитики и точек роста.' => 'A look at the campaign, the ads, the budget, the analytics and what to change.',
		'Разовая услуга' => 'One-off',
		'Аудит Яндекс Директ' => 'Yandex Direct audit',
		'Проверка ставок, минус-слов, объявлений, целей и логики кампаний.' => 'A check of bids, negative keywords, ads, goals and campaign logic.',
		'Запуск' => 'Launch',
		'Настройка Яндекс Директ' => 'Yandex Direct setup',
		'от 20 000 ₽' => 'from 20 000 ₽',
		'Подготовка и запуск кампаний под заявки, звонки, трафик или тест спроса.' => 'Prepare and launch campaigns for leads, calls, traffic or a demand test.',
		'Ведение и оптимизация рекламы' => 'Ongoing ads work',
		'от 25 000 ₽ / месяц' => 'from 25 000 ₽/month',
		'Корректировки, чистка, тесты, контроль расходов и понятные отчёты.' => 'Adjustments, cleanup, tests, spend control and clear reports.',
		'Боты' => 'Bots',
		'Боты для MAX и автоматизация' => 'MAX bots and automation',
		'Сбор заявок, уведомления и интеграции с таблицами, сайтом или CRM.' => 'Lead collection, notices and links to spreadsheets, a site or a CRM.',
		'Лендинг bots.krivoshein.site ↗' => 'Landing page bots.krivoshein.site ↗',
		'Простой бот для заявок' => 'Simple lead bot',
		'от 40 000 ₽' => 'from 40 000 ₽',
		'Сценарий до 5–7 шагов, сбор контактов и уведомления администратору.' => 'A flow of up to 5-7 steps, contact collection and notices to an admin.',
		'С интеграциями' => 'With integrations',
		'Бот с таблицами, сайтом или CRM' => 'Bot with spreadsheets, a site or a CRM',
		'от 70 000 ₽' => 'from 70 000 ₽',
		'Сложный сценарий, передача данных, статистика и автоматизация процессов.' => 'A longer flow, data transfer, stats and process automation.',
		'Поддержка' => 'Care',
		'Сопровождение бота' => 'Bot care',
		'от 5 000 ₽ / месяц' => 'from 5 000 ₽/month',
		'Контроль работы, исправления и развитие сценариев после запуска.' => 'Checks, fixes and changes to the flow after launch.',
		'AI-ready сайт и нейропоиск' => 'AI-ready site and AI search',
		'Техническая и содержательная подготовка без обещаний «топ-1».' => 'Technical and content preparation. No promise of first place in search.',
		'Лендинг ai-ready.krivoshein.site ↗' => 'Landing page ai-ready.krivoshein.site ↗',
		'Базовая подготовка сайта' => 'Basic site preparation',
		'Мини-аудит, robots.txt, sitemap.xml, мета-данные и рекомендации по структуре.' => 'A short audit, robots.txt, sitemap.xml, meta data and notes on structure.',
		'Расширенная подготовка' => 'Extended preparation',
		'Schema.org, FAQ, доработка страниц услуг и структуры контента.' => 'Schema.org, FAQ, changes to service pages and content structure.',
		'Сайт + автоматизация' => 'Site plus automation',
		'от 30 000 ₽' => 'from 30 000 ₽',
		'Подготовка сайта и интеграция с ботом, формой заявок, Telegram, MAX или CRM.' => 'Site preparation and a link to a bot, a lead form, Telegram, MAX or a CRM.',
		'Дополнительно' => 'Extra',
		'Дополнительные работы' => 'Extra work',
		'Отдельные задачи, которые можно добавить к основному проекту.' => 'Separate jobs you can add to the main project.',
		'Готовая тема и настройка' => 'A ready theme and setup',
		'5 000–10 000 ₽' => '5 000–10 000 ₽',
		'Быстрый старт без отдельного дизайна с нуля.' => 'A fast start without a design drawn from scratch.',
		'Уникальный дизайн' => 'A unique design',
		'Когда проекту нужен свой внешний вид, а не аккуратный шаблон.' => 'When the project needs its own look, not a tidy template.',
		'Базовая SEO-подготовка' => 'Basic SEO preparation',
		'Мета-данные, индексация, карта сайта, robots.txt и базовая структура.' => 'Meta data, indexing, a sitemap, robots.txt and a basic structure.',
		'Формы и документы' => 'Forms and documents',
		'Подготовка сайта под сбор заявок' => 'Preparing a site to collect leads',
		'от 12 000 ₽' => 'from 12 000 ₽',
		'Политика обработки данных, согласия, cookie, формы и аналитика.' => 'A data policy, consents, a cookie notice, forms and analytics.',
		'политика обработки персональных данных' => 'Personal data policy',
		'согласия и привязка к формам' => 'Consents linked to forms',
		'cookie-уведомление' => 'A cookie notice',
		'проверка логики сбора заявок' => 'A check of how leads are collected',
		'Отдельная юридическая экспертиза под конкретный бизнес при необходимости считается отдельно.' => 'A separate legal review for a specific business is priced separately, if you need it.',
		'Что обычно входит в работу' => 'What the work usually includes',
		'Адаптивность' => 'Responsive layout',
		'Формы' => 'Forms',
		'Метрика' => 'Yandex Metrica',
		'Яндекс Директ' => 'Yandex Direct',
		'MAX-боты' => 'MAX bots',
		'Техподдержка' => 'Support',
		'Как проходит работа' => 'How the work goes',
		'Сначала разбираем задачу, потом фиксируем понятный объём работ.' => 'First we sort out the task. Then we fix a clear scope.',
		'Коротко обсуждаем задачу' => 'A short talk about the task',
		'Что есть сейчас, что не работает, какой результат нужен и какие ограничения по срокам или бюджету.' => 'What you have now, what is broken, what result you need, and any limits on time or budget.',
		'Смотрю сайт или проект' => 'I look at the site or the project',
		'Проверяю WordPress, сервер, формы, аналитику, рекламу или сценарий будущего бота.' => 'I check WordPress, the server, forms, analytics, ads, or the flow of a future bot.',
		'Предлагаю план' => 'I suggest a plan',
		'Что делаем сразу, что можно отложить, где есть риски и сколько это будет стоить.' => 'What we do now, what can wait, where the risks are, and what it will cost.',
		'Делаю и проверяю' => 'I do the work and check it',
		'Вношу правки, настраиваю, тестирую и объясняю, что было сделано.' => 'I make the changes, set them up, test them and explain what was done.',
		'Частые вопросы' => 'Common questions',
		'Почему статический лендинг стоит от 25 000 ₽, а WordPress-лендинг — от 50 000 ₽?' => 'Why does a static landing start at 25 000 ₽, and a WordPress landing at 50 000 ₽?',
		'Это разные форматы. Статика проще, быстрее и не требует CMS. WordPress нужен, когда важны редактор контента, управляемые блоки, плагины и дальнейшее развитие сайта.' => 'They are different formats. A static page is simpler and faster, and it needs no CMS. WordPress is for a content editor, editable blocks, plugins and later growth of the site.',
		'Чем сайт с Flexible Content отличается от кастомных блоков за 15 000 ₽?' => 'How is a Flexible Content site different from custom blocks at 15 000 ₽?',
		'Кастомные блоки — точечная задача на существующем сайте: несколько управляемых секций. Flexible Content — конструктор страницы целиком: набор layout’ов в теме, клиент сам собирает порядок. Такой сайт начинается от 60 000 ₽.' => 'Custom blocks are a small job on an existing site: a few editable sections. Flexible Content is a builder for the whole page: a set of layouts in the theme, and you set the order. A site like that starts at 60 000 ₽.',
		'Можно ли просто посмотреть сайт и сказать, что с ним не так?' => 'Can you just look at a site and say what is wrong?',
		'Да. Для этого подходит диагностика или консультация. После неё становится понятно, нужен новый сайт или достаточно привести в порядок текущий.' => 'Yes. A diagnostic or a consultation is for that. After it, it is clear whether you need a new site or a cleanup of the current one.',
		'Вы берёте небольшие задачи?' => 'Do you take small jobs?',
		'Да, если задача понятная и её можно нормально оценить: форма, SSL, блок, Метрика, ошибка WordPress или небольшая доработка.' => 'Yes, if the job is clear and can be priced properly: a form, SSL, a block, Yandex Metrica, a WordPress error, or a small change.',
		'Что если задача в процессе окажется сложнее оценки?' => 'What if the job turns out harder than the estimate?',
		'Я останавливаюсь и согласовываю с вами дополнительный объём и смету — до продолжения работы. Бюджет не растёт молча, и неожиданных счетов после сдачи не бывает.' => 'I stop and agree the extra scope and the new price with you before I continue. The budget never grows silently, and there are no surprise bills after delivery.',
		'Можно ли заказать только рекламу?' => 'Can I order ads only?',
		'Можно. Но перед запуском лучше проверить сайт, формы и цели, чтобы реклама не сжигала бюджет из-за технических проблем.' => 'Yes. Before a launch it is better to check the site, the forms and the goals, so the ads do not burn the budget on technical problems.',
		'Бот для MAX или Telegram можно связать с сайтом?' => 'Can a MAX or Telegram bot be linked to the site?',
		'Да. Можно настроить сбор заявок, уведомления, таблицы, CRM и передачу данных между сайтом и ботом.' => 'Yes. I can set up lead collection, notices, spreadsheets, a CRM and data passed between the site and the bot.',
		'Делаете сайты на Python, не только на WordPress?' => 'Do you build Python sites, not only WordPress?',
		'Да. Если редактор контента не нужен, а нужна своя серверная логика, собираю сайт на Flask: страницы, формы и бэкенд на Python. Небольшой такой сайт начинается от 50 000 ₽. Кабинет, оплата и интеграции считаются отдельно.' => 'Yes. If you do not need a content editor and you need your own server logic, I build a Flask site: pages, forms and a Python backend. A small site like that starts at 50 000 ₽. An account area, payments and integrations are priced separately.',
		'Есть задача, но непонятно, с чего начать?' => 'You have a task, but it is not clear where to start?',
		'Напишите коротко, что есть сейчас и какой результат нужен. Я предложу первый нормальный шаг: консультацию, диагностику, доработку, поддержку или отдельную смету.' => 'Write a short note about what you have now and what result you need. I will suggest a reasonable first step: a consultation, a diagnostic, a change, care, or a separate estimate.',
		'Написать по задаче' => 'Write about the task',
		'Цены указаны как стартовые ориентиры. Простые задачи с понятным объёмом считаются от указанной суммы; интеграции, срочность, длительная отладка и большой объём правок оцениваются отдельно.' => 'Prices are starting points. Simple jobs with a clear scope are priced from the listed sum. Integrations, rush work, long debugging and a large set of changes are priced separately.',
	);
}

/**
 * Deep-copy a settings node and replace known Russian strings.
 *
 * @param mixed                 $value Settings node.
 * @param array<string, string> $map   Russian string to English.
 * @return mixed
 */
function krv_en_price_translate_copy( $value, array $map ) {
	if ( is_string( $value ) ) {
		return $map[ $value ] ?? $value;
	}

	if ( ! is_array( $value ) ) {
		return $value;
	}

	$copy = array();

	foreach ( $value as $key => $item ) {
		$copy[ $key ] = krv_en_price_translate_copy( $item, $map );
	}

	return $copy;
}
