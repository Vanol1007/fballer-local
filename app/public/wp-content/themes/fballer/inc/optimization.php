<?php

// Защита от прямого доступа к файлу
if ( ! defined('ABSPATH') ) {
	exit;
}

// Удаление файлов license.txt и readme.html для защиты.
if ( is_admin() && ! defined('DOING_AJAX') ) {
	$license_file = ABSPATH . '/license.txt';
	$readme_file = ABSPATH . '/readme.html';

	if ( file_exists($license_file) && current_user_can('manage_options') ) {
		$deleted = unlink($license_file) && unlink($readme_file);

		if ( ! $deleted ) {
			$GLOBALS['readmedel'] = 'Не удалось удалить файлы: license.txt и readme.html из папки `'. ABSPATH .'`. Удалите их вручную!';
		} else {
			$GLOBALS['readmedel'] = 'Файлы: license.txt и readme.html удалены из папки `'. ABSPATH .'`.';
		}
		add_action( 'admin_notices', function(){ echo '<div class="error is-dismissible"><p>'. $GLOBALS['readmedel'] .'</p></div>'; } );
	}
}

// Удаляем meta generator
if ( get_theme_mod('optimization-meta-generator') ) {
	remove_action('wp_head', 'wp_generator');
	add_filter('the_generator', '__return_empty_string');
	
	// Удалить meta generator для плагина WP Bakery Page Builder
	function theme_remove_vc_meta_generator(){
		if ( class_exists( 'Vc_Base' ) ) {
			remove_action('wp_head', array(visual_composer(), 'addMetaData'));
		}
	}
	add_action('wp_head', 'theme_remove_vc_meta_generator', 1);
	
	// Удалить meta generator для плагина Slider Revolution
	add_filter('revslider_meta_generator', '__return_empty_string');
}

// Удаляем RSD ссылку
if ( get_theme_mod('optimization-rsd') ) {
	remove_action('wp_head', 'rsd_link');
}

// Удаляем WLW manifest ссылку
if ( get_theme_mod('optimization-wlw-manifest') ) {
	remove_action('wp_head', 'wlwmanifest_link');
}

// Убрать ссылки на предыдущую/следующую запись
if ( get_theme_mod('optimization-next-prev-url') ) {
	remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
}

// Удаляем короткую ссылку
if ( get_theme_mod('optimization-shortlink') ) {
	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
	remove_action('template_redirect', 'wp_shortlink_header', 11, 0);
}

// Отключаем Emoji
if ( get_theme_mod('optimization-emoji') ) {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}

// Удаляем стили .recentcomments
if ( get_theme_mod('optimization-recentcomments') ) {
	function theme_remove_recent_comments_style() {  
		global $wp_widget_factory;  
		remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );  
	}  
	add_action( 'widgets_init', 'theme_remove_recent_comments_style' );
}

// Удаляем dns-prefetch
if ( get_theme_mod('optimization-dns-prefetch') ) {
	remove_action('wp_head', 'wp_resource_hints', 2);
}

// Отключаем XML-RPC
if ( get_theme_mod('optimization-xml-rpc') ) {
	add_filter('xmlrpc_enabled', '__return_false');
	function theme_remove_x_pingback($headers) {
		unset($headers['X-Pingback']);
		return $headers;
	}
	add_filter('wp_headers', 'theme_remove_x_pingback');
	header_remove('X-Pingback');
	header_remove('Server');
}

// Удалить архивы дат и убираем виджет с архивами
if ( get_theme_mod('optimization-archive-date') ) {
	function theme_remove_archives_date() {
		if ( is_date() && !is_admin() ) {
			wp_redirect( get_home_url(), 301 );
			exit();
		}
		return false;
	}
	add_action( 'wp', 'theme_remove_archives_date' );

	function theme_widgets_init_remove_archives_date() {
		unregister_widget('WP_Widget_Archives');
		return false;
	}
	add_action( 'widgets_init', 'theme_widgets_init_remove_archives_date' );
}

