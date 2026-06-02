<?php

// Защита от прямого доступа к файлу
if ( ! defined('ABSPATH') ) {
	exit;
}

// AJAX обработчик для получения постов
function fballer_get_ajax_filter_ids_9842( $value ) {
	if ( is_string( $value ) ) {
		$decoded = json_decode( wp_unslash( $value ), true );
		if ( is_array( $decoded ) ) {
			$value = $decoded;
		}
	}

	if ( ! is_array( $value ) ) {
		return array();
	}

	return array_values(array_filter(array_map('intval', $value)));
}

function fballer_apply_ajax_filters_to_query_9842( $query_vars, $request ) {
	if ( ! isset($query_vars['meta_query']) || ! is_array($query_vars['meta_query']) ) {
		$query_vars['meta_query'] = array();
	}

	if ( ! isset($query_vars['tax_query']) || ! is_array($query_vars['tax_query']) ) {
		$query_vars['tax_query'] = array();
	}

	$filter_date = isset($request['f1']) ? strtotime((string) $request['f1']) : null;
	if ( $filter_date ) {
		$day_start = strtotime('midnight', $filter_date);
		$date_end = strtotime('tomorrow', $day_start) - 1;

		$key = 'champ_start';
		if ( isset($query_vars['post_type']) && $query_vars['post_type'] === 'games' ) {
			$key = 'game_date';
		}

		$query_vars['meta_query'][] = array(
			'key' => $key,
			'value' => array($day_start, $date_end),
			'compare' => 'BETWEEN',
			'type' => 'NUMERIC',
		);
	}

	$filter_district = (isset($request['f2']) && ! empty($request['f2']) ? (int) $request['f2'] : null);
	if ( $filter_district ) {
		$query_vars['tax_query'][] = array(
			'taxonomy' => 'city',
			'field' => 'term_id',
			'terms' => $filter_district,
		);
	}

	$filter_field_size = (isset($request['f3']) && ! empty($request['f3']) ? (int) $request['f3'] : null);
	if ( $filter_field_size ) {
		$query_vars['tax_query'][] = array(
			'taxonomy' => 'field_size',
			'field' => 'term_id',
			'terms' => $filter_field_size,
		);
	}

	$filter_coating = (isset($request['f4']) && ! empty($request['f4']) ? (int) $request['f4'] : null);
	if ( $filter_coating ) {
		$query_vars['tax_query'][] = array(
			'taxonomy' => 'coating',
			'field' => 'term_id',
			'terms' => $filter_coating,
		);
	}

	$filter_game_format = (isset($request['f5']) && ! empty($request['f5']) ? (int) $request['f5'] : null);
	if ( $filter_game_format ) {
		$query_vars['tax_query'][] = array(
			'taxonomy' => 'game_format',
			'field' => 'term_id',
			'terms' => $filter_game_format,
		);
	}

	$team_level = (isset($request['f7']) && ! empty($request['f7']) ? (int) $request['f7'] : null);
	if ( $team_level ) {
		$query_vars['tax_query'][] = array(
			'taxonomy' => 'team_level',
			'field' => 'term_id',
			'terms' => $team_level,
		);
	}

	$team_position = (isset($request['f8']) && ! empty($request['f8']) ? (int) $request['f8'] : null);
	if ( $team_position ) {
		$query_vars['tax_query'][] = array(
			'taxonomy' => 'team_position',
			'field' => 'term_id',
			'terms' => $team_position,
		);
	}

	$player_goal = isset($request['f9']) ? sanitize_text_field(wp_unslash($request['f9'])) : '';
	if ( $player_goal && in_array($player_goal, array('team', 'game'), true) ) {
		$query_vars['meta_query'][] = array(
			'key' => 'player_goal',
			'value' => $player_goal,
			'compare' => '=',
		);
	}

	$filter_formats = fballer_get_ajax_filter_ids_9842( $request['f10'] ?? array() );
	if ( ! empty($filter_formats) ) {
		$query_vars['tax_query'][] = array(
			'taxonomy' => 'game_format',
			'field' => 'term_id',
			'terms' => $filter_formats,
		);
	}

	foreach ( array(
		'f11' => 'city_direction',
		'f12' => 'admin_area',
		'f13' => 'district',
		'f14' => 'metro',
	) as $request_key => $taxonomy ) {
		$terms = fballer_get_ajax_filter_ids_9842( $request[ $request_key ] ?? array() );
		if ( empty($terms) ) {
			continue;
		}

		$query_vars['tax_query'][] = array(
			'taxonomy' => $taxonomy,
			'field' => 'term_id',
			'terms' => $terms,
		);
	}

	$game_price_filters = isset($request['f15']) ? array_values(array_filter(array_map('sanitize_text_field', (array) $request['f15']))) : array();
	if ( ! empty($game_price_filters) && isset($query_vars['post_type']) && $query_vars['post_type'] === 'games' ) {
		$price_meta_query = array( 'relation' => 'OR' );

		foreach ( $game_price_filters as $bucket ) {
			if ( $bucket === 'free' ) {
				$price_meta_query[] = array(
					'key' => 'game_price',
					'value' => 'бесплатно',
					'compare' => 'LIKE',
				);
				continue;
			}

			if ( $bucket === '500' ) {
				$price_meta_query[] = array(
					'relation' => 'AND',
					array(
						'key' => 'game_price',
						'value' => 'бесплатно',
						'compare' => 'NOT LIKE',
					),
					array(
						'key' => 'game_price',
						'value' => 500,
						'compare' => '<=',
						'type' => 'NUMERIC',
					),
				);
				continue;
			}

			if ( $bucket === '1000' ) {
				$price_meta_query[] = array(
					'relation' => 'AND',
					array(
						'key' => 'game_price',
						'value' => 'бесплатно',
						'compare' => 'NOT LIKE',
					),
					array(
						'key' => 'game_price',
						'value' => 1000,
						'compare' => '<=',
						'type' => 'NUMERIC',
					),
				);
				continue;
			}

			if ( $bucket === '1000_plus' ) {
				$price_meta_query[] = array(
					'relation' => 'AND',
					array(
						'key' => 'game_price',
						'value' => 'бесплатно',
						'compare' => 'NOT LIKE',
					),
					array(
						'key' => 'game_price',
						'value' => 1000,
						'compare' => '>',
						'type' => 'NUMERIC',
					),
				);
			}
		}

		if ( count($price_meta_query) > 1 ) {
			$query_vars['meta_query'][] = $price_meta_query;
		}
	}

	$filter_coatings = fballer_get_ajax_filter_ids_9842( $request['f16'] ?? array() );
	if ( ! empty($filter_coatings) ) {
		$query_vars['tax_query'][] = array(
			'taxonomy' => 'coating',
			'field' => 'term_id',
			'terms' => $filter_coatings,
		);
	}

	$team_level_filters = fballer_get_ajax_filter_ids_9842( $request['f17'] ?? array() );
	if ( ! empty($team_level_filters) ) {
		$query_vars['tax_query'][] = array(
			'taxonomy' => 'team_level',
			'field' => 'term_id',
			'terms' => $team_level_filters,
		);
	}

	$team_position_filters = fballer_get_ajax_filter_ids_9842( $request['f18'] ?? array() );
	if ( ! empty($team_position_filters) ) {
		$query_vars['tax_query'][] = array(
			'taxonomy' => 'team_position',
			'field' => 'term_id',
			'terms' => $team_position_filters,
		);
	}

	$player_goal_filters = isset($request['f19']) ? array_values(array_filter(array_map('sanitize_text_field', (array) $request['f19']))) : array();
	if ( ! empty($player_goal_filters) ) {
		$query_vars['meta_query'][] = array(
			'key' => 'player_goal',
			'value' => $player_goal_filters,
			'compare' => 'IN',
		);
	}

	$filter_price = isset($request['f6']) ? (int) $request['f6'] : null;
	if ( $filter_price ) {
		$query_vars['meta_query'] = array(
			'relation' => 'AND',
			array(
				'key' => 'champ_price',
				'compare' => 'EXISTS',
			),
			array(
				'key' => 'champ_price',
				'value' => '',
				'compare' => '!=',
			),
			array(
				'key' => 'champ_price',
				'value' => $filter_price,
				'compare' => '<=',
				'type' => 'NUMERIC',
			),
		);
	}

	return $query_vars;
}

