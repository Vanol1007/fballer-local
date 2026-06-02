<?php

if ( ! defined('ABSPATH') ) {
	exit;
}

if ( ! defined('FBALLER_GEO_EXTERNAL_ID_META') ) {
	define('FBALLER_GEO_EXTERNAL_ID_META', '_fballer_geo_external_id');
}

function fballer_geo_import_supported_types() {
	return array(
		'city' => 'city',
		'city_direction' => 'city_direction',
		'admin_area' => 'admin_area',
		'district' => 'district',
		'metro' => 'metro',
	);
}

function fballer_geo_import_supported_taxonomies() {
	return array_values(fballer_geo_import_supported_types());
}

function fballer_geo_import_boolean_to_meta_value( $value ) {
	return $value ? 'yes' : '';
}

function fballer_geo_import_normalize_coordinate( $value ) {
	if ( $value === null || $value === '' ) {
		return '';
	}

	if ( is_string( $value ) ) {
		$value = str_replace(',', '.', trim($value));
	}

	if ( ! is_numeric($value) ) {
		return null;
	}

	$float = (float) $value;
	return rtrim(rtrim(sprintf('%.8F', $float), '0'), '.');
}

function fballer_geo_import_normalize_city_payload( $city ) {
	$city = is_array($city) ? $city : array();

	$coordinates = isset($city['coordinates']) && is_array($city['coordinates']) ? $city['coordinates'] : array();
	$center = isset($city['center']) && is_array($city['center']) ? $city['center'] : array();
	$bounds = isset($city['bounds']) && is_array($city['bounds']) ? $city['bounds'] : array();
	$flags = isset($city['flags']) && is_array($city['flags']) ? $city['flags'] : array();

	return array(
		'id' => isset($city['id']) ? trim((string) $city['id']) : '',
		'slug' => isset($city['slug']) ? sanitize_title((string) $city['slug']) : '',
		'name' => isset($city['name']) ? trim((string) $city['name']) : '',
		'type' => isset($city['type']) ? trim((string) $city['type']) : 'city',
		'timezone' => isset($city['timezone']) ? trim((string) $city['timezone']) : '',
		'map_zoom' => isset($city['map_zoom']) ? (int) $city['map_zoom'] : 0,
		'geo_lat' => fballer_geo_import_normalize_coordinate($coordinates['lat'] ?? null),
		'geo_lng' => fballer_geo_import_normalize_coordinate($coordinates['lng'] ?? null),
		'center_lat' => fballer_geo_import_normalize_coordinate($center['lat'] ?? null),
		'center_lng' => fballer_geo_import_normalize_coordinate($center['lng'] ?? null),
		'bounds_north' => fballer_geo_import_normalize_coordinate($bounds['north'] ?? null),
		'bounds_south' => fballer_geo_import_normalize_coordinate($bounds['south'] ?? null),
		'bounds_east' => fballer_geo_import_normalize_coordinate($bounds['east'] ?? null),
		'bounds_west' => fballer_geo_import_normalize_coordinate($bounds['west'] ?? null),
		'flags' => array(
			'has_directions' => ! empty($flags['has_directions']),
			'has_admin_areas' => ! empty($flags['has_admin_areas']),
			'has_districts' => array_key_exists('has_districts', $flags) ? ! empty($flags['has_districts']) : true,
			'has_metro' => ! empty($flags['has_metro']),
		),
		'nodes' => isset($city['nodes']) && is_array($city['nodes']) ? array_values($city['nodes']) : array(),
	);
}

function fballer_geo_import_normalize_node_payload( $node ) {
	$node = is_array($node) ? $node : array();
	$coordinates = isset($node['coordinates']) && is_array($node['coordinates']) ? $node['coordinates'] : array();

	return array(
		'id' => isset($node['id']) ? trim((string) $node['id']) : '',
		'slug' => isset($node['slug']) ? sanitize_title((string) $node['slug']) : '',
		'name' => isset($node['name']) ? trim((string) $node['name']) : '',
		'type' => isset($node['type']) ? trim((string) $node['type']) : '',
		'city_id' => isset($node['city_id']) ? trim((string) $node['city_id']) : '',
		'parent_id' => isset($node['parent_id']) ? trim((string) $node['parent_id']) : '',
		'geo_lat' => fballer_geo_import_normalize_coordinate($coordinates['lat'] ?? null),
		'geo_lng' => fballer_geo_import_normalize_coordinate($coordinates['lng'] ?? null),
	);
}

function fballer_geo_import_load_json_file( $file_path ) {
	if ( ! is_readable($file_path) ) {
		return new WP_Error('geo_import_file_missing', 'JSON file is not readable: ' . $file_path);
	}

	$raw = file_get_contents($file_path);
	if ( $raw === false ) {
		return new WP_Error('geo_import_file_read', 'Unable to read JSON file: ' . $file_path);
	}

	$data = json_decode($raw, true);
	if ( ! is_array($data) ) {
		return new WP_Error('geo_import_invalid_json', 'Invalid JSON in file: ' . $file_path);
	}

	return $data;
}

function fballer_geo_import_collect_input_files( $input_path ) {
	$input_path = (string) $input_path;
	if ( $input_path === '' ) {
		return new WP_Error('geo_import_missing_path', 'Provide --file=/path/to/file-or-directory');
	}

	if ( is_dir($input_path) ) {
		$files = glob(trailingslashit($input_path) . '*.json');
		if ( ! is_array($files) || empty($files) ) {
			return new WP_Error('geo_import_empty_dir', 'No JSON files found in directory: ' . $input_path);
		}

		sort($files, SORT_NATURAL);
		return array_values($files);
	}

	if ( is_file($input_path) ) {
		return array($input_path);
	}

	return new WP_Error('geo_import_invalid_path', 'Path not found: ' . $input_path);
}

function fballer_geo_import_extract_city_payloads( $decoded, $source_file ) {
	$decoded = is_array($decoded) ? $decoded : array();
	$source_file = (string) $source_file;

	$cities = array();
	if ( isset($decoded['cities']) && is_array($decoded['cities']) ) {
		$cities = $decoded['cities'];
	} elseif ( isset($decoded['id']) && isset($decoded['type']) && 'city' === $decoded['type'] ) {
		$cities = array($decoded);
	}

	if ( empty($cities) ) {
		return new WP_Error('geo_import_missing_cities', 'Expected top-level city object or cities[] in file: ' . $source_file);
	}

	$normalized = array();
	foreach ( $cities as $city ) {
		$normalized[] = fballer_geo_import_normalize_city_payload($city);
	}

	return $normalized;
}

function fballer_geo_import_find_term_by_external_id( $taxonomy, $external_id ) {
	$terms = get_terms(array(
		'taxonomy' => $taxonomy,
		'hide_empty' => false,
		'meta_query' => array(
			array(
				'key' => FBALLER_GEO_EXTERNAL_ID_META,
				'value' => $external_id,
			),
		),
	));

	if ( is_wp_error($terms) || empty($terms) ) {
		return null;
	}

	return $terms[0];
}

