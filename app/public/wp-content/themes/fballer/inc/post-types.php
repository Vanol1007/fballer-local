<?php

// Защита от прямого доступа к файлу
if ( ! defined('ABSPATH') ) {
	exit;
}

// Регистрируем кастомный тип записей
function register_custom_post_types_9842() {
	// Матчи
	register_post_type( 'games', array(
		'labels' => array(
			'name' => 'Матчи',
			'singular_name' => 'Матч',
		),
		'public' => true,
		// 'has_archive' => false,
		'has_archive' => true,
		// 'publicly_queryable' => false,
		'show_ui' => true,
		'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'rewrite' => array( 'slug' => 'agames' ),
		// 'rewrite' => false,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		// 'exclude_from_search' => true,
		'menu_icon' => 'dashicons-calendar', // Иконка календаря для матчей
	) );

	// Чемпионаты
	register_post_type( 'champs', array(
		'labels' => array(
			'name' => 'Чемпионаты',
			'singular_name' => 'Чемпионат',
		),
		'public' => true,
		'has_archive' => true,
		'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'rewrite' => array( 'slug' => 'champs' ),
		'menu_icon' => 'dashicons-awards', // Иконка наград для чемпионатов
	) );

	// Поля
	register_post_type( 'places', array(
		'labels' => array(
			'name' => 'Поля',
			'singular_name' => 'Поле',
		),
		'public' => true,
		'has_archive' => true,
		'supports' => array( 'title', 'editor', 'thumbnail' ),
		'rewrite' => array( 'slug' => 'places' ),
		'menu_icon' => 'dashicons-location', // Иконка местоположения для полей
	) );
	
	// Команды
	register_post_type( 'teams', array(
		'labels' => array(
			'name' => 'Команды',
			'singular_name' => 'Команда',
		),
		'public' => true,
		'has_archive' => true,
		'show_ui' => true,
		'supports' => array( 'title', 'editor', 'thumbnail' ),
		'rewrite' => array( 'slug' => 'teams' ),
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'menu_icon' => 'dashicons-networking',
	) );

	// Игроки
	register_post_type( 'players', array(
		'labels' => array(
			'name' => 'Игроки',
			'singular_name' => 'Игрок',
		),
		'public' => true,
		'has_archive' => true,
		'show_ui' => true,
		'show_in_rest' => true,
		'rest_base' => 'players',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'rewrite' => array( 'slug' => 'players' ),
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'menu_icon' => 'dashicons-groups',
	) );


	// Новости
	register_post_type( 'news', array(
		'labels' => array(
			'name' => 'Новости',
			'singular_name' => 'Новость',
		),
		'public' => true,
		'has_archive' => true,
		'show_ui' => true,
		'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'rewrite' => array( 'slug' => 'news' ),
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'menu_icon' => 'dashicons-megaphone',
	) );

	// Реклама
	register_post_type( 'ads', array(
		'labels' => array(
			'name' => 'Реклама',
			'singular_name' => 'Баннер',
		),
		'public' => true,
		'has_archive' => false,
		'show_ui' => true,
		'supports' => array( 'title', 'thumbnail' ),
		'rewrite' => array( 'slug' => 'ads' ),
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'menu_icon' => 'dashicons-format-image',
	) );


}
add_action( 'init', 'register_custom_post_types_9842' );