// Удалить архивы пользователей
if ( get_theme_mod('optimization-archive-user') ) {
	function theme_remove_archives_author() {
		if ( is_author() ) {
			wp_redirect( get_home_url(), 301 );
			exit();
		}
		return false;
	}
	add_action( 'wp', 'theme_remove_archives_author' );
}

// Убрать из админ бара меню с логотипом Wordpress
if ( get_theme_mod('optimization-adminbar') ) {
	function theme_admin_bar_remove() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('wp-logo');
	}
	add_action('wp_before_admin_bar_render', 'theme_admin_bar_remove', 0);
}

// RSS
if ( get_theme_mod('optimization-rss') ) {
	remove_action('wp_head', 'feed_links_extra', 3);
	remove_action('wp_head', 'feed_links', 2);
	
	function theme_disable_rss_feeds_redirect($wut) {
		wp_redirect( get_home_url(), 301 );
		exit;
	}
	add_action('do_feed', 'theme_disable_rss_feeds_redirect', 1);
	add_action('do_feed_rdf', 'theme_disable_rss_feeds_redirect', 1);
	add_action('do_feed_rss', 'theme_disable_rss_feeds_redirect', 1);
	add_action('do_feed_rss2', 'theme_disable_rss_feeds_redirect', 1);
	add_action('do_feed_atom', 'theme_disable_rss_feeds_redirect', 1);
	add_action('do_feed_rss2_comments', 'theme_disable_rss_feeds_redirect', 1);
	add_action('do_feed_atom_comments', 'theme_disable_rss_feeds_redirect', 1);
}

// Минификация HTML
if ( get_theme_mod('optimization-minify-html') ) {
	if ( ! is_admin() ) {
		function theme_html_minify($buffer){
			$buffer = ob_get_contents();
			
			// JS inline
			$buffer = preg_replace_callback(
				'/<script((?:(?!src=).)*?)>(.*?)<\/script>/is',
				function ($matches) {
					$js = $matches[0];
					
					$js = preg_replace( '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/', '', $js ); // Comments
					$js = preg_replace( '/(\s)+/si', "$1", $js );
					$js = preg_replace( '/(\r\n|\n|\r)/', '', $js );
					$js = preg_replace( '/(\t)/', ' ', $js );
					
					return $js;
				},
				$buffer
			);
			
			$buffer = preg_replace('%(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!/?(?:textarea|pre)\b))*+)(?:<(?>textarea|pre)\b|\z))%ix', ' ', $buffer);
			$buffer = preg_replace('~<!-- ([a-zA-Z0-9\s\.\/\\\\:\-\#\{\\$}]+) -->~', '', $buffer);
			
			return $buffer;
		}
		
		function theme_html_minify_start(){
			ob_start('theme_html_minify');
		}
		add_action( 'wp_loaded', 'theme_html_minify_start' );
		
		function theme_html_minify_end(){
			if ( ob_get_length() ) ob_end_flush();
		}
		add_action( 'shutdown', 'theme_html_minify_end' );
	}
}

// Отключить визуальный поиск для изображений
if ( get_theme_mod('optimization-disable-visual-search') ) {
	if ( ! function_exists('theme_optimization_disable_visual_search_621') ) {
		function theme_optimization_disable_visual_search_621() {
			$style_id = 'disable-visual-search';
			$css = 'img { pointer-events: none; }';
			
			wp_register_style($style_id, false);
			wp_enqueue_style($style_id);
			wp_add_inline_style($style_id, $css);
		}
		add_action( 'wp_enqueue_scripts', 'theme_optimization_disable_visual_search_621' );
	}
}

// Состояние здоровья сайта > Настройки автозагрузки могут повлиять на производительность
add_filter('site_status_autoloaded_options_size_limit', function($limit) {
	$limit = 1048576; // 1 Mb
	return $limit;
});

// Состояние здоровья сайта > Обнаружена активная PHP сессия
function theme_curl_before_request($curlhandle){
	session_write_close();
}
add_action( 'requests-curl.before_request', 'theme_curl_before_request', 9999 );