function fballer_geo_import_find_city_term( $city ) {
	$city_by_external_id = fballer_geo_import_find_term_by_external_id('city', $city['id']);
	if ( $city_by_external_id ) {
		return $city_by_external_id;
	}

	if ( $city['slug'] !== '' ) {
		$existing = get_term_by('slug', $city['slug'], 'city');
		if ( $existing && ! is_wp_error($existing) ) {
			return $existing;
		}
	}

	if ( $city['name'] !== '' ) {
		$terms = get_terms(array(
			'taxonomy' => 'city',
			'name' => $city['name'],
			'parent' => 0,
			'hide_empty' => false,
		));

		if ( is_array($terms) && ! empty($terms) ) {
			return $terms[0];
		}
	}

	return null;
}

function fballer_geo_import_find_geo_term( $node, $city_term_id ) {
	$taxonomy = $node['type'];
	$existing = fballer_geo_import_find_term_by_external_id($taxonomy, $node['id']);
	if ( $existing ) {
		return $existing;
	}

	if ( $node['slug'] !== '' ) {
		$existing = get_term_by('slug', $node['slug'], $taxonomy);
		if ( $existing && ! is_wp_error($existing) ) {
			$related_city = (int) get_term_meta($existing->term_id, 'related_city', true);
			$is_root = 'yes' === get_term_meta($existing->term_id, '_fballer_geo_city_root', true);
			if ( ! $is_root && $related_city === (int) $city_term_id ) {
				return $existing;
			}
		}
	}

	return null;
}

function fballer_geo_import_preview_term_id( $type, $external_id ) {
	return 'dry-run:' . $type . ':' . $external_id;
}

function fballer_geo_import_preview_city_root_term_id( $taxonomy, $city_external_id ) {
	return 'dry-run:' . $taxonomy . ':city-root:' . $city_external_id;
}

function fballer_geo_import_log( $result, $level, $message ) {
	$result['logs'][ $level ][] = $message;

	if ( isset($result['counts'][ $level ]) ) {
		$result['counts'][ $level ]++;
	}

	return $result;
}

function fballer_geo_import_update_meta( $object_type, $object_id, $meta_key, $new_value, $dry_run, &$result, $context ) {
	$meta_key = (string) $meta_key;

	if ( $dry_run ) {
		$object_id = 0;
	} else {
		$object_id = (int) $object_id;
	}

	if ( $object_type === 'term' ) {
		$current_value = (string) get_term_meta($object_id, $meta_key, true);
	} else {
		$current_value = (string) get_post_meta($object_id, $meta_key, true);
	}

	$new_value = (string) $new_value;
	if ( $current_value === $new_value ) {
		return;
	}

	if ( $dry_run ) {
		$result = fballer_geo_import_log($result, 'updated', sprintf('%s meta %s: "%s" -> "%s"', $context, $meta_key, $current_value, $new_value));
		return;
	}

	if ( $object_type === 'term' ) {
		if ( $new_value === '' ) {
			if ( function_exists('carbon_set_term_meta') ) {
				carbon_set_term_meta($object_id, $meta_key, '');
			}
			delete_term_meta($object_id, $meta_key);
		} else {
			if ( function_exists('carbon_set_term_meta') ) {
				carbon_set_term_meta($object_id, $meta_key, $new_value);
			}
			update_term_meta($object_id, $meta_key, $new_value);
		}
	} else {
		if ( $new_value === '' ) {
			delete_post_meta($object_id, $meta_key);
		} else {
			update_post_meta($object_id, $meta_key, $new_value);
		}
	}

	$result = fballer_geo_import_log($result, 'updated', sprintf('%s meta %s synced', $context, $meta_key));
}

function fballer_geo_import_upsert_city( $city, $dry_run, &$result ) {
	$existing = fballer_geo_import_find_city_term($city);
	$term_id = $existing ? (int) $existing->term_id : 0;
	$context = sprintf('city[%s]', $city['id']);

	if ( $term_id > 0 ) {
		$update_args = array();
		if ( $city['name'] !== '' && $existing->name !== $city['name'] ) {
			$update_args['name'] = $city['name'];
		}
		if ( $city['slug'] !== '' && $existing->slug !== $city['slug'] ) {
			$update_args['slug'] = $city['slug'];
		}
		if ( $existing->parent !== 0 ) {
			$update_args['parent'] = 0;
		}

		if ( ! empty($update_args) ) {
			if ( $dry_run ) {
				$result = fballer_geo_import_log($result, 'updated', $context . ' term will be updated');
			} else {
				$updated = wp_update_term($term_id, 'city', $update_args);
				if ( is_wp_error($updated) ) {
					return $updated;
				}
			}
		} else {
			$result = fballer_geo_import_log($result, 'skipped', $context . ' already up to date');
		}
	} else {
		if ( $dry_run ) {
			$result = fballer_geo_import_log($result, 'created', $context . ' will be created');
			$term_id = fballer_geo_import_preview_term_id('city', $city['id']);
		} else {
			$inserted = wp_insert_term($city['name'], 'city', array(
				'slug' => $city['slug'],
				'parent' => 0,
			));

			if ( is_wp_error($inserted) ) {
				return $inserted;
			}

			$term_id = (int) $inserted['term_id'];
			$result = fballer_geo_import_log($result, 'created', $context . ' created');
		}
	}

	if ( ! $dry_run && is_int($term_id) && $term_id > 0 ) {
		fballer_geo_import_update_meta('term', $term_id, FBALLER_GEO_EXTERNAL_ID_META, $city['id'], false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'city_timezone', $city['timezone'], false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'has_directions', fballer_geo_import_boolean_to_meta_value($city['flags']['has_directions']), false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'has_admin_areas', fballer_geo_import_boolean_to_meta_value($city['flags']['has_admin_areas']), false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'has_districts', fballer_geo_import_boolean_to_meta_value($city['flags']['has_districts']), false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'has_metro', fballer_geo_import_boolean_to_meta_value($city['flags']['has_metro']), false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'geo_lat', $city['geo_lat'], false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'geo_lng', $city['geo_lng'], false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'map_zoom', $city['map_zoom'] > 0 ? (string) $city['map_zoom'] : '', false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'map_center_lat', $city['center_lat'], false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'map_center_lng', $city['center_lng'], false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'map_bounds_north', $city['bounds_north'], false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'map_bounds_south', $city['bounds_south'], false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'map_bounds_east', $city['bounds_east'], false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'map_bounds_west', $city['bounds_west'], false, $result, $context);
	} elseif ( $dry_run ) {
		$result = fballer_geo_import_log($result, 'updated', $context . ' meta will be synced');
	}

	return array(
		'term_id' => $term_id,
		'result' => $result,
	);
}

function fballer_geo_import_validate_city( $city ) {
	if ( $city['id'] === '' ) {
		return new WP_Error('geo_import_city_id_missing', 'City item must have non-empty id');
	}

	if ( $city['slug'] === '' ) {
		return new WP_Error('geo_import_city_slug_missing', 'City ' . $city['id'] . ' must have non-empty slug');
	}

	if ( $city['name'] === '' ) {
		return new WP_Error('geo_import_city_name_missing', 'City ' . $city['id'] . ' must have non-empty name');
	}

	if ( 'city' !== $city['type'] ) {
		return new WP_Error('geo_import_city_type_invalid', 'City ' . $city['id'] . ' must have type=city');
	}

	if ( $city['timezone'] === '' ) {
		return new WP_Error('geo_import_city_timezone_missing', 'City ' . $city['id'] . ' must have timezone');
	}

	try {
		new DateTimeZone($city['timezone']);
	} catch ( Exception $exception ) {
		return new WP_Error('geo_import_city_timezone_invalid', 'Invalid timezone for city ' . $city['id'] . ': ' . $city['timezone']);
	}

	return true;
}

