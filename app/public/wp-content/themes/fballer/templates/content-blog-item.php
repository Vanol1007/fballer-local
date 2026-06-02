
<?php
$excerpt = wp_html_excerpt(get_the_excerpt(), 47, '...');
$image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
?>

<li class="news-item" onclick="window.location='<?php the_permalink(); ?>'">
	<div class="news-item__body">
		<div class="news-item__name">
			<?php the_title(); ?>
		</div>
		<div class="news-item_description">
			<?php echo $excerpt; ?>
		</div>
	</div>
	<div class="news-item__image">
		<?php if ( $image ) { ?>
			<img src="<?php echo $image; ?>" alt="img">
		<?php } ?>
	</div>
</li>
