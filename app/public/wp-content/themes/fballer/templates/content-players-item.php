<?php
$excerpt = wp_html_excerpt(get_the_excerpt(), 200, '...');

$terms = wp_get_post_terms(get_the_ID(), 'team_position');
$positions = [];
if ( $terms && ! empty($terms) && ! is_wp_error($terms) ) {
	foreach ( $terms as $term ) {
		$positions[] = $term->name;
	}
}

$terms = wp_get_post_terms(get_the_ID(), 'team_level');
$levels = [];
if ( $terms && ! empty($terms) && ! is_wp_error($terms) ) {
	foreach ( $terms as $term ) {
		$levels[] = $term->name;
	}
}

$goal = trim((string) carbon_get_the_post_meta('player_goal'));
if ( $goal !== 'game' ) {
	$goal = 'team';
}

$goal_text = $goal === 'game' ? 'Ищет игру' : 'Ищет команду';

$btn_text = trim((string) carbon_get_the_post_meta('player_btn_text'));
if ( $btn_text === '' ) {
	$btn_text = 'Написать';
}

$btn_link = carbon_get_the_post_meta('player_btn_link');
if ( ! $btn_link ) {
	$btn_link = 'javascript:void(0)';
}
?>

<div class="player-item player-item--player" data-url="<?php the_permalink(); ?>">
	<div class="player-item__head">
		<div class="player-item__date">
			<?php echo get_the_title(); ?>
		</div>
	</div>
	<div class="player-item__body">
		<div>
			<div class="player-item__address">
				<span>Что ищет</span>
			</div>
			<div class="player-item--player__text">
				<?php echo esc_html($goal_text); ?>
			</div>
		</div>

		<?php if ( ! empty($positions) ) { ?>
		<div>
			<div class="player-item__address">
				<span>Позиции</span>
			</div>
			<div class="player-item--player__text">
				<?php echo esc_html(implode(', ', $positions)); ?>
			</div>
		</div>
		<?php } ?>

		<?php if ( ! empty($levels) ) { ?>
		<div>
			<div class="player-item__address">
				<span>Уровень</span>
			</div>
			<div class="player-item--player__text">
				<?php echo esc_html(implode(', ', $levels)); ?>
			</div>
		</div>
		<?php } ?>

		<div class="player-item__description">
			<?php echo $excerpt; ?>
		</div>
	</div>
	<div class="player-item__footer">
		<a href="<?php echo esc_url($btn_link); ?>" class="secondary-btn">
			<?php echo esc_html($btn_text); ?>
		</a>
	</div>
</div>
