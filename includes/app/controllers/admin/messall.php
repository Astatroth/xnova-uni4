<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] < 1)
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

if (isset($_POST["tresc"]))
{
	if (user::get()->data['authlevel'] == 3)
	{
		$kolor = 'yellow';
		$ranga = 'Администратор';
	}
	elseif (user::get()->data['authlevel'] == 1)
	{
		$kolor = 'skyblue';
		$ranga = 'Оператор';
	}
	elseif (user::get()->data['authlevel'] == 2)
	{
		$kolor = 'yellow';
		$ranga = 'Супер оператор';
	}

	if ((isset($_POST["tresc"]) && $_POST["tresc"] != '') && (isset($_POST["temat"]) && $_POST["temat"] != ''))
	{
		$sq = db::query("SELECT `id` FROM game_users");

		$Time = time();

		$From 		= "<font color=\"" . $kolor . "\">Информационное сообщение (".user::get()->data['username'].")</font>";
		$Message 	= $_POST['tresc'];

		while ($u = db::fetch($sq))
		{
			user::get()->sendMessage($u['id'], false, $Time, 1, $From, $Message);
		}

		$this->message("<font color=\"lime\">Сообщение успешно отправлено всем игрокам!</font>", "Выполнено", "?set=admin&mode=messall", 3);
	}
	else
		$this->message("<font color=\"red\">Не все поля заполнены!</font>", "Ошибка", "?set=admin&mode=messall", 3);
}
else
{
	$this->setTemplate('messtoall');
	$this->setTitle('Рассылка');
	$this->display();
}

?>