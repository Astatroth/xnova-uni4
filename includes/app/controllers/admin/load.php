<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= 3)
{
	$result = array();
	$result['rows'] = array();

	$query = db::query("SELECT * FROM game_log_load WHERE time >= ".(time() - 86400)." ORDER BY time ASC");

	while ($e = db::fetch($query))
	{
		$result['rows'][] = Array
		(
			'TIME'	=> $e['time'],
			'LOAD'	=> json_decode($e['value'], true)
		);
	}

	$this->setTemplate('load');
	$this->set('parse', $result);
	$this->setTitle("Загрузка сервера");
	$this->display();
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>