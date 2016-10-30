<?php

namespace Xcms;

define('VALUE_STRING', 1);
define('VALUE_INT', 2);
define('VALUE_FLOAT', 3);

/**
 * Класс - обработчик поступающих HTTP запросов
 * @author AlexPro
 * @copyright 2011 - 2013
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class request
{
	static $url 			= '';
	static $client_ip 		= '';
	static $client_browser 	= '';
	static $timestamp		= 0;
	static $originalQuery	= Array('query' => '');
	static $method			= 'GET';
	
	private static $_get 	= array();
	private static $_post 	= array();

	/**
	 * Конструктор - инициализатор класса
	 */
	function __construct()
	{
		self::$timestamp = time();

		if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != '' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1')
			self::$client_ip = $_SERVER['REMOTE_ADDR'];
		elseif (isset($_SERVER['HTTP_X_REAL_IP']) && $_SERVER['HTTP_X_REAL_IP'] != '' && $_SERVER['HTTP_X_REAL_IP'] != '127.0.0.1')
			self::$client_ip = $_SERVER['HTTP_X_REAL_IP'];
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '' && $_SERVER['HTTP_X_FORWARDED_FOR'] != '127.0.0.1')
			self::$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else
			self::$client_ip = '127.0.0.1';

		self::$client_browser = $_SERVER['HTTP_USER_AGENT'];
		
		self::$_post = $_POST;
		self::$method = $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Разбираем строку запроса на внутренний переменные со структурой ключ=>значение
	 * @static
	 * @param string $uri Строка адреса
	 * @return bool
	 */
	static function parseUrl ($uri = '')
	{
		$uri = self::parseRequest($uri);

		if (!$uri)
			return false;

		$uri    = ltrim($uri, '/');

		$rules = array();

		if (file_exists(ROOT_DIR.'includes/url_rewrite.php'))
		{
			//подключаем список rewrite-правил
			include(ROOT_DIR.'includes/url_rewrite.php');
		}

		$found = false;

		if (count($rules) > 0)
		{
			//перебираем правила
			foreach ($rules AS $rule)
			{
				if (preg_match($rule['source'], $uri, $matches))
				{
					foreach($matches as $key=>$value)
					{
						if (!$key)
							continue;

						if (strstr($rule['target'], '{'.$key.'}'))
						{
							$rule['target'] = str_replace('{'.$key.'}', $value, $rule['target']);
						}

						if (!$rule['action'])
							$rule['action'] = 'rewrite';

						switch($rule['action'])
						{
          					case 'rewrite'      : $uri = $rule['target']; $found = true; break;
          					case 'redirect'     : self::redirectTo($rule['target']); break;
          					case 'redirect-301' : self::redirectTo($rule['target'], '301'); break;
          					case 'alias'        : include_once($rule['target']); die(); break;
						}
					}

					if ($found)
						break;
				}
			}
		}

		$get = self::getQueryString();

		if ($get != '')
		{
      		parse_str($get, $arr);

			foreach ($arr as $key => $value)
			{
				self::$_get[$key] = $value;
			}

			$uri = str_replace('?'.$get, '', $uri);
  		}

		if (strpos($uri, '.php') !== false)
			$uri = substr($uri, strpos($uri, '.php') + 4);

		self::$url = $uri;

		if ($uri != '')
		{
			$params = explode('/', $uri);

			self::$_get['set'] = $params[0];

			for ($i = 1; $i < count($params); $i += 2)
			{
				if ($params[$i] != '')
					self::$_get[$params[$i]] = (isset($params[$i+1])) ? $params[$i+1] : '';
			}
		}

		core::addLogEvent('request', 'parseUrl', self::$_get);

		return true;
	}

	/**
	 * Установить параметр $_GET
	 * @param string $key Ключ
	 * @param mixed $value Значение
	 * @return mixed
	 */
	static function setG ($key, $value)
	{
		return self::$_get[$key] = $value;
	}

	/**
	 * Установить параметр $_POST
	 * @param string $key Ключ
	 * @param mixed $value Значение
	 * @return mixed
	 */
	static function setP ($key, $value)
	{
		return self::$_post[$key] = $value;
	}

	/**
	 * Получение IP адреса пользователя в 2х форматах:
	 * 1) В формате 32х битного числа
	 * 2) В виде обычной строки
	 * @static
	 * @param bool $is_int Выбор формата
	 * @return int|string IP адрес
	 */
	static function getClientIp ($is_int = false)
	{
		if ($is_int)
			return sprintf("%u", ip2long(self::$client_ip));
		else
			return self::$client_ip;
	}

	/**
	 * Получение информации о браузере пользователя в 2х форматах:
	 * 1) В виде строки
	 * 2) В виде массива данных
	 * @static
	 * @param bool $is_string Выбор формата
	 * @return mixed|string
	 */
	static function getClientBrowser ($is_string = true)
	{
		if ($is_string)
			return self::$client_browser;
		else
			return get_browser(null, true);
	}

	/**
	 * Преобразование IP адреса из числа в строку
	 * @static
	 * @param $ip int IP адрес в виде числа
	 * @return string IP адрес в виде строки
	 */
	static function convertIp ($ip)
	{
		return long2ip($ip);
	}

	/**
	 * @param $key
	 * @param bool $default
	 * @param int $type
	 * @return bool|float|int|string|array
	 */
	static function G ($key, $default = false, $type = VALUE_STRING)
	{
		if (isset(self::$_get[$key]))
			return self::typingValue(self::$_get[$key], $type);
		else
			return $default;
	}

	/**
	 * @param $key
	 * @param bool $default
	 * @param int $type
	 * @return bool|float|int|string|array
	 */
	static function P ($key, $default = false, $type = VALUE_STRING)
	{
		if (isset(self::$_post[$key]))
			return self::typingValue(self::$_post[$key], $type);
		else
			return $default;
	}

	/**
	 * @param $key
	 * @param bool $default
	 * @param int $type
	 * @return bool|float|int|string|array
	 */
	static function R ($key, $default = false, $type = VALUE_STRING)
	{
		if (isset(self::$_get[$key]))
			return self::typingValue(self::$_get[$key], $type);
		elseif (isset(self::$_post[$key]))
			return self::typingValue(self::$_post[$key], $type);
		else
			return $default;
	}

	/**
	 * @param $value
	 * @param int $type
	 * @return float|int|string|array
	 */
	private static function typingValue ($value, $type = VALUE_STRING)
	{
		if (is_array($value))
		{
			$data = array();

			foreach ($value as $key => $val)
			{
				$data[$key] = self::typingValue($val, $type);
			}

			return $data;
		}
		else
		{
			switch ($type)
			{
				case VALUE_STRING:
					return (string) $value;
					break;
				case VALUE_INT:
				
					if (!$value)
						$value = 0;
				
					return (int) $value;
					break;
				case VALUE_FLOAT:
				
					if (!$value)
						$value = 0;
				
					return (float) $value;
					break;
				default:
					return (string) $value;
			}
		}
	}

	/**
	 * @param string $url
	 * @param string $code
	 */
	static function redirectTo ($url = '', $code = '')
	{
		if ($code == '301')
			self::sendHeader('HTTP/1.1 301 Moved Permanently');
		if ($code == '404')
			self::sendHeader('HTTP/1.1 404 Not Found');

		if ($url == '')
			$url = SITE_URL;

		if (self::R('popup') !== false)
		{
			die('<script>top.location.href="'.$url.'";</script>');
		}

		self::sendHeader("Location", $url.(self::R('ajax') !== false ? '&ajax' : '').((isset($_SESSION['OKAPI'])) ? '&'.http_build_query($_SESSION['OKAPI']) : ''));
		die();
	}

	static public function sendHeader($name, $value = NULL)
	{
		header($name.(!is_null($value) ? ': '.$value : ''));
	}

	static function parseRequest ($url = '')
	{
		if ($url == '')
			$url = $_SERVER['REQUEST_URI'];

		self::$originalQuery = parse_url($url);

		return $url;
	}

	static function getQueryString ()
	{
		return (isset(self::$originalQuery['query']) ? self::$originalQuery['query'] : '');
	}

	static function getClearQuery ()
	{
		$out = self::$_get;

		unset($out['ajax']);
		unset($out['popup']);
		unset($out['random']);
		unset($out['_']);

		return '?'.http_build_query($out);
	}

	static function checkSaveState ()
	{
		return (!(self::G('ep') == 'dontsavestate'));
	}
}

new request;

?>