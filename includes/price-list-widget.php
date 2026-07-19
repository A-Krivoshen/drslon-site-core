<?php
/**
 * ACF-driven price list shortcode [krv_price_list] for /prays-list/.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Append UTM parameters to a URL.
 */
function krv_price_list_utm_url( string $base_url, string $campaign ): string {
	return add_query_arg(
		array(
			'utm_source'   => 'krivoshein.site',
			'utm_medium'   => 'prays-list',
			'utm_campaign' => sanitize_key( $campaign ),
		),
		$base_url
	);
}

/**
 * Build contacts page URL with UTM and optional topic prefill.
 */
function krv_price_list_contacts_url( string $campaign, string $topic = '' ): string {
	$params = array(
		'utm_source'   => 'krivoshein.site',
		'utm_medium'   => 'prays-list',
		'utm_campaign' => sanitize_key( $campaign ),
	);

	if ( $topic !== '' ) {
		$params['topic'] = sanitize_key( $topic );
	}

	return add_query_arg( $params, home_url( '/contacts/' ) ) . '#krv-contact-block';
}

/**
 * Map an admin-friendly topic to the contacts URL.
 */
function krv_price_list_contacts_url_for_topic( string $topic ): string {
	switch ( sanitize_key( $topic ) ) {
		case 'diagnostic':
			return krv_price_list_contacts_url( 'diagnostic', 'diagnostic' );
		case 'repair':
			return krv_price_list_contacts_url( 'repair', 'repair' );
		case 'support':
			return krv_price_list_contacts_url( 'support', 'support' );
		case 'cta-bottom':
			return krv_price_list_contacts_url( 'cta-bottom' );
		default:
			return krv_price_list_contacts_url( 'general' );
	}
}

/**
 * Add UTM parameters to every known *.krivoshein.site landing automatically.
 */
function krv_price_list_maybe_utm_url( string $url ): string {
	$url = trim( $url );

	if ( $url === '' || $url[0] === '#' || $url[0] === '/' ) {
		return $url;
	}

	$host = wp_parse_url( $url, PHP_URL_HOST );

	if ( is_string( $host ) && preg_match( '/^([a-z0-9-]+)\.krivoshein\.site$/i', $host, $matches ) ) {
		return krv_price_list_utm_url( $url, strtolower( $matches[1] ) );
	}

	return $url;
}

/**
 * Static and safe target/rel HTML for external links.
 */
function krv_price_list_link_attrs( bool $new_tab ): string {
	return $new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';
}

/**
 * Extract non-empty text values from an ACF repeater.
 *
 * @param mixed $rows Repeater rows.
 * @return array<int, string>
 */
function krv_price_list_text_values( $rows ): array {
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$values = array();

	foreach ( $rows as $row ) {
		$text = is_array( $row ) ? trim( (string) ( $row['text'] ?? '' ) ) : '';

		if ( $text !== '' ) {
			$values[] = $text;
		}
	}

	return $values;
}

/**
 * Normalize tab IDs and remove empty rows before rendering ARIA controls.
 *
 * @param mixed $rows ACF repeater rows.
 * @return array<int, array<string, mixed>>
 */
function krv_price_list_normalize_tabs( $rows ): array {
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$tabs = array();
	$used = array();

	foreach ( $rows as $index => $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$label   = trim( (string) ( $row['tab_label'] ?? '' ) );
		$heading = trim( (string) ( $row['heading'] ?? '' ) );

		if ( $label === '' && $heading === '' ) {
			continue;
		}

		$base_slug = sanitize_title( (string) ( $row['tab_id'] ?? $label ) );
		$base_slug = $base_slug !== '' ? $base_slug : 'tab-' . ( $index + 1 );
		$slug      = $base_slug;
		$suffix    = 2;

		while ( isset( $used[ $slug ] ) ) {
			$slug = $base_slug . '-' . $suffix;
			++$suffix;
		}

		$used[ $slug ] = true;
		$row['tab_label'] = $label !== '' ? $label : $heading;
		$row['_slug']   = $slug;
		$tabs[]         = $row;
	}

	return $tabs;
}

/**
 * Resolve route URL from its ACF link mode.
 *
 * @param array<string, mixed> $route Route row.
 */
