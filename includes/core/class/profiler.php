<?php

namespace Xcms;

/**
 * @author  Aleksey Ivanov <alertdevelop@gmail.com>
 * @version 0.1.1 beta
 * @see	 http://alertdevelop.ru/projects/profilertoolbar
 * @see	 https://github.com/Alert/profilertoolbar
 */
class profiler
{
	private static $_cfg = null;
	// data for output
	public static $DATA_APP_TIME = null;
	public static $DATA_APP_MEMORY = null;
	public static $DATA_SQL = null;
	public static $DATA_CACHE = null;
	public static $DATA_POST = null;
	public static $DATA_GET = null;
	public static $DATA_FILES = null;
	public static $DATA_COOKIE = null;
	public static $DATA_SESSION = null;
	public static $DATA_SERVER = null;
	public static $DATA_ROUTES = null;
	public static $DATA_INC_FILES = null;
	public static $DATA_CUSTOM = null;

	private static $_data_collected = false;

	protected static $_marks = array();
	public static $rollover = 1000;

	public static function start($group, $name)
	{
		static $counter = 0;

		// Create a unique token based on the counter
		$token = 'kp/'.base_convert($counter++, 10, 32);

		self::$_marks[$token] = array
		(
			'group' => strtolower($group),
			'name'  => (string) $name,

			// Start the benchmark
			'start_time'   => microtime(TRUE),
			'start_memory' => memory_get_usage(),

			// Set the stop keys without values
			'stop_time'    => FALSE,
			'stop_memory'  => FALSE,
		);

		return $token;
	}

	public static function stop($token)
	{
		// Stop the benchmark
		self::$_marks[$token]['stop_time']   = microtime(TRUE);
		self::$_marks[$token]['stop_memory'] = memory_get_usage();
	}

	public static function delete($token)
	{
		// Remove the benchmark
		unset(self::$_marks[$token]);
	}

	public static function groups()
	{
		$groups = array();

		foreach (self::$_marks as $token => $mark)
		{
			// Sort the tokens by the group and name
			$groups[$mark['group']][$mark['name']][] = $token;
		}

		return $groups;
	}

	public static function total($token)
	{
		// Import the benchmark data
		$mark = self::$_marks[$token];

		if ($mark['stop_time'] === FALSE)
		{
			// The benchmark has not been stopped yet
			$mark['stop_time']   = microtime(TRUE);
			$mark['stop_memory'] = memory_get_usage();
		}

		return array
		(
			// Total time in seconds
			$mark['stop_time'] - $mark['start_time'],

			// Amount of memory in bytes
			$mark['stop_memory'] - $mark['start_memory'],
		);
	}

	public static function stats(array $tokens)
	{
		// Min and max are unknown by default
		$min = $max = array(
			'time' => NULL,
			'memory' => NULL);

		// Total values are always integers
		$total = array(
			'time' => 0,
			'memory' => 0);

		foreach ($tokens as $token)
		{
			// Get the total time and memory for this benchmark
			list($time, $memory) = self::total($token);

			if ($max['time'] === NULL OR $time > $max['time'])
			{
				// Set the maximum time
				$max['time'] = $time;
			}

			if ($min['time'] === NULL OR $time < $min['time'])
			{
				// Set the minimum time
				$min['time'] = $time;
			}

			// Increase the total time
			$total['time'] += $time;

			if ($max['memory'] === NULL OR $memory > $max['memory'])
			{
				// Set the maximum memory
				$max['memory'] = $memory;
			}

			if ($min['memory'] === NULL OR $memory < $min['memory'])
			{
				// Set the minimum memory
				$min['memory'] = $memory;
			}

			// Increase the total memory
			$total['memory'] += $memory;
		}

		// Determine the number of tokens
		$count = count($tokens);

		// Determine the averages
		$average = array(
			'time' => $total['time'] / $count,
			'memory' => $total['memory'] / $count);

		return array(
			'min' => $min,
			'max' => $max,
			'total' => $total,
			'average' => $average);
	}

	public static function application()
	{
		// Load the stats from cache, which is valid for 1 day
		//$stats = Kohana::cache('self_application_stats', NULL, 3600 * 24);

		$stats = false;

		if (!is_array($stats))
		{
			// Initialize the stats array
			$stats = array(
				'min'   => array(
					'time'   => NULL,
					'memory' => NULL),
				'max'   => array(
					'time'   => NULL,
					'memory' => NULL),
				'total' => array(
					'time'   => NULL,
					'memory' => NULL),
				'count' => 0);
		}

		// Get the application run time
		$time = microtime(TRUE) - START_TIME;

		// Get the total memory usage
		$memory = memory_get_usage() - START_MEMORY;

		// Calculate max time
		if ($stats['max']['time'] === NULL OR $time > $stats['max']['time'])
		{
			$stats['max']['time'] = $time;
		}

		// Calculate min time
		if ($stats['min']['time'] === NULL OR $time < $stats['min']['time'])
		{
			$stats['min']['time'] = $time;
		}

		// Add to total time
		$stats['total']['time'] += $time;

		// Calculate max memory
		if ($stats['max']['memory'] === NULL OR $memory > $stats['max']['memory'])
		{
			$stats['max']['memory'] = $memory;
		}

		// Calculate min memory
		if ($stats['min']['memory'] === NULL OR $memory < $stats['min']['memory'])
		{
			$stats['min']['memory'] = $memory;
		}

		// Add to total memory
		$stats['total']['memory'] += $memory;

		// Another mark has been added to the stats
		$stats['count']++;

		// Determine the averages
		$stats['average'] = array(
			'time'   => $stats['total']['time'] / $stats['count'],
			'memory' => $stats['total']['memory'] / $stats['count']);

		// Cache the new stats
		//Kohana::cache('self_application_stats', $stats);

		// Set the current application execution time and memory
		// Do NOT cache these, they are specific to the current request only
		$stats['current']['time']   = $time;
		$stats['current']['memory'] = $memory;

		// Return the total application run time and memory usage
		return $stats;
	}

