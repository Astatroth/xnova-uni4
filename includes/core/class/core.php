<?php

namespace Xcms;

/**
 * Ядро для работы с системой
 * @author AlexPro
 * @copyright 2011 - 2013
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class core
{
	private static $systemLog 	= array();
	private static $startTime 	= 0;
	private static $config 		= array();
	private static $loadedLib	= array();
	private static $autoloadClasses = array();

	public static $varArray 	= array();

	function __construct(){}

	function __destruct(){}

	/**
	 * @param string $serverCode
	 * @throws \Exception
	 */
	public static function init($serverCode = '')
	{
		if (!defined('START_TIME'))
			define('START_TIME', microtime(TRUE));

		if (!defined('START_MEMORY'))
			define('START_MEMORY', memory_get_usage());

		// Регистрируем автозагрузчик классов
		if (!spl_autoload_register(array(__CLASS__, 'loadClass')))
  			throw new \Exception('Could not register '.__CLASS__.' class autoload function');

		$serverList = array();

		require_once(substr(__DIR__, 0, strlen(__DIR__) - strlen("core/class/")).'/constants.php');

		self::loadServerEnvironment($serverList, $serverCode);

		date_default_timezone_set(TIMEZONE);

		//register_shutdown_function(function()
		//{
			//@chmod($_SERVER['DOCUMENT_ROOT'].'/logs/sql_'.date("d.m.Y").'.log', 0666);
			//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/logs/sql_'.date("d.m.Y").'.log', date("d.m.Y H:i:s")." - SHUTDOWN_FUNCTION\n\n", FILE_APPEND);
		//});

		require_once(ROOT_DIR.CORE_PATH.'functions/core.php');
	}

	public static function registerAutoloadClass ($classes)
	{
		foreach ($classes AS $className => $classPath)
		{
			if (!isset(self::$autoloadClasses[mb_strtolower($className, 'UTF-8')]))
				self::$autoloadClasses[mb_strtolower($className, 'UTF-8')] = $classPath;
		}
	}

	/**
	 * Метод для подключения дополнительных библиотек
	 * @static
	 * @param string $libname Название подключаемой библиотеки
	 * @throws \Exception
	 */
	public static function loadLib ($libname)
	{
		if (!in_array($libname, self::$loadedLib))
		{
			if (file_exists(ROOT_DIR.LIB_PATH.$libname.'/include.php'))
			{
				include_once(ROOT_DIR.LIB_PATH.$libname.'/include.php');
				self::addLogEvent('Core', 'Load library '.$libname.'');

				self::$loadedLib[] = $libname;
			}
			else
				throw new \Exception('System library '.$libname.' not found!');
		}
	}

	/**
	 * Подключает указанный класс, используется совместно с автозагрузчиком классов,
	 * поэтому не должен больше вызываться в системе
	 * @static
	 * @param string $classname Название подключаемого класса
	 */
	public static function loadClass ($classname)
	{
		$classname 	= mb_strtolower($classname, 'UTF-8');

		$namespaces = explode("\\", $classname);

		$path = ROOT_DIR;

		if (strtolower(__NAMESPACE__) == $namespaces[0])
			$path .= CORE_PATH;
		else
			$path .= APP_PATH;

		unset($namespaces[0]);

		$path .= 'class/'.implode('/', $namespaces).'.php';

		if (file_exists($path))
		{
			include_once($path);
			self::addLogEvent('Core', 'Load class '.$classname.'');
		}
		elseif (isset(self::$autoloadClasses[$classname]))
		{
			include_once(self::$autoloadClasses[$classname]);

			self::addLogEvent('Core', 'Load registered class '.$classname.'');
		}
	}

	/**
	 * Загружает пользовательскую конфигурацию системы в специальный массив.
	 * Конфиг берется из файлов констант (неизменяемый) и из БД (изменяемый)
	 * @static
	 * @return void
	 */
	public static function loadConfig ()
	{
		$result = cache::get('core::config');

		if ($result === false)
		{
			$loads = db::query("SELECT `key`, `value` FROM game_config");

			while ($load = db::fetch_assoc($loads))
			{
				$result[$load['key']] = $load['value'];
			}

			cache::set('core::config', $result, 300);
		}

		if (is_array($result))
			self::$config += $result;
	}

	/**
	 * Очистка кэша конфигурации
	 * @static
	 * @return void
	 */
	public static function clearConfig ()
	{
		cache::delete('core::config');
	}

	/**
	 * Обновление параметров конфигурации
	 * @static
	 * @param string $key
	 * @param string|int $value
	 */
	public static function updateConfig ($key, $value)
	{
		db::query("UPDATE game_config SET `value` = '". $value ."' WHERE `key` = '".$key."';");
		self::setConfig($key, $value);
	}

	/**
	 * Получаем значение конфигурации по его ключу
	 * @static
	 * @param string $name Ключ конфигурационной переменной
	 * @param mixed $default Значение по умолчанию, если данный параметр не найден
	 * @return mixed
	 */
	public static function getConfig ($name, $default = false)
	{
		if (isset(self::$config[$name]))
			return self::$config[$name];
		else
			return $default;
	}

	/**
	 * Получаем значение конфигурации по его ключу
	 * @static
	 * @param string $name Ключ конфигурационной переменной
	 * @param mixed $default Значение по умолчанию, если данный параметр не найден
	 * @return mixed
	 */
	public static function getConfigFromDB ($name, $default = false)
	{
		$result = db::query("SELECT `value` FROM game_config WHERE `key` = '".$name."' LIMIT 1", true);

		if (isset($result['value']))
			return $result['value'];
		else
			return $default;
	}

	/**
	 * Установка значения конфигурационной переменной
	 * @static
	 * @param string $key Ключ конфигурационной переменной
	 * @param mixed $value Значение конфигурационной переменной
	 * @return bool
	 */
    public static function setConfig ($key, $value)
   	{
   		self::$config[$key] = $value;

        return true;
   	}

	/**
	 * Добавляем системное событие в журнал
	 * @static
	 * @param string $module Название модуля, в котором произошло событие
	 * @param string $message Описание события
	 * @param array $params Дополнительные параметры для вывода
	 */
	public static function addLogEvent ($module, $message, $params = array())
	{
		self::$systemLog[] = array($module, $message, $params);
	}

	/**
	 * Получаем журнал событий
	 * @static
	 * @return array Массив событий
	 */
	public static function getLogEvent ()
	{
		return self::$systemLog;
	}

	/**
	 * Запускаем системный таймер
	 * @static
	 * @return bool
	 */
	public static function startTimer()
	{
		$start_time         = microtime();
		$start_array        = explode(" ", $start_time);
		self::$startTime   = $start_array[1] + $start_array[0];

		return true;
 	}

	/**
	 * Получаем время, пройденное после последнего запуска таймера
	 * Если таймер не был запущен - возвращаем текущее время в милисекундах
	 * @static
	 * @return mixed Количество милисекунд, пройденное после запуска таймера
	 */
	public static function getTimer()
	{
		$end_time   = microtime();
		$end_array  = explode(" ", $end_time);
		$time       = $end_array[1] + $end_array[0] - self::$startTime;

		return $time;
	}

	static public function getVariable ($key, $default = false)
	{
		return (isset(self::$varArray[$key]) ? self::$varArray[$key] : $default);
	}

	static public function setVariable ($key, $value)
	{
		self::$varArray[$key] = $value;

		return true;
	}

	static function loadServerEnvironment ($serverList, $code = '')
	{
		if (!isset($_SERVER['HTTP_HOST']))
			$_SERVER['HTTP_HOST'] = '';

		foreach ($serverList AS $serverCode => $serverConfig)
		{
			if (($code != '' && $code == $serverCode) || ($code == '' && ($serverConfig['LOCATION'] == $_SERVER['HTTP_HOST'] || $serverConfig['LOCATION'] == '')))
			{
				if (!$serverConfig['ROOT_DIR'])
					$serverConfig['ROOT_DIR'] = $_SERVER['DOCUMENT_ROOT'].'/';

				if (!$serverConfig['LOCATION'])
					$serverConfig['LOCATION'] = $_SERVER['HTTP_HOST'];

				define('SERVER_CODE', 	$serverCode);
				define('ROOT_DIR', 		$serverConfig['ROOT_DIR']);
				define('SITE_URL', 		'http://'.$serverConfig['LOCATION'].'/');

				require_once(ROOT_DIR.'/includes/environment/'.mb_strtolower(SERVER_CODE, 'UTF-8').'_constants.php');

				/**
				 * @var $config array
				 */
				self::$config = $config;

				break;
			}
		}

		if (!defined('SERVER_CODE'))
			throw new \Exception('UNIVERSE not found');
	}
}

?>