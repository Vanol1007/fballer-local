<?php
/*
Template Name: Матчи
*/

// Подключаем CSS и JS
// wp_enqueue_script( 'theme-search', get_template_directory_uri() . '/assets/js/search.js', array(), filemtime(get_template_directory() . '/assets/js/search.js'), true );

$ajaxVariable = 'ajaxGamesData';
$selected_city = fballer_get_selected_city_id();

$get_city_flag = static function( $city_id, $key ) {
	$value = '';

	if ( function_exists('carbon_get_term_meta') ) {
		$value = (string) carbon_get_term_meta( $city_id, $key );
	}

	if ( '' === $value ) {
		$value = (string) get_term_meta( $city_id, $key, true );
	}

	return in_array( $value, array( 'yes', '1' ), true );
};

$city_term = $selected_city ? get_term($selected_city, 'city') : null;
$city_has_directions = $selected_city ? $get_city_flag( $selected_city, 'has_directions' ) : false;
$city_has_admin_areas = $selected_city ? $get_city_flag( $selected_city, 'has_admin_areas' ) : false;
$city_has_districts = $selected_city ? $get_city_flag( $selected_city, 'has_districts' ) : false;
$city_has_metro = $selected_city ? $get_city_flag( $selected_city, 'has_metro' ) : false;

$game_formats = get_terms(array(
	'taxonomy' => 'game_format',
	'hide_empty' => false,
));

if ( ! is_wp_error($game_formats) && ! empty($game_formats) ) {
	$game_formats = array_values(array_filter($game_formats, static function($term) {
		$name = isset($term->name) ? (string) $term->name : '';
		if ( ! preg_match('/^(\d+)/', $name, $matches) ) {
			return false;
		}

		return ((int) $matches[1]) <= 11;
	}));

	usort($game_formats, static function($a, $b) {
		preg_match('/^(\d+)/', (string) $a->name, $a_matches);
		preg_match('/^(\d+)/', (string) $b->name, $b_matches);

		$a_number = isset($a_matches[1]) ? (int) $a_matches[1] : 999;
		$b_number = isset($b_matches[1]) ? (int) $b_matches[1] : 999;

		if ( $a_number === $b_number ) {
			return strnatcasecmp((string) $a->name, (string) $b->name);
		}

		return $a_number <=> $b_number;
	});
} else {
	$game_formats = array();
}

$direction_terms = get_terms(array(
	'taxonomy' => 'city_direction',
	'hide_empty' => false,
));
$admin_area_terms = get_terms(array(
	'taxonomy' => 'admin_area',
	'hide_empty' => false,
));
$district_terms = get_terms(array(
	'taxonomy' => 'district',
	'hide_empty' => false,
));
$metro_terms = get_terms(array(
	'taxonomy' => 'metro',
	'hide_empty' => false,
));

$filter_terms = array(
	'city_direction' => is_array($direction_terms) ? $direction_terms : array(),
	'admin_area' => is_array($admin_area_terms) ? $admin_area_terms : array(),
	'district' => is_array($district_terms) ? $district_terms : array(),
	'metro' => is_array($metro_terms) ? $metro_terms : array(),
);

$filter_geo_term_by_city = static function( $terms, $city_id ) {
	if ( ! $city_id || ! is_array($terms) ) {
		return array();
	}

	return array_values(array_filter($terms, static function( $term ) use ( $city_id ) {
		if ( 'yes' === get_term_meta($term->term_id, '_fballer_geo_city_root', true) ) {
			return false;
		}

		return (int) get_term_meta($term->term_id, 'related_city', true) === (int) $city_id;
	}));
};

foreach ( array('city_direction', 'admin_area', 'district', 'metro') as $taxonomy ) {
	$filter_terms[ $taxonomy ] = $filter_geo_term_by_city( $filter_terms[ $taxonomy ], $selected_city );
}
?>

