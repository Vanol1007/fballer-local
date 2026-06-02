<?php
/*
Template Name: Добавить игру
*/

$result = fballer_handle_frontend_submission('games');
$errors = $result['errors'] ?? [];
$success = $result['success'] ?? false;

$selected_city = isset($_COOKIE['selected_city']) ? (int) $_COOKIE['selected_city'] : 0;
$cities = get_terms([
	'taxonomy' => 'city',
	'fields' => 'all',
	'hide_empty' => false,
	'parent' => 0,
]);
$places = get_posts([
	'post_type' => 'places',
	'posts_per_page' => -1,
	'orderby' => 'title',
	'order' => 'ASC',
]);
$formats = get_terms([
	'taxonomy' => 'game_format',
	'fields' => 'all',
	'hide_empty' => false,
]);
if ( ! is_wp_error($formats) && ! empty($formats) ) {
	usort($formats, static function($a, $b) {
		$a_name = isset($a->name) ? (string) $a->name : '';
		$b_name = isset($b->name) ? (string) $b->name : '';

		$a_number = (int) preg_replace('/^(\d+).*/', '$1', $a_name);
		$b_number = (int) preg_replace('/^(\d+).*/', '$1', $b_name);

		if ( $a_number === $b_number ) {
			return strnatcasecmp($a_name, $b_name);
		}

		return $a_number <=> $b_number;
	});
}
$selected_place_id = isset($_POST['game_places'][0]) ? (int) $_POST['game_places'][0] : 0;
$selected_place_title = '';
if ( $selected_place_id > 0 ) {
	$selected_place_post = get_post($selected_place_id);
	if ( $selected_place_post instanceof WP_Post ) {
		$selected_place_title = $selected_place_post->post_title;
	}
}
?>

<?php get_header(); ?>

