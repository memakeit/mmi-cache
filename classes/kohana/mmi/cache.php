<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Cache functions that support different cache types.
 * Each cache type is stored in a corresponding directory.
 *
 * @package		MMI Cache
 * @author		Me Make It
 * @copyright	(c) 2010 Me Make It
 * @license		http://www.memakeit.com/license
 */
class Kohana_MMI_Cache
{
	// Cache type constants
	const CACHE_TYPE_DEFAULT	= '';

	const CACHE_TYPE_CSS		= '_css';
	const CACHE_TYPE_DATA		= '_data';
	const CACHE_TYPE_FEED		= '_feed';
	const CACHE_TYPE_FRAGMENT	= '_fragment';
	const CACHE_TYPE_JS			= '_js';
	const CACHE_TYPE_NONE		= NULL;
	const CACHE_TYPE_PAGE		= '_page';
	const CACHE_TYPE_SYSTEM		= '_system';
	const CACHE_TYPE_XML		= '_xml';

	// Other class constants
	const DEFAULT_LIFETIME = 60;

	/**
	 * @var Kohana_Config cache settings
	 */
	protected static $_config;

	/**
	 * @var MMI_Cache the class instance
	 */
	protected static $_instance;

	/**
	 * Get a cache item.
	 *
	 * @access	public
	 * @param	string	the cache id
	 * @param	string	the cache type
	 * @param	integer	the cache lifetime
	 * @return	mixed
	 */
	public function get($id, $type = self::CACHE_TYPE_DEFAULT, $lifetime = NULL)
	{
		return $this->_cache($id, $type, NULL, $lifetime);
	}

	/**
	 * Set a cache item.
	 *
	 * @access	public
	 * @param	string	the cache id
	 * @param	string	the cache type
	 * @param	mixed	the cache value
	 * @param	integer	the cache lifetime
	 * @return	boolean
	 */
	public function set($id, $type = self::CACHE_TYPE_DEFAULT, $value = NULL, $lifetime = NULL)
	{
		return $this->_cache($id, $type, $value, $lifetime);
	}

	/**
	 * Delete a cache item.
	 *
	 * @access	public
	 * @param	string	the cache id
	 * @param	string	the cache type
	 * @return	integer
	 */
	public function delete($id, $type = self::CACHE_TYPE_DEFAULT)
	{
		$delete_empty_dir = self::get_config()->get('delete_empty_dir', FALSE);
		$file = $this->_get_file($id);
		$dir = $this->_get_directory($file, $type);
		$num_deleted = 0;

		if (is_file($dir.$file))
		{
			if (unlink($dir.$file))
			{
				$num_deleted++;
			}

			if ($delete_empty_dir)
			{
				$file_count = count(glob($dir.'*.txt'));
				if ($file_count === 0)
				{
					rmdir($dir);
				}
			}
		}
		return $num_deleted;
	}

	/**
	 * Delete the cache items for a cache type.
	 *
	 * @access	public
	 * @param	string	the cache type
	 * @return	integer
	 */
	public function delete_type($type)
	{
		$base_dir = $this->_get_directory('', $type);
		if (substr($base_dir, strlen($base_dir) - 1) !== DIRECTORY_SEPARATOR)
		{
			$base_dir .= DIRECTORY_SEPARATOR;
		}
		$num_deleted = 0;

		if (is_dir($base_dir))
		{
			$sub_dirs = glob($base_dir.'*', GLOB_ONLYDIR);
			if (count($sub_dirs) > 0)
			{
				foreach ($sub_dirs as $sub_dir)
				{
					if ((empty($type) AND substr($sub_dir, 0, 1) !== '_') OR ( ! empty($type)))
					{
						$files = glob($sub_dir.DIRECTORY_SEPARATOR.'*.txt');
						if (count($files) > 0)
						{
							foreach ($files as $file)
							{
								if (unlink($file))
								{
									$num_deleted++;
								}
							}
							rmdir($sub_dir);
						}
					}
				}
			}
		}
		return $num_deleted;
	}

