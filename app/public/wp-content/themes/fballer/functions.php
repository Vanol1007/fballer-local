<?php

// Защита от прямого доступа к файлу
if ( ! defined('ABSPATH') ) {
	exit;
}

// Дефолтные настройки Wordpress
function theme_after_activation_hook($oldname, $oldtheme = false) {
	update_option('blogdescription', '');
	update_option('rss_use_excerpt', 1);
	update_option('default_comment_status', '');
	update_option('medium_size_w', 0);
	update_option('medium_size_h', 0);
	update_option('large_size_w', 0);
	update_option('large_size_h', 0);
	update_option('uploads_use_yearmonth_folders', 0);
	update_option('permalink_structure', '/%category%/%postname%/');
	
	flush_rewrite_rules();
}
add_action( "after_switch_theme", "theme_after_activation_hook", 10, 2 );

// "Библиотеки"
include_once 'inc/admin.php';
include_once 'inc/breadcrumbs.php'; // Хлебные крошки
include_once 'inc/add-forms.php';
include_once 'inc/carbonfields.php';
include_once 'inc/customizer.php';
if ( file_exists(get_template_directory() . '/inc/importers.php') ) {
	include_once 'inc/importers.php';
}
include_once 'inc/menu.php';
include_once 'inc/optimization.php'; // Костыли для очистки Wordpress
include_once 'inc/post-types.php';
include_once 'inc/posts.php'; // AJAX и прочее для записей
include_once 'inc/yoast.php';

// Поддержка миниатюр
add_theme_support('post-thumbnails');

// Обрезка текста
function theme_content_resize($text, $size) {
	if ( ! $text ) return '';
	if ( ! $size ) $size = 100;
	
	$content_striped = wp_strip_all_tags($text, 0);
	$content_striped = rtrim($content_striped, "!,.-");
	$content_striped = preg_replace("/&#?[a-z0-9]+;/i", "", $content_striped); // Удаляем спецсимволы
	$content_length = mb_strlen($content_striped);
	$content = mb_substr($content_striped, 0, $size);
	// $content = mb_substr($content, 0, mb_strrpos($content, ' '));
	if ( $content_length > $size ) {
		return $content . '...';
	} else {
		return $content_striped;
	}
}

// Подключение сайдбара
if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'name' => 'Боковая панель',
		'id' => 'sidebar',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	));
}

// Сжатие inline CSS
function theme_css_minify($css) {
	if ( ! $css ) return false;
	
	$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
	$css = str_replace( array("\r\n", "\r", "\n", "\t", "  ", "   ", "    "), '', $css );
	$css = str_replace( '{ ', '{', $css );
	$css = str_replace( ' {', '{', $css );
	$css = str_replace( ' }', '}', $css );
	$css = str_replace( '; ', ';' , $css );
	
	return $css;
}

