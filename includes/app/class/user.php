<?php

namespace Xnova;

use Xcms\cache;
use Xcms\db;
use Xcms\sql;

/**
 * Класс пользователя
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */
class User extends \Xcms\User
{
	/**
	 * @var user
	 */
	private static $instance;
	protected $bonus = array();

	public static function get()
    {
        if (!isset(self::$instance))
		{
            $className = __CLASS__;
            self::$instance = new $className;
        }

        return self::$instance;
    }

	function __construct()
	{
		$this->addOptions(Array
		(
			'widescreen' 		=> 0,
			'bb_parser' 		=> 0,
			'ajax_navigation' 	=> 0,
			'planetlist' 		=> 0,
			'planetlistselect' 	=> 0,
			'gameactivity' 		=> 0,
			'records' 			=> 0,
			'only_available' 	=> 0
		));
	}

	public static function onBeforeUserParse (&$data)
	{
		if (!isset($data['id']))
			return false;

		$bonusArrays = array
		(
			'storage', 'metal', 'crystal', 'deuterium', 'energy', 'solar',
			'res_fleet', 'res_defence', 'res_research', 'res_building', 'res_levelup',
			'time_fleet', 'time_defence', 'time_research', 'time_building',
			'fleet_fuel', 'fleet_speed', 'queue'
		);

		$data['bonus_values'] = array();

		// Значения по умолчанию
		foreach ($bonusArrays AS $name)
		{
			$data['bonus_values'][$name] = 1;
		}

		$data['bonus_values']['queue'] = 0;

		// Расчет бонусов от офицеров
		if ($data['rpg_geologue'] > time())
		{
			$data['bonus_values']['metal'] 			+= 0.25;
			$data['bonus_values']['crystal'] 		+= 0.25;
			$data['bonus_values']['deuterium'] 		+= 0.25;
			$data['bonus_values']['storage'] 		+= 0.25;
		}
		if ($data['rpg_ingenieur'] > time())
		{
			$data['bonus_values']['energy'] 		+= 0.15;
			$data['bonus_values']['solar'] 			+= 0.15;
			$data['bonus_values']['res_defence'] 	-= 0.1;
		}
		if ($data['rpg_admiral'] > time())
		{
			$data['bonus_values']['res_fleet'] 		-= 0.1;
			$data['bonus_values']['fleet_speed'] 	+= 0.25;
		}
		if ($data['rpg_constructeur'] > time())
		{
			$data['bonus_values']['time_fleet'] 	-= 0.25;
			$data['bonus_values']['time_defence'] 	-= 0.25;
			$data['bonus_values']['time_building'] 	-= 0.25;
			$data['bonus_values']['queue'] 			+= 2;
		}
		if ($data['rpg_technocrate'] > time())
		{
			$data['bonus_values']['time_research'] 	-= 0.25;
		}
		if ($data['rpg_meta'] > time())
		{
			$data['bonus_values']['fleet_fuel'] 	-= 0.1;
		}

		// Расчет бонусов от рас
		if ($data['race'] == 1)
		{
			$data['bonus_values']['metal'] 			+= 0.15;
			$data['bonus_values']['solar'] 			+= 0.15;
			$data['bonus_values']['res_levelup'] 	-= 0.1;
			$data['bonus_values']['time_fleet'] 	-= 0.1;
		}
		elseif ($data['race'] == 2)
		{
			$data['bonus_values']['deuterium'] 		+= 0.15;
			$data['bonus_values']['solar'] 			+= 0.05;
			$data['bonus_values']['storage'] 		+= 0.2;
			$data['bonus_values']['res_fleet'] 		-= 0.1;
		}
		elseif ($data['race'] == 3)
		{
			$data['bonus_values']['metal'] 			+= 0.05;
			$data['bonus_values']['crystal'] 		+= 0.05;
			$data['bonus_values']['deuterium'] 		+= 0.05;
			$data['bonus_values']['res_defence'] 	-= 0.05;
			$data['bonus_values']['res_building'] 	-= 0.05;
			$data['bonus_values']['time_building'] 	-= 0.1;
		}
		elseif ($data['race'] == 4)
		{
			$data['bonus_values']['crystal'] 		+= 0.15;
			$data['bonus_values']['energy'] 		+= 0.05;
			$data['bonus_values']['res_research'] 	-= 0.1;
			$data['bonus_values']['fleet_speed'] 	+= 0.1;
		}

		if (false)
		{
			include_once(ROOT_DIR . APP_PATH . 'varsArtifacts.php');

			$artifacts = db::query("SELECT * FROM game_artifacts WHERE user_id = " . $data['id'] . " AND expired > 0 AND expired < " . time() . "");

			while ($artifact = db::fetch($artifacts))
			{
				/**
				 * @var $artifactsData array
				 */
				$data = $artifactsData[$artifact['element_id']];

				if (isset($data['resources']))
				{
					foreach ($data['resources'] AS $res => $lvl)
					{
						if (!isset($data['bonus_values'][$res]))
							continue;

						$factor = (($lvl[1] - $lvl[0]) / $data['level']) * $artifact['level'];

						$data['bonus_values'][$res] += round($factor / 100, 5);
					}
				}

				if (isset($data['build']))
				{
					foreach ($data['build'] AS $res => $lvl)
					{
						if (!isset($data['bonus_values']['time_' . $res]))
							continue;

						$factor = (($lvl[1] - $lvl[0]) / $data['level']) * $artifact['level'];

						$data['bonus_values']['time_' . $res] -= round($factor / 100, 5);
					}
				}

				if (isset($data['cost']))
				{
					foreach ($data['cost'] AS $res => $lvl)
					{
						if (!isset($data['bonus_values']['res_' . $res]))
							continue;

						$factor = (($lvl[1] - $lvl[0]) / $data['level']) * $artifact['level'];

						$data['bonus_values']['res_' . $res] -= round($factor / 100, 5);
					}
				}

				if (isset($data['queue']))
				{
					$factor = (($data['queue'][1] - $data['queue'][0]) / $data['level']) * $artifact['level'];

					$data['bonus_values']['queue'] += $factor;
				}

				if (isset($data['fleet']))
				{
					foreach ($data['fleet'] AS $res => $lvl)
					{
						if (!isset($data['bonus_values']['fleet_' . $res]))
							continue;

						$factor = (($lvl[1] - $lvl[0]) / $data['level']) * $artifact['level'];

						$data['bonus_values']['fleet_' . $res] -= round($factor / 100, 5);
					}
				}
			}
		}

		return true;
	}

