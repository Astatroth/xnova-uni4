<?php

namespace Xcms\cache;

use Xcms\core;
use Xcms\profiler;

/**
 * Класс для работы с системой кэширования eaccelerator.
 * @author AlexPro
 * @copyright 2011 - 2013
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class eaccelarator
{
	private $connected = false;

	function __construct()
	{
		if (!function_exists('eaccelerator_get'))
			core::addLogEvent('Cache', 'Не установлено расширение eaccelerator');
		else
			$this->connected = true;
	}

	public function get ($name)
	{
		if (!$this->connected)
			return false;

		/** @noinspection PhpUndefinedFunctionInspection */
		$ret = eaccelerator_get($name);

		if (core::getConfig('DEBUG'))
			profiler::cacheLog('get', 'main', $name, $ret);

		if (!$ret)
			$ret = false;

		return $ret;
	}

	public function set ($name, $value, $time)
	{
		if (!$this->connected)
			return false;

		if (core::getConfig('DEBUG'))
			profiler::cacheLog('set', 'main', $name, $value, $time);

		/** @noinspection PhpUndefinedFunctionInspection */
		return eaccelerator_put($name, $value, $time);
	}

	public function delete ($name)
	{
		if (!$this->connected)
			return false;

		if (core::getConfig('DEBUG'))
			profiler::cacheLog('del', 'main', $name, '');

		/** @noinspection PhpUndefinedFunctionInspection */
		return eaccelerator_rm($name);
	}
}

?>
