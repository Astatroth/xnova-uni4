<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xcms\request;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= 1)
{
	if (request::P('username', '') != '')
	{
		$info = db::query("SELECT id, username, banaday, urlaubs_modus_time FROM game_users WHERE username = '".addslashes(request::P('username', ''))."';", true);

		if (isset($info['id']))
		{
			db::query("DELETE FROM game_banned WHERE who = '" . $info['id'] . "'");
			db::query("UPDATE game_users SET banaday = 0 WHERE id = '" . $info['id'] . "'");

			if ($info['urlaubs_modus_time'] == 1)
				db::query("UPDATE game_users SET urlaubs_modus_time = 0 WHERE id = '" . $info['id'] . "'");

			$this->message("Игрок ".$info['username']." разбанен!", 'Информация');
		}
		else
			$this->message("Игрок не найден!", 'Информация');
	}

	$this->setTemplate('unbanned');
	$this->setTitle('Разблокировка');
	$this->display();
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>