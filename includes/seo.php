<?php
/**
 * SEO: The SEO Framework integration (titles, descriptions, robots)
 * and WP core sitemap disable.
 * Extracted from legacy-arkai-child-functions.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** =========================
 *  3) TSF title/description without duplicates
 *  ========================= */
if ( function_exists( 'tsf' ) ) {

	function krv_tsf_clean_text( $text ): string {
		$text = (string) $text;
		$text = strip_shortcodes( $text );
		$text = wp_strip_all_tags( $text );
		$text = preg_replace( '~https?://\S+~u', '', $text );
		$text = preg_replace( '/\s+/u', ' ', $text );
		return trim( $text );
	}

	function krv_tsf_trim( $text, $max = 155 ): string {
		$text = trim( preg_replace( '/\s+/u', ' ', (string) $text ) );
		if ( $text === '' ) {
			return '';
		}

		if ( mb_strlen( $text, 'UTF-8' ) <= $max ) {
			return rtrim( $text );
		}

		$cut = mb_substr( $text, 0, $max, 'UTF-8' );
		$cut = preg_replace( '/\s+\S*$/u', '', $cut );

		return rtrim( $cut, " \t\n\r\0\x0B.,;:!-" ) . '…';
	}

	function krv_build_meta_title(): string {
		$paged        = (int) get_query_var( 'paged' );
		$paged_suffix = $paged > 1 ? ' — страница ' . $paged : '';

		if ( is_front_page() ) {
			return 'IT Решения — ИП Кривошеин Алексей Сергеевич' . $paged_suffix;
		}

		if ( is_home() ) {
			return 'Блог — krivoshein.site' . $paged_suffix;
		}

		if ( is_singular() ) {
			$id = get_queried_object_id();
			$pt = get_post_type( $id );

			if ( $pt === 'price' ) {
				$price = trim( (string) get_post_meta( $id, 'price_value', true ) );
				$t     = 'Цены: ' . get_the_title( $id );

				if ( $price !== '' ) {
					$t .= ' — ' . $price . ' ₽';
				}

				return $t . $paged_suffix;
			}

			if ( $pt === 'usluga' ) {
				return 'Услуга: ' . get_the_title( $id ) . $paged_suffix;
			}

			if ( $pt === 'project' ) {
				return 'Проект: ' . get_the_title( $id ) . $paged_suffix;
			}

			if ( $pt === 'client' ) {
				return 'Клиент: ' . get_the_title( $id ) . $paged_suffix;
			}

			if ( $pt === 'partner' ) {
				return 'Партнёр: ' . get_the_title( $id ) . $paged_suffix;
			}

			return get_the_title( $id ) . $paged_suffix;
		}

		if ( is_category() ) {
			$term = get_queried_object();
			return 'Категория: ' . ( $term->name ?? '' ) . $paged_suffix;
		}

		if ( is_tag() ) {
			$term = get_queried_object();
			return 'Тег: ' . ( $term->name ?? '' ) . $paged_suffix;
		}

		if ( is_tax() ) {
			$term      = get_queried_object();
			$tax       = $term->taxonomy ?? '';
			$tax_obj   = $tax ? get_taxonomy( $tax ) : null;
			$tax_label = ( $tax_obj && ! empty( $tax_obj->labels->singular_name ) )
				? $tax_obj->labels->singular_name
				: 'Раздел';

			return $tax_label . ': ' . ( $term->name ?? '' ) . $paged_suffix;
		}

		if ( is_post_type_archive() ) {
			$pt    = get_query_var( 'post_type' );
			$pt    = is_array( $pt ) ? reset( $pt ) : $pt;
			$obj   = $pt ? get_post_type_object( $pt ) : null;
			$label = $obj ? $obj->labels->name : 'Архив';

			return $label . $paged_suffix;
		}

		if ( is_author() ) {
			$u    = get_queried_object();
			$name = $u->display_name ?? '';
			return 'Статьи автора: ' . $name . $paged_suffix;
		}

		if ( is_search() ) {
			return 'Поиск: ' . get_search_query() . $paged_suffix;
		}

		if ( is_404() ) {
			return 'Страница не найдена' . $paged_suffix;
		}

		return '';
	}

	function krv_build_meta_description(): string {
		$paged        = (int) get_query_var( 'paged' );
		$paged_suffix = $paged > 1 ? ' Страница ' . $paged . '.' : '';

		if ( is_front_page() ) {
			return trim( 'Разработка и продвижение сайтов, WordPress, Linux, DevOps, настройка серверов и практические разборы.' . $paged_suffix );
		}

		if ( is_home() ) {
			return trim( 'Блог о Linux, WordPress, DevOps, серверах, хостинге, безопасности и веб-разработке.' . $paged_suffix );
		}

		if ( is_search() ) {
			$q = trim( (string) get_search_query() );
			return trim(
				( $q !== ''
					? 'Результаты поиска по запросу «' . $q . '» на сайте krivoshein.site.'
					: 'Поиск по материалам сайта krivoshein.site.'
				) . $paged_suffix
			);
		}

		if ( is_404() ) {
			return trim( 'Страница не найдена. Возможно, материал был удалён, перенесён или ссылка устарела.' . $paged_suffix );
		}

		if ( is_singular() ) {
			$id   = get_queried_object_id();
			$post = get_post( $id );

			if ( ! $post ) {
				return '';
			}

			$ex = krv_tsf_clean_text( get_the_excerpt( $post ) );
			$pt = get_post_type( $id );

			if ( $ex === '' && $pt === 'usluga' ) {
				$ex = krv_tsf_clean_text( get_post_meta( $id, 'service_description', true ) );
			}

			if ( $ex === '' && $pt === 'price' ) {
				$pd = krv_tsf_clean_text( get_post_meta( $id, 'price_description', true ) );
				$pv = trim( (string) get_post_meta( $id, 'price_value', true ) );
				$ex = ( $pv !== '' ? 'Стоимость: ' . $pv . ' ₽. ' : '' ) . $pd;
				$ex = krv_tsf_clean_text( $ex );
			}

			if ( $ex === '' && $pt === 'partner' ) {
				$ex = krv_tsf_clean_text( get_post_meta( $id, 'partner_description', true ) );
			}

			if ( $ex === '' ) {
				$ex = krv_tsf_clean_text( $post->post_content );
			}

			return trim( krv_tsf_trim( $ex, 155 ) . $paged_suffix );
		}

		if ( is_category() || is_tag() || is_tax() ) {
			$term   = get_queried_object();
			$name   = $term->name ?? '';
			$t_desc = isset( $term->description ) ? krv_tsf_clean_text( $term->description ) : '';

			if ( $t_desc !== '' ) {
				return trim( krv_tsf_trim( $t_desc, 155 ) . $paged_suffix );
			}

			if ( is_category() ) {
				return trim( 'Категория «' . $name . '» — статьи, инструкции, конфиги и разборы ошибок.' . $paged_suffix );
			}

			if ( is_tag() ) {
				return trim( 'Тег «' . $name . '» — подборка материалов по теме.' . $paged_suffix );
			}

			$tax       = $term->taxonomy ?? '';
			$tax_obj   = $tax ? get_taxonomy( $tax ) : null;
			$tax_label = ( $tax_obj && ! empty( $tax_obj->labels->singular_name ) )
				? $tax_obj->labels->singular_name
				: 'Раздел';

			return trim( $tax_label . ' «' . $name . '» — подборка материалов и заметок.' . $paged_suffix );
		}

		if ( is_post_type_archive() ) {
			$pt    = get_query_var( 'post_type' );
			$pt    = is_array( $pt ) ? reset( $pt ) : $pt;
			$obj   = $pt ? get_post_type_object( $pt ) : null;
			$label = $obj ? $obj->labels->name : 'Архив';

			return trim( $label . ': подборка материалов и заметок на сайте.' . $paged_suffix );
		}

		if ( is_author() ) {
			$u    = get_queried_object();
			$name = $u->display_name ?? '';
			return trim( 'Публикации автора ' . $name . ': Linux, WordPress, DevOps и практические разборы.' . $paged_suffix );
		}

		return '';
	}

	add_filter( 'the_seo_framework_title_from_generation', function( $title, $args ) {
		if ( null !== $args ) {
			return $title;
		}

		$new = krv_build_meta_title();
		return $new !== '' ? $new : $title;
	}, 10, 2 );

	add_filter( 'the_seo_framework_title_from_custom_field', function( $title, $args ) {
		if ( null !== $args ) {
			return $title;
		}

		$new = krv_build_meta_title();
		return $new !== '' ? $new : $title;
	}, 10, 2 );

	add_filter( 'the_seo_framework_generated_description', function( $description, $args, $type = null ) {
		if ( null !== $args ) {
			return $description;
		}

		$new = krv_build_meta_description();
		return $new !== '' ? $new : $description;
	}, 10, 3 );

	add_filter( 'the_seo_framework_custom_field_description', function( $description, $args ) {
		if ( null !== $args ) {
			return $description;
		}

		$new = krv_build_meta_description();
		return $new !== '' ? $new : $description;
	}, 10, 2 );

	add_filter( 'the_seo_framework_robots_meta_array', function( $meta, $args, $options = null ) {
		$taxonomy = null === $args ? tsf()->query()->get_current_taxonomy() : ( $args['taxonomy'] ?? null );

		if ( 'post_tag' === $taxonomy ) {
			$meta['noindex']  = 'noindex';
			$meta['nofollow'] = 'nofollow';
		}

		if ( null === $args && is_paged() && ! is_singular() ) {
			$meta['noindex'] = 'noindex';
		}

		return $meta;
	}, 10, 3 );
}

/** Disable WP core sitemap */
add_filter( 'wp_sitemaps_enabled', '__return_false' );
