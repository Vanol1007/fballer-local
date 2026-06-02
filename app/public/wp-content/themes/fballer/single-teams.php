<?php get_header(); ?>

<?php if ( have_posts() ) { while ( have_posts() ) { the_post(); ?>

<?php
$icon_base = get_template_directory_uri() . '/assets/img/icons';

$get_term_names = static function($post_id, $taxonomy) {
	$terms = wp_get_post_terms($post_id, $taxonomy);
	if ( ! $terms || is_wp_error($terms) ) {
		return [];
	}

	$items = [];
	foreach ( $terms as $term ) {
		$items[] = $term->name;
	}

	return $items;
};

$positions = $get_term_names(get_the_ID(), 'team_position');
$districts = $get_term_names(get_the_ID(), 'district');
$cities = $get_term_names(get_the_ID(), 'city');
$metro = $get_term_names(get_the_ID(), 'metro');
$team_levels = $get_term_names(get_the_ID(), 'team_level');

$btn_text = trim((string) carbon_get_the_post_meta('team_btn_text'));
if ( $btn_text === '' ) {
	$btn_text = 'Написать';
}
$btn_link = carbon_get_the_post_meta('team_btn_link') ?: 'javascript:void(0)';

$team_title = get_the_title();
$team_short_name = preg_replace('/\s+ищет.*$/ui', '', $team_title);
$team_short_name = trim((string) $team_short_name);
if ( $team_short_name === '' ) {
	$team_short_name = $team_title;
}

$title_format = '';
if ( preg_match('/\(([^,()]+)(?:,\s*[^)]+)?\)/u', $team_title, $matches) ) {
	$title_format = trim((string) $matches[1]);
}

$location_parts = [];
if ( ! empty($cities) ) {
	$location_parts[] = implode(', ', $cities);
}
if ( ! empty($metro) ) {
	$location_parts[] = 'м. ' . implode(', ', $metro);
}
if ( ! empty($districts) ) {
	$location_parts[] = implode(', ', $districts);
}
$location_text = implode(', ', array_filter($location_parts));

$team_meta_items = array_filter([
	[
		'icon' => $icon_base . '/team.svg',
		'text' => $team_short_name,
	],
	! empty($team_levels) ? [
		'icon' => $icon_base . '/level.svg',
		'text' => implode(', ', $team_levels),
	] : null,
	! empty($positions) ? [
		'icon' => $icon_base . '/position.svg',
		'text' => implode(', ', $positions),
	] : null,
	$location_text !== '' ? [
		'icon' => $icon_base . '/location.svg',
		'text' => $location_text,
	] : null,
	$title_format !== '' ? [
		'icon' => $icon_base . '/format.svg',
		'text' => $title_format,
	] : null,
]);
?>

<section class="section place-detail single-team-page">
	<div class="place-detail__bg">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/img/cart-bg.png" alt="img">
	</div>
	<div class="container">
		<div class="single-news__content-inner">
			<div class="team-single">
				<div class="team-single__summary">
					<h1 class="team-single__title"><?php the_title(); ?></h1>

					<?php if ( ! empty($team_meta_items) ) { ?>
						<ul class="team-single__meta">
							<?php foreach ( $team_meta_items as $item ) { ?>
								<li class="team-single__meta-item">
									<span class="team-single__meta-icon">
										<img src="<?php echo esc_url($item['icon']); ?>" alt="">
									</span>
									<span class="team-single__meta-text"><?php echo esc_html($item['text']); ?></span>
								</li>
							<?php } ?>
						</ul>
					<?php } ?>

					<a href="<?php echo esc_url($btn_link); ?>" class="secondary-btn team-single__button" target="_blank" rel="nofollow">
						<?php echo esc_html($btn_text); ?>
					</a>
				</div>

				<div class="team-single__content">
					<div class="team-single__description">
						<?php the_content(); ?>
					</div>
				</div>
			</div>
		</div>

		<?php if ( comments_open() ) { ?>
			<?php comments_template('', true); ?>
		<?php } ?>

		<?php echo str_replace('<h2 class="screen-reader-text">REMOVE_ME_PLS</h2>', '', get_the_posts_pagination(
			array(
				'mid_size' => 2,
				'prev_text' => __( '&larr;' ),
				'next_text' => __( '&rarr;' ),
				'screen_reader_text' => __( 'REMOVE_ME_PLS' ),
			))
		); ?>
	</div>
</section>

<?php }} ?>

<?php $ajaxVariable = 'ajaxTeamsData'; ?>

<section class="section players players--teams single-team-related" id="play">
	<div class="container ajax-posts" ajax-data="<?php echo $ajaxVariable; ?>">
		<div class="section-title">Они тоже тебя ищут:</div>

		<?php
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
			<a href="/add-team/" class="player-item player-item--team">
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

		<?php if ( $query->max_num_pages > 1 ) { ?>
			<a href="/teams/" id="load-more" class="primary-btn load-more">Показать все</a>
		<?php } ?>
	</div>
</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
