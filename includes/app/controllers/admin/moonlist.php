<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xcms\request;
use Xnova\system;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= 2)
{
	$parse = array();

	$action = request::G('action', '');

	if ($action == 'add')
	{
		if (isset($_POST['user']))
		{
			$Galaxy = request::P('galaxy', VALUE_INT, 0);
			$System = request::P('system', VALUE_INT, 0);
			$Planet = request::P('planet', VALUE_INT, 0);
			$UserId = request::P('user', VALUE_INT, 0);
			$Diamet = request::P('diameter', VALUE_INT, 0);

			if ($Galaxy > MAX_GALAXY_IN_WORLD || $Galaxy < 1)
				$this->message('Ошибочная галактика!');
			if ($System > MAX_SYSTEM_IN_GALAXY || $System < 1)
				$this->message('Ошибочная система!');
			if ($Planet > MAX_PLANET_IN_SYSTEM || $Planet < 1)
				$this->message('Ошибочная планета!');

			$check = db::query("SELECT id FROM game_users WHERE id = " . $UserId . "", true);

			if (!isset($check['id']))
				$this->message('Пользователя не существует');

			$Diamet = min(max($Diamet, 20), 0);

			$moon = system::CreateOneMoonRecord($Galaxy, $System, $Planet, $UserId, $Diamet);

			if ($moon !== false)
				$this->message('ID: ' . $moon);
			else
				$this->message('Луна не создана');
		}

		$this->setTemplate('moonlist_add');
		$this->setTitle('Создание луны');
	}
	else
	{
		$parse['moons'] = array();

		$query = db::query("SELECT * FROM game_planets WHERE planet_type='3' ORDER BY galaxy,system,planet");

		while ($u = db::fetch($query))
		{
			$parse['moons'][] = $u;
		}

		$this->setTemplate('moonlist');
		$this->setTitle('Список лун');
	}
	$this->set('parse', $parse);
	$this->display();
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>