<?php

namespace Xcms\database;

use Xcms\Core;
use Xcms\Profiler;
use Xcms\sql;

/**
 * Драйвер для работы с базой данных MYSQLi
 * @author AlexPro
 * @copyright 2011 - 2013
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class mysqli
{
	private $server 	= '';
	private $database 	= '';
	private $login 		= '';
	private $password 	= '';
	public  $log		= '';
	public  $numqueries	= 0;

	/**
	 * @var \mysqli
	 */
	public $link = null;

	function __construct($server, $login, $password, $database)
	{
		$this->server 	= $server;
		$this->database = $database;
		$this->login 	= $login;
		$this->password = $password;

		$this->init();
	}

	public function db_name ()
	{
		return $this->database;
	}

	private function init()
	{
		if (!$this->isConnected())
		{
			//mysqli_report(MYSQLI_REPORT_ALL);
			$this->link = new \mysqli($this->server, $this->login, $this->password, $this->database);

			if ($this->link->connect_errno)
			{
				exit('Error connecting to database. Please try later.');
			}

			//$this->link->set_charset("utf8");
		}
	}

	public function isConnected ()
	{
		return !is_null($this->link);
	}

	private function replacePrefix ($sql, $prefix = 'ap_cms_')
	{
		if ($prefix == SQL_PREFIX)
			return $sql;

		return trim(str_replace($prefix, SQL_PREFIX, $sql));
	}

	public function query ($query, $fetch = false)
	{
		if (!$this->isConnected())
		{
			$this->init();
		}

		$query = $this->replacePrefix($query);

		if (core::getConfig('DEBUG'))
		{
			$benchmark = profiler::start("Database (".$this->database.")", htmlspecialchars($query));
		}

		//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/logs/sql_'.date("d.m.Y").'.log', date("d.m.Y H:i:s").' - '.$query."\n", FILE_APPEND);

		$sqlquery = $this->link->query($query) or $this->sql_error($this->link->error."<br />".htmlspecialchars($query)."<br />");

		//$this->lastQuery = $query;

		if (isset($benchmark))
		{
			profiler::stop($benchmark);
		}

		if ($fetch)
		{
			return $this->fetch_assoc($sqlquery);
		}
		else
		{
			return $sqlquery;
		}
	}

	/**
	 * @static
	 * @param $result \mysqli_result
	 * @return array
	 */
	public function fetch_assoc ($result)
	{
		return $result->fetch_assoc();
	}

	/**
	 * @static
	 * @param $result \mysqli_result
	 * @return array
	 */
	public function fetch_array ($result)
	{
		return $result->fetch_array();
	}

	/**
	 * @static
	 * @param $result \mysqli_result
	 * @return int
	 */
	public function num_rows ($result)
	{
		if ($result)
		{
			return $result->num_rows;
		}

		return 0;
	}

	public function insert_id ()
	{
		return $this->link->insert_id;
	}

	public function extractResult ($result, $field = false)
	{
		$data = array();

		if (!$field)
		{
			while ($res = $this->fetch_assoc($result))
				$data[] = $res;
		}
		else
		{
			while ($res = $this->fetch_assoc($result))
				$data[$res[$field]] = $res;
		}

		return $data;
	}

	public function escape_string ($string)
	{
		if ($this->isConnected())
		{
			$this->init();
		}

		return $this->link->real_escape_string($string);
	}

	private function sql_error ($message)
	{
		if(!$this->isConnected())
			die('error ###');

		//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/logs/sql_'.date("d.m.Y").'.log', date("d.m.Y H:i:s").' - sql error '.$message."\n", FILE_APPEND);

		if (strpos($message, 'game_errors') === false)
		{
			sql::build()->insert('game_errors')->set(Array
			(
				'error_sender' 	=> ((isset($_SESSION['uid'])) ? $_SESSION['uid'] : 0),
				'error_time' 	=> time(),
				'error_type' 	=> 'sql error',
				'error_text' 	=> self::escape_string($message)
			))
			->execute();
		}

        if (function_exists('message'))
		    message($message);
		else
			echo $message;
	}
}

?>