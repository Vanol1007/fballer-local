<?php get_header(); ?>

<?php if ( function_exists('theme_breadcrumbs') ){
	theme_breadcrumbs();
} ?>

<?php the_archive_title('<h1>', '</h1>'); ?>
<?php echo get_the_archive_description(); ?>

<?php query_posts('posts_per_page=6'); if ( have_posts() ) { ?>
<?php while ( have_posts() ) : the_post(); ?>
	<?php get_template_part('templates/content-loop'); ?>
<?php endwhile; ?>
<?php } else { ?>
<h2>Извините, ничего не найдено...</h2>
<?php } ?>
 
<?php echo str_replace('<h2 class="screen-reader-text">REMOVE_ME_PLS</h2>', '', get_the_posts_pagination(
	array(
		'mid_size' => 2,
		'prev_text' => __( '&larr;' ),
		'next_text' => __( '&rarr;' ),
		'screen_reader_text' => __( 'REMOVE_ME_PLS' ),
	))
); ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>