<?php get_header(); ?>

<?php
global $wp_query;
$ajaxVariable = 'ajaxTeamsData';

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
	<div class="container">
		<div class="search-inner">
			<div class="container ajax-posts" ajax-data="<?php echo $ajaxVariable; ?>">
				<div class="section-title">
					<?php //echo get_the_archive_title(); ?>
					Поиск команды
				</div>
				
				<?php if ( $value = get_theme_mod('custom_content_archive_teams') ) { ?>
				
					<div class="section-desctiption">
						<?php echo apply_filters('the_content', $value); ?>
					</div>
					
				<?php } ?>

				<div class="search-filter__row">

					<div class="filter-chiose__items active" id="filter-1">
					
						<?php
						$terms = get_terms(array(
							'taxonomy' => 'team_level',
							'fields' => 'all', // ids, names, count, id=>parent
							'hide_empty' => false,
						));
						?>

						<?php if ( ! is_wp_error($terms) && ! empty($terms) ) { ?>

							<div class="input-wrap filter-chiose__item lg">
								<label for="team_level">Уровень игры</label>
								<select name="team_level" id="team_level">
									<option value="" selected>Все</option>
								
									<?php foreach ( $terms as $term ) { ?>

										<option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
								
									<?php } ?>

								</select>
							</div>
							
						<?php } ?>
						
						<?php
						$terms = get_terms(array(
							'taxonomy' => 'team_position',
							'fields' => 'all', // ids, names, count, id=>parent
							'hide_empty' => false,
						));
						?>

						<?php if ( ! is_wp_error($terms) && ! empty($terms) ) { ?>

							<div class="input-wrap filter-chiose__item lg">
								<label for="team_position">Позиция на поле</label>
								<select name="team_position" id="team_position">
									<option value="" selected>Все</option>
								
									<?php foreach ( $terms as $term ) { ?>

										<option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
								
									<?php } ?>

								</select>
							</div>
							
						<?php } ?>
					
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
						
						<div class="input-wrap filter-chiose__item">
							<button class="primary-btn filter-btn">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 13 14"><path fill="#fff" d="M4.057 1.557a.831.831 0 0 0-.575.228.761.761 0 0 0-.238.55c0 .206.086.404.238.55a.831.831 0 0 0 .575.227.831.831 0 0 0 .574-.228.761.761 0 0 0 .238-.55.761.761 0 0 0-.238-.55.831.831 0 0 0-.574-.227Zm-2.3 0c.168-.455.48-.85.891-1.128A2.514 2.514 0 0 1 4.057 0c.504 0 .996.15 1.408.429.412.278.723.673.891 1.128h5.826c.215 0 .422.082.574.228a.761.761 0 0 1 .238.55.761.761 0 0 1-.238.55.832.832 0 0 1-.574.227H6.356c-.168.455-.48.85-.891 1.129a2.514 2.514 0 0 1-1.408.428c-.505 0-.997-.15-1.409-.428a2.347 2.347 0 0 1-.89-1.129H.806a.831.831 0 0 1-.575-.228.761.761 0 0 1-.238-.55c0-.206.086-.403.238-.55a.831.831 0 0 1 .575-.227h.95Zm7.175 4.665a.831.831 0 0 0-.575.228.761.761 0 0 0-.238.55c0 .206.086.404.238.55a.831.831 0 0 0 .575.228.831.831 0 0 0 .574-.228.761.761 0 0 0 .238-.55.761.761 0 0 0-.238-.55.831.831 0 0 0-.574-.228Zm-2.3 0c.168-.455.48-.85.891-1.128a2.514 2.514 0 0 1 1.409-.429c.504 0 .996.15 1.408.429.412.279.723.673.891 1.128h.95c.216 0 .423.082.575.228a.761.761 0 0 1 .238.55.761.761 0 0 1-.238.55.832.832 0 0 1-.574.228h-.951c-.168.455-.48.85-.891 1.128a2.514 2.514 0 0 1-1.408.429c-.505 0-.997-.15-1.409-.429a2.347 2.347 0 0 1-.89-1.128H.806a.831.831 0 0 1-.575-.228A.761.761 0 0 1-.006 7c0-.206.086-.404.238-.55a.831.831 0 0 1 .575-.228h5.825Zm-2.575 4.666a.832.832 0 0 0-.575.228.76.76 0 0 0-.238.55.76.76 0 0 0 .238.55.832.832 0 0 0 .575.227.832.832 0 0 0 .574-.228.76.76 0 0 0 .238-.55.76.76 0 0 0-.238-.55.832.832 0 0 0-.574-.227Zm-2.3 0c.168-.456.48-.85.891-1.129a2.514 2.514 0 0 1 1.409-.428c.504 0 .996.15 1.408.428.412.28.723.673.891 1.129h5.826c.215 0 .422.082.574.228a.761.761 0 0 1 .238.55.761.761 0 0 1-.238.55.832.832 0 0 1-.574.227H6.356c-.168.455-.48.85-.891 1.128A2.515 2.515 0 0 1 4.057 14c-.505 0-.997-.15-1.409-.429a2.347 2.347 0 0 1-.89-1.128H.806a.832.832 0 0 1-.575-.228.76.76 0 0 1-.238-.55.76.76 0 0 1 .238-.55.832.832 0 0 1 .575-.227h.95Z"/></svg>
								найти
							</button>
						</div>
					</div>
				</div>

				<div class="player-items search-player__items ajax-posts__items">
					<a href="<?php echo esc_url(fballer_get_add_page_url('add-team', 'templates/template-add-team.php')); ?>" class="player-item player-item--team">
						<div class="add-wrapper">
							<svg width="82" height="82" viewBox="0 0 82 82" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="41" cy="41" r="41" fill="#D9D9D9" /><path d="M39.644 49.4981V31.1021H43.172V49.4981H39.644ZM32 41.9801V38.6621H50.816V41.9801H32Z" fill="white" /></svg>
							<div class="add-text">Команда ищет игрока</div>
						</div>
					</a>
				
					<?php if ( have_posts() ) { ?>
						<?php
						while ( have_posts() ) {
							the_post();
							get_template_part('templates/content-teams-item');
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
