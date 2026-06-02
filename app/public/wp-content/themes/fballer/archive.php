<?php get_header(); ?>

<section class="section news">
	<div class="place-detail__bg">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/img/cart-bg.png" alt="img">
	</div>

	<div class="container ajax-posts">
		<div class="section-title"><?php echo get_the_archive_title(); ?></div>
			<?php the_archive_description( '<div class="category-description">', '</div>' ); ?>
<br/>
	
		<?php if ( have_posts() ) { ?>
	
			<ul class="news-items ajax-posts__items">
			
				<?php while ( have_posts() ) : the_post(); ?>
				
					<?php get_template_part('templates/content-blog-item'); ?>
					
				<?php endwhile; ?>

			</ul>
			
			<?php
			global $wp_query;
			if ( $wp_query->max_num_pages > 1 ) { ?>
			
				<a href="javascript:void(0)" class="more-btn ajax-posts__btn">больше новостей</a>
				
			<?php } ?>

		<?php } else { ?>
		
			<h2>Извините, ничего не найдено...</h2>
			
		<?php } ?>
		
	</div>
</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>