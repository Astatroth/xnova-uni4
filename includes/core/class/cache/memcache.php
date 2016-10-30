<?php

namespace Xcms\cache;

use Xcms\Core;
use Xcms\Profiler;

/**
 * Класс для работы с системой кэширования memcache.
 * @author AlexPro
 * @copyright 2011 - 2013
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class memcache
{
	/**
	 * @var $cache Memcache
	 */
	private $cache;
	private $init = false;

	function __construct ()
	{
		if (!class_exists('Memcache'))
		{
			core::addLogEvent('Cache', 'Невозможно загрузить классы Memcache');
		}
		else
		{
			$this->cache = new \Memcache;
			$this->init = $this->cache->connect(MEMCACHE_HOST, MEMCACHE_PORT);
		}
	}

	public function get ($name)
	{
		if ($this->init)
		{
			$ret = $this->cache->get($name);

			if (core::getConfig('DEBUG'))
				profiler::cacheLog('get', 'main', $name, $ret);

			return $ret;
		}
		else
			return false;
	}

	public function set ($name, $value, $time)
	{
		if ($this->init)
		{
			if (core::getConfig('DEBUG'))
				profiler::cacheLog('set', 'main', $name, $value, $time);

			return $this->cache->set($name, $value, 0, $time);
		}
		else
			return false;
	}

	public function delete ($name)
	{
		if ($this->init)
		{
			if (core::getConfig('DEBUG'))
				profiler::cacheLog('del', 'main', $name, '');

			return $this->cache->delete($name);
		}
		return false;
	}
}

?>