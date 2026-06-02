<?php

// Защита от прямого доступа к файлу
if ( ! defined('ABSPATH') ) {
    exit;
}

function fballer_get_add_page_url($slug, $template) {
    $page = get_page_by_path($slug);

    if ( ! $page ) {
        $pages = get_pages([
            'meta_key' => '_wp_page_template',
            'meta_value' => $template,
            'number' => 1,
        ]);
        if ( ! empty($pages) ) {
            $page = $pages[0];
        }
    }

    if ( $page ) {
        return get_permalink($page->ID);
    }

    return home_url('/' . trim($slug, '/') . '/');
}

function fballer_parse_date_to_timestamp($date_str) {
    $date_str = trim((string) $date_str);
    if ( $date_str === '' ) return null;

    $timestamp = strtotime($date_str);
    if ( ! $timestamp ) return null;

    return $timestamp;
}

function fballer_parse_time_to_hhmm($time_str) {
    $time_str = trim((string) $time_str);
    if ( $time_str === '' ) return null;

    $parts = explode(':', $time_str);
    if ( count($parts) !== 2 ) return null;

    $h = str_pad((int) $parts[0], 2, '0', STR_PAD_LEFT);
    $m = str_pad((int) $parts[1], 2, '0', STR_PAD_LEFT);

    return $h . $m;
}

function fballer_format_hhmm_to_human($time_str) {
    $time_str = trim((string) $time_str);
    if ( $time_str === '' ) return '';

    if ( strpos($time_str, ':') !== false ) {
        return $time_str;
    }

    if ( preg_match('/^\d{4}$/', $time_str) ) {
        return substr($time_str, 0, 2) . ':' . substr($time_str, 2, 2);
    }

    return $time_str;
}

function fballer_prepare_association_meta($post_ids, $post_type) {
    $post_ids = is_array($post_ids) ? $post_ids : [];
    $data = [];

    foreach ( $post_ids as $id ) {
        $id = (int) $id;
        if ( $id <= 0 ) continue;

        $data[] = [
            'id' => $id,
            'type' => 'post',
            'subtype' => $post_type,
        ];
    }

    return $data;
}

function fballer_get_term_name($term_id, $taxonomy) {
    $term_id = (int) $term_id;
    if ( $term_id <= 0 ) return '';

    $term = get_term($term_id, $taxonomy);
    if ( ! $term || is_wp_error($term) ) return '';

    return (string) $term->name;
}

function fballer_set_post_meta_value($post_id, $key, $value) {
    if ( function_exists('carbon_set_post_meta') ) {
        carbon_set_post_meta($post_id, $key, $value);
        return;
    }

    update_post_meta($post_id, $key, $value);
}

function fballer_get_post_term_ids($post_id, $taxonomy) {
    $terms = wp_get_post_terms((int) $post_id, $taxonomy, ['fields' => 'ids']);
    if ( ! is_array($terms) || is_wp_error($terms) ) {
        return [];
    }

    return array_values(array_filter(array_map('intval', $terms)));
}

function fballer_get_player_goal_label($goal) {
    $goal = (string) $goal;

    if ( $goal === 'game' ) {
        return 'Игру';
    }

    return 'Команду';
}

function fballer_collect_matching_post_ids($query_args, $limit, $ids = []) {
    if ( count($ids) >= $limit ) {
        return array_slice($ids, 0, $limit);
    }

    $query_args['post_status'] = 'publish';
    $query_args['fields'] = 'ids';
    $query_args['posts_per_page'] = $limit;

    if ( ! empty($ids) ) {
        $query_args['post__not_in'] = isset($query_args['post__not_in']) && is_array($query_args['post__not_in'])
            ? array_values(array_unique(array_merge($query_args['post__not_in'], $ids)))
            : $ids;
    }

    $query = new WP_Query($query_args);
    if ( ! empty($query->posts) ) {
        foreach ( $query->posts as $post_id ) {
            $post_id = (int) $post_id;
            if ( $post_id > 0 && ! in_array($post_id, $ids, true) ) {
                $ids[] = $post_id;
                if ( count($ids) >= $limit ) {
                    break;
                }
            }
        }
    }

    wp_reset_postdata();

    return array_slice($ids, 0, $limit);
}

