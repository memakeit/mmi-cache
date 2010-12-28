<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Base cache test controller.
 *
 * @package		MMI Cache
 * @author		Me Make It
 * @copyright	(c) 2010 Me Make It
 * @license		http://www.memakeit.com/license
 */
abstract class Controller_MMI_Cache_Test extends Controller
{
	/**
	 * @var string the cache type
	 **/
	public $cache_type = NULL;

	/**
	 * @var boolean turn debugging on?
	 **/
	public $debug = TRUE;
} // End Controller_MMI_Cache_Test
