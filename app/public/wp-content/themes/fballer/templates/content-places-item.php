<?php
$image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
$icon_base = get_template_directory_uri() . '/assets/img/icons';

// Покрытие
$terms = wp_get_post_terms(get_the_ID(), 'coating');
$coating = [];
if ( $terms && ! empty($terms) && ! is_wp_error($terms) ) {
	foreach ( $terms as $term ) {
		$coating[] = $term->name;
	}
}

// Размер поля
$terms = wp_get_post_terms(get_the_ID(), 'field_size');
$size = [];
if ( $terms && ! empty($terms) && ! is_wp_error($terms) ) {
	foreach ( $terms as $term ) {
		$size[] = $term->name;
	}
}

$get_meta = carbon_get_the_post_meta('place_price');
$price = '';
if ( $get_meta ) {
	$price = $get_meta;
}

$get_meta = carbon_get_the_post_meta('place_address');
$address = '';
if ( $get_meta ) {
	$address = $get_meta;
}


?>

<div class="places-item" onclick="window.location='<?php the_permalink(); ?>'">
	<div class="places-item__info">
		<div class="places-item__name">
			<?php echo get_the_title(); ?>
		</div>
		<?php if ( isset($info) && ! empty($info) ) { ?>
		
			<div class="places-item__loc-item">
				<div class="places-item__loc-item__icon">
					<span class="card-icon-mask" style="--icon-url: url('<?php echo esc_url($icon_base . '/location.svg'); ?>');"></span>
				</div>
				<div class="places-item__loc-item__text">
					<?php echo $info; ?>
				</div>
			</div>
			
			<?php } ?>
		
		
		<div class="places-item__loc-items">
		
			<?php if ( isset($address) && ! empty($address) ) { ?>
		
			<div class="places-item__loc-item">
				<div class="places-item__loc-item__icon">
					<span class="card-icon-mask" style="--icon-url: url('<?php echo esc_url($icon_base . '/location.svg'); ?>');"></span>
				</div>
				<div class="places-item__loc-item__text">
					<?php echo $address; ?>
				</div>
			</div>
			
			<?php } ?>
			
			<?php if ( isset($price) && ! empty($price) ) { ?>
			
				<div class="places-item__loc-item">
					<div class="places-item__loc-item__icon">
						<span class="card-icon-mask" style="--icon-url: url('<?php echo esc_url($icon_base . '/price.svg'); ?>');"></span>
					</div>
					<div class="places-item__loc-item__text">
						<?php echo $price; ?>
					</div>
				</div>
			
			<?php } ?>

			<?php if ( isset($coating) && ! empty($coating) ) { ?>

				<div class="places-item__loc-item">
					<div class="places-item__loc-item__icon">
						<span class="card-icon-mask" style="--icon-url: url('<?php echo esc_url($icon_base . '/gazon.svg'); ?>');"></span>
					</div>
					<div class="places-item__loc-item__text">
						<?php echo ( ! empty($coating) ? implode(', ', $coating) : '' ); ?>
					</div>
				</div>
				
			<?php } ?>
			
			<?php if ( isset($size) && ! empty($size) ) { ?>
			
				<div class="places-item__loc-item">
					<div class="places-item__loc-item__icon">
						<span class="card-icon-mask" style="--icon-url: url('<?php echo esc_url($icon_base . '/razmer-polya.svg'); ?>');"></span>
					</div>
					<div class="places-item__loc-item__text">
						<?php echo ( ! empty($size) ? implode(', ', $size) : '' ); ?>
					</div>
				</div>
			
			<?php } ?>
			
		</div>
	</div>
	<div class="places-item__image">
		<?php if ( $image ) { ?>
			<img src="<?php echo $image; ?>" alt="img">
		<?php } ?>
	</div>

</div>
