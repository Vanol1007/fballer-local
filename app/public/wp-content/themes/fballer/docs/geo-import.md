# Geo Import

Канонический формат рассчитан на импорт городов в текущую geo-архитектуру темы:

- `city` остается отдельной таксономией.
- `city_direction`, `admin_area`, `district`, `metro` импортируются как обычные термы своих таксономий.
- Внутренние city-root термы (`city-<city-slug>`) создаются автоматически существующей логикой темы и используются как скрытые родители.
- Идемпотентность обеспечивается через term meta `_fballer_geo_external_id`.

## JSON schema

Каждый файл может содержать:

- один объект города с `type: "city"`
- или объект с массивом `cities`

Минимальный формат города:

```json
{
  "id": "ru-moscow",
  "slug": "moskva",
  "name": "Москва",
  "type": "city",
  "timezone": "Europe/Moscow",
  "flags": {
    "has_directions": false,
    "has_admin_areas": true,
    "has_districts": true,
    "has_metro": true
  },
  "coordinates": {
    "lat": 55.7558,
    "lng": 37.6176
  },
  "map_zoom": 10,
  "center": {
    "lat": 55.7558,
    "lng": 37.6176
  },
  "bounds": {
    "north": 56.0210,
    "south": 55.4899,
    "east": 37.9674,
    "west": 37.3193
  },
  "nodes": [
    {
      "id": "ru-moscow-cao",
      "slug": "moskva-cao",
      "name": "ЦАО",
      "type": "admin_area",
      "city_id": "ru-moscow",
      "parent_id": "",
      "coordinates": {
        "lat": 55.7539,
        "lng": 37.6208
      }
    }
  ]
}
```

## Mapping

- `city.id` -> `city` term meta `_fballer_geo_external_id`
- `city.slug` -> `city.slug`
- `city.name` -> `city.name`
- `city.timezone` -> `city_timezone`
- `city.flags.has_directions` -> `has_directions`
- `city.flags.has_admin_areas` -> `has_admin_areas`
- `city.flags.has_districts` -> `has_districts`
- `city.flags.has_metro` -> `has_metro`
- `city.coordinates.lat` -> `geo_lat`
- `city.coordinates.lng` -> `geo_lng`
- `city.map_zoom` -> `map_zoom`
- `city.center.lat` -> `map_center_lat`
- `city.center.lng` -> `map_center_lng`
- `city.bounds.north` -> `map_bounds_north`
- `city.bounds.south` -> `map_bounds_south`
- `city.bounds.east` -> `map_bounds_east`
- `city.bounds.west` -> `map_bounds_west`
- `node.id` -> term meta `_fballer_geo_external_id`
- `node.slug` -> term slug соответствующей таксономии
- `node.name` -> term name
- `node.type` -> taxonomy (`city_direction`, `admin_area`, `district`, `metro`)
- `node.city_id` -> lookup города, затем `related_city`
- `node.parent_id` -> источник для `related_direction` / `related_admin_area` / `related_district`
- `node.coordinates.lat` -> `geo_lat`
- `node.coordinates.lng` -> `geo_lng`

## Import

Локально:

```bash
wp fballer import-geo --file=wp-content/themes/fballer/data/geo --dry-run
wp fballer import-geo --file=wp-content/themes/fballer/data/geo
```

Только один город:

```bash
wp fballer import-geo --file=wp-content/themes/fballer/data/geo --city=ru-moscow --dry-run
wp fballer import-geo --file=wp-content/themes/fballer/data/geo --city=moskva
```

На проде:

```bash
wp fballer import-geo --file=/absolute/path/to/geo-json --dry-run
wp db export before-geo-import.sql
wp fballer import-geo --file=/absolute/path/to/geo-json
```

Что важно:

- dry-run показывает `created`, `updated`, `skipped`, но ничего не пишет в БД
- повторный импорт обновляет найденные термы, а не плодит дубли
- автоматического удаления старых термов нет
- если нарушены связи `parent_id` или `city_id`, команда завершится с явной ошибкой

## Geo reset

Для полной зачистки старых geo-термов есть отдельная команда:

```bash
wp fballer reset-geo --dry-run
wp fballer reset-geo --yes
```

Что делает команда:

- удаляет только geo-таксономии `city`, `city_direction`, `admin_area`, `district`, `metro`
- чистит связанные записи в `term_relationships` и `termmeta`
- не запускается в боевом режиме без явного `--yes`
- поддерживает `dry-run`

## Legacy remap

Если старые geo-термы на проде были удалены, а `games / places / champs / teams / players` нужно привязать к новой geo-структуре, используется отдельная команда:

```bash
wp fballer remap-legacy-geo --dump=/absolute/path/to/legacy.sql --dry-run
wp fballer remap-legacy-geo --dump=/absolute/path/to/legacy.sql
```

Опционально:

```bash
wp fballer remap-legacy-geo --dump=/absolute/path/to/legacy.sql --mapping=wp-content/themes/fballer/data/geo/legacy-remap.php --post-types=games,places,champs,teams,players --dry-run
```

Что делает команда:

- читает старые geo-связи постов из SQL-дампа
- маппит legacy `city / district / metro` на новые термы после `import-geo`
- восстанавливает связи постов с новыми `city / city_direction / admin_area / district / metro`
- поддерживает `dry-run`
- пишет warnings по legacy-термам, которые не удалось сопоставить автоматически

Файл ручных соответствий по умолчанию:

```text
wp-content/themes/fballer/data/geo/legacy-remap.php
```

Рекомендуемый порядок на проде:

```bash
wp db export before-geo-reset.sql
wp fballer reset-geo --dry-run
wp fballer reset-geo --yes
wp fballer import-geo --file=/absolute/path/to/geo-json
wp fballer remap-legacy-geo --dump=/absolute/path/to/legacy.sql --dry-run
wp fballer remap-legacy-geo --dump=/absolute/path/to/legacy.sql
```