function load_more_posts_4832() {
	$query_vars = json_decode(stripslashes($_POST['query_vars']), true);
	$is_home = (isset($_POST['is_home']) && $_POST['is_home'] == true ? true : false);
	$query_vars['paged'] = $_POST['current_page'];

	if ( isset($query_vars['post_type']) && $query_vars['post_type'] === 'games' ) {
		$query_vars = fballer_apply_upcoming_games_query_args($query_vars);
	}

	if ( isset($query_vars['post_type']) && $query_vars['post_type'] === 'teams' ) {
		$query_vars = fballer_apply_recent_teams_query_args($query_vars);
	}

	$query_vars = fballer_apply_ajax_filters_to_query_9842( $query_vars, $_POST );

	if ( $query_vars['post_type'] == 'games' || $query_vars['post_type'] == 'champs' || $query_vars['post_type'] == 'teams' || $query_vars['post_type'] == 'players' ) {
		$posts_per_page = 4;
		if ( $query_vars['post_type'] == 'champs' ) $posts_per_page = 3;
		if ( $query_vars['post_type'] == 'teams' ) $posts_per_page = 12;
		if ( $query_vars['post_type'] == 'players' ) $posts_per_page = 12;

		$offset = $query_vars['posts_per_page'] + (($query_vars['paged'] - 2) * $posts_per_page);

		$query_vars['posts_per_page'] = $posts_per_page;
		$query_vars['offset'] = $offset;
	}

	// echo $query_vars['paged'];
	// echo '<hr>';
	// echo $query_vars['offset'];
	// echo '<hr>';
	// echo $query_vars['posts_per_page'];

	$query = new WP_Query($query_vars);

	// Если есть посты, подключаем шаблон
	if ( $query->have_posts() ) {
		// Устанавливаем глобальные данные для корректной работы шаблона
		global $wp_query;
		$wp_query = $query;

		// ob_start();

		while ( have_posts() ) {
			the_post();

			if ( $query_vars['post_type'] == 'games' ) {
				get_template_part('templates/content-games-item');
			} elseif ( $query_vars['post_type'] == 'places' ) {
				get_template_part('templates/content-places-item');
			} elseif ( $query_vars['post_type'] == 'teams' ) {
				get_template_part('templates/content-teams-item');
			} elseif ( $query_vars['post_type'] == 'players' ) {
				get_template_part('templates/content-players-item');
			} elseif ( $query_vars['post_type'] == 'champs' ) {
				get_template_part('templates/content-champs-item', null, array('is_home' => $is_home));
			} else {
				get_template_part('templates/content-blog-item');
			}
		}

		// $html = ob_get_clean();
		// $data['html'] = $html;
		// if ( $query_vars['post_type'] == 'games' ) $data['max_pages'] = $query->max_num_pages;
		// wp_send_json_success($data);

		wp_reset_postdata(); // Сбрасываем данные после работы с WP_Query
	} else {
		wp_send_json_error('No more posts'); // Возвращаем сообщение об ошибке
	}

	wp_die();
}
add_action('wp_ajax_load_more_posts_4832', 'load_more_posts_4832');
add_action('wp_ajax_nopriv_load_more_posts_4832', 'load_more_posts_4832');

