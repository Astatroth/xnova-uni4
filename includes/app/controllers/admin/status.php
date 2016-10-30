<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\core;
use Xcms\db;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= 1)
{
	$totalOnline = db::query("SELECT COUNT(id) AS num FROM game_users WHERE `onlinetime` >= '" . (time() - ONLINETIME * 60) ."'", true);

	$result = Array
	(
		'success' => true,
		'data' => Array
		(
			'online' => '<span style="color:green">'.$totalOnline['num'].'</span>',
			'total' => core::getConfig('users_amount'),
			'version' => '<span style="color:red">'.VERSION.'</span>',
			'time' => date("d.m.Y H:i:s")
		)
	);

	echo json_encode($result);
}

?>