// Подключаем CSS и JS
function theme_default_scripts() {
	// Заменяем jQuery
	//wp_deregister_script( 'jquery' );
	//wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js' );
	//wp_enqueue_script( 'jquery' );
	
	wp_enqueue_style( 'google-fonts-montserrat', '//fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap' );
	wp_enqueue_style( 'google-fonts-rubik', '//fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap' );
	
	wp_enqueue_style( 'theme-app', get_template_directory_uri() . '/assets/css/app.css', '', filemtime(get_template_directory() . '/assets/css/app.css') );
	
	// Fancybox
	wp_enqueue_style( 'fancyboxCss', get_template_directory_uri() . '/assets/css/fancybox.css', '', filemtime(get_template_directory() . '/assets/css/fancybox.css') );
	wp_enqueue_script( 'fancyboxJS', get_template_directory_uri() . '/assets/js/fancybox.umd.js', array(), filemtime(get_template_directory() . '/assets/js/fancybox.umd.js'), true );
	
	// Splide
	wp_enqueue_style( 'splide-core', get_template_directory_uri() . '/assets/css/splide-core.min.css', '', filemtime(get_template_directory() . '/assets/css/splide-core.min.css') );
	wp_enqueue_style( 'splide', get_template_directory_uri() . '/assets/css/splide.min.css', '', filemtime(get_template_directory() . '/assets/css/splide.min.css') );
	wp_enqueue_script( 'splide', get_template_directory_uri() . '/assets/js/splide.min.js', array(), filemtime(get_template_directory() . '/assets/js/splide.min.js'), true );
	
	wp_enqueue_script( 'theme-main', get_template_directory_uri() . '/assets/js/main.js', array(), filemtime(get_template_directory() . '/assets/js/main.js'), true );
	wp_localize_script('theme-main', 'fballerMainData', array(
		'defaultCityId' => (string) fballer_get_default_city_id(),
	));

	if ( is_single() && is_singular('post') ) {
		wp_enqueue_script( 'theme-single', get_template_directory_uri() . '/assets/js/single.js', array(), filemtime(get_template_directory() . '/assets/js/single.js'), true );
	}
	
	if (
		is_category() ||
		is_page_template('templates/template-games.php') ||
		is_page_template('templates/template-champs.php') ||
		is_page_template('templates/template-places.php') ||
		is_page_template('templates/template-teams.php') ||
		is_page_template('templates/template-players.php') ||
		is_page_template('templates/template-add-player.php') ||
		is_home() || is_front_page() ||
		is_singular('games') || is_singular('teams') || is_singular('players') ||
		is_post_type_archive('places') ||
		is_post_type_archive('teams') ||
		is_post_type_archive('champs') ||
		is_post_type_archive('players')
	) {
		wp_enqueue_script( 'ajax-posts', get_template_directory_uri() . '/assets/js/ajax-posts.js', array('jquery'), filemtime(get_template_directory() . '/assets/js/ajax-posts.js'), true );

		wp_localize_script('ajax-posts', 'ajaxPostsData', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			// 'query_vars' => json_encode($GLOBALS['wp_query']->query),
			'query_vars' => json_encode($GLOBALS['wp_query']->query_vars),
			'current_page' => max( 1, get_query_var('paged') ),
			'max_pages' => $GLOBALS['wp_query']->max_num_pages,
			// 'posts_per_page' => get_option('posts_per_page'),
		));
	}
}
add_action( 'wp_enqueue_scripts', 'theme_default_scripts' );

function fballer_admin_geo_scripts_9842() {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	$allowed_post_types = array( 'games', 'champs', 'places', 'teams', 'players' );
	$allowed_taxonomies = array( 'city_direction', 'admin_area', 'district', 'metro' );

	$screen_taxonomy = isset( $screen->taxonomy ) ? $screen->taxonomy : '';
	$is_geo_post_screen = in_array( $screen->base, array( 'post', 'post-new' ), true ) && in_array( $screen->post_type, $allowed_post_types, true );
	$is_geo_term_screen = in_array( $screen->base, array( 'edit-tags', 'term' ), true ) && in_array( $screen_taxonomy, $allowed_taxonomies, true );

	if ( ! $is_geo_post_screen && ! $is_geo_term_screen ) {
		return;
	}

	wp_enqueue_script(
		'fballer-admin-geo',
		get_template_directory_uri() . '/assets/js/admin-geo-taxonomies.js',
		array( 'jquery' ),
		filemtime( get_template_directory() . '/assets/js/admin-geo-taxonomies.js' ),
		true
	);

	$geo_taxonomies = array( 'city_direction', 'admin_area', 'district', 'metro' );
	$term_map = array();
	$city_roots = array();
	$root_term_ids = array();

	foreach ( $geo_taxonomies as $taxonomy ) {
		$term_map[ $taxonomy ] = array();
		$city_roots[ $taxonomy ] = array();
		$root_term_ids[ $taxonomy ] = array();

		$terms = get_terms(array(
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
		));

		if ( ! is_array( $terms ) ) {
			continue;
		}

		foreach ( $terms as $term ) {
			$related_city = (int) get_term_meta( $term->term_id, 'related_city', true );
			$related_direction = (int) get_term_meta( $term->term_id, 'related_direction', true );
			$related_admin_area = (int) get_term_meta( $term->term_id, 'related_admin_area', true );
			$related_district = (int) get_term_meta( $term->term_id, 'related_district', true );
			$is_root = ( 'yes' === get_term_meta( $term->term_id, '_fballer_geo_city_root', true ) );

			$term_map[ $taxonomy ][ $term->term_id ] = array(
				'city' => $related_city,
				'direction' => $related_direction,
				'adminArea' => $related_admin_area,
				'district' => $related_district,
				'isRoot' => $is_root,
			);

			if ( $is_root && $related_city ) {
				$city_roots[ $taxonomy ][ $related_city ] = (int) $term->term_id;
				$root_term_ids[ $taxonomy ][] = (int) $term->term_id;
			}
		}
	}

	wp_localize_script( 'fballer-admin-geo', 'fballerGeoAdminData', array(
		'screenBase' => $screen->base,
		'screenTaxonomy' => $screen_taxonomy,
		'termMap' => $term_map,
		'cityRoots' => $city_roots,
		'rootTermIds' => $root_term_ids,
	) );
}
add_action( 'admin_enqueue_scripts', 'fballer_admin_geo_scripts_9842' );

