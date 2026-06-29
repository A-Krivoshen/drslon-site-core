<?php
/**
 * Combined landing shortcode [krv_services_landing]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Combined landing shortcode
 * Usage: [krv_services_landing]
 */
add_shortcode( 'krv_services_landing', function () {
	ob_start();
	?>
	<div class="krv-services-landing">
		<style>
			.krv-services-landing {
				--accent: #5181fe;
				--accent-hover: #4169d4;
				--accent-bg: #eaf1ff;
				--text-main: #333;
				--text-soft: #666;
				--card-bg: #fff;
				--card-radius: 20px;
				--card-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
				--card-shadow-hover: 0 6px 15px rgba(0, 0, 0, 0.15);
				--service-radius: 10px;
				max-width: 1200px;
				margin: 0 auto;
				padding: 20px 15px;
				font-family: "Poppins", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
				color: var(--text-main);
			}

			.krv-services-landing,
			.krv-services-landing * {
				box-sizing: border-box;
			}

			.krv-services-landing-section + .krv-services-landing-section {
				margin-top: 22px;
			}

			.krv-landing-contact-card {
				width: 100%;
				padding: 24px;
				text-align: center;
				background: var(--card-bg);
				border-radius: var(--card-radius);
				box-shadow: var(--card-shadow);
			}

			.krv-landing-avatar-wrap {
				width: 126px;
				height: 126px;
				aspect-ratio: 1 / 1;
				margin: 0 auto 20px;
				border: 3px solid var(--accent);
				border-radius: 50%;
				overflow: hidden;
				background: #fff;
			}

			.krv-services-landing .krv-landing-avatar-wrap > .krv-landing-avatar {
				display: block;
				width: 100%;
				height: 100%;
				aspect-ratio: 1 / 1;
				object-fit: cover;
				object-position: center;
			}

			.krv-services-landing .krv-landing-avatar-wrap > .krv-landing-avatar img {
				display: block;
				width: 100%;
				height: 100%;
				object-fit: cover;
				object-position: center;
			}

			.krv-landing-title {
				margin: 0 0 15px;
				font-size: 1.8rem;
				line-height: 1.2;
				color: var(--text-main);
			}

			.krv-landing-lead,
			.krv-landing-meta {
				margin: 0 0 14px;
				font-size: 1rem;
				line-height: 1.5;
				color: var(--text-soft);
			}

			.krv-landing-meta {
				display: flex;
				flex-direction: column;
				gap: 4px;
				align-items: center;
			}

			.krv-landing-meta-line {
				display: block;
			}

			.krv-landing-contacts {
				display: flex;
				justify-content: center;
				align-items: center;
				flex-wrap: wrap;
				gap: 15px;
				margin-top: 10px;
			}

			.krv-landing-contacts a {
				display: flex;
				justify-content: center;
				align-items: center;
				width: 50px;
				height: 50px;
				text-decoration: none;
				color: var(--accent);
				background: #f9f9f9;
				border: 3px solid var(--accent);
				border-radius: 50%;
				transition: border-color 0.25s ease, background 0.25s ease, box-shadow 0.25s ease;
			}

			.krv-landing-contacts a:hover {
				border-color: var(--accent-hover);
				background: var(--accent-bg);
				box-shadow: var(--card-shadow);
			}

			.krv-landing-social-icon {
				display: block;
				width: 22px;
				height: 22px;
				color: currentColor;
				flex-shrink: 0;
			}

			.krv-landing-social-icon path,
			.krv-landing-social-icon circle,
			.krv-landing-social-icon rect {
				fill: currentColor;
			}

			.krv-landing-services {
				padding: 15px 0 0;
			}

			.krv-landing-services-header {
				margin-bottom: 20px;
				text-align: center;
			}

			.krv-landing-services-header h2 {
				margin: 0 0 10px;
				font-size: 2rem;
				color: var(--text-main);
			}

			.krv-landing-services-header p {
				margin: 0;
				font-size: 1rem;
				color: var(--text-soft);
			}

			.krv-landing-services-grid {
				display: grid;
				grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
				gap: 20px;
				width: 100%;
			}

			.krv-landing-service-item {
				width: 100%;
				padding: 15px;
				text-align: center;
				background: var(--card-bg);
				border-radius: var(--service-radius);
				box-shadow: var(--card-shadow);
				transition: box-shadow 0.25s ease;
			}

			.krv-landing-service-item:hover {
				box-shadow: var(--card-shadow-hover);
			}

			.krv-landing-service-icon {
				display: inline-block;
				width: 34px;
				height: 34px;
				margin-bottom: 15px;
				color: var(--accent);
			}

			.krv-landing-service-icon path,
			.krv-landing-service-icon circle,
			.krv-landing-service-icon rect,
			.krv-landing-service-icon polyline,
			.krv-landing-service-icon line {
				fill: none;
				stroke: currentColor;
				stroke-width: 1.8;
				stroke-linecap: round;
				stroke-linejoin: round;
			}

			.krv-landing-service-item h3 {
				margin: 0 0 10px;
				font-size: 1.25rem;
				color: var(--text-main);
			}

			.krv-landing-service-item p {
				margin: 0;
				font-size: 0.9rem;
				line-height: 1.5;
				color: var(--text-soft);
			}

			.krv-landing-pricing {
				width: 100%;
				padding: 20px;
				text-align: center;
				background: var(--card-bg);
				border-radius: var(--card-radius);
				box-shadow: var(--card-shadow);
			}

			.krv-landing-pricing-title {
				margin: 0 0 15px;
				font-size: 2rem;
				line-height: 1.2;
				color: var(--text-main);
			}

			.krv-landing-pricing-lead {
				margin: 0 0 18px;
				font-size: 1.1rem;
				line-height: 1.6;
				color: var(--text-soft);
			}

			.krv-landing-pricing-rate {
				display: inline-block;
				margin-top: 6px;
				font-size: 2rem;
				font-weight: 700;
				line-height: 1.2;
				color: var(--accent);
			}

			.krv-landing-pricing-list {
				list-style: none;
				margin: 0 0 22px;
				padding: 0;
				display: grid;
				gap: 12px;
			}

			.krv-landing-pricing-list li {
				display: flex;
				align-items: flex-start;
				justify-content: center;
				gap: 10px;
				font-size: 1rem;
				line-height: 1.5;
				color: var(--text-soft);
				text-align: left;
			}

			.krv-landing-pricing-icon {
				flex: 0 0 20px;
				width: 20px;
				height: 20px;
				margin-top: 2px;
				color: var(--accent);
			}

			.krv-landing-pricing-icon path,
			.krv-landing-pricing-icon circle,
			.krv-landing-pricing-icon rect,
			.krv-landing-pricing-icon polyline,
			.krv-landing-pricing-icon line {
				fill: none;
				stroke: currentColor;
				stroke-width: 1.8;
				stroke-linecap: round;
				stroke-linejoin: round;
			}

			.krv-landing-pricing-button {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				min-height: 46px;
				padding: 12px 22px;
				border-radius: 10px;
				background: var(--accent);
				color: #fff;
				text-decoration: none;
				font-size: 1rem;
				font-weight: 600;
				line-height: 1;
				transition: background 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
			}

			.krv-landing-pricing-button:hover {
				background: var(--accent-hover);
				box-shadow: var(--card-shadow-hover);
			}

			.krv-landing-pricing-button:active {
				transform: translateY(1px);
			}

			@media (max-width: 890px) {
				.krv-landing-services-grid {
					grid-template-columns: 1fr;
				}
			}

			@media (max-width: 768px) {
				.krv-services-landing {
					padding: 16px 10px;
				}

				.krv-landing-contact-card,
				.krv-landing-pricing {
					padding: 15px;
				}

				.krv-landing-avatar-wrap {
					width: 112px;
					height: 112px;
				}

				.krv-landing-title,
				.krv-landing-services-header h2,
				.krv-landing-pricing-title {
					font-size: 1.5rem;
				}

				.krv-landing-lead,
				.krv-landing-meta,
				.krv-landing-services-header p,
				.krv-landing-pricing-lead {
					font-size: 0.95rem;
				}

				.krv-landing-contacts {
					gap: 10px;
				}

				.krv-landing-contacts a {
					width: 40px;
					height: 40px;
				}

				.krv-landing-social-icon {
					width: 20px;
					height: 20px;
				}

				.krv-landing-service-icon {
					width: 30px;
					height: 30px;
				}

				.krv-landing-pricing-rate {
					font-size: 1.8rem;
				}

				.krv-landing-pricing-list li {
					font-size: 0.95rem;
				}
			}

			@media (max-width: 480px) {
				.krv-services-landing {
					padding: 12px 8px;
				}

				.krv-landing-contact-card,
				.krv-landing-pricing {
					padding: 10px;
				}

				.krv-landing-avatar-wrap {
					width: 104px;
					height: 104px;
				}

				.krv-landing-contacts {
					gap: 8px;
				}

				.krv-landing-contacts a {
					width: 36px;
					height: 36px;
				}

				.krv-landing-social-icon {
					width: 18px;
					height: 18px;
				}

				.krv-landing-service-icon {
					width: 28px;
					height: 28px;
				}

				.krv-landing-title,
				.krv-landing-services-header h2,
				.krv-landing-pricing-title {
					font-size: 1.5rem;
				}

				.krv-landing-pricing-rate {
					font-size: 1.6rem;
				}

				.krv-landing-pricing-list li {
					gap: 8px;
					font-size: 0.9rem;
				}

				.krv-landing-pricing-button {
					width: 100%;
					max-width: 280px;
					padding: 10px 16px;
					font-size: 0.9rem;
				}
			}
		</style>

		<div class="krv-services-landing-section">
			<div class="krv-landing-contact-card">
				<div class="krv-landing-avatar-wrap">
					<img class="krv-landing-avatar" src="https://krivoshein.site/wp-content/uploads/2026/06/drslon_avatar.png" alt="Алексей Кривошеин">
				</div>

				<h2 class="krv-landing-title">Алексей Кривошеин</h2>

				<p class="krv-landing-lead">Специализируюсь на разработке и продвижении веб-сайтов, администрировании серверов и поддержке существующих проектов.</p>

				<div class="krv-landing-meta">
					<span class="krv-landing-meta-line">Работаю по договору и принимаю безналичный расчёт.</span>
					<span class="krv-landing-meta-line">ОГРН 321774600479249</span>
					<span class="krv-landing-meta-line">ИНН 770603253213</span>
				</div>

				<div class="krv-landing-contacts">
					<a href="https://t.me/DrSlon" target="_blank" title="Telegram" rel="noopener noreferrer" aria-label="Telegram">
						<svg class="krv-landing-social-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21.543 2.498a1.53 1.53 0 0 0-1.58-.26L3.55 8.617a1.54 1.54 0 0 0 .08 2.893l4.11 1.353 1.59 5.01a1.54 1.54 0 0 0 2.52.66l2.29-2.21 3.78 2.78a1.54 1.54 0 0 0 2.42-.9L21.98 4.01a1.53 1.53 0 0 0-.437-1.512ZM9.33 11.97l8.09-4.98-6.7 6.46-.26 2.76-1.13-4.24Z"/></svg>
					</a>

					<a href="https://github.com/A-Krivoshen" target="_blank" title="GitHub" rel="noopener noreferrer" aria-label="GitHub">
						<svg class="krv-landing-social-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 2C6.48 2 2 6.59 2 12.25c0 4.53 2.87 8.37 6.84 9.73.5.1.68-.22.68-.49 0-.24-.01-1.03-.01-1.87-2.78.62-3.37-1.21-3.37-1.21-.45-1.18-1.11-1.49-1.11-1.49-.91-.64.07-.63.07-.63 1 .07 1.53 1.06 1.53 1.06.9 1.57 2.35 1.12 2.92.85.09-.67.35-1.12.64-1.38-2.22-.26-4.56-1.15-4.56-5.1 0-1.13.39-2.06 1.03-2.79-.1-.26-.45-1.31.1-2.74 0 0 .84-.28 2.75 1.07A9.32 9.32 0 0 1 12 6.84c.85 0 1.71.12 2.51.36 1.9-1.35 2.74-1.07 2.74-1.07.55 1.43.2 2.48.1 2.74.64.73 1.03 1.66 1.03 2.79 0 3.96-2.34 4.83-4.57 5.09.36.32.68.95.68 1.92 0 1.39-.01 2.5-.01 2.84 0 .27.18.59.69.49A10.25 10.25 0 0 0 22 12.25C22 6.59 17.52 2 12 2Z"/></svg>
					</a>

					<a href="https://vk.com/drslon" target="_blank" title="VK" rel="noopener noreferrer" aria-label="VK">
						<svg class="krv-landing-social-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3.61 5.18c.13 6.32 3.3 10.12 8.86 10.12h.32v-3.62c2.04.21 3.58 1.7 4.2 3.62H20c-.79-2.88-2.87-4.47-4.17-5.08 1.3-.76 3.12-2.59 3.56-5.04h-2.74c-.57 1.99-2.25 3.82-3.86 3.99V5.18H10.1v7c-1.63-.42-3.7-2.39-3.79-7H3.61Z"/></svg>
					</a>

					<a href="https://mastodon.ml/@krivoshein" target="_blank" title="Mastodon" rel="noopener noreferrer" aria-label="Mastodon">
						<svg class="krv-landing-social-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20.94 14c-.28 1.41-2.45 2.96-4.95 3.25-1.3.15-2.58.3-3.95.24-2.24-.1-4-.5-4-.5v.62c.32 2.22 2.25 2.35 4.03 2.41 1.8.05 3.4-.43 3.4-.43l.08 1.65s-1.26.69-3.5.82c-1.23.07-2.76-.03-4.54-.48-3.86-.95-4.52-4.78-4.63-8.67-.03-1.16-.01-2.25-.01-3.16 0-3.98 2.61-5.15 2.61-5.15C6.8 3.9 9.03 3.6 11.23 3.58h.05c2.2.02 4.43.32 5.75.99 0 0 2.61 1.17 2.61 5.15 0 0 .03 2.93-.7 4.28Zm-3.1-4.39c0-.98-.25-1.76-.77-2.33-.54-.57-1.24-.87-2.12-.87-1.01 0-1.78.39-2.3 1.18l-.5.83-.5-.83c-.52-.79-1.29-1.18-2.3-1.18-.88 0-1.58.3-2.12.87-.52.57-.77 1.35-.77 2.33v4.79h1.9V9.75c0-.98.41-1.48 1.24-1.48.91 0 1.37.59 1.37 1.75v2.56h1.88v-2.56c0-1.16.46-1.75 1.37-1.75.83 0 1.24.5 1.24 1.48v4.65h1.9V9.61Z"/></svg>
					</a>

					<a href="https://www.linkedin.com/in/krivosheinaleksey" target="_blank" title="LinkedIn" rel="noopener noreferrer" aria-label="LinkedIn">
						<svg class="krv-landing-social-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6.94 8.5H3.56V20h3.38V8.5ZM5.25 3A2.06 2.06 0 0 0 3.2 5.06c0 1.13.92 2.06 2.05 2.06A2.07 2.07 0 0 0 7.31 5.06 2.07 2.07 0 0 0 5.25 3Zm6.84 5.5H8.83V20h3.26v-6.05c0-1.6.3-3.14 2.25-3.14 1.92 0 1.95 1.8 1.95 3.24V20h3.27v-6.62c0-3.25-.7-5.74-4.5-5.74-1.82 0-3.04 1.02-3.54 1.99h-.05V8.5Z"/></svg>
					</a>

					<a href="https://krivoshein.site/max" target="_blank" title="MAX" rel="noopener noreferrer" aria-label="MAX">
						<svg class="krv-landing-social-icon" viewBox="7 7 22 22" aria-hidden="true" focusable="false"><path d="M18.1,28.3c-2,0-2.9-0.3-4.4-1.5c-1,1.3-4.2,2.3-4.3,0.6c0-1.3-0.3-2.4-0.6-3.6C8.4,22.4,8,20.8,8,18.4c0-5.7,4.7-10,10.2-10S28,13,28,18.4C27.9,23.9,23.6,28.3,18.1,28.3z M18.2,13.3c-2.7-0.1-4.8,1.7-5.2,4.7c-0.4,2.4,0.3,5.4,0.9,5.5c0.3,0.1,0.9-0.5,1.4-0.9c0.7,0.5,1.5,0.8,2.4,0.9c2.8,0.1,5.2-2,5.4-4.8C23.1,15.9,20.9,13.5,18.2,13.3L18.2,13.3z"/></svg>
					</a>
				</div>
			</div>
		</div>

		<div class="krv-services-landing-section">
			<div class="krv-landing-services" id="uslugi">
				<div class="krv-landing-services-header">
					<h2>Услуги</h2>
					<p>Индивидуальный подход и профессиональная реализация для вашего бизнеса</p>
				</div>

				<div class="krv-landing-services-grid">
					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline><line x1="14" y1="4" x2="10" y2="20"></line></svg>
						<h3>Веб-разработка</h3>
						<p>Создание и доработка сайтов на современных технологиях. Индивидуальные решения для любых задач.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="8" height="7" rx="1"></rect><rect x="13" y="4" width="8" height="7" rx="1"></rect><rect x="8" y="13" width="8" height="7" rx="1"></rect><line x1="7" y1="11" x2="12" y2="13"></line><line x1="17" y1="11" x2="12" y2="13"></line></svg>
						<h3>Интеграция сервисов в Docker</h3>
						<p>Создание и оптимизация контейнеров, упрощающих развертывание и масштабирование. Минимизация конфликтов окружения и ускорение разработки.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="6" rx="1"></rect><rect x="4" y="14" width="16" height="6" rx="1"></rect><circle cx="8" cy="7" r="0.8" style="fill:currentColor;stroke:none"></circle><circle cx="8" cy="17" r="0.8" style="fill:currentColor;stroke:none"></circle></svg>
						<h3>Настройка VPS</h3>
						<p>Установка и оптимизация виртуальных серверов под конкретные задачи. Гарантия стабильной и безопасной работы вашего проекта.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H8l-4 4V5a2 2 0 0 1 2-2h13a2 2 0 0 1 2 2Z"></path><circle cx="9" cy="10" r="0.8" style="fill:currentColor;stroke:none"></circle><circle cx="12" cy="10" r="0.8" style="fill:currentColor;stroke:none"></circle><circle cx="15" cy="10" r="0.8" style="fill:currentColor;stroke:none"></circle></svg>
						<h3>Разработка ботов для MAX</h3>
						<p>Создание чат-ботов для мессенджера MAX под задачи бизнеса и поддержки клиентов. Настройка сценариев, интеграций и автоматизации коммуникаций.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18"></path><path d="M12 3a14 14 0 0 1 0 18"></path><path d="M12 3a14 14 0 0 0 0 18"></path></svg>
						<h3>Услуги по регистрации домена</h3>
						<p>Подбор оптимального доменного имени и помощь в регистрации. Сопровождение и поддержка DNS-записей для корректной работы вашего сайта.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18h10a4 4 0 0 0 .4-8A6 6 0 0 0 6 11a3.5 3.5 0 0 0 1 7Z"></path></svg>
						<h3>Настройки и миграция в облако</h3>
						<p>Анализ текущей инфраструктуры и безопасный перенос в облачные сервисы. Оптимизация ресурсов и снижение расходов на ИТ.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 3v5c0 4.5-2.9 8.1-7 10-4.1-1.9-7-5.5-7-10V6l7-3Z"></path><path d="M9.5 12.5l1.8 1.8 3.7-4.1"></path></svg>
						<h3>Безопасность сайта</h3>
						<p>Комплексные меры защиты: от регулярных аудитов и установки SSL до нейтрализации вредоносного кода и настройки систем обнаружения вторжений.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"></circle><line x1="12" y1="12" x2="16.5" y2="9.5"></line><line x1="12" y1="12" x2="12" y2="7"></line></svg>
						<h3>Оптимизация скорости сайта</h3>
						<p>Ускорение загрузки за счёт оптимизации кода, баз данных и изображений. Повышение показателей производительности и удобства для пользователей.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="7" height="5" rx="1"></rect><rect x="14" y="4" width="7" height="5" rx="1"></rect><rect x="14" y="15" width="7" height="5" rx="1"></rect><line x1="10" y1="8.5" x2="14" y2="6.5"></line><line x1="10" y1="8.5" x2="14" y2="17.5"></line></svg>
						<h3>Подключение к CDN</h3>
						<p>Интеграция с сетью доставки контента для быстрой загрузки и надёжности при высоких нагрузках. Настройка кэширования и балансировки.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="6" width="16" height="12" rx="2"></rect><path d="M8 10.5h8"></path><path d="M8 13.5h5"></path></svg>
						<h3>Контекстная реклама</h3>
						<p>Создание и управление кампаниями в поисковых системах и соцсетях. Анализ эффективности и оптимизация бюджета для увеличения конверсий.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1 1 0 0 0 .2 1.1l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1 1 0 0 0-1.1-.2 1 1 0 0 0-.6.9V20a2 2 0 1 1-4 0v-.2a1 1 0 0 0-.6-.9 1 1 0 0 0-1.1.2l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1 1 0 0 0 .2-1.1 1 1 0 0 0-.9-.6H4a2 2 0 1 1 0-4h.2a1 1 0 0 0 .9-.6 1 1 0 0 0-.2-1.1l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1 1 0 0 0 1.1.2 1 1 0 0 0 .6-.9V4a2 2 0 1 1 4 0v.2a1 1 0 0 0 .6.9 1 1 0 0 0 1.1-.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1 1 0 0 0-.2 1.1 1 1 0 0 0 .9.6H20a2 2 0 1 1 0 4h-.2a1 1 0 0 0-.9.6Z"></path></svg>
						<h3>Техническая поддержка сайта</h3>
						<p>Оперативное реагирование на любые сбои, плановые обновления и контроль стабильности. Гарантия бесперебойной работы вашего ресурса.</p>
					</div>

					<div class="krv-landing-service-item">
						<svg class="krv-landing-service-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6"></circle><line x1="16" y1="16" x2="21" y2="21"></line></svg>
						<h3>SEO аудит сайта</h3>
						<p>Детальный анализ структуры, контента и технических параметров. Рекомендации по улучшению позиций сайта в поисковой выдаче.</p>
					</div>
				</div>
			</div>
		</div>

		<div class="krv-services-landing-section">
			<?php echo do_shortcode( '[krv_clients_grid]' ); ?>
		</div>

		<div class="krv-services-landing-section">
			<div class="krv-landing-pricing">
				<h2 class="krv-landing-pricing-title">Стоимость услуг</h2>

				<p class="krv-landing-pricing-lead">
					Индивидуальный подход к каждой задаче.<br>
					Моя базовая ставка:<br>
					<span class="krv-landing-pricing-rate">2000 ₽/час</span>
				</p>

				<ul class="krv-landing-pricing-list">
					<li>
						<svg class="krv-landing-pricing-icon" viewBox="0 0 24 24" aria-hidden="true">
							<line x1="12" y1="2" x2="12" y2="8"></line>
							<line x1="12" y1="16" x2="12" y2="22"></line>
							<line x1="4.93" y1="4.93" x2="9.17" y2="9.17"></line>
							<line x1="14.83" y1="14.83" x2="19.07" y2="19.07"></line>
							<line x1="2" y1="12" x2="8" y2="12"></line>
							<line x1="16" y1="12" x2="22" y2="12"></line>
							<line x1="4.93" y1="19.07" x2="9.17" y2="14.83"></line>
							<line x1="14.83" y1="9.17" x2="19.07" y2="4.93"></line>
						</svg>
						<span>Чем точнее описана задача, тем быстрее она будет выполнена.</span>
					</li>

					<li>
						<svg class="krv-landing-pricing-icon" viewBox="0 0 24 24" aria-hidden="true">
							<path d="M14 3h7v7"></path>
							<path d="M10 14L21 3"></path>
							<path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"></path>
						</svg>
						<span>Финальная стоимость зависит от сложности проекта и ваших ожиданий.</span>
					</li>

					<li>
						<svg class="krv-landing-pricing-icon" viewBox="0 0 24 24" aria-hidden="true">
							<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
						</svg>
						<span>Первичная консультация — бесплатно.</span>
					</li>
				</ul>

				<a class="krv-landing-pricing-button" href="https://krivoshein.site/contacts/" rel="noopener">
					Обсудить проект
				</a>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
} );