// AJAX обработчик для фильтра
function load_more_posts_6728() {
	$query_vars = json_decode(stripslashes($_POST['query_vars']), true);
	$query_vars['paged'] = $_POST['current_page'];

	if ( isset($query_vars['post_type']) && $query_vars['post_type'] === 'games' ) {
		$query_vars = fballer_apply_upcoming_games_query_args($query_vars);
	}

	if ( isset($query_vars['post_type']) && $query_vars['post_type'] === 'teams' ) {
		$query_vars = fballer_apply_recent_teams_query_args($query_vars);
	}
	$query_vars = fballer_apply_ajax_filters_to_query_9842( $query_vars, $_POST );


	$query = new WP_Query($query_vars);

	// Если есть посты, подключаем шаблон
	if ( $query->have_posts() ) {
		// Устанавливаем глобальные данные для корректной работы шаблона
		global $wp_query;
		$wp_query = $query;

		ob_start();

		while ( have_posts() ) {
			the_post();

			if ($query_vars['post_type'] == 'games') {
				get_template_part('templates/content-games-item');
			} elseif ($query_vars['post_type'] == 'places') {
				get_template_part('templates/content-places-item');
			} elseif ($query_vars['post_type'] == 'champs') {
				get_template_part('templates/content-champs-item');
			} elseif ($query_vars['post_type'] == 'teams') {
				get_template_part('templates/content-teams-item');
			} elseif ($query_vars['post_type'] == 'players') {
				get_template_part('templates/content-players-item');
			} else {
				get_template_part('templates/content-blog-item');
			}
		}

		$html = ob_get_clean();
		$data['html'] = $html;
		$data['max_pages'] = $wp_query->max_num_pages;
		$data['query'] = $wp_query;

		wp_send_json_success($data);

		wp_reset_postdata(); // Сбрасываем данные после работы с WP_Query
	} else {
		$data['html'] = '';
		$data['max_pages'] = 0;

		wp_send_json_success($data);
	}

	wp_die();
}
add_action('wp_ajax_load_more_posts_6728', 'load_more_posts_6728');
add_action('wp_ajax_nopriv_load_more_posts_6728', 'load_more_posts_6728');

