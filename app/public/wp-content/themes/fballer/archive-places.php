<?php get_header(); ?>

<?php
global $wp_query;
$ajaxVariable = 'ajaxPlacesData';

// wp_enqueue_script( 'theme-search', get_template_directory_uri() . '/assets/js/search.js', array(), filemtime(get_template_directory() . '/assets/js/search.js'), true );

wp_localize_script('ajax-posts', $ajaxVariable, array(
	'ajaxurl' => admin_url('admin-ajax.php'),
	'query_vars' => json_encode($wp_query->query_vars),
	'current_page' => max( 1, get_query_var('paged') ),
	'max_pages' => $wp_query->max_num_pages,
	// 'posts_per_page' => get_option('posts_per_page'),
));
?>

<section class="section place-detail">
	<div class="place-detail__bg">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/img/cart-bg.png" alt="img">
	</div>
	<div class="container">
		<div class="search-inner">
			<div class="container ajax-posts" ajax-data="<?php echo $ajaxVariable; ?>">
				<div class="section-title"><?php echo get_the_archive_title(); ?></div>

				<?php if ( $value = get_theme_mod('custom_content_archive_places') ) { ?>
				
					<div class="section-desctiption">
						<?php echo apply_filters('the_content', $value); ?>
					</div>
					
				<?php } ?>
				
				<div class="search-filter__row">
					<!-- <button class="apply-filter" data-target="filter-1">
						<svg width="13" height="14" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.05664 1.55704C3.84115 1.55704 3.63449 1.63896 3.48212 1.78478C3.32974 1.9306 3.24414 2.12838 3.24414 2.3346C3.24414 2.54083 3.32974 2.7386 3.48212 2.88443C3.63449 3.03025 3.84115 3.11217 4.05664 3.11217C4.27213 3.11217 4.47879 3.03025 4.63116 2.88443C4.78354 2.7386 4.86914 2.54083 4.86914 2.3346C4.86914 2.12838 4.78354 1.9306 4.63116 1.78478C4.47879 1.63896 4.27213 1.55704 4.05664 1.55704ZM1.75727 1.55704C1.92513 1.10175 2.23643 0.707495 2.64825 0.428625C3.06008 0.149756 3.55215 0 4.05664 0C4.56113 0 5.0532 0.149756 5.46503 0.428625C5.87685 0.707495 6.18815 1.10175 6.35602 1.55704H12.1816C12.3971 1.55704 12.6038 1.63896 12.7562 1.78478C12.9085 1.9306 12.9941 2.12838 12.9941 2.3346C12.9941 2.54083 12.9085 2.7386 12.7562 2.88443C12.6038 3.03025 12.3971 3.11217 12.1816 3.11217H6.35602C6.18815 3.56746 5.87685 3.96171 5.46503 4.24058C5.0532 4.51945 4.56113 4.66921 4.05664 4.66921C3.55215 4.66921 3.06008 4.51945 2.64825 4.24058C2.23643 3.96171 1.92513 3.56746 1.75727 3.11217H0.806641C0.591152 3.11217 0.38449 3.03025 0.232117 2.88443C0.0797432 2.7386 -0.00585938 2.54083 -0.00585938 2.3346C-0.00585938 2.12838 0.0797432 1.9306 0.232117 1.78478C0.38449 1.63896 0.591152 1.55704 0.806641 1.55704H1.75727ZM8.93164 6.22243C8.71615 6.22243 8.50949 6.30436 8.35712 6.45018C8.20474 6.596 8.11914 6.79378 8.11914 7C8.11914 7.20622 8.20474 7.404 8.35712 7.54982C8.50949 7.69564 8.71615 7.77757 8.93164 7.77757C9.14713 7.77757 9.35379 7.69564 9.50616 7.54982C9.65854 7.404 9.74414 7.20622 9.74414 7C9.74414 6.79378 9.65854 6.596 9.50616 6.45018C9.35379 6.30436 9.14713 6.22243 8.93164 6.22243ZM6.63227 6.22243C6.80013 5.76714 7.11143 5.37289 7.52325 5.09402C7.93508 4.81515 8.42715 4.6654 8.93164 4.6654C9.43613 4.6654 9.9282 4.81515 10.34 5.09402C10.7519 5.37289 11.0632 5.76714 11.231 6.22243H12.1816C12.3971 6.22243 12.6038 6.30436 12.7562 6.45018C12.9085 6.596 12.9941 6.79378 12.9941 7C12.9941 7.20622 12.9085 7.404 12.7562 7.54982C12.6038 7.69564 12.3971 7.77757 12.1816 7.77757H11.231C11.0632 8.23286 10.7519 8.62711 10.34 8.90598C9.9282 9.18485 9.43613 9.3346 8.93164 9.3346C8.42715 9.3346 7.93508 9.18485 7.52325 8.90598C7.11143 8.62711 6.80013 8.23286 6.63227 7.77757H0.806641C0.591152 7.77757 0.38449 7.69564 0.232117 7.54982C0.0797432 7.404 -0.00585938 7.20622 -0.00585938 7C-0.00585938 6.79378 0.0797432 6.596 0.232117 6.45018C0.38449 6.30436 0.591152 6.22243 0.806641 6.22243H6.63227ZM4.05664 10.8878C3.84115 10.8878 3.63449 10.9698 3.48212 11.1156C3.32974 11.2614 3.24414 11.4592 3.24414 11.6654C3.24414 11.8716 3.32974 12.0694 3.48212 12.2152C3.63449 12.361 3.84115 12.443 4.05664 12.443C4.27213 12.443 4.47879 12.361 4.63116 12.2152C4.78354 12.0694 4.86914 11.8716 4.86914 11.6654C4.86914 11.4592 4.78354 11.2614 4.63116 11.1156C4.47879 10.9698 4.27213 10.8878 4.05664 10.8878ZM1.75727 10.8878C1.92513 10.4325 2.23643 10.0383 2.64825 9.75942C3.06008 9.48055 3.55215 9.33079 4.05664 9.33079C4.56113 9.33079 5.0532 9.48055 5.46503 9.75942C5.87685 10.0383 6.18815 10.4325 6.35602 10.8878H12.1816C12.3971 10.8878 12.6038 10.9698 12.7562 11.1156C12.9085 11.2614 12.9941 11.4592 12.9941 11.6654C12.9941 11.8716 12.9085 12.0694 12.7562 12.2152C12.6038 12.361 12.3971 12.443 12.1816 12.443H6.35602C6.18815 12.8983 5.87685 13.2925 5.46503 13.5714C5.0532 13.8502 4.56113 14 4.05664 14C3.55215 14 3.06008 13.8502 2.64825 13.5714C2.23643 13.2925 1.92513 12.8983 1.75727 12.443H0.806641C0.591152 12.443 0.38449 12.361 0.232117 12.2152C0.0797432 12.0694 -0.00585938 11.8716 -0.00585938 11.6654C-0.00585938 11.4592 0.0797432 11.2614 0.232117 11.1156C0.38449 10.9698 0.591152 10.8878 0.806641 10.8878H1.75727Z" fill="white" /></svg>
						<span>Фильтр</span>
					</button> -->
					<div class="filter-chiose__items active" id="filter-1">
					
						<?php
						$terms = get_terms(array(
							'taxonomy' => 'district',
							'fields' => 'all', // ids, names, count, id=>parent
							'hide_empty' => false,
						));
						?>

						<?php if ( ! is_wp_error($terms) && ! empty($terms) ) { ?>

							<div class="input-wrap filter-chiose__item lg">
								<label for="district">Район</label>
								<select name="district" id="district">
									<option value="" selected>Все</option>
								
									<?php foreach ( $terms as $term ) { ?>

										<option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
								
									<?php } ?>

								</select>
							</div>
							
						<?php } ?>
						
						<?php
						$terms = get_terms(array(
							'taxonomy' => 'field_size',
							'fields' => 'all', // ids, names, count, id=>parent
							'hide_empty' => false,
						));
						?>

						<?php if ( ! is_wp_error($terms) && ! empty($terms) ) { ?>

							<div class="input-wrap filter-chiose__item lg">
								<label for="field_size">Размер поля</label>
								<select name="field_size" id="field_size">
									<option value="" selected>Все</option>
								
									<?php foreach ( $terms as $term ) { ?>

										<option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
								
									<?php } ?>

								</select>
							</div>
							
						<?php } ?>
						<!-- Цена -->
						<div class="input-wrap filter-chiose__item lg">
							<label for="price">Цена за игру</label>
							<select name="f6" id="price"> <!-- Измените name на f6 для consistency -->
								<option value="">Все</option>
								<option value="5000" <?php selected($_GET['f6'] ?? '', '5000'); ?>>до 5000 руб</option>
								<option value="10000" <?php selected($_GET['f6'] ?? '', '10000'); ?>>до 10000 руб</option>
								<option value="20000" <?php selected($_GET['f6'] ?? '', '20000'); ?>>до 20000 руб</option>
								<option value="50000" <?php selected($_GET['f6'] ?? '', '50000'); ?>>до 50000 руб</option>
							</select>
						</div>

						<!--
						<div class="input-wrap filter-chiose__item lg">
							<label for="place-email">Цена</label>
							<select name="format" id="format">
								<option value="5x5">от 5000 руб/ч</option>
								<option value="5x5">от 5000 руб/ч</option>
								<option value="5x5">от 5000 руб/ч</option>
							</select>
						</div>
						<div class="input-wrap filter-chiose__item">
							<label for="place-address">Тип поля</label>
							<select name="format" id="format">
								<option value="5x5">Крытое</option>
								<option value="5x5">Крытое</option>
								<option value="5x5">Крытое</option>
							</select>
						</div>
						-->
						
						<?php
						$terms = get_terms(array(
							'taxonomy' => 'coating',
							'fields' => 'all', // ids, names, count, id=>parent
							'hide_empty' => false,
						));
						?>

						<?php if ( ! is_wp_error($terms) && ! empty($terms) ) { ?>

							<div class="input-wrap filter-chiose__item lg">
								<label for="coating">Покрытие</label>
								<select name="coating" id="coating">
									<option value="" selected>Все</option>
								
									<?php foreach ( $terms as $term ) { ?>

										<option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
								
									<?php } ?>

								</select>
							</div>
							
						<?php } ?>

						<div class="input-wrap filter-chiose__item">
							<button class="primary-btn filter-btn">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 13 14"><path fill="#fff" d="M4.057 1.557a.831.831 0 0 0-.575.228.761.761 0 0 0-.238.55c0 .206.086.404.238.55a.831.831 0 0 0 .575.227.831.831 0 0 0 .574-.228.761.761 0 0 0 .238-.55.761.761 0 0 0-.238-.55.831.831 0 0 0-.574-.227Zm-2.3 0c.168-.455.48-.85.891-1.128A2.514 2.514 0 0 1 4.057 0c.504 0 .996.15 1.408.429.412.278.723.673.891 1.128h5.826c.215 0 .422.082.574.228a.761.761 0 0 1 .238.55.761.761 0 0 1-.238.55.832.832 0 0 1-.574.227H6.356c-.168.455-.48.85-.891 1.129a2.514 2.514 0 0 1-1.408.428c-.505 0-.997-.15-1.409-.428a2.347 2.347 0 0 1-.89-1.129H.806a.831.831 0 0 1-.575-.228.761.761 0 0 1-.238-.55c0-.206.086-.403.238-.55a.831.831 0 0 1 .575-.227h.95Zm7.175 4.665a.831.831 0 0 0-.575.228.761.761 0 0 0-.238.55c0 .206.086.404.238.55a.831.831 0 0 0 .575.228.831.831 0 0 0 .574-.228.761.761 0 0 0 .238-.55.761.761 0 0 0-.238-.55.831.831 0 0 0-.574-.228Zm-2.3 0c.168-.455.48-.85.891-1.128a2.514 2.514 0 0 1 1.409-.429c.504 0 .996.15 1.408.429.412.279.723.673.891 1.128h.95c.216 0 .423.082.575.228a.761.761 0 0 1 .238.55.761.761 0 0 1-.238.55.832.832 0 0 1-.574.228h-.951c-.168.455-.48.85-.891 1.128a2.514 2.514 0 0 1-1.408.429c-.505 0-.997-.15-1.409-.429a2.347 2.347 0 0 1-.89-1.128H.806a.831.831 0 0 1-.575-.228A.761.761 0 0 1-.006 7c0-.206.086-.404.238-.55a.831.831 0 0 1 .575-.228h5.825Zm-2.575 4.666a.832.832 0 0 0-.575.228.76.76 0 0 0-.238.55.76.76 0 0 0 .238.55.832.832 0 0 0 .575.227.832.832 0 0 0 .574-.228.76.76 0 0 0 .238-.55.76.76 0 0 0-.238-.55.832.832 0 0 0-.574-.227Zm-2.3 0c.168-.456.48-.85.891-1.129a2.514 2.514 0 0 1 1.409-.428c.504 0 .996.15 1.408.428.412.28.723.673.891 1.129h5.826c.215 0 .422.082.574.228a.761.761 0 0 1 .238.55.761.761 0 0 1-.238.55.832.832 0 0 1-.574.227H6.356c-.168.455-.48.85-.891 1.128A2.515 2.515 0 0 1 4.057 14c-.505 0-.997-.15-1.409-.429a2.347 2.347 0 0 1-.89-1.128H.806a.832.832 0 0 1-.575-.228.76.76 0 0 1-.238-.55.76.76 0 0 1 .238-.55.832.832 0 0 1 .575-.227h.95Z"/></svg>
								найти
							</button>
						</div>
					</div>
				</div>

				<div class="places-items ajax-posts__items">
				
					<?php if ( have_posts() ) { ?>
						<?php
						while ( have_posts() ) {
							the_post();
							get_template_part('templates/content-places-item');
						}
						wp_reset_postdata();
						?>
					<?php } ?>

				</div>
				
				<?php if ( $wp_query->max_num_pages > 1 ) { ?>
				
					<a href="javascript:void(0)" id="load-more" class="primary-btn load-more ajax-posts__btn">Загрузить еще</a>
					
				<?php } ?>
			</div>
		</div>
	</div>
</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>