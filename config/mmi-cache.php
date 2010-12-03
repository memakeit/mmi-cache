<?php defined('SYSPATH') or die('No direct script access.');

// MMI cache configuration
return array
(
	'delete_empty_dir'	=> TRUE,
	'lifetimes' => array
	(
		MMI_Cache::CACHE_TYPE_DEFAULT	=> 8 * Date::HOUR,
		MMI_Cache::CACHE_TYPE_CSS		=> Date::YEAR,
		MMI_Cache::CACHE_TYPE_DATA		=> 15 * Date::MINUTE,
		MMI_Cache::CACHE_TYPE_FEED		=> 15 * Date::MINUTE,
		MMI_Cache::CACHE_TYPE_FRAGMENT	=> 4 * Date::HOUR,
		MMI_Cache::CACHE_TYPE_JS		=> Date::YEAR,
		MMI_Cache::CACHE_TYPE_PAGE		=> 15 * Date::MINUTE,
		MMI_Cache::CACHE_TYPE_SYSTEM	=> 1 * Date::HOUR,
		MMI_Cache::CACHE_TYPE_XML		=> 15 * Date::MINUTE,
	),
);