<?php get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<section class="section place-detail">
	<div class="place-detail__bg">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/img/cart-bg.png" alt="img">
	</div>
	<div class="container">
		<div class="search-inner">
			<div class="container ajax-posts" ajax-data="<?php echo $ajaxVariable; ?>">
				<div class="section-title"><?php the_title(); ?></div>
				
				<div class="search-filter__row search-filter__row--games">
					<!-- <button class="apply-filter" data-target="filter-2">
						<svg width="13" height="14" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.05664 1.55704C3.84115 1.55704 3.63449 1.63896 3.48212 1.78478C3.32974 1.9306 3.24414 2.12838 3.24414 2.3346C3.24414 2.54083 3.32974 2.7386 3.48212 2.88443C3.63449 3.03025 3.84115 3.11217 4.05664 3.11217C4.27213 3.11217 4.47879 3.03025 4.63116 2.88443C4.78354 2.7386 4.86914 2.54083 4.86914 2.3346C4.86914 2.12838 4.78354 1.9306 4.63116 1.78478C4.47879 1.63896 4.27213 1.55704 4.05664 1.55704ZM1.75727 1.55704C1.92513 1.10175 2.23643 0.707495 2.64825 0.428625C3.06008 0.149756 3.55215 0 4.05664 0C4.56113 0 5.0532 0.149756 5.46503 0.428625C5.87685 0.707495 6.18815 1.10175 6.35602 1.55704H12.1816C12.3971 1.55704 12.6038 1.63896 12.7562 1.78478C12.9085 1.9306 12.9941 2.12838 12.9941 2.3346C12.9941 2.54083 12.9085 2.7386 12.7562 2.88443C12.6038 3.03025 12.3971 3.11217 12.1816 3.11217H6.35602C6.18815 3.56746 5.87685 3.96171 5.46503 4.24058C5.0532 4.51945 4.56113 4.66921 4.05664 4.66921C3.55215 4.66921 3.06008 4.51945 2.64825 4.24058C2.23643 3.96171 1.92513 3.56746 1.75727 3.11217H0.806641C0.591152 3.11217 0.38449 3.03025 0.232117 2.88443C0.0797432 2.7386 -0.00585938 2.54083 -0.00585938 2.3346C-0.00585938 2.12838 0.0797432 1.9306 0.232117 1.78478C0.38449 1.63896 0.591152 1.55704 0.806641 1.55704H1.75727ZM8.93164 6.22243C8.71615 6.22243 8.50949 6.30436 8.35712 6.45018C8.20474 6.596 8.11914 6.79378 8.11914 7C8.11914 7.20622 8.20474 7.404 8.35712 7.54982C8.50949 7.69564 8.71615 7.77757 8.93164 7.77757C9.14713 7.77757 9.35379 7.69564 9.50616 7.54982C9.65854 7.404 9.74414 7.20622 9.74414 7C9.74414 6.79378 9.65854 6.596 9.50616 6.45018C9.35379 6.30436 9.14713 6.22243 8.93164 6.22243ZM6.63227 6.22243C6.80013 5.76714 7.11143 5.37289 7.52325 5.09402C7.93508 4.81515 8.42715 4.6654 8.93164 4.6654C9.43613 4.6654 9.9282 4.81515 10.34 5.09402C10.7519 5.37289 11.0632 5.76714 11.231 6.22243H12.1816C12.3971 6.22243 12.6038 6.30436 12.7562 6.45018C12.9085 6.596 12.9941 6.79378 12.9941 7C12.9941 7.20622 12.9085 7.404 12.7562 7.54982C12.6038 7.69564 12.3971 7.77757 12.1816 7.77757H11.231C11.0632 8.23286 10.7519 8.62711 10.34 8.90598C9.9282 9.18485 9.43613 9.3346 8.93164 9.3346C8.42715 9.3346 7.93508 9.18485 7.52325 8.90598C7.11143 8.62711 6.80013 8.23286 6.63227 7.77757H0.806641C0.591152 7.77757 0.38449 7.69564 0.232117 7.54982C0.0797432 7.404 -0.00585938 7.20622 -0.00585938 7C-0.00585938 6.79378 0.0797432 6.596 0.232117 6.45018C0.38449 6.30436 0.591152 6.22243 0.806641 6.22243H6.63227ZM4.05664 10.8878C3.84115 10.8878 3.63449 10.9698 3.48212 11.1156C3.32974 11.2614 3.24414 11.4592 3.24414 11.6654C3.24414 11.8716 3.32974 12.0694 3.48212 12.2152C3.63449 12.361 3.84115 12.443 4.05664 12.443C4.27213 12.443 4.47879 12.361 4.63116 12.2152C4.78354 12.0694 4.86914 11.8716 4.86914 11.6654C4.86914 11.4592 4.78354 11.2614 4.63116 11.1156C4.47879 10.9698 4.27213 10.8878 4.05664 10.8878ZM1.75727 10.8878C1.92513 10.4325 2.23643 10.0383 2.64825 9.75942C3.06008 9.48055 3.55215 9.33079 4.05664 9.33079C4.56113 9.33079 5.0532 9.48055 5.46503 9.75942C5.87685 10.0383 6.18815 10.4325 6.35602 10.8878H12.1816C12.3971 10.8878 12.6038 10.9698 12.7562 11.1156C12.9085 11.2614 12.9941 11.4592 12.9941 11.6654C12.9941 11.8716 12.9085 12.0694 12.7562 12.2152C12.6038 12.361 12.3971 12.443 12.1816 12.443H6.35602C6.18815 12.8983 5.87685 13.2925 5.46503 13.5714C5.0532 13.8502 4.56113 14 4.05664 14C3.55215 14 3.06008 13.8502 2.64825 13.5714C2.23643 13.2925 1.92513 12.8983 1.75727 12.443H0.806641C0.591152 12.443 0.38449 12.361 0.232117 12.2152C0.0797432 12.0694 -0.00585938 11.8716 -0.00585938 11.6654C-0.00585938 11.4592 0.0797432 11.2614 0.232117 11.1156C0.38449 10.9698 0.591152 10.8878 0.806641 10.8878H1.75727Z" fill="white" /></svg>
						<span>Фильтр</span>
					</button> -->
					<form class="filter-chiose__items active games-filter-form" id="filter-2">
						<?php
						$geo_config = array(
							'city_direction' => array(
								'label' => 'Направление',
								'visible' => $city_has_directions,
							),
							'admin_area' => array(
								'label' => 'Административный округ',
								'visible' => $city_has_admin_areas,
							),
							'district' => array(
								'label' => 'Район',
								'visible' => $city_has_districts,
							),
							'metro' => array(
								'label' => 'Метро',
								'visible' => $city_has_metro,
							),
						);

						$visible_geo_filters_count = 0;
						foreach ( $geo_config as $taxonomy => $config ) {
							if ( $config['visible'] && ! empty($filter_terms[ $taxonomy ]) ) {
								$visible_geo_filters_count++;
							}
						}

						$desktop_filter_columns = 3 + $visible_geo_filters_count;
						?>
						<button type="button" class="games-filter-mobile-toggle" data-games-filter-toggle aria-expanded="false">Фильтровать</button>
						<div class="games-filter-mobile-panel" data-games-filter-panel>
						<div class="input-row games-filter-form__row" style="--games-filter-columns: <?php echo (int) $desktop_filter_columns; ?>;">
							<div class="input-wrap">
								<label for="date">Дата</label>
								<div class="games-datepicker" data-datepicker>
									<input type="hidden" id="date" name="date">
									<button type="button" class="games-datepicker__trigger" data-datepicker-trigger aria-haspopup="dialog" aria-expanded="false">
										<span class="games-datepicker__value" data-datepicker-value>дд.мм.гггг</span>
									</button>
									<div class="games-datepicker__panel" data-datepicker-panel>
										<div class="games-datepicker__header">
											<button type="button" class="games-datepicker__nav" data-datepicker-prev aria-label="Предыдущий месяц">&lt;</button>
											<div class="games-datepicker__title" data-datepicker-title></div>
											<button type="button" class="games-datepicker__nav" data-datepicker-next aria-label="Следующий месяц">&gt;</button>
										</div>
										<div class="games-datepicker__weekdays">
											<span>Пн</span>
											<span>Вт</span>
											<span>Ср</span>
											<span>Чт</span>
											<span>Пт</span>
											<span>Сб</span>
											<span>Вс</span>
										</div>
										<div class="games-datepicker__days" data-datepicker-days></div>
										<div class="games-datepicker__footer">
											<button type="button" class="games-datepicker__action" data-datepicker-clear>Сбросить</button>
											<button type="button" class="games-datepicker__action games-datepicker__action--primary" data-datepicker-today>Сегодня</button>
										</div>
									</div>
								</div>
							</div>

							<div class="input-wrap">
								<label>Формат игры</label>
								<div class="multi-select search-multi-select" data-placeholder="Все">
									<button type="button" class="multi-select__btn" aria-haspopup="listbox" aria-expanded="false">Все</button>
									<div class="multi-select__panel" role="listbox">
										<?php foreach ( $game_formats as $term ) { ?>
											<label class="multi-select__item">
												<input type="checkbox" name="game_format[]" value="<?php echo (int) $term->term_id; ?>">
												<span><?php echo esc_html($term->name); ?></span>
											</label>
										<?php } ?>
									</div>
								</div>
							</div>

							<div class="input-wrap">
								<label>Стоимость игры</label>
								<div class="multi-select search-multi-select" data-placeholder="Все">
									<button type="button" class="multi-select__btn" aria-haspopup="listbox" aria-expanded="false">Все</button>
									<div class="multi-select__panel" role="listbox">
										<label class="multi-select__item">
											<input type="checkbox" name="game_price_bucket[]" value="free">
											<span>Бесплатно</span>
										</label>
										<label class="multi-select__item">
											<input type="checkbox" name="game_price_bucket[]" value="500">
											<span>До 500 рублей</span>
										</label>
										<label class="multi-select__item">
											<input type="checkbox" name="game_price_bucket[]" value="1000">
											<span>До 1000 рублей</span>
										</label>
										<label class="multi-select__item">
											<input type="checkbox" name="game_price_bucket[]" value="1000_plus">
											<span>Более 1000 рублей</span>
										</label>
									</div>
								</div>
							</div>

							<?php foreach ( $geo_config as $taxonomy => $config ) { ?>
								<?php if ( ! $config['visible'] || empty($filter_terms[ $taxonomy ]) ) { continue; } ?>
								<div class="input-wrap" data-geo-filter="<?php echo esc_attr($taxonomy); ?>">
									<label><?php echo esc_html($config['label']); ?></label>
									<div class="multi-select search-multi-select" data-placeholder="Все">
										<button type="button" class="multi-select__btn" aria-haspopup="listbox" aria-expanded="false">Все</button>
										<div class="multi-select__panel" role="listbox">
											<?php foreach ( $filter_terms[ $taxonomy ] as $term ) {
												$related_city = (int) get_term_meta($term->term_id, 'related_city', true);
												$related_direction = (int) get_term_meta($term->term_id, 'related_direction', true);
												$related_admin_area = (int) get_term_meta($term->term_id, 'related_admin_area', true);
												$related_district = (int) get_term_meta($term->term_id, 'related_district', true);
												?>
												<label
													class="multi-select__item"
													data-city="<?php echo esc_attr($related_city); ?>"
													data-direction="<?php echo esc_attr($related_direction); ?>"
													data-admin-area="<?php echo esc_attr($related_admin_area); ?>"
													data-district="<?php echo esc_attr($related_district); ?>"
												>
													<input type="checkbox" name="<?php echo esc_attr($taxonomy); ?>[]" value="<?php echo (int) $term->term_id; ?>">
													<span><?php echo esc_html($term->name); ?></span>
												</label>
											<?php } ?>
										</div>
									</div>
								</div>
							<?php } ?>

							<div class="input-col">
								<button class="primary-btn filter-btn">применить</button>
							</div>
						</div>
						</div>
					</form>
				</div>
				
				<?php
				// Пагинация
				$paged = ( get_query_var('paged') ? get_query_var('paged') : 1 );
				$posts_per_page = ( $paged == 1 ? 11 : 8 );
				$query_args = fballer_apply_upcoming_games_query_args(array(
					'post_type' => 'games',
					'posts_per_page' => $posts_per_page,
					'paged' => $paged,
					'orderby' => [
						'meta_value_num' => 'ASC',
						'post_date' => 'DESC',
					],
					'tax_query' => array(
						array(
							'taxonomy' => 'city',
							'field' => 'term_id',
							'terms' => $selected_city,
						),
					),
				), $selected_city);

				$query = new WP_Query($query_args);

				
				wp_localize_script('ajax-posts', $ajaxVariable, array(
					'ajaxurl' => admin_url('admin-ajax.php'),
					'query_vars' => json_encode($query->query_vars),
					'current_page' => max( 1, get_query_var('paged') ),
					'max_pages' => $query->max_num_pages,
					// 'posts_per_page' => get_option('posts_per_page'),
				));
				?>

				<div class="player-items search-player__items ajax-posts__items games-search-items">
					<a href="/add-game/" class="player-item">
						<div class="add-wrapper">
							<svg width="82" height="82" viewBox="0 0 82 82" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="41" cy="41" r="41" fill="#D9D9D9" /><path d="M39.644 49.4981V31.1021H43.172V49.4981H39.644ZM32 41.9801V38.6621H50.816V41.9801H32Z" fill="white" /></svg>
							<div class="add-text">Добавить свою игру</div>
						</div>
					</a>
					
					<?php if ( $query->have_posts() ) { ?>
						<?php
						while ( $query->have_posts() ) {
							$query->the_post();
							get_template_part('templates/content-games-item');
						}
						wp_reset_postdata();
						?>
					<?php } ?>

				</div>
				
				<?php if ( $query->max_num_pages > 1 ) { ?>
				
					<a href="javascript:void(0)" id="load-more" class="primary-btn load-more ajax-posts__btn">Показать еще</a>
				
				<?php } ?>

				<?php if ( get_the_content() ) { ?>
				
					<div class="page-content single-news__content-inner search-results__seo-text">
						<?php the_content(); ?>
					</div>
					
				<?php } ?>
			</div>
		</div>
	</div>
