<?php
/**
 * Plugin Name: Fballer REST Bridge
 * Description: Включает REST для CPT игр, команд, игроков и площадок и регистрирует Carbon Fields метаполя под REST.
 * Version: 1.1.0
 */

if (!defined('ABSPATH')) exit;

const FB_CPTS = [
  'games'   => 'games',
  'teams'   => 'teams',
  'players' => 'players',
  'places'  => 'places',
];

const FB_REST_TAXONOMIES = [
  'city',
  'city_direction',
  'admin_area',
  'district',
  'metro',
  'game_format',
  'team_level',
  'team_position',
];

add_action('init', function () {
  foreach (['city_direction', 'admin_area', 'district', 'metro'] as $taxonomy) {
    foreach (['related_city', 'related_direction', 'related_admin_area', 'related_district'] as $meta_key) {
      register_term_meta($taxonomy, $meta_key, [
        'type' => 'integer',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'absint',
        'auth_callback' => function() { return current_user_can('edit_posts'); },
      ]);
    }
  }
});

add_filter('register_post_type_args', function($args, $post_type) {
  if (isset(FB_CPTS[$post_type])) {
    $args['show_in_rest'] = true;
    $args['rest_base'] = FB_CPTS[$post_type];
    if (empty($args['supports'])) {
      $args['supports'] = ['title', 'editor', 'custom-fields'];
    } elseif (!in_array('custom-fields', $args['supports'], true)) {
      $args['supports'][] = 'custom-fields';
    }
  }
  return $args;
}, 10, 2);

add_filter('register_taxonomy_args', function($args, $taxonomy) {
  if (in_array($taxonomy, FB_REST_TAXONOMIES, true)) {
    $args['show_in_rest'] = true;
  }
  return $args;
}, 10, 2);

add_action('init', function () {
  foreach (['_game_date', 'game_date', '_game_time', 'game_time', '_game_price', 'game_price', 'game_start_local_ts', 'source_message_link', 'game_custom_place_name', 'game_custom_place_address'] as $key) {
    register_post_meta('games', $key, [
      'type' => 'string',
      'single' => true,
      'show_in_rest' => true,
      'auth_callback' => function() { return current_user_can('edit_posts'); },
    ]);
  }

  register_post_meta('games', '_btn_link', [
    'type' => 'string',
    'single' => true,
    'show_in_rest' => true,
    'auth_callback' => function() { return current_user_can('edit_posts'); },
  ]);

  register_post_meta('games', 'game_places', [
    'type' => 'array',
    'single' => true,
    'show_in_rest' => [
      'schema' => [
        'type' => 'array',
        'items' => [
          'type' => 'object',
        ],
      ],
    ],
    'auth_callback' => function() { return current_user_can('edit_posts'); },
  ]);
});

add_action('init', function () {
  foreach (['team_btn_text', 'team_btn_link', 'phone', 'source_message_link'] as $key) {
    register_post_meta('teams', $key, [
      'type' => 'string',
      'single' => true,
      'show_in_rest' => true,
      'auth_callback' => function() { return current_user_can('edit_posts'); },
    ]);
  }

  register_post_meta('teams', '_team_btn_link', [
    'type' => 'string',
    'single' => true,
    'show_in_rest' => true,
    'auth_callback' => function() { return current_user_can('edit_posts'); },
  ]);
});

add_action('init', function () {
  foreach (['player_goal', 'player_btn_text', 'player_btn_link', 'phone', 'source_message_link'] as $key) {
    register_post_meta('players', $key, [
      'type' => 'string',
      'single' => true,
      'show_in_rest' => true,
      'auth_callback' => function() { return current_user_can('edit_posts'); },
    ]);
  }
});

add_action('init', function () {
  foreach (['place_address', 'place_info', 'place_price', 'phone'] as $key) {
    register_post_meta('places', $key, [
      'type' => 'string',
      'single' => true,
      'show_in_rest' => true,
      'auth_callback' => function() { return current_user_can('edit_posts'); },
    ]);
  }

  register_post_meta('places', 'place_gallery', [
    'type' => 'array',
    'single' => true,
    'show_in_rest' => [
      'schema' => [
        'type' => 'array',
        'items' => [
          'type' => 'integer',
        ],
      ],
    ],
    'auth_callback' => function() { return current_user_can('edit_posts'); },
  ]);
});