// Модифицируем $query
function theme_change_query($query) {
	if ( $query->is_main_query() && ! is_admin() ) {
		// Поля
		if ( is_post_type_archive('places') ) {
			$query->set('posts_per_page', '8');
			
			// Получаем текущий tax_query
			$tax_query = $query->get('tax_query');
			if ( ! is_array($tax_query) ) $tax_query = [];
			$tax_query[] = array(
				'taxonomy' => 'city',
				'field' => 'term_id',
				'terms' => $_COOKIE['selected_city'],
			);

			// Устанавливаем обновленные данные обратно в запрос
			$query->set('tax_query', $tax_query);
		}
		
		// Чемпионаты
		if ( is_post_type_archive('champs') ) {
			// Пагинация
			$paged = (get_query_var('paged') ? get_query_var('paged') : 1);
			$posts_per_page = ($paged == 1 ? 5 : 6);
			$query->set('posts_per_page', $posts_per_page);

			// Сортировка
			$query->set('orderby', [
				'meta_value_num' => 'ASC',
				'post_date' => 'DESC',
			]);

			// Получаем текущий meta_query
			$meta_query = $query->get('meta_query');
			if (! is_array($meta_query)) $meta_query = [];
			$meta_query[] = array(
				'key' => 'champ_start',
				'compare' => 'EXISTS',
			);
			
			// Получаем текущий tax_query
			$tax_query = $query->get('tax_query');
			if ( ! is_array($tax_query) ) $tax_query = [];
			$tax_query[] = array(
				'taxonomy' => 'city',
				'field' => 'term_id',
				'terms' => $_COOKIE['selected_city'],
			);

			// Устанавливаем обновленные данные обратно в запрос
			$query->set('meta_query', $meta_query);
			$query->set('tax_query', $tax_query);
		}
		
		// Команды
		if ( is_post_type_archive('teams') ) {
			// Пагинация
			$paged = ( get_query_var('paged') ? get_query_var('paged') : 1 );
			$posts_per_page = 11;
			$query->set('posts_per_page', $posts_per_page);
			
			// Получаем текущий tax_query
			$tax_query = $query->get('tax_query');
			if ( ! is_array($tax_query) ) $tax_query = [];
			$tax_query[] = array(
				'taxonomy' => 'city',
				'field' => 'term_id',
				'terms' => $_COOKIE['selected_city'],
			);

			// Устанавливаем обновленные данные обратно в запрос
			$query->set('tax_query', $tax_query);
		}

		if ( is_post_type_archive('players') ) {
			$paged = ( get_query_var('paged') ? get_query_var('paged') : 1 );
			$posts_per_page = 11;
			$query->set('posts_per_page', $posts_per_page);

			$tax_query = $query->get('tax_query');
			if ( ! is_array($tax_query) ) $tax_query = [];
			$tax_query[] = array(
				'taxonomy' => 'city',
				'field' => 'term_id',
				'terms' => $_COOKIE['selected_city'],
			);

			$query->set('tax_query', $tax_query);
		}
	}

	return $query;
}
add_action('pre_get_posts', 'theme_change_query');

