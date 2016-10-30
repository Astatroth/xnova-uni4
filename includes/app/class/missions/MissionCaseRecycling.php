<?php

namespace Xnova\missions;

use Xcms\db;
use Xcms\strings;
use Xnova\User;
use Xnova\fleet_engine;

class MissionCaseRecycling extends fleet_engine implements Mission
{
	function __construct($Fleet)
	{
			$this->_fleet = $Fleet;
	}

	public function TargetEvent()
	{
		global $CombatCaps;
		
		$TargetGalaxy = db::query("SELECT id, debris_metal, debris_crystal FROM game_planets WHERE `galaxy` = '" . $this->_fleet['fleet_end_galaxy'] . "' AND `system` = '" . $this->_fleet['fleet_end_system'] . "' AND `planet` = '" . $this->_fleet['fleet_end_planet'] . "' AND `planet_type` != 3 LIMIT 1;", true);

		$RecyclerCapacity = 0;
		$OtherFleetCapacity = 0;

		$fleetData = unserializeFleet($this->_fleet['fleet_array']);

		foreach ($fleetData as $shipId => $shipArr)
		{
			if (isset($shipArr['lvl']) && $shipArr['lvl'] > 0 && isset($CombatCaps[$shipId]["power_consumption"]) && $CombatCaps[$shipId]["power_consumption"] > 0)
				$capacity = round($CombatCaps[$shipId]["capacity"] * (1 + $shipArr['lvl'] * ($CombatCaps[$shipId]["power_consumption"] / 100))) * $shipArr['cnt'];
			else
				$capacity = $CombatCaps[$shipId]["capacity"] * $shipArr['cnt'];

			if ($shipId == 209)
				$RecyclerCapacity += $capacity;
			else
				$OtherFleetCapacity += $capacity;
		}

		$IncomingFleetGoods = $this->_fleet["fleet_resource_metal"] + $this->_fleet["fleet_resource_crystal"] + $this->_fleet["fleet_resource_deuterium"];

		// Если часть ресурсов хранится в переработчиках
		if ($IncomingFleetGoods > $OtherFleetCapacity)
			$RecyclerCapacity -= ($IncomingFleetGoods - $OtherFleetCapacity);

		if (($TargetGalaxy["debris_metal"] + $TargetGalaxy["debris_crystal"]) <= $RecyclerCapacity)
		{
			$RecycledGoods["metal"] = $TargetGalaxy["debris_metal"];
			$RecycledGoods["crystal"] = $TargetGalaxy["debris_crystal"];
		}
		else
		{
			if (($TargetGalaxy["debris_metal"] > $RecyclerCapacity / 2) AND ($TargetGalaxy["debris_crystal"] > $RecyclerCapacity / 2))
			{
				$RecycledGoods["metal"] = $RecyclerCapacity / 2;
				$RecycledGoods["crystal"] = $RecyclerCapacity / 2;
			}
			else
			{
				if ($TargetGalaxy["debris_metal"] > $TargetGalaxy["debris_crystal"])
				{
					$RecycledGoods["crystal"] = $TargetGalaxy["debris_crystal"];

					if ($TargetGalaxy["debris_metal"] > ($RecyclerCapacity - $RecycledGoods["crystal"]))
						$RecycledGoods["metal"] = $RecyclerCapacity - $RecycledGoods["crystal"];
					else
						$RecycledGoods["metal"] = $TargetGalaxy["debris_metal"];
				}
				else
				{
					$RecycledGoods["metal"] = $TargetGalaxy["debris_metal"];

					if ($TargetGalaxy["debris_crystal"] > ($RecyclerCapacity - $RecycledGoods["metal"]))
						$RecycledGoods["crystal"] = $RecyclerCapacity - $RecycledGoods["metal"];
					else
						$RecycledGoods["crystal"] = $TargetGalaxy["debris_crystal"];
				}
			}
		}

		db::query("UPDATE game_planets SET `debris_metal` = `debris_metal` - '" . $RecycledGoods["metal"] . "', `debris_crystal` = `debris_crystal` - '" . $RecycledGoods["crystal"] . "' WHERE `id` = '" . $TargetGalaxy['id'] . "' LIMIT 1;");

		$this->ReturnFleet(array('+fleet_resource_metal' => $RecycledGoods["metal"], '+fleet_resource_crystal' => $RecycledGoods["crystal"]));

		$Message = sprintf(_getText('sys_recy_gotten'),
						strings::pretty_number($RecycledGoods["metal"]), _getText('Metal'),
						strings::pretty_number($RecycledGoods["crystal"]), _getText('Crystal'),
						GetTargetAdressLink($this->_fleet));

		user::get()->sendMessage($this->_fleet['fleet_owner'], 0, $this->_fleet['fleet_start_time'], 4, _getText('sys_mess_spy_control'), $Message);
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