<?php

namespace Xcms;

/**
 * Абстрактный класс для описания интерфейса взаимодействий с базой данных
 * @author AlexPro
 * @copyright 2011 - 2013
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class db
{
	/**
	 * @var \Xcms\database\mysqli
	 */
	private static $class;

	function __construct()
	{
		$classname = 'Xcms\database\\'.DB_DRIVER;

		self::$class = new $classname(SQL_SERVER, SQL_LOGIN, SQL_PASSWORD, SQL_DB_NAME);
	}

    private function __clone()
	{}

	/**
	 * Возвращает имя текущей базы данных
	 * @static
	 */
	public static function db_name ()
	{
		return self::$class->db_name();
	}

	/**
	 * Проверяет, существует ли соединение с базой данных
	 * @static
	 */
	public static function isConnected ()
	{
		return self::$class->isConnected();
	}

	/**
	 * Выполнение запроса к базе данных
	 * @static
	 * @param string $query Текст SQL запроса
	 * @param bool $fetch Возвращать ли ассоциативный массив с результатом запроса
	 * @return array|\mysqli_result
	 */
	public static function query ($query, $fetch = false)
	{
		return self::$class->query($query, $fetch);
	}

	/**
	 * Возвращает ассоциативный массив с результатом запроса
	 * @static
	 * @param \mysqli_result $result Объект выборки из базы данных
	 * @return array
	 */
	public static function fetch_assoc ($result)
	{
		return self::$class->fetch_assoc($result);
	}

	/**
	 * fetch_assoc alias
	 * @param \mysqli_result $result
	 * @return array
	 */
	public static function fetch ($result)
	{
		return self::$class->fetch_assoc($result);
	}

	/**
	 * Возвращает ассоциативный и нумерованный массивы с результатом запроса
	 * @static
	 * @param \mysqli_result $result Объект выборки из базы данных
	 * @return array
	 */
	public static function fetch_array ($result)
	{
		return self::$class->fetch_array($result);
	}

	/**
	 * Возвращает количество строк результата запроса
	 * @static
	 * @param \mysqli_result $result Объект выборки из базы данных
	 * @return int
	 */
	public static function num_rows ($result)
	{
		return self::$class->num_rows($result);
	}

	/**
	 * Возвращает id последней вставленной строки
	 * @static
	 */
	public static function insert_id ()
	{
		return self::$class->insert_id();
	}

	/**
	 * Возвращает результат работы запроса в виде индексированного массива
	 * @static
	 * @param object $result Объект выборки из базы данных
	 * @param bool|string $field Поле ключа массива
	 * @return array
	 */
	public static function extractResult ($result, $field = false)
	{
		return self::$class->extractResult($result, $field);
	}

	/**
	 * Возвращает первый элемент массива
	 * @param $result
	 * @return mixed
	 */
	public static function first ($result)
	{
		if (is_array($result) && count($result) > 0)
			return array_shift($result);
		else
			return false;
	}

	/**
	 * Экранирует текст SQL запроса
	 * @static
 	 * @param string $string Строка SQL запроса
	 * @return string
	 */
	public static function escape_string ($string)
	{
		return self::$class->escape_string($string);
	}
}

new db;
 
?>