add_action('rest_after_insert_games', function($post, $request, $creating) {
  $meta = $request->get_param('meta');
  if (is_array($meta)) {
    $pairs = [
      '_game_date' => 'game_date',
      '_game_time' => 'game_time',
      '_game_price' => 'game_price',
      'source_message_link' => 'source_message_link',
      'game_custom_place_name' => 'game_custom_place_name',
      'game_custom_place_address' => 'game_custom_place_address',
    ];

    foreach ($pairs as $src => $dst) {
      if (!array_key_exists($src, $meta)) {
        continue;
      }
      if (function_exists('carbon_set_post_meta')) {
        carbon_set_post_meta($post->ID, $dst, $meta[$src]);
      }
      update_post_meta($post->ID, $dst, $meta[$src]);
    }

    if (array_key_exists('_btn_link', $meta)) {
      if (function_exists('carbon_set_post_meta')) {
        carbon_set_post_meta($post->ID, 'btn_link', (string) $meta['_btn_link']);
      }
      update_post_meta($post->ID, 'btn_link', (string) $meta['_btn_link']);
    }

    if (array_key_exists('game_places', $meta) && is_array($meta['game_places'])) {
      if (function_exists('carbon_set_post_meta')) {
        carbon_set_post_meta($post->ID, 'game_places', $meta['game_places']);
      }
      update_post_meta($post->ID, 'game_places', $meta['game_places']);
    }
  }

  if (function_exists('fballer_sync_game_start_local_ts')) {
    fballer_sync_game_start_local_ts($post->ID);
  }
}, 10, 3);

add_action('rest_after_insert_teams', function($post, $request, $creating) {
  $meta = $request->get_param('meta');
  if (is_array($meta)) {
    $pairs = [
      'team_btn_text' => 'team_btn_text',
      'team_btn_link' => 'team_btn_link',
      'phone' => 'phone',
      'source_message_link' => 'source_message_link',
    ];

    foreach ($pairs as $src => $dst) {
      if (!array_key_exists($src, $meta)) {
        continue;
      }
      if (function_exists('carbon_set_post_meta')) {
        carbon_set_post_meta($post->ID, $dst, (string) $meta[$src]);
      }
      update_post_meta($post->ID, $dst, (string) $meta[$src]);
    }
  }

  $team_level = $request->get_param('team_level');
  if (is_array($team_level) && !empty($team_level)) {
    wp_set_post_terms($post->ID, array_map('intval', $team_level), 'team_level', false);
  }

  $team_position = $request->get_param('team_position');
  if (is_array($team_position) && !empty($team_position)) {
    wp_set_post_terms($post->ID, array_map('intval', $team_position), 'team_position', false);
  }
}, 10, 3);

add_action('rest_after_insert_players', function($post, $request, $creating) {
  $meta = $request->get_param('meta');
  if (is_array($meta)) {
    foreach (['player_goal', 'player_btn_text', 'player_btn_link', 'phone', 'source_message_link'] as $key) {
      if (!array_key_exists($key, $meta)) {
        continue;
      }
      if (function_exists('carbon_set_post_meta')) {
        carbon_set_post_meta($post->ID, $key, (string) $meta[$key]);
      }
      update_post_meta($post->ID, $key, (string) $meta[$key]);
    }
  }

  $team_level = $request->get_param('team_level');
  if (is_array($team_level) && !empty($team_level)) {
    wp_set_post_terms($post->ID, array_map('intval', $team_level), 'team_level', false);
  }

  $team_position = $request->get_param('team_position');
  if (is_array($team_position) && !empty($team_position)) {
    wp_set_post_terms($post->ID, array_map('intval', $team_position), 'team_position', false);
  }
}, 10, 3);

add_action('rest_after_insert_places', function($post, $request, $creating) {
  $meta = $request->get_param('meta');
  if (!is_array($meta)) {
    return;
  }

  foreach (['place_address', 'place_info', 'place_price', 'phone', 'place_gallery'] as $key) {
    if (!array_key_exists($key, $meta)) {
      continue;
    }
    if (function_exists('carbon_set_post_meta')) {
      carbon_set_post_meta($post->ID, $key, $meta[$key]);
    }
    update_post_meta($post->ID, $key, $meta[$key]);
  }
}, 10, 3);
