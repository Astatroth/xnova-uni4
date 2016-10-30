<?php

namespace Xnova\controllers;

use Xcms\core;
use Xcms\db;
use Xnova\User;
use Xnova\app;

class showMobilePage
{
	public 	$mode = '';
	private $result = array('success' => false, 'message' => '');

	public function __construct ()
	{
		error_reporting(E_ERROR);

		if (user::get()->isAuthorized())
		{
			app::loadPlanet();

			$this->result['planet'] = ShowTopNavigationBar();
		}
	}

	public function login ()
	{
		if ($_POST['emails'] != '' && $_POST['password'] != '')
		{
			$login = db::query("SELECT u.id, u.options_toggle, ui.password FROM game_users u, game_users_info ui WHERE ui.id = u.id AND ui.`email` = '" . db::escape_string($_POST['emails']) . "' LIMIT 1", true);

			if (isset($login['id']))
			{
				if ($login['password'] == md5($_POST['password']))
				{
					global $session;

					$options = user::get()->unpackOptions($login['options_toggle']);

					$this->result['id'] = $login['id'];
					$this->result['secret'] = $session->getCookiePassword($login['id'], $login['password'], $options['security']);
					$this->result['success'] = true;
				}
				else
					$this->result['message'] = 'Неверный E-mail и/или пароль';
			}
			else
				$this->result['message'] = 'Такого игрока не существует';
		}
		else
			$this->result['message'] = 'Введите хоть что-нибудь!';

		$this->show();
	}

	public function overview ()
	{
		$this->result['success'] = true;
		$this->show();
	}

	public function galaxy ()
	{
		if (!user::get()->isAuthorized())
		{
			$this->result['message'] = 'Необходима авторизация';
			$this->show();
		}

		app::loadPlanet();

		$mode = isset($_GET['r']) ? intval($_GET['r']) : 0;

		if ($mode == 0)
		{
			$galaxy = app::$planetrow->data['galaxy'];
			$system = app::$planetrow->data['system'];
		}
		else
		{
			$galaxy = intval($_GET['galaxy']);
			$system = intval($_GET['system']);
		}

		$galaxy = min(max($galaxy, 1), MAX_GALAXY_IN_WORLD);
		$system = min(max($system, 1), MAX_SYSTEM_IN_GALAXY);

		$GalaxyRow = db::query("SELECT
								p.planet, p.id AS id_planet, p.id_ally AS ally_planet, p.debris_metal AS metal, p.debris_crystal AS crystal, p.name, p.planet_type, p.destruyed, p.image, p.last_active, p.parent_planet,
								p2.id AS luna_id, p2.name AS luna_name, p2.destruyed AS luna_destruyed, p2.last_active AS luna_update, p2.diameter AS luna_diameter, p2.temp_min AS luna_temp,
								u.id AS user_id, u.username, u.race, u.ally_id, u.onlinetime,
								a.ally_name, a.ally_members, a.ally_web, a.ally_tag,
								s.total_rank, s.total_points
				FROM game_planets p
				LEFT JOIN game_planets p2 ON (p.parent_planet = p2.id AND p.parent_planet != 0)
				LEFT JOIN game_users u ON (u.id = p.id_owner AND p.id_owner != 0)
				LEFT JOIN game_alliance a ON (a.id = u.ally_id AND u.ally_id != 0)
				LEFT JOIN game_statpoints s ON (s.id_owner = u.id AND s.stat_type = '1' AND s.stat_code = '1')
				WHERE p.planet_type <> 3 AND p.`galaxy` = '" . $galaxy . "' AND p.`system` = '" . $system . "';", '');

		$rows = array();

		while ($row = db::fetch_assoc($GalaxyRow))
		{
			if ($row['last_active'] != "" && $row['luna_update'] > $row['last_active'])
				$row['last_active'] = $row['luna_update'];

			unset($row['luna_update']);

			if ($row['destruyed'] != 0 && $row["id_planet"] != '')
				app::$planetrow->checkAbandonPlanetState($row);

			if ($row["luna_id"] != "" && $row["luna_destruyed"] != 0)
				app::$planetrow->checkAbandonMoonState($row);

			$online = $row['onlinetime'];

			if ($online < (time() - 60 * 60 * 24 * 7) && $online > (time() - 60 * 60 * 24 * 28))
				$row['onlinetime'] = 1;
			elseif ($online < (time() - 60 * 60 * 24 * 28))
				$row['onlinetime'] = 2;
			else
				$row['onlinetime'] = 0;

			if ($row['last_active'] > (time() - 59 * 60))
				$row['last_active'] = floor((time() - $row['last_active']) / 60);
			else
				$row['last_active'] = 60;

			if ($row['total_rank'] == '')
				$row['total_rank'] = 0;

			$row['status'] = '';

			if ($row['banaday'] > time())
				$row['status'] = 'banned';
			elseif ($row['urlaubs_modus_time'] > 0)
				$row['status'] = 'vacation';
			elseif ($row['onlinetime'] == 1)
				$row['status'] = 'inactive';
			elseif ($row['onlinetime'] == 2)
				$row['status'] = 'longinactive';
			elseif ($row['total_points'] * core::getConfig('noobprotectionmulti', 5) < user::get()->data['total_points'] && $row['total_points'] < 50000)
				$row['status'] = 'noob';
			elseif ($row['total_points'] > user::get()->data['total_points'] * core::getConfig('noobprotectionmulti', 5) && $row['total_points'] < 50000)
				$row['status'] = 'strong';

			foreach ($row AS &$v)
				if (is_numeric($v))
					$v = intval($v);

			unset($v);

			$rows[] = $row;
		}

		$this->result['galaxy'] = $galaxy;
		$this->result['system'] = $system;

		$this->result['rows'] = $rows;

		$this->result['success'] = true;
		$this->show();
	}

	public function show()
	{
		$this->result['mode'] = $this->mode;

		echo json_encode($this->result);
		die();
	}
}
 
?>