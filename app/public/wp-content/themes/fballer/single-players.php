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
$cities = $get_term_names(get_the_ID(), 'city');
$team_levels = $get_term_names(get_the_ID(), 'team_level');

$player_goal = trim((string) carbon_get_the_post_meta('player_goal'));
if ( $player_goal !== 'game' ) {
	$player_goal = 'team';
}

$goal_label = $player_goal === 'game' ? 'Ищет игру' : 'Ищет команду';

$btn_text = trim((string) carbon_get_the_post_meta('player_btn_text'));
if ( $btn_text === '' ) {
	$btn_text = 'Написать';
}
$btn_link = carbon_get_the_post_meta('player_btn_link') ?: 'javascript:void(0)';

$player_meta_items = array_filter([
	[
		'icon' => $icon_base . '/team.svg',
		'text' => $goal_label,
	],
	! empty($team_levels) ? [
		'icon' => $icon_base . '/level.svg',
		'text' => implode(', ', $team_levels),
	] : null,
	! empty($positions) ? [
		'icon' => $icon_base . '/position.svg',
		'text' => implode(', ', $positions),
	] : null,
	! empty($cities) ? [
		'icon' => $icon_base . '/location.svg',
		'text' => implode(', ', $cities),
	] : null,
]);

$matched_ids = $player_goal === 'game'
	? fballer_get_matching_games_for_player(get_the_ID(), 7)
	: fballer_get_matching_teams_for_player(get_the_ID(), 7);
$matched_post_type = $player_goal === 'game' ? 'games' : 'teams';
$matched_title = $player_goal === 'game' ? 'Эти игры могли бы вам подойти' : 'Эти команды могли бы вам подойти';
?>

<section class="section place-detail single-player-page">
	<div class="place-detail__bg">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/img/cart-bg.png" alt="img">
	</div>
	<div class="container">
		<div class="single-news__content-inner">
			<div class="team-single">
				<div class="team-single__summary">
					<h1 class="team-single__title"><?php the_title(); ?></h1>

					<?php if ( ! empty($player_meta_items) ) { ?>
						<ul class="team-single__meta">
							<?php foreach ( $player_meta_items as $item ) { ?>
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
	</div>
</section>

<?php if ( ! empty($matched_ids) ) { ?>
<section class="section players single-player-related" id="play">
	<div class="container">
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
</section>
<?php } ?>

<?php }} ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