// Подключаем код в <head>
function theme_add_code_to_header(){
	// Цвет вкладок для мобильных устройств
	if ( get_theme_mod('mobile-color') ) {
		$color = get_theme_mod('mobile-color');
		echo '<meta name="theme-color" content="' . $color . '" />';
		echo '<meta name="msapplication-navbutton-color" content="' . $color . '" />';
		echo '<meta name="apple-mobile-web-app-status-bar-style" content="' . $color . '" />';
	}
	
	// Google Fonts
	if ( wp_style_is('google-fonts-montserrat') || wp_style_is('google-fonts-rubik') ) {
		echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
	}
	
	// Дополнительные скрипты
	if ( get_theme_mod('custom_html_head') ) {
		echo get_theme_mod('custom_html_head');
	}
}
add_action( 'wp_head', 'theme_add_code_to_header' );

// preload styles
function style_loader_tag_preload_filter($html, $handle) {
	if ( strpos($handle, 'google-fonts-') !== false ) {
        $html = str_replace("rel='stylesheet'", "rel='preload' as='style' onload='this.rel=\"stylesheet\"'", $html);
    }
	
    return $html;
}
add_filter( 'style_loader_tag',  'style_loader_tag_preload_filter', 10, 2 );

// Подключаем код перед </body>
function theme_add_code_to_footer(){
	// Дополнительные скрипты
	if ( get_theme_mod('custom_html_footer') ) {
		echo get_theme_mod('custom_html_footer');
	}
}
add_action( 'wp_footer', 'theme_add_code_to_footer' );

// Очистка заголовков архивов
function theme_clean_archive_title($title) {
	return preg_replace('~^[^:]+: ~', '', $title );
}
add_filter('get_the_archive_title', 'theme_clean_archive_title');

// Отключение уведомления об обновлении движка Wordpress
add_filter( 'pre_site_transient_update_core', function($a){ return null; } );
wp_clear_scheduled_hook('wp_version_check');

function fballer_is_local_environment_9842() {
	if ( wp_get_environment_type() === 'local' ) {
		return true;
	}

	$site_url = wp_parse_url( home_url(), PHP_URL_HOST );
	if ( $site_url && preg_match('/(localhost|\.local|\.test)$/i', $site_url) ) {
		return true;
	}

	return false;
}

