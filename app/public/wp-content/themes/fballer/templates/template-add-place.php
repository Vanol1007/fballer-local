<?php
/*
Template Name: Добавить поле
*/

$result = fballer_handle_frontend_submission('places');
$errors = $result['errors'] ?? [];
$success = $result['success'] ?? false;

$selected_city = isset($_COOKIE['selected_city']) ? (int) $_COOKIE['selected_city'] : 0;
$cities = get_terms([
	'taxonomy' => 'city',
	'fields' => 'all',
	'hide_empty' => false,
	'parent' => 0,
]);
$field_sizes = get_terms([
	'taxonomy' => 'field_size',
	'fields' => 'all',
	'hide_empty' => false,
]);
$formats = get_terms([
	'taxonomy' => 'game_format',
	'fields' => 'all',
	'hide_empty' => false,
]);
if ( is_wp_error($formats) || empty($formats) ) {
	$formats = [
		(object) ['term_id' => 195, 'name' => '10x10'],
		(object) ['term_id' => 18, 'name' => '11x11'],
		(object) ['term_id' => 196, 'name' => '12x12'],
		(object) ['term_id' => 19, 'name' => '3x3'],
		(object) ['term_id' => 20, 'name' => '4x4'],
		(object) ['term_id' => 13, 'name' => '5x5'],
		(object) ['term_id' => 14, 'name' => '6x6'],
		(object) ['term_id' => 15, 'name' => '7x7'],
		(object) ['term_id' => 16, 'name' => '8x8'],
		(object) ['term_id' => 17, 'name' => '9x9'],
	];
}
$coatings = get_terms([
	'taxonomy' => 'coating',
	'fields' => 'all',
	'hide_empty' => false,
]);
$field_types = get_terms([
	'taxonomy' => 'field_type',
	'fields' => 'all',
	'hide_empty' => false,
]);
$features = get_terms([
	'taxonomy' => 'features',
	'fields' => 'all',
	'hide_empty' => false,
]);
$selected_features = isset($_POST['features']) ? array_map('intval', (array) $_POST['features']) : [];