	/**
	 * Get the last modified date for a cache item.
	 *
	 * @access	public
	 * @param	string	the cache id
	 * @param	string	the cache type
	 * @return	integer	(timestamp)
	 */
	public function last_modified($id, $type = self::CACHE_TYPE_DEFAULT)
	{
		$file = $this->_get_file($id);
		$dir = $this->_get_directory($file, $type);

		$last_modified = time();
		if (is_file($dir.$file))
		{
			$last_modified = filemtime($dir.$file);
		}
		return $last_modified;
	}

	/**
	 * Get the default cache lifetime.
	 *
	 * @access	public
	 * @param	string	the cache type
	 * @return	integer
	 */
	public function get_default_lifetime($type)
	{
		$lifetimes = self::get_config()->get('lifetimes', array());
		return Arr::get($lifetimes, $type, self::DEFAULT_LIFETIME);
	}

	/**
	 * Get or set a cache item.
	 *
	 * @access	protected
	 * @param	string	the cache id
	 * @param	string	the cache type
	 * @param	mixed	the cache value
	 * @param	integer	the cache lifetime
	 * @return	mixed
	 */
	protected function _cache($id, $type = self::CACHE_TYPE_DEFAULT, $value = NULL, $lifetime = NULL)
	{
		if ( ! is_int($lifetime))
		{
			$lifetime = $this->get_default_lifetime($type);
		}

		// Cache file name is a hash of the name
		$file = $this->_get_file($id);
		$dir = $this->_get_directory($file, $type);

		try
		{
			if ($value === NULL)
			{
				// Get cache
				if (is_file($dir.$file))
				{
					if ((time() - filemtime($dir.$file)) < $lifetime)
					{
						// Return the cache
						return unserialize(file_get_contents($dir.$file));
					}
					else
					{
						// Cache has expired
						if (unlink($dir.$file))
						{
							if (self::get_config()->get('delete_empty_dir', FALSE))
							{
								$file_count = count(glob($dir.'*.txt'));
								if ($file_count === 0)
								{
									rmdir($dir);
								}
							}
						}
					}
				}

				// Cache not found
				return NULL;
			}

			// Set cache
			if ( ! is_dir($dir))
			{
				// Create the cache directory
				mkdir($dir, 0777, TRUE);

				// Set permissions (must be manually set to fix umask issues)
				chmod($dir, 0777);
			}
			return (bool) file_put_contents($dir.$file, serialize($value), LOCK_EX);
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * Get the filename for a cache item.
	 *
	 * @access	protected
	 * @param	string	the cache id
	 * @return	string
	 */
	protected function _get_file($id)
	{
		return sha1($id).'.txt';
	}

	/**
	 * Get the directory for a cache item.
	 *
	 * @access	protected
	 * @param	string	the filename hash
	 * @param	string	the cache type
	 * @return	string
	 */
	protected function _get_directory($file = '', $type = self::CACHE_TYPE_DEFAULT)
	{
		$dir = Kohana::$cache_dir;
		if ( ! empty($type))
		{
			$dir .= DIRECTORY_SEPARATOR.$type;
		}

		if ( ! empty($file))
		{
			// Cache directories are split by keys to prevent filesystem overload
			$dir .= DIRECTORY_SEPARATOR.$file[0].$file[1].DIRECTORY_SEPARATOR;
		}
		return $dir;
	}

	/**
	 * Clone the MMI_Cache class.
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function __clone() {}

	/**
	 * Get the class instance.
	 *
	 * @access	public
	 * @return	MMI_Cache
	 */
	public static function instance()
	{
		if ( ! (self::$_instance instanceof self))
		{
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	/**
	 * Get the configuration settings.
	 *
	 * @access	public
	 * @param	boolean	return the configuration as an array?
	 * @return	mixed
	 */
	public static function get_config($as_array = FALSE)
	{
		(self::$_config === NULL) AND self::$_config = Kohana::config('mmi-cache');
		if ($as_array)
		{
			return self::$_config->as_array();
		}
		return self::$_config;
	}
} // End Kohana_MMI_Cache