if ( fballer_is_local_environment_9842() ) {
	add_filter( 'automatic_updater_disabled', '__return_true' );
	add_filter( 'pre_site_transient_update_plugins', '__return_null' );
	add_filter( 'pre_site_transient_update_themes', '__return_null' );
	add_filter( 'pre_site_transient_update_core', '__return_null' );
	add_filter( 'pre_http_request', function( $pre, $args, $url ) {
		if ( false === strpos( $url, 'api.wordpress.org' ) ) {
			return $pre;
		}

		return array(
			'headers'  => array(),
			'body'     => '',
			'response' => array(
				'code'    => 200,
				'message' => 'OK',
			),
			'cookies'  => array(),
			'filename' => null,
		);
	}, 10, 3 );

	add_action( 'admin_init', function() {
		remove_action( 'admin_init', '_maybe_update_core' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_themes' );
	} );

	add_action( 'init', function() {
		wp_clear_scheduled_hook( 'wp_version_check' );
		wp_clear_scheduled_hook( 'wp_update_plugins' );
		wp_clear_scheduled_hook( 'wp_update_themes' );
	}, 20 );
}

// Поддержка шорткодов в описаниях рубрик
add_filter( 'term_description', 'shortcode_unautop' );
add_filter( 'term_description', 'do_shortcode' );

// "Обрезка" отрывка
function theme_new_excerpt_more($more) {
	global $post;
	//return '... <a href="'. get_permalink($post->ID) . '">Читать далее &raquo;</a>';
	return '...';
}
add_filter('excerpt_more', 'theme_new_excerpt_more');

// Убираем рекомендованные видео для iframe YouTube
function theme_strip_related_videos($return, $data, $url) {
	if ( $data->provider_name == 'YouTube' ) {
		$data->html = str_replace('feature=oembed', 'feature=oembed&#038;rel=0', $data->html);
		return $data->html;
    } else {
		return $return;
	}
}
add_filter( 'oembed_dataparse', 'theme_strip_related_videos', 10, 3 );

// Отключаем Gutenberg
add_filter( 'use_block_editor_for_post', '__return_false', 10 );

// Переопределение вывода шорткода галереи
function custom_gallery_shortcode_8942($output, $atts, $instance) {
	static $gallery_count = 0; // Глобальный счётчик галерей
	$gallery_count++;
	
	$post = get_post();

	// Разбираем атрибуты галереи
	$atts = shortcode_atts(
		[
			'order'   => 'ASC',
			'orderby' => 'menu_order ID',
			'ids'     => '',
			'size'    => 'thumbnail',
		],
		$atts,
		'gallery'
	);

	$ids = explode(',', $atts['ids']); // Получаем массив ID изображений
	if ( empty( $ids ) ) {
		return '';
	}

	$gallery_id = 'gallery-' . $gallery_count; // Уникальный ID
	
	// Начинаем формировать вывод
	$output = '<div id="' . $gallery_id . '" class="post-gallery" data-post-image="post-gallery-' . $gallery_id . '">';
	foreach ( $ids as $id ) {
		$image_full = wp_get_attachment_image_url($id, 'full');
		$image_url = wp_get_attachment_image_url($id, $atts['size']);
		
		$output .= '<a href="' . esc_url($image_full) . '" class="post-gallery__image" data-fancybox="gal-post-gallery-' . $gallery_id . '">';
		$output .= '<img src="' . esc_url($image_url) . '" alt="img">';
		$output .= '</a>';
	}
	$output .= '</div>';

	return $output;
}
add_filter( 'post_gallery', 'custom_gallery_shortcode_8942', 10, 3 );

// Классы для body
function theme_new_body_classes($classes) {
	if ( is_home() || is_front_page() ) {
		$classes[] = 'homepage-body';
	}
	
	return $classes;
}
add_filter( 'body_class', 'theme_new_body_classes' );

function fballer_get_default_city_id() {
	$default_city = get_term_by('slug', 'moskva', 'city');
	if ( $default_city instanceof WP_Term && (int) $default_city->parent === 0 ) {
		return (int) $default_city->term_id;
	}

	$cities = get_terms(array(
		'taxonomy' => 'city',
		'hide_empty' => false,
		'parent' => 0,
		'number' => 1,
		'orderby' => 'term_order',
		'order' => 'ASC',
	));

	if ( is_array($cities) && ! empty($cities) ) {
		return (int) $cities[0]->term_id;
	}

	return 0;
}

function fballer_get_selected_city_id() {
	$city_id = isset($_COOKIE['selected_city']) ? (int) $_COOKIE['selected_city'] : 0;
	if ( $city_id > 0 ) {
		$term = get_term($city_id, 'city');
		if ( $term instanceof WP_Term && (int) $term->parent === 0 ) {
			return $city_id;
		}
	}

	return fballer_get_default_city_id();
}

function fballer_get_city_timezone($city_id = 0) {
	$city_id = (int) $city_id;
	if ( $city_id <= 0 ) {
		$city_id = fballer_get_selected_city_id();
	}

	$timezone = '';
	if ( $city_id > 0 ) {
		if ( function_exists('carbon_get_term_meta') ) {
			$timezone = (string) carbon_get_term_meta($city_id, 'city_timezone');
		}

		if ( $timezone === '' ) {
			$timezone = (string) get_term_meta($city_id, 'city_timezone', true);
		}
	}

	if ( $timezone === '' ) {
		$timezone = wp_timezone_string();
	}

	if ( $timezone === '' ) {
		$timezone = 'Europe/Moscow';
	}

	try {
		new DateTimeZone($timezone);
		return $timezone;
	} catch (Exception $e) {
		return 'Europe/Moscow';
	}
}

function fballer_get_city_now_timestamp($city_id = 0) {
	$timezone = new DateTimeZone(fballer_get_city_timezone($city_id));
	$now = new DateTimeImmutable('now', $timezone);

	return $now->getTimestamp();
}

function fballer_build_game_start_local_timestamp($game_date, $game_time, $city_id = 0) {
	$game_date = (int) $game_date;
	$game_time = preg_replace('/\D+/', '', (string) $game_time);
	$city_id = (int) $city_id;

	if ( $game_date <= 0 || $game_time === '' ) {
		return null;
	}

	if ( strlen($game_time) === 3 ) {
		$game_time = '0' . $game_time;
	}

	if ( ! preg_match('/^\d{4}$/', $game_time) ) {
		return null;
	}

	$date_string = wp_date('Y-m-d', $game_date, wp_timezone());
	if ( $date_string === '' ) {
		return null;
	}

	$timezone = new DateTimeZone(fballer_get_city_timezone($city_id));
	$game_start = DateTimeImmutable::createFromFormat('Y-m-d Hi', $date_string . ' ' . $game_time, $timezone);

	if ( ! $game_start ) {
		return null;
	}

	return $game_start->getTimestamp();
}

function fballer_sync_game_start_local_ts($post_id, $city_id = 0) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || get_post_type($post_id) !== 'games' ) {
		return;
	}

	if ( $city_id <= 0 ) {
		$city_terms = wp_get_post_terms($post_id, 'city', ['fields' => 'ids']);
		$city_id = ! empty($city_terms) ? (int) $city_terms[0] : 0;
	}

	$game_date = get_post_meta($post_id, 'game_date', true);
	if ( $game_date === '' || $game_date === null ) {
		$game_date = get_post_meta($post_id, '_game_date', true);
	}

	$game_time = get_post_meta($post_id, 'game_time', true);
	if ( $game_time === '' || $game_time === null ) {
		$game_time = get_post_meta($post_id, '_game_time', true);
	}

	$game_start_local_ts = fballer_build_game_start_local_timestamp($game_date, $game_time, $city_id);

	if ( $game_start_local_ts ) {
		update_post_meta($post_id, 'game_start_local_ts', $game_start_local_ts);
	} else {
		delete_post_meta($post_id, 'game_start_local_ts');
	}
}