function fballer_geo_import_validate_node( $node, $city ) {
	$supported_types = fballer_geo_import_supported_types();

	if ( $node['id'] === '' ) {
		return new WP_Error('geo_import_node_id_missing', 'Geo node in city ' . $city['id'] . ' must have non-empty id');
	}

	if ( $node['slug'] === '' ) {
		return new WP_Error('geo_import_node_slug_missing', 'Geo node ' . $node['id'] . ' must have non-empty slug');
	}

	if ( $node['name'] === '' ) {
		return new WP_Error('geo_import_node_name_missing', 'Geo node ' . $node['id'] . ' must have non-empty name');
	}

	if ( ! isset($supported_types[ $node['type'] ]) || 'city' === $node['type'] ) {
		return new WP_Error('geo_import_node_type_invalid', 'Geo node ' . $node['id'] . ' has unsupported type: ' . $node['type']);
	}

	if ( $node['city_id'] !== $city['id'] ) {
		return new WP_Error('geo_import_node_city_mismatch', 'Geo node ' . $node['id'] . ' must reference city_id=' . $city['id']);
	}

	return true;
}

function fballer_geo_import_resolve_parent_meta( $node, $term_ids_by_external_id, $city_term_id, $city_external_id = '' ) {
	$mapping = array(
		'city_direction' => array(
			'allowed_parents' => array(''),
			'related_direction' => 0,
			'related_admin_area' => 0,
			'related_district' => 0,
		),
		'admin_area' => array(
			'allowed_parents' => array('', 'city_direction'),
			'related_admin_area' => 0,
			'related_district' => 0,
		),
		'district' => array(
			'allowed_parents' => array('', 'city_direction', 'admin_area'),
			'related_district' => 0,
		),
		'metro' => array(
			'allowed_parents' => array('', 'city_direction', 'admin_area', 'district'),
		),
	);

	$config = $mapping[ $node['type'] ];
	$parent_node = null;

	if ( $node['parent_id'] !== '' ) {
		if ( ! isset($term_ids_by_external_id[ $node['parent_id'] ]) ) {
			return new WP_Error('geo_import_parent_missing', 'Parent ' . $node['parent_id'] . ' for node ' . $node['id'] . ' was not imported before child');
		}

		$parent_node = $term_ids_by_external_id[ $node['parent_id'] ];
		if ( ! in_array($parent_node['type'], $config['allowed_parents'], true) ) {
			return new WP_Error('geo_import_parent_type_invalid', 'Parent ' . $node['parent_id'] . ' has invalid type for node ' . $node['id']);
		}
	}

	$city_root_term_id = 0;
	if ( is_int($city_term_id) && $city_term_id > 0 && function_exists('fballer_get_geo_city_root_term_id_9842') ) {
		$city_root_term_id = (int) fballer_get_geo_city_root_term_id_9842($node['type'], $city_term_id);
	} elseif ( is_string($city_term_id) && 0 === strpos($city_term_id, 'dry-run:') ) {
		$city_root_term_id = fballer_geo_import_preview_city_root_term_id($node['type'], $city_external_id);
	}

	if ( empty($city_root_term_id) ) {
		return new WP_Error('geo_import_city_root_missing', 'Unable to resolve city root term for taxonomy ' . $node['type'] . ' and city term ' . $city_term_id);
	}

	$meta = array(
		'parent_term_id' => $city_root_term_id,
		'related_city' => (int) $city_term_id,
		'related_direction' => 0,
		'related_admin_area' => 0,
		'related_district' => 0,
	);

	if ( ! $parent_node ) {
		return $meta;
	}

	switch ( $parent_node['type'] ) {
		case 'city_direction':
			$meta['related_direction'] = $parent_node['term_id'];
			break;
		case 'admin_area':
			$meta['related_admin_area'] = $parent_node['term_id'];
			$meta['related_direction'] = (int) ($parent_node['related_direction'] ?? 0);
			break;
		case 'district':
			$meta['related_district'] = $parent_node['term_id'];
			$meta['related_admin_area'] = (int) ($parent_node['related_admin_area'] ?? 0);
			$meta['related_direction'] = (int) ($parent_node['related_direction'] ?? 0);
			break;
	}

	if ( 'metro' === $node['type'] && 'district' === $parent_node['type'] ) {
		$meta['related_district'] = $parent_node['term_id'];
	}

	return $meta;
}

function fballer_geo_import_upsert_node( $node, $city_term_id, $parent_meta, $dry_run, &$result ) {
	$existing = fballer_geo_import_find_geo_term($node, $city_term_id);
	$term_id = $existing ? (int) $existing->term_id : 0;
	$context = sprintf('%s[%s]', $node['type'], $node['id']);

	if ( $term_id > 0 ) {
		$update_args = array();
		if ( $node['name'] !== '' && $existing->name !== $node['name'] ) {
			$update_args['name'] = $node['name'];
		}
		if ( $node['slug'] !== '' && $existing->slug !== $node['slug'] ) {
			$update_args['slug'] = $node['slug'];
		}
		if ( $existing->parent !== (int) $parent_meta['parent_term_id'] ) {
			$update_args['parent'] = (int) $parent_meta['parent_term_id'];
		}

		if ( ! empty($update_args) ) {
			if ( $dry_run ) {
				$result = fballer_geo_import_log($result, 'updated', $context . ' term will be updated');
			} else {
				$updated = wp_update_term($term_id, $node['type'], $update_args);
				if ( is_wp_error($updated) ) {
					return $updated;
				}
			}
		} else {
			$result = fballer_geo_import_log($result, 'skipped', $context . ' already up to date');
		}
	} else {
		if ( $dry_run ) {
			$result = fballer_geo_import_log($result, 'created', $context . ' will be created');
			$term_id = fballer_geo_import_preview_term_id($node['type'], $node['id']);
		} else {
			$inserted = wp_insert_term($node['name'], $node['type'], array(
				'slug' => $node['slug'],
				'parent' => (int) $parent_meta['parent_term_id'],
			));

			if ( is_wp_error($inserted) ) {
				return $inserted;
			}

			$term_id = (int) $inserted['term_id'];
			$result = fballer_geo_import_log($result, 'created', $context . ' created');
		}
	}

	if ( ! $dry_run && is_int($term_id) && $term_id > 0 ) {
		fballer_geo_import_update_meta('term', $term_id, FBALLER_GEO_EXTERNAL_ID_META, $node['id'], false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'related_city', (string) $parent_meta['related_city'], false, $result, $context);

		if ( 'city_direction' !== $node['type'] ) {
			fballer_geo_import_update_meta('term', $term_id, 'related_direction', (string) $parent_meta['related_direction'], false, $result, $context);
		}

		if ( in_array($node['type'], array('district', 'metro'), true) ) {
			fballer_geo_import_update_meta('term', $term_id, 'related_admin_area', (string) $parent_meta['related_admin_area'], false, $result, $context);
		}

		if ( 'metro' === $node['type'] ) {
			fballer_geo_import_update_meta('term', $term_id, 'related_district', (string) $parent_meta['related_district'], false, $result, $context);
		}

		fballer_geo_import_update_meta('term', $term_id, '_fballer_geo_city_root', '', false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'geo_lat', $node['geo_lat'], false, $result, $context);
		fballer_geo_import_update_meta('term', $term_id, 'geo_lng', $node['geo_lng'], false, $result, $context);
	} elseif ( $dry_run ) {
		$result = fballer_geo_import_log($result, 'updated', $context . ' meta will be synced');
	}

	return array(
		'term_id' => $term_id,
		'result' => $result,
	);
}

