<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

	<?php
	$selected_city = fballer_get_selected_city_id();
	$news_query_args = [
		'post_type' => 'news',
		'posts_per_page' => 12,
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC',
	];
	if ( $selected_city ) {
		$news_query_args['tax_query'] = [
			'relation' => 'OR',
			[
				'taxonomy' => 'city',
				'field' => 'term_id',
				'terms' => $selected_city,
			],
			[
				'taxonomy' => 'city',
				'operator' => 'NOT EXISTS',
			],
		];
	}
	$news_query = new WP_Query($news_query_args);
	$has_home_news = $news_query->have_posts();
	?>

	<section class="section hero-home<?php echo $has_home_news ? '' : ' hero-home--no-news'; ?>">
		<div class="hero-home__bg">
			<div class="hero-home__pattern" aria-hidden="true">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/img/ms-dec-new.svg" alt="">
			</div>
		</div>
		<div class="container">
			<div class="hero-home__inner">
				<div class="hero-home__content">
					<h1 class="hero-home__title">Найдите игру, команду или поле в вашем городе</h1>

					<div class="hero-home__actions">
						<a class="hero-home__card" href="#play">
							<span class="hero-home__card-icon" aria-hidden="true">
								<img src="<?php echo get_template_directory_uri(); ?>/assets/img/icons/inventar.svg" alt="">
							</span>
							<span class="hero-home__card-text">
								<span class="hero-home__card-title">ПОИГРАТЬ В ФУТБОЛ</span>
								<span class="hero-home__card-eyebrow">найти игру рядом</span>
							</span>
						</a>

						<a class="hero-home__card" href="#team">
							<span class="hero-home__card-icon" aria-hidden="true">
								<img src="<?php echo get_template_directory_uri(); ?>/assets/img/icons/team.svg" alt="">
							</span>
							<span class="hero-home__card-text">
								<span class="hero-home__card-title">НАЙТИ КОМАНДУ</span>
								<span class="hero-home__card-eyebrow">найти себе команду</span>
							</span>
						</a>

						<a class="hero-home__card" href="#players">
							<span class="hero-home__card-icon" aria-hidden="true">
								<img src="<?php echo get_template_directory_uri(); ?>/assets/img/icons/player.svg" alt="">
							</span>
							<span class="hero-home__card-text">
								<span class="hero-home__card-title">НАЙТИ ИГРОКА</span>
								<span class="hero-home__card-eyebrow">найти игрока в команду или на игру</span>
							</span>
						</a>

						<a class="hero-home__card" href="#place">
							<span class="hero-home__card-icon" aria-hidden="true">
								<img src="<?php echo get_template_directory_uri(); ?>/assets/img/icons/razmer-polya.svg" alt="">
							</span>
							<span class="hero-home__card-text">
								<span class="hero-home__card-title">НАЙТИ ПОЛЕ</span>
								<span class="hero-home__card-eyebrow">найти удобное поле</span>
							</span>
						</a>

						<a class="hero-home__card" href="#champ">
							<span class="hero-home__card-icon" aria-hidden="true">
								<img src="<?php echo get_template_directory_uri(); ?>/assets/img/icons/nagrada.svg" alt="">
							</span>
							<span class="hero-home__card-text">
								<span class="hero-home__card-title">НАЙТИ ЧЕМПИОНАТ</span>
								<span class="hero-home__card-eyebrow">найти чемпионат для команды</span>
							</span>
						</a>
					</div>

					<div class="hero-home__cta">
						<div class="hero-home__cta-label">или создайте свое объявление:</div>
						<div class="add-dropdown hero-home__cta-dropdown">
							<button type="button" class="hero-home__cta-button add-dropdown__toggle" aria-expanded="false">
								Разместить объявление <span aria-hidden="true">+</span>
							</button>
							<div class="add-dropdown__menu" role="menu">
								<a href="<?php echo esc_url(fballer_get_add_page_url('add-place', 'templates/template-add-place.php')); ?>" class="add-dropdown__item">Добавить поле</a>
								<a href="<?php echo esc_url(fballer_get_add_page_url('add-champ', 'templates/template-add-champ.php')); ?>" class="add-dropdown__item">Добавить чемпионат</a>
								<a href="<?php echo esc_url(fballer_get_add_page_url('add-game', 'templates/template-add-game.php')); ?>" class="add-dropdown__item">Добавить игру</a>
								<a href="<?php echo esc_url(fballer_get_add_page_url('add-team', 'templates/template-add-team.php')); ?>" class="add-dropdown__item">Команда ищет игрока</a>
								<a href="<?php echo esc_url(fballer_get_add_page_url('add-player', 'templates/template-add-player.php')); ?>" class="add-dropdown__item">Игрок ищет команду или игру</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
	?>

