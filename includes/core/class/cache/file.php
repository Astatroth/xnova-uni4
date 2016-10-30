<?php

namespace Xcms\cache;
use Xcms\core;
use Xcms\profiler;

/**
 * Класс для работы с системой кэширования на текстовых файла.
 * Рекомендуется использовать только если нет доступа к другим системам кэширования.
 * @author AlexPro
 * @copyright 2011 - 2013
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class file
{
	public function set($name, $value, $time)
	{
		if (core::getConfig('DEBUG'))
			profiler::cacheLog('set', 'main', $name, $value, $time);

		$str_val 	= serialize($value);
		$file_name 	= $this->pathCache($name);

		if (!$file_name)
			return false;

		$file_name .= $this->nameCache($name);

		$f = fopen($file_name, 'w+');

		if (flock($f, LOCK_EX))
		{
			fwrite($f, $str_val.'|{:}|'.($time + time()));
			flock($f, LOCK_UN);
		}
		fclose($f);

		chmod($file_name, 0777);

		unset($str_val);

		return true;
	}

	public function get($name)
	{
		$file_name = $this->getPathCache($name) . $this->nameCache($name);

		if (!file_exists($file_name))
			return false;

		if (!$data = file($file_name))
			return false;

		$data = implode('', $data);

		$data = explode('|{:}|', $data);

		if (core::getConfig('DEBUG'))
			profiler::cacheLog('get', 'main', $name, unserialize($data[0]), date('d - H:i:s', $data[1]));

		if ($data[1] < time())
			return false;

		return unserialize($data[0]);
	}

	public function delete($name)
	{
		if (core::getConfig('DEBUG'))
			profiler::cacheLog('del', 'main', $name, '');

		$file_name = $this->getPathCache($name) . $this->nameCache($name);

		if (is_file($file_name))
			unlink($file_name);
	}

	private function pathCache($valueID)
	{
		$md5 = $this->nameCache($valueID);
		$first_literal = array($md5{0}.$md5{1}, $md5{2}.$md5{3});
		$path = CACHE_DIR . DIRECTORY_SEPARATOR;

		if (!file_exists(ROOT_DIR . $path))
			if (!mkdir(ROOT_DIR . $path, 0777)) return false;

		foreach ($first_literal as $dir)
		{
			$path .= $dir . DIRECTORY_SEPARATOR;

			if (!file_exists(ROOT_DIR . $path))
			{
				if (!mkdir(ROOT_DIR . $path, 0777))
					return false;
			}
		}

		return ROOT_DIR . $path;
	}

	private function getPathCache($valueID)
	{
		$md5 = $this->nameCache($valueID);
		$first_literal = array($md5{0}.$md5{1}, $md5{2}.$md5{3});

		return ROOT_DIR . CACHE_DIR . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $first_literal) . DIRECTORY_SEPARATOR;
	}

	private function nameCache($valueID)
	{
		return md5($valueID);
	}
}
?>