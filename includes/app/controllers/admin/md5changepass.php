<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xcms\request;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] == 3)
{
	if ($_POST)
	{
		if (request::P('password', '') != "" || request::P('username', '') != "")
		{
			$info = db::query("SELECT `id` FROM game_users WHERE `username` = '" . request::P('username') . "'", true);

			if (isset($info['id']))
			{
				db::query("UPDATE game_users_info SET `password` = '" . md5(request::P('password')) . "' WHERE `id` = '" . $info['id'] . "';");

				$this->message('Пароль успешно изменён.', 'Успех', '/admin/mode/md5changepass/', 3);
			}
			else
				$this->message('Такого игрока несуществует.', 'Ошибка', '/admin/mode/md5changepass/', 3);
		}
		else
			$this->message('Не введён логин игрока или новый пароль.', 'Ошибка', '/admin/mode/md5changepass/', 3);
	}

	$this->setTemplate('changepass');
	$this->setTitle('Смена пароля');
	$this->display();
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>