// Регистрируем новые таксономии
function register_custom_taxonomies_9842() {
	// Направление
	register_taxonomy( 'city_direction', array( 'games', 'champs', 'places', 'teams', 'players' ), array(
		'label' => 'Направление',
		'public' => false,
		'hierarchical' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => false,
	) );

	// Административный округ
	register_taxonomy( 'admin_area', array( 'games', 'champs', 'places', 'teams', 'players' ), array(
		'label' => 'Административный округ',
		'public' => false,
		'hierarchical' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => false,
	) );

	// Район
	register_taxonomy( 'district', array( 'games', 'champs', 'places', 'teams', 'players' ), array(
		'label' => 'Район',
		'public' => false,
		'hierarchical' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => false,
	) );

	// Формат игры
	register_taxonomy( 'game_format', array( 'games', 'champs', 'places' ), array(
		'label' => 'Формат игры',
		'public' => false,
		'hierarchical' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => false,
	) );

	// Метро
	register_taxonomy( 'metro', array( 'games', 'champs', 'places', 'teams', 'players' ), array(
		'label' => 'Метро',
		'public' => false,
		'hierarchical' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => false,
	) );
	
	// Покрытие
	register_taxonomy( 'coating', array( 'places' ), array(
		'label' => 'Покрытие',
		'public' => false,
		'hierarchical' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => false,
	) );
	
	// Тип поля
	register_taxonomy( 'field_type', array( 'places' ), array(
		'label' => 'Тип поля',
		'public' => false,
		'hierarchical' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => false,
	) );
	
	// Размер поля
	register_taxonomy( 'field_size', array( 'places' ), array(
		'label' => 'Размер поля',
		'public' => false,
		'hierarchical' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => false,
	) );
	
	// Особенности
	register_taxonomy( 'features', array( 'places' ), array(
		'label' => 'Особенности',
		'public' => false,
		'hierarchical' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => false,
	) );
	
	// Города
	register_taxonomy( 'city', array( 'games', 'champs', 'places', 'teams', 'players', 'news', 'ads' ), array(
		'label' => 'Города',
		'public' => false,
		'hierarchical' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => false,
	) );
	
	// Уровень игры
	register_taxonomy( 'team_level', array( 'teams', 'players' ), array(
		'label' => 'Уровень игры',
		'public' => false,
		'hierarchical' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => false,
	) );
	
	// Позиция на поле
	register_taxonomy( 'team_position', array( 'teams', 'players' ), array(
		'label' => 'Позиция на поле',
		'public' => false,
		'hierarchical' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => false,
	) );
}
add_action( 'init', 'register_custom_taxonomies_9842' );

function fballer_get_geo_taxonomies_9842() {
	return array( 'city_direction', 'admin_area', 'district', 'metro' );
}

function fballer_get_city_terms_for_geo_9842() {
	return get_terms(array(
		'taxonomy' => 'city',
		'hide_empty' => false,
		'parent' => 0,
	));
}

function fballer_normalize_term_meta_value_9842( $value ) {
	if ( is_array( $value ) ) {
		$first = reset( $value );

		if ( is_array( $first ) && isset( $first['id'] ) ) {
			return (int) $first['id'];
		}

		return (int) $first;
	}

	return (int) $value;
}

function fballer_get_geo_city_root_term_id_9842( $taxonomy, $city_term_id ) {
	$city_term_id = (int) $city_term_id;
	if ( $city_term_id <= 0 ) {
		return 0;
	}

	$city = get_term( $city_term_id, 'city' );
	if ( is_wp_error( $city ) || ! $city ) {
		return 0;
	}

	$slug = 'city-' . $city->slug;
	$existing = get_term_by( 'slug', $slug, $taxonomy );
	if ( $existing && ! is_wp_error( $existing ) ) {
		if ( ! get_term_meta( $existing->term_id, '_fballer_geo_city_root', true ) ) {
			update_term_meta( $existing->term_id, '_fballer_geo_city_root', 'yes' );
		}
		update_term_meta( $existing->term_id, 'related_city', $city_term_id );
		return (int) $existing->term_id;
	}

	$inserted = wp_insert_term( $city->name, $taxonomy, array(
		'slug' => $slug,
		'parent' => 0,
	));

	if ( is_wp_error( $inserted ) || empty( $inserted['term_id'] ) ) {
		return 0;
	}

	update_term_meta( $inserted['term_id'], '_fballer_geo_city_root', 'yes' );
	update_term_meta( $inserted['term_id'], 'related_city', $city_term_id );

	return (int) $inserted['term_id'];
}

function fballer_sync_geo_city_roots_9842() {
	$cities = fballer_get_city_terms_for_geo_9842();

	if ( is_wp_error( $cities ) || empty( $cities ) ) {
		return;
	}

	foreach ( fballer_get_geo_taxonomies_9842() as $taxonomy ) {
		foreach ( $cities as $city ) {
			fballer_get_geo_city_root_term_id_9842( $taxonomy, $city->term_id );
		}
	}
}
add_action( 'init', 'fballer_sync_geo_city_roots_9842', 30 );

function fballer_render_geo_term_fields_9842( $taxonomy, $term = null ) {
	$cities = fballer_get_city_terms_for_geo_9842();

	if ( is_wp_error( $cities ) ) {
		$cities = array();
	}

	$term_id = ( $term && ! empty( $term->term_id ) ) ? (int) $term->term_id : 0;
	$selected_city = $term_id ? (int) get_term_meta( $term_id, 'related_city', true ) : 0;
	$selected_direction = $term_id ? (int) get_term_meta( $term_id, 'related_direction', true ) : 0;
	$selected_admin_area = $term_id ? (int) get_term_meta( $term_id, 'related_admin_area', true ) : 0;
	$selected_district = $term_id ? (int) get_term_meta( $term_id, 'related_district', true ) : 0;

	$directions = get_terms(array(
		'taxonomy' => 'city_direction',
		'hide_empty' => false,
	));
	$admin_areas = get_terms(array(
		'taxonomy' => 'admin_area',
		'hide_empty' => false,
	));
	$districts = get_terms(array(
		'taxonomy' => 'district',
		'hide_empty' => false,
	));

	if ( ! is_array( $directions ) ) {
		$directions = array();
	}

	if ( ! is_array( $admin_areas ) ) {
		$admin_areas = array();
	}

	if ( ! is_array( $districts ) ) {
		$districts = array();
	}

	ob_start();
	?>
	<tr class="form-field">
		<th scope="row"><label for="fballer_related_city">Город</label></th>
		<td>
			<select name="fballer_related_city" id="fballer_related_city">
				<option value="">Не выбрано</option>
				<?php foreach ( $cities as $city ) { ?>
					<option value="<?php echo esc_attr( $city->term_id ); ?>" <?php selected( $selected_city, $city->term_id ); ?>>
						<?php echo esc_html( $city->name ); ?>
					</option>
				<?php } ?>
			</select>
		</td>
	</tr>
	<?php if ( in_array( $taxonomy, array( 'admin_area', 'district', 'metro' ), true ) ) { ?>
		<tr class="form-field">
			<th scope="row"><label for="fballer_related_direction">Направление</label></th>
			<td>
				<select name="fballer_related_direction" id="fballer_related_direction">
					<option value="">Не выбрано</option>
					<?php foreach ( $directions as $direction ) {
						if ( get_term_meta( $direction->term_id, '_fballer_geo_city_root', true ) ) {
							continue;
						}
						?>
						<option value="<?php echo esc_attr( $direction->term_id ); ?>" <?php selected( $selected_direction, $direction->term_id ); ?>>
							<?php echo esc_html( $direction->name ); ?>
						</option>
					<?php } ?>
				</select>
			</td>
		</tr>
	<?php } ?>
	<?php if ( in_array( $taxonomy, array( 'district', 'metro' ), true ) ) { ?>
		<tr class="form-field">
			<th scope="row"><label for="fballer_related_admin_area">Административный округ</label></th>
			<td>
				<select name="fballer_related_admin_area" id="fballer_related_admin_area">
					<option value="">Не выбрано</option>
					<?php foreach ( $admin_areas as $admin_area ) {
						if ( get_term_meta( $admin_area->term_id, '_fballer_geo_city_root', true ) ) {
							continue;
						}
						?>
						<option value="<?php echo esc_attr( $admin_area->term_id ); ?>" <?php selected( $selected_admin_area, $admin_area->term_id ); ?>>
							<?php echo esc_html( $admin_area->name ); ?>
						</option>
					<?php } ?>
				</select>
			</td>
		</tr>
	<?php } ?>
	<?php if ( 'metro' === $taxonomy ) { ?>
		<tr class="form-field">
			<th scope="row"><label for="fballer_related_district">Район</label></th>
			<td>
				<select name="fballer_related_district" id="fballer_related_district">
					<option value="">Не выбрано</option>
					<?php foreach ( $districts as $district ) {
						if ( get_term_meta( $district->term_id, '_fballer_geo_city_root', true ) ) {
							continue;
						}
						?>
						<option value="<?php echo esc_attr( $district->term_id ); ?>" <?php selected( $selected_district, $district->term_id ); ?>>
							<?php echo esc_html( $district->name ); ?>
						</option>
					<?php } ?>
				</select>
			</td>
		</tr>
	<?php } ?>
	<?php

	return ob_get_clean();
}

function fballer_render_geo_term_add_fields_9842( $taxonomy ) {
	$cities = fballer_get_city_terms_for_geo_9842();
	$directions = get_terms(array(
		'taxonomy' => 'city_direction',
		'hide_empty' => false,
	));
	$admin_areas = get_terms(array(
		'taxonomy' => 'admin_area',
		'hide_empty' => false,
	));
	$districts = get_terms(array(
		'taxonomy' => 'district',
		'hide_empty' => false,
	));
	?>
	<div class="form-field">
		<label for="fballer_related_city">Город</label>
		<select name="fballer_related_city" id="fballer_related_city">
			<option value="">Не выбрано</option>
			<?php foreach ( $cities as $city ) { ?>
				<option value="<?php echo esc_attr( $city->term_id ); ?>"><?php echo esc_html( $city->name ); ?></option>
			<?php } ?>
		</select>
	</div>
	<?php if ( in_array( $taxonomy, array( 'admin_area', 'district', 'metro' ), true ) ) { ?>
		<div class="form-field">
			<label for="fballer_related_direction">Направление</label>
			<select name="fballer_related_direction" id="fballer_related_direction">
				<option value="">Не выбрано</option>
				<?php foreach ( (array) $directions as $direction ) {
					if ( get_term_meta( $direction->term_id, '_fballer_geo_city_root', true ) ) {
						continue;
					}
					?>
					<option value="<?php echo esc_attr( $direction->term_id ); ?>"><?php echo esc_html( $direction->name ); ?></option>
				<?php } ?>
			</select>
		</div>
	<?php } ?>
	<?php if ( in_array( $taxonomy, array( 'district', 'metro' ), true ) ) { ?>
		<div class="form-field">
			<label for="fballer_related_admin_area">Административный округ</label>
			<select name="fballer_related_admin_area" id="fballer_related_admin_area">
				<option value="">Не выбрано</option>
				<?php foreach ( (array) $admin_areas as $admin_area ) {
					if ( get_term_meta( $admin_area->term_id, '_fballer_geo_city_root', true ) ) {
						continue;
					}
					?>
					<option value="<?php echo esc_attr( $admin_area->term_id ); ?>"><?php echo esc_html( $admin_area->name ); ?></option>
				<?php } ?>
			</select>
		</div>
	<?php } ?>
	<?php if ( 'metro' === $taxonomy ) { ?>
		<div class="form-field">
			<label for="fballer_related_district">Район</label>
			<select name="fballer_related_district" id="fballer_related_district">
				<option value="">Не выбрано</option>
				<?php foreach ( (array) $districts as $district ) {
					if ( get_term_meta( $district->term_id, '_fballer_geo_city_root', true ) ) {
						continue;
					}
					?>
					<option value="<?php echo esc_attr( $district->term_id ); ?>"><?php echo esc_html( $district->name ); ?></option>
				<?php } ?>
			</select>
		</div>
	<?php }
}