	public static function onAfterUserParse (&$object)
	{
		foreach ($object->data['bonus_values'] AS $key => $value)
		{
			$object->bonus[$key] = $value;
		}
	}

	public static function onBeforeUserDelete ($userId)
	{
		$userInfo = db::query("SELECT id, ally_id FROM game_users WHERE id = ".intval($userId)."", true);

		if (!isset($userInfo['id']))
			return false;

		if ($userInfo['ally_id'] != 0)
		{
			$ally = db::query("SELECT * FROM game_alliance WHERE `id` = '" . $userInfo['ally_id'] . "';", true);

			if ($ally['ally_owner'] != $userId)
			{
				db::query("UPDATE game_alliance SET `ally_members` = '" . ($ally['ally_members'] - 1) . "' WHERE `id` = '" . $ally['id'] . "';");
				db::query("DELETE FROM game_alliance_members WHERE `u_id` = '" . $userId . "';");
			}
			else
			{
				db::query("UPDATE game_users SET `ally_id` = '0', `ally_name` = '' WHERE ally_id = '" . $ally['id'] . "' AND id != " . $userId . "");
				db::query("DELETE FROM game_alliance WHERE `id` = '" . $ally['id'] . "';");
				db::query("DELETE FROM game_alliance_chat WHERE ally_id = '" . $ally['id'] . "'");
				db::query("DELETE FROM game_alliance_members WHERE a_id = '" . $ally['id'] . "'");
				db::query("DELETE FROM game_alliance_requests WHERE a_id = '" . $ally['id'] . "'");
				db::query("DELETE FROM game_alliance_diplomacy WHERE a_id = '" . $ally['id'] . "' OR d_id = '" . $ally['id'] . "';");
				db::query("DELETE FROM game_statpoints WHERE `stat_type` = '2' AND `id_owner` = '" . $ally['id'] . "';");
			}
		}

		db::query("DELETE FROM game_alliance_requests WHERE `u_id` = '" . $userId . "';");
		db::query("DELETE FROM game_statpoints WHERE `stat_type` = '1' AND `id_owner` = '" . $userId . "';");
		db::query("DELETE FROM game_planets WHERE `id_owner` = '" . $userId . "';");
		db::query("DELETE FROM game_notes WHERE `owner` = '" . $userId . "';");
		db::query("DELETE FROM game_fleets WHERE `fleet_owner` = '" . $userId . "';");
		db::query("DELETE FROM game_buddy WHERE `sender` = '" . $userId . "' OR `owner` = '" . $userId . "';");
		db::query("DELETE FROM game_refs WHERE `r_id` = '" . $userId . "' OR `u_id` = '" . $userId . "';");
		db::query("DELETE FROM game_log_attack WHERE `uid` = '" . $userId . "';");
		db::query("DELETE FROM game_log_credits WHERE `uid` = '" . $userId . "';");
		db::query("DELETE FROM game_log_email WHERE `user_id` = '" . $userId . "';");
		db::query("DELETE FROM game_log_history WHERE `user_id` = '" . $userId . "';");
		db::query("DELETE FROM game_log_transfers WHERE `user_id` = '" . $userId . "';");
		db::query("DELETE FROM game_log_username WHERE `user_id` = '" . $userId . "';");
		db::query("DELETE FROM game_log_stats WHERE `id` = '" . $userId . "' AND type = 1;");
		db::query("DELETE FROM game_logs WHERE `s_id` = '" . $userId . "' OR `e_id` = '" . $userId . "';");
		db::query("DELETE FROM game_users_auth WHERE `user_id` = '" . $userId . "';");

		return true;
	}