function fballer_geo_import_sort_nodes( $nodes ) {
	$order = array(
		'city_direction' => 10,
		'admin_area' => 20,
		'district' => 30,
		'metro' => 40,
	);

	usort($nodes, static function( $left, $right ) use ( $order ) {
		$left_order = $order[ $left['type'] ] ?? 999;
		$right_order = $order[ $right['type'] ] ?? 999;

		if ( $left_order !== $right_order ) {
			return $left_order <=> $right_order;
		}

		if ( $left['parent_id'] === '' && $right['parent_id'] !== '' ) {
			return -1;
		}

		if ( $left['parent_id'] !== '' && $right['parent_id'] === '' ) {
			return 1;
		}

		return strcmp($left['id'], $right['id']);
	});

	return $nodes;
}

function fballer_import_geo_json( $input_path, $args = array() ) {
	$args = wp_parse_args($args, array(
		'dry_run' => false,
		'city' => '',
	));

	$result = array(
		'counts' => array(
			'created' => 0,
			'updated' => 0,
			'skipped' => 0,
			'warnings' => 0,
		),
		'logs' => array(
			'created' => array(),
			'updated' => array(),
			'skipped' => array(),
			'warnings' => array(),
		),
	);

	$files = fballer_geo_import_collect_input_files($input_path);
	if ( is_wp_error($files) ) {
		return $files;
	}

	$city_filter = trim((string) $args['city']);

	foreach ( $files as $file_path ) {
		$decoded = fballer_geo_import_load_json_file($file_path);
		if ( is_wp_error($decoded) ) {
			return $decoded;
		}

		$cities = fballer_geo_import_extract_city_payloads($decoded, $file_path);
		if ( is_wp_error($cities) ) {
			return $cities;
		}

		foreach ( $cities as $city ) {
			if ( $city_filter !== '' && ! in_array($city_filter, array($city['id'], $city['slug']), true) ) {
				continue;
			}

			$valid_city = fballer_geo_import_validate_city($city);
			if ( is_wp_error($valid_city) ) {
				return $valid_city;
			}

			$city_result = fballer_geo_import_upsert_city($city, ! empty($args['dry_run']), $result);
			if ( is_wp_error($city_result) ) {
				return $city_result;
			}

			$result = $city_result['result'];
			$city_term_id = $city_result['term_id'];
			$term_ids_by_external_id = array(
				$city['id'] => array(
					'type' => 'city',
					'term_id' => $city_term_id,
					'related_direction' => 0,
					'related_admin_area' => 0,
					'related_district' => 0,
				),
			);

			$nodes = array();
			$node_ids = array();
			foreach ( $city['nodes'] as $raw_node ) {
				$node = fballer_geo_import_normalize_node_payload($raw_node);
				$valid_node = fballer_geo_import_validate_node($node, $city);
				if ( is_wp_error($valid_node) ) {
					return $valid_node;
				}

				if ( isset($node_ids[ $node['id'] ]) ) {
					return new WP_Error('geo_import_duplicate_node_id', 'Duplicate node id in city ' . $city['id'] . ': ' . $node['id']);
				}

				$node_ids[ $node['id'] ] = true;
				$nodes[] = $node;
			}

			$nodes = fballer_geo_import_sort_nodes($nodes);

			foreach ( $nodes as $node ) {
				$parent_meta = fballer_geo_import_resolve_parent_meta($node, $term_ids_by_external_id, $city_term_id);
				if ( is_wp_error($parent_meta) ) {
					return $parent_meta;
				}

				$node_result = fballer_geo_import_upsert_node($node, $city_term_id, $parent_meta, ! empty($args['dry_run']), $result);
				if ( is_wp_error($node_result) ) {
					return $node_result;
				}

				$result = $node_result['result'];
				$term_ids_by_external_id[ $node['id'] ] = array_merge($parent_meta, array(
					'type' => $node['type'],
					'term_id' => $node_result['term_id'],
				));
			}
		}
	}

	if ( $city_filter !== '' && 0 === array_sum($result['counts']) ) {
		return new WP_Error('geo_import_city_not_found', 'City filter did not match any city in provided JSON: ' . $city_filter);
	}

	return $result;
}

function fballer_geo_legacy_normalize_string( $value ) {
	$value = is_scalar($value) ? (string) $value : '';
	$value = remove_accents($value);
	$value = mb_strtolower($value, 'UTF-8');
	$value = str_replace('ё', 'е', $value);
	$value = str_replace('й', 'и', $value);
	$value = preg_replace('/[^a-zа-я0-9]+/u', ' ', $value);
	return trim((string) $value);
}

function fballer_geo_legacy_default_mapping_file() {
	return get_template_directory() . '/data/geo/legacy-remap.php';
}

function fballer_geo_legacy_load_mapping( $file_path = '' ) {
	$file_path = $file_path !== '' ? $file_path : fballer_geo_legacy_default_mapping_file();
	if ( ! is_readable($file_path) ) {
		return array(
			'terms' => array(),
		);
	}

	$mapping = include $file_path;
	if ( ! is_array($mapping) ) {
		return new WP_Error('geo_legacy_mapping_invalid', 'Legacy geo mapping file must return an array: ' . $file_path);
	}

	if ( ! isset($mapping['terms']) || ! is_array($mapping['terms']) ) {
		$mapping['terms'] = array();
	}

	return $mapping;
}

function fballer_geo_legacy_parse_insert_rows( $values_sql ) {
	$values_sql = trim((string) $values_sql);
	$values_sql = rtrim($values_sql, ';');
	$rows = array();
	$current = '';
	$depth = 0;
	$in_string = false;
	$previous = '';
	$length = strlen($values_sql);

	for ( $index = 0; $index < $length; $index++ ) {
		$char = $values_sql[ $index ];
		if ( "'" === $char && '\\' !== $previous ) {
			$in_string = ! $in_string;
		}

		if ( ! $in_string ) {
			if ( '(' === $char ) {
				$depth++;
			} elseif ( ')' === $char ) {
				$depth--;
			}
		}

		$current .= $char;
		if ( ! $in_string && 0 === $depth && ')' === $char ) {
			$rows[] = trim($current, ", \r\n\t");
			$current = '';
		}

		$previous = $char;
	}

	return $rows;
}

