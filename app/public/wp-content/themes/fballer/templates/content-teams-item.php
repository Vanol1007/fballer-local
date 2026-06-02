<?php
$excerpt = wp_html_excerpt(get_the_excerpt(), 200, '...');
// $image = get_the_post_thumbnail_url( get_the_ID(), 'full' );

// Позиция на поле
$terms = wp_get_post_terms(get_the_ID(), 'team_position');
$positions = [];
if ( $terms && ! empty($terms) && ! is_wp_error($terms) ) {
	foreach ( $terms as $term ) {
		$positions[] = $term->name;
	}
}

// Район
$terms = wp_get_post_terms(get_the_ID(), 'district');
$district = [];
if ( $terms && ! empty($terms) && ! is_wp_error($terms) ) {
	foreach ( $terms as $term ) {
		$district[] = $term->name;
	}
}

$get_meta = carbon_get_the_post_meta('team_btn_text');
$btn_text = 'Написать';
if ( $get_meta ) {
	$btn_text = $get_meta;
}

$get_meta = carbon_get_the_post_meta('team_btn_link');
$btn_link = 'javascript:void(0)';
if ( $get_meta ) {
	$btn_link = $get_meta;
}
?>

<div class="player-item player-item--team" data-url="<?php the_permalink(); ?>">
	<div class="player-item__head">
		<div class="player-item__date">
			<?php echo get_the_title(); ?>
		</div>
	</div>
	<div class="player-item__body">
	
		<?php if ( ! empty($positions) ) { ?>
		<div>
			<div class="player-item__address">
				<span>Кого ищут</span>
			</div>
			<div class="player-item--team__text">
				<?php echo implode(', ', $positions); ?>
			</div>
		</div>
		<?php } ?>
		
		<?php if ( ! empty($district) ) { ?>
		<div>
			<div class="player-item__address">
				<span>Район</span>
			</div>
			<div class="player-item--team__text">
				<?php echo implode(', ', $district); ?>
			</div>
		</div>
		<?php } ?>
		
		<div class="player-item__description">
			<?php echo $excerpt; ?>
		</div>
	</div>
	<div class="player-item__footer">
		<a href="<?php echo $btn_link; ?>" class="secondary-btn">
			<?php echo $btn_text; ?>
		</a>
	</div>
</div>
