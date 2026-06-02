<?php
$excerpt = wp_html_excerpt(get_the_excerpt(), 200, '...');
// $image = get_the_post_thumbnail_url( get_the_ID(), 'full' );

// Формат игры
$terms = wp_get_post_terms(get_the_ID(), 'game_format');
$format = [];
if ( $terms && ! empty($terms) && ! is_wp_error($terms) ) {
	foreach ( $terms as $term ) {
		$format[] = $term->name;
	}
}
// Поле
$get_meta = carbon_get_the_post_meta('game_places');
$place = false;
if ( $get_meta ) {
	foreach ( $get_meta as $place ) {
		$get_post = get_post($place['id'], ARRAY_A);
		if ( $get_post ) {
			// Метро
			$terms = wp_get_post_terms($get_post['ID'], 'metro');
			$metro = [];
			if ( $terms && ! empty($terms) && ! is_wp_error($terms) ) {
				foreach ( $terms as $term ) {
					$metro[] = $term->name;
				}
			}
			if ( ! empty($metro) ) $get_post['metro'] = $metro;
			
			// Покрытие
			$terms = wp_get_post_terms($get_post['ID'], 'coating');
			$coating = [];
			if ( $terms && ! empty($terms) && ! is_wp_error($terms) ) {
				foreach ( $terms as $term ) {
					$coating[] = $term->name;
				}
			}
			if ( ! empty($coating) ) $get_post['coating'] = $coating;
			
			$place = $get_post;
			break;
		}
	}
}

$get_meta = carbon_get_the_post_meta('game_price');
$price = '';
if ( $get_meta ) {
	$price_raw = trim((string) $get_meta);
	if ( mb_strtolower($price_raw) === 'бесплатно' ) {
		$price = $price_raw;
	} else {
		$price = $price_raw . ' р.';
	}
}

$get_meta = carbon_get_the_post_meta('btn_text');
$btn_text = 'Написать';
if ( $get_meta ) {
	$btn_text = $get_meta;
}

$get_meta = carbon_get_the_post_meta('btn_link');
$btn_link = 'javascript:void(0)';
if ( $get_meta ) {
	$btn_link = $get_meta;
}

$get_meta = carbon_get_the_post_meta('game_date');
$date_format = 'd F';
$date = get_the_date($date_format);
if ( $get_meta ) {
	$date = wp_date($date_format, $get_meta);
}

$get_meta = carbon_get_the_post_meta('game_time');
$time = '';
if ( $get_meta ) {
	$time_parts = str_split( $get_meta, 2 ); // Разбиваем строку на массив [12, 55]
	$time =  implode( ':', $time_parts ); // Собираем обратно с двоеточием
}

?>

<div class="player-item player-item--game<?php echo ! $place ? ' player-item--no-place' : ''; ?>" data-url="<?php the_permalink(); ?>">
	<div class="player-item__head">
		<div class="player-item__date">
			<?php echo $date; ?>
		</div>
		<div class="player-item__time"><?php echo $time; ?></div>
	</div>
	<div class="player-item__body">
		<div class="player-item__meta">
			<div class="player-item__price"><?php echo $price; ?></div>
			<div class="player-item__size">
				<?php echo ( ! empty($format) ? implode(', ', $format) : '' ); ?>
			</div>
		</div>

		<div class="player-item__description">
			<?php echo $excerpt; ?>
		</div>

		<?php if ( $place ) { ?>
			<a href="<?php echo  get_post_permalink($place['ID']); ?>" class="player-item__place">
			<?php if ( isset($place['post_title']) ) { ?>
				<?php echo $place['post_title']; ?>
			<?php } ?>
			</a>
			<div class="player-item__address">
				<span><?php echo ( isset($place['metro']) && ! empty($place['metro']) ? implode(', ', $place['metro']) : '' ); ?></span>
				<span><?php echo ( isset($place['coating']) && ! empty($place['coating']) ? implode(', ', $place['coating']) : '' ); ?></span>
			</div>
		<?php } ?>
	</div>
	<div class="player-item__footer">
		<a href="<?=$btn_link ?>" class="secondary-btn"><?=$btn_text ?></a>
	</div>
</div>