function fballer_geo_legacy_parse_sql_row( $row_sql ) {
	$row_sql = trim((string) $row_sql);
	$row_sql = preg_replace('/^\(/', '', $row_sql);
	$row_sql = preg_replace('/\)$/', '', $row_sql);

	$values = array();
	$current = '';
	$in_string = false;
	$previous = '';
	$length = strlen($row_sql);

	for ( $index = 0; $index < $length; $index++ ) {
		$char = $row_sql[ $index ];
		if ( "'" === $char && '\\' !== $previous ) {
			$in_string = ! $in_string;
			$current .= $char;
			$previous = $char;
			continue;
		}

		if ( ',' === $char && ! $in_string ) {
			$values[] = $current;
			$current = '';
			$previous = $char;
			continue;
		}

		$current .= $char;
		$previous = $char;
	}

	$values[] = $current;

	return array_map(
		static function( $value ) {
			$value = trim((string) $value);
			if ( 'NULL' === $value ) {
				return null;
			}

			if ( preg_match("/^'(.*)'$/s", $value, $matches) ) {
				return str_replace(array("\\\\", "\\'"), array("\\", "'"), $matches[1]);
			}

			if ( preg_match('/^-?\d+$/', $value) ) {
				return (int) $value;
			}

			return $value;
		},
		$values
	);
}

function fballer_geo_legacy_load_dump_rows( $dump_path, $tables ) {
	if ( ! is_readable($dump_path) ) {
		return new WP_Error('geo_legacy_dump_missing', 'Legacy SQL dump is not readable: ' . $dump_path);
	}

	$tables = array_values(array_unique(array_map('strval', (array) $tables)));
	$rows_by_table = array_fill_keys($tables, array());
	$handle = fopen($dump_path, 'r');
	if ( false === $handle ) {
		return new WP_Error('geo_legacy_dump_open_failed', 'Unable to open legacy SQL dump: ' . $dump_path);
	}

	$active_table = '';
	$buffer = '';

	while ( false !== ($line = fgets($handle)) ) {
		$trimmed = trim($line);
		if ( '' === $active_table ) {
			if ( ! preg_match('/^INSERT INTO `([^`]+)` VALUES\s*(.*)$/', $trimmed, $matches) ) {
				continue;
			}

			if ( ! in_array($matches[1], $tables, true) ) {
				continue;
			}

			$active_table = $matches[1];
			$buffer = $matches[2];
		} else {
			$buffer .= $trimmed;
		}

		if ( substr($trimmed, -1) !== ';' ) {
			continue;
		}

		$rows = fballer_geo_legacy_parse_insert_rows($buffer);
		foreach ( $rows as $row_sql ) {
			$rows_by_table[ $active_table ][] = fballer_geo_legacy_parse_sql_row($row_sql);
		}

		$active_table = '';
		$buffer = '';
	}

	fclose($handle);
	return $rows_by_table;
}

function fballer_geo_legacy_build_dump_context( $dump_path ) {
	$rows = fballer_geo_legacy_load_dump_rows($dump_path, array(
		'wp735_terms',
		'wp735_term_taxonomy',
		'wp735_term_relationships',
		'wp735_posts',
	));

	if ( is_wp_error($rows) ) {
		return $rows;
	}

	$terms = array();
	foreach ( $rows['wp735_terms'] as $row ) {
		$terms[ (int) $row[0] ] = array(
			'term_id' => (int) $row[0],
			'name' => (string) $row[1],
			'slug' => (string) $row[2],
		);
	}

	$taxonomies = array();
	foreach ( $rows['wp735_term_taxonomy'] as $row ) {
		$term_id = (int) $row[1];
		$taxonomy = (string) $row[2];
		if ( ! in_array($taxonomy, array('city', 'city_direction', 'admin_area', 'district', 'metro'), true) ) {
			continue;
		}

		$taxonomies[ (int) $row[0] ] = array(
			'term_taxonomy_id' => (int) $row[0],
			'term_id' => $term_id,
			'taxonomy' => $taxonomy,
			'parent' => (int) $row[4],
			'count' => (int) $row[5],
			'name' => isset($terms[ $term_id ]) ? $terms[ $term_id ]['name'] : '',
			'slug' => isset($terms[ $term_id ]) ? $terms[ $term_id ]['slug'] : '',
		);
	}

	$posts = array();
	foreach ( $rows['wp735_posts'] as $row ) {
		$posts[ (int) $row[0] ] = array(
			'ID' => (int) $row[0],
			'post_title' => (string) $row[5],
			'post_status' => (string) $row[7],
			'post_type' => (string) $row[20],
		);
	}

	$relationships = array();
	foreach ( $rows['wp735_term_relationships'] as $row ) {
		$post_id = (int) $row[0];
		$term_taxonomy_id = (int) $row[1];
		if ( ! isset($posts[ $post_id ], $taxonomies[ $term_taxonomy_id ]) ) {
			continue;
		}

		$relationships[ $post_id ][] = $taxonomies[ $term_taxonomy_id ];
	}

	return array(
		'terms' => $terms,
		'taxonomies' => $taxonomies,
		'posts' => $posts,
		'relationships' => $relationships,
	);
}

function fballer_geo_legacy_get_current_term_index() {
	$index = array(
		'by_external_id' => array(),
		'by_slug' => array(),
		'by_slug_city' => array(),
		'by_name' => array(),
		'by_name_city' => array(),
	);

	foreach ( array('city', 'city_direction', 'admin_area', 'district', 'metro') as $taxonomy ) {
		$terms = get_terms(array(
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
		));

		if ( ! is_array($terms) ) {
			continue;
		}

		foreach ( $terms as $term ) {
			$is_root = 'yes' === get_term_meta($term->term_id, '_fballer_geo_city_root', true);
			if ( $is_root ) {
				continue;
			}

			$term_data = array(
				'term_id' => (int) $term->term_id,
				'taxonomy' => $taxonomy,
				'name' => (string) $term->name,
				'slug' => (string) $term->slug,
				'external_id' => (string) get_term_meta($term->term_id, FBALLER_GEO_EXTERNAL_ID_META, true),
				'related_city' => (int) get_term_meta($term->term_id, 'related_city', true),
				'related_direction' => (int) get_term_meta($term->term_id, 'related_direction', true),
				'related_admin_area' => (int) get_term_meta($term->term_id, 'related_admin_area', true),
				'related_district' => (int) get_term_meta($term->term_id, 'related_district', true),
			);

			if ( 'city' === $taxonomy && 0 === $term_data['related_city'] ) {
				$term_data['related_city'] = $term_data['term_id'];
			}

			if ( $term_data['external_id'] !== '' ) {
				$index['by_external_id'][ $taxonomy ][ $term_data['external_id'] ] = $term_data;
			}

			$index['by_slug'][ $taxonomy ][ $term_data['slug'] ][] = $term_data;
			$index['by_name'][ $taxonomy ][ fballer_geo_legacy_normalize_string($term_data['name']) ][] = $term_data;

			$city_key = $term_data['related_city'] > 0 ? (string) $term_data['related_city'] : '0';
			$index['by_slug_city'][ $taxonomy ][ $term_data['slug'] . '|' . $city_key ][] = $term_data;
			$index['by_name_city'][ $taxonomy ][ fballer_geo_legacy_normalize_string($term_data['name']) . '|' . $city_key ][] = $term_data;
		}
	}

	return $index;
}

function fballer_geo_legacy_find_current_city_term( $legacy_term, $current_index ) {
	$slug = isset($legacy_term['slug']) ? (string) $legacy_term['slug'] : '';
	$name_key = fballer_geo_legacy_normalize_string($legacy_term['name'] ?? '');

	if ( $slug !== '' && ! empty($current_index['by_slug']['city'][ $slug ]) ) {
		return $current_index['by_slug']['city'][ $slug ][0];
	}

	if ( $name_key !== '' && ! empty($current_index['by_name']['city'][ $name_key ]) ) {
		return $current_index['by_name']['city'][ $name_key ][0];
	}

	return null;
}