function fballer_backfill_game_start_local_ts_for_city($city_id) {
	$city_id = (int) $city_id;
	if ( $city_id <= 0 ) {
		return;
	}

	$game_ids = get_posts([
		'post_type' => 'games',
		'post_status' => ['publish', 'pending', 'draft', 'future', 'private'],
		'posts_per_page' => -1,
		'fields' => 'ids',
		'no_found_rows' => true,
		'tax_query' => [
			[
				'taxonomy' => 'city',
				'field' => 'term_id',
				'terms' => [$city_id],
			],
		],
		'meta_query' => [
			'relation' => 'AND',
			[
				'key' => 'game_date',
				'compare' => 'EXISTS',
			],
			[
				'key' => 'game_time',
				'compare' => 'EXISTS',
			],
			[
				'relation' => 'OR',
				[
					'key' => 'game_start_local_ts',
					'compare' => 'NOT EXISTS',
				],
				[
					'key' => 'game_start_local_ts',
					'value' => '',
					'compare' => '=',
				],
			],
		],
	]);

	foreach ( $game_ids as $game_id ) {
		fballer_sync_game_start_local_ts((int) $game_id, $city_id);
	}
}

function fballer_apply_upcoming_games_query_args($query_args, $city_id = 0) {
	$city_id = (int) $city_id;
	if ( $city_id <= 0 ) {
		$city_id = fballer_get_selected_city_id();
	}

	if ( $city_id <= 0 ) {
		return $query_args;
	}

	fballer_backfill_game_start_local_ts_for_city($city_id);

	$current_timestamp = fballer_get_city_now_timestamp($city_id);

	$meta_query = isset($query_args['meta_query']) && is_array($query_args['meta_query']) ? $query_args['meta_query'] : [];
	$meta_relation = isset($meta_query['relation']) ? $meta_query['relation'] : null;
	$clean_meta_query = [];

	foreach ( $meta_query as $meta_clause ) {
		if ( ! is_array($meta_clause) ) {
			continue;
		}

		$key = $meta_clause['key'] ?? '';
		if ( in_array($key, ['game_date', 'game_start_local_ts'], true) ) {
			continue;
		}

		$clean_meta_query[] = $meta_clause;
	}

	if ( $meta_relation ) {
		$clean_meta_query['relation'] = $meta_relation;
	}

	$clean_meta_query[] = [
		'key' => 'game_start_local_ts',
		'value' => $current_timestamp,
		'compare' => '>=',
		'type' => 'NUMERIC',
	];

	$tax_query = isset($query_args['tax_query']) && is_array($query_args['tax_query']) ? $query_args['tax_query'] : [];
	$tax_relation = isset($tax_query['relation']) ? $tax_query['relation'] : null;
	$has_city_clause = false;

	foreach ( $tax_query as $tax_clause ) {
		if ( is_array($tax_clause) && ($tax_clause['taxonomy'] ?? '') === 'city' ) {
			$has_city_clause = true;
			break;
		}
	}

	if ( ! $has_city_clause ) {
		$tax_query[] = [
			'taxonomy' => 'city',
			'field' => 'term_id',
			'terms' => $city_id,
		];
	}

	if ( $tax_relation ) {
		$tax_query['relation'] = $tax_relation;
	}

	$query_args['meta_key'] = 'game_start_local_ts';
	$query_args['meta_query'] = $clean_meta_query;
	$query_args['tax_query'] = $tax_query;

	if ( isset($query_args['orderby']) && is_array($query_args['orderby']) ) {
		$query_args['orderby'] = array_merge(['meta_value_num' => 'ASC'], $query_args['orderby']);
	}

	return $query_args;
}