function krv_price_list_route_url( array $route ): string {
	if ( ( $route['link_mode'] ?? 'url' ) === 'contacts' ) {
		return krv_price_list_contacts_url_for_topic( (string) ( $route['contacts_topic'] ?? 'general' ) );
	}

	return krv_price_list_maybe_utm_url( (string) ( $route['url'] ?? '' ) );
}

/**
 * Resolve package CTA URL.
 *
 * @param array<string, mixed> $package Package row.
 */
function krv_price_list_package_url( array $package ): string {
	$type = (string) ( $package['cta_type'] ?? 'none' );

	if ( $type === 'none' ) {
		return '';
	}

	if ( $type === 'custom' ) {
		return krv_price_list_maybe_utm_url( (string) ( $package['cta_custom_url'] ?? '' ) );
	}

	return krv_price_list_contacts_url_for_topic( $type );
}

/**
 * Convert a phone display value into a tel: value.
 */
function krv_price_list_phone_href( string $phone ): string {
	$phone  = trim( $phone );
	$digits = preg_replace( '/\D+/', '', $phone ) ?: '';

	return isset( $phone[0] ) && $phone[0] === '+' ? '+' . $digits : $digits;
}

/**
 * Render a price value with an optional small note.
 */
function krv_price_list_render_price( string $price, string $note = '' ): void {
	if ( trim( $price ) === '' ) {
		return;
	}
	?>
	<div class="krv-price">
		<?php echo esc_html( $price ); ?>
		<?php if ( trim( $note ) !== '' ) : ?>
			<span class="krv-price-small-inline">(<?php echo esc_html( trim( $note, " \t\n\r\0\x0B()" ) ); ?>)</span>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render the complete ACF-driven price list.
 */
function krv_price_list_render(): string {
	$settings = function_exists( 'krv_price_list_get_settings' )
		? krv_price_list_get_settings()
		: array();

	$routes        = is_array( $settings['routes'] ?? null ) ? $settings['routes'] : array();
	$landings      = is_array( $settings['landings'] ?? null ) ? $settings['landings'] : array();
	$packages      = is_array( $settings['packages'] ?? null ) ? $settings['packages'] : array();
	$tabs          = krv_price_list_normalize_tabs( $settings['price_tabs'] ?? array() );
	$stack_items   = krv_price_list_text_values( $settings['stack_items'] ?? array() );
	$process_steps = is_array( $settings['process_steps'] ?? null ) ? $settings['process_steps'] : array();
	$faq_items     = is_array( $settings['faq_items'] ?? null ) ? $settings['faq_items'] : array();

	$show_scenarios = ! empty( $settings['show_scenarios'] ) && ! empty( $routes );
	$show_packages  = ! empty( $settings['show_packages'] ) && ! empty( $packages );
	$show_prices    = ! empty( $settings['show_prices'] ) && ! empty( $tabs );
	$show_stack     = ! empty( $settings['show_stack'] ) && ! empty( $stack_items );
	$show_process   = ! empty( $settings['show_process'] ) && ! empty( $process_steps );
	$show_faq       = ! empty( $settings['show_faq'] ) && ! empty( $faq_items );
	$show_cta       = ! empty( $settings['show_cta'] );

	$package_anchors    = array();
	$package_anchor_ids = array();
	$used_anchors       = array_fill_keys( array( 'krv-scenarios', 'krv-packages', 'krv-prices', 'krv-faq' ), true );
	foreach ( $packages as $package_index => $package ) {
		if ( ! is_array( $package ) ) {
			continue;
		}

		$base_anchor = sanitize_title( (string) ( $package['anchor_id'] ?? '' ) );
		$anchor      = $base_anchor;
		$suffix      = 2;

		while ( $anchor !== '' && isset( $used_anchors[ $anchor ] ) ) {
			$anchor = $base_anchor . '-' . $suffix;
			++$suffix;
		}

		if ( $anchor !== '' ) {
			$used_anchors[ $anchor ]              = true;
			$package_anchors[]                    = $anchor;
			$package_anchor_ids[ $package_index ] = $anchor;
		}
	}

	$available_anchors = $show_packages ? $package_anchors : array();
	if ( $show_scenarios ) {
		$available_anchors[] = 'krv-scenarios';
	}
	if ( $show_packages ) {
		$available_anchors[] = 'krv-packages';
	}
	if ( $show_prices ) {
		$available_anchors[] = 'krv-prices';
	}
	if ( $show_faq ) {
		$available_anchors[] = 'krv-faq';
	}

	$hero_anchor      = trim( (string) ( $settings['hero_panel_anchor'] ?? '' ) );
	$hero_anchor_id   = sanitize_title( ltrim( $hero_anchor, '#' ) );
	$show_hero_anchor = $hero_anchor_id !== '' && in_array( $hero_anchor_id, $available_anchors, true );

	$contacts_general    = krv_price_list_contacts_url_for_topic( 'general' );
	$contacts_diagnostic = krv_price_list_contacts_url_for_topic( 'diagnostic' );
	$contacts_cta        = krv_price_list_contacts_url_for_topic( 'cta-bottom' );
	$telegram_url        = krv_price_list_utm_url( 'https://t.me/DrSlon', 'telegram-chat' );
	$max_url             = home_url( '/max' );
	$max_icon            = function_exists( 'krv_max_messenger_icon_svg' )
		? krv_max_messenger_icon_svg( 'krv-max-icon' )
		: '';

	$phone      = trim( (string) ( $settings['hero_phone'] ?? '' ) );
	$phone_href = krv_price_list_phone_href( $phone );
	$email      = sanitize_email( (string) ( $settings['hero_email'] ?? '' ) );

	$nav_links = array();
	if ( $show_scenarios ) {
		$nav_links['krv-scenarios'] = 'Направления';
	}
	if ( $show_packages ) {
		$nav_links['krv-packages'] = 'Пакеты';
	}
	if ( $show_prices ) {
		$nav_links['krv-prices'] = 'Цены';
	}
	if ( $show_faq ) {
		$nav_links['krv-faq'] = 'FAQ';
	}

	ob_start();
	?>
	<div class="krv-price-widget">
		<div class="krv-price-shell">
			<section class="krv-hero">
				<div class="krv-hero-grid">
					<div class="krv-hero-main">
						<?php if ( trim( (string) ( $settings['hero_badge'] ?? '' ) ) !== '' ) : ?>
							<div class="krv-badge"><?php echo esc_html( $settings['hero_badge'] ); ?></div>
						<?php endif; ?>

						<h2 class="krv-title"><?php echo esc_html( (string) ( $settings['hero_title'] ?? '' ) ); ?></h2>
						<p class="krv-subtitle"><?php echo esc_html( (string) ( $settings['hero_subtitle'] ?? '' ) ); ?></p>
						<p class="krv-lead"><?php echo esc_html( (string) ( $settings['hero_lead'] ?? '' ) ); ?></p>

						<div class="krv-actions">
							<a class="krv-btn krv-btn-primary" href="<?php echo esc_url( $contacts_general ); ?>">Обсудить задачу</a>
							<a class="krv-btn" href="<?php echo esc_url( $telegram_url ); ?>" target="_blank" rel="noopener noreferrer">Telegram</a>
							<a class="krv-btn krv-btn-max" href="<?php echo esc_url( $max_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo $max_icon; ?><span>MAX</span></a>
						</div>

						<?php if ( $phone_href !== '' || $email !== '' ) : ?>
							<div class="krv-contacts">
								<?php if ( $phone_href !== '' ) : ?>
									<a href="tel:<?php echo esc_attr( $phone_href ); ?>"><?php echo esc_html( $phone ); ?></a>
								<?php endif; ?>
								<?php if ( $email !== '' ) : ?>
									<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>

					<div class="krv-hero-panel krv-hero-panel-accent">
						<h2><?php echo esc_html( (string) ( $settings['hero_panel_title'] ?? '' ) ); ?></h2>
						<p><?php echo esc_html( (string) ( $settings['hero_panel_text'] ?? '' ) ); ?></p>
						<p class="krv-hero-panel-note"><?php echo wp_kses( (string) ( $settings['hero_panel_note'] ?? '' ), array( 'strong' => array(), 'em' => array(), 'br' => array() ) ); ?></p>

						<div class="krv-hero-panel-quick">
							<span class="krv-hero-panel-quick-label"><?php echo esc_html( (string) ( $settings['hero_panel_quick_label'] ?? '' ) ); ?></span>
							<div class="krv-hero-messengers">
								<a class="krv-btn krv-btn-messenger" href="<?php echo esc_url( $telegram_url ); ?>" target="_blank" rel="noopener noreferrer">Telegram</a>
								<a class="krv-btn krv-btn-messenger krv-btn-max" href="<?php echo esc_url( $max_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo $max_icon; ?><span>MAX</span></a>
							</div>
						</div>

						<?php if ( trim( (string) ( $settings['hero_panel_diagnostic_label'] ?? '' ) ) !== '' ) : ?>
							<a class="krv-btn krv-btn-primary krv-btn-block" href="<?php echo esc_url( $contacts_diagnostic ); ?>"><?php echo esc_html( (string) $settings['hero_panel_diagnostic_label'] ); ?></a>
						<?php endif; ?>
						<p class="krv-hero-panel-meta"><?php echo esc_html( (string) ( $settings['hero_panel_diagnostic_meta'] ?? '' ) ); ?></p>
						<?php if ( $show_hero_anchor && trim( (string) ( $settings['hero_panel_anchor_label'] ?? '' ) ) !== '' ) : ?>
							<a class="krv-hero-panel-link" href="<?php echo esc_url( $hero_anchor ); ?>"><?php echo esc_html( (string) $settings['hero_panel_anchor_label'] ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</section>

			<?php if ( function_exists( 'krv_price_list_render_trust_strip' ) ) : ?>
				<?php echo krv_price_list_render_trust_strip( $settings ); ?>
			<?php endif; ?>

			<?php if ( ! empty( $nav_links ) ) : ?>
				<nav class="krv-anchor-nav krv-anchor-nav-sticky" aria-label="Навигация по разделам прайса">
					<?php foreach ( $nav_links as $anchor => $label ) : ?>
						<a href="#<?php echo esc_attr( $anchor ); ?>"><?php echo esc_html( $label ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>

			<?php if ( $show_scenarios ) : ?>
				<section class="krv-section" id="krv-scenarios">
					<div class="krv-section-head">
						<h2><?php echo esc_html( (string) ( $settings['scenarios_title'] ?? '' ) ); ?></h2>
						<p><?php echo esc_html( (string) ( $settings['scenarios_text'] ?? '' ) ); ?></p>
					</div>

					<div class="krv-route-grid">
						<?php foreach ( $routes as $route ) : ?>
							<?php
							if ( ! is_array( $route ) ) {
								continue;
							}

							$route_url = krv_price_list_route_url( $route );
							$route_text = trim( implode( ' ', array( $route['kicker'] ?? '', $route['title'] ?? '', $route['text'] ?? '', $route['go_label'] ?? '' ) ) );
							if ( $route_url === '' || $route_text === '' ) {
								continue;
							}
							?>
							<a class="krv-route-card" href="<?php echo esc_url( $route_url ); ?>"<?php echo krv_price_list_link_attrs( ! empty( $route['new_tab'] ) ); ?>>
								<?php if ( trim( (string) ( $route['kicker'] ?? '' ) ) !== '' ) : ?>
									<span class="krv-kicker"><?php echo esc_html( $route['kicker'] ); ?></span>
								<?php endif; ?>
								<span class="krv-route-title"><?php echo esc_html( (string) ( $route['title'] ?? '' ) ); ?></span>
								<span class="krv-route-text"><?php echo esc_html( (string) ( $route['text'] ?? '' ) ); ?></span>
								<span class="krv-route-go"><?php echo esc_html( (string) ( $route['go_label'] ?? '' ) ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>

					<?php if ( ! empty( $landings ) ) : ?>
						<div class="krv-landings-strip">
							<span class="krv-landings-strip-label"><?php echo esc_html( (string) ( $settings['landings_label'] ?? '' ) ); ?></span>
							<?php foreach ( $landings as $landing ) : ?>
								<?php
								if ( ! is_array( $landing ) ) {
									continue;
								}
								$url = krv_price_list_maybe_utm_url( (string) ( $landing['url'] ?? '' ) );
								$label = trim( (string) ( $landing['label'] ?? '' ) );
								if ( $url === '' || $label === '' ) {
									continue;
								}
								?>
								<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $label ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</section>
			<?php endif; ?>

			<?php if ( $show_packages ) : ?>
				<section class="krv-section" id="krv-packages">
					<div class="krv-section-head">
						<h2><?php echo esc_html( (string) ( $settings['packages_title'] ?? '' ) ); ?></h2>
						<p><?php echo esc_html( (string) ( $settings['packages_text'] ?? '' ) ); ?></p>
					</div>

					<div class="krv-package-grid">
						<?php foreach ( $packages as $package_index => $package ) : ?>
							<?php
							if ( ! is_array( $package ) ) {
								continue;
							}

							$features = krv_price_list_text_values( $package['features'] ?? array() );
							$cta_url  = krv_price_list_package_url( $package );
							$anchor   = $package_anchor_ids[ $package_index ] ?? '';
							$classes  = 'krv-package' . ( ! empty( $package['featured'] ) ? ' krv-package-accent' : '' );
							?>
							<div class="<?php echo esc_attr( $classes ); ?>"<?php echo $anchor !== '' ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
								<?php if ( trim( (string) ( $package['kicker'] ?? '' ) ) !== '' ) : ?>
									<div class="krv-kicker"><?php echo esc_html( $package['kicker'] ); ?></div>
								<?php endif; ?>
								<h3 class="krv-card-title"><?php echo esc_html( (string) ( $package['title'] ?? '' ) ); ?></h3>
								<?php krv_price_list_render_price( (string) ( $package['price'] ?? '' ) ); ?>
								<p class="krv-text"><?php echo esc_html( (string) ( $package['text'] ?? '' ) ); ?></p>
								<?php if ( ! empty( $features ) ) : ?>
									<ul class="krv-list">
										<?php foreach ( $features as $feature ) : ?>
											<li><?php echo esc_html( $feature ); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
								<?php if ( $cta_url !== '' && trim( (string) ( $package['cta_label'] ?? '' ) ) !== '' ) : ?>
									<?php $cta_class = ( $package['cta_style'] ?? 'secondary' ) === 'primary' ? 'krv-btn-primary' : 'krv-btn-secondary'; ?>
									<a class="krv-btn <?php echo esc_attr( $cta_class ); ?> krv-package-cta" href="<?php echo esc_url( $cta_url ); ?>"<?php echo krv_price_list_link_attrs( ! empty( $package['cta_new_tab'] ) ); ?>><?php echo esc_html( $package['cta_label'] ); ?></a>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $show_prices ) : ?>
				<section class="krv-section" id="krv-prices">
					<div class="krv-section-head">
						<h2><?php echo esc_html( (string) ( $settings['prices_title'] ?? '' ) ); ?></h2>
						<p><?php echo esc_html( (string) ( $settings['prices_text'] ?? '' ) ); ?></p>
					</div>

					<div class="krv-prices-tabs" role="tablist" aria-label="Категории услуг">
						<?php foreach ( $tabs as $index => $tab ) : ?>
							<?php $slug = $tab['_slug']; ?>
							<button type="button" class="krv-prices-tab<?php echo $index === 0 ? ' is-active' : ''; ?>" role="tab" id="krv-tab-<?php echo esc_attr( $slug ); ?>" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="krv-panel-<?php echo esc_attr( $slug ); ?>" tabindex="<?php echo $index === 0 ? '0' : '-1'; ?>"><?php echo esc_html( (string) ( $tab['tab_label'] ?? $tab['heading'] ?? '' ) ); ?></button>
						<?php endforeach; ?>
					</div>

					<div class="krv-prices-panels">
						<?php foreach ( $tabs as $index => $tab ) : ?>
							<?php
							$slug           = $tab['_slug'];
							$items          = is_array( $tab['items'] ?? null ) ? $tab['items'] : array();
							$panel_features = krv_price_list_text_values( $tab['panel_features'] ?? array() );
							$all_mini       = ! empty( $items );

							foreach ( $items as $item ) {
								if ( ! is_array( $item ) || ( $item['style'] ?? 'card' ) !== 'mini' ) {
									$all_mini = false;
									break;
								}
							}
							?>
							<div class="krv-prices-panel<?php echo $index === 0 ? ' is-active' : ''; ?>" role="tabpanel" id="krv-panel-<?php echo esc_attr( $slug ); ?>" aria-labelledby="krv-tab-<?php echo esc_attr( $slug ); ?>"<?php echo $index === 0 ? '' : ' hidden'; ?>>
								<div class="krv-service-card">
									<h2><?php echo esc_html( (string) ( $tab['heading'] ?? '' ) ); ?></h2>

									<?php if ( trim( (string) ( $tab['landing_url'] ?? '' ) ) !== '' && trim( (string) ( $tab['landing_label'] ?? '' ) ) !== '' ) : ?>
										<a class="krv-service-cta" href="<?php echo esc_url( krv_price_list_maybe_utm_url( (string) $tab['landing_url'] ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( (string) ( $tab['landing_label'] ?? '' ) ); ?></a>
									<?php endif; ?>

									<?php if ( trim( (string) ( $tab['intro'] ?? '' ) ) !== '' ) : ?>
										<p class="krv-text krv-tab-intro"><?php echo esc_html( $tab['intro'] ); ?></p>
									<?php endif; ?>

									<?php if ( ! empty( $items ) ) : ?>
										<div class="<?php echo $all_mini ? 'krv-mini-list' : 'krv-service-list'; ?>">
											<?php foreach ( $items as $item ) : ?>
												<?php
												if ( ! is_array( $item ) ) {
													continue;
												}

												$style   = in_array( $item['style'] ?? 'card', array( 'card', 'clean', 'mini' ), true ) ? $item['style'] : 'card';
												$cta_url = krv_price_list_maybe_utm_url( (string) ( $item['cta_url'] ?? '' ) );
												?>

												<?php if ( $style === 'mini' ) : ?>
													<div class="krv-mini">
														<?php if ( trim( (string) ( $item['kicker'] ?? '' ) ) !== '' ) : ?><div class="krv-mini-label"><?php echo esc_html( $item['kicker'] ); ?></div><?php endif; ?>
														<div class="krv-mini-title"><?php echo esc_html( (string) ( $item['title'] ?? '' ) ); ?></div>
														<div class="krv-mini-text">
															<?php echo esc_html( (string) ( $item['text'] ?? '' ) ); ?>
															<?php if ( trim( (string) ( $item['price'] ?? '' ) ) !== '' ) : ?><br><strong><?php echo esc_html( $item['price'] ); ?></strong><?php endif; ?>
														</div>
													</div>
												<?php else : ?>
													<div class="<?php echo $style === 'clean' ? 'krv-service-item-clean' : 'krv-service-item'; ?>">
														<?php if ( $style === 'card' && trim( (string) ( $item['kicker'] ?? '' ) ) !== '' ) : ?><div class="krv-kicker"><?php echo esc_html( $item['kicker'] ); ?></div><?php endif; ?>
														<h3 class="krv-card-title"><?php echo esc_html( (string) ( $item['title'] ?? '' ) ); ?></h3>
														<?php krv_price_list_render_price( (string) ( $item['price'] ?? '' ), (string) ( $item['price_note'] ?? '' ) ); ?>
														<p class="krv-text"><?php echo esc_html( (string) ( $item['text'] ?? '' ) ); ?></p>
														<?php if ( $cta_url !== '' && trim( (string) ( $item['cta_label'] ?? '' ) ) !== '' ) : ?>
															<a class="krv-service-cta" href="<?php echo esc_url( $cta_url ); ?>"<?php echo krv_price_list_link_attrs( ! empty( $item['cta_new_tab'] ) ); ?>><?php echo esc_html( $item['cta_label'] ); ?></a>
														<?php endif; ?>
													</div>
												<?php endif; ?>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>

									<?php if ( ! empty( $panel_features ) ) : ?>
										<ul class="krv-list">
											<?php foreach ( $panel_features as $feature ) : ?><li><?php echo esc_html( $feature ); ?></li><?php endforeach; ?>
										</ul>
									<?php endif; ?>

									<?php if ( trim( (string) ( $tab['panel_note'] ?? '' ) ) !== '' || trim( (string) ( $tab['panel_note_link_url'] ?? '' ) ) !== '' ) : ?>
										<div class="krv-note">
											<?php echo esc_html( (string) ( $tab['panel_note'] ?? '' ) ); ?>
											<?php if ( trim( (string) ( $tab['panel_note_link_url'] ?? '' ) ) !== '' && trim( (string) ( $tab['panel_note_link_label'] ?? '' ) ) !== '' ) : ?>
												<a class="krv-service-cta" href="<?php echo esc_url( krv_price_list_maybe_utm_url( (string) $tab['panel_note_link_url'] ) ); ?>"<?php echo krv_price_list_link_attrs( ! empty( $tab['panel_note_new_tab'] ) ); ?>><?php echo esc_html( (string) ( $tab['panel_note_link_label'] ?? '' ) ); ?></a>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $show_stack ) : ?>
				<section class="krv-section">
					<div class="krv-info-card krv-info-card-wide">
						<h2><?php echo esc_html( (string) ( $settings['stack_title'] ?? '' ) ); ?></h2>
						<div class="krv-stack">
							<?php foreach ( $stack_items as $item ) : ?><span><?php echo esc_html( $item ); ?></span><?php endforeach; ?>
						</div>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $show_process ) : ?>
				<section class="krv-section">
					<div class="krv-section-head">
						<h2><?php echo esc_html( (string) ( $settings['process_title'] ?? '' ) ); ?></h2>
						<p><?php echo esc_html( (string) ( $settings['process_text'] ?? '' ) ); ?></p>
					</div>
					<div class="krv-process-grid">
						<?php foreach ( $process_steps as $index => $step ) : ?>
							<?php if ( is_array( $step ) ) : ?>
								<div class="krv-step">
									<div class="krv-step-num"><?php echo esc_html( (string) ( $index + 1 ) ); ?></div>
									<h3><?php echo esc_html( (string) ( $step['title'] ?? '' ) ); ?></h3>
									<p><?php echo esc_html( (string) ( $step['text'] ?? '' ) ); ?></p>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $show_faq ) : ?>
				<section class="krv-section" id="krv-faq">
					<div class="krv-section-head"><h2><?php echo esc_html( (string) ( $settings['faq_title'] ?? '' ) ); ?></h2></div>
					<div class="krv-faq">
						<?php foreach ( $faq_items as $item ) : ?>
							<?php if ( is_array( $item ) && trim( (string) ( $item['question'] ?? '' ) ) !== '' ) : ?>
								<details class="krv-faq-item">
									<summary><?php echo esc_html( $item['question'] ); ?></summary>
									<p><?php echo esc_html( (string) ( $item['answer'] ?? '' ) ); ?></p>
								</details>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $show_cta ) : ?>
				<section class="krv-cta">
					<div class="krv-cta-grid">
						<div>
							<h2><?php echo esc_html( (string) ( $settings['cta_title'] ?? '' ) ); ?></h2>
							<p><?php echo esc_html( (string) ( $settings['cta_text'] ?? '' ) ); ?></p>
						</div>
						<?php if ( trim( (string) ( $settings['cta_label'] ?? '' ) ) !== '' ) : ?>
							<a class="krv-btn krv-btn-primary" href="<?php echo esc_url( $contacts_cta ); ?>"><?php echo esc_html( (string) $settings['cta_label'] ); ?></a>
						<?php endif; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( trim( (string) ( $settings['disclaimer'] ?? '' ) ) !== '' ) : ?>
				<div class="krv-small"><?php echo esc_html( $settings['disclaimer'] ); ?></div>
			<?php endif; ?>

			<div class="krv-mobile-cta" aria-label="Быстрые действия">
				<a class="krv-btn krv-btn-primary" href="<?php echo esc_url( $contacts_diagnostic ); ?>">Диагностика</a>
				<a class="krv-btn" href="<?php echo esc_url( $telegram_url ); ?>" target="_blank" rel="noopener noreferrer">Telegram</a>
				<a class="krv-btn krv-btn-max" href="<?php echo esc_url( $max_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo $max_icon; ?><span>MAX</span></a>
			</div>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

add_shortcode( 'krv_price_list', 'krv_price_list_render' );
