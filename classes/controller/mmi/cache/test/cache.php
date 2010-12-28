<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Cache test controller.
 *
 * @package		MMI Cache
 * @author		Me Make It
 * @copyright	(c) 2010 Me Make It
 * @license		http://www.memakeit.com/license
 */
class Controller_MMI_Cache_Test_Cache extends Controller_MMI_Cache_Test
{
	/**
	 * Test cache functionality.
	 *
	 * @access	public
	 * @return	void
	 * @uses	MMI_Debug::dump
	 */
	public function action_index()
	{
		$cache = MMI_Cache::instance();
		$id = 'company-name';

		$data = $cache->set($id, MMI_Cache::CACHE_TYPE_DATA, 'Me Make It');
		MMI_Debug::dump($data, '$cache->set');

		$data = $cache->get($id, MMI_Cache::CACHE_TYPE_DATA);
		MMI_Debug::dump($data, '$cache->get');

		$data = $cache->get_default_lifetime(MMI_Cache::CACHE_TYPE_DATA);
		MMI_Debug::dump($data, '$cache->get_default_lifetime(MMI_Cache::CACHE_TYPE_DATA)');

		$data = $cache->last_modified($id, MMI_Cache::CACHE_TYPE_DATA);
		MMI_Debug::dump($data, '$cache->last_modified');

		$data = $cache->delete($id, MMI_Cache::CACHE_TYPE_DATA);
		MMI_Debug::dump($data, '$cache->delete');

		$data = $cache->get($id, MMI_Cache::CACHE_TYPE_DATA);
		MMI_Debug::dump($data, '$cache->get');
	}
} // End Controller_MMI_Cache_Test_Cache