</section>

<?php endwhile; ?>     

<script>
document.addEventListener('DOMContentLoaded', function () {
  var root = document.querySelector('.games-filter-form');
  if (!root) return;

  var cityId = <?php echo (int) $selected_city; ?>;
  var dateInput = root.querySelector('#date');
  var datepicker = root.querySelector('[data-datepicker]');
  var datepickerTrigger = root.querySelector('[data-datepicker-trigger]');
  var datepickerPanel = root.querySelector('[data-datepicker-panel]');
  var datepickerValue = root.querySelector('[data-datepicker-value]');
  var datepickerTitle = root.querySelector('[data-datepicker-title]');
  var datepickerDays = root.querySelector('[data-datepicker-days]');
  var datepickerPrev = root.querySelector('[data-datepicker-prev]');
  var datepickerNext = root.querySelector('[data-datepicker-next]');
  var datepickerToday = root.querySelector('[data-datepicker-today]');
  var datepickerClear = root.querySelector('[data-datepicker-clear]');
  var mobileToggle = root.querySelector('[data-games-filter-toggle]');
  var mobilePanel = root.querySelector('[data-games-filter-panel]');
  var selectWrappers = root.querySelectorAll('.search-multi-select');
  var currentCalendarDate = new Date();
  var weekdaysStartOffset = 1;
  var monthNames = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

  function pad(value) {
    return String(value).padStart(2, '0');
  }

  function formatDateValue(date) {
    return [date.getFullYear(), pad(date.getMonth() + 1), pad(date.getDate())].join('-');
  }

  function formatDateLabel(value) {
    if (!value) {
      return 'дд.мм.гггг';
    }

    var parts = value.split('-');
    if (parts.length !== 3) {
      return 'дд.мм.гггг';
    }

    return [parts[2], parts[1], parts[0]].join('.');
  }

  function parseDateValue(value) {
    if (!value) {
      return null;
    }

    var parts = value.split('-').map(function (part) { return parseInt(part, 10) || 0; });
    if (parts.length !== 3 || !parts[0] || !parts[1] || !parts[2]) {
      return null;
    }

    return new Date(parts[0], parts[1] - 1, parts[2]);
  }

  function isSameDate(a, b) {
    return a && b &&
      a.getFullYear() === b.getFullYear() &&
      a.getMonth() === b.getMonth() &&
      a.getDate() === b.getDate();
  }

  function setDateValue(value) {
    if (!dateInput || !datepickerValue) return;
    dateInput.value = value || '';
    datepickerValue.textContent = formatDateLabel(value);
    syncDateState();
    dateInput.dispatchEvent(new Event('change', { bubbles: true }));
  }

  function renderDatepicker() {
    if (!datepickerTitle || !datepickerDays) return;

    var year = currentCalendarDate.getFullYear();
    var month = currentCalendarDate.getMonth();
    var firstDay = new Date(year, month, 1);
    var startDay = (firstDay.getDay() + 6) % 7;
    var startDate = new Date(year, month, 1 - startDay);
    var selectedDate = parseDateValue(dateInput ? dateInput.value : '');
    var today = new Date();

    datepickerTitle.textContent = monthNames[month] + ' ' + year;
    datepickerDays.innerHTML = '';

    for (var i = 0; i < 42; i += 1) {
      var dayDate = new Date(startDate);
      dayDate.setDate(startDate.getDate() + i);

      var dayButton = document.createElement('button');
      dayButton.type = 'button';
      dayButton.className = 'games-datepicker__day';
      dayButton.textContent = dayDate.getDate();
      dayButton.dataset.value = formatDateValue(dayDate);

      if (dayDate.getMonth() !== month) {
        dayButton.classList.add('is-outside');
      }

      if (isSameDate(dayDate, today)) {
        dayButton.classList.add('is-today');
      }

      if (isSameDate(dayDate, selectedDate)) {
        dayButton.classList.add('is-selected');
      }

      datepickerDays.appendChild(dayButton);
    }
  }

  function openDatepicker() {
    if (!datepicker || !datepickerPanel || !datepickerTrigger) return;
    closeAll();
    datepicker.classList.add('is-open');
    datepickerTrigger.setAttribute('aria-expanded', 'true');
    renderDatepicker();
  }

  function closeDatepicker() {
    if (!datepicker || !datepickerPanel || !datepickerTrigger) return;
    datepicker.classList.remove('is-open');
    datepickerTrigger.setAttribute('aria-expanded', 'false');
  }

  function syncDateState() {
    if (!dateInput) return;
    dateInput.classList.toggle('has-value', Boolean(dateInput.value));
    if (datepicker) {
      datepicker.classList.toggle('has-value', Boolean(dateInput.value));
    }
  }

  function syncMobileFilterPanel(forceOpen) {
    if (!mobileToggle || !mobilePanel) return;

    var isMobile = window.matchMedia('(max-width: 670px)').matches;
    var shouldOpen = isMobile ? Boolean(forceOpen) : true;

    mobileToggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
    mobileToggle.textContent = shouldOpen ? 'Закрыть фильтр' : 'Фильтровать';
    mobilePanel.hidden = !shouldOpen;
    mobilePanel.classList.toggle('is-open', shouldOpen);
  }

  function updateLabel(wrapper) {
    var btn = wrapper.querySelector('.multi-select__btn');
    var checked = wrapper.querySelectorAll('input[type="checkbox"]:checked');
    if (!btn) return;
    if (!checked.length) {
      btn.textContent = wrapper.dataset.placeholder || 'Все';
      return;
    }
    var names = Array.prototype.map.call(checked, function (el) {
      var label = el.closest('label');
      return label ? label.textContent.trim() : '';
    }).filter(Boolean);

    var joined = names.join(', ');
    if (checked.length > 2 || joined.length > 28) {
      btn.textContent = 'Выбрано: ' + checked.length;
      return;
    }

    btn.textContent = joined;
  }

  function closeAll(except) {
    selectWrappers.forEach(function (wrapper) {
      if (wrapper !== except) {
        wrapper.classList.remove('is-open');
      }
      var btn = wrapper.querySelector('.multi-select__btn');
      if (btn) {
        btn.setAttribute('aria-expanded', wrapper.classList.contains('is-open') ? 'true' : 'false');
      }
    });

    if (!except || except !== datepicker) {
      closeDatepicker();
    }
  }

  function getCheckedValues(name) {
    return Array.prototype.map.call(
      root.querySelectorAll('input[name="' + name + '[]"]:checked'),
      function (input) { return parseInt(input.value, 10) || 0; }
    ).filter(Boolean);
  }

  function itemMatches(item, key, values) {
    if (!values.length) return true;
    var raw = parseInt(item.dataset[key] || '0', 10) || 0;
    if (!raw) return true;
    return values.indexOf(raw) !== -1;
  }

  function toggleGeoOptions() {
    var selectedDirections = getCheckedValues('city_direction');
    var selectedAdminAreas = getCheckedValues('admin_area');
    var selectedDistricts = getCheckedValues('district');

    root.querySelectorAll('[data-geo-filter]').forEach(function (field) {
      var taxonomy = field.getAttribute('data-geo-filter');
      var wrapper = field.querySelector('.search-multi-select');
      if (!wrapper) return;

      var visibleCount = 0;

      wrapper.querySelectorAll('.multi-select__item').forEach(function (item) {
        var visible = true;
        var itemCity = parseInt(item.dataset.city || '0', 10) || 0;

        if (cityId && itemCity && itemCity !== cityId) {
          visible = false;
        }

        if (visible && taxonomy === 'admin_area') {
          visible = itemMatches(item, 'direction', selectedDirections);
        }

        if (visible && taxonomy === 'district') {
          if (selectedAdminAreas.length) {
            visible = itemMatches(item, 'adminArea', selectedAdminAreas);
          } else if (selectedDirections.length) {
            visible = itemMatches(item, 'direction', selectedDirections);
          }
        }

        if (visible && taxonomy === 'metro') {
          if (selectedDistricts.length) {
            visible = itemMatches(item, 'district', selectedDistricts);
          } else if (selectedAdminAreas.length) {
            visible = itemMatches(item, 'adminArea', selectedAdminAreas);
          } else if (selectedDirections.length) {
            visible = itemMatches(item, 'direction', selectedDirections);
          }
        }

        var input = item.querySelector('input');
        if (!visible && input) {
          input.checked = false;
        }

        item.hidden = !visible;
        if (visible) visibleCount += 1;
      });

      field.hidden = visibleCount === 0;
      updateLabel(wrapper);
    });
  }

  if (dateInput && datepicker) {
    var selectedDate = parseDateValue(dateInput.value);
    currentCalendarDate = selectedDate || new Date();
    setDateValue(dateInput.value);

    if (datepickerTrigger) {
      datepickerTrigger.addEventListener('mousedown', function (event) {
        event.preventDefault();
        event.stopPropagation();
      });
      datepickerTrigger.addEventListener('click', function (event) {
        event.stopPropagation();
        if (datepicker.classList.contains('is-open')) {
          closeDatepicker();
          return;
        }
        openDatepicker();
      });
    }

    if (datepickerPrev) {
      datepickerPrev.addEventListener('click', function () {
        currentCalendarDate = new Date(currentCalendarDate.getFullYear(), currentCalendarDate.getMonth() - 1, 1);
        renderDatepicker();
      });
    }

    if (datepickerNext) {
      datepickerNext.addEventListener('click', function () {
        currentCalendarDate = new Date(currentCalendarDate.getFullYear(), currentCalendarDate.getMonth() + 1, 1);
        renderDatepicker();
      });
    }

    if (datepickerToday) {
      datepickerToday.addEventListener('click', function () {
        var today = new Date();
        currentCalendarDate = new Date(today.getFullYear(), today.getMonth(), 1);
        setDateValue(formatDateValue(today));
        renderDatepicker();
        closeDatepicker();
      });
    }

    if (datepickerClear) {
      datepickerClear.addEventListener('click', function () {
        setDateValue('');
        renderDatepicker();
        closeDatepicker();
      });
    }

    if (datepickerDays) {
      datepickerDays.addEventListener('click', function (event) {
        var button = event.target.closest('.games-datepicker__day');
        if (!button) return;
        setDateValue(button.dataset.value || '');
        var picked = parseDateValue(button.dataset.value || '');
        if (picked) {
          currentCalendarDate = new Date(picked.getFullYear(), picked.getMonth(), 1);
        }
        renderDatepicker();
        closeDatepicker();
      });
    }
  }

  if (mobileToggle && mobilePanel) {
    syncMobileFilterPanel(false);

    mobileToggle.addEventListener('click', function () {
      syncMobileFilterPanel(mobilePanel.hidden);
    });

    window.addEventListener('resize', function () {
      if (window.matchMedia('(max-width: 670px)').matches) {
        syncMobileFilterPanel(false);
        return;
      }

      syncMobileFilterPanel(true);
    });
  }

  selectWrappers.forEach(function (wrapper) {
    var btn = wrapper.querySelector('.multi-select__btn');
    var panel = wrapper.querySelector('.multi-select__panel');
    updateLabel(wrapper);

    if (btn) {
      btn.addEventListener('click', function () {
        var open = wrapper.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) closeAll(wrapper);
      });
    }

    if (panel) {
      panel.addEventListener('change', function () {
        updateLabel(wrapper);
        toggleGeoOptions();
      });
    }
  });

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.search-multi-select') && !e.target.closest('[data-datepicker]')) {
      closeAll();
    }

    if (!e.target.closest('[data-datepicker]')) {
      closeDatepicker();
    }
  });

  toggleGeoOptions();
});
</script>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