	public function bonusValue ($key)
	{
		return (isset($this->bonus[$key]) ? $this->bonus[$key] : 1);
	}

	function getRankId ($lvl)
	{
		if ($lvl == 1)
			$lvl = 0;

		if ($lvl <= 80)
			return (ceil($lvl / 4) + 1);
		else
			return 22;
	}

	public function getAllyInfo ()
	{
		$this->data['ally'] = array();

		if ($this->data['ally_id'] > 0)
		{
			$ally = cache::get('user::ally_' . $this->data['id'] . '_' . $this->data['ally_id']);

			if ($ally === false)
			{
				$ally = db::query("SELECT a.id, a.ally_owner, a.ally_name, a.ally_ranks, m.rank FROM game_alliance a, game_alliance_members m WHERE m.a_id = a.id AND m.u_id = " . $this->data['id'] . " AND a.id = " . $this->data['ally_id'] . "", true);

				cache::set('user::ally_' . $this->data['id'] . '_' . $this->data['ally_id'], $ally, 300);
			}

			if (isset($ally['id']))
			{
				if (!$ally['ally_ranks'])
					$ally['ally_ranks'] = 'a:0:{}';

				$ally_ranks = json_decode($ally['ally_ranks'], true);

				$this->data['ally'] = $ally;
				$this->data['ally']['rights'] = isset($ally_ranks[$ally['rank'] - 1]) ? $ally_ranks[$ally['rank'] - 1] : array('name' => '', 'planet' => 0);
			}
		}
	}

	public function getUserPlanets ($userId, $moons = true, $allyId = 0)
	{
		if (!$userId)
			return array();

		$qryPlanets = "SELECT `id`, `name`, `image`, `galaxy`, `system`, `planet`, `planet_type`, `destruyed` FROM game_planets WHERE `id_owner` = '" . $userId . "' ";

		$qryPlanets .= ($allyId > 0 ? " OR id_ally = '".$allyId."'" : "");

		if (!$moons)
			$qryPlanets .= " AND planet_type != 3 ";

		$qryPlanets .= $this->getPlanetListSortQuery();

		return db::extractResult(db::query($qryPlanets));
	}

	public function getPlanetListSortQuery ($sort = false, $order = false)
	{
		if ($this->isAuthorized())
		{
			if (!$sort)
				$sort 	= $this->data['planet_sort'];
			if (!$order)
				$order 	= $this->data['planet_sort_order'];
		}

		$qryPlanets = ' ORDER BY ';

		switch ($sort)
		{
			case 1:
				$qryPlanets .= "`galaxy`, `system`, `planet`, `planet_type` ";
				break;
			case 2:
				$qryPlanets .= "`name` ";
				break;
			case 3:
				$qryPlanets .= "`planet_type` ";
				break;
			default:
				$qryPlanets .= "`id` ";
		}

		$qryPlanets .= ($order == 1) ? "DESC" : "ASC";

		return $qryPlanets;
	}

	public function setSelectedPlanet ()
	{
		if (isset($_GET['cp']) && is_numeric($_GET['cp']) && isset($_GET['re']) && intval($_GET['re']) == 0)
		{
			$selectPlanet = intval($_GET['cp']);

			if ($this->data['current_planet'] == $selectPlanet)
				return true;

			$IsPlanetMine = db::query("SELECT `id`, `id_owner`, `id_ally` FROM game_planets WHERE `id` = '" . $selectPlanet . "' AND (`id_owner` = '" . $this->getId() . "' OR (`id_ally` > 0 AND `id_ally` = '".$this->data['ally_id']."'));", true);

			if (isset($IsPlanetMine['id']))
			{
				if ($IsPlanetMine['id_ally'] > 0 && $IsPlanetMine['id_owner'] != $this->getId() && !$this->data['ally']['rights']['planet'])
				{
					message("Вы не можете переключится на эту планету. Недостаточно прав.", "Альянс", "?set=overview", 2);
				}

				$this->data['current_planet'] = $selectPlanet;

				sql::build()->update('game_users')->setField('current_planet', $this->data['current_planet'])->where('id', '=', $this->getId())->execute();
			}
			else
				return false;
		}

		return true;
	}
}

?>