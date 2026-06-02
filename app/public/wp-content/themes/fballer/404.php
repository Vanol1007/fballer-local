<?php get_header(); ?>

<section class="section">
	<div class="container">
		<div class="page-title"><?php the_title(); ?></div>
		<div class="page-content single-news__content-inner">
			<h1>Ошибка 404! Страница не найдена.</h1>
			<p>Такой страницы не существует.</p>
			<a href="<?php bloginfo('url') ?>" class="primary-btn">Вернуться на главную</a>
		</div>
	</div>
</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