<?php if ( $news_query->have_posts() ) { ?>
	<section class="section home-news">
		<div class="container">
			<div class="section-title">Новости Fballer</div>
			<div class="home-news__wrap">
				<button class="home-news__nav home-news__nav--prev" type="button" aria-label="Назад"></button>
				<div class="home-news__items">
					<?php while ( $news_query->have_posts() ) : $news_query->the_post(); ?>
						<div class="home-news__item" role="button" tabindex="0" data-title="<?php the_title_attribute(); ?>" data-img="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>" data-content="<?php echo esc_attr(wp_strip_all_tags(get_the_excerpt() ?: wp_trim_words(get_the_content(), 28, '...'))); ?>" data-btn-enabled="<?php echo esc_attr(carbon_get_the_post_meta('news_btn_enabled')); ?>" data-btn-text="<?php echo esc_attr(carbon_get_the_post_meta('news_btn_text')); ?>" data-btn-link="<?php echo esc_attr(carbon_get_the_post_meta('news_btn_link')); ?>">
							<div class="home-news__image">
								<div class="home-news__thumb">
									<img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>" alt="<?php the_title_attribute(); ?>">
								</div>
								<div class="home-news__name"><?php the_title(); ?></div>
							</div>
						</div>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
				<button class="home-news__nav home-news__nav--next" type="button" aria-label="Вперёд"></button>
			</div>
		</div>
	</section>

	<div class="home-news-modal" aria-hidden="true">
		<div class="home-news-modal__overlay" data-close></div>
		<div class="home-news-modal__dialog" role="dialog" aria-modal="true">
			<div class="home-news-modal__progress"><span class="home-news-modal__progress-bar"></span></div>
			<button class="home-news-modal__close" type="button" aria-label="Закрыть" data-close>×</button>
			<button class="home-news-modal__nav home-news-modal__nav--prev" type="button" aria-label="Назад"></button>
			<button class="home-news-modal__nav home-news-modal__nav--next" type="button" aria-label="Вперёд"></button>
			<button class="home-news-modal__cta" type="button">Читать новость</button>
			<div class="home-news-modal__title"></div>
			<div class="home-news-modal__text"></div>
		</div>
	</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var wrap = document.querySelector('.home-news');
  if (!wrap) return;
  var list = wrap.querySelector('.home-news__items');
  var prev = wrap.querySelector('.home-news__nav--prev');
  var next = wrap.querySelector('.home-news__nav--next');

  if (list && prev && next) {
    var step = 220;
    prev.addEventListener('click', function (e) { e.preventDefault(); list.scrollBy({ left: -step, behavior: 'smooth' }); });
    next.addEventListener('click', function (e) { e.preventDefault(); list.scrollBy({ left: step, behavior: 'smooth' }); });
  }

  var modal = document.querySelector('.home-news-modal');
  if (!modal) return;
  var dialog = modal.querySelector('.home-news-modal__dialog');
  var titleEl = modal.querySelector('.home-news-modal__title');
  var textEl = modal.querySelector('.home-news-modal__text');
  var progress = modal.querySelector('.home-news-modal__progress-bar');
  var cta = modal.querySelector('.home-news-modal__cta');
  var modalPrev = modal.querySelector('.home-news-modal__nav--prev');
  var modalNext = modal.querySelector('.home-news-modal__nav--next');

  var items = Array.prototype.map.call(document.querySelectorAll('.home-news__item'), function (card) {
    return {
      title: card.getAttribute('data-title') || '',
      img: card.getAttribute('data-img') || '',
      content: card.getAttribute('data-content') || '',
      el: card,
    };
  });

  var current = 0;
  var timer = null;

  function setProgress() {
    if (!progress) return;
    progress.classList.remove('is-animating');
    void progress.offsetWidth;
    progress.classList.add('is-animating');
  }

  function show(index) {
    current = (index + items.length) % items.length;
    var item = items[current];
    dialog.style.backgroundImage = item.img ? 'url(' + item.img + ')' : 'none';
    titleEl.textContent = item.title;
    textEl.textContent = item.content;
    var btnEnabled = (item.el.getAttribute('data-btn-enabled') || '').toLowerCase();
    var btnText = item.el.getAttribute('data-btn-text') || 'Читать новость';
    var btnLink = item.el.getAttribute('data-btn-link') || '';
    if (cta) {
      if (btnEnabled === 'yes' || btnEnabled === '1' || btnEnabled === 'true') {
        cta.style.display = '';
        cta.textContent = btnText;
        cta.setAttribute('data-link', btnLink);
      } else {
        cta.style.display = 'none';
      }
    }
    setProgress();
    clearTimeout(timer);
    timer = setTimeout(function () { show(current + 1); }, 6000);
  }

  function openModal(startIndex) {
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    show(startIndex);
  }

  function closeModal() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    clearTimeout(timer);
  }

  if (modalPrev && modalNext) {
    modalPrev.addEventListener('click', function () { show(current - 1); });
    modalNext.addEventListener('click', function () { show(current + 1); });
  }

  if (cta) cta.addEventListener('click', function () {
    var link = cta.getAttribute('data-link');
    if (link) window.location.href = link;
  });

  items.forEach(function (item, idx) {
    item.el.addEventListener('click', function () { openModal(idx); });
    item.el.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openModal(idx); }
    });
  });

  modal.addEventListener('click', function (e) {
    if (e.target.hasAttribute('data-close') || e.target === modal) closeModal();
  });
  document.addEventListener('keydown', function (e) {
    if (!modal.classList.contains('is-open')) return;
    if (e.key === 'Escape') closeModal();
    if (e.key === 'ArrowLeft') show(current - 1);
    if (e.key === 'ArrowRight') show(current + 1);
  });

  var isDragging = false;
  var startX = 0;
  var scrollLeft = 0;

  function dragStart(e) {
    isDragging = true;
    startX = e.pageX - list.offsetLeft;
    scrollLeft = list.scrollLeft;
    list.classList.add('is-dragging');
  }

  function dragMove(e) {
    if (!isDragging) return;
    e.preventDefault();
    var x = e.pageX - list.offsetLeft;
    var walk = (x - startX) * 0.6;
    list.scrollLeft = scrollLeft - walk;
  }

  function dragEnd() {
    isDragging = false;
    list.classList.remove('is-dragging');
  }

  list.addEventListener('mousedown', dragStart);
  list.addEventListener('mousemove', dragMove);
  list.addEventListener('mouseup', dragEnd);
  list.addEventListener('mouseleave', dragEnd);
  list.addEventListener('touchstart', function (e) { dragStart(e.touches[0]); }, { passive: true });
  list.addEventListener('touchmove', function (e) { dragMove(e.touches[0]); }, { passive: false });
  list.addEventListener('touchend', dragEnd);
});
</script>

<?php } ?>

	<?php
	$ads_query_args = [
		'post_type' => 'ads',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC',
	];
	if ( $selected_city ) {
		$ads_query_args['tax_query'] = [
			'relation' => 'OR',
			[
				'taxonomy' => 'city',
				'field' => 'term_id',
				'terms' => $selected_city,
			],
			[
				'taxonomy' => 'city',
				'operator' => 'NOT EXISTS',
			],
		];
	}
	$ads_query = new WP_Query($ads_query_args);
	?>

	<?php if ( $ads_query->have_posts() ) { ?>
		<section class="section home-ads">
			<div class="container">
				<div class="home-ads__items" data-ads-carousel>
					<?php while ( $ads_query->have_posts() ) : $ads_query->the_post(); ?>
						<?php
						$desktop_id = (int) carbon_get_the_post_meta('ad_image_desktop');
						$tablet_id = (int) carbon_get_the_post_meta('ad_image_tablet');
						$mobile_id = (int) carbon_get_the_post_meta('ad_image_mobile');
						$ad_link = trim((string) carbon_get_the_post_meta('ad_link'));

						$fallback_id = $desktop_id ?: ($tablet_id ?: $mobile_id);
						if (! $desktop_id) $desktop_id = $fallback_id;
						if (! $tablet_id) $tablet_id = $desktop_id;
						if (! $mobile_id) $mobile_id = $tablet_id;
						$desktop_url = $desktop_id ? wp_get_attachment_image_url($desktop_id, 'full') : '';
						$tablet_url = $tablet_id ? wp_get_attachment_image_url($tablet_id, 'full') : $desktop_url;
						$mobile_url = $mobile_id ? wp_get_attachment_image_url($mobile_id, 'full') : $tablet_url;
						?>

						<?php if ( $desktop_url ) { ?>
						<div class="home-ad">
							<?php if ( $ad_link !== '' ) { ?>
								<a class="home-ad__link" href="<?php echo esc_url($ad_link); ?>">
							<?php } else { ?>
								<div class="home-ad__link">
							<?php } ?>
									<picture>
										<?php if ( $mobile_url ) { ?>
											<source media="(max-width: 670px)" srcset="<?php echo esc_url($mobile_url); ?>">
										<?php } ?>
										<?php if ( $tablet_url ) { ?>
											<source media="(max-width: 992px)" srcset="<?php echo esc_url($tablet_url); ?>">
										<?php } ?>
										<img class="home-ad__image" src="<?php echo esc_url($desktop_url); ?>" alt="<?php the_title_attribute(); ?>">
									</picture>
							<?php if ( $ad_link !== '' ) { ?>
								</a>
							<?php } else { ?>
								</div>
							<?php } ?>
						</div>
						<?php } ?>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</div>
		</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var ads = document.querySelector('[data-ads-carousel]');
  if (!ads) return;
  var items = ads.querySelectorAll('.home-ad');
  if (!items || items.length < 2) return;
  var index = 0;
  var interval = 6000;

  function goNext() {
    index = (index + 1) % items.length;
    ads.scrollTo({ left: ads.clientWidth * index, behavior: 'smooth' });
  }

  var timer = setInterval(goNext, interval);

  ads.addEventListener('mouseenter', function () { clearInterval(timer); });
  ads.addEventListener('mouseleave', function () { timer = setInterval(goNext, interval); });
});
</script>
	<?php } ?>

<?php $ajaxVariable = 'ajaxGamesData'; ?>

	<section class="section players" id="play">
		<div class="container ajax-posts" ajax-data="<?php echo $ajaxVariable; ?>">
			<div class="section-title">С кем поиграть?</div>

			<?php

	
			$selected_city = fballer_get_selected_city_id();

			// Пагинация
			$paged = (get_query_var('paged') ? get_query_var('paged') : 1);
			$posts_per_page = ($paged == 1 ? 7 : 4);

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
				'current_page' => max(1, get_query_var('paged')),
				'max_pages' => $query->max_num_pages,
			));
			?>

			<div class="player-items ajax-posts__items">
				<a href="/add-game/" class="player-item">

					<div class="add-wrapper">
						<svg width="82" height="82" viewBox="0 0 82 82" fill="none" xmlns="http://www.w3.org/2000/svg">
							<circle cx="41" cy="41" r="41" fill="#D9D9D9" />
							<path d="M39.644 49.4981V31.1021H43.172V49.4981H39.644ZM32 41.9801V38.6621H50.816V41.9801H32Z" fill="white" />
						</svg>
						<div class="add-text">Добавить свою игру</div>
					</div>
				</a>

				<?php if ($query->have_posts()) { ?>
					<?php
					while ( $query->have_posts() ) {
						$query->the_post();
						get_template_part('templates/content-games-item');
					}
					wp_reset_postdata();
					?>
				<?php } ?>

			</div>

			<?php if ($query->max_num_pages > 1) { ?>

				<a href="/poisk-matchey" id="load-more" class="primary-btn load-more">Показать все</a>

			<?php } ?>
		</div>
	</section>

	<?php $ajaxVariable = 'ajaxTeamsData'; ?>

	<section class="section players players--teams" id="team">
		<div class="container ajax-posts" ajax-data="<?php echo $ajaxVariable; ?>">
			<div class="section-title">Команды ищут игроков</div>

			<?php
			// Пагинация
			$paged = ( get_query_var('paged') ? get_query_var('paged') : 1 );
			$posts_per_page = ($paged == 1 ? 7 : 4);
			$selected_city = fballer_get_selected_city_id();

			$query_args = fballer_apply_recent_teams_query_args(array(
				'post_type' => 'teams',
				'posts_per_page' => $posts_per_page,
				'paged' => $paged,
				'orderby' => [
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
				'current_page' => max(1, get_query_var('paged')),
				'max_pages' => $query->max_num_pages,
			));
			?>

			<div class="player-items ajax-posts__items">
				<a href="<?php echo esc_url(fballer_get_add_page_url('add-team', 'templates/template-add-team.php')); ?>" class="player-item player-item--team">

					<div class="add-wrapper">
						<svg width="82" height="82" viewBox="0 0 82 82" fill="none" xmlns="http://www.w3.org/2000/svg">
							<circle cx="41" cy="41" r="41" fill="#D9D9D9" />
							<path d="M39.644 49.4981V31.1021H43.172V49.4981H39.644ZM32 41.9801V38.6621H50.816V41.9801H32Z" fill="white" />
						</svg>
						<div class="add-text">Команда ищет игрока</div>
					</div>
				</a>

				<?php if ( $query->have_posts() ) { ?>
					<?php
					while ( $query->have_posts() ) {
						$query->the_post();
						get_template_part('templates/content-teams-item');
					}
					wp_reset_postdata();
					?>
				<?php } ?>

			</div>

			<div class="places-buttons">
				<?php if ( $query->max_num_pages > 1 ) { ?>

					<a href="/teams/" class="primary-btn load-more">Показать все</a>

				<?php } ?>

				<a href="<?php echo esc_url(fballer_get_add_page_url('add-team', 'templates/template-add-team.php')); ?>" class="primary-btn load-more load-more--add">Команда ищет игрока</a>
			</div>
		</div>
	</section>

	<?php $ajaxVariable = 'ajaxPlayersData'; ?>

	<section class="section players players--players" id="players">
		<div class="container ajax-posts" ajax-data="<?php echo $ajaxVariable; ?>">
			<div class="section-title">Игроки ищут команду или игру</div>

			<?php
			$paged = ( get_query_var('paged') ? get_query_var('paged') : 1 );
			$posts_per_page = ($paged == 1 ? 7 : 4);
			$selected_city = fballer_get_selected_city_id();

			$query = new WP_Query([
				'post_type' => 'players',
				'posts_per_page' => $posts_per_page,
				'paged' => $paged,
				'orderby' => [
					'post_date' => 'DESC',
				],
				'tax_query' => [
					[
						'taxonomy' => 'city',
						'field' => 'term_id',
						'terms' => $selected_city,
					],
				],
			]);

			wp_localize_script('ajax-posts', $ajaxVariable, array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'query_vars' => json_encode($query->query_vars),
				'current_page' => max(1, get_query_var('paged')),
				'max_pages' => $query->max_num_pages,
			));
			?>

			<div class="player-items ajax-posts__items">
				<a href="<?php echo esc_url(fballer_get_add_page_url('add-player', 'templates/template-add-player.php')); ?>" class="player-item player-item--player">
					<div class="add-wrapper">
						<svg width="82" height="82" viewBox="0 0 82 82" fill="none" xmlns="http://www.w3.org/2000/svg">
							<circle cx="41" cy="41" r="41" fill="#D9D9D9" />
							<path d="M39.644 49.4981V31.1021H43.172V49.4981H39.644ZM32 41.9801V38.6621H50.816V41.9801H32Z" fill="white" />
						</svg>
						<div class="add-text">Игрок ищет команду или игру</div>
					</div>
				</a>

				<?php if ( $query->have_posts() ) { ?>
					<?php
					while ( $query->have_posts() ) {
						$query->the_post();
						get_template_part('templates/content-players-item');
					}
					wp_reset_postdata();
					?>
				<?php } ?>
			</div>

			<div class="places-buttons">
				<?php if ( $query->max_num_pages > 1 ) { ?>
					<a href="<?php echo esc_url(fballer_get_archive_page_url('players', 'templates/template-players.php')); ?>" class="primary-btn load-more">Показать все</a>
				<?php } ?>

				<a href="<?php echo esc_url(fballer_get_add_page_url('add-player', 'templates/template-add-player.php')); ?>" class="primary-btn load-more load-more--add">Добавить объявление</a>
			</div>
		</div>
	</section>

	<?php $ajaxVariable = 'ajaxPlacesData'; ?>

	<section class="section places" id="place">
		<div class="container ajax-posts" ajax-data="<?php echo $ajaxVariable; ?>">
			<div class="section-title">Поля, поляны,<br>спорткомплексы</div>

			<?php
			// Пагинация
			$paged = (get_query_var('paged') ? get_query_var('paged') : 1);
			$posts_per_page = 4;

			// Запрос для получения постов
			$query = new WP_Query(array(
				'post_type' => 'places',
				'posts_per_page' => $posts_per_page,
				'paged' => $paged,
				'tax_query' => array(
					array(
						'taxonomy' => 'city',
						'field' => 'term_id',
						'terms' => $_COOKIE['selected_city'],
					),
				),
			));

			wp_localize_script('ajax-posts', $ajaxVariable, array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'query_vars' => json_encode($query->query_vars),
				'current_page' => max(1, get_query_var('paged')),
				'max_pages' => $query->max_num_pages,
			));
			?>

			<div class="places-items ajax-posts__items">

				<?php if ( $query->have_posts() ) { ?>
					<?php
					while ($query->have_posts()) {
						$query->the_post();
						get_template_part('templates/content-places-item');
					}
					wp_reset_postdata();
					?>
				<?php } ?>

			</div>

			<div class="places-buttons">
				
				<?php if ( $query->max_num_pages > 1 ) { ?>

					<a href="/places/" id="load-more" class="primary-btn load-more">Показать все</a>

				<?php } ?>
				
				<a href="/add-place/" class="primary-btn load-more load-more--add">Добавить поле</a>
			</div>
					</div>
	</section>

	<?php $ajaxVariable = 'ajaxChampsData'; ?>

	<section class="section championship" id="champ">
		<div class="container ajax-posts" ajax-data="<?php echo $ajaxVariable; ?>">
			<div class="section-title">Чемпионаты</div>

			<?php
			// Пагинация
			$paged = (get_query_var('paged') ? get_query_var('paged') : 1);
			$posts_per_page = ($paged == 1 ? 5 : 3);

			// Запрос для получения постов
			$query = new WP_Query(array(
				'post_type' => 'champs',
				'posts_per_page' => $posts_per_page,
				'paged' => $paged,
				'orderby' => [
					'meta_value_num' => 'ASC',
					'post_date' => 'DESC',
				],
				'meta_query' => array(
					array(
						'key' => 'champ_start',
						'compare' => 'EXISTS',
					),
				),
				'tax_query' => array(
					array(
						'taxonomy' => 'city',
						'field' => 'term_id',
						'terms' => $_COOKIE['selected_city'],
					),
				),
			));

			wp_localize_script('ajax-posts', $ajaxVariable, array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'query_vars' => json_encode($query->query_vars),
				'current_page' => max(1, get_query_var('paged')),
				'max_pages' => $query->max_num_pages,
				'is_home' => true,
			));
			?>

			<div class="championship-items ajax-posts__items">
				<a href="/add-champ/" class="championship-item">
					<div class="add-wrapper">
						<svg width="82" height="82" viewBox="0 0 82 82" fill="none" xmlns="http://www.w3.org/2000/svg">
							<circle cx="41" cy="41" r="41" fill="#D9D9D9"></circle>
							<path d="M39.644 49.4981V31.1021H43.172V49.4981H39.644ZM32 41.9801V38.6621H50.816V41.9801H32Z" fill="white"></path>
						</svg>
						<div class="add-text">Добавить чемпионат</div>
					</div>
				</a>

				<?php if ($query->have_posts()) { ?>
					<?php
					while ($query->have_posts()) {
						$query->the_post();
						get_template_part('templates/content-champs-item');
					}
					wp_reset_postdata();
					?>
				<?php } ?>

			</div>

			<div class="places-buttons">
				<?php if ($query->max_num_pages > 1) { ?>

					<a href="/champs/" class="primary-btn load-more">Показать все</a>

				<?php } ?>

				<a href="/add-champ/" class="primary-btn load-more load-more--add">Добавить чемпионат</a>
			</div>
		</div>
	</section>

<?php endwhile; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