// Изменяем заголовки архивных страниц
function custom_the_archive_title_9842($title)
{
	if (is_post_type_archive('places')) {
		$title = 'Поиск полей';
	}

	if (is_post_type_archive('players')) {
		$title = 'Игроки ищут команду или игру';
	}

	return $title;
}
add_filter('get_the_archive_title', 'custom_the_archive_title_9842');

// AJAX: get places by city (for CF7 autocomplete)
function theme_cf7_get_places_by_city() {
	$city_value = isset($_POST['city']) ? sanitize_text_field(wp_unslash($_POST['city'])) : '';
	if ($city_value === '') {
		wp_send_json_success(array('places' => array()));
	}

	$city_term = null;
	if (ctype_digit($city_value)) {
		$city_term = get_term((int) $city_value, 'city');
	} else {
		$city_term = get_term_by('name', $city_value, 'city');
	}

	if (! $city_term || is_wp_error($city_term)) {
		wp_send_json_success(array('places' => array()));
	}

	$places = get_posts(array(
		'post_type' => 'places',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
		'tax_query' => array(
			array(
				'taxonomy' => 'city',
				'field' => 'term_id',
				'terms' => array((int) $city_term->term_id),
			),
		),
	));

	$place_titles = array();
	if (! empty($places)) {
		foreach ($places as $place) {
			$place_titles[] = $place->post_title;
		}
	}

	wp_send_json_success(array(
		'places' => $place_titles,
	));
}
add_action('wp_ajax_cf7_places_by_city', 'theme_cf7_get_places_by_city');
add_action('wp_ajax_nopriv_cf7_places_by_city', 'theme_cf7_get_places_by_city');

// AJAX: get metro terms by city (based on places in city)
function theme_cf7_get_metro_by_city() {
	$city_value = isset($_POST['city']) ? sanitize_text_field(wp_unslash($_POST['city'])) : '';
	if ($city_value === '') {
		wp_send_json_success(array('metro' => array()));
	}

	$city_term = null;
	if (ctype_digit($city_value)) {
		$city_term = get_term((int) $city_value, 'city');
	} else {
		$city_term = get_term_by('name', $city_value, 'city');
	}

	if (! $city_term || is_wp_error($city_term)) {
		wp_send_json_success(array('metro' => array()));
	}

	$places = get_posts(array(
		'post_type' => 'places',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'fields' => 'ids',
		'tax_query' => array(
			array(
				'taxonomy' => 'city',
				'field' => 'term_id',
				'terms' => array((int) $city_term->term_id),
			),
		),
	));

	if (empty($places)) {
		wp_send_json_success(array('metro' => array()));
	}

	$metro_terms = wp_get_object_terms($places, 'metro', array('fields' => 'names'));
	if (is_wp_error($metro_terms) || empty($metro_terms)) {
		wp_send_json_success(array('metro' => array()));
	}

	$metro_terms = array_values(array_unique($metro_terms));

	wp_send_json_success(array(
		'metro' => $metro_terms,
	));
}
add_action('wp_ajax_cf7_metro_by_city', 'theme_cf7_get_metro_by_city');
add_action('wp_ajax_nopriv_cf7_metro_by_city', 'theme_cf7_get_metro_by_city');
