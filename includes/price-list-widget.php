<?php
/**
 * Price list widget shortcode [krv_price_list] for /prays-list/.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Append UTM parameters to external landing URLs.
 *
 * @param string $base_url  Landing base URL.
 * @param string $campaign  utm_campaign value (wordpress, vps, direct, bots).
 * @return string
 */
function krv_price_list_utm_url( string $base_url, string $campaign ): string {
	$params = array(
		'utm_source'   => 'krivoshein.site',
		'utm_medium'   => 'prays-list',
		'utm_campaign' => $campaign,
	);

	return add_query_arg( $params, $base_url );
}

/**
 * Build contacts page URL with UTM and optional topic prefill.
 *
 * @param string $campaign utm_campaign value.
 * @param string $topic    Optional ?topic= query for the form context.
 * @return string
 */
function krv_price_list_contacts_url( string $campaign, string $topic = '' ): string {
	$params = array(
		'utm_source'   => 'krivoshein.site',
		'utm_medium'   => 'prays-list',
		'utm_campaign' => $campaign,
	);

	if ( $topic !== '' ) {
		$params['topic'] = $topic;
	}

	$url = add_query_arg( $params, home_url( '/contacts/' ) );

	return $url . '#krv-contact-block';
}

/**
 * Replace placeholders and UTM-tagged links in widget HTML.
 *
 * @param string $html Widget HTML.
 * @return string
 */
function krv_price_list_finalize_html( string $html ): string {
	$replacements = array(
		'{{KRV_CONTACTS_GENERAL}}'    => esc_url( krv_price_list_contacts_url( 'general' ) ),
		'{{KRV_CONTACTS_DIAGNOSTIC}}' => esc_url( krv_price_list_contacts_url( 'diagnostic', 'diagnostic' ) ),
		'{{KRV_CONTACTS_CTA}}'        => esc_url( krv_price_list_contacts_url( 'cta-bottom' ) ),
		'{{KRV_CONTACTS_LINK}}'       => esc_url( krv_price_list_contacts_url( 'link-block' ) ),
		'{{KRV_MAX_CHAT}}'            => esc_url( home_url( '/max' ) ),
		'{{KRV_TG_CHAT}}'             => esc_url( krv_price_list_utm_url( 'https://t.me/DrSlon', 'telegram-chat' ) ),
	);

	$html = str_replace( array_keys( $replacements ), array_values( $replacements ), $html );

	return krv_price_list_apply_utm_links( $html );
}

/**
 * Replace landing subdomain links with UTM-tagged URLs.
 *
 * @param string $html Widget HTML.
 * @return string
 */
function krv_price_list_apply_utm_links( string $html ): string {
	$landing_map = array(
		'https://wordpress.krivoshein.site/' => 'wordpress',
		'https://vps.krivoshein.site/'       => 'vps',
		'https://direct.krivoshein.site/'    => 'direct',
		'https://bots.krivoshein.site/'      => 'bots',
	);

	foreach ( $landing_map as $base_url => $campaign ) {
		$utm_url = esc_url( krv_price_list_utm_url( $base_url, $campaign ) );
		$html    = str_replace( 'href="' . $base_url . '"', 'href="' . $utm_url . '"', $html );
	}

	return $html;
}

/**
 * Render the price list widget markup.
 *
 * @return string
 */