	/**
	 * Collect all data
	 * @static
	 * @return void
	 */
	public static function collectData ()
	{
		if (self::$_data_collected)
			return;

		self::$DATA_APP_TIME = self::getAppTime();
		self::$DATA_APP_MEMORY = self::getAppMemory();
		self::$DATA_SQL = self::getSql();
		self::$DATA_CACHE = self::getCache();
		self::$DATA_POST = self::getPost();
		self::$DATA_GET = self::getGet();
		self::$DATA_FILES = self::getFiles();
		self::$DATA_COOKIE = self::getCookie();
		self::$DATA_SESSION = self::getSession();
		self::$DATA_SERVER = self::getServer();
		self::$DATA_ROUTES = self::getRoutes();
		self::$DATA_INC_FILES = self::getIncFiles();
		self::$DATA_CUSTOM = self::getCustom();
		self::$_data_collected = true;
	}

	/**
	 * Render data to html
	 * @static
	 * @param $class string
	 * @param bool $print - echo rendered data
	 * @return string
	 */
	public static function render ($class = '', $print = false)
	{
		if (!self::cfg('enabled') || !$class)
			return '';

		ob_start();
		include(ROOT_DIR.'template/modules/profiler/'.$class.'.php');
		$html = ob_get_contents();
		ob_end_clean();

		if ($print)
			echo $html;

		return $html;
	}

	// =============== methods for collect data ======================================
	private static function getAppTime ()
	{
		$tmp = self::application();
		return $tmp['current']['time'];
	}

	private static function getAppMemory ()
	{
		$tmp = self::application();
		return $tmp['current']['memory'];
	}

	private static function getSql ()
	{
		$sql = array();
		$groups = self::groups();
		foreach ($groups as $groupName => $benchmarks)
		{
			if (strpos($groupName, 'database') !== 0)
				continue;
			$sqlGroup = substr($groupName, strpos($groupName, '(') + 1, strpos($groupName, ')') - strpos($groupName, '(') - 1);
			$sql[$sqlGroup] = array('data' => array(), 'total' => array('time' => 0, 'memory' => 0, 'count' => 0));

			foreach ($benchmarks as $benchName => $tokens)
			{
				foreach ($tokens as $token)
				{
					$stats = self::stats(array($token));
					$sql[$sqlGroup]['data'][] = array('sql' => $benchName, 'time' => $stats['total']['time'], 'memory' => $stats['total']['memory'], 'rows' => (isset(self::$DATA_SQL[$sqlGroup][$benchName])) ? self::$DATA_SQL[$sqlGroup][$benchName]['rows'] : null, 'explain' => (isset(self::$DATA_SQL[$sqlGroup][$benchName])) ? self::$DATA_SQL[$sqlGroup][$benchName]['explain'] : null,);
					$sql[$sqlGroup]['total']['time'] += $stats['total']['time'];
					$sql[$sqlGroup]['total']['memory'] += $stats['total']['memory'];
					$sql[$sqlGroup]['total']['count']++;
				}
			}

			if ($sql[$sqlGroup]['total']['time'] <= 0)
				$sql[$sqlGroup]['total']['time'] = 0.000001;
		}

		return $sql;
	}

	private static function getCache ()
	{
		if (!isset(self::$DATA_CACHE['total']))
			self::$DATA_CACHE['total'] = array('get' => 0, 'set' => 0, 'del' => 0);
		if (!isset(self::$DATA_CACHE['data']))
		{
			self::$DATA_CACHE['data']['default'] = array('total' => array('get' => 0, 'set' => 0, 'del' => 0), 'data' => array(),);
		}
		return self::$DATA_CACHE;
	}

	private static function getPost ()
	{
		return (isset($_POST)) ? $_POST : array();
	}

	private static function getGet ()
	{
		return (isset($_GET)) ? $_GET : array();
	}