function fballer_apply_recent_teams_query_args($query_args, $city_id = 0) {
	$city_id = (int) $city_id;
	if ( $city_id <= 0 ) {
		$city_id = fballer_get_selected_city_id();
	}

	$tax_query = isset($query_args['tax_query']) && is_array($query_args['tax_query']) ? $query_args['tax_query'] : [];
	$tax_relation = isset($tax_query['relation']) ? $tax_query['relation'] : null;
	$has_city_clause = false;

	foreach ( $tax_query as $tax_clause ) {
		if ( is_array($tax_clause) && ($tax_clause['taxonomy'] ?? '') === 'city' ) {
			$has_city_clause = true;
			break;
		}
	}

	if ( $city_id > 0 && ! $has_city_clause ) {
		$tax_query[] = [
			'taxonomy' => 'city',
			'field' => 'term_id',
			'terms' => $city_id,
		];
	}

	if ( $tax_relation ) {
		$tax_query['relation'] = $tax_relation;
	}

	$query_args['tax_query'] = $tax_query;
	$query_args['date_query'] = [
		[
			'after' => '30 days ago',
			'inclusive' => true,
			'column' => 'post_date',
		],
	];

	if ( isset($query_args['orderby']) && is_array($query_args['orderby']) ) {
		$query_args['orderby'] = array_merge(['date' => 'DESC'], $query_args['orderby']);
	}

	return $query_args;
}

