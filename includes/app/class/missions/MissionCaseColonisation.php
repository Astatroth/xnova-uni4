<?php

namespace Xnova\missions;

use Xcms\cache;
use Xcms\db;
use Xnova\User;
use Xnova\fleet_engine;
use Xnova\system;

class MissionCaseColonisation extends fleet_engine implements Mission
{
	function __construct($Fleet)
	{
			$this->_fleet = $Fleet;
	}

	public function TargetEvent()
	{
		$MaxColo = db::query("SELECT `colonisation_tech` FROM game_users WHERE id={$this->_fleet['fleet_owner']}", true);
		$iMaxColo = $MaxColo['colonisation_tech'] + 1;

		if ($iMaxColo > MAX_PLAYER_PLANETS)
			$iMaxColo = MAX_PLAYER_PLANETS;

		$iPlanetCount = db::first(db::query("SELECT count(*) as num FROM game_planets WHERE `id_owner` = '" . $this->_fleet['fleet_owner'] . "' AND `planet_type` = '1'", true));

		$TargetAdress = sprintf(_getText('sys_adress_planet'), $this->_fleet['fleet_end_galaxy'], $this->_fleet['fleet_end_system'], $this->_fleet['fleet_end_planet']);

		if (system::isPositionFree($this->_fleet['fleet_end_galaxy'], $this->_fleet['fleet_end_system'], $this->_fleet['fleet_end_planet']))
		{
			if ($iPlanetCount >= $iMaxColo)
			{
				$TheMessage = _getText('sys_colo_arrival') . $TargetAdress . _getText('sys_colo_maxcolo') . $iMaxColo . _getText('sys_colo_planet');

				user::get()->sendMessage($this->_fleet['fleet_owner'], 0, $this->_fleet['fleet_start_time'], 0, _getText('sys_colo_mess_from'), $TheMessage);

				$this->ReturnFleet();
			}
			else
			{
				$NewOwnerPlanet = system::CreateOnePlanetRecord($this->_fleet['fleet_end_galaxy'], $this->_fleet['fleet_end_system'], $this->_fleet['fleet_end_planet'], $this->_fleet['fleet_owner'], _getText('sys_colo_defaultname'), false);

				if ($NewOwnerPlanet !== false)
				{
					$TheMessage = _getText('sys_colo_arrival') . $TargetAdress . _getText('sys_colo_allisok');

					user::get()->sendMessage($this->_fleet['fleet_owner'], 0, $this->_fleet['fleet_start_time'], 0, _getText('sys_colo_mess_from'), $TheMessage);

					$NewFleet = "";

					$fleetData = unserializeFleet($this->_fleet['fleet_array']);

					foreach ($fleetData as $shipId => $shipArr)
					{
						if ($shipId == 208 && $shipArr['cnt'] > 0)
							$NewFleet .= $shipId . "," . ($shipArr['cnt'] - 1) . "!0;";
						elseif ($shipArr['cnt'] > 0)
							$NewFleet .= $shipId . "," . $shipArr['cnt'] . "!;";
					}

					$this->_fleet['fleet_array'] = $NewFleet;

					$this->RestoreFleetToPlanet(false);
					$this->KillFleet();

					cache::delete('app::planetlist_'.$this->_fleet['fleet_owner']);
				}
				else
				{
					$this->ReturnFleet();

					$TheMessage = _getText('sys_colo_arrival') . $TargetAdress . _getText('sys_colo_badpos');

					user::get()->sendMessage($this->_fleet['fleet_owner'], 0, $this->_fleet['fleet_start_time'], 0, _getText('sys_colo_mess_from'), $TheMessage);
				}
			}
		}
		else
		{
			$this->ReturnFleet();

			$TheMessage = _getText('sys_colo_arrival') . $TargetAdress . _getText('sys_colo_notfree');

			user::get()->sendMessage($this->_fleet['fleet_owner'], 0, $this->_fleet['fleet_end_time'], 0, _getText('sys_colo_mess_from'), $TheMessage);
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