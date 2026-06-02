<?php get_header(); ?>

<?php if (have_posts()) {
	while (have_posts()) {
		the_post(); ?>
		<?php
		$icon_base = get_template_directory_uri() . '/assets/img/icons';
		$feature_icon_map = [
			'душ' => 'dush.svg',
			'инвентарь' => 'inventar.svg',
			'крыша' => 'krisha.svg',
			'парковка' => 'parking.svg',
			'раздевал' => 'razdevalki.svg',
			'свет' => 'svet.svg',
			'освещ' => 'svet.svg',
			'трибун' => 'tribuni.svg',
			'24 час' => 'vremya-raboti.svg',
			'время' => 'vremya-raboti.svg',
			'час' => 'vremya-raboti.svg',
		];
		?>

		<section class="section place-detail">
			<div class="place-detail__bg">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/img/cart-bg.png" alt="img">
			</div>
			<div class="container">
				<div class="place-detail__inner">
					<div class="place-detail__info">
						<div class="section-title"><?php the_title(); ?></div>

						<a href="https://t.me/nikanorov1" style="display: block;" target="_blank" class="place-detail__info-ask">
							Вы владалец? Напишите нам в телеграм 
						</a>

						<div class="place-detail__description">
							<?php the_content(); ?>
						</div>
						<div class="place-detail__address">
							<ul class="marked-list">

								<?php $get_meta = carbon_get_the_post_meta('place_address');
								if ($get_meta) { ?>

									<li>
										<div class="marked-list__icon">
											<img src="<?php echo esc_url($icon_base . '/location.svg'); ?>" alt="" />
										</div>
										<div class="marked-list__text">
											<?php echo $get_meta; ?>
										</div>
									</li>

								<?php } ?>

								<?php
								// Метро
								$terms = wp_get_post_terms(get_the_ID(), 'metro');
								$metro = [];
								if ($terms && ! empty($terms) && ! is_wp_error($terms)) {
									foreach ($terms as $term) {
										$metro[] = $term->name;
									}
								}
								?>

								<?php if (! empty($metro)) { ?>

									<li>
										<div class="marked-list__icon">
											<img src="<?php echo esc_url($icon_base . '/metro.svg'); ?>" alt="" />
										</div>
										<div class="marked-list__text">
											<?php echo implode(', ', $metro); ?>
										</div>
									</li>

								<?php } ?>

							</ul>
							<?php
							$phone = carbon_get_the_post_meta('phone');
							if ($phone) {
								$cleanPhone = str_replace(' ', '', $phone);
							}
							?>
							<?php

							if ($phone) {
							?>
								<a href="javascript:void(0)" class="primary-btn desktop-call" id="showPhoneBtn">Показать телефон</a>
								<a href="tel:<?= $cleanPhone ?>" class="primary-btn mobile-call">Позвонить</a>

								<script>
									document.addEventListener("DOMContentLoaded", function() {
										const showPhoneBtn = document.getElementById("showPhoneBtn");

										<?php if ($phone && $cleanPhone): ?>
											const phoneText = "<?= htmlspecialchars($phone) ?>";
											const phoneHref = "tel:<?= $cleanPhone ?>";
											let revealed = false;

											showPhoneBtn.addEventListener("click", function(e) {
												if (!revealed) {
													e.preventDefault();
													showPhoneBtn.textContent = phoneText;
													showPhoneBtn.setAttribute("href", phoneHref);
													revealed = true;
												}

											});
										<?php endif; ?>
									});
								</script>
							<?php

							}
							?>

						</div>

						<?php
						$features = [];

						// Цена
						$get_meta = carbon_get_the_post_meta('place_price');
						if ($get_meta) {
							$features[] = [
								'image' => $icon_base . '/price.svg',
								'text' => $get_meta,
							];
						}

						// Покрытие
						$terms = wp_get_post_terms(get_the_ID(), 'coating');
						if ($terms && ! empty($terms) && ! is_wp_error($terms)) {
							$data = [];

							foreach ($terms as $term) {
								$data[] = $term->name;
							}

							if (! empty($data)) {
								$features[] = [
									'image' => $icon_base . '/gazon.svg',
									'text' => implode(', ', $data),
								];
							}
						}

						// Формат игры
						$terms = wp_get_post_terms(get_the_ID(), 'game_format');
						if ($terms && ! empty($terms) && ! is_wp_error($terms)) {
							$data = [];

							foreach ($terms as $term) {
								$data[] = $term->name;
							}

							if (! empty($data)) {
								$features[] = [
									'image' => $icon_base . '/format.svg',
									'text' => implode(', ', $data),
								];
							}
						}

						// Размер поля
						$terms = wp_get_post_terms(get_the_ID(), 'field_size');
						if ($terms && ! empty($terms) && ! is_wp_error($terms)) {
							$data = [];

							foreach ($terms as $term) {
								$data[] = $term->name;
							}

							if (! empty($data)) {
								$features[] = [
									'image' => $icon_base . '/razmer-polya.svg',
									'text' => implode(', ', $data),
								];
							}
						}

						// Особенности
						$terms = wp_get_post_terms(get_the_ID(), 'features');
						if ($terms && ! empty($terms) && ! is_wp_error($terms)) {
							foreach ($terms as $term) {
								$term_image = carbon_get_term_meta($term->term_id, 'image');
								$term_name = $term->name;
								if (function_exists('mb_strtolower')) {
									$term_name = mb_strtolower($term_name, 'UTF-8');
								} else {
									$term_name = strtolower($term_name);
								}

								foreach ($feature_icon_map as $needle => $icon_file) {
									if (strpos($term_name, $needle) !== false) {
										$term_image = $icon_base . '/' . $icon_file;
										break;
									}
								}

								$features[] = [
									'image' => $term_image,
									'text' => $term->name,
								];
							}
						}
						?>

						<?php if (isset($features) && ! empty($features)) {
							$half = ceil(count($features) / 2); // Определяем точку раздела массива
							$counter = 0;
						?>

							<div class="place-detail__services-list desktop-list">
								<ul class="marked-list">

									<?php foreach ($features as $item) { ?>

										<?php if ($counter == $half && count($features) > 1) { ?>

								</ul>
								<ul class="marked-list">

								<?php } ?>

								<li>
									<div class="marked-list__icon">
										<?php echo (isset($item['svg']) ? $item['svg'] : (isset($item['image']) && ! empty($item['image']) ? '<img src="' . $item['image'] . '">' : '')); ?>
									</div>
									<div class="marked-list__text">
										<?php echo (isset($item['text']) ? $item['text'] : ''); ?>
									</div>
								</li>

							<?php $counter++;
									} ?>

								</ul>
							</div>

						<?php } ?>
								
					</div>


					<?php $get_meta = carbon_get_the_post_meta('place_gallery');
					if ($get_meta) { ?>

						<div class="place-detail__gallery">
							<div class="place-slider__wrapper">
								<div class="splide place-slider">
									<div class="splide__track">
										<ul class="splide__list">

											<?php foreach ($get_meta as $image) { ?>

												<li class="splide__slide">
														<a href="<?php echo wp_get_attachment_url($image); ?>" data-fancybox="gallery">
														<img src="<?php echo wp_get_attachment_url($image); ?>" alt="alt">
													</a>
												</li>

											<?php } ?>

										</ul>
									</div>
									<div class="splide__arrows splide__arrows--ltr">
										<button class="splide__arrow splide__arrow--prev" type="button" aria-label="Previous slide"
											aria-controls="splide01-track">
											<svg width="14" height="24" viewBox="0 0 14 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M12 22L2.89559 13.5728C1.87921 12.632 1.8938 11.0204 2.92704 10.0982L12 2" stroke="white" stroke-width="3" stroke-linecap="round" />
											</svg>
										</button>
										<button class="splide__arrow splide__arrow--next" type="button" aria-label="Next slide"
											aria-controls="splide01-track">
											<svg width="14" height="24" viewBox="0 0 14 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M2 2L11.1044 10.4272C12.1208 11.368 12.1062 12.9796 11.073 13.9018L2 22" stroke="white" stroke-width="3" stroke-linecap="round" />
											</svg>
										</button>
									</div>
								</div>
								<div class="splide thumb-place-slider">
									<div class="splide__track">
										<ul class="splide__list">

											<?php foreach ($get_meta as $image) { ?>

												<li class="splide__slide">
													<img src="<?php echo wp_get_attachment_url($image); ?>" alt="alt">
												</li>

											<?php } ?>

										</ul>
									</div>
								</div>
							</div>
						</div>

					<?php } ?>

					<?php if (isset($features) && ! empty($features)) {
						$half = ceil(count($features) / 2); // Определяем точку раздела массива
						$counter = 0;
					?>

						<div class="place-detail__services-list mobile-list">
							<ul class="marked-list">

								<?php foreach ($features as $item) { ?>

									<?php if ($counter == $half && count($features) > 1) { ?>

							</ul>
							<ul class="marked-list">

							<?php } ?>

							<li>
								<div class="marked-list__icon">
									<?php echo (isset($item['svg']) ? $item['svg'] : (isset($item['image']) && ! empty($item['image']) ? '<img src="' . $item['image'] . '">' : '')); ?>
								</div>
								<div class="marked-list__text">
									<?php echo (isset($item['text']) ? $item['text'] : ''); ?>
								</div>
							</li>

						<?php $counter++;
								} ?>

							</ul>
						</div>

					<?php } ?>

				</div>
			</div>
		</section>

<?php }
} ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
