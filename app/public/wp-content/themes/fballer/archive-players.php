<?php get_header(); ?>

<?php
global $wp_query;
$ajaxVariable = 'ajaxPlayersData';
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

$city_has_directions = $selected_city ? $get_city_flag( $selected_city, 'has_directions' ) : false;
$city_has_admin_areas = $selected_city ? $get_city_flag( $selected_city, 'has_admin_areas' ) : false;
$city_has_districts = $selected_city ? $get_city_flag( $selected_city, 'has_districts' ) : false;
$city_has_metro = $selected_city ? $get_city_flag( $selected_city, 'has_metro' ) : false;

$team_levels = get_terms(array(
	'taxonomy' => 'team_level',
	'hide_empty' => false,
));
$team_positions = get_terms(array(
	'taxonomy' => 'team_position',
	'hide_empty' => false,
));
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

wp_localize_script('ajax-posts', $ajaxVariable, array(
	'ajaxurl' => admin_url('admin-ajax.php'),
	'query_vars' => json_encode($wp_query->query_vars),
	'current_page' => max(1, get_query_var('paged')),
	'max_pages' => $wp_query->max_num_pages,
));
?>

<section class="section place-detail">
	<div class="container">
		<div class="search-inner">
			<div class="container ajax-posts" ajax-data="<?php echo $ajaxVariable; ?>">
				<div class="section-title">Игроки ищут команду или игру</div>

				<div class="search-filter__row search-filter__row--games">
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
					<form class="filter-chiose__items active games-filter-form" id="filter-1">
						<button type="button" class="games-filter-mobile-toggle" data-games-filter-toggle aria-expanded="false">Фильтровать</button>
						<div class="games-filter-mobile-panel" data-games-filter-panel>
							<div class="input-row games-filter-form__row" style="--games-filter-columns: <?php echo (int) $desktop_filter_columns; ?>;">
								<div class="input-wrap">
									<label>Что ищет</label>
									<div class="multi-select search-multi-select" data-placeholder="Все">
										<button type="button" class="multi-select__btn" aria-haspopup="listbox" aria-expanded="false">Все</button>
										<div class="multi-select__panel" role="listbox">
											<label class="multi-select__item">
												<input type="checkbox" name="player_goal_multi[]" value="team">
												<span>Команду</span>
											</label>
											<label class="multi-select__item">
												<input type="checkbox" name="player_goal_multi[]" value="game">
												<span>Игру</span>
											</label>
										</div>
									</div>
								</div>

								<div class="input-wrap">
									<label>Уровень игры</label>
									<div class="multi-select search-multi-select" data-placeholder="Все">
										<button type="button" class="multi-select__btn" aria-haspopup="listbox" aria-expanded="false">Все</button>
										<div class="multi-select__panel" role="listbox">
											<?php foreach ( (array) $team_levels as $term ) { ?>
												<label class="multi-select__item">
													<input type="checkbox" name="team_level_multi[]" value="<?php echo (int) $term->term_id; ?>">
													<span><?php echo esc_html($term->name); ?></span>
												</label>
											<?php } ?>
										</div>
									</div>
								</div>

								<div class="input-wrap">
									<label>Позиция на поле</label>
									<div class="multi-select search-multi-select" data-placeholder="Все">
										<button type="button" class="multi-select__btn" aria-haspopup="listbox" aria-expanded="false">Все</button>
										<div class="multi-select__panel" role="listbox">
											<?php foreach ( (array) $team_positions as $term ) { ?>
												<label class="multi-select__item">
													<input type="checkbox" name="team_position_multi[]" value="<?php echo (int) $term->term_id; ?>">
													<span><?php echo esc_html($term->name); ?></span>
												</label>
											<?php } ?>
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

				<div class="player-items search-player__items ajax-posts__items">
					<a href="<?php echo esc_url(fballer_get_add_page_url('add-player', 'templates/template-add-player.php')); ?>" class="player-item player-item--player">
						<div class="add-wrapper">
							<svg width="82" height="82" viewBox="0 0 82 82" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="41" cy="41" r="41" fill="#D9D9D9" /><path d="M39.644 49.4981V31.1021H43.172V49.4981H39.644ZM32 41.9801V38.6621H50.816V41.9801H32Z" fill="white" /></svg>
							<div class="add-text">Добавить игрока</div>
						</div>
					</a>

					<?php if ( have_posts() ) { ?>
						<?php while ( have_posts() ) { the_post(); ?>
							<?php get_template_part('templates/content-players-item'); ?>
						<?php } wp_reset_postdata(); ?>
					<?php } ?>
				</div>

				<?php if ( $wp_query->max_num_pages > 1 ) { ?>
					<a href="javascript:void(0)" id="load-more" class="primary-btn load-more ajax-posts__btn">Загрузить еще</a>
				<?php } ?>
			</div>
		</div>
	</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var root = document.querySelector('.post-type-archive-players .games-filter-form, .page-template-template-players .games-filter-form');
  if (!root) return;

  var cityId = <?php echo (int) $selected_city; ?>;
  var mobileToggle = root.querySelector('[data-games-filter-toggle]');
  var mobilePanel = root.querySelector('[data-games-filter-panel]');
  var selectWrappers = root.querySelectorAll('.search-multi-select');

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
    if (!e.target.closest('.search-multi-select')) {
      closeAll();
    }
  });

  toggleGeoOptions();
});
</script>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
