<?php get_header(); ?>

<?php if ( have_posts() ) { while ( have_posts() ) { the_post(); ?>

<?php
$icon_base = get_template_directory_uri() . '/assets/img/icons';

$get_term_names = static function($post_id, $taxonomy) {
	$terms = wp_get_post_terms($post_id, $taxonomy);
	if ( ! $terms || is_wp_error($terms) ) {
		return [];
	}

	$items = [];
	foreach ( $terms as $term ) {
		$items[] = $term->name;
	}

	return $items;
};

$format = $get_term_names(get_the_ID(), 'game_format');
$cities = $get_term_names(get_the_ID(), 'city');

$place = false;
$game_places = carbon_get_the_post_meta('game_places');
if ( $game_places ) {
	foreach ( $game_places as $place_item ) {
		$get_post = get_post($place_item['id'], ARRAY_A);
		if ( ! $get_post ) {
			continue;
		}

		$metro = $get_term_names($get_post['ID'], 'metro');
		if ( ! empty($metro) ) {
			$get_post['metro'] = $metro;
		}

		$coating = $get_term_names($get_post['ID'], 'coating');
		if ( ! empty($coating) ) {
			$get_post['coating'] = $coating;
		}

		$get_post['address'] = carbon_get_post_meta($get_post['ID'], 'place_address') ?: '';
		$place = $get_post;
		break;
	}
}

$custom_place_name = trim((string) carbon_get_the_post_meta('game_custom_place_name'));
$custom_place_address = trim((string) carbon_get_the_post_meta('game_custom_place_address'));

$price = '';
$game_price = carbon_get_the_post_meta('game_price');
if ( $game_price !== '' && $game_price !== null ) {
	$game_price = trim((string) $game_price);
	$price = mb_strtolower($game_price) === 'бесплатно' ? 'Бесплатно' : $game_price . ' р.';
}

$date_format = 'd F';
$date = get_the_date($date_format);
$game_date = carbon_get_the_post_meta('game_date');
if ( $game_date ) {
	$date = wp_date($date_format, $game_date);
}

$time = '';
$game_time = carbon_get_the_post_meta('game_time');
if ( $game_time ) {
	$time_parts = str_split($game_time, 2);
	$time = implode(':', $time_parts);
}

$title = trim($date . ( $time !== '' ? ' ' . $time : '' ));
if ( $title === '' ) {
	$title = get_the_title();
}

$btn_text = trim((string) carbon_get_the_post_meta('btn_text'));
if ( $btn_text === '' ) {
	$btn_text = 'Написать';
}
$btn_link = carbon_get_the_post_meta('btn_link') ?: 'javascript:void(0)';

$game_meta_items = array_filter([
	isset($place['post_title']) ? [
		'icon' => $icon_base . '/location.svg',
		'text' => $place['post_title'],
		'link' => get_post_permalink($place['ID']),
	] : null,
	! empty($cities) ? [
		'icon' => $icon_base . '/location.svg',
		'text' => implode(', ', $cities),
	] : null,
	$custom_place_name !== '' ? [
		'icon' => $icon_base . '/team.svg',
		'text' => $custom_place_name,
	] : null,
	$custom_place_address !== '' ? [
		'icon' => $icon_base . '/location.svg',
		'text' => $custom_place_address,
	] : null,
	! empty($place['address']) ? [
		'icon' => $icon_base . '/location.svg',
		'text' => $place['address'],
	] : null,
	! empty($place['metro']) ? [
		'icon' => $icon_base . '/metro.svg',
		'text' => implode(', ', $place['metro']),
	] : null,
	! empty($place['coating']) ? [
		'icon' => $icon_base . '/gazon.svg',
		'text' => implode(', ', $place['coating']),
	] : null,
	$price !== '' ? [
		'icon' => $icon_base . '/price.svg',
		'text' => $price,
	] : null,
	! empty($format) ? [
		'icon' => $icon_base . '/format.svg',
		'text' => implode(', ', $format),
	] : null,
]);
?>

<section class="section place-detail single-game-page">
	<div class="place-detail__bg">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/img/cart-bg.png" alt="img">
	</div>
	<div class="container">
		<div class="single-news__content-inner">
			<div class="game-single">
				<div class="game-single__summary">
					<h1 class="game-single__title"><?php echo esc_html($title); ?></h1>

					<?php if ( ! empty($game_meta_items) ) { ?>
						<ul class="game-single__meta">
							<?php foreach ( $game_meta_items as $item ) { ?>
								<li class="game-single__meta-item">
									<span class="game-single__meta-icon">
										<img src="<?php echo esc_url($item['icon']); ?>" alt="">
									</span>
									<?php if ( ! empty($item['link']) ) { ?>
										<a class="game-single__meta-text game-single__meta-link" href="<?php echo esc_url($item['link']); ?>">
											<?php echo esc_html($item['text']); ?>
										</a>
									<?php } else { ?>
										<span class="game-single__meta-text"><?php echo esc_html($item['text']); ?></span>
									<?php } ?>
								</li>
							<?php } ?>
						</ul>
					<?php } ?>

					<a href="<?php echo esc_url($btn_link); ?>" class="secondary-btn game-single__button" target="_blank" rel="nofollow">
						<?php echo esc_html($btn_text); ?>
					</a>
				</div>

				<div class="game-single__content">
					<div class="game-single__description">
						<?php the_content(); ?>
					</div>
				</div>
			</div>
		</div>

		<?php if ( comments_open() ) { ?>
			<?php comments_template('', true); ?>
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

<?php $ajaxVariable = 'ajaxGamesData'; ?>

<section class="section players single-game-related" id="play">
	<div class="container ajax-posts" ajax-data="<?php echo $ajaxVariable; ?>">
		<div class="section-title">С кем поиграть?</div>

		<?php
		$selected_city = fballer_get_selected_city_id();
		$paged = (get_query_var('paged') ? get_query_var('paged') : 1);
		$posts_per_page = ($paged == 1 ? 7 : 4);

		$query_args = fballer_apply_upcoming_games_query_args(array(
			'post_type' => 'games',
			'posts_per_page' => $posts_per_page,
			'paged' => $paged,
			'orderby' => [
				'meta_value_num' => 'ASC',
				'post_date' => 'DESC',
			],
			'tax_query' => array(
				array(
					'taxonomy' => 'city',
					'field' => 'term_id',
					'terms' => $selected_city,
				),
			),
		), $selected_city);

		$query = new WP_Query($query_args);

		wp_localize_script('ajax-posts', $ajaxVariable, array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'query_vars' => json_encode($query->query_vars),
			'current_page' => max(1, get_query_var('paged')),
			'max_pages' => $query->max_num_pages,
		));
		?>

		<div class="player-items ajax-posts__items">
			<a href="/add-game/" class="player-item">
				<div class="add-wrapper">
					<svg width="82" height="82" viewBox="0 0 82 82" fill="none" xmlns="http://www.w3.org/2000/svg">
						<circle cx="41" cy="41" r="41" fill="#D9D9D9" />
						<path d="M39.644 49.4981V31.1021H43.172V49.4981H39.644ZM32 41.9801V38.6621H50.816V41.9801H32Z" fill="white" />
					</svg>
					<div class="add-text">Добавить свою игру</div>
				</div>
			</a>

			<?php if ( $query->have_posts() ) { ?>
				<?php
				while ( $query->have_posts() ) {
					$query->the_post();
					get_template_part('templates/content-games-item');
				}
				wp_reset_postdata();
				?>
			<?php } ?>
		</div>

		<?php if ( $query->max_num_pages > 1 ) { ?>
			<a href="/poisk-matchey" id="load-more" class="primary-btn load-more">Показать все</a>
		<?php } ?>
	</div>
</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
