<?php

// Защита от прямого доступа к файлу
if ( ! defined('ABSPATH') ) {
	exit;
}

if ( ! class_exists('WPSEO_Options') ) {
	return;
}

// Удаляем комментарий "This site is optimized with the Yoast SEO plugin"
add_filter( 'wpseo_debug_markers', '__return_false' );

// Сортировка мета-бокса "Yoast SEO" с меньшим приоритетом по-умолчанию
function prefix_yoast_meta_order(){
	return 'low';
}
add_filter( 'wpseo_metabox_prio', 'prefix_yoast_meta_order' );

// Удалить 'application/ld+json'
function theme_yoast_remove_jsonld($data){
	return false;
}
// add_filter( 'wpseo_json_ld_output', 'theme_yoast_remove_jsonld', 10, 1 );

// Yoast SEO Breadcrumbs
if ( function_exists('yoast_breadcrumb') ) {
	// Вывод хлебных крошек
	function theme_custom_wpseo_breadcrumb_output( $output ) {
		if ( is_home() || is_front_page() ) return false;
		
		$before = '<div class="container">';
		$after = '</div>';
		
		return $before . $output . $after;
	}
	add_filter( 'wpseo_breadcrumb_output', 'theme_custom_wpseo_breadcrumb_output' );
	
	// Класс хлебных крошек
	function add_breadcrumb_class($class) {
		$class .= ' breadcrubms';
		return trim($class);
	}
	add_filter( 'wpseo_breadcrumb_output_class', 'add_breadcrumb_class' );
	
	// Меняем обертку элемента
	function theme_custom_wpseo_breadcrumb_single_link( $link_output, $link ) {
		$output = '';

		if ( strpos($link_output, 'breadcrumb_last') === false ) {
			if ( isset($link['url']) && $link['url'] ) {
				$output .= '<a href="' . esc_url($link['url']) . '">' . esc_html($link['text']) . '</a>';
			} else {
				$output .= '<span>' . esc_html( $link['text'] ) . '</span>';
			}
		} else {
			if ( isset( $opt['breadcrumbs-boldlast'] ) && $opt['breadcrumbs-boldlast'] ) {
				$output .= '<span><strong>' . esc_html( $link['text'] ) . '</strong></span>';
			} else {
				$output .= '<span>' . esc_html( $link['text'] ) . '</span>';
			}
		}

		if ( ! empty($output) ) return $output;
		return $link_output;
	}
	add_filter( 'wpseo_breadcrumb_single_link', 'theme_custom_wpseo_breadcrumb_single_link', 10, 2 );

	// Меняем ссылки в хлебных крошках
	function custom_breadcrumbs_modify_link_3872($links) {
		$new_title = yoast_title_mod_for_games();
		if ( $new_title ) {
			// Последний элемент массива — текущая запись
			$lastIndex = count($links) - 1;

			if ( isset($links[ $lastIndex ]['text']) ) {
				$links[ $lastIndex ]['text'] = $new_title;
			}
		}
		
		// Команды
		if ( is_singular('teams') ) {
			// Предпоследний элемент массива — архивная страница
			$lastIndex = count($links) - 2;

			if ( isset($links[ $lastIndex ]['text']) ) {
				$links[ $lastIndex ]['text'] = 'Поиск игрока в команду';
			}
		}

		if ( is_singular('players') ) {
			$lastIndex = count($links) - 2;

			if ( isset($links[ $lastIndex ]['text']) ) {
				$links[ $lastIndex ]['text'] = 'Игроки ищут команду или игру';
			}
		}

		return $links;
	}
	add_filter( 'wpseo_breadcrumb_links', 'custom_breadcrumbs_modify_link_3872' );
}

// Модифицируем title
function theme_yoast_modify_title($title) { 
	$new_title = yoast_title_mod_for_games();
	if ( $new_title ) $title = $new_title;

	return $title;
}
add_filter('wpseo_title', 'theme_yoast_modify_title', 1);

function yoast_title_mod_for_games() {
	$title = null;
	
	if ( is_singular('games') ) {
		$title = 'Игра';
		
		$get_meta = carbon_get_the_post_meta('game_date');
		$date_format = 'd F';
		$date = get_the_date($date_format);
		if ( $get_meta ) {
			$title .= ' ' . wp_date($date_format, $get_meta);
		}
	}

	return $title;
}