function fballer_geo_legacy_find_current_geo_term( $taxonomy, $legacy_term, $current_index, $city_term_id = 0 ) {
	$slug = isset($legacy_term['slug']) ? (string) $legacy_term['slug'] : '';
	$name_key = fballer_geo_legacy_normalize_string($legacy_term['name'] ?? '');
	$city_key = (string) (int) $city_term_id;

	if ( $city_term_id > 0 && $slug !== '' && ! empty($current_index['by_slug_city'][ $taxonomy ][ $slug . '|' . $city_key ]) ) {
		return $current_index['by_slug_city'][ $taxonomy ][ $slug . '|' . $city_key ][0];
	}

	if ( $city_term_id > 0 && $name_key !== '' && ! empty($current_index['by_name_city'][ $taxonomy ][ $name_key . '|' . $city_key ]) ) {
		return $current_index['by_name_city'][ $taxonomy ][ $name_key . '|' . $city_key ][0];
	}

	if ( $slug !== '' && ! empty($current_index['by_slug'][ $taxonomy ][ $slug ]) && 1 === count($current_index['by_slug'][ $taxonomy ][ $slug ]) ) {
		return $current_index['by_slug'][ $taxonomy ][ $slug ][0];
	}

	if ( $name_key !== '' && ! empty($current_index['by_name'][ $taxonomy ][ $name_key ]) && 1 === count($current_index['by_name'][ $taxonomy ][ $name_key ]) ) {
		return $current_index['by_name'][ $taxonomy ][ $name_key ][0];
	}

	return null;
}

function fballer_geo_legacy_resolve_manual_targets( $legacy_term, $mapping, $current_index ) {
	$taxonomy = (string) $legacy_term['taxonomy'];
	$slug = (string) $legacy_term['slug'];
	if ( empty($mapping['terms'][ $taxonomy ][ $slug ]) || ! is_array($mapping['terms'][ $taxonomy ][ $slug ]) ) {
		return array();
	}

	$resolved = array();
	foreach ( $mapping['terms'][ $taxonomy ][ $slug ] as $spec ) {
		if ( ! is_array($spec) || empty($spec['taxonomy']) ) {
			continue;
		}

		$target_taxonomy = (string) $spec['taxonomy'];
		$term = null;
		if ( ! empty($spec['external_id']) && ! empty($current_index['by_external_id'][ $target_taxonomy ][ $spec['external_id'] ]) ) {
			$term = $current_index['by_external_id'][ $target_taxonomy ][ $spec['external_id'] ];
		} elseif ( ! empty($spec['slug']) && ! empty($current_index['by_slug'][ $target_taxonomy ][ $spec['slug'] ]) ) {
			$term = $current_index['by_slug'][ $target_taxonomy ][ $spec['slug'] ][0];
		}

		if ( $term ) {
			$resolved[] = $term;
		}
	}

	return $resolved;
}

function fballer_geo_legacy_map_term( $legacy_term, $legacy_terms_by_id, $current_index, $mapping, $current_city_term_id = 0 ) {
	$resolved = fballer_geo_legacy_resolve_manual_targets($legacy_term, $mapping, $current_index);
	if ( ! empty($resolved) ) {
		return $resolved;
	}

	$taxonomy = (string) $legacy_term['taxonomy'];
	if ( 'city' === $taxonomy ) {
		$city_term = null;
		if ( (int) $legacy_term['parent'] > 0 ) {
			$legacy_parent = isset($legacy_terms_by_id[ (int) $legacy_term['parent'] ]) ? $legacy_terms_by_id[ (int) $legacy_term['parent'] ] : null;
			if ( $legacy_parent ) {
				$city_term = fballer_geo_legacy_find_current_city_term($legacy_parent, $current_index);
			}

			$targets = array();
			if ( $city_term ) {
				$targets[] = $city_term;
				foreach ( array('city_direction', 'admin_area', 'district', 'metro') as $geo_taxonomy ) {
					$matched = fballer_geo_legacy_find_current_geo_term($geo_taxonomy, $legacy_term, $current_index, (int) $city_term['term_id']);
					if ( $matched ) {
						$targets[] = $matched;
						break;
					}
				}
			}

			return $targets;
		}

		$city_term = fballer_geo_legacy_find_current_city_term($legacy_term, $current_index);
		return $city_term ? array($city_term) : array();
	}

	$matched = fballer_geo_legacy_find_current_geo_term($taxonomy, $legacy_term, $current_index, $current_city_term_id);
	return $matched ? array($matched) : array();
}

function fballer_geo_legacy_add_term_target( &$targets_by_taxonomy, $term ) {
	if ( empty($term['taxonomy']) || empty($term['term_id']) ) {
		return;
	}

	$taxonomy = (string) $term['taxonomy'];
	$term_id = (int) $term['term_id'];
	if ( ! isset($targets_by_taxonomy[ $taxonomy ]) ) {
		$targets_by_taxonomy[ $taxonomy ] = array();
	}

	$targets_by_taxonomy[ $taxonomy ][ $term_id ] = $term_id;
}

function fballer_geo_legacy_expand_targets( &$targets_by_taxonomy ) {
	$initial = $targets_by_taxonomy;
	foreach ( $initial as $taxonomy => $term_ids ) {
		foreach ( $term_ids as $term_id ) {
			$term_id = (int) $term_id;
			if ( $term_id <= 0 ) {
				continue;
			}

			if ( 'city' !== $taxonomy ) {
				$related_city = (int) get_term_meta($term_id, 'related_city', true);
				if ( $related_city > 0 ) {
					fballer_geo_legacy_add_term_target($targets_by_taxonomy, array(
						'taxonomy' => 'city',
						'term_id' => $related_city,
					));
				}
			}

			if ( in_array($taxonomy, array('admin_area', 'district', 'metro'), true) ) {
				$related_direction = (int) get_term_meta($term_id, 'related_direction', true);
				if ( $related_direction > 0 ) {
					fballer_geo_legacy_add_term_target($targets_by_taxonomy, array(
						'taxonomy' => 'city_direction',
						'term_id' => $related_direction,
					));
				}
			}

			if ( in_array($taxonomy, array('district', 'metro'), true) ) {
				$related_admin_area = (int) get_term_meta($term_id, 'related_admin_area', true);
				if ( $related_admin_area > 0 ) {
					fballer_geo_legacy_add_term_target($targets_by_taxonomy, array(
						'taxonomy' => 'admin_area',
						'term_id' => $related_admin_area,
					));
				}
			}

			if ( 'metro' === $taxonomy ) {
				$related_district = (int) get_term_meta($term_id, 'related_district', true);
				if ( $related_district > 0 ) {
					fballer_geo_legacy_add_term_target($targets_by_taxonomy, array(
						'taxonomy' => 'district',
						'term_id' => $related_district,
					));
				}
			}
		}
	}
}

