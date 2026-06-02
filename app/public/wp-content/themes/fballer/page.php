<?php get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<section class="section">
	<div class="container">
		<div class="page-title"><?php the_title(); ?></div>
		<div class="page-content single-news__content-inner">
			<?php the_content(); ?>
			
			<?php if ( comments_open() ) { ?>
				<?php comments_template( '', true ); ?>
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
	</div>
</section>

<?php endwhile; ?> 

<section class="section decor-section">
	<img src="<?php echo get_template_directory_uri(); ?>/assets/img/rdec.jpg" alt="img">
</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