function fballer_sync_game_start_local_ts_on_save($post_id, $post, $update) {
	if ( wp_is_post_revision($post_id) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ) {
		return;
	}

	fballer_sync_game_start_local_ts($post_id);
}
add_action('save_post_games', 'fballer_sync_game_start_local_ts_on_save', 20, 3);

function fballer_sync_game_start_local_ts_on_city_set($object_id, $terms, $tt_ids, $taxonomy) {
	if ( $taxonomy !== 'city' || get_post_type($object_id) !== 'games' ) {
		return;
	}

	$city_id = ! empty($terms) ? (int) reset($terms) : 0;
	fballer_sync_game_start_local_ts((int) $object_id, $city_id);
}
add_action('set_object_terms', 'fballer_sync_game_start_local_ts_on_city_set', 20, 4);

function theme_set_default_city_cookie() {
	$key = 'selected_city';
	$selected_city = fballer_get_selected_city_id();
	if ( $selected_city > 0 ) {
		$_COOKIE[ $key ] = $selected_city; // Делаем переменную доступной при первом посещении
	}
}
add_action('init', 'theme_set_default_city_cookie');

function fballer_get_page_url_by_template($slug, $template) {
	$page = get_page_by_path($slug);

	if ( ! $page ) {
		$pages = get_pages([
			'meta_key' => '_wp_page_template',
			'meta_value' => $template,
			'number' => 1,
		]);

		if ( ! empty($pages) ) {
			$page = $pages[0];
		}
	}

	if ( $page ) {
		return get_permalink($page->ID);
	}

	return '';
}

function fballer_get_archive_page_url($slug, $template) {
	$page_url = fballer_get_page_url_by_template($slug, $template);
	if ( $page_url ) {
		return $page_url;
	}

	return home_url('/' . trim($slug, '/') . '/');
}

// Редиректы
function theme_template_redirect() {
	// Редирект для архивных страниц
	$acrhive_pages = [
		'games' => 53,
		'champs' => 1057,
		'places' => 711,
		'teams' => 1106,
	];
	if ( isset($acrhive_pages) ) {
		foreach ( $acrhive_pages as $archive_slug => $post_id ) {
			if ( is_post_type_archive($archive_slug) ) {
				$url = get_page_link($post_id);
				if ( $url ) {
					wp_redirect( $url, 301 );
					exit();
				}
			}
		}
	}

	if ( is_post_type_archive('players') ) {
		$url = fballer_get_page_url_by_template('players', 'templates/template-players.php');
		if ( $url ) {
			wp_redirect($url, 301);
			exit();
		}
	}
}
add_action( 'template_redirect', 'theme_template_redirect' );

function fballer_append_players_link_to_header_menu($items, $args) {
	if ( empty($args->theme_location) || $args->theme_location !== 'menu-header' ) {
		return $items;
	}

	$url = fballer_get_archive_page_url('players', 'templates/template-players.php');
	if ( ! $url || strpos($items, 'menu-item-players-link') !== false ) {
		return $items;
	}

	if ( strpos($items, esc_url($url)) !== false ) {
		return $items;
	}

	$items .= '<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-players-link"><a href="' . esc_url($url) . '">игроки</a></li>';

	return $items;
}
add_filter('wp_nav_menu_items', 'fballer_append_players_link_to_header_menu', 10, 2);