function fballer_get_matching_players_for_team($team_id, $limit = 6) {
    $team_id = (int) $team_id;
    $limit = max(1, (int) $limit);

    $city_ids = fballer_get_post_term_ids($team_id, 'city');
    $level_ids = fballer_get_post_term_ids($team_id, 'team_level');
    $position_ids = fballer_get_post_term_ids($team_id, 'team_position');

    $base_meta_query = [
        [
            'key' => 'player_goal',
            'value' => 'team',
        ],
    ];

    $ids = [];
    $base_args = [
        'post_type' => 'players',
        'orderby' => ['post_date' => 'DESC'],
        'meta_query' => $base_meta_query,
    ];

    if ( ! empty($city_ids) && ! empty($position_ids) && ! empty($level_ids) ) {
        $ids = fballer_collect_matching_post_ids(array_merge($base_args, [
            'tax_query' => [
                'relation' => 'AND',
                [
                    'taxonomy' => 'city',
                    'field' => 'term_id',
                    'terms' => $city_ids,
                ],
                [
                    'taxonomy' => 'team_position',
                    'field' => 'term_id',
                    'terms' => $position_ids,
                    'operator' => 'IN',
                ],
                [
                    'taxonomy' => 'team_level',
                    'field' => 'term_id',
                    'terms' => $level_ids,
                ],
            ],
        ]), $limit, $ids);
    }

    if ( count($ids) < $limit && ! empty($city_ids) && ! empty($position_ids) ) {
        $ids = fballer_collect_matching_post_ids(array_merge($base_args, [
            'tax_query' => [
                'relation' => 'AND',
                [
                    'taxonomy' => 'city',
                    'field' => 'term_id',
                    'terms' => $city_ids,
                ],
                [
                    'taxonomy' => 'team_position',
                    'field' => 'term_id',
                    'terms' => $position_ids,
                    'operator' => 'IN',
                ],
            ],
        ]), $limit, $ids);
    }

    if ( count($ids) < $limit && ! empty($city_ids) ) {
        $ids = fballer_collect_matching_post_ids(array_merge($base_args, [
            'tax_query' => [
                [
                    'taxonomy' => 'city',
                    'field' => 'term_id',
                    'terms' => $city_ids,
                ],
            ],
        ]), $limit, $ids);
    }

    return $ids;
}

function fballer_get_matching_teams_for_player($player_id, $limit = 6) {
    $player_id = (int) $player_id;
    $limit = max(1, (int) $limit);

    $city_ids = fballer_get_post_term_ids($player_id, 'city');
    $level_ids = fballer_get_post_term_ids($player_id, 'team_level');
    $position_ids = fballer_get_post_term_ids($player_id, 'team_position');

    $ids = [];

    if ( ! empty($city_ids) && ! empty($position_ids) && ! empty($level_ids) ) {
        $ids = fballer_collect_matching_post_ids(fballer_apply_recent_teams_query_args([
            'post_type' => 'teams',
            'orderby' => ['post_date' => 'DESC'],
            'tax_query' => [
                'relation' => 'AND',
                [
                    'taxonomy' => 'city',
                    'field' => 'term_id',
                    'terms' => $city_ids,
                ],
                [
                    'taxonomy' => 'team_position',
                    'field' => 'term_id',
                    'terms' => $position_ids,
                    'operator' => 'IN',
                ],
                [
                    'taxonomy' => 'team_level',
                    'field' => 'term_id',
                    'terms' => $level_ids,
                ],
            ],
        ], ! empty($city_ids) ? (int) $city_ids[0] : 0), $limit, $ids);
    }

    if ( count($ids) < $limit && ! empty($city_ids) && ! empty($position_ids) ) {
        $ids = fballer_collect_matching_post_ids(fballer_apply_recent_teams_query_args([
            'post_type' => 'teams',
            'orderby' => ['post_date' => 'DESC'],
            'tax_query' => [
                'relation' => 'AND',
                [
                    'taxonomy' => 'city',
                    'field' => 'term_id',
                    'terms' => $city_ids,
                ],
                [
                    'taxonomy' => 'team_position',
                    'field' => 'term_id',
                    'terms' => $position_ids,
                    'operator' => 'IN',
                ],
            ],
        ], ! empty($city_ids) ? (int) $city_ids[0] : 0), $limit, $ids);
    }

    if ( count($ids) < $limit && ! empty($city_ids) ) {
        $ids = fballer_collect_matching_post_ids(fballer_apply_recent_teams_query_args([
            'post_type' => 'teams',
            'orderby' => ['post_date' => 'DESC'],
            'tax_query' => [
                [
                    'taxonomy' => 'city',
                    'field' => 'term_id',
                    'terms' => $city_ids,
                ],
            ],
        ], ! empty($city_ids) ? (int) $city_ids[0] : 0), $limit, $ids);
    }

    return $ids;
}

