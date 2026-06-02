<?php

// Защита от прямого доступа к файлу
if ( ! defined('ABSPATH') ) {
	exit;
}

// Меню сайта
register_nav_menus(array(
	// 'menu-header' => 'Верхнее меню',
	'menu-footer' => 'Нижнее меню',
	'menu-footer2' => 'Нижнее меню 2',
	'menu-footer3' => 'Нижнее меню 3',
	'menu-footer-center' => 'Нижнее центральное меню',
));

register_nav_menus(array(
	// 'menu-header' => 'Верхнее меню',
	'menu-header' => 'Верхнее меню',
));

// Добавляем параметры для ссылок меню
function theme_menu_add_atts_to_link($atts, $item, $args) {
	if ( $args->theme_location == 'menu-header' ) {
		if ( ! isset($atts['class']) ) $atts['class'] = '';

		if ( isset($item->classes[0]) && $item->classes[0] != '' ) {
			$atts['class'] .= ' ' . $item->classes[0];
		}
		
		if ( $item->current == 1 ) {
			$atts['class'] .= ' activepath';
		}
		
		$atts['class'] = trim($atts['class']);
	}

    return $atts;
}
// add_filter( 'nav_menu_link_attributes', 'theme_menu_add_atts_to_link', 10, 3 );

// Добавляем классы для элемента меню
function theme_menu_add_class_to_item( $classes, $item, $args, $depth ){
	if ( $args->theme_location == 'menu-header' ) {
		$classes[] = 'moto-widget-menu-item';
	}
	return $classes;
}
// add_filter( 'nav_menu_css_class', 'theme_menu_add_class_to_item', 10, 4 );

// Изменить HTML меню
function theme_new_menu_html( $items, $args ) {
	if ( 'header_menu' == $args->theme_location ) {
		$items .= '<li><a href=""><i class="icon-instagram"></i></a></li>';
	}

	return $items;
}
// add_filter( 'wp_nav_menu_items', 'theme_new_menu_html', 25, 2 );

// Изменяем меню
function theme_menu_args( $nav_menu_args, $nav_menu, $args, $instance){
	if ( $args['id'] == 'footer-4' ) {
		$nav_menu_args = array_merge( $nav_menu_args, array(
			'container_class' => 'footer-social p-relative',
			'walker' => new theme_walker_footer_menu(),
		) );
	}

	return $nav_menu_args;
}
// add_filter('widget_nav_menu_args', 'theme_menu_args', 10, 4); // Для меню в виджетах
// add_filter('wp_nav_menu_args', 'theme_menu_args'); // Для меню

// Кастомное меню
class Theme_Custom_Walker_Nav_Menu extends Walker_Nav_Menu {
	// Display Element
	function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		$id_field = $this->db_fields['id'];

		if ( isset($args[0]) && is_object($args[0]) ) {
			$args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
		}

		return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	// Start Element
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		// Wrapper текста ссылки
		$args->link_before = '<span class="yjm_has_none"><span class="yjm_title">';
		$args->link_after = '</span></span>';

		// Wrapper ссылки
		$args->before = '<span class="mymarg">';
		if ( is_object($args) && ! empty($args->has_children) ) {
			$args->before = '<span class="child">';
		}
		$args->after = '</span>';

		parent::start_el($output, $item, $depth, $args, $id);
	}

	// Start Level
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("t", $depth);
		// $output .= "\n$indent<ul class=\"dropdown-menu list-unstyled\">\n";
		$html = '<div class="ulholder level1 nogroup"><ul class="subul_main level1 nogroup">';
		$output .= "\n$indent" . $html . "\n";
	}
	
	// End level
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$html = '</ul></div>';
		$output .= "\n$indent" . $html . "\n";
	}
}

?>