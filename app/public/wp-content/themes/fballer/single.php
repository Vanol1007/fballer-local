<?php get_header(); ?> 

<?php if ( have_posts() ) { while ( have_posts() ) { the_post(); ?>

<section class="section single-news__content">
	<div class="container">
		<div class="single-news__content-inner">
			<h1><?php the_title(); ?></h1>
			
			<?php the_content(); ?>
		</div>
		
		<?php 
		$post_permalink = get_permalink(); 
		$previous_post = get_adjacent_post(false, '', true);
		$next_post = get_adjacent_post(false, '', false);
		?>
		<div class="post-navigation">
			<?php if ( $post_permalink != get_permalink($previous_post) ) { ?>
			<a href="<?php echo get_permalink($previous_post); ?>" class="post-nav__item prev-post">
				<div class="post-nav__item__head">
					<div class="post-nav__item__head-title">Предыдущая статья</div>
					<div class="post-nav__item__head-arrow">
						<svg width="414" height="9" viewBox="0 0 414 9" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.646454 4.06445C0.451172 4.25971 0.451172 4.5763 0.646454 4.77156L3.82843 7.95354C4.02368 8.1488 4.34027 8.1488 4.53552 7.95354C4.7308 7.75828 4.7308 7.44169 4.53552 7.24643L1.70709 4.418L4.53552 1.58958C4.7308 1.39432 4.7308 1.07773 4.53552 0.882471C4.34027 0.687208 4.02368 0.687208 3.82843 0.882471L0.646454 4.06445ZM414 3.91797L1 3.918L1 4.918L414 4.91797L414 3.91797Z" fill="#C6C6C6" /></svg>
					</div>
				</div>
				<div class="post-nav__item-card">
					<div class="post-nav__item-body">
						<div class="post-nav__item-card-title"><?php echo $previous_post->post_title; ?></div>
						<?php
						$excerpt = $previous_post->post_excerpt;
						if ( empty($excerpt) ) {
							$excerpt = wp_html_excerpt($previous_post->post_content, 47, '...');
						}
						?>
						<div class="post-nav__item-card-subtitle"><?php echo $excerpt; ?></div>
					</div>
					<div class="post-nav__item-image">
						<?php $thumbnail_url = get_the_post_thumbnail_url( $previous_post->ID, 'full' ); ?>
						<?php if ( $thumbnail_url ) { ?>
							<img src="<?php echo $thumbnail_url; ?>" alt="img">
						<?php } ?>
					</div>
				</div>
			</a>

			<?php } ?>

			<?php if ( $post_permalink != get_permalink($next_post) ) { ?>
			<a href="<?php echo get_permalink($next_post); ?>" class="post-nav__item prev-post">
				<div class="post-nav__item__head">
					<div class="post-nav__item__head-title">Следующая статья</div>
					<div class="post-nav__item__head-arrow">
						<svg width="414" height="8" viewBox="0 0 414 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M413.572 3.62305C413.768 3.81831 413.768 4.13489 413.572 4.33015L410.39 7.51213C410.195 7.70739 409.878 7.70739 409.683 7.51213C409.488 7.31687 409.488 7.00029 409.683 6.80503L412.512 3.9766L409.683 1.14817C409.488 0.952909 409.488 0.636327 409.683 0.441064C409.878 0.245802 410.195 0.245802 410.39 0.441064L413.572 3.62305ZM0.21875 3.47656L413.219 3.4766L413.219 4.4766L0.21875 4.47656L0.21875 3.47656Z" fill="#C6C6C6" /></svg>
					</div>
				</div>
				<div class="post-nav__item-card">
					<div class="post-nav__item-body">
						<div class="post-nav__item-card-title"><?php echo $next_post->post_title; ?></div>
						<?php
						$excerpt = $next_post->post_excerpt;
						if ( empty($excerpt) ) {
							$excerpt = wp_html_excerpt($next_post->post_content, 47, '...');
						}
						?>
						<div class="post-nav__item-card-subtitle"><?php echo $excerpt; ?></div>
					</div>
					<div class="post-nav__item-image">
						<?php $thumbnail_url = get_the_post_thumbnail_url( $next_post->ID, 'full' ); ?>
						<?php if ( $thumbnail_url ) { ?>
							<img src="<?php echo $thumbnail_url; ?>" alt="img">
						<?php } ?>
					</div>
				</div>
			</a>
			<?php } ?>
		</div>

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
</section>

<?php }} ?>

<section class="section decor-section">
	<img src="<?php echo get_template_directory_uri(); ?>/assets/img/decor-s1.png" alt="img">
</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