function fballer_get_matching_games_for_player($player_id, $limit = 6) {
    $player_id = (int) $player_id;
    $limit = max(1, (int) $limit);

    $city_ids = fballer_get_post_term_ids($player_id, 'city');
    if ( empty($city_ids) ) {
        return [];
    }

    $city_id = (int) $city_ids[0];

    return fballer_collect_matching_post_ids(fballer_apply_upcoming_games_query_args([
        'post_type' => 'games',
        'orderby' => [
            'meta_value_num' => 'ASC',
            'post_date' => 'DESC',
        ],
        'tax_query' => [
            [
                'taxonomy' => 'city',
                'field' => 'term_id',
                'terms' => [$city_id],
            ],
        ],
    ], $city_id), $limit);
}

function fballer_notify_admin($subject, $message, $post_id = 0) {
    $recipients = array_filter([
        get_option('admin_email'),
        'f8aller@yandex.com',
    ]);
    if ( empty($recipients) ) return;

    $site_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    $subject = '[' . $site_name . '] ' . $subject;

    if ( $post_id ) {
        $message .= "\n\nАдминка: " . admin_url('post.php?post=' . (int) $post_id . '&action=edit');
    }

    wp_mail($recipients, $subject, $message);
}

function fballer_validate_antispam_submission() {
    $honeypot = isset($_POST['fb_website']) ? trim(wp_unslash((string) $_POST['fb_website'])) : '';
    if ( $honeypot !== '' ) {
        return [
            'success' => false,
            'errors' => ['Не удалось отправить форму. Обновите страницу и попробуйте еще раз.'],
        ];
    }

    $rendered_at = isset($_POST['fb_rendered_at']) ? (int) $_POST['fb_rendered_at'] : 0;
    $elapsed = $rendered_at > 0 ? time() - $rendered_at : 0;

    if ( $rendered_at <= 0 || $elapsed < 3 ) {
        return [
            'success' => false,
            'errors' => ['Форма отправлена слишком быстро. Проверьте данные и попробуйте еще раз.'],
        ];
    }

    return null;
}

function fballer_render_antispam_fields() {
    $rendered_at = isset($_POST['fb_rendered_at']) ? (int) $_POST['fb_rendered_at'] : time();
    ?>
    <div class="fb-antispam-field" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;">
        <label for="fb-website">Ваш сайт</label>
        <input type="text" id="fb-website" name="fb_website" value="" tabindex="-1" autocomplete="off">
    </div>
    <input type="hidden" name="fb_rendered_at" value="<?php echo esc_attr($rendered_at); ?>">
    <?php
}

