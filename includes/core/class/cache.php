<?php

namespace Xcms;

/**
 * Абстрактный класс для описания взаимодействий с системой кэширования.
 * Описаны только те методы, который в обязательном порядке должны быть реализованы в драйвере.
 * Наличие остальных методов допустимо, но только с модификатором private (внутренние методы)
 * @author AlexPro
 * @copyright 2011 - 2013
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class cache
{
	/**
	 * @var \Xcms\cache\blank
	 */
	private static $class;
	private static $global = false;

	function __construct()
	{
		$classname = 'Xcms\cache\\'.CACHE_DRIVER;

		self::$class = new $classname();
	}

    private function __clone()
	{}

	/**
	 * Пометить кэш как глобальный
	 * @param bool $value флаг
	 */
	static function isGlobal ($value = true)
	{
		self::$global = $value;
	}

	/**
	 * Получить переменную из кэша.
	 * Возвращает false в случае неудачи.
	 * @static
	 * @param string $name Название переменной
	 * @return mixed
	 */
	static function get ($name)
	{
		return self::$class->get(SERVER_CODE.$name);
	}

	/**
	 * Добавляет переменную в кэш. Если переменная существует или истек срок жизни - перезаписываем.
	 * @static
	 * @param string $name Название переменной
	 * @param mixed $value Значение переменной
	 * @param int $time Время жизни в кэше
	 * @return bool
	 */
	static function set ($name, $value, $time)
	{
		return self::$class->set(SERVER_CODE.$name, $value, $time);
	}

	/**
	 * Удаление переменной из кэша
	 * @static
	 * @param string $name Название переменной
	 * @return bool
	 */
	static function delete ($name)
	{
		return self::$class->delete(SERVER_CODE.$name);
	}
}

new cache;
 
?>