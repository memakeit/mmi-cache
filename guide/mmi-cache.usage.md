# MMI Cache

The mmi-cache module provides an additional level of organization over file-based caching.
Different cache types (data, fragments, pages, etc.) are stored in a corresponding
folder (located in the Kohana cache directory).

## Cache Types

The following cache types are supported.

* `css` CSS files
* `data` data
* `default` use the default Kohana cache logic
* `feed` Atom and RSS feeds
* `fragment` page fragments
* `js` JavaScript files
* `page` pages
* `system` system data
* `xml` XML files

## Usage

The `get`, `set`, and `delete` methods all accept a cache type parameter. If no cache type
is specified, the `default` cache type is used.

		$id = 'company-name';
		$cache_type = MMI_Cache::CACHE_TYPE_DATA;
		$cache = MMI_Cache::instance();
		$ok = $cache->set($id, $cache_type, 'Me Make It');
		$data = $cache->get($id, $cache_type);
		$num_deleted = $cache->delete($id, $cache_type);

All the items for a cache type can be deleted using the `delete_type` method.

		$num_deleted = MMI_Cache::instance()->delete_type(MMI_Cache::CACHE_TYPE_DATA);

The utility methods `get_default_lifetime` and `last_modified` are also available.

		$id = 'company-name';
		$cache_type = MMI_Cache::CACHE_TYPE_DATA;
		$lifetime = MMI_Cache::instance()->get_default_lifetime($cache_type);
		$last_mod = MMI_Cache::instance()->last_modified($id, $cache_type);

## Configuration

The cache configuration file is named `mmi-cache.php`. The default lifetimes for the
cache types are configured using the `lifetimes` setting.

