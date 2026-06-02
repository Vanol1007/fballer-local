<?php
/*
Template Name: Добавить чемпионат
*/

$result = fballer_handle_frontend_submission('champs');
$errors = $result['errors'] ?? [];
$success = $result['success'] ?? false;

$selected_city = isset($_COOKIE['selected_city']) ? (int) $_COOKIE['selected_city'] : 0;
$cities = get_terms([
	'taxonomy' => 'city',
	'fields' => 'all',
	'hide_empty' => false,
	'parent' => 0,
]);
$formats = get_terms([
	'taxonomy' => 'game_format',
	'fields' => 'all',
	'hide_empty' => false,
]);
$places = get_posts([
	'post_type' => 'places',
	'posts_per_page' => -1,
	'orderby' => 'title',
	'order' => 'ASC',
]);
?>

<?php get_header(); ?>

<section class="section place-detail">
	<div class="place-detail__bg">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/img/cart-bg.png" alt="img">
	</div>
	<div class="container">

		<div class="search-inner">
			<div class="section-title">Добавить чемпионат</div>

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

				<?php wp_nonce_field('fb_add_champs', '_fb_nonce'); ?>
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
						<label for="champ_start">Старт</label>
						<input type="text" id="champ_start" placeholder="дд.мм.гггг" name="champ_start" value="<?php echo esc_attr($_POST['champ_start'] ?? ''); ?>">
					</div>
					<div class="input-wrap">
						<label for="champ_end">Окончание</label>
						<input type="text" id="champ_end" placeholder="дд.мм.гггг" name="champ_end" value="<?php echo esc_attr($_POST['champ_end'] ?? ''); ?>">
					</div>
				</div>

				<div class="input-row">
					<div class="input-wrap">
						<label for="champ_address">Адрес</label>
						<input type="text" id="champ_address" name="champ_address" value="<?php echo esc_attr($_POST['champ_address'] ?? ''); ?>">
					</div>
					<div class="input-wrap">
						<label for="phone">Телефон</label>
						<input type="text" id="place-phone" name="phone" value="<?php echo esc_attr($_POST['phone'] ?? ''); ?>">
					</div>
				</div>

				<div class="input-row">
					<div class="input-wrap">
						<label for="champ_reward">Приз</label>
						<input type="text" id="champ_reward" name="champ_reward" value="<?php echo esc_attr($_POST['champ_reward'] ?? ''); ?>">
					</div>
					<div class="input-wrap">
						<label for="champ_price">Цена за матч</label>
						<input type="number" id="champ_price" name="champ_price" min="0" step="100" value="<?php echo esc_attr($_POST['champ_price'] ?? ''); ?>">
					</div>
				</div>

				
<div class="input-row">
  <div class="input-wrap">
    <label for="champ_website">Сайт</label>
    <input type="url" id="champ_website" name="champ_website" value="<?php echo esc_attr($_POST['champ_website'] ?? ''); ?>">
  </div>
  <div class="input-wrap">
    <label>Формат игры</label>
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
  function formatDateInput(el) {
    var v = el.value.replace(/[^0-9]/g, '').slice(0, 8);
    var parts = [];
    if (v.length > 0) parts.push(v.slice(0, 2));
    if (v.length >= 3) parts.push(v.slice(2, 4));
    if (v.length >= 5) parts.push(v.slice(4, 8));
    el.value = parts.join('.');
  }

  document.querySelectorAll('input[name="date-game"], input[name="champ_start"], input[name="champ_end"]').forEach(function (el) {
    el.addEventListener('input', function () { formatDateInput(el); });
    el.addEventListener('blur', function () { formatDateInput(el); });
  });
});
</script>


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


<script>
document.addEventListener('DOMContentLoaded', function () {
  var citySelect = document.getElementById('city');
  var champPlaces = document.querySelector('.multi-select--places .multi-select__panel');
  if (!citySelect || !champPlaces) return;

  function filterChampPlacesByCity() {
    var cityId = citySelect.value;
    var items = champPlaces.querySelectorAll('.multi-select__item');
    items.forEach(function (item) {
      var input = item.querySelector('input');
      var data = (input && input.getAttribute('data-cities')) || item.getAttribute('data-cities') || '';
      var list = data.split(',').map(function (s) { return s.trim(); }).filter(Boolean);
      if (!cityId) {
        item.hidden = false;
        if (input) input.disabled = false;
        return;
      }
      var match = list.indexOf(String(cityId)) !== -1;
      item.hidden = !match;
      if (input) {
        input.disabled = !match;
        if (!match) input.checked = false;
      }
    });
  }

  citySelect.addEventListener('change', filterChampPlacesByCity);
  filterChampPlacesByCity();
});
</script>

<?php get_footer(); ?>
