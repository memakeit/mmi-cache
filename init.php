<?php defined('SYSPATH') or die('No direct script access.');

// Test routes
if (Kohana::$environment !== Kohana::PRODUCTION)
{
	Route::set('mmi/cache/test', 'mmi/cache/test/<controller>(/<action>)')
	->defaults(array
	(
		'directory' => 'mmi/cache/test',
	));
}