$covered_field_type_id = 0;
$open_field_type_id = 0;
if ( ! is_wp_error($field_types) ) {
	foreach ( $field_types as $term ) {
		$normalized_name = mb_strtolower(trim((string) $term->name));
		if ( $normalized_name === 'крытое' ) {
			$covered_field_type_id = (int) $term->term_id;
		}
		if ( $normalized_name === 'открытое' ) {
			$open_field_type_id = (int) $term->term_id;
		}
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
			<div class="section-title">Добавить поле</div>

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

				<?php wp_nonce_field('fb_add_places', '_fb_nonce'); ?>
				<?php fballer_render_antispam_fields(); ?>

				<div class="input-row">
					<div class="input-wrap">
						<label for="title">Название</label>
						<input type="text" id="title" name="title" value="<?php echo esc_attr($_POST['title'] ?? ''); ?>" required>
					</div>
					<div class="input-wrap">
						<label for="city">Город</label>
						<select id="city" name="city" required>
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
				</div>

				<div class="input-row">
					<div class="input-wrap">
						<label for="place_address">Адрес</label>
						<input type="text" id="place_address" name="place_address" value="<?php echo esc_attr($_POST['place_address'] ?? ''); ?>">
					</div>
					<div class="input-wrap">
						<label for="place-phone">Телефон</label>
						<input type="text" id="place-phone" name="phone" value="<?php echo esc_attr($_POST['phone'] ?? ''); ?>">
					</div>
				</div>

					<div class="input-row">
						<div class="input-wrap">
							<label for="place_price">Минимальная цена за час</label>
							<input type="text" id="place_price" name="place_price" value="<?php echo esc_attr($_POST['place_price'] ?? ''); ?>">
						</div>
					<div class="input-wrap">
						<label>Подходит для формата игры</label>
					<?php $selected_formats = (array) ($_POST['game_format'] ?? []); ?>
					<div class="multi-select" data-placeholder="Не выбрано">
						<button type="button" class="multi-select__btn" aria-haspopup="listbox" aria-expanded="false">Не выбрано</button>
						<div class="multi-select__panel" role="listbox">
							<?php if ( ! is_wp_error($formats) ) { ?>
								<?php foreach ( $formats as $term ) { ?>
									<?php $checked = in_array((string) $term->term_id, array_map('strval', $selected_formats), true); ?>
									<label class="multi-select__item">
										<input type="checkbox" name="game_format[]" value="<?php echo (int) $term->term_id; ?>" <?php echo $checked ? 'checked' : ''; ?>>
										<span><?php echo esc_html($term->name); ?></span>
									</label>
								<?php } ?>
							<?php } ?>
						</div>
						</div>
</div>
					</div>

					<div class="input-row">
						<div class="input-wrap">
							<label for="coating">Покрытие</label>
							<select id="coating" name="coating">
								<option value="">Не выбрано</option>
								<?php if ( ! is_wp_error($coatings) ) { ?>
								<?php foreach ( $coatings as $term ) { ?>
									<option value="<?php echo (int) $term->term_id; ?>" <?php selected($_POST['coating'] ?? '', $term->term_id); ?>>
										<?php echo esc_html($term->name); ?>
									</option>
								<?php } ?>
							<?php } ?>
						</select>
					</div>
						<div class="input-wrap">
							<label>Тип поля</label>
							<?php $selected_field_types = array_map('intval', (array) ($_POST['field_type'] ?? [])); ?>
							<div class="features-list">
								<?php if ( $covered_field_type_id > 0 ) { ?>
									<label class="feature-item">
										<input type="checkbox" name="field_type[]" value="<?php echo $covered_field_type_id; ?>" <?php echo in_array($covered_field_type_id, $selected_field_types, true) ? 'checked' : ''; ?>>
										<span>Крытое</span>
									</label>
								<?php } ?>
								<?php if ( $open_field_type_id > 0 ) { ?>
									<label class="feature-item">
										<input type="checkbox" name="field_type[]" value="<?php echo $open_field_type_id; ?>" <?php echo in_array($open_field_type_id, $selected_field_types, true) ? 'checked' : ''; ?>>
										<span>Открытое</span>
									</label>
								<?php } ?>
							</div>
						</div>
					</div>

				<div class="input-row">
					<div class="input-wrap">
						<label>Особенности</label>
						<div class="features-list">
							<?php if ( ! is_wp_error($features) ) { ?>
								<?php foreach ( $features as $term ) { ?>
									<label class="feature-item">
										<input type="checkbox" name="features[]" value="<?php echo (int) $term->term_id; ?>" <?php echo in_array((int) $term->term_id, $selected_features, true) ? 'checked' : ''; ?>>
										<span><?php echo esc_html($term->name); ?></span>
									</label>
								<?php } ?>
							<?php } ?>
						</div>
					</div>
					<div class="input-wrap">
						<label for="place_info">Инфо</label>
						<textarea id="place_info" name="place_info"><?php echo esc_textarea($_POST['place_info'] ?? ''); ?></textarea>
					</div>
					
				</div>

				<div class="input-row input-row--message">
					<div class="input-col">
						<button type="submit" class="submit-btn">Отправить</button>
						<div class="form-policy">Нажимая кнопку “Отправить”, вы соглашаетесь с условиями <a href="/privacy-policy/">политики конфиденциальности</a></div>
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
document.addEventListener('DOMContentLoaded', function () {
  var selects = document.querySelectorAll('.multi-select');
  function updateLabel(wrapper) {
    var btn = wrapper.querySelector('.multi-select__btn');
    var checked = wrapper.querySelectorAll('input[type="checkbox"]:checked');
    if (!btn) return;
    if (!checked.length) {
      btn.textContent = wrapper.dataset.placeholder || 'Не выбрано';
      return;
    }
    var names = Array.prototype.map.call(checked, function (el) {
      var label = el.closest('label');
      return label ? label.textContent.trim() : '';
    }).filter(Boolean);
    btn.textContent = names.join(', ');
  }
  function closeAll(except) {
    selects.forEach(function (w) {
      if (w !== except) w.classList.remove('is-open');
      var b = w.querySelector('.multi-select__btn');
      if (b) b.setAttribute('aria-expanded', w.classList.contains('is-open') ? 'true' : 'false');
    });
  }
  selects.forEach(function (wrapper) {
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
      });
    }
  });
  document.addEventListener('click', function (e) {
    var inside = e.target.closest('.multi-select');
    if (!inside) closeAll();
  });
});
</script>


<?php get_footer(); ?>
