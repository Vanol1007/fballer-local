
<?php
$image = get_the_post_thumbnail_url( get_the_ID(), 'full' );

// Поле
$get_meta = carbon_get_the_post_meta('champ_places');
$place = false;
if ( $get_meta ) {
	foreach ( $get_meta as $place ) {
		$get_post = get_post($place['id'], ARRAY_A);
		if ( $get_post ) {
			$terms = wp_get_post_terms($get_post['ID'], 'metro');
			$metro = [];
			if ( $terms && ! empty($terms) && ! is_wp_error($terms) ) {
				foreach ( $terms as $term ) {
					$metro[] = $term->name;
				}
			}
			if ( ! empty($metro) ) {
				$get_post['metro'] = $metro;
			}

			$terms = wp_get_post_terms($get_post['ID'], 'coating');
			$coating = [];
			if ( $terms && ! empty($terms) && ! is_wp_error($terms) ) {
				foreach ( $terms as $term ) {
					$coating[] = $term->name;
				}
			}
			if ( ! empty($coating) ) {
				$get_post['coating'] = $coating;
			}

			$place = $get_post;
			break;
		}
	}
}

$place_title = ( $place && isset($place['post_title']) ) ? $place['post_title'] : '';
$place_metro = ( $place && isset($place['metro']) && ! empty($place['metro']) ) ? $place['metro'] : [];
$place_coating = ( $place && isset($place['coating']) && ! empty($place['coating']) ) ? implode(', ', $place['coating']) : '';
$cta_text = 'Подробнее';

// Старт
$get_meta = carbon_get_the_post_meta('champ_start');
$champ_start = '';
if ( $get_meta ) {
	$champ_start = wp_date('d.m.Y', $get_meta);
}

// Окончание
$get_meta = carbon_get_the_post_meta('champ_end');
$champ_end = '';
if ( $get_meta ) {
	$champ_end = wp_date('d.m.Y', $get_meta);
}

// Формат игры
$terms = wp_get_post_terms(get_the_ID(), 'game_format');
$game_format = '';
if ( $terms && ! empty($terms) && ! is_wp_error($terms) ) {
	$data = [];
	
	foreach ( $terms as $term ) {
		$data[] = $term->name;
	}
	
	if ( ! empty($data) ) {
		$game_format = implode(', ', $data);
	}
}

// Цена
$get_meta = carbon_get_the_post_meta('champ_price');
$champ_price = '';
if ( $get_meta ) {
	$champ_price = $get_meta;
}

?>

<div class="championship-item championship-card" onclick="window.location='<?php the_permalink(); ?>'" style="cursor: pointer;">
	<div class="championship-card__layout">
		<div class="championship-card__media-col">
			<div class="championship-card__image<?php echo ! $image ? ' championship-card__image--placeholder' : ''; ?>">
				<?php if ( $image ) { ?>
					<img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
				<?php } else { ?>
					<div class="championship-card__image-placeholder" aria-hidden="true">
						<?php echo esc_html(mb_substr(get_the_title(), 0, 1)); ?>
					</div>
				<?php } ?>
			</div>

			<div class="championship-item__footer championship-card__footer">
				<a href="<?php the_permalink(); ?>" id="join" class="primary-btn desktop-btn"><?php echo esc_html($cta_text); ?></a>
			</div>
		</div>

		<div class="championship-item__info championship-card__info">
			<div class="championship-item__info-head championship-card__head">
				<div class="championship-item__name championship-card__name">
					<?php echo get_the_title(); ?>
				</div>
			</div>

			<div class="championship-card__meta">
				<?php if ( $game_format !== '' ) { ?>
					<div class="championship-card__meta-item">
						<div class="championship-card__label">Формат</div>
						<div class="championship-card__value championship-card__value--accent"><?php echo esc_html($game_format); ?></div>
					</div>
				<?php } ?>

				<?php if ( $champ_price !== '' ) { ?>
					<div class="championship-card__meta-item">
						<div class="championship-card__label">Стоимость</div>
						<div class="championship-card__value championship-card__value--accent"><?php echo esc_html($champ_price); ?> ₽</div>
					</div>
				<?php } ?>

				<?php if ( $champ_start !== '' || $champ_end !== '' ) { ?>
					<div class="championship-card__meta-item championship-card__meta-item--full">
						<div class="championship-card__label">Даты</div>
						<div class="championship-card__value championship-card__value--accent">
							<?php echo esc_html(trim($champ_start . ( $champ_end !== '' ? ' - ' . $champ_end : '' ))); ?>
						</div>
					</div>
				<?php } ?>

				<?php if ( $place_title !== '' || $place_coating !== '' || ! empty($place_metro) ) { ?>
					<div class="championship-card__meta-item championship-card__meta-item--full">
						<div class="championship-card__label">Локация</div>
						<?php if ( $place_title !== '' ) { ?>
							<div class="championship-card__value championship-card__value--accent championship-card__location"><?php echo esc_html($place_title); ?></div>
						<?php } ?>
						<?php if ( $place_coating !== '' ) { ?>
							<div class="championship-card__subvalue"><?php echo esc_html($place_coating); ?></div>
						<?php } ?>
						<?php if ( ! empty($place_metro) ) { ?>
							<div class="championship-card__metro-list">
								<?php foreach ( $place_metro as $metro_item ) { ?>
									<span class="championship-card__metro-item"><?php echo esc_html('м. ' . preg_replace('/^м\.\s*/u', '', $metro_item)); ?></span>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>

			<div class="championship-item__footer championship-card__footer championship-card__footer--mobile">
				<a href="<?php the_permalink(); ?>" id="join-mobile" class="primary-btn desktop-btn"><?php echo esc_html($cta_text); ?></a>
			</div>
		</div>
	</div>
	
	<a href="javascript:void(0)" id="join" class="primary-btn mobile-btn">
		<?php echo esc_html($cta_text); ?>
	</a>
</div>
