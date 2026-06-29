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
  <style>
    .krv-price-widget {
      --krv-bg: #ffffff;
      --krv-card: #ffffff;
      --krv-soft: #f8fafc;
      --krv-soft-2: #f8fafc;
      --krv-line: #e7ecf5;
      --krv-line-strong: #d7e0ee;
      --krv-text: #111827;
      --krv-muted: #5b6472;
      --krv-accent: #5181fe;
      --krv-accent-dark: #315fe8;
      --krv-accent-soft: #eef4ff;
      --krv-shadow: 0 12px 34px rgba(15, 23, 42, 0.07);
      --krv-shadow-soft: 0 8px 22px rgba(15, 23, 42, 0.05);

      width: 100%;
      max-width: 1180px;
      margin: 0 auto;
      padding: 20px;
      border-radius: 26px;
      box-sizing: border-box;
      color: var(--krv-text);
      font-family: Inter, Arial, sans-serif;
      background: var(--krv-bg);
    }

    .krv-price-widget,
    .krv-price-widget * {
      box-sizing: border-box;
    }

    .krv-price-widget a {
      color: inherit;
      text-decoration: none;
      -webkit-tap-highlight-color: transparent;
    }

    .krv-price-widget a:hover {
      text-decoration: none;
    }

    .krv-price-shell {
      width: 100%;
      max-width: 1120px;
      margin: 0 auto;
    }

    .krv-hero {
      overflow: hidden;
      position: relative;
      margin-bottom: 22px;
      padding: 30px;
      border: 1px solid var(--krv-line);
      border-radius: 24px;
      background: #ffffff;
      box-shadow: var(--krv-shadow);
    }

    .krv-hero::after {
      display: none;
    }

    .krv-hero-grid {
      position: relative;
      z-index: 1;
      display: grid;
      grid-template-columns: minmax(0, 1.25fr) minmax(280px, 0.75fr);
      gap: 22px;
      align-items: start;
    }

    .krv-badge {
      display: inline-flex;
      align-items: center;
      min-height: 34px;
      margin-bottom: 14px;
      padding: 8px 12px;
      border-radius: 999px;
      background: var(--krv-accent-soft);
      color: var(--krv-accent-dark);
      font-size: 13px;
      line-height: 1.2;
      font-weight: 800;
    }

    .krv-title {
      margin: 0 0 10px;
      max-width: 820px;
      color: var(--krv-text);
      font-size: 38px;
      line-height: 1.08;
      letter-spacing: -0.03em;
      font-weight: 900;
    }

    .krv-subtitle {
      margin: 0 0 14px;
      max-width: 780px;
      color: var(--krv-accent-dark);
      font-size: 18px;
      line-height: 1.5;
      font-weight: 800;
    }

    .krv-lead {
      margin: 0;
      max-width: 820px;
      color: var(--krv-muted);
      font-size: 16px;
      line-height: 1.72;
    }

    .krv-actions,
    .krv-contacts,
    .krv-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .krv-actions {
      margin-top: 18px;
    }

    .krv-contacts {
      margin-top: 14px;
    }

    .krv-tags {
      position: relative;
      z-index: 1;
      margin-top: 18px;
    }

    .krv-btn,
    .krv-contacts a {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 40px;
      padding: 9px 14px;
      border-radius: 999px;
      border: 1px solid var(--krv-line);
      background: var(--krv-soft);
      color: #374151;
      font-size: 14px;
      line-height: 1.3;
      font-weight: 700;
      text-align: center;
      transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease, color 0.2s ease;
    }

    .krv-btn:hover,
    .krv-contacts a:hover {
      transform: translateY(-1px);
      border-color: #b9caff;
      background: #f5f8ff;
      color: var(--krv-accent-dark);
    }

    .krv-btn-primary {
      border-color: var(--krv-accent);
      background: var(--krv-accent);
      color: #ffffff !important;
      box-shadow: 0 10px 22px rgba(81, 129, 254, 0.24);
    }

    .krv-btn-primary:hover {
      border-color: var(--krv-accent-dark);
      background: var(--krv-accent-dark);
      color: #ffffff !important;
    }

    .krv-hero-panel {
      padding: 20px;
      border: 1px solid var(--krv-line);
      border-radius: 20px;
      background: #ffffff;
      box-shadow: var(--krv-shadow-soft);
    }

    .krv-hero-panel h2 {
      margin: 0 0 10px;
      color: var(--krv-text);
      font-size: 20px;
      line-height: 1.25;
      font-weight: 900;
    }

    .krv-hero-panel p {
      margin: 0 0 12px;
      color: var(--krv-muted);
      font-size: 14px;
      line-height: 1.65;
    }

    .krv-tags span {
      display: inline-flex;
      align-items: center;
      min-height: 32px;
      padding: 7px 11px;
      border-radius: 999px;
      background: #f1f5f9;
      color: #334155;
      font-size: 13px;
      line-height: 1.2;
      font-weight: 700;
    }

    .krv-section {
      margin-bottom: 22px;
    }

    .krv-section-head {
      margin-bottom: 14px;
    }

    .krv-section-head h2 {
      margin: 0 0 8px;
      color: var(--krv-text);
      font-size: 26px;
      line-height: 1.2;
      letter-spacing: -0.02em;
      font-weight: 900;
    }

    .krv-section-head p {
      margin: 0;
      max-width: 850px;
      color: var(--krv-muted);
      font-size: 15px;
      line-height: 1.65;
    }

    .krv-route-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 14px;
    }

    .krv-route-card,
    .krv-package,
    .krv-service-card,
    .krv-info-card,
    .krv-step,
    .krv-faq-item {
      background: var(--krv-card);
      border: 1px solid var(--krv-line);
      border-radius: 20px;
      box-shadow: var(--krv-shadow-soft);
    }

    .krv-route-card {
      display: block;
      min-height: 100%;
      padding: 18px;
      transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }

    .krv-route-card:hover {
      transform: translateY(-2px);
      border-color: #b9caff;
      background: #fbfdff;
    }

    .krv-kicker {
      display: inline-flex;
      align-items: center;
      min-height: 28px;
      margin-bottom: 10px;
      padding: 6px 10px;
      border-radius: 999px;
      background: var(--krv-accent-soft);
      color: var(--krv-accent-dark);
      font-size: 12px;
      line-height: 1.2;
      font-weight: 800;
    }

    .krv-route-title {
      display: block;
      margin-bottom: 6px;
      color: var(--krv-text);
      font-size: 17px;
      line-height: 1.35;
      font-weight: 900;
    }

    .krv-route-text {
      display: block;
      color: var(--krv-muted);
      font-size: 14px;
      line-height: 1.58;
    }

    .krv-package-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 16px;
    }

    .krv-package {
      min-height: 100%;
      padding: 22px;
    }

    .krv-package-accent {
      border-color: #b7c8ff;
      background: #ffffff;
    }

    .krv-card-title {
      margin: 0 0 7px;
      color: var(--krv-text);
      font-size: 18px;
      line-height: 1.35;
      font-weight: 900;
    }

    .krv-price {
      margin: 0 0 10px;
      color: var(--krv-accent-dark);
      font-size: 20px;
      line-height: 1.25;
      font-weight: 900;
    }

    .krv-price-small-inline {
      color: var(--krv-muted);
      font-size: 13px;
      font-weight: 700;
      white-space: nowrap;
    }

    .krv-text {
      margin: 0;
      color: var(--krv-muted);
      font-size: 15px;
      line-height: 1.65;
    }

    .krv-list {
      margin: 12px 0 0;
      padding-left: 18px;
      color: var(--krv-muted);
    }

    .krv-list li {
      margin-bottom: 7px;
      font-size: 14px;
      line-height: 1.55;
    }

    .krv-list li:last-child {
      margin-bottom: 0;
    }

    .krv-service-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 18px;
      align-items: stretch;
    }

    .krv-service-card {
      display: flex;
      flex-direction: column;
      min-height: 100%;
      padding: 22px;
    }

    .krv-service-card h2,
    .krv-info-card h2 {
      margin: 0 0 16px;
      color: var(--krv-text);
      font-size: 22px;
      line-height: 1.2;
      font-weight: 900;
    }

    .krv-service-list {
      display: grid;
      gap: 14px;
      margin-top: 0;
    }

    .krv-service-item {
      padding: 15px;
      border: 1px solid var(--krv-line);
      border-radius: 16px;
      background: var(--krv-soft);
    }

    .krv-service-item-clean {
      padding: 0 0 15px;
      border-bottom: 1px solid var(--krv-line);
    }

    .krv-service-item-clean:last-child {
      padding-bottom: 0;
      border-bottom: 0;
    }

    .krv-note {
      margin-top: 14px;
      padding: 14px 16px;
      border-radius: 16px;
      background: #f8fbff;
      border: 1px solid #dbe7ff;
      color: #4b5563;
      font-size: 14px;
      line-height: 1.65;
    }

    .krv-mini-list {
      display: grid;
      gap: 12px;
    }

    .krv-mini,
    .krv-link-card {
      display: block;
      padding: 14px 16px;
      border-radius: 16px;
      background: #f8fafc;
      border: 1px solid var(--krv-line);
    }

    .krv-link-card {
      transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }

    .krv-link-card:hover {
      transform: translateY(-1px);
      border-color: #bfd0ff;
      background: #f5f8ff;
    }

    .krv-mini-label {
      display: inline-block;
      margin-bottom: 8px;
      color: var(--krv-accent-dark);
      font-size: 12px;
      font-weight: 800;
    }

    .krv-mini-title,
    .krv-link-title {
      display: block;
      margin: 0 0 4px;
      color: var(--krv-text);
      font-size: 15px;
      line-height: 1.4;
      font-weight: 900;
    }

    .krv-mini-text,
    .krv-link-meta {
      display: block;
      color: var(--krv-muted);
      font-size: 13px;
      line-height: 1.55;
    }

    .krv-mini-text strong {
      color: var(--krv-text);
    }

    .krv-info-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
      gap: 18px;
    }

    .krv-info-card {
      padding: 22px;
    }

    .krv-stack {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .krv-stack span {
      display: inline-flex;
      align-items: center;
      min-height: 36px;
      padding: 8px 12px;
      border-radius: 12px;
      background: #f8fafc;
      border: 1px solid var(--krv-line);
      color: #334155;
      font-size: 14px;
      font-weight: 700;
    }

    .krv-links {
      display: grid;
      gap: 12px;
    }

    .krv-process-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 14px;
    }

    .krv-step {
      min-height: 100%;
      padding: 18px;
    }

    .krv-step-num {
      display: inline-flex;
      width: 34px;
      height: 34px;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
      border-radius: 50%;
      background: var(--krv-accent);
      color: #fff;
      font-size: 14px;
      font-weight: 900;
    }

    .krv-step h3,
    .krv-faq-item h3 {
      margin: 0 0 7px;
      color: var(--krv-text);
      font-size: 16px;
      line-height: 1.35;
      font-weight: 900;
    }

    .krv-step p,
    .krv-faq-item p {
      margin: 0;
      color: var(--krv-muted);
      font-size: 14px;
      line-height: 1.6;
    }

    .krv-faq {
      display: grid;
      gap: 12px;
    }

    .krv-faq-item {
      padding: 16px;
    }

    .krv-cta {
      position: relative;
      overflow: hidden;
      padding: 28px;
      border-radius: 24px;
      background: #5181fe;
      color: #ffffff;
      box-shadow: 0 20px 48px rgba(49, 95, 232, 0.26);
    }

    .krv-cta-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 20px;
      align-items: center;
    }

    .krv-cta h2 {
      margin: 0 0 8px;
      color: #fff;
      font-size: 26px;
      line-height: 1.22;
      font-weight: 900;
    }

    .krv-cta p {
      margin: 0;
      max-width: 780px;
      color: rgba(255, 255, 255, 0.9);
      font-size: 15px;
      line-height: 1.7;
    }

    .krv-cta .krv-btn {
      background: #fff;
      border-color: #fff;
      color: var(--krv-accent-dark);
      white-space: nowrap;
    }

    .krv-small {
      margin-top: 10px;
      color: var(--krv-muted);
      font-size: 13px;
      line-height: 1.55;
    }

    @media (max-width: 1080px) {
      .krv-route-grid,
      .krv-landing-grid,
      .krv-process-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .krv-package-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 900px) {
      .krv-hero-grid,
      .krv-service-grid,
      .krv-info-grid,
      .krv-cta-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 700px) {
      .krv-price-widget {
        padding: 12px;
        border-radius: 20px;
      }

      .krv-hero,
      .krv-service-card,
      .krv-info-card,
      .krv-cta {
        padding: 18px;
        border-radius: 18px;
      }

      .krv-title {
        font-size: 29px;
      }

      .krv-subtitle {
        font-size: 16px;
      }

      .krv-section-head h2 {
        font-size: 23px;
      }

      .krv-route-grid,
      .krv-landing-grid,
      .krv-process-grid {
        grid-template-columns: 1fr;
      }

      .krv-actions,
      .krv-contacts {
        display: grid;
        grid-template-columns: 1fr;
      }

      .krv-btn,
      .krv-contacts a {
        width: 100%;
      }
    }

    @media (max-width: 520px) {
      .krv-title {
        font-size: 24px;
      }

      .krv-lead,
      .krv-text,
      .krv-list li,
      .krv-route-text,
      .krv-faq-item p {
        font-size: 14px;
      }

      .krv-service-card h2,
      .krv-info-card h2,
      .krv-section-head h2 {
        font-size: 21px;
      }

      .krv-cta h2 {
        font-size: 21px;
      }
    }

    .krv-route-card:focus-visible,
    .krv-link-card:focus-visible,
    .krv-landing-card:focus-visible,
    .krv-btn:focus-visible {
      outline: 3px solid rgba(81, 129, 254, 0.38);
      outline-offset: 2px;
    }

    .krv-route-go,
    .krv-landing-open {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-top: 12px;
      color: var(--krv-accent-dark);
      font-size: 13px;
      line-height: 1.3;
      font-weight: 800;
    }

    .krv-landing-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 14px;
    }

    .krv-landing-card {
      display: flex;
      flex-direction: column;
      min-height: 100%;
      padding: 18px;
      background: var(--krv-card);
      border: 1px solid var(--krv-line);
      border-radius: 20px;
      box-shadow: var(--krv-shadow-soft);
      transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }

    .krv-landing-card:hover {
      transform: translateY(-2px);
      border-color: #b9caff;
      background: #fbfdff;
    }

    .krv-landing-domain {
      display: inline-block;
      margin-bottom: 8px;
      color: var(--krv-accent-dark);
      font-size: 12px;
      line-height: 1.2;
      font-weight: 800;
      letter-spacing: 0.01em;
    }

    .krv-landing-open {
      margin-top: auto;
      padding-top: 10px;
    }

    .krv-service-cta {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-top: 10px;
      color: var(--krv-accent-dark);
      font-size: 13px;
      font-weight: 800;
    }

    .krv-service-cta:hover {
      text-decoration: underline;
    }

    .krv-anchor-nav {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 22px;
      padding: 12px 16px;
      border: 1px solid var(--krv-line);
      border-radius: 999px;
      background: var(--krv-soft);
      box-shadow: var(--krv-shadow-soft);
    }

    .krv-anchor-nav a {
      display: inline-flex;
      align-items: center;
      min-height: 36px;
      padding: 8px 14px;
      border-radius: 999px;
      background: #ffffff;
      border: 1px solid var(--krv-line);
      color: var(--krv-accent-dark);
      font-size: 14px;
      line-height: 1.3;
      font-weight: 700;
      transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }

    .krv-anchor-nav a:hover {
      transform: translateY(-1px);
      border-color: var(--krv-accent);
      background: var(--krv-accent-soft);
      color: var(--krv-accent-dark);
    }

    .krv-anchor-nav a:focus-visible {
      outline: 3px solid rgba(81, 129, 254, 0.38);
      outline-offset: 2px;
    }

    @media (max-width: 700px) {
      .krv-anchor-nav {
        border-radius: 18px;
        padding: 10px 12px;
      }

      .krv-anchor-nav a {
        flex: 1 1 calc(50% - 5px);
        justify-content: center;
        min-height: 40px;
      }
    }
  </style>

  <div class="krv-price-shell">
    <section class="krv-hero">
      <div class="krv-hero-grid">
        <div>
          <div class="krv-badge">Антикризисный прайс • WordPress / Linux / DevOps / реклама / боты</div>
          <h1 class="krv-title">Стоимость сайтов, поддержки, рекламы и технических работ</h1>
          <p class="krv-subtitle">Разработка сайтов • Поддержка • Linux / DevOps • Яндекс Директ • Автоматизация заявок</p>
          <p class="krv-lead">Делаю сайты на WordPress, дорабатываю существующие проекты, настраиваю серверы, рекламу, аналитику, формы, cookie-уведомления и простых ботов для заявок. Ниже базовые ориентиры по цене. Это антикризисный прайс без агентской наценки, но не демпинг: финальная стоимость зависит от задачи, состояния проекта и объема работ.</p>

          <div class="krv-actions">
            <a class="krv-btn krv-btn-primary" href="https://krivoshein.site/contacts/" target="_blank" rel="noopener noreferrer">Обсудить задачу</a>
            <a class="krv-btn" href="https://t.me/DrSlon" target="_blank" rel="noopener noreferrer">Написать в Telegram</a>
          </div>

          <div class="krv-contacts">
            <a href="tel:+79636641615" target="_blank" rel="noopener noreferrer">+7 (963) 664-16-15</a>
            <a href="mailto:aleksey@krivoshein.site" target="_blank" rel="noopener noreferrer">aleksey@krivoshein.site</a>
            <a href="https://krivoshein.site/max" target="_blank" rel="noopener noreferrer">MAX</a>
          </div>
        </div>

        <div class="krv-hero-panel">
          <h2>Не знаете, что именно нужно?</h2>
          <p>Это нормально. Иногда нужен новый сайт, а иногда достаточно починить формы, цели в Метрике, скорость, рекламу или сервер.</p>
          <p>Самый разумный старт — короткая диагностика. Смотрим, где ломается связка: сайт, WordPress, хостинг, аналитика, реклама или заявки.</p>
          <a class="krv-btn krv-btn-primary" href="https://krivoshein.site/contacts/" target="_blank" rel="noopener noreferrer">Начать с диагностики</a>
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

    <section class="krv-section">
      <div class="krv-section-head">
        <h2>Популярные форматы</h2>
        <p>Это ориентиры, чтобы сразу понимать порядок бюджета. Финальная смета зависит от реальной задачи.</p>
      </div>

      <div class="krv-package-grid">
        <div class="krv-package">
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

            <a class="krv-link-card" href="https://krivoshein.site/contacts/" target="_blank" rel="noopener noreferrer">
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
        <a class="krv-btn" href="https://krivoshein.site/contacts/" target="_blank" rel="noopener noreferrer">Написать по задаче</a>
      </div>
    </section>

    <div class="krv-small">
      Цены на странице указаны как стартовый антикризисный ориентир. Простые задачи с понятным объемом считаются от указанной суммы. Если проект требует сложной логики, интеграций, срочности, длительной отладки или большого количества правок, стоимость считается отдельно.
    </div>
  </div>
</div>
KRV_PRICE_LIST_HTML;

	return krv_price_list_apply_utm_links( $html );
}

add_shortcode( 'krv_price_list', function () {
	return krv_price_list_render();
} );