	private static function getFiles ()
	{
		$all = array();
		foreach ($_FILES as $k => $file)
		{
			if (is_array($file['name']))
			{
				$count = count($file['name']);
				for ($i = 0; $i < $count; $i++)
				{
					$all[$k . " [$i]"] = array('name' => $file['name'][$i], 'type' => $file['type'][$i], 'tmp_name' => $file['tmp_name'][$i], 'error' => $file['error'][$i], 'size' => $file['size'][$i]);
				}
			}
			else
			{
				$all[$k] = $file;
			}
		}
		return $all;
	}

	private static function getCookie ()
	{
		return (isset($_COOKIE)) ? $_COOKIE : array();
	}

	private static function getSession ()
	{
		return (isset($_SESSION)) ? $_SESSION : array();
	}

	private static function getServer ()
	{
		return (isset($_SERVER)) ? $_SERVER : array();
	}

	private static function getRoutes ()
	{
		$res = array('data' => array(), 'total' => array('count' => 0));
		$res['data'] = core::getLogEvent();
		$res['total']['count'] = count($res['data']);
		return $res;
	}

	private static function getIncFiles ()
	{
		$files = get_included_files();
		$res = array('data' => array(), 'total' => array('size' => 0, 'lines' => 0, 'count' => 0));
		foreach ($files as $file)
		{
			$size = filesize($file);
			$lines = substr_count(file_get_contents($file), "\n");
			$res['total']['size'] += $size;
			$res['total']['lines'] += $lines;
			$res['total']['count']++;
			$res['data'][] = array('name' => $file, 'size' => $size, 'lines' => $lines, 'lastModified' => filemtime($file),);
		}
		return $res;
	}

	private static function getCustom ()
	{
		return self::$DATA_CUSTOM;
	}

	// =============== /methods for collect data ======================================
	/**
	 * Collect sql queries
	 * Used in database classes
	 * @static
	 * @param $instance
	 * @param $sql
	 * @param null $rows
	 * @param null $explain
	 * @return void
	 */
	public static function setSqlData ($instance, $sql, $rows = null, $explain = null)
	{
		self::$DATA_SQL[$instance][$sql]['rows'] 	= $rows;
		self::$DATA_SQL[$instance][$sql]['explain'] = $explain;
	}

	/**
	 * Collect Cache log item
	 * Used in Cache classes
	 * @static
	 * @param $action
	 * @param $instalce
	 * @param $id
	 * @param $data mixed
	 * @param null $lifetime
	 * @return void
	 */
	public static function cacheLog ($action, $instalce, $id, $data, $lifetime = null)
	{
		if (!in_array($action, array('get', 'set', 'del')))
			return;

		self::$DATA_CACHE['data'][$instalce]['data'][] = array('action' => $action, 'id' => $id, 'data' => print_r($data, true), 'lifetime' => $lifetime);

		if (!isset(self::$DATA_CACHE['total']))
			self::$DATA_CACHE['total'] = array('get' => 0, 'set' => 0, 'del' => 0);

		if (!isset(self::$DATA_CACHE['data'][$instalce]['total']))
			self::$DATA_CACHE['data'][$instalce]['total'] = array('get' => 0, 'set' => 0, 'del' => 0);

		self::$DATA_CACHE['total'][$action]++;
		self::$DATA_CACHE['data'][$instalce]['total'][$action]++;
	}

	/**
	 * Add YOUR custom data
	 * @static
	 * @param string $tabName
	 * @param $data mixed
	 * @return void
	 */
	public static function addData ($data, $tabName = 'default')
	{
		self::$DATA_CUSTOM[$tabName][] = $data;
	}

	/**
	 * Get module config param
	 * @static
	 * @param string $param
	 * @param bool $default
	 * @return mixed
	 */
	public static function cfg ($param = '', $default = false)
	{
		if (self::$_cfg === null)
			self::$_cfg = core::getConfig('profilertoolbar');

		if (empty($param))
			return self::$_cfg;

		if (isset(self::$_cfg[$param]))
			return self::$_cfg[$param];
		else
			return $default;
	}

	// ============================= help functions ==========================================
	public static function formatTime ($time, $addUnit = true, $spaceBeforeUnit = true)
	{
		$decimals = 6;
		if (($p = self::cfg('format.time')) == 'ms')
		{
			$time *= 1000;
			$decimals = 3;
		}

		$res = number_format($time, $decimals);
		if ($addUnit)
			$res .= ($spaceBeforeUnit) ? ' ' . $p : $p;

		return $res;
	}

	public static function formatMemory ($memory, $addUnit = true, $spaceBeforeUnit = true)
	{
		if (($p = self::cfg('format.memory')) == 'kb')
			$memory /= 1024;
		else
			$memory /= 1024 * 1024;

		$res = number_format($memory);
		if ($addUnit)
			$res .= ($spaceBeforeUnit) ? ' ' . $p : $p;

		return $res;
	}

	public static function varDump ($var)
	{
		if (is_bool($var))
			return ($var) ? 'true' : 'false';
		elseif (is_scalar($var))
			return (string)$var;
		else
		{
			ob_start();
			var_dump($var);
			return '<pre>' . preg_replace('/=>\n\s+/', ' => ', ob_get_clean()) . '</pre>';
		}
	}
}