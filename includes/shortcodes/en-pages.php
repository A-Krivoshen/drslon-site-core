<?php
/**
 * English commercial pages. Blog, projects and partners stay in Russian.
 *
 * [krv_en_home] [krv_en_services] [krv_en_price] [krv_en_consult] [krv_en_resume] [krv_en_contacts]
 *
 * Prices and facts match the Russian pages as of 2026-11-06. Nothing here is a new offer.
 *
 * @package drslon-site-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * English home: the Russian landing, with English words.
 */
function krv_en_home_shortcode(): string {
	if ( ! function_exists( 'krv_services_landing_render' ) ) {
		return '';
	}

	return krv_services_landing_render( 'en' );
}
add_shortcode( 'krv_en_home', 'krv_en_home_shortcode' );

/**
 * English index of the free tools. Same card grid as the Russian page.
 * The tools themselves stay in Russian.
 */
function krv_en_services_shortcode(): string {
	if ( ! function_exists( 'krv_services_showcase_render' ) ) {
		return '';
	}

	return krv_services_showcase_render( 'en' );
}
add_shortcode( 'krv_en_services', 'krv_en_services_shortcode' );

/**
 * English price list. The Russian widget, with English words and the same numbers.
 */
function krv_en_price_shortcode(): string {
	require_once __DIR__ . '/en-price.php';

	if ( ! function_exists( 'krv_en_price_settings' ) || ! function_exists( 'krv_price_list_render' ) ) {
		return '';
	}

	if ( function_exists( 'krv_en_price_prepare' ) ) {
		krv_en_price_prepare();
	}

	return krv_price_list_render( krv_en_price_settings() );
}
add_shortcode( 'krv_en_price', 'krv_en_price_shortcode' );

/**
 * Consultations. Same widget as the Russian page: 2000 ₽/hour, first talk free.
 * Markup lives in partials/consult-en.html so the FAQ toggle matches the live CSS.
 */
function krv_en_consult_shortcode(): string {
	$path = __DIR__ . '/partials/consult-en.html';
	if ( ! is_readable( $path ) ) {
		return '';
	}

	$html = file_get_contents( $path );

	return is_string( $html ) ? $html : '';
}
add_shortcode( 'krv_en_consult', 'krv_en_consult_shortcode' );

/**
 * Resume. Same jobs, dates and certificates as the Russian page.
 */
