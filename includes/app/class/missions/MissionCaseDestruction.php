<?php

namespace Xnova\missions;

use Xcms\cache;
use Xcms\db;
use Xcms\sql;
use Xnova\User;
use Xnova\fleet_engine;

class MissionCaseDestruction extends fleet_engine implements Mission
{
	function __construct($Fleet)
	{
			$this->_fleet = $Fleet;
	}

	public function TargetEvent()
	{
		// Проводим бой	
		if (!class_exists('MissionCaseAttack'))
			require_once(ROOT_DIR.APP_PATH.'class/missions/MissionCaseAttack.php');

		$mission = new MissionCaseAttack($this->_fleet);
		$result = $mission->TargetEvent();

		if ($result == true)
		{
			$checkFleet = db::query("SELECT fleet_array, won FROM game_fleets WHERE fleet_id = " . $this->_fleet['fleet_id'] . ";", true);

			if (isset($checkFleet['fleet_array']) && $checkFleet['won'] == 1)
			{
				$this->_fleet['fleet_array'] = $checkFleet['fleet_array'];
				$this->_fleet['won'] = $checkFleet['won'];

				$ripsKilled = false;
				$moonDestroyed = false;

				$Rips = 0;

				$fleetData = unserializeFleet($this->_fleet['fleet_array']);

				if (isset($fleetData[214]))
					$Rips = $fleetData[214]['cnt'];

				if ($Rips > 0)
				{
					$TargetMoon = db::query("SELECT id, id_owner, diameter FROM game_planets WHERE `galaxy` = '" . $this->_fleet['fleet_end_galaxy'] . "' AND `system` = '" . $this->_fleet['fleet_end_system'] . "' AND `planet` = '" . $this->_fleet['fleet_end_planet'] . "' AND `planet_type` = '3';", true);
					$CurrentUser = db::query("SELECT `rpg_admiral`, `rpg_ingenieur` FROM game_users WHERE `id` = '" . $this->_fleet['fleet_owner'] . "';", true);

					$moonDestroyChance = round((100 - sqrt($TargetMoon['diameter'])) * (sqrt($Rips)));

					if ($CurrentUser['rpg_admiral'] > time())
						$moonDestroyChance = $moonDestroyChance * 1.1;

					$moonDestroyChance 	= max(min(floor($moonDestroyChance), 100), 0);
					$fleetDestroyChance = (sqrt($TargetMoon['diameter'])) / 4;
					
					if ($Rips > 150)
						$fleetDestroyChance *= 0.1;
					elseif ($Rips > 100)
						$fleetDestroyChance *= 0.25;
					elseif ($Rips > 50)
						$fleetDestroyChance *= 0.5;
					elseif ($Rips > 25)
						$fleetDestroyChance *= 0.75;

					if ($CurrentUser['rpg_ingenieur'] > time())
						$fleetDestroyChance *= 0.5;

					$fleetDestroyChance = max(min(ceil($fleetDestroyChance), 100), 0);

					$randChance = mt_rand(1, 100);

					if ($randChance <= $moonDestroyChance)
					{
						$moonDestroyed = true;

						db::query("UPDATE game_planets SET destruyed = " . (time() + 60 * 60 * 24) . ", id_owner = 0 WHERE `id` = '" . $TargetMoon['id'] . "';");
						db::query("UPDATE game_users SET current_planet = id_planet WHERE id = " . $TargetMoon['id_owner'] . ";");

						db::query("UPDATE game_fleets SET fleet_start_type = 1 WHERE fleet_start_galaxy = " . $this->_fleet['fleet_end_galaxy'] . " AND fleet_start_system = " . $this->_fleet['fleet_end_system'] . " AND fleet_start_planet = " . $this->_fleet['fleet_end_planet'] . " AND fleet_start_type = 3;");
						db::query("UPDATE game_fleets SET fleet_end_type = 1 WHERE fleet_end_galaxy = " . $this->_fleet['fleet_end_galaxy'] . " AND fleet_end_system = " . $this->_fleet['fleet_end_system'] . " AND fleet_end_planet = " . $this->_fleet['fleet_end_planet'] . " AND fleet_end_type = 3;");
					}
					else
					{
						$randChance = mt_rand(1, 100);

						if ($randChance <= $fleetDestroyChance)
						{
							$ripsKilled = true;

							$this->KillFleet();

							$debree = $this->convertFleetToDebris($fleetData);

							if ($debree['metal'] > 0 && $debree['crystal'] > 0)
							{
								sql::build()->update('game_planets')->set(Array('+debris_metal' => $debree['metal'], '+debris_crystal' => $debree['crystal']))
										->where('galaxy', '=', $this->_fleet['fleet_end_galaxy'])->addAND()
										->where('system', '=', $this->_fleet['fleet_end_system'])->addAND()
										->where('planet', '=', $this->_fleet['fleet_end_planet'])->addAND()
										->where('planet_type', '!=', 3)->execute();
							}
						}
					}

					$message = _getText('sys_destruc_mess1');

					if ($moonDestroyed && !$ripsKilled)
						$message .= _getText('sys_destruc_reussi');

					if ($moonDestroyed && $ripsKilled)
						$message .= _getText('sys_destruc_all');

					if (!$moonDestroyed && $ripsKilled)
						$message .= _getText('sys_destruc_echec');

					if (!$moonDestroyed && !$ripsKilled)
						$message .= _getText('sys_destruc_null');

					$message .= "<br><br>" . _getText('sys_destruc_lune') . $moonDestroyChance . "%. <br>" . _getText('sys_destruc_rip') . $fleetDestroyChance . "%";

					user::get()->sendMessage($this->_fleet['fleet_owner'], 0, $this->_fleet['fleet_start_time'], 3, _getText('sys_mess_destruc_report'), $message);
					user::get()->sendMessage($TargetMoon['id_owner'], 0, $this->_fleet['fleet_start_time'], 3, _getText('sys_mess_destruc_report'), $message);

					cache::delete('app::planetlist_'.$TargetMoon['id_owner']);
				}
				else
					user::get()->sendMessage($this->_fleet['fleet_owner'], 0, $this->_fleet['fleet_start_time'], 3, _getText('sys_mess_destruc_report'), _getText('sys_destruc_stop'));
			}
			else
				user::get()->sendMessage($this->_fleet['fleet_owner'], 0, $this->_fleet['fleet_start_time'], 3, _getText('sys_mess_destruc_report'), _getText('sys_destruc_stop'));
		}
	}

	public function EndStayEvent()
	{
		return;
	}

	public function ReturnEvent()
	{
		$this->RestoreFleetToPlanet();
		$this->KillFleet();
	}
}

?>