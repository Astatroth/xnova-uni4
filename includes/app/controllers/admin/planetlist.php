<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xcms\request;
use Xcms\strings;
use Xnova\system;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= 2)
{
	$action = request::G('action', '');

	if ($action == 'add')
	{
		if (isset($_POST['user']))
		{
			$Galaxy = request::P('galaxy', VALUE_INT, 0);
			$System = request::P('system', VALUE_INT, 0);
			$Planet = request::P('planet', VALUE_INT, 0);
			$UserId = request::P('user', VALUE_INT, 0);

			if ($Galaxy > MAX_GALAXY_IN_WORLD || $Galaxy < 1)
				$this->message('Ошибочная галактика!');
			if ($System > MAX_SYSTEM_IN_GALAXY || $System < 1)
				$this->message('Ошибочная система!');
			if ($Planet > MAX_PLANET_IN_SYSTEM || $Planet < 1)
				$this->message('Ошибочная планета!');

			$check = db::query("SELECT id FROM game_users WHERE id = " . $UserId . "", true);

			if (!isset($check['id']))
				$this->message('Пользователя не существует');

			$planet = system::CreateOnePlanetRecord($Galaxy, $System, $Planet, $UserId, _getText('sys_colo_defaultname'), false);

			if ($planet !== false)
				$this->message('ID: ' . $planet);
			else
				$this->message('Луна не создана');
		}

		$this->setTemplate('planetlist_add');
		$this->setTitle('Создание планеты');
	}
	else
	{
		$this->setTemplate('planetlist');

		$p = request::G('p', VALUE_INT, 1);
		if ($p < 1)
			$p = 1;

		$list = db::query("SELECT `id`, `name`, `galaxy`, `system`, `planet` FROM game_planets WHERE planet_type = '1' ORDER by id LIMIT " . (($p - 1) * 50) . ", 50");

		$total = db::query("SELECT COUNT(*) AS num FROM game_planets WHERE planet_type = '1'", true);

		$this->set('planetlist', db::extractResult($list));
		$this->set('all', $total['num']);

		$pagination = strings::pagination($total['num'], 50, '/admin/mode/planetlist/', $p);

		$this->set('pagination', $pagination);
		$this->setTitle('Список планет');
	}

	$this->display();
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>