function fballer_render_geo_term_edit_fields_9842( $term, $taxonomy ) {
	echo fballer_render_geo_term_fields_9842( $taxonomy, $term ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function fballer_save_geo_term_meta_9842( $term_id, $tt_id = 0, $taxonomy = '' ) {
	if ( ! in_array( $taxonomy, fballer_get_geo_taxonomies_9842(), true ) ) {
		return;
	}

	$city_id = isset( $_POST['fballer_related_city'] ) ? (int) $_POST['fballer_related_city'] : 0;
	$direction_id = isset( $_POST['fballer_related_direction'] ) ? (int) $_POST['fballer_related_direction'] : 0;
	$admin_area_id = isset( $_POST['fballer_related_admin_area'] ) ? (int) $_POST['fballer_related_admin_area'] : 0;
	$district_id = isset( $_POST['fballer_related_district'] ) ? (int) $_POST['fballer_related_district'] : 0;

	update_term_meta( $term_id, 'related_city', $city_id );

	if ( 'city_direction' !== $taxonomy ) {
		update_term_meta( $term_id, 'related_direction', $direction_id );
	}

	if ( ! in_array( $taxonomy, array( 'city_direction', 'admin_area' ), true ) ) {
		update_term_meta( $term_id, 'related_admin_area', $admin_area_id );
	}

	if ( 'metro' === $taxonomy ) {
		update_term_meta( $term_id, 'related_district', $district_id );
	}

	if ( $city_id > 0 ) {
		$city_root_term_id = fballer_get_geo_city_root_term_id_9842( $taxonomy, $city_id );
		if ( $city_root_term_id && (int) $term_id !== (int) $city_root_term_id ) {
			wp_update_term( $term_id, $taxonomy, array( 'parent' => $city_root_term_id ) );
		}
	}
}

function fballer_prepare_geo_term_insert_9842( $term, $taxonomy, $args ) {
	if ( ! in_array( $taxonomy, fballer_get_geo_taxonomies_9842(), true ) ) {
		return $term;
	}

	$city_id = isset( $_POST['fballer_related_city'] ) ? (int) $_POST['fballer_related_city'] : 0;
	if ( $city_id <= 0 ) {
		return $term;
	}

	$city_root_term_id = fballer_get_geo_city_root_term_id_9842( $taxonomy, $city_id );
	if ( $city_root_term_id > 0 ) {
		$_POST['parent'] = $city_root_term_id;
	}

	return $term;
}
add_filter( 'pre_insert_term', 'fballer_prepare_geo_term_insert_9842', 10, 3 );

foreach ( fballer_get_geo_taxonomies_9842() as $fballer_geo_taxonomy ) {
	add_action( $fballer_geo_taxonomy . '_add_form_fields', function() use ( $fballer_geo_taxonomy ) {
		fballer_render_geo_term_add_fields_9842( $fballer_geo_taxonomy );
	} );

	add_action( $fballer_geo_taxonomy . '_edit_form_fields', 'fballer_render_geo_term_edit_fields_9842', 10, 2 );
	add_action( 'created_' . $fballer_geo_taxonomy, 'fballer_save_geo_term_meta_9842', 10, 3 );
	add_action( 'edited_' . $fballer_geo_taxonomy, 'fballer_save_geo_term_meta_9842', 10, 3 );
}

function fballer_register_rest_meta_9842() {
	$meta_map = array(
		'games' => array(
			'btn_text',
			'btn_link',
			'source_message_link',
			'game_date',
			'game_time',
			'game_price',
			'game_custom_place_name',
			'game_custom_place_address',
		),
		'teams' => array(
			'phone',
			'team_btn_text',
			'team_btn_link',
			'source_message_link',
		),
		'players' => array(
			'phone',
			'player_goal',
			'player_btn_text',
			'player_btn_link',
			'source_message_link',
		),
	);

	foreach ( $meta_map as $post_type => $meta_keys ) {
		foreach ( $meta_keys as $meta_key ) {
			register_post_meta(
				$post_type,
				$meta_key,
				array(
					'single' => true,
					'type' => 'string',
					'show_in_rest' => true,
					'auth_callback' => function() {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
}
add_action( 'init', 'fballer_register_rest_meta_9842', 20 );

function fballer_sync_rest_meta_to_carbon_9842( $post, $request, $creating ) {
	if ( ! function_exists( 'carbon_set_post_meta' ) || ! $request instanceof WP_REST_Request ) {
		return;
	}

	$post_type = get_post_type( $post );
	$meta_map = array(
		'games' => array( 'btn_text', 'btn_link', 'source_message_link', 'game_date', 'game_time', 'game_price', 'game_custom_place_name', 'game_custom_place_address' ),
		'teams' => array( 'phone', 'team_btn_text', 'team_btn_link', 'source_message_link' ),
		'players' => array( 'phone', 'player_goal', 'player_btn_text', 'player_btn_link', 'source_message_link' ),
	);

	if ( ! isset( $meta_map[ $post_type ] ) ) {
		return;
	}

	$meta = $request->get_param( 'meta' );
	if ( ! is_array( $meta ) ) {
		$meta = array();
	}

	foreach ( $meta_map[ $post_type ] as $meta_key ) {
		if ( ! array_key_exists( $meta_key, $meta ) ) {
			continue;
		}

		carbon_set_post_meta( $post->ID, $meta_key, $meta[ $meta_key ] );
	}

	if ( $post_type === 'games' ) {
		$game_places = null;
		if ( $request->has_param( 'game_places' ) ) {
			$game_places = $request->get_param( 'game_places' );
		} elseif ( is_array( $meta ) && array_key_exists( 'game_places', $meta ) ) {
			$game_places = $meta['game_places'];
		}

		if ( is_array( $game_places ) ) {
			$game_place_ids = array();
			foreach ( $game_places as $place_item ) {
				$place_id = is_array( $place_item ) && isset( $place_item['id'] ) ? (int) $place_item['id'] : (int) $place_item;
				if ( $place_id <= 0 || get_post_type( $place_id ) !== 'places' ) {
					continue;
				}
				$game_place_ids[] = $place_id;
			}

			$prepared_game_places = function_exists( 'fballer_prepare_association_meta' )
				? fballer_prepare_association_meta( $game_place_ids, 'places' )
				: array_map(
					function( $place_id ) {
						return array(
							'id' => $place_id,
							'type' => 'post',
							'subtype' => 'places',
						);
					},
					$game_place_ids
				);

			if ( ! empty( $prepared_game_places ) ) {
				carbon_set_post_meta( $post->ID, 'game_places', $prepared_game_places );
			}
		}
	}
}
add_action( 'rest_after_insert_games', 'fballer_sync_rest_meta_to_carbon_9842', 10, 3 );
add_action( 'rest_after_insert_teams', 'fballer_sync_rest_meta_to_carbon_9842', 10, 3 );
add_action( 'rest_after_insert_players', 'fballer_sync_rest_meta_to_carbon_9842', 10, 3 );