function fballer_geo_legacy_update_post_terms( $post_id, $targets_by_taxonomy, $dry_run, &$result, $context ) {
	foreach ( array('city', 'city_direction', 'admin_area', 'district', 'metro') as $taxonomy ) {
		$desired = isset($targets_by_taxonomy[ $taxonomy ]) ? array_values(array_map('intval', $targets_by_taxonomy[ $taxonomy ])) : array();
		sort($desired, SORT_NUMERIC);
		$current = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'ids'));
		$current = is_array($current) ? array_values(array_map('intval', $current)) : array();
		sort($current, SORT_NUMERIC);

		if ( $current === $desired ) {
			continue;
		}

		if ( $dry_run ) {
			$result = fballer_geo_import_log($result, 'updated', sprintf('%s taxonomy %s will be remapped to [%s]', $context, $taxonomy, implode(',', $desired)));
			continue;
		}

		$set_result = wp_set_object_terms($post_id, $desired, $taxonomy, false);
		if ( is_wp_error($set_result) ) {
			$result = fballer_geo_import_log($result, 'warnings', sprintf('%s failed to sync taxonomy %s: %s', $context, $taxonomy, $set_result->get_error_message()));
			continue;
		}

		$result = fballer_geo_import_log($result, 'updated', sprintf('%s taxonomy %s remapped', $context, $taxonomy));
	}
}

function fballer_remap_legacy_geo_posts( $dump_path, $args = array() ) {
	$args = wp_parse_args($args, array(
		'dry_run' => false,
		'mapping' => '',
		'post_types' => array('games', 'places', 'champs', 'teams', 'players'),
	));

	$result = array(
		'counts' => array(
			'created' => 0,
			'updated' => 0,
			'skipped' => 0,
			'warnings' => 0,
		),
		'logs' => array(
			'created' => array(),
			'updated' => array(),
			'skipped' => array(),
			'warnings' => array(),
		),
		'stats' => array(
			'posts_scanned' => 0,
			'posts_updated' => 0,
			'posts_missing' => 0,
			'legacy_terms_unmatched' => 0,
		),
	);

	$mapping = fballer_geo_legacy_load_mapping((string) $args['mapping']);
	if ( is_wp_error($mapping) ) {
		return $mapping;
	}

	$dump = fballer_geo_legacy_build_dump_context($dump_path);
	if ( is_wp_error($dump) ) {
		return $dump;
	}

	$current_index = fballer_geo_legacy_get_current_term_index();
	$allowed_post_types = array_values(array_unique(array_map('strval', (array) $args['post_types'])));

	foreach ( $dump['relationships'] as $post_id => $legacy_terms ) {
		$legacy_post = isset($dump['posts'][ $post_id ]) ? $dump['posts'][ $post_id ] : null;
		if ( ! $legacy_post || ! in_array($legacy_post['post_type'], $allowed_post_types, true) ) {
			continue;
		}

		$result['stats']['posts_scanned']++;
		$post = get_post($post_id);
		$context = sprintf('post[%d:%s]', $post_id, $legacy_post['post_type']);

		if ( ! $post || $post->post_type !== $legacy_post['post_type'] ) {
			$result['stats']['posts_missing']++;
			$result = fballer_geo_import_log($result, 'warnings', $context . ' missing on current site');
			continue;
		}

		$targets_by_taxonomy = array();
		$current_city_term_id = 0;

		foreach ( $legacy_terms as $legacy_term ) {
			if ( 'city' !== $legacy_term['taxonomy'] ) {
				continue;
			}

			$mapped = fballer_geo_legacy_map_term($legacy_term, $dump['terms'], $current_index, $mapping, 0);
			foreach ( $mapped as $term ) {
				fballer_geo_legacy_add_term_target($targets_by_taxonomy, $term);
				if ( 'city' === $term['taxonomy'] && $current_city_term_id <= 0 ) {
					$current_city_term_id = (int) $term['term_id'];
				}
			}

			if ( empty($mapped) ) {
				$result['stats']['legacy_terms_unmatched']++;
				$result = fballer_geo_import_log($result, 'warnings', sprintf('%s legacy city term unmatched: %s [%s]', $context, $legacy_term['name'], $legacy_term['slug']));
			}
		}

		foreach ( $legacy_terms as $legacy_term ) {
			if ( 'city' === $legacy_term['taxonomy'] ) {
				continue;
			}

			$mapped = fballer_geo_legacy_map_term($legacy_term, $dump['terms'], $current_index, $mapping, $current_city_term_id);
			foreach ( $mapped as $term ) {
				fballer_geo_legacy_add_term_target($targets_by_taxonomy, $term);
			}

			if ( empty($mapped) ) {
				$result['stats']['legacy_terms_unmatched']++;
				$result = fballer_geo_import_log($result, 'warnings', sprintf('%s legacy %s term unmatched: %s [%s]', $context, $legacy_term['taxonomy'], $legacy_term['name'], $legacy_term['slug']));
			}
		}

		fballer_geo_legacy_expand_targets($targets_by_taxonomy);

		$before_updates = $result['counts']['updated'];
		fballer_geo_legacy_update_post_terms($post_id, $targets_by_taxonomy, ! empty($args['dry_run']), $result, $context);
		if ( $result['counts']['updated'] > $before_updates ) {
			$result['stats']['posts_updated']++;
		} else {
			$result = fballer_geo_import_log($result, 'skipped', $context . ' already up to date');
		}
	}

	return $result;
}

