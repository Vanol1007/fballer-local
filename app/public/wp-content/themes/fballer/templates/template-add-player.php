<?php
/*
Template Name: Игрок ищет команду или игру
*/

$result = fballer_handle_frontend_submission('players');
$errors = $result['errors'] ?? [];
$success = $result['success'] ?? false;
$created_post_id = (int) ($result['post_id'] ?? 0);

$selected_city = isset($_COOKIE['selected_city']) ? (int) $_COOKIE['selected_city'] : 0;
$cities = get_terms([
	'taxonomy' => 'city',
	'fields' => 'all',
	'hide_empty' => false,
	'parent' => 0,
]);
$team_levels = get_terms([
	'taxonomy' => 'team_level',
	'fields' => 'all',
	'hide_empty' => false,
]);
$team_positions = get_terms([
	'taxonomy' => 'team_position',
	'fields' => 'all',
	'hide_empty' => false,
]);

$player_goal = $_POST['player_goal'] ?? 'team';
if ( ! in_array($player_goal, ['team', 'game'], true) ) {
	$player_goal = 'team';
}

$matched_ids = [];
$matched_title = '';
$matched_post_type = '';
if ( $success && $created_post_id > 0 ) {
	if ( $player_goal === 'game' ) {
		$matched_ids = fballer_get_matching_games_for_player($created_post_id, 6);
		$matched_title = 'Эти игры могли бы вам подойти';
		$matched_post_type = 'games';
	} else {
		$matched_ids = fballer_get_matching_teams_for_player($created_post_id, 6);
		$matched_title = 'Эти команды могли бы вам подойти';
		$matched_post_type = 'teams';
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
			<div class="section-title">Игрок ищет команду или игру</div>

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

				<?php if ( ! empty($matched_ids) && $matched_post_type !== '' ) { ?>
					<div class="players">
						<div class="section-title"><?php echo esc_html($matched_title); ?></div>
						<div class="player-items">
							<?php
							$matched_query = new WP_Query([
								'post_type' => $matched_post_type,
								'post__in' => $matched_ids,
								'orderby' => 'post__in',
								'posts_per_page' => count($matched_ids),
							]);
							if ( $matched_query->have_posts() ) {
								while ( $matched_query->have_posts() ) {
									$matched_query->the_post();
									if ( $matched_post_type === 'games' ) {
										get_template_part('templates/content-games-item');
									} else {
										get_template_part('templates/content-teams-item');
									}
								}
								wp_reset_postdata();
							}
							?>
						</div>
					</div>
				<?php } ?>
			<?php } else { ?>
			<form class="form add-form" method="post" action="">
				<?php if ( ! empty($errors) ) { ?>
					<div class="add-form__error">
						<?php echo esc_html(implode(' ', $errors)); ?>
					</div>
				<?php } ?>

				<?php wp_nonce_field('fb_add_players', '_fb_nonce'); ?>
				<?php fballer_render_antispam_fields(); ?>

				<div class="input-row">
					<div class="input-wrap">
						<label for="player_goal">Что ищете</label>
						<select id="player_goal" name="player_goal" required>
							<option value="team" <?php selected($player_goal, 'team'); ?>>Команду</option>
							<option value="game" <?php selected($player_goal, 'game'); ?>>Игру</option>
						</select>
					</div>
					<div class="input-wrap">
						<label for="title">Ваше имя или заголовок объявления</label>
						<input type="text" id="title" name="title" value="<?php echo esc_attr($_POST['title'] ?? ''); ?>" required>
					</div>
				</div>

				<div class="input-row">
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
					<div class="input-wrap">
						<label for="team_level">Уровень игры</label>
						<select id="team_level" name="team_level">
							<option value="">Не выбрано</option>
							<?php if ( ! is_wp_error($team_levels) ) { ?>
								<?php foreach ( $team_levels as $term ) { ?>
									<option value="<?php echo (int) $term->term_id; ?>" <?php selected($_POST['team_level'] ?? '', $term->term_id); ?>>
										<?php echo esc_html($term->name); ?>
									</option>
								<?php } ?>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="input-row">
					<div class="input-wrap">
						<label>Позиции</label>
						<?php $selected_positions = array_map('intval', (array) ($_POST['team_position'] ?? [])); ?>
						<div class="multi-select" data-placeholder="Не выбрано">
							<button type="button" class="multi-select__btn" aria-haspopup="listbox" aria-expanded="false">Не выбрано</button>
							<div class="multi-select__panel" role="listbox">
								<?php if ( ! is_wp_error($team_positions) ) { ?>
									<?php foreach ( $team_positions as $term ) { ?>
										<label class="multi-select__item">
											<input type="checkbox" name="team_position[]" value="<?php echo (int) $term->term_id; ?>" <?php echo in_array((int) $term->term_id, $selected_positions, true) ? 'checked' : ''; ?>>
											<span><?php echo esc_html($term->name); ?></span>
										</label>
									<?php } ?>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="input-wrap">
						<label for="player_btn_link">Ссылка на Telegram</label>
						<input type="url" id="player_btn_link" name="player_btn_link" placeholder="https://t.me/username" value="<?php echo esc_attr($_POST['player_btn_link'] ?? ''); ?>">
						<input type="hidden" name="player_btn_text" value="<?php echo esc_attr($_POST['player_btn_text'] ?? 'Написать'); ?>">
					</div>
				</div>

				<div class="input-row">
					<div class="input-wrap">
						<label for="phone">Телефон</label>
						<input type="text" id="phone" name="phone" placeholder="+7 (900) 000-00-00" value="<?php echo esc_attr($_POST['phone'] ?? ''); ?>">
					</div>
				</div>

				<div class="input-row input-row--message">
					<div class="input-wrap input-wrap--textarea">
						<label for="content">Описание</label>
						<textarea id="content" name="content" placeholder="Напишите, кого или что вы ищете, как играете и как с вами связаться."><?php echo esc_textarea($_POST['content'] ?? ''); ?></textarea>
					</div>

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
  var phoneInput = document.getElementById('phone');
  function formatPhone(value) {
    var digits = value.replace(/\D/g, '');
    if (!digits) return '';
    if (digits.charAt(0) === '8') digits = '7' + digits.slice(1);
    if (digits.charAt(0) !== '7') digits = '7' + digits;
    digits = digits.slice(0, 11);

    var result = '+7';
    if (digits.length > 1) result += ' (' + digits.slice(1, 4);
    if (digits.length >= 4) result += ')';
    if (digits.length > 4) result += ' ' + digits.slice(4, 7);
    if (digits.length > 7) result += '-' + digits.slice(7, 9);
    if (digits.length > 9) result += '-' + digits.slice(9, 11);
    return result;
  }
  if (phoneInput) {
    phoneInput.addEventListener('input', function () {
      phoneInput.value = formatPhone(phoneInput.value);
    });
    phoneInput.addEventListener('blur', function () {
      phoneInput.value = formatPhone(phoneInput.value);
    });
    if (phoneInput.value) {
      phoneInput.value = formatPhone(phoneInput.value);
    }
  }

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