<section class="section place-detail">
	<div class="place-detail__bg">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/img/cart-bg.png" alt="img">
	</div>
	<div class="container">

		<div class="search-inner">
			<div class="section-title">Добавить игру</div>

			<?php if ( $success ) { ?>
				<div class="form-success-state" role="status">
					<div class="form-success-state__badge" aria-hidden="true">
						<svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M23.333 8L11.667 19.667 6.667 14.667" stroke="#E52F24" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</div>
					<h2 class="form-success-state__title">Заявка отправлена</h2>
					<p class="form-success-state__text">Спасибо. Мы получили данные и передали их на модерацию. После проверки информация появится на сайте.</p>
					<div class="form-success-state__actions">
						<a href="<?php echo esc_url(get_permalink(get_queried_object_id())); ?>" class="primary-btn">Отправить ещё одну</a>
						<a href="<?php echo esc_url(home_url('/')); ?>" class="primary-btn">На главную</a>
					</div>
				</div>
			<?php } else { ?>
			<form class="form add-form" method="post" action="">
				<?php if ( $success ) { ?>
									<?php } ?>
				<?php if ( ! empty($errors) ) { ?>
					<div class="add-form__error">
						<?php echo esc_html(implode(' ', $errors)); ?>
					</div>
				<?php } ?>

				<?php wp_nonce_field('fb_add_games', '_fb_nonce'); ?>
				<?php fballer_render_antispam_fields(); ?>

				<div class="input-row">
					<div class="input-wrap">
						<label>Город</label>
						<select name="city" id="city" required>
							<option value="">Не выбрано</option>
							<?php if ( ! is_wp_error($cities) ) { ?>
								<?php foreach ( $cities as $term ) { ?>
									<?php $selected = $_POST['city'] ?? $selected_city; ?>
									<option value="<?php echo (int) $term->term_id; ?>" <?php selected($selected, $term->term_id); ?>>
										<?php echo esc_html($term->name); ?>
									</option>
								<?php } ?>
							<?php } ?>
						</select>
					</div>

					<div class="input-wrap">
						<label>Поле (если есть)</label>
						<input
							type="text"
							id="place"
							list="game-place-options"
							placeholder="Начните вводить название поля"
							value="<?php echo esc_attr($selected_place_title); ?>"
							autocomplete="off"
						>
						<input type="hidden" name="game_places[]" id="place-id" value="<?php echo esc_attr($selected_place_id); ?>">
						<datalist id="game-place-options">
							<?php foreach ( $places as $place ) { ?>
								<?php $place_cities = wp_get_post_terms($place->ID, 'city', ['fields' => 'ids']); ?>
								<option
									value="<?php echo esc_attr($place->post_title); ?>"
									data-id="<?php echo (int) $place->ID; ?>"
									data-cities="<?php echo esc_attr(implode(',', $place_cities)); ?>"
								></option>
							<?php } ?>
						</datalist>
					</div>
				</div>

				<div class="input-row">
					<div class="input-wrap">
						<label>
							<input type="checkbox" name="field-not-found" value="1" <?php checked(isset($_POST['field-not-found'])); ?>>
							Поле не найдено в списке
						</label>
					</div>
				</div>

				<div class="input-row place-extra">
					<div class="input-wrap">
						<label>Название поля</label>
						<input type="text" name="text-place-name" placeholder="Название поля" value="<?php echo esc_attr($_POST['text-place-name'] ?? ''); ?>">
					</div>
					<div class="input-wrap">
						<label>Адрес поля</label>
						<input type="text" name="text-address" placeholder="ул. Софийская, д.2" value="<?php echo esc_attr($_POST['text-address'] ?? ''); ?>">
					</div>
				</div>

				<div class="input-row">
					<div class="input-wrap">
						<label>Формат игры</label>
						<select name="game_format" required>
							<option value="">Не выбрано</option>
							<?php if ( ! is_wp_error($formats) ) { ?>
								<?php foreach ( $formats as $term ) { ?>
									<option value="<?php echo (int) $term->term_id; ?>" <?php selected($_POST['game_format'] ?? '', $term->term_id); ?>>
										<?php echo esc_html($term->name); ?>
									</option>
								<?php } ?>
							<?php } ?>
						</select>
					</div>

					<div class="input-wrap">
						<label>Дата</label>
						<input type="text" name="game_date" placeholder="дд.мм.гггг" value="<?php echo esc_attr($_POST['game_date'] ?? ''); ?>" required>
					</div>
				</div>

				<div class="input-row">
					<div class="input-wrap">
						<label>Время (часы)</label>
						<select name="select-hour" id="hour" required>
							<?php for ( $h = 0; $h <= 23; $h++ ) { $hh = str_pad((string)$h, 2, '0', STR_PAD_LEFT); ?>
								<option value="<?php echo esc_attr($hh); ?>" <?php selected($_POST['select-hour'] ?? '', $hh); ?>><?php echo esc_html($hh); ?></option>
							<?php } ?>
						</select>
					</div>

					<div class="input-wrap">
						<label>Время (минуты)</label>
						<select name="select-minute" id="minute" required>
							<?php foreach ( ['00','15','30','45'] as $mm ) { ?>
								<option value="<?php echo esc_attr($mm); ?>" <?php selected($_POST['select-minute'] ?? '', $mm); ?>><?php echo esc_html($mm); ?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="input-row">
					<div class="input-wrap">
						<label>Ссылка на Telegram</label>
						<input type="url" name="btn_link" placeholder="https://t.me/username" value="<?php echo esc_attr($_POST['btn_link'] ?? ''); ?>">
						<input type="hidden" name="btn_text" value="<?php echo esc_attr($_POST['btn_text'] ?? 'Написать'); ?>">
					</div>

					<div class="input-wrap">
						<label>Стоимость</label>
						<input type="text" name="game_price" placeholder="400" value="<?php echo esc_attr($_POST['game_price'] ?? ''); ?>">
					</div>
				</div>

				<div class="input-row input-row--message">
				<div class="input-wrap input-wrap--textarea">
					<label>Описание</label>
					<textarea name="content" placeholder="Привет. Сегодня есть вариант поиграть в однодневном турнире. Начало в 17:00. Софийская дом 2. Манеж 5x5 газон"><?php echo esc_textarea($_POST['content'] ?? ''); ?></textarea>
				</div>

				<div class="input-col">
					<div class="input-wrap">
						<label>Телефон</label>
						<input type="text" id="place-phone" name="phone" placeholder="+7 (900) 000-00-00" value="<?php echo esc_attr($_POST['phone'] ?? ''); ?>" required>
					</div>
					<input type="hidden" name="game_time" id="game-time-hidden" value="<?php echo esc_attr($_POST['game_time'] ?? ''); ?>">
					<button type="submit" class="submit-btn">Отправить</button>
					<div class="form-policy">Нажимая кнопку “Отправить”, вы соглашаетесь с условиями <a href="/privacy-policy/">политики конфиденциальности</a></div>
				</div>
			</div>
				</div>

				</div>
			</form>
			<?php } ?>
		</div>
	</div>
</section>

<?php if ( get_the_content() ) { ?>
<section class="section">
	<div class="container">
		<div class="page-content single-news__content-inner form-seo-text">
			<?php the_content(); ?>
		</div>
	</div>
</section>
<?php } ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const checkbox = document.querySelector('input[name="field-not-found"]');
	const extra = document.querySelector('.place-extra');
	if ( ! checkbox || ! extra ) return;
	const sync = () => {
		extra.style.display = checkbox.checked ? 'grid' : 'none';
	};
	sync();
	checkbox.addEventListener('change', sync);
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
  var hourSelect = document.getElementById('hour');
  var minuteSelect = document.getElementById('minute');
  var timeInput = document.getElementById('game-time-hidden');

  function syncGameTime() {
    if (!hourSelect || !minuteSelect || !timeInput) return;
    timeInput.value = hourSelect.value + ':' + minuteSelect.value;
  }

  if (hourSelect && minuteSelect && timeInput) {
    if (timeInput.value) {
      var parts = timeInput.value.split(':');
      if (parts.length === 2) {
        hourSelect.value = parts[0];
        minuteSelect.value = parts[1];
      }
    }
    syncGameTime();
    hourSelect.addEventListener('change', syncGameTime);
    minuteSelect.addEventListener('change', syncGameTime);
  }
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
  function formatDateInput(el) {
    var v = el.value.replace(/[^0-9]/g, '').slice(0, 8);
    var parts = [];
    if (v.length > 0) parts.push(v.slice(0, 2));
    if (v.length >= 3) parts.push(v.slice(2, 4));
    if (v.length >= 5) parts.push(v.slice(4, 8));
    el.value = parts.join('.');
  }

  document.querySelectorAll('input[name="game_date"], input[name="champ_start"], input[name="champ_end"]').forEach(function (el) {
    el.addEventListener('input', function () { formatDateInput(el); });
    el.addEventListener('blur', function () { formatDateInput(el); });
  });
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
  var citySelect = document.getElementById('city');
  var placeInput = document.getElementById('place');
  var placeIdInput = document.getElementById('place-id');
  var placeOptions = Array.prototype.slice.call(document.querySelectorAll('#game-place-options option')).map(function (option) {
    return {
      id: option.getAttribute('data-id') || '',
      title: option.value || '',
      cities: (option.getAttribute('data-cities') || '').split(',').filter(Boolean)
    };
  });
  var fieldNotFoundCheckbox = document.querySelector('input[name="field-not-found"]');

  if (!citySelect || !placeInput || !placeIdInput) return;

  function buildOptions() {
    var cityId = citySelect.value;
    var datalist = document.getElementById('game-place-options');
    if (!datalist) return;

    datalist.innerHTML = '';
    placeOptions.forEach(function (item) {
      if (cityId && item.cities.indexOf(String(cityId)) === -1) {
        return;
      }
      var option = document.createElement('option');
      option.value = item.title;
      option.setAttribute('data-id', item.id);
      datalist.appendChild(option);
    });
  }

  function syncSelectedPlace() {
    var value = placeInput.value.trim();
    var cityId = citySelect.value;
    var match = placeOptions.find(function (item) {
      if (item.title !== value) {
        return false;
      }
      if (cityId && item.cities.indexOf(String(cityId)) === -1) {
        return false;
      }
      return true;
    });

    placeIdInput.value = match ? match.id : '';
  }

  function handleCityChange() {
    buildOptions();
    syncSelectedPlace();

    if (!placeIdInput.value && placeInput.value.trim() !== '') {
      placeInput.value = '';
    }
  }

  if (fieldNotFoundCheckbox && fieldNotFoundCheckbox.checked && !placeInput.value) {
    placeIdInput.value = '';
  }

  citySelect.addEventListener('change', handleCityChange);
  placeInput.addEventListener('input', syncSelectedPlace);
  placeInput.addEventListener('change', syncSelectedPlace);

  handleCityChange();
});
</script>

<?php get_footer(); ?>