function fballer_geo_reset_terms( $args = array() ) {
	global $wpdb;

	$args = wp_parse_args($args, array(
		'dry_run' => false,
	));

	$taxonomies = fballer_geo_import_supported_taxonomies();
	$taxonomy_placeholders = implode(', ', array_fill(0, count($taxonomies), '%s'));

	$result = array(
		'counts' => array(
			'created' => 0,
			'updated' => 0,
			'skipped' => 0,
			'warnings' => 0,
		),
		'logs' => array(
			'created' => array(),
			'updated' => array(),
			'skipped' => array(),
			'warnings' => array(),
		),
		'stats' => array(
			'geo_terms' => 0,
			'geo_term_taxonomy_rows' => 0,
			'geo_relationships' => 0,
			'geo_termmeta' => 0,
			'deleted_terms' => 0,
			'deleted_term_taxonomy_rows' => 0,
			'deleted_relationships' => 0,
			'deleted_termmeta' => 0,
		),
	);

	$term_ids = $wpdb->get_col($wpdb->prepare(
		"SELECT DISTINCT term_id FROM {$wpdb->term_taxonomy} WHERE taxonomy IN ($taxonomy_placeholders)",
		$taxonomies
	));

	$term_ids = array_values(array_filter(array_map('intval', (array) $term_ids)));
	$result['stats']['geo_terms'] = count($term_ids);

	$geo_term_taxonomy_rows = (int) $wpdb->get_var($wpdb->prepare(
		"SELECT COUNT(*) FROM {$wpdb->term_taxonomy} WHERE taxonomy IN ($taxonomy_placeholders)",
		$taxonomies
	));
	$result['stats']['geo_term_taxonomy_rows'] = $geo_term_taxonomy_rows;

	if ( empty($term_ids) ) {
		return fballer_geo_import_log($result, 'skipped', 'No geo terms found for reset');
	}

	$term_placeholders = implode(', ', array_fill(0, count($term_ids), '%d'));
	$geo_relationships = (int) $wpdb->get_var($wpdb->prepare(
		"SELECT COUNT(*)
		FROM {$wpdb->term_relationships} tr
		INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
		WHERE tt.taxonomy IN ($taxonomy_placeholders)",
		$taxonomies
	));
	$result['stats']['geo_relationships'] = $geo_relationships;

	$geo_termmeta = (int) $wpdb->get_var($wpdb->prepare(
		"SELECT COUNT(*) FROM {$wpdb->termmeta} WHERE term_id IN ($term_placeholders)",
		$term_ids
	));
	$result['stats']['geo_termmeta'] = $geo_termmeta;

	$result = fballer_geo_import_log($result, 'updated', sprintf(
		'%s reset will remove %d geo terms, %d taxonomy rows, %d relationships, %d termmeta rows',
		$args['dry_run'] ? 'dry-run' : 'geo',
		$result['stats']['geo_terms'],
		$result['stats']['geo_term_taxonomy_rows'],
		$result['stats']['geo_relationships'],
		$result['stats']['geo_termmeta']
	));

	if ( $args['dry_run'] ) {
		return $result;
	}

	$deleted_relationships = (int) $wpdb->query($wpdb->prepare(
		"DELETE tr
		FROM {$wpdb->term_relationships} tr
		INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
		WHERE tt.taxonomy IN ($taxonomy_placeholders)",
		$taxonomies
	));

	$deleted_termmeta = (int) $wpdb->query($wpdb->prepare(
		"DELETE FROM {$wpdb->termmeta} WHERE term_id IN ($term_placeholders)",
		$term_ids
	));

	$deleted_term_taxonomy_rows = (int) $wpdb->query($wpdb->prepare(
		"DELETE FROM {$wpdb->term_taxonomy} WHERE taxonomy IN ($taxonomy_placeholders)",
		$taxonomies
	));

	$deleted_terms = (int) $wpdb->query($wpdb->prepare(
		"DELETE t
		FROM {$wpdb->terms} t
		LEFT JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id
		WHERE t.term_id IN ($term_placeholders)
		  AND tt.term_id IS NULL",
		$term_ids
	));

	$result['stats']['deleted_relationships'] = max(0, $deleted_relationships);
	$result['stats']['deleted_termmeta'] = max(0, $deleted_termmeta);
	$result['stats']['deleted_term_taxonomy_rows'] = max(0, $deleted_term_taxonomy_rows);
	$result['stats']['deleted_terms'] = max(0, $deleted_terms);

	clean_term_cache($term_ids, '', false);
	wp_cache_flush();

	$result = fballer_geo_import_log($result, 'updated', sprintf(
		'geo reset removed %d geo terms, %d taxonomy rows, %d relationships, %d termmeta rows',
		$result['stats']['deleted_terms'],
		$result['stats']['deleted_term_taxonomy_rows'],
		$result['stats']['deleted_relationships'],
		$result['stats']['deleted_termmeta']
	));

	return $result;
}

if ( defined('WP_CLI') && WP_CLI ) {
	WP_CLI::add_command('fballer import-geo', function( $args, $assoc_args ) {
		$file = isset($assoc_args['file']) ? (string) $assoc_args['file'] : '';
		$dry_run = isset($assoc_args['dry-run']);
		$city_filter = isset($assoc_args['city']) ? (string) $assoc_args['city'] : '';

		$result = fballer_import_geo_json($file, array(
			'dry_run' => $dry_run,
			'city' => $city_filter,
		));

		if ( is_wp_error($result) ) {
			WP_CLI::error($result->get_error_message());
		}

		foreach ( array('created', 'updated', 'skipped', 'warnings') as $level ) {
			foreach ( $result['logs'][ $level ] as $message ) {
				switch ( $level ) {
					case 'created':
						WP_CLI::log('[created] ' . $message);
						break;
					case 'updated':
						WP_CLI::log('[updated] ' . $message);
						break;
					case 'skipped':
						WP_CLI::log('[skipped] ' . $message);
						break;
					default:
						WP_CLI::warning($message);
						break;
				}
			}
		}

		WP_CLI::success(sprintf(
			'Geo import finished. created=%d updated=%d skipped=%d warnings=%d dry_run=%s',
			$result['counts']['created'],
			$result['counts']['updated'],
			$result['counts']['skipped'],
			$result['counts']['warnings'],
			$dry_run ? 'yes' : 'no'
		));
	});

	WP_CLI::add_command('fballer remap-legacy-geo', function( $args, $assoc_args ) {
		$dump = isset($assoc_args['dump']) ? (string) $assoc_args['dump'] : '';
		if ( '' === $dump ) {
			WP_CLI::error('Provide --dump=/path/to/legacy.sql');
		}

		$mapping = isset($assoc_args['mapping']) ? (string) $assoc_args['mapping'] : '';
		$dry_run = isset($assoc_args['dry-run']);
		$post_types = isset($assoc_args['post-types']) ? array_filter(array_map('trim', explode(',', (string) $assoc_args['post-types']))) : array('games', 'places', 'champs', 'teams', 'players');

		$result = fballer_remap_legacy_geo_posts($dump, array(
			'dry_run' => $dry_run,
			'mapping' => $mapping,
			'post_types' => $post_types,
		));

		if ( is_wp_error($result) ) {
			WP_CLI::error($result->get_error_message());
		}

		foreach ( array('created', 'updated', 'skipped', 'warnings') as $level ) {
			foreach ( $result['logs'][ $level ] as $message ) {
				if ( 'warnings' === $level ) {
					WP_CLI::warning($message);
				} else {
					WP_CLI::log('[' . $level . '] ' . $message);
				}
			}
		}

		WP_CLI::success(sprintf(
			'Legacy geo remap finished. posts_scanned=%d posts_updated=%d posts_missing=%d legacy_terms_unmatched=%d updated=%d skipped=%d warnings=%d dry_run=%s',
			$result['stats']['posts_scanned'],
			$result['stats']['posts_updated'],
			$result['stats']['posts_missing'],
			$result['stats']['legacy_terms_unmatched'],
			$result['counts']['updated'],
			$result['counts']['skipped'],
			$result['counts']['warnings'],
			$dry_run ? 'yes' : 'no'
		));
	});

	WP_CLI::add_command('fballer reset-geo', function( $args, $assoc_args ) {
		$dry_run = isset($assoc_args['dry-run']);
		$confirmed = isset($assoc_args['yes']);

		if ( ! $dry_run && ! $confirmed ) {
			WP_CLI::error('Geo reset is destructive. Run with --dry-run first, then repeat with --yes');
		}

		$result = fballer_geo_reset_terms(array(
			'dry_run' => $dry_run,
		));

		if ( is_wp_error($result) ) {
			WP_CLI::error($result->get_error_message());
		}

		foreach ( array('created', 'updated', 'skipped', 'warnings') as $level ) {
			foreach ( $result['logs'][ $level ] as $message ) {
				if ( 'warnings' === $level ) {
					WP_CLI::warning($message);
				} else {
					WP_CLI::log('[' . $level . '] ' . $message);
				}
			}
		}

		WP_CLI::success(sprintf(
			'Geo reset finished. geo_terms=%d deleted_terms=%d deleted_taxonomy_rows=%d deleted_relationships=%d deleted_termmeta=%d warnings=%d dry_run=%s',
			$result['stats']['geo_terms'],
			$result['stats']['deleted_terms'],
			$result['stats']['deleted_term_taxonomy_rows'],
			$result['stats']['deleted_relationships'],
			$result['stats']['deleted_termmeta'],
			$result['counts']['warnings'],
			$dry_run ? 'yes' : 'no'
		));
	});
}
