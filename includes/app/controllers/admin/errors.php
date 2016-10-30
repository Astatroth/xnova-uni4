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
	if (isset($_GET['delete']))
	{
		db::query("DELETE FROM game_errors WHERE `error_id` = '" . intval($_GET['delete']) . "'");
	}
	elseif (isset($_GET['deleteall']))
	{
		db::query("TRUNCATE TABLE game_errors");
	}

	$result = array();
	$result['rows'] = array();

	$query = db::query("SELECT * FROM game_errors");

	$result['total'] = db::num_rows($query);

	while ($e = db::fetch($query))
	{
		$result['rows'][] = Array
		(
			'ID' 		=> $e['error_id'],
			'TYPE'		=> $e['error_type'],
			'SENDER'	=> $e['error_sender'],
			'TIME'		=> $e['error_time'],
			'TEXT'		=> htmlspecialchars($e['error_text'])
		);
	}

	$this->setTemplate('errors');
	$this->set('parse', $result);
	$this->setTitle("Ошибки SQL");
	$this->display();
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>