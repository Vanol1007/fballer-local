<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php if (! has_site_icon()) { ?>
		<link rel="shortcut icon" href="<?php echo get_site_url(); ?>/favicon.ico" type="image/x-icon" />
	<?php } ?>
	<title><?php wp_title('|', true, 'right'); ?></title>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<header class="site-header">
		<nav class="site-nav">
			<div class="container">
				<div class="nav-inner">
					<a href="<?php echo get_site_url(); ?>" class="logo">
						<?php
						$is_main = ( is_front_page() || is_home() );
						$logo = $is_main
							? get_template_directory_uri() . '/assets/img/fbwhlogo.svg'
							: get_template_directory_uri() . '/assets/img/fbbllogo.svg';
						$sticky_logo = get_template_directory_uri() . '/assets/img/fbbllogo.svg';
						$mobile_logo = $is_main
							? get_template_directory_uri() . '/assets/img/fbbllogo.svg'
							: $logo;
						?>
						<img class="logo-desktop" src="<?php echo $logo; ?>" data-default-src="<?php echo $logo; ?>" data-sticky-src="<?php echo $sticky_logo; ?>" alt="logo" />
						
						<img class="logo-mobile" src="<?php echo $mobile_logo; ?>" data-default-src="<?php echo $mobile_logo; ?>" data-sticky-src="<?php echo $mobile_logo; ?>" alt="logo" />
					</a>

					<?php
					$cities = get_terms(array(
						'taxonomy' => 'city',
						'fields' => 'all', // ids, names, count, id=>parent
						'hide_empty' => false,
						'parent' => 0,
					));
					?>
					
					<?php if ( ! is_wp_error($cities) && ! empty($cities) ) { ?>

						<div class="town-select desktop-select">
							<span></span>
							<svg width="9" height="8" viewBox="0 0 9 8" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M4.56787 7.63464L0.670757 0.884643L8.46498 0.884643L4.56787 7.63464Z" fill="black" />
							</svg>

							<div class="town-select__info">
								<div class="town-select__info-wrapper">
									<p> Вы в городе <span></span>?</p>
									<div class="town-select__info-buttons">
										<a href="#" class="primary-btn town-select__info-button town-select__info-button--black">Да</a>
										<a href="#" class="primary-btn town-select__info-button town-select__info-button--white">Выбрать другой город</a>
									</div>
								</div>
							</div>
							
							<div class="town-select__list">
								<div class="town-select__list-wrapper">
						
									<?php foreach ( $cities as $term ) { ?>
									
										<div class="town-select__item" data-id="<?php echo $term->term_id; ?>">
											<?php echo $term->name; ?>
										</div>
									
									<?php } ?>
								
								</div>
							</div>
						</div>
						
					<?php } ?>
					
					<?php // Меню дефолтное
					$args = array(
						'theme_location' => 'menu-header',
						'container' => '',
						'menu_class' => 'header-menu',
						'depth' => 0,
						'fallback_cb' => '',
					);
					wp_nav_menu($args);
					?>
					
					<div class="menu-right">
						<a href="https://t.me/fba11er" class="social-link" target="_blank" rel="nofollow noopener">
						
							<?php if ( is_front_page() || is_home() ) { ?>
						
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 29 24"><path fill="#fff" d="M27.089 0c.21 0 .71.053 1.025.315.263.21.342.5.369.71-.027.158.026.631 0 1-.395 4.154-2.103 14.25-2.971 18.905-.368 1.971-1.105 2.63-1.788 2.709-1.525.131-2.683-1-4.155-1.973-2.314-1.525-3.629-2.472-5.864-3.944-2.603-1.71-.92-2.656.578-4.181.395-.394 7.127-6.521 7.258-7.073a.527.527 0 0 0-.132-.473c-.157-.131-.367-.079-.551-.053-.237.053-3.919 2.498-11.097 7.336-1.052.71-1.999 1.078-2.84 1.052-.947-.026-2.734-.526-4.075-.973-1.657-.525-2.945-.815-2.84-1.735.053-.473.71-.947 1.945-1.446 7.678-3.34 12.78-5.548 15.33-6.6C24.591.526 26.09 0 27.09 0Z"/></svg>
								
							<?php } else { ?>
						
								<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 0C6.72 0 0 6.72 0 15C0 23.28 6.72 30 15 30C23.28 30 30 23.28 30 15C30 6.72 23.28 0 15 0ZM21.96 10.2C21.735 12.57 20.76 18.33 20.265 20.985C20.055 22.11 19.635 22.485 19.245 22.53C18.375 22.605 17.715 21.96 16.875 21.405C15.555 20.535 14.805 19.995 13.53 19.155C12.045 18.18 13.005 17.64 13.86 16.77C14.085 16.545 17.925 13.05 18 12.735C18.0104 12.6873 18.009 12.6378 17.996 12.5907C17.9829 12.5437 17.9585 12.5005 17.925 12.465C17.835 12.39 17.715 12.42 17.61 12.435C17.475 12.465 15.375 13.86 11.28 16.62C10.68 17.025 10.14 17.235 9.66 17.22C9.12 17.205 8.1 16.92 7.335 16.665C6.39 16.365 5.655 16.2 5.715 15.675C5.745 15.405 6.12 15.135 6.825 14.85C11.205 12.945 14.115 11.685 15.57 11.085C19.74 9.345 20.595 9.045 21.165 9.045C21.285 9.045 21.57 9.075 21.75 9.225C21.9 9.345 21.945 9.51 21.96 9.63C21.945 9.72 21.975 9.99 21.96 10.2Z" fill="#E52F24" /></svg>
							
							<?php } ?>
							
						</a>

						<div class="add-dropdown" style="margin-left: auto;">
							<button type="button" class="primary-btn add-dropdown__toggle" aria-expanded="false">
								<?php echo ( is_front_page() || is_home() ) ? 'Разместить объявление' : 'Добавить'; ?>
							</button>
							<div class="add-dropdown__menu" role="menu">
								<a href="<?php echo esc_url(fballer_get_add_page_url('add-place', 'templates/template-add-place.php')); ?>" class="add-dropdown__item">Добавить поле</a>
								<a href="<?php echo esc_url(fballer_get_add_page_url('add-champ', 'templates/template-add-champ.php')); ?>" class="add-dropdown__item">Добавить чемпионат</a>
								<a href="<?php echo esc_url(fballer_get_add_page_url('add-game', 'templates/template-add-game.php')); ?>" class="add-dropdown__item">Добавить игру</a>
								<a href="<?php echo esc_url(fballer_get_add_page_url('add-team', 'templates/template-add-team.php')); ?>" class="add-dropdown__item">Команда ищет игрока</a>
								<a href="<?php echo esc_url(fballer_get_add_page_url('add-player', 'templates/template-add-player.php')); ?>" class="add-dropdown__item">Игрок ищет команду или игру</a>
							</div>
						</div>

						<div class="menu-hamburger menu-hamburger--tablet">
							<span></span>
							<span></span>
							<span></span>
						</div>
					</div>
					
					<div class="menu-right menu-right--mobile">
						<div class="menu-hamburger">
							<span></span>
							<span></span>
							<span></span>
						</div>
					</div>

					<?php if ( ! is_wp_error($cities) && ! empty($cities) ) { ?>
						<div class="town-select mobile-select mobile-select--inline">
							<span></span>
							<svg width="9" height="8" viewBox="0 0 9 8" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M4.56787 7.63464L0.670757 0.884643L8.46498 0.884643L4.56787 7.63464Z" fill="black" />
							</svg>
							
							<div class="town-select__info">
								<div class="town-select__info-wrapper">
									<p> Вы в городе <span></span>?</p>
									<div class="town-select__info-buttons">
										<a href="#" class="primary-btn town-select__info-button town-select__info-button--black">Да</a>
										<a href="#" class="primary-btn town-select__info-button town-select__info-button--white">Выбрать другой город</a>
									</div>
								</div>
							</div>
							
							<div class="town-select__list">
								<div class="town-select__list-wrapper">
									<?php foreach ( $cities as $term ) { ?>
										<div class="town-select__item" data-id="<?php echo $term->term_id; ?>">
											<?php echo $term->name; ?>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					<?php } ?>
					
				</div>

				<?php if ( ! is_wp_error($cities) && ! empty($cities) ) { ?>
				
					<div class="town-select mobile-select">
						<span></span>
						<svg width="9" height="8" viewBox="0 0 9 8" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M4.56787 7.63464L0.670757 0.884643L8.46498 0.884643L4.56787 7.63464Z" fill="black" />
						</svg>
						
						<div class="town-select__info">
							<div class="town-select__info-wrapper">
								<p> Вы в городе <span></span>?</p>
								<div class="town-select__info-buttons">
									<a href="#" class="primary-btn town-select__info-button town-select__info-button--black">Да</a>
									<a href="#" class="primary-btn town-select__info-button town-select__info-button--white">Выбрать другой город</a>
								</div>
							</div>
						</div>
						
						<div class="town-select__list">
							<div class="town-select__list-wrapper">
					
								<?php foreach ( $cities as $term ) { ?>
								
									<div class="town-select__item" data-id="<?php echo $term->term_id; ?>">
										<?php echo $term->name; ?>
									</div>
								
								<?php } ?>
							
							</div>
						</div>
					</div>
				
				<?php } ?>
				
				<div class="menu-mobile">
						<div class="container">
							<div class="nav-inner">
								<a href="<?php echo get_site_url(); ?>" class="logo">
									<img class="logo-mobile" src="<?php echo get_template_directory_uri() . '/assets/img/fbwhlogo.svg'; ?>" alt="logo" />
								</a>
								
								<div class="menu-right menu-right--mobile">
									<a href="https://t.me/fba11er" class="social-link" target="_blank" rel="nofollow noopener">
										<svg width="29" height="24" viewBox="0 0 29 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M27.089 0c.21 0 .71.053 1.025.315.263.21.342.5.369.71-.027.158.026.631 0 1-.395 4.154-2.103 14.25-2.971 18.905-.368 1.971-1.105 2.63-1.788 2.709-1.525.131-2.683-1-4.155-1.973-2.314-1.525-3.629-2.472-5.864-3.944-2.603-1.71-.92-2.656.578-4.181.395-.394 7.127-6.521 7.258-7.073a.527.527 0 0 0-.132-.473c-.157-.131-.367-.079-.551-.053-.237.053-3.919 2.498-11.097 7.336-1.052.71-1.999 1.078-2.84 1.052-.947-.026-2.734-.526-4.075-.973-1.657-.525-2.945-.815-2.84-1.735.053-.473.71-.947 1.945-1.446 7.678-3.34 12.78-5.548 15.33-6.6C24.591.526 26.09 0 27.09 0Z" fill="#E52F24"/></svg>
									</a>

									<div class="menu-mobile__close">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M28.941 31.786L.613 60.114a2.014 2.014 0 1 0 2.848 2.849l28.541-28.541 28.541 28.541c.394.394.909.59 1.424.59a2.014 2.014 0 0 0 1.424-3.439L35.064 31.786 63.41 3.438A2.014 2.014 0 1 0 60.562.589L32.003 29.15 3.441.59A2.015 2.015 0 0 0 .593 3.439l28.348 28.347z"></path>
										</svg>
									</div>
								</div>
							</div>

							<?php if ( ! is_wp_error($cities) && ! empty($cities) ) { ?>
							
								<div class="town-select mobile-select">
									<span></span>
									<svg width="9" height="8" viewBox="0 0 9 8" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M4.56787 7.63464L0.670757 0.884643L8.46498 0.884643L4.56787 7.63464Z" fill="black" />
									</svg>
									
									<div class="town-select__info">
										<div class="town-select__info-wrapper">
											<p> Вы в городе <span></span>?</p>
											<div class="town-select__info-buttons">
												<a href="#" class="primary-btn town-select__info-button town-select__info-button--black">Да</a>
												<a href="#" class="primary-btn town-select__info-button town-select__info-button--white">Выбрать другой город</a>
											</div>
										</div>
									</div>
									
									<div class="town-select__list">
										<div class="town-select__list-wrapper">
								
											<?php foreach ( $cities as $term ) { ?>
											
												<div class="town-select__item" data-id="<?php echo $term->term_id; ?>">
													<?php echo $term->name; ?>
												</div>
											
											<?php } ?>
										
										</div>
									</div>
								</div>
							
							<?php } ?>

							<?php // Меню дефолтное
							$args = array(
								'theme_location' => 'menu-header',
								'container' => '',
								'menu_class' => 'header-menu',
								'depth' => 0,
								'fallback_cb' => '',
							);
							wp_nav_menu($args);
							?>

							<div class="add-dropdown add-dropdown--mobile">
								<button type="button" class="primary-btn add-dropdown__toggle" aria-expanded="false">
									Добавить
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
		</nav>
	</header>
	<main>
		<?php if (function_exists('theme_breadcrumbs')) echo theme_breadcrumbs(); ?>
