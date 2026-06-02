<?php

// Защита от прямого доступа к файлу
if ( ! defined('ABSPATH') ) {
	exit;
}

// Спасибо вам за творчество с WordPress
function theme_remove_footer_admin($text) {
	return false;
} 
add_filter('admin_footer_text', 'theme_remove_footer_admin');

// Убираем панель приветствия
remove_action('welcome_panel', 'wp_welcome_panel');

// Убриаем социальные ссылки в профиле
function theme_modified_user_fields( $contact_methods ){
	unset($contact_methods['facebook']);
	unset($contact_methods['instagram']);
	unset($contact_methods['linkedin']);
	unset($contact_methods['myspace']);
	unset($contact_methods['pinterest']);
	unset($contact_methods['tumblr']);
	unset($contact_methods['twitter']);
	unset($contact_methods['youtube']);
	unset($contact_methods['wikipedia']);
	unset($contact_methods['soundcloud']);

	return $contact_methods;
}
add_filter('user_contactmethods', 'theme_modified_user_fields');

function my_login_logo_url() {
	return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
	return 'Вернуться на главную страницу';
}
add_filter( 'login_headertext', 'my_login_logo_url_title' );

// Скрываем подсказки об ошибках на странице ввода логина и пароля
function theme_hide_wordpress_errors(){
	return 'Что-то пошло не так!';
}
add_filter('login_errors', 'theme_hide_wordpress_errors');

// Скрываем админбар для всех кроме администраторов
function theme_handle_admin_bar($content) {
	if ( ! current_user_can('manage_options') ) {
		show_admin_bar( false );
	}
}
add_action( 'init', 'theme_handle_admin_bar' );