function fballer_handle_frontend_submission($type) {
    if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) return null;

    $type = (string) $type;
    $nonce_action = 'fb_add_' . $type;

    if ( ! isset($_POST['_fb_nonce']) || ! wp_verify_nonce($_POST['_fb_nonce'], $nonce_action) ) {
        return [
            'success' => false,
            'errors' => ['Неверный или истекший токен формы. Попробуйте еще раз.'],
        ];
    }

    $antispam_result = fballer_validate_antispam_submission();
    if ( $antispam_result ) {
        return $antispam_result;
    }

    $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';

    $errors = [];
    if ( $type !== 'games' && $title === '' ) $errors[] = 'Заполните название.';

    if ( $type === 'games' && $title === '' ) {
        $raw_date = sanitize_text_field($_POST['game_date'] ?? '');
        $raw_time = sanitize_text_field($_POST['game_time'] ?? '');
        $title_parts = [];

        if ( $raw_date !== '' ) {
            $title_parts[] = $raw_date;
        }

        if ( $raw_time !== '' ) {
            $title_parts[] = fballer_format_hhmm_to_human($raw_time);
        }

        if ( $phone !== '' ) {
            $title_parts[] = $phone;
        }

        if ( ! empty($title_parts) ) {
            $title = 'Игра ' . implode(' ', $title_parts);
        } else {
            $title = 'Игра ' . current_time('d.m.Y H:i');
        }
    }

    if ( ! empty($errors) ) {
        return [
            'success' => false,
            'errors' => $errors,
        ];
    }

    $post_type = $type;
    $post_id = wp_insert_post([
        'post_type' => $post_type,
        'post_title' => $title,
        'post_content' => $content,
        'post_status' => 'pending',
    ], true);

    if ( is_wp_error($post_id) ) {
        return [
            'success' => false,
            'errors' => ['Не удалось создать запись. Попробуйте позже.'],
        ];
    }

    $city = isset($_POST['city']) ? (int) $_POST['city'] : 0;
    if ( $city > 0 ) {
        wp_set_post_terms($post_id, [$city], 'city', false);
    }

    if ( $type === 'games' ) {
        $game_date = fballer_parse_date_to_timestamp($_POST['game_date'] ?? '');
        $game_time = fballer_parse_time_to_hhmm($_POST['game_time'] ?? '');
        $game_price = isset($_POST['game_price']) ? (int) $_POST['game_price'] : '';
        $game_start_local_ts = fballer_build_game_start_local_timestamp($game_date, $game_time, $city);
        $game_custom_place_name = sanitize_text_field($_POST['text-place-name'] ?? '');
        $game_custom_place_address = sanitize_text_field($_POST['text-address'] ?? '');

        fballer_set_post_meta_value($post_id, 'game_date', $game_date);
        fballer_set_post_meta_value($post_id, 'game_time', $game_time);
        fballer_set_post_meta_value($post_id, 'game_price', $game_price);
        fballer_set_post_meta_value($post_id, 'game_start_local_ts', $game_start_local_ts);
        if ( $game_custom_place_name !== '' ) {
            fballer_set_post_meta_value($post_id, 'game_custom_place_name', $game_custom_place_name);
        }
        if ( $game_custom_place_address !== '' ) {
            fballer_set_post_meta_value($post_id, 'game_custom_place_address', $game_custom_place_address);
        }

        $btn_text = isset($_POST['btn_text']) ? sanitize_text_field($_POST['btn_text']) : '';
        $btn_link = isset($_POST['btn_link']) ? esc_url_raw($_POST['btn_link']) : '';
        if ( $btn_text !== '' ) fballer_set_post_meta_value($post_id, 'btn_text', $btn_text);
        if ( $btn_link !== '' ) fballer_set_post_meta_value($post_id, 'btn_link', $btn_link);

        $format = isset($_POST['game_format']) ? (int) $_POST['game_format'] : 0;
        if ( $format > 0 ) {
            wp_set_post_terms($post_id, [$format], 'game_format', false);
        }

        $game_places = fballer_prepare_association_meta($_POST['game_places'] ?? [], 'places');
        fballer_set_post_meta_value($post_id, 'game_places', $game_places);

        $city_name = fballer_get_term_name($city, 'city');
        $format_name = fballer_get_term_name($format, 'game_format');
        $selected_place_title = '—';
        if ( ! empty($game_places) && ! empty($game_places[0]['id']) ) {
            $selected_place = get_post((int) $game_places[0]['id']);
            if ( $selected_place instanceof WP_Post ) {
                $selected_place_title = $selected_place->post_title;
            }
        }
        $message_lines = [
            'Новая игра (на модерации)',
            'Название: ' . $title,
            'Город: ' . ($city_name !== '' ? $city_name : '—'),
            'Поле из списка: ' . $selected_place_title,
            'Свое поле: ' . ($game_custom_place_name !== '' ? $game_custom_place_name : '—'),
            'Адрес своего поля: ' . ($game_custom_place_address !== '' ? $game_custom_place_address : '—'),
            'Формат: ' . ($format_name !== '' ? $format_name : '—'),
            'Дата: ' . ($game_date ? date_i18n('d.m.Y', $game_date) : '—'),
            'Время: ' . ($game_time ? substr($game_time, 0, 2) . ':' . substr($game_time, 2, 2) : '—'),
            'Цена: ' . ($game_price !== '' ? $game_price : '—'),
            'Телефон: ' . ($phone !== '' ? $phone : '—'),
            'Telegram: ' . ($btn_link !== '' ? $btn_link : '—'),
        ];
        fballer_notify_admin('Новая игра', implode("\n", $message_lines), $post_id);
    }

    if ( $type === 'places' ) {
        $place_address = sanitize_text_field($_POST['place_address'] ?? '');
        $place_info = wp_kses_post($_POST['place_info'] ?? '');
        $place_price = sanitize_text_field($_POST['place_price'] ?? '');
        $place_phone = sanitize_text_field($_POST['phone'] ?? '');

        fballer_set_post_meta_value($post_id, 'place_address', $place_address);
        fballer_set_post_meta_value($post_id, 'place_info', $place_info);
        fballer_set_post_meta_value($post_id, 'place_price', $place_price);
        fballer_set_post_meta_value($post_id, 'phone', $place_phone);

        $field_size = isset($_POST['field_size']) ? (int) $_POST['field_size'] : 0;
        if ( $field_size > 0 ) wp_set_post_terms($post_id, [$field_size], 'field_size', false);

        $coating = isset($_POST['coating']) ? (int) $_POST['coating'] : 0;
        if ( $coating > 0 ) wp_set_post_terms($post_id, [$coating], 'coating', false);

        $field_types = isset($_POST['field_type']) && is_array($_POST['field_type']) ? array_values(array_filter(array_map('intval', $_POST['field_type']))) : [];
        if ( ! empty($field_types) ) {
            wp_set_post_terms($post_id, $field_types, 'field_type', false);
        }

        $formats = isset($_POST['game_format']) && is_array($_POST['game_format']) ? array_values(array_filter(array_map('intval', $_POST['game_format']))) : [];
        if ( ! empty($formats) ) {
            wp_set_post_terms($post_id, $formats, 'game_format', false);
        }

        $features = isset($_POST['features']) && is_array($_POST['features']) ? array_map('intval', $_POST['features']) : [];
        if ( ! empty($features) ) wp_set_post_terms($post_id, $features, 'features', false);

        $city_name = fballer_get_term_name($city, 'city');
        $message_lines = [
            'Новое поле (на модерации)',
            'Название: ' . $title,
            'Город: ' . ($city_name !== '' ? $city_name : '—'),
            'Адрес: ' . ($place_address !== '' ? $place_address : '—'),
            'Телефон: ' . ($place_phone !== '' ? $place_phone : '—'),
            'Цена: ' . ($place_price !== '' ? $place_price : '—'),
        ];
        fballer_notify_admin('Новое поле', implode("\n", $message_lines), $post_id);
    }

    if ( $type === 'champs' ) {
        $champ_start = fballer_parse_date_to_timestamp($_POST['champ_start'] ?? '');
        $champ_end = fballer_parse_date_to_timestamp($_POST['champ_end'] ?? '');

        $champ_address = sanitize_text_field($_POST['champ_address'] ?? '');
        $champ_reward = sanitize_text_field($_POST['champ_reward'] ?? '');
        $champ_price = sanitize_text_field($_POST['champ_price'] ?? '');
        $champ_website = esc_url_raw($_POST['champ_website'] ?? '');
        $champ_phone = sanitize_text_field($_POST['phone'] ?? '');

        fballer_set_post_meta_value($post_id, 'champ_start', $champ_start);
        fballer_set_post_meta_value($post_id, 'champ_end', $champ_end);
        fballer_set_post_meta_value($post_id, 'champ_address', $champ_address);
        fballer_set_post_meta_value($post_id, 'champ_reward', $champ_reward);
        fballer_set_post_meta_value($post_id, 'champ_price', $champ_price);
        fballer_set_post_meta_value($post_id, 'champ_website', $champ_website);
        fballer_set_post_meta_value($post_id, 'phone', $champ_phone);

        $formats = isset($_POST['game_format']) && is_array($_POST['game_format']) ? array_values(array_filter(array_map('intval', $_POST['game_format']))) : [];
        if ( ! empty($formats) ) {
            wp_set_post_terms($post_id, $formats, 'game_format', false);
        }

        $champ_places = fballer_prepare_association_meta($_POST['champ_places'] ?? [], 'places');
        fballer_set_post_meta_value($post_id, 'champ_places', $champ_places);

        $city_name = fballer_get_term_name($city, 'city');
        $format_names = [];
        foreach ( $formats as $format_id ) {
            $format_name = fballer_get_term_name($format_id, 'game_format');
            if ( $format_name !== '' ) {
                $format_names[] = $format_name;
            }
        }
        $message_lines = [
            'Новый чемпионат (на модерации)',
            'Название: ' . $title,
            'Город: ' . ($city_name !== '' ? $city_name : '—'),
            'Формат: ' . (! empty($format_names) ? implode(', ', $format_names) : '—'),
            'Старт: ' . ($champ_start ? date_i18n('d.m.Y', $champ_start) : '—'),
            'Окончание: ' . ($champ_end ? date_i18n('d.m.Y', $champ_end) : '—'),
            'Адрес: ' . ($champ_address !== '' ? $champ_address : '—'),
            'Телефон: ' . ($champ_phone !== '' ? $champ_phone : '—'),
            'Цена: ' . ($champ_price !== '' ? $champ_price : '—'),
            'Сайт: ' . ($champ_website !== '' ? $champ_website : '—'),
            'Приз: ' . ($champ_reward !== '' ? $champ_reward : '—'),
        ];
        fballer_notify_admin('Новый чемпионат', implode("\n", $message_lines), $post_id);
    }

    if ( $type === 'teams' ) {
        $team_phone = sanitize_text_field($_POST['phone'] ?? '');
        $team_btn_text = sanitize_text_field($_POST['team_btn_text'] ?? 'Написать');
        $team_btn_link = esc_url_raw($_POST['team_btn_link'] ?? '');

        if ( $team_phone !== '' ) {
            fballer_set_post_meta_value($post_id, 'phone', $team_phone);
        }
        if ( $team_btn_text !== '' ) {
            fballer_set_post_meta_value($post_id, 'team_btn_text', $team_btn_text);
        }
        if ( $team_btn_link !== '' ) {
            fballer_set_post_meta_value($post_id, 'team_btn_link', $team_btn_link);
        }

        $team_level = isset($_POST['team_level']) ? (int) $_POST['team_level'] : 0;
        if ( $team_level > 0 ) {
            wp_set_post_terms($post_id, [$team_level], 'team_level', false);
        }

        $team_positions = isset($_POST['team_position']) && is_array($_POST['team_position']) ? array_values(array_filter(array_map('intval', $_POST['team_position']))) : [];
        if ( ! empty($team_positions) ) {
            wp_set_post_terms($post_id, $team_positions, 'team_position', false);
        }

        $city_name = fballer_get_term_name($city, 'city');
        $team_level_name = fballer_get_term_name($team_level, 'team_level');
        $position_names = [];
        foreach ( $team_positions as $position_id ) {
            $position_name = fballer_get_term_name($position_id, 'team_position');
            if ( $position_name !== '' ) {
                $position_names[] = $position_name;
            }
        }

        $message_lines = [
            'Новый поиск игрока (на модерации)',
            'Название: ' . $title,
            'Город: ' . ($city_name !== '' ? $city_name : '—'),
            'Уровень: ' . ($team_level_name !== '' ? $team_level_name : '—'),
            'Позиции: ' . (! empty($position_names) ? implode(', ', $position_names) : '—'),
            'Телефон: ' . ($team_phone !== '' ? $team_phone : '—'),
            'Telegram: ' . ($team_btn_link !== '' ? $team_btn_link : '—'),
        ];
        fballer_notify_admin('Новый поиск игрока', implode("\n", $message_lines), $post_id);
    }

    if ( $type === 'players' ) {
        $player_phone = sanitize_text_field($_POST['phone'] ?? '');
        $player_goal = sanitize_text_field($_POST['player_goal'] ?? 'team');
        $player_btn_text = sanitize_text_field($_POST['player_btn_text'] ?? 'Написать');
        $player_btn_link = esc_url_raw($_POST['player_btn_link'] ?? '');

        if ( ! in_array($player_goal, ['team', 'game'], true) ) {
            $player_goal = 'team';
        }

        if ( $player_phone !== '' ) {
            fballer_set_post_meta_value($post_id, 'phone', $player_phone);
        }
        if ( $player_btn_text !== '' ) {
            fballer_set_post_meta_value($post_id, 'player_btn_text', $player_btn_text);
        }
        if ( $player_btn_link !== '' ) {
            fballer_set_post_meta_value($post_id, 'player_btn_link', $player_btn_link);
        }
        fballer_set_post_meta_value($post_id, 'player_goal', $player_goal);

        $team_level = isset($_POST['team_level']) ? (int) $_POST['team_level'] : 0;
        if ( $team_level > 0 ) {
            wp_set_post_terms($post_id, [$team_level], 'team_level', false);
        }

        $team_positions = isset($_POST['team_position']) && is_array($_POST['team_position']) ? array_values(array_filter(array_map('intval', $_POST['team_position']))) : [];
        if ( ! empty($team_positions) ) {
            wp_set_post_terms($post_id, $team_positions, 'team_position', false);
        }

        $city_name = fballer_get_term_name($city, 'city');
        $team_level_name = fballer_get_term_name($team_level, 'team_level');
        $position_names = [];
        foreach ( $team_positions as $position_id ) {
            $position_name = fballer_get_term_name($position_id, 'team_position');
            if ( $position_name !== '' ) {
                $position_names[] = $position_name;
            }
        }

        $message_lines = [
            'Новый поиск команды или игры (на модерации)',
            'Название: ' . $title,
            'Что ищет: ' . fballer_get_player_goal_label($player_goal),
            'Город: ' . ($city_name !== '' ? $city_name : '—'),
            'Уровень: ' . ($team_level_name !== '' ? $team_level_name : '—'),
            'Позиции: ' . (! empty($position_names) ? implode(', ', $position_names) : '—'),
            'Телефон: ' . ($player_phone !== '' ? $player_phone : '—'),
            'Telegram: ' . ($player_btn_link !== '' ? $player_btn_link : '—'),
        ];
        fballer_notify_admin('Новый поиск команды или игры', implode("\n", $message_lines), $post_id);
    }

    return [
        'success' => true,
        'post_id' => $post_id,
    ];
}
