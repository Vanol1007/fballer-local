<?php

// Защита от прямого доступа к файлу
if (! defined('ABSPATH')) {
	exit;
}

if (! defined('Carbon_Fields_Plugin\PLUGIN_FILE')) {
	return;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Widget;

function fballer_geo_coordinate_fields_9842($label_prefix = '')
{
	$lat_label = $label_prefix ? $label_prefix . ' широта' : 'Широта';
	$lng_label = $label_prefix ? $label_prefix . ' долгота' : 'Долгота';

	return array(
		Field::make('text', 'geo_lat', $lat_label)
			->set_attribute('type', 'number')
			->set_attribute('step', 'any')
			->set_width(50)
			->set_help_text('Например: 55.7558'),
		Field::make('text', 'geo_lng', $lng_label)
			->set_attribute('type', 'number')
			->set_attribute('step', 'any')
			->set_width(50)
			->set_help_text('Например: 37.6176'),
	);
}

// Подлючение Carbon Fields
function theme_hook_for_carbon_fields_register()
{
	add_action('admin_enqueue_scripts', function () {
		// wp_enqueue_script( 'flatpickr-locale-ru', '//npmcdn.com/flatpickr/dist/l10n/ru.js', array('carbon-fields-boot') );
		wp_enqueue_script('flatpickr-locale-ru', '//npmcdn.com/flatpickr/dist/l10n/ru.js', array('carbon-fields-core'));
	});

	// Матч
	Container::make('post_meta', __('Параметры матча'))
		->where('post_type', '=', 'games') // https://docs.carbonfields.net/#/containers/condition-types
		// ->where( 'post_template', '=', 'templates/template-games.php' ) // Шаблон страницы
		->add_fields(array_merge(array(
			Field::make('date', 'game_date', __('Дата'))
				->set_picker_options(array(
					'locale' => 'ru',
					'minDate' => 'today', // Запрещает выбор прошедших дат
					// 'weekNumbers' => true,
					'allowInput' => false, // Запрещает ручной ввод, предотвращает очистку
				))
				->set_storage_format('U') // Сохраняет дату в формате UNIX timestamp
				->set_width(50),
			Field::make('time', 'game_time', __('Время'))
				->set_input_format('H:i', 'H:i')
				->set_storage_format('Hi')
				->set_picker_options(array(
					'time_24hr' => true,
					'enableSeconds' => false,
					'minTime' => '08:00',
					'maxTime' => '23:00',
					'altFormat' => 'H:i',
				))
				->set_width(50),
			Field::make('text', 'game_price', __('Цена'))
				->set_help_text('Стоимость в рублях')
				->set_attribute('type', 'number'),
			Field::make('text', 'btn_text', __('Текст в кнопке "написать'))
				->set_help_text('Введите текст в кнопке "написать"')
				->set_attribute('type', 'text'),
			Field::make('text', 'btn_link', __('Ссылка в кнопке "написать"'))
				->set_help_text('Вставьте ссылку для кнопки "написать"')
				->set_attribute('type', 'text'),
			Field::make('association', 'game_places', __('Поля'))
				->set_types([
					[
						'type' => 'post',
						'post_type' => 'places',
					],
				])
				->set_max(1), // Максимум 5 связей
			Field::make('text', 'game_custom_place_name', __('Свое поле: название'))
				->set_help_text('Заполняется из фронтовой формы, если поля нет в списке')
				->set_attribute('type', 'text'),
			Field::make('text', 'game_custom_place_address', __('Свое поле: адрес'))
				->set_help_text('Заполняется из фронтовой формы, если поля нет в списке')
				->set_attribute('type', 'text'),
		), fballer_geo_coordinate_fields_9842()));
		
	// Команда
	Container::make('post_meta', __('Параметры команды'))
		->where('post_type', '=', 'teams')
		->add_fields(array_merge(array(
			Field::make('text', 'phone', __('Телефон'))
				->set_help_text('Контактный телефон')
				->set_attribute('type', 'text'),
			Field::make('text', 'team_btn_text', __('Текст в кнопке "написать"'))
				->set_help_text('Введите текст в кнопке "написать"')
				->set_attribute('type', 'text'),
			Field::make('text', 'team_btn_link', __('Ссылка в кнопке "написать"'))
				->set_help_text('Вставьте ссылку для кнопки "написать"')
				->set_attribute('type', 'text'),
		), fballer_geo_coordinate_fields_9842()));

	// Игрок
	Container::make('post_meta', __('Параметры игрока'))
		->where('post_type', '=', 'players')
		->add_fields(array_merge(array(
			Field::make('select', 'player_goal', __('Что ищет игрок'))
				->add_options(array(
					'team' => 'Команду',
					'game' => 'Игру',
				)),
			Field::make('text', 'phone', __('Телефон'))
				->set_help_text('Контактный телефон')
				->set_attribute('type', 'text'),
			Field::make('text', 'player_btn_text', __('Текст в кнопке "написать"'))
				->set_help_text('Введите текст в кнопке "написать"')
				->set_attribute('type', 'text'),
			Field::make('text', 'player_btn_link', __('Ссылка в кнопке "написать"'))
				->set_help_text('Вставьте ссылку для кнопки "написать"')
				->set_attribute('type', 'text'),
		), fballer_geo_coordinate_fields_9842()));

	// Поле
	Container::make('post_meta', __('Параметры поля'))
		->where('post_type', '=', 'places') // https://docs.carbonfields.net/#/containers/condition-types
		->add_fields(array_merge(array(
			Field::make('text', 'place_address', __('Адрес')),
			Field::make('rich_text', 'place_info', __('Инфо')),
			Field::make('text', 'place_price', __('Цена')),
			Field::make('media_gallery', 'place_gallery', __('Галерея'))
				->set_type(array('image'))
				->set_duplicates_allowed(true),
			Field::make('text', 'phone', __('Телефон')),
		), fballer_geo_coordinate_fields_9842()));

	// Чемпионат
	Container::make('post_meta', __('Параметры чемпионата'))
		->where('post_type', '=', 'champs') // https://docs.carbonfields.net/#/containers/condition-types
		// ->where( 'post_template', '=', 'templates/template-games.php' ) // Шаблон страницы
		->add_fields(array_merge(array(
			Field::make('date', 'champ_start', __('Старт'))
				->set_picker_options(array(
					'locale' => 'ru',
					//'minDate' => 'today', // Запрещает выбор прошедших дат
					// 'weekNumbers' => true,
					'allowInput' => false, // Запрещает ручной ввод, предотвращает очистку
				))
				->set_storage_format('U') // Сохраняет дату в формате UNIX timestamp
				->set_width(50),
			Field::make('date', 'champ_end', __('Окончание'))
				->set_picker_options(array(
					'locale' => 'ru',
					'minDate' => 'today', // Запрещает выбор прошедших дат
					// 'weekNumbers' => true,
					'allowInput' => false, // Запрещает ручной ввод, предотвращает очистку
				))
				->set_storage_format('U') // Сохраняет дату в формате UNIX timestamp
				->set_width(50),
				
			Field::make('text', 'champ_address', __('Адрес')),
			Field::make('text', 'champ_reward', __('Приз')),
			// Field::make('text', 'champ_price', __('Цена')),
			Field::make('text', 'champ_price', 'Цена')
    ->set_attribute('type', 'number')
    ->set_attribute('min', 0)
    ->set_attribute('step', 100)
    ->set_help_text('Введите цену в рублях, только цифры'),
		Field::make('text', 'champ_website', __('Сайт')),

			Field::make('media_gallery', 'champ_gallery', __('Галерея'))
				->set_type(array('image'))
				->set_duplicates_allowed(true),
			Field::make('text', 'phone', __('Телефон')),
			Field::make('association', 'champ_places', __('Поля'))
				->set_types(array(
					array(
						'type' => 'post',
						'post_type' => 'places',
					)
				))
				->set_max(10),
			Field::make('complex', 'champ_links', __('Ссылки'))
				->setup_labels(array(
					'plural_name' => 'ссылки',
					'singular_name' => 'ссылку',
				))
				->set_layout('tabbed-horizontal') // grid (default), tabbed-horizontal, tabbed-vertical
				->add_fields(array(
					Field::make('text', 'title', __('Название'))
						->set_width(50),
					Field::make('text', 'url', __('Адрес ссылки'))
						->set_width(50),
				))
				->set_header_template('№<%- $_index + 1 %> <%- title ? " - " + title : "" %>'),
		), fballer_geo_coordinate_fields_9842()));


	// Новости
	Container::make('post_meta', __('Параметры новости'))
		->where('post_type', '=', 'news')
		->add_fields(array(
			Field::make('checkbox', 'news_btn_enabled', __('Показывать кнопку'))
				->set_option_value('yes')
				->set_default_value('yes'),
			Field::make('text', 'news_btn_text', __('Текст кнопки'))
				->set_default_value('Читать новость'),
			Field::make('text', 'news_btn_link', __('Ссылка кнопки'))
		));

	// Реклама
	Container::make('post_meta', __('Параметры рекламы'))
		->where('post_type', '=', 'ads')
		->add_fields(array(
			Field::make('image', 'ad_image_desktop', __('Баннер (desktop 1280x240)'))
				->set_value_type('id'),
			Field::make('image', 'ad_image_tablet', __('Баннер (tablet)'))
				->set_value_type('id'),
			Field::make('image', 'ad_image_mobile', __('Баннер (mobile)'))
				->set_value_type('id'),
			Field::make('text', 'ad_link', __('Ссылка'))
		));

	// Таксономия features
	Container::make('term_meta', __('Параметры'))
		->where('term_taxonomy', '=', 'field_type')
		->or_where('term_taxonomy', '=', 'features')
		->add_fields(array(
			Field::make('image', 'image', __('Изображение'))
				->set_value_type('url')
		));
		
	// Таксономия city
	Container::make( 'term_meta', __('Параметры') )
		->where( 'term_taxonomy', '=', 'city' )
		->where('term', 'CUSTOM', function($term_id) {
			if ( ! $term_id ) return false;
			
			$term = get_term($term_id);
			if ( is_wp_error($term) || ! $term ) return false;
			
			$level = count(get_ancestors($term_id, $term->taxonomy, 'taxonomy')) + 1;
			return $level === 1;
		})
		->add_fields( array(
			Field::make('text', 'city_name', 'Название вложенных рубрик'),
			Field::make('select', 'city_timezone', 'Часовой пояс города')
				->add_options([
					'Europe/Kaliningrad'   => 'Калининград (UTC+2)',
					'Europe/Moscow'        => 'Москва / Санкт-Петербург (UTC+3)',
					'Europe/Samara'        => 'Самара (UTC+4)',
					'Asia/Yekaterinburg'   => 'Екатеринбург (UTC+5)',
					'Asia/Omsk'            => 'Омск (UTC+6)',
					'Asia/Krasnoyarsk'     => 'Красноярск (UTC+7)',
					'Asia/Irkutsk'         => 'Иркутск (UTC+8)',
					'Asia/Yakutsk'         => 'Якутск (UTC+9)',
					'Asia/Vladivostok'     => 'Владивосток (UTC+10)',
					'Asia/Sakhalin'        => 'Сахалин (UTC+11)',
					'Asia/Kamchatka'       => 'Камчатка (UTC+12)',
				])
				->set_default_value('Europe/Moscow')
				->set_help_text('Используется для скрытия уже прошедших игр по местному времени города.'),
			Field::make('checkbox', 'has_admin_areas', 'Есть административные округа')
				->set_option_value('yes')
				->set_help_text('Нужно включать только для городов вроде Москвы.'),
			Field::make('checkbox', 'has_directions', 'Есть направления')
				->set_option_value('yes')
				->set_help_text('Например: север, юг, запад, юго-восток.'),
			Field::make('checkbox', 'has_districts', 'Есть районы')
				->set_option_value('yes')
				->set_default_value('yes'),
			Field::make('checkbox', 'has_metro', 'Есть метро')
				->set_option_value('yes')
		));

	Container::make('term_meta', __('Координаты'))
		->where('term_taxonomy', '=', 'city')
		->where('term', 'CUSTOM', function($term_id) {
			if ( ! $term_id ) return false;

			$term = get_term($term_id);
			if ( is_wp_error($term) || ! $term ) return false;

			$level = count(get_ancestors($term_id, $term->taxonomy, 'taxonomy')) + 1;
			return $level === 1;
		})
		->add_fields(fballer_geo_coordinate_fields_9842('Центр города'));

	Container::make('term_meta', __('Координаты'))
		->where('term_taxonomy', '=', 'city_direction')
		->or_where('term_taxonomy', '=', 'admin_area')
		->or_where('term_taxonomy', '=', 'district')
		->or_where('term_taxonomy', '=', 'metro')
		->add_fields(fballer_geo_coordinate_fields_9842());

}
add_action('carbon_fields_register_fields', 'theme_hook_for_carbon_fields_register');

// Фикс для локализации
function modify_carbon_fields_config($config)
{
	$domain = 'carbon-fields-ui';
	$translations = get_translations_for_domain($domain);
	if ($translations->entries) {
		foreach ($translations->entries as $msgid => $entry) {
			$config['config']['locale'][$entry->singular] = $entry->translations;
		}
	}

	return $config;
}
add_filter('carbon_fields_config', 'modify_carbon_fields_config', 10, 1);

// Разрешаем SVG
function allow_svg_upload_via_safe_svg_9842($mimes)
{
	if (class_exists('SafeSvg\safe_svg')) {
		$mimes['svg'] = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
	}

	return $mimes;
}
add_filter('upload_mimes', 'allow_svg_upload_via_safe_svg_9842');
