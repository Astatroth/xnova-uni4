<?php

namespace Xcms\cache;

/**
 * Класс - заглушка для системы кэширования. На все действия возвращает  false
 * @author AlexPro
 * @copyright 2011 - 2013
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class blank
{
	public function get ($name)
	{
		return false;
	}

	public function set ($name, $value, $time)
	{
		return false;
	}

	public function delete ($name)
	{
		return false;
	}
}

?>
