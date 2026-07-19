<?php
/**
 * Universal service page shell [krv_service_page] with ACF options page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** ACF options page slug. */
function krv_service_page_option_id(): string {
	return 'krv-service-pages';
}

/**
 * Supported DNS record type descriptions (page 7304).
 *
 * @return array<string, string>
 */
function krv_service_page_dns_record_types(): array {
	return array(
		'A'     => 'IPv4 адрес записи, указывающий на IP-адрес сервера.',
		'MX'    => 'Почтовый обменник для домена, указывающий на сервер для приема электронной почты.',
		'CNAME' => 'Каноническое имя, указывающее на другой домен.',
		'TXT'   => 'Текстовая запись, содержащая полезную информацию, например SPF.',
		'NS'    => 'Серверы имён, отвечающие за зону данного домена.',
		'AAAA'  => 'IPv6 адрес записи, указывающий на IP-адрес сервера.',
	);
}

/**
 * Hardcoded ACF seed rows extracted from legacy page content.
 *
 * @return array<int, array<string, mixed>>
 */
function krv_service_page_get_seed_rows(): array {
	return array(
		array(
			'page_id'               => 6204,
			'page_heading'          => 'Добро пожаловать на страницу «Информация о домене»!',
			'intro_paragraphs'      => array(
				array( 'text' => 'Здесь вы можете получить все необходимые данные о вашем сайте или потенциальном домене всего за несколько секунд.' ),
				array( 'text' => 'Наша проверка WhoIs — это удобный инструмент, который поможет вам узнать регистрационную информацию о любом сайте.' ),
				array( 'text' => 'С помощью проверки WhoIs вы получите подробную информацию о владельце домена, дате регистрации и истечении срока её действия, контактной информации и других важных данных.' ),
				array( 'text' => 'Эта информация может быть полезной для принятия решений при выборе доменного имени или проведении маркетинговых исследований.' ),
				array( 'text' => 'Не упустите возможность воспользоваться нашим надёжным сервисом проверки WhoIs уже сегодня!' ),
			),
			'quote_text'            => '',
			'show_dns_record_types' => 0,
			'show_yandex_rsya'      => 1,
			'custom_html_after'     => '',
		),
		array(
			'page_id'               => 7287,
			'page_heading'          => '',
			'intro_paragraphs'      => array(
				array( 'text' => 'Добро пожаловать на страницу конвертации доменов в формат Punycode! Здесь вы можете легко и быстро преобразовать доменные имена с нестандартными символами, такими как кириллица, в формат, совместимый с системой доменных имен (DNS).' ),
			),
			'quote_text'            => 'Введите доменное имя в поле ниже и нажмите кнопку "Конвертировать". Сервис моментально выполнит преобразование и покажет вам результат в виде Punycode, который можно использовать в интернете.',
			'show_dns_record_types' => 0,
			'show_yandex_rsya'      => 1,
			'custom_html_after'     => '',
		),
		array(
			'page_id'               => 7304,
			'page_heading'          => '',
			'intro_paragraphs'      => array(
				array( 'text' => 'Добро пожаловать на страницу Easy DNS Lookup! Здесь вы можете легко и быстро получить информацию о DNS-записях для любого доменного имени.' ),
				array( 'text' => 'Используйте форму ниже, чтобы начать поиск и получить полные данные о вашем домене.' ),
			),
			'quote_text'            => 'Для того чтобы выполнить поиск, введите доменное имя в поле ниже, выберите тип DNS-записи (или выберите "Все записи" для получения всей доступной информации), и нажмите кнопку "Поиск DNS-записей".',
			'show_dns_record_types' => 1,
			'show_yandex_rsya'      => 1,
			'custom_html_after'     => '',
		),
		array(
			'page_id'               => 7323,
			'page_heading'          => '',
			'intro_paragraphs'      => array(
				array( 'text' => 'Проверьте информацию о домене или IP-адресе' ),
				array( 'text' => 'Введите IP-адрес или доменное имя, чтобы получить детальную информацию о нем, включая страну, город, интернет-провайдера и другие параметры. Это удобный инструмент для быстрой проверки данных о владельце IP-адреса или домена.' ),
			),
			'quote_text'            => 'Этот сервис может быть полезен для системных администраторов, специалистов по безопасности или просто пользователей, желающих узнать больше о доменах и IP-адресах в сети.',
			'show_dns_record_types' => 0,
			'show_yandex_rsya'      => 1,
			'custom_html_after'     => '',
		),
		array(
			'page_id'               => 7352,
			'page_heading'          => '',
			'intro_paragraphs'      => array(
				array( 'text' => 'Мой онлайн-генератор crontab создан для системных администраторов и разработчиков, которым необходимо быстро и удобно составить расписания для планировщика задач. С помощью этого сервиса вы можете сгенерировать crontab-запись всего за несколько шагов:' ),
				array( 'text' => '1. Введите значения для минут, часов, дней и других параметров в соответствующие поля. Все параметры поддерживают как точные значения, так и символы (например, * для любой минуты).' ),
				array( 'text' => '2. Нажмите кнопку "Сгенерировать Crontab", чтобы получить готовую строку расписания.' ),
				array( 'text' => '3. Копируйте результат и используйте его в вашем серверном crontab для автоматизации задач.' ),
			),
			'quote_text'            => 'Генератор также поддерживает динамическую проверку данных, чтобы исключить ошибки при вводе. Если нужно начать заново, вы можете быстро очистить все поля с помощью кнопки "Сбросить".',
			'show_dns_record_types' => 0,
			'show_yandex_rsya'      => 1,
			'custom_html_after'     => '',
		),
		array(
			'page_id'               => 7369,
			'page_heading'          => '',
			'intro_paragraphs'      => array(
				array( 'text' => 'Конфигуратор файрвола — это удобный инструмент, который позволяет сгенерировать необходимые правила для настройки вашего файрвола без лишних усилий.' ),
				array( 'text' => 'С его помощью вы можете создать правила для UFW, IPTables и NFTables, просто выбрав нужные опции и указав необходимые параметры.' ),
			),
			'quote_text'            => 'Вы можете выбрать стандартные порты для популярных сервисов или указать кастомные порты по своему усмотрению. Кроме того, имеется возможность установить лимит соединений, что поможет защитить ваш сервер от избыточной нагрузки и потенциальных атак. После настройки параметров достаточно нажать кнопку «Сгенерировать правила», и вы получите готовые команды для применения в вашей системе.',
			'show_dns_record_types' => 0,
			'show_yandex_rsya'      => 1,
			'custom_html_after'     => '',
		),
		array(
			'page_id'               => 7529,
			'page_heading'          => '',
			'intro_paragraphs'      => array(
				array( 'text' => 'Тестирование скорости интернета — важная часть проверки качества соединения для любого пользователя. Наша услуга по измерению скорости позволяет в реальном времени узнать точные параметры загрузки и выгрузки данных, что помогает оценить качество интернет-провайдера. Тест на нашем сайте прост и понятен в использовании: достаточно подтвердить, что вы не робот, и нажать на кнопку для запуска теста' ),
			),
			'quote_text'            => '',
			'show_dns_record_types' => 0,
			'show_yandex_rsya'      => 1,
			'custom_html_after'     => '<p>Мы предлагаем не только измерение скорости загрузки и выгрузки, но и возможность проверить стабильность соединения. Это особенно важно для тех, кто занимается стримингом или использует облачные сервисы. Надежность и точность наших измерений помогут вам лучше понять, насколько ваш интернет соответствует ожиданиям и требованиям.</p>',
		),
	);
}

/**
 * Register ACF options page and local field group.
 */
function krv_service_page_register_acf(): void {
	if ( ! function_exists( 'acf_add_options_page' ) || ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_options_page(
		krv_acf_options_page_args(
			krv_service_page_option_id(),
			array(
				'page_title' => 'Сервисные страницы',
				'menu_title' => 'Сервисные страницы',
				'capability' => 'edit_theme_options',
				'redirect'   => false,
				'position'   => 63,
				'icon_url'   => 'dashicons-admin-tools',
			)
		)
	);

	acf_add_local_field_group(
		array(
			'key'    => 'group_krv_service_pages',
			'title'  => 'Сервисные страницы',
			'fields' => array(
				array(
					'key'          => 'field_krv_sp_service_pages',
					'label'        => 'Страницы инструментов',
					'name'         => 'service_pages',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Добавить страницу',
					'sub_fields'   => array(
						array(
							'key'           => 'field_krv_sp_page_id',
							'label'         => 'ID страницы',
							'name'          => 'page_id',
							'type'          => 'number',
							'required'      => 1,
							'min'           => 1,
							'step'          => 1,
							'default_value' => '',
						),
						array(
							'key'   => 'field_krv_sp_page_heading',
							'label' => 'Заголовок (необязательно)',
							'name'  => 'page_heading',
							'type'  => 'text',
						),
						array(
							'key'          => 'field_krv_sp_intro_paragraphs',
							'label'        => 'Вводные абзацы',
							'name'         => 'intro_paragraphs',
							'type'         => 'repeater',
							'layout'       => 'table',
							'button_label' => 'Добавить абзац',
							'sub_fields'   => array(
								array(
									'key'   => 'field_krv_sp_intro_text',
									'label' => 'Текст',
									'name'  => 'text',
									'type'  => 'textarea',
									'rows'  => 2,
								),
							),
						),
						array(
							'key'   => 'field_krv_sp_quote_text',
							'label' => 'Цитата (необязательно)',
							'name'  => 'quote_text',
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'           => 'field_krv_sp_show_dns_record_types',
							'label'         => 'Показывать типы DNS-записей',
							'name'          => 'show_dns_record_types',
							'type'          => 'true_false',
							'ui'            => 1,
							'default_value' => 0,
						),
						array(
							'key'           => 'field_krv_sp_show_yandex_rsya',
							'label'         => 'Показывать блок Яндекс РСЯ',
							'name'          => 'show_yandex_rsya',
							'type'          => 'true_false',
							'ui'            => 1,
							'default_value' => 1,
						),
						array(
							'key'   => 'field_krv_sp_custom_html_after',
							'label' => 'HTML после инструмента (необязательно)',
							'name'  => 'custom_html_after',
							'type'  => 'textarea',
							'rows'  => 4,
						),
					),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => krv_service_page_option_id(),
					),
				),
			),
		)
	);
}

/**
 * One-time seed of ACF options from extracted page intros.
 */
function krv_service_page_seed_defaults(): void {
	if ( get_option( 'krv_service_pages_seeded_v1' ) ) {
		return;
	}

	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	$rows = krv_service_page_get_seed_rows();

	update_field( 'service_pages', $rows, krv_service_page_option_id() );
	update_option( 'krv_service_pages_seeded_v1', DRSLON_SITE_CORE_VERSION, false );
}

add_action(
	'acf/init',
	function () {
		krv_service_page_register_acf();
		krv_service_page_seed_defaults();
	},
	20
);

/**
 * Find ACF repeater row for a page ID.
 *
 * @return array<string, mixed>
 */
function krv_service_page_get_acf_row( int $page_id ): array {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$rows = get_field( 'service_pages', krv_service_page_option_id() );

	if ( ! is_array( $rows ) ) {
		return array();
	}

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		if ( (int) ( $row['page_id'] ?? 0 ) === $page_id ) {
			return $row;
		}
	}

	return array();
}

/**
 * Fallback seed row for a page when ACF has no stored row yet.
 *
 * @return array<string, mixed>
 */
function krv_service_page_get_seed_row_for_page( int $page_id ): array {
	foreach ( krv_service_page_get_seed_rows() as $row ) {
		if ( (int) ( $row['page_id'] ?? 0 ) === $page_id ) {
			return $row;
		}
	}

	return array();
}

/**
 * Build merged view model from registry + ACF/fallback seed.
 *
 * @return array<string, mixed>|null
 */
function krv_service_page_get_render_data( ?int $page_id = null ): ?array {
	$config = krv_service_page_get_config( $page_id );

	if ( ! is_array( $config ) ) {
		return null;
	}

	$page_id  = (int) $config['page_id'];
	$acf_row  = krv_service_page_get_acf_row( $page_id );
	$seed_row = krv_service_page_get_seed_row_for_page( $page_id );
	$source   = ! empty( $acf_row ) ? $acf_row : $seed_row;

	$shell = (string) ( $config['shell'] ?? 'tool' );

	$show_dns = ! empty( $config['show_dns_types'] );
	if ( array_key_exists( 'show_dns_record_types', $source ) ) {
		$show_dns = (bool) $source['show_dns_record_types'];
	}

	$show_rsya = $shell === 'tool';
	if ( array_key_exists( 'show_yandex_rsya', $source ) ) {
		$show_rsya = (bool) $source['show_yandex_rsya'];
	}

	$heading = trim( (string) ( $source['page_heading'] ?? '' ) );
	if ( $heading === '' ) {
		$heading = get_the_title( $page_id );
	}

	$intro_paragraphs = array();
	if ( ! empty( $source['intro_paragraphs'] ) && is_array( $source['intro_paragraphs'] ) ) {
		foreach ( $source['intro_paragraphs'] as $paragraph ) {
			if ( ! is_array( $paragraph ) ) {
				continue;
			}

			$text = trim( (string) ( $paragraph['text'] ?? '' ) );
			if ( $text !== '' ) {
				$intro_paragraphs[] = $text;
			}
		}
	}

	return array(
		'page_id'          => $page_id,
		'shell'            => $shell,
		'shortcode'        => (string) ( $config['shortcode'] ?? '' ),
		'atts'             => (string) ( $config['atts'] ?? '' ),
		'heading'          => $heading,
		'intro_paragraphs' => $intro_paragraphs,
		'quote_text'       => trim( (string) ( $source['quote_text'] ?? '' ) ),
		'show_dns_types'   => $show_dns,
		'show_yandex_rsya' => $show_rsya,
		'custom_html_after'=> (string) ( $source['custom_html_after'] ?? '' ),
	);
}

/**
 * Build inner tool shortcode tag.
 */
function krv_service_page_build_inner_shortcode( array $data ): string {
	$tag  = trim( (string) ( $data['shortcode'] ?? '' ) );
	$atts = trim( (string) ( $data['atts'] ?? '' ) );

	if ( $tag === '' ) {
		return '';
	}

	return $atts !== '' ? sprintf( '[%s %s]', $tag, $atts ) : sprintf( '[%s]', $tag );
}

/**
 * Render Yandex RSYA recommendation block markup.
 */
function krv_service_page_render_rsya_block(): void {
	if ( ! function_exists( 'krv_rsya_reco_enabled' ) || ! krv_rsya_reco_enabled() ) {
		return;
	}

	$render_to = krv_rsya_reco_render_to();
	$block_id  = krv_rsya_reco_block_id();
	$reco_code = krv_rsya_reco_code();
	?>
	<div class="krv-service-page__rsya">
		<?php if ( $reco_code !== '' ) : ?>
			<?php echo $reco_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- raw Yandex RTB code from manage_options. ?>
		<?php else : ?>
			<div id="<?php echo esc_attr( $render_to ); ?>" class="krv-service-page__rsya-slot"></div>
			<script>
			(function () {
				var renderTo = <?php echo wp_json_encode( $render_to ); ?>;
				var blockId  = <?php echo wp_json_encode( $block_id ); ?>;
				var el = document.getElementById(renderTo);
				if (!el) return;

				function hasFill() {
					return !!el.querySelector('iframe');
				}

				function renderOnce() {
					if (el.dataset.krvRecoInit === '1') return;
					el.dataset.krvRecoInit = '1';

					window.yaContextCb = window.yaContextCb || [];
					window.yaContextCb.push(function () {
						try {
							if (!window.Ya || !Ya.Context || !Ya.Context.AdvManager) return;
							if (hasFill()) return;

							Ya.Context.AdvManager.renderWidget({
								renderTo: renderTo,
								blockId: blockId
							});
						} catch (e) {}
					});
				}

				renderOnce();

				setTimeout(function () {
					if (hasFill()) return;
					el.innerHTML = '';
					el.dataset.krvRecoInit = '0';
					renderOnce();
				}, 9000);
			})();
			</script>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Shortcode renderer.
 *
 * @param array<string, string> $atts Shortcode attributes.
 */
function krv_service_page_render( $atts = array() ): string {
	$atts = shortcode_atts(
		array(
			'page_id' => '',
		),
		$atts,
		'krv_service_page'
	);

	$page_id = $atts['page_id'] !== '' ? (int) $atts['page_id'] : null;
	$data    = krv_service_page_get_render_data( $page_id );

	if ( ! is_array( $data ) ) {
		return '';
	}

	$inner_shortcode = krv_service_page_build_inner_shortcode( $data );

	if ( $inner_shortcode === '' ) {
		return '';
	}

	$shell_class = $data['shell'] === 'minimal' ? 'krv-service-page--minimal' : 'krv-service-page--tool';

	ob_start();
	?>
	<div class="krv-service-page <?php echo esc_attr( $shell_class ); ?>" data-page-id="<?php echo esc_attr( (string) $data['page_id'] ); ?>">
		<?php if ( $data['heading'] !== '' ) : ?>
			<h2 class="krv-service-page__heading"><?php echo esc_html( $data['heading'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $data['intro_paragraphs'] ) ) : ?>
			<div class="krv-service-page__intro">
				<?php foreach ( $data['intro_paragraphs'] as $paragraph ) : ?>
					<p><?php echo esc_html( $paragraph ); ?></p>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $data['quote_text'] !== '' && $data['shell'] === 'tool' ) : ?>
			<blockquote class="krv-service-page__quote">
				<p><?php echo esc_html( $data['quote_text'] ); ?></p>
			</blockquote>
		<?php endif; ?>

		<div class="krv-service-page__tool">
			<?php echo do_shortcode( $inner_shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>

		<?php if ( $data['show_dns_types'] ) : ?>
			<div class="krv-service-page__dns-types">
				<p class="krv-service-page__dns-types-title">Поддерживаемые типы записей:</p>
				<ul>
					<?php foreach ( krv_service_page_dns_record_types() as $type => $description ) : ?>
						<li><strong><?php echo esc_html( $type ); ?></strong>: <?php echo esc_html( $description ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( $data['custom_html_after'] !== '' ) : ?>
			<div class="krv-service-page__after">
				<?php echo wp_kses_post( $data['custom_html_after'] ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $data['show_yandex_rsya'] ) : ?>
			<?php krv_service_page_render_rsya_block(); ?>
		<?php endif; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}

add_shortcode( 'krv_service_page', 'krv_service_page_render' );

/**
 * Load Yandex context script on service pages that show RSYA.
 */
add_action(
	'wp_head',
	function () {
		if ( is_admin() || ! is_singular() ) {
			return;
		}

		if ( ! function_exists( 'krv_rsya_reco_enabled' ) || ! krv_rsya_reco_enabled() ) {
			return;
		}

		$data = krv_service_page_get_render_data();

		if ( ! is_array( $data ) || empty( $data['show_yandex_rsya'] ) ) {
			return;
		}

		static $done = false;
		if ( $done ) {
			return;
		}
		$done = true;

		echo "<script>window.yaContextCb=window.yaContextCb||[];</script>\n";
		echo "<script async src=\"https://yandex.ru/ads/system/context.js\"></script>\n";
	},
	30
);