function krv_price_list_render(): string {
	$html = <<<'KRV_PRICE_LIST_HTML'
<div class="krv-price-widget">

  <div class="krv-price-shell">
    <section class="krv-hero">
      <div class="krv-hero-grid">
        <div>
          <div class="krv-badge">Антикризисный прайс • WordPress / Linux / DevOps / реклама / боты</div>
          <h1 class="krv-title">Стоимость сайтов, поддержки, рекламы и технических работ</h1>
          <p class="krv-subtitle">Разработка сайтов • Поддержка • Linux / DevOps • Яндекс Директ • Автоматизация заявок</p>
          <p class="krv-lead">Делаю сайты на WordPress, дорабатываю существующие проекты, настраиваю серверы, рекламу, аналитику, формы, cookie-уведомления и простых ботов для заявок. Ниже базовые ориентиры по цене. Это антикризисный прайс без агентской наценки, но не демпинг: финальная стоимость зависит от задачи, состояния проекта и объема работ.</p>

          <div class="krv-actions">
            <a class="krv-btn krv-btn-primary" href="{{KRV_CONTACTS_GENERAL}}">Обсудить задачу</a>
            <a class="krv-btn" href="{{KRV_TG_CHAT}}" target="_blank" rel="noopener noreferrer">Telegram</a>
            <a class="krv-btn" href="{{KRV_MAX_CHAT}}" target="_blank" rel="noopener noreferrer">MAX</a>
          </div>

          <div class="krv-contacts">
            <a href="tel:+79636641615" target="_blank" rel="noopener noreferrer">+7 (963) 664-16-15</a>
            <a href="mailto:aleksey@krivoshein.site" target="_blank" rel="noopener noreferrer">aleksey@krivoshein.site</a>
            <a href="https://krivoshein.site/max" target="_blank" rel="noopener noreferrer">MAX</a>
          </div>
        </div>

        <div class="krv-hero-panel krv-hero-panel-accent">
          <h2>Не знаете, что именно нужно?</h2>
          <p>Это нормально. Иногда нужен новый сайт, а иногда достаточно починить формы, цели в Метрике, скорость, рекламу или сервер.</p>
          <p class="krv-hero-panel-note">Коротко в <strong>Telegram</strong> или <strong>MAX</strong> — бесплатно. Полный технический разбор сайта — <strong>от 5 000 ₽</strong>.</p>

          <div class="krv-hero-panel-quick">
            <span class="krv-hero-panel-quick-label">Бесплатно уточнить задачу</span>
            <div class="krv-hero-messengers">
              <a class="krv-btn krv-btn-messenger" href="{{KRV_TG_CHAT}}" target="_blank" rel="noopener noreferrer">Telegram</a>
              <a class="krv-btn krv-btn-messenger" href="{{KRV_MAX_CHAT}}" target="_blank" rel="noopener noreferrer">MAX</a>
            </div>
          </div>

          <a class="krv-btn krv-btn-primary krv-btn-block" href="{{KRV_CONTACTS_DIAGNOSTIC}}">Заказать диагностику</a>
          <p class="krv-hero-panel-meta">от 5 000 ₽ · WordPress, формы, Метрика, скорость, рекомендации</p>
          <a class="krv-hero-panel-link" href="#krv-package-diagnostic">Что входит в диагностику ↓</a>
        </div>
      </div>

      <div class="krv-tags">
        <span>WordPress</span>
        <span>Linux</span>
        <span>Nginx</span>
        <span>Docker</span>
        <span>Redis</span>
        <span>VPS</span>
        <span>Яндекс Директ</span>
        <span>Метрика</span>
        <span>SEO</span>
        <span>MAX-боты</span>
        <span>Telegram-боты</span>
        <span>Cookies</span>
        <span>Персональные данные</span>
      </div>
    </section>

    <nav class="krv-anchor-nav" aria-label="Навигация по разделам прайса">
      <a href="#krv-scenarios">С чего начать</a>
      <a href="#krv-landings">Лендинги</a>
      <a href="#krv-prices">Цены</a>
      <a href="#krv-faq">FAQ</a>
    </nav>

    <section class="krv-section" id="krv-scenarios">
      <div class="krv-section-head">
        <h2>С чего начать</h2>
        <p>Выберите ближайший сценарий. Так проще понять, что именно нужно: новый сайт, ремонт текущего, реклама, бот или регулярная техническая поддержка.</p>
      </div>

      <div class="krv-route-grid">
        <a class="krv-route-card" href="https://wordpress.krivoshein.site/" target="_blank" rel="noopener noreferrer">
          <span class="krv-kicker">Сайт с нуля</span>
          <span class="krv-route-title">Нужен новый сайт</span>
          <span class="krv-route-text">Лендинг, сайт услуг, корпоративный сайт или проект со своей структурой на WordPress.</span>
          <span class="krv-route-go">Лендинг WordPress ↗</span>
        </a>

        <a class="krv-route-card" href="https://wordpress.krivoshein.site/" target="_blank" rel="noopener noreferrer">
          <span class="krv-kicker">Ремонт</span>
          <span class="krv-route-title">Есть сайт, но он чудит</span>
          <span class="krv-route-text">Тормозит, ломается, не отправляет формы, плохо выглядит на телефоне или давно не обновлялся.</span>
          <span class="krv-route-go">Доработка и поддержка ↗</span>
        </a>

        <a class="krv-route-card" href="https://direct.krivoshein.site/" target="_blank" rel="noopener noreferrer">
          <span class="krv-kicker">Заявки</span>
          <span class="krv-route-title">Нужна реклама или аналитика</span>
          <span class="krv-route-text">Проверка Яндекс Директа, Метрики, целей, расходов и всей связки от клика до заявки.</span>
          <span class="krv-route-go">Лендинг Яндекс Директ ↗</span>
        </a>

        <a class="krv-route-card" href="https://bots.krivoshein.site/" target="_blank" rel="noopener noreferrer">
          <span class="krv-kicker">Автоматизация</span>
          <span class="krv-route-title">Нужен бот для заявок</span>
          <span class="krv-route-text">MAX, Telegram или бот на сайте: принять обращение, задать вопросы и отправить данные владельцу.</span>
          <span class="krv-route-go">Лендинг ботов MAX ↗</span>
        </a>
      </div>
    </section>
    <section class="krv-section" id="krv-landings">
      <div class="krv-section-head">
        <h2>Лендинги по направлениям</h2>
        <p>Отдельные страницы с подробностями, примерами и форматом работы. Открываются в новой вкладке — прайс остаётся здесь.</p>
      </div>

      <div class="krv-landing-grid">
        <a class="krv-landing-card" href="https://wordpress.krivoshein.site/" target="_blank" rel="noopener noreferrer">
          <span class="krv-landing-domain">wordpress.krivoshein.site</span>
          <span class="krv-route-title">WordPress: сайты и поддержка</span>
          <span class="krv-route-text">Новые проекты, доработки, ускорение, формы, плагины и техническое сопровождение.</span>
          <span class="krv-landing-open">Открыть лендинг ↗</span>
        </a>

        <a class="krv-landing-card" href="https://vps.krivoshein.site/" target="_blank" rel="noopener noreferrer">
          <span class="krv-landing-domain">vps.krivoshein.site</span>
          <span class="krv-route-title">VPS и серверы под ключ</span>
          <span class="krv-route-text">Nginx, SSL, Docker, Redis, перенос WordPress и базовая безопасность.</span>
          <span class="krv-landing-open">Открыть лендинг ↗</span>
        </a>

        <a class="krv-landing-card" href="https://direct.krivoshein.site/" target="_blank" rel="noopener noreferrer">
          <span class="krv-landing-domain">direct.krivoshein.site</span>
          <span class="krv-route-title">Яндекс Директ</span>
          <span class="krv-route-text">Аудит, запуск, ведение и связка рекламы с сайтом, Метрикой и заявками.</span>
          <span class="krv-landing-open">Открыть лендинг ↗</span>
        </a>

        <a class="krv-landing-card" href="https://bots.krivoshein.site/" target="_blank" rel="noopener noreferrer">
          <span class="krv-landing-domain">bots.krivoshein.site</span>
          <span class="krv-route-title">Боты MAX и автоматизация</span>
          <span class="krv-route-text">Сценарии заявок, уведомления, интеграции с сайтом и таблицами.</span>
          <span class="krv-landing-open">Открыть лендинг ↗</span>
        </a>
      </div>
    </section>

    <section class="krv-section" id="krv-prices">
      <div class="krv-section-head">
        <h2>Популярные форматы</h2>
        <p>Это ориентиры, чтобы сразу понимать порядок бюджета. Финальная смета зависит от реальной задачи.</p>
      </div>

      <div class="krv-package-grid">
        <div class="krv-package" id="krv-package-diagnostic">
          <div class="krv-kicker">Быстрый старт</div>
          <h3 class="krv-card-title">Диагностика сайта</h3>
          <div class="krv-price">от 5 000 ₽</div>
          <p class="krv-text">Подходит, если сайт уже есть, но непонятно, почему нет заявок, всё тормозит или реклама работает странно.</p>
          <ul class="krv-list">
            <li>проверка WordPress и плагинов</li>
            <li>формы, почта, заявки</li>
            <li>Метрика и цели</li>
            <li>скорость и базовые ошибки</li>
            <li>список рекомендаций</li>
          </ul>
          <a class="krv-btn krv-btn-primary krv-package-cta" href="{{KRV_CONTACTS_DIAGNOSTIC}}">Заказать диагностику</a>
        </div>

        <div class="krv-package krv-package-accent">
          <div class="krv-kicker">Самый частый вариант</div>
          <h3 class="krv-card-title">Ремонт и доработка сайта</h3>
          <div class="krv-price">от 10 000 ₽</div>
          <p class="krv-text">Когда сайт не надо выбрасывать в окно, но пора привести его в нормальный вид и технический порядок.</p>
          <ul class="krv-list">
            <li>исправление ошибок</li>
            <li>доработка блоков и страниц</li>
            <li>адаптация под мобильные</li>
            <li>ускорение WordPress</li>
            <li>настройка форм и аналитики</li>
          </ul>
        </div>

        <div class="krv-package">
          <div class="krv-kicker">Регулярно</div>
          <h3 class="krv-card-title">Техническая поддержка</h3>
          <div class="krv-price">от 20 000 ₽ / мес</div>
          <p class="krv-text">Для проектов, где сайт должен не просто существовать, а спокойно работать без режима «ой, всё упало».</p>
          <ul class="krv-list">
            <li>обновления WordPress</li>
            <li>резервные копии</li>
            <li>мелкие правки</li>
            <li>контроль ошибок</li>
            <li>консультации по развитию</li>
          </ul>
        </div>
      </div>
    </section>

    <section class="krv-section" id="krv-prices">
      <div class="krv-section-head">
        <h2>Услуги и цены</h2>
        <p>Основные направления разложены в две ровные колонки, чтобы страница не превращалась в лестницу из блоков разной длины.</p>
      </div>

      <div class="krv-service-grid">
        <div class="krv-service-card">
          <h2>Разработка сайтов</h2>
          <a class="krv-service-cta" href="https://wordpress.krivoshein.site/" target="_blank" rel="noopener noreferrer">Лендинг wordpress.krivoshein.site ↗</a>
          <div class="krv-service-list">
            <div class="krv-service-item">
              <div class="krv-kicker">Стартовый формат</div>
              <h3 class="krv-card-title">Лендинг на WordPress</h3>
              <div class="krv-price">от 50 000 ₽</div>
              <p class="krv-text">Одностраничный сайт под услугу, продукт, рекламу или сбор заявок. Нормальная структура, адаптивность, формы, базовая аналитика.</p>
            </div>

            <div class="krv-service-item">
              <div class="krv-kicker">Основной формат</div>
              <h3 class="krv-card-title">Корпоративный сайт</h3>
              <div class="krv-price">от 90 000 ₽</div>
              <p class="krv-text">Сайт компании с услугами, контактами, формами, блогом, базовой SEO-структурой и понятной админкой.</p>
            </div>

            <div class="krv-service-item">
              <div class="krv-kicker">Расширенный проект</div>
              <h3 class="krv-card-title">Сайт со сложной логикой</h3>
              <div class="krv-price">от 180 000 ₽</div>
              <p class="krv-text">Каталог, кастомные типы записей, ACF-поля, нестандартные шаблоны, фильтры, интеграции и дополнительные сценарии.</p>
            </div>

            <div class="krv-service-item">
              <div class="krv-kicker">Точечные задачи</div>
              <h3 class="krv-card-title">Доработки существующего сайта</h3>
              <div class="krv-price">от 5 000 ₽ <span class="krv-price-small-inline">(минимальный чек)</span></div>
              <p class="krv-text">Правки, новые блоки, исправления, переносы, ускорение, адаптация, настройка плагинов и техническое наведение порядка.</p>
            </div>
          </div>
        </div>

        <div class="krv-service-card">
          <h2>Боты и автоматизация заявок</h2>
          <p class="krv-text">Бот не заменяет сайт, но хорошо закрывает рутину: принять обращение, спросить имя, телефон, услугу, город и передать данные владельцу.</p>

          <div class="krv-service-list" style="margin-top:14px;">
            <div class="krv-service-item">
              <div class="krv-kicker">MVP</div>
              <h3 class="krv-card-title">Простой бот для заявок</h3>
              <div class="krv-price">от 40 000 ₽</div>
              <p class="krv-text">Сценарий общения, сбор контактов, уведомления администратору, базовая установка и проверка работы.</p>
            </div>

            <div class="krv-service-item">
              <div class="krv-kicker">С интеграциями</div>
              <h3 class="krv-card-title">Бот с таблицами, сайтом или CRM</h3>
              <div class="krv-price">от 70 000 ₽</div>
              <p class="krv-text">Передача данных в таблицы, на сайт, в CRM или другую систему. Подходит для заявок, записи, консультаций и простых внутренних процессов.</p>
            </div>

            <div class="krv-service-item">
              <div class="krv-kicker">Поддержка</div>
              <h3 class="krv-card-title">Сопровождение бота</h3>
              <div class="krv-price">от 5 000 ₽ / месяц</div>
              <p class="krv-text">Контроль работы, мелкие правки, обновление сценариев, проверка ошибок и техническое сопровождение.</p>
            </div>
          </div>

          <div class="krv-note">
            Сейчас это направление можно запускать в пилотном формате: MAX, Telegram, сайт, простые Python-сценарии и автоматизация заявок.
            <a class="krv-service-cta" href="https://bots.krivoshein.site/" target="_blank" rel="noopener noreferrer">Лендинг ботов MAX ↗</a>
          </div>
        </div>

        <div class="krv-service-card">
          <h2>Отдельные технические услуги</h2>
          <div class="krv-service-list">
            <div class="krv-service-item-clean">
              <h3 class="krv-card-title">Готовая тема и настройка</h3>
              <div class="krv-price">3 000–10 000 ₽</div>
              <p class="krv-text">Быстрый старт на готовой теме без отдельного дизайна с нуля. Подходит для простых сайтов и MVP.</p>
            </div>

            <div class="krv-service-item-clean">
              <h3 class="krv-card-title">Уникальный дизайн</h3>
              <div class="krv-price">от 30 000 ₽</div>
              <p class="krv-text">Когда проекту нужен свой внешний вид, а не просто аккуратный шаблон.</p>
            </div>

            <div class="krv-service-item-clean">
              <h3 class="krv-card-title">Кастомные поля и блоки ACF</h3>
              <div class="krv-price">от 15 000 ₽</div>
              <p class="krv-text">Собственная структура контента, удобная админка, повторяемые блоки, карточки, каталоги и управляемые секции.</p>
            </div>

            <div class="krv-service-item-clean">
              <h3 class="krv-card-title">Настройка VPS, Nginx, Docker, Redis</h3>
              <div class="krv-price">от 10 000 ₽</div>
              <p class="krv-text">Сервер, SSL, перенос сайта, кеширование, базовая безопасность и техническая база под WordPress.</p>
              <a class="krv-service-cta" href="https://vps.krivoshein.site/" target="_blank" rel="noopener noreferrer">Подробнее на vps.krivoshein.site ↗</a>
            </div>

            <div class="krv-service-item-clean">
              <h3 class="krv-card-title">Базовая SEO-подготовка</h3>
              <div class="krv-price">10 000–20 000 ₽</div>
              <p class="krv-text">Мета-данные, индексация, карта сайта, robots.txt, базовая структура и порядок перед запуском.</p>
            </div>
          </div>
        </div>

        <div class="krv-service-card">
          <h2>Яндекс Директ и реклама</h2>
          <a class="krv-service-cta" href="https://direct.krivoshein.site/" target="_blank" rel="noopener noreferrer">Лендинг direct.krivoshein.site ↗</a>
          <div class="krv-service-list">
            <div class="krv-service-item">
              <div class="krv-kicker">Почасовой формат</div>
              <h3 class="krv-card-title">Консультация по рекламе</h3>
              <div class="krv-price">от 2 000 ₽ / час</div>
              <p class="krv-text">Разбор рекламной кампании, структуры, объявлений, бюджета, аналитики и общих точек роста.</p>
            </div>

            <div class="krv-service-item">
              <div class="krv-kicker">Разовая услуга</div>
              <h3 class="krv-card-title">Аудит Яндекс Директ</h3>
              <div class="krv-price">от 10 000 ₽</div>
              <p class="krv-text">Проверка текущих кампаний, ставок, минус-слов, объявлений, целей и базовой логики ведения рекламы.</p>
            </div>

            <div class="krv-service-item">
              <div class="krv-kicker">Запуск</div>
              <h3 class="krv-card-title">Настройка Яндекс Директ</h3>
              <div class="krv-price">от 20 000 ₽</div>
              <p class="krv-text">Подготовка и запуск рекламных кампаний под конкретную задачу: заявки, звонки, трафик или тест спроса.</p>
            </div>

            <div class="krv-service-item">
              <div class="krv-kicker">Регулярно</div>
              <h3 class="krv-card-title">Ведение и оптимизация рекламы</h3>
              <div class="krv-price">от 25 000 ₽ / месяц</div>
              <p class="krv-text">Сопровождение кампаний, корректировки, чистка, тесты, контроль расхода бюджета и постепенная оптимизация.</p>
            </div>
          </div>
        </div>

        <div class="krv-service-card">
          <h2>Консультации и поддержка</h2>
          <div class="krv-mini-list">
            <div class="krv-mini">
              <div class="krv-mini-label">Почасовой формат</div>
              <div class="krv-mini-title">Консультация</div>
              <div class="krv-mini-text">Разбор сайта, сервера, WordPress, SEO, рекламы, бота или общей технической задачи.<br><strong>2 000 ₽ / час</strong></div>
            </div>

            <div class="krv-mini">
              <div class="krv-mini-label">Регулярное сопровождение</div>
              <div class="krv-mini-title">Техническая поддержка</div>
              <div class="krv-mini-text">Обновления, резервные копии, мелкие доработки, диагностика и сопровождение проекта.<br><strong>20 000 ₽ / месяц</strong></div>
            </div>
          </div>

          <div class="krv-note">
            Поддержка подходит проектам, которым нужен регулярный контроль. Крупные отдельные задачи считаются отдельно.
          </div>
        </div>

        <div class="krv-service-card">
          <h2>Формы, персональные данные и cookies</h2>
          <div class="krv-mini">
            <div class="krv-mini-label">Отдельная услуга</div>
            <div class="krv-mini-title">Подготовка сайта под сбор заявок</div>
            <div class="krv-mini-text">Если на сайте есть формы, обратная связь, Метрика, аналитика и cookie-механики, это лучше сразу привести в порядок.<br><strong>от 12 000 ₽</strong></div>
          </div>

          <ul class="krv-list">
            <li>политика обработки персональных данных</li>
            <li>согласия и привязка к формам</li>
            <li>cookie-уведомление</li>
            <li>проверка логики сбора заявок</li>
            <li>приведение сайта в порядок по формам и аналитике</li>
          </ul>

          <div class="krv-note">
            Это техническая и документная подготовка сайта. Отдельная юридическая экспертиза под конкретный бизнес при необходимости считается отдельно.
          </div>
        </div>
      </div>
    </section>

    <section class="krv-section">
      <div class="krv-info-grid">
        <div class="krv-info-card">
          <h2>Что обычно входит в работу</h2>
          <div class="krv-stack">
            <span>WordPress</span>
            <span>Адаптивность</span>
            <span>Формы</span>
            <span>SSL</span>
            <span>Метрика</span>
            <span>Цели</span>
            <span>Настройка плагинов</span>
            <span>Базовая SEO</span>
            <span>Технический запуск</span>
            <span>Базовая безопасность</span>
            <span>Резервные копии</span>
            <span>Техподдержка</span>
          </div>
        </div>

        <div class="krv-info-card">
          <h2>Как удобнее начать</h2>
          <div class="krv-links">
            <a class="krv-link-card" href="https://wordpress.krivoshein.site/" target="_blank" rel="noopener noreferrer">
              <span class="krv-link-title">Нужен новый сайт</span>
              <span class="krv-link-meta">Лендинг WordPress: форматы, сроки и что входит в работу. ↗</span>
            </a>

            <a class="krv-link-card" href="https://wordpress.krivoshein.site/" target="_blank" rel="noopener noreferrer">
              <span class="krv-link-title">Есть текущий сайт</span>
              <span class="krv-link-meta">Доработка, диагностика, ускорение и поддержка WordPress. ↗</span>
            </a>

            <a class="krv-link-card" href="https://vps.krivoshein.site/" target="_blank" rel="noopener noreferrer">
              <span class="krv-link-title">Нужен сервер или перенос</span>
              <span class="krv-link-meta">VPS, Nginx, SSL, Docker, Redis и настройка под WordPress. ↗</span>
            </a>

            <a class="krv-link-card" href="https://direct.krivoshein.site/" target="_blank" rel="noopener noreferrer">
              <span class="krv-link-title">Нужна реклама</span>
              <span class="krv-link-meta">Яндекс Директ: аудит, запуск и связка с сайтом. ↗</span>
            </a>

            <a class="krv-link-card" href="https://bots.krivoshein.site/" target="_blank" rel="noopener noreferrer">
              <span class="krv-link-title">Нужен бот или автоматизация</span>
              <span class="krv-link-meta">MAX-боты, сценарии заявок и интеграции. ↗</span>
            </a>

            <a class="krv-link-card" href="{{KRV_CONTACTS_LINK}}">
              <span class="krv-link-title">Сразу обсудить задачу</span>
              <span class="krv-link-meta">Контакты, короткий бриф и первый шаг по смете.</span>
            </a>
          </div>
        </div>
      </div>
    </section>

    <section class="krv-section">
      <div class="krv-section-head">
        <h2>Как проходит работа</h2>
        <p>Сначала разбираем задачу, потом фиксируем понятный объем работ.</p>
      </div>

      <div class="krv-process-grid">
        <div class="krv-step">
          <div class="krv-step-num">1</div>
          <h3>Коротко обсуждаем задачу</h3>
          <p>Что есть сейчас, что не работает, какой результат нужен и какие ограничения по срокам или бюджету.</p>
        </div>

        <div class="krv-step">
          <div class="krv-step-num">2</div>
          <h3>Смотрю сайт или проект</h3>
          <p>Проверяю WordPress, сервер, формы, аналитику, рекламу или сценарий будущего бота.</p>
        </div>

        <div class="krv-step">
          <div class="krv-step-num">3</div>
          <h3>Предлагаю план</h3>
          <p>Что делаем сразу, что можно отложить, где есть риски и сколько это будет стоить.</p>
        </div>

        <div class="krv-step">
          <div class="krv-step-num">4</div>
          <h3>Делаю и проверяю</h3>
          <p>Вношу правки, настраиваю, тестирую и объясняю, что было сделано.</p>
        </div>
      </div>
    </section>

    <section class="krv-section" id="krv-faq">
      <div class="krv-section-head">
        <h2>Частые вопросы</h2>
      </div>

      <div class="krv-faq">
        <div class="krv-faq-item">
          <h3>Можно ли просто посмотреть сайт и сказать, что с ним не так?</h3>
          <p>Да. Для этого подходит диагностика или консультация. Часто после нее становится понятно, нужен ли новый сайт или достаточно привести в порядок текущий.</p>
        </div>

        <div class="krv-faq-item">
          <h3>Вы берете маленькие задачи?</h3>
          <p>Да, если задача понятная и ее можно нормально оценить. Например, исправить форму, настроить SSL, поправить блок, проверить Метрику или починить ошибку WordPress.</p>
        </div>

        <div class="krv-faq-item">
          <h3>Можно ли заказать только рекламу?</h3>
          <p>Можно. Но если сайт технически слабый, реклама может просто сжигать бюджет. Поэтому перед запуском лучше проверить сайт, формы и цели.</p>
        </div>

        <div class="krv-faq-item">
          <h3>Бот для MAX или Telegram можно связать с сайтом?</h3>
          <p>Да. Можно сделать сбор заявок, уведомления владельцу, запись данных в таблицу, базу или передачу в другую систему.</p>
        </div>
      </div>
    </section>

    <section class="krv-cta">
      <div class="krv-cta-grid">
        <div>
          <h2>Есть сайт, реклама или идея бота, но непонятно, с чего начать?</h2>
          <p>Напишите коротко, что есть сейчас и что хотите получить. Я посмотрю задачу и предложу нормальный первый шаг: консультацию, диагностику, доработку, поддержку или отдельную смету.</p>
        </div>
        <a class="krv-btn krv-btn-primary" href="{{KRV_CONTACTS_CTA}}">Написать по задаче</a>
      </div>
    </section>

    <div class="krv-small">
      Цены на странице указаны как стартовый антикризисный ориентир. Простые задачи с понятным объемом считаются от указанной суммы. Если проект требует сложной логики, интеграций, срочности, длительной отладки или большого количества правок, стоимость считается отдельно.
    </div>
  </div>
</div>
KRV_PRICE_LIST_HTML;

	return krv_price_list_finalize_html( $html );
}

add_shortcode( 'krv_price_list', function () {
	return krv_price_list_render();
} );