function krv_en_resume_shortcode(): string {
	ob_start();
	?>
	<div class="krv-resume-widget">
		<div class="krv-resume-shell">
			<section class="krv-resume-hero">
				<div class="krv-resume-hero-top">
					<div class="krv-resume-hero-left">
						<div class="krv-resume-badge">IT work for business · WordPress / Linux / DevOps / Python</div>
						<h2 class="krv-resume-name">Aleksey Sergeevich Krivoshein</h2>
						<p class="krv-resume-role">Sites · WordPress · Linux / DevOps · SEO and Yandex Direct</p>
						<p class="krv-resume-summary">I build and repair sites, set up Linux servers and look after web projects. WordPress, Nginx, Docker, SEO and Yandex Direct. I also use Python on the backend. I can put a site up and find the place where it started to break.</p>
					</div>
				</div>
				<div class="krv-resume-contacts">
					<a href="tel:+79636641615">+7 (963) 664-16-15</a>
					<a href="mailto:aleksey@krivoshein.site">aleksey@krivoshein.site</a>
					<a href="https://t.me/DrSlon" target="_blank" rel="noopener">@DrSlon</a>
				</div>
				<div class="krv-resume-tags">
					<span>WordPress</span><span>Linux</span><span>Docker</span><span>Nginx</span><span>PHP</span><span>Python</span><span>SEO</span><span>Yandex Direct</span><span>DevOps</span><span>VPS</span>
				</div>
			</section>
			<div class="krv-resume-grid">
				<div class="krv-resume-column">
					<section class="krv-resume-card">
						<h2>Work</h2>
						<div class="krv-resume-list">
							<div class="krv-resume-item">
								<div class="krv-resume-period">2021 — now</div>
								<h3>Sole trader</h3>
								<div class="krv-resume-company">ИП Кривошеин А.С.</div>
								<ul class="krv-resume-ul">
									<li>Sites, mostly WordPress: build, change and look after them.</li>
									<li>Linux servers, Nginx, SSL, Docker, backups and basic DevOps.</li>
									<li>SEO audit, technical cleanup, speed, analytics and contextual ads.</li>
									<li>Client projects from the domain and hosting through launch and later care.</li>
								</ul>
							</div>
							<div class="krv-resume-item">
								<div class="krv-resume-period">2017 — 2021</div>
								<h3>Independent practice</h3>
								<div class="krv-resume-company">WordPress, launch and care of web projects</div>
								<ul class="krv-resume-ul">
									<li>Promotion and care of WordPress sites.</li>
									<li>Changes to existing sites, bug fixes, basic server support.</li>
								</ul>
							</div>
							<div class="krv-resume-item">
								<div class="krv-resume-period">2010 — 2012</div>
								<h3>Microsoft engineer</h3>
								<div class="krv-resume-company">I-Teco (Ай-Теко)</div>
								<ul class="krv-resume-ul">
									<li>Corporate Microsoft infrastructure.</li>
									<li>SCOM, SCMS, ADMT, migration work and follow-up.</li>
								</ul>
							</div>
							<div class="krv-resume-item">
								<div class="krv-resume-period">2009 — 2010</div>
								<h3>Engineer, technical directorate</h3>
								<div class="krv-resume-company">INLINE Technologies</div>
								<p>The systems catalogue, monitoring, and infrastructure tasks.</p>
							</div>
							<div class="krv-resume-item">
								<div class="krv-resume-period">2001 — 2009</div>
								<h3>System administrator / engineer</h3>
								<div class="krv-resume-company">Nezavisimost Group, Transenercom, TPG TERNA, Seventh Continent</div>
								<p>System administration, IT infrastructure support, centralised support, engineering tasks.</p>
							</div>
						</div>
					</section>
					<section class="krv-resume-card">
						<h2>Skills</h2>
						<div class="krv-resume-stack">
							<span>WordPress</span><span>PHP</span><span>Python</span><span>Linux</span><span>Nginx</span><span>Docker</span><span>VPS and hosting</span><span>DevOps</span><span>SEO</span><span>Yandex Direct</span><span>Technical site audit</span><span>Speed</span><span>SSL / DNS</span><span>Backups and moves</span>
						</div>
					</section>
					<section class="krv-resume-card">
						<h2>Where I am useful</h2>
						<div class="krv-resume-note">Useful when the job is not only to assemble a site, but to leave it working: server, SSL, speed, basic SEO, analytics, ads and later care. The strong area is the joint between WordPress, Linux and the infrastructure.</div>
					</section>
				</div>
				<div class="krv-resume-column">
					<section class="krv-resume-card">
						<h2>Certificates</h2>
						<div class="krv-resume-cert-list">
							<div class="krv-resume-mini">
								<div class="krv-resume-mini-year">2026</div>
								<div class="krv-resume-mini-title">Yandex Direct</div>
								<div class="krv-resume-mini-text">Current Yandex certificate<br><a href="https://yandex.ru/adv/expert/certificates?certId=301983&amp;lastname=%D0%9A%D1%80%D0%B8%D0%B2%D0%BE%D1%88%D0%B5%D0%B8%D0%BD" target="_blank" rel="noopener">Open the certificate</a></div>
							</div>
							<div class="krv-resume-mini">
								<div class="krv-resume-mini-year">2025</div>
								<div class="krv-resume-mini-title">Yandex Direct</div>
								<div class="krv-resume-mini-text">The previous certificate<br><a href="https://yandex.ru/adv/expert/certificates?certId=283001&amp;lastname=%D0%9A%D1%80%D0%B8%D0%B2%D0%BE%D1%88%D0%B5%D0%B8%D0%BD" target="_blank" rel="noopener">Open the certificate</a></div>
							</div>
							<div class="krv-resume-mini">
								<div class="krv-resume-mini-year">Current</div>
								<div class="krv-resume-mini-title">Cloud.ru: Cloud Fundamentals</div>
								<div class="krv-resume-mini-text"><a href="https://awardme.ru/ru/users/aleksei-krivoshein-681f2de0" target="_blank" rel="noopener">Open the badge</a></div>
							</div>
							<div class="krv-resume-mini">
								<div class="krv-resume-mini-year">2005–2006</div>
								<div class="krv-resume-mini-title">Microsoft / MCSA 2003 Security</div>
								<div class="krv-resume-mini-text">The systems background is still there<br><a href="https://learn.microsoft.com/ru-ru/users/94294613/transcript/v2zelcogoxkm66n" target="_blank" rel="noopener">Open the transcript</a></div>
							</div>
						</div>
					</section>
					<section class="krv-resume-card">
						<h2>Education</h2>
						<div class="krv-resume-edu-list">
							<div class="krv-resume-mini">
								<div class="krv-resume-mini-year">2004</div>
								<div class="krv-resume-mini-title">KSPU</div>
								<div class="krv-resume-mini-text">Inter-university faculty of engineering and education</div>
							</div>
							<div class="krv-resume-mini">
								<div class="krv-resume-mini-year">2017</div>
								<div class="krv-resume-mini-title">Netology</div>
								<div class="krv-resume-mini-text">Further training: HTML layout<br><a href="https://krivoshein.site/wp-content/uploads/2024/11/povushenia_kvalificacia-scaled.jpg" target="_blank" rel="noopener">Open the document</a></div>
							</div>
						</div>
					</section>
					<section class="krv-resume-card">
						<h2>Languages</h2>
						<div class="krv-resume-lang">
							<div class="krv-resume-lang-row">
								<div class="krv-resume-lang-top"><span>Russian</span><span>native</span></div>
								<div class="krv-resume-lang-bar"><span style="width:100%;"></span></div>
							</div>
							<div class="krv-resume-lang-row">
								<div class="krv-resume-lang-top"><span>English</span><span>B1</span></div>
								<div class="krv-resume-lang-bar"><span style="width:60%;"></span></div>
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'krv_en_resume', 'krv_en_resume_shortcode' );

/**
 * Contacts. Same channels as the Russian page. There is no form.
 */
function krv_en_contacts_shortcode(): string {
	ob_start();
	?>
	<div id="krv-contact-block" class="krv-contact-block">
		<div class="krv-contact-grid">
			<div class="krv-contact-card">
				<h2 class="krv-contact-title">Contact</h2>
				<p class="krv-contact-lead">A question about a site, a server, ads or a project: write in whichever channel is easier.</p>
				<div class="krv-contact-list">
					<div class="krv-contact-item">
						<span class="krv-contact-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07A19.5 19.5 0 0 1 5.15 12.8 19.86 19.86 0 0 1 2.08 4.09 2 2 0 0 1 4.06 1.9h3a2 2 0 0 1 2 1.72c.12.91.35 1.8.68 2.65a2 2 0 0 1-.45 2.11L8 9.66a16 16 0 0 0 6.34 6.34l1.28-1.29a2 2 0 0 1 2.11-.45c.85.33 1.74.56 2.65.68A2 2 0 0 1 22 16.92z"></path></svg>
						</span>
						<a href="tel:+79636641615">+7 (963) 664-16-15</a>
					</div>
					<div class="krv-contact-item">
						<span class="krv-contact-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24"><path d="M4 4h16a2 2 0 0 1 2 2v.4l-10 6.25L2 6.4V6a2 2 0 0 1 2-2zm18 5.1V18a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9.1l9.47 5.92a1 1 0 0 0 1.06 0L22 9.1z"></path></svg>
						</span>
						<a href="mailto:aleksey@krivoshein.site">aleksey@krivoshein.site</a>
					</div>
					<div class="krv-contact-item">
						<span class="krv-contact-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24"><path d="M21.543 2.498a1.53 1.53 0 0 0-1.58-.26L3.55 8.617a1.54 1.54 0 0 0 .08 2.893l4.11 1.353 1.59 5.01a1.54 1.54 0 0 0 2.52.66l2.29-2.21 3.78 2.78a1.54 1.54 0 0 0 2.42-.9L21.98 4.01a1.53 1.53 0 0 0-.437-1.512ZM9.33 11.97l8.09-4.98-6.7 6.46-.26 2.76-1.13-4.24Z"></path></svg>
						</span>
						<a href="https://t.me/DrSlon" target="_blank" rel="noopener">@DrSlon</a>
					</div>
					<div class="krv-contact-item">
						<span class="krv-contact-icon" aria-hidden="true">
							<svg viewBox="7 7 22 22"><path d="M18.1,28.3c-2,0-2.9-0.3-4.4-1.5c-1,1.3-4.2,2.3-4.3,0.6c0-1.3-0.3-2.4-0.6-3.6C8.4,22.4,8,20.8,8,18.4c0-5.7,4.7-10,10.2-10S28,13,28,18.4C27.9,23.9,23.6,28.3,18.1,28.3z M18.2,13.3c-2.7-0.1-4.8,1.7-5.2,4.7c-0.4,2.4,0.3,5.4,0.9,5.5c0.3,0.1,0.9-0.5,1.4-0.9c0.7,0.5,1.5,0.8,2.4,0.9c2.8,0.1,5.2-2,5.4-4.8C23.1,15.9,20.9,13.5,18.2,13.3L18.2,13.3z"></path></svg>
						</span>
						<a href="<?php echo esc_url( home_url( '/max/' ) ); ?>" target="_blank" rel="noopener">MAX</a>
					</div>
				</div>
			</div>
			<div class="krv-contact-image-card">
				<a href="<?php echo esc_url( home_url( '/en/home/' ) ); ?>" class="krv-contact-home-link" title="English home">
					<div class="krv-contact-image-wrap">
						<img src="https://krivoshein.site/wp-content/uploads/2025/05/chatgpt-image-21-maya-2025-g.-11_12_07-1.png" alt="An elephant at a computer" class="krv-contact-image">
						<div class="krv-contact-caption">If you are not on the internet, you are not in the business</div>
					</div>
				</a>
			</div>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'krv_en_contacts', 'krv_en_contacts_shortcode' );
