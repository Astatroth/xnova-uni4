<?php

namespace Xnova;
use Xcms\core;
use Xcms\db;
use Xcms\sql;
use Xcms\strings;

/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class fleet_engine
{
	public $_fleet = array();

	public function KillFleet ($fleetId = false)
	{
		if (!$fleetId)
			$fleetId = $this->_fleet['fleet_id'];

		db::query("DELETE FROM game_fleets WHERE `fleet_id` = ".$fleetId);
	}

	public function RestoreFleetToPlanet ($Start = true, $fleet = true)
	{
		global $resource;

		if (!isset($this->_fleet["fleet_id"]))
			return;

		if ($fleet)
		{
			if ($Start && $this->_fleet['fleet_start_type'] == 3)
			{
				$CheckFleet = db::query("SELECT destruyed FROM game_planets WHERE `galaxy` = '" . $this->_fleet['fleet_start_galaxy'] . "' AND `system` = '" . $this->_fleet['fleet_start_system'] . "' AND `planet` = '" . $this->_fleet['fleet_start_planet'] . "' AND `planet_type` = '" . $this->_fleet['fleet_start_type'] . "'", true);

				if ($CheckFleet['destruyed'] != 0)
					$this->_fleet['fleet_start_type'] = 1;
			}
			elseif ($this->_fleet['fleet_end_type'] == 3)
			{
				$CheckFleet = db::query("SELECT destruyed FROM game_planets WHERE `galaxy` = '" . $this->_fleet['fleet_end_galaxy'] . "' AND `system` = '" . $this->_fleet['fleet_end_system'] . "' AND `planet` = '" . $this->_fleet['fleet_end_planet'] . "' AND `planet_type` = '" . $this->_fleet['fleet_end_type'] . "'", true);

				if ($CheckFleet['destruyed'] != 0)
					$this->_fleet['fleet_end_type'] = 1;
			}
		}

		if ($Start)
			$p = 'start';
		else
			$p = 'end';

		$TargetPlanet = new planet();
		$TargetPlanet->load_from_coords($this->_fleet['fleet_'.$p.'_galaxy'], $this->_fleet['fleet_'.$p.'_system'], $this->_fleet['fleet_'.$p.'_planet'], $this->_fleet['fleet_'.$p.'_type']);

		if (isset($TargetPlanet->data['id']) && $TargetPlanet->data['id_owner'] > 0)
		{
			$TargetUser = new user;
			$TargetUser->load_from_id($TargetPlanet->data['id_owner']);

			if (isset($TargetUser->data['id']))
			{
				$TargetPlanet->load_user_info($TargetUser);
				$TargetPlanet->PlanetResourceUpdate(time());
			}
		}

		sql::build()->update('game_planets');

		if ($fleet)
		{
			$fleetData = unserializeFleet($this->_fleet['fleet_array']);

			foreach ($fleetData as $shipId => $shipArr)
			{
				if ($shipArr['cnt'] > 0)
					sql::build()->setField('+'.$resource[$shipId], $shipArr['cnt']);
			}
		}

		sql::build()->set(Array
		(
			'+metal' 		=> $this->_fleet['fleet_resource_metal'],
			'+crystal' 		=> $this->_fleet['fleet_resource_crystal'],
			'+deuterium' 	=> $this->_fleet['fleet_resource_deuterium']
		));

		sql::build()->where('galaxy', '=', $this->_fleet['fleet_'.$p.'_galaxy'])->addAND()
					->where('system', '=', $this->_fleet['fleet_'.$p.'_system'])->addAND()
					->where('planet', '=', $this->_fleet['fleet_'.$p.'_planet'])->addAND()
					->where('planet_type', '=', $this->_fleet['fleet_'.$p.'_type']);

		sql::build()->execute();
	}

	public function StoreGoodsToPlanet ($Start = true)
	{
		if (!isset($this->_fleet["fleet_id"]))
			return;

		sql::build()->update('game_planets')->set(Array
		(
			'+metal' 		=> $this->_fleet['fleet_resource_metal'],
			'+crystal' 		=> $this->_fleet['fleet_resource_crystal'],
			'+deuterium' 	=> $this->_fleet['fleet_resource_deuterium']
		));

		if ($Start)
			$p = 'start';
		else
			$p = 'end';

		sql::build()->where('galaxy', '=', $this->_fleet['fleet_'.$p.'_galaxy'])->addAND()
					->where('system', '=', $this->_fleet['fleet_'.$p.'_system'])->addAND()
					->where('planet', '=', $this->_fleet['fleet_'.$p.'_planet'])->addAND()
					->where('planet_type', '=', $this->_fleet['fleet_'.$p.'_type']);

		sql::build()->execute();
	}

	public function SpyTarget ($TargetPlanet, $Mode, $TitleString)
	{
		global $resource;

		$LookAtLoop = true;
		$String = '';
		$Loops = 0;
		$ResFrom = array();
		$ResTo = array();

		if ($Mode == 0)
		{
			$t = time().''.mt_rand(1, 100);

			$String .= "<table width=\"100%\"><tr><td class=\"c\" colspan=\"4\">";
			$String .= $TitleString . " " . $TargetPlanet['name'];
			$String .= " <a href=\"?set=galaxy&r=3&galaxy=" . $TargetPlanet["galaxy"] . "&system=" . $TargetPlanet["system"] . "\">";
			$String .= "[" . $TargetPlanet["galaxy"] . ":" . $TargetPlanet["system"] . ":" . $TargetPlanet["planet"] . "]</a>";
			$String .= "<br>на <span id='d".$t."'></span><script>$('#d".$t."').html(print_date(" . time() . ", 1));</script></td>";
			$String .= "</tr><tr>";
			$String .= "<th width=220>металла:</th><th width=220 align=right>" . strings::pretty_number($TargetPlanet['metal']) . "</th>";
			$String .= "<th width=220>кристалла:</th><th width=220 align=right>" . strings::pretty_number($TargetPlanet['crystal']) . "</th>";
			$String .= "</tr><tr>";
			$String .= "<th width=220>дейтерия:</th><th width=220 align=right>" . strings::pretty_number($TargetPlanet['deuterium']) . "</th>";
			$String .= "<th width=220>энергии:</th><th width=220 align=right>" . strings::pretty_number($TargetPlanet['energy_max']) . "</th>";
			$String .= "</tr>";
			$LookAtLoop = false;
		}
		elseif ($Mode == 1)
		{
			$ResFrom[0] = 200;
			$ResTo[0] = 299;
			$Loops = 1;
		}
		elseif ($Mode == 2)
		{
			$ResFrom[0] = 400;
			$ResTo[0] = 499;
			$ResFrom[1] = 500;
			$ResTo[1] = 599;
			$Loops = 2;
		}
		elseif ($Mode == 3)
		{
			$ResFrom[0] = 1;
			$ResTo[0] = 99;
			$Loops = 1;
		}
		elseif ($Mode == 4)
		{
			$ResFrom[0] = 100;
			$ResTo[0] = 199;
			$Loops = 1;
		}
		elseif ($Mode == 5)
		{
			$ResFrom[0] = 300;
			$ResTo[0] = 375;
			$Loops = 1;
		}
		elseif ($Mode == 6)
		{
			$ResFrom[0] = 600;
			$ResTo[0] = 607;
			$Loops = 1;
		}

		if ($LookAtLoop == true)
		{
			$String = "<table width=\"100%\" cellspacing=\"1\"><tr><td class=\"c\" colspan=\"" . ((2 * SPY_REPORT_ROW) + (SPY_REPORT_ROW - 2)) . "\">" . $TitleString . "</td></tr>";
			$Count = 0;
			$CurrentLook = 0;
			while ($CurrentLook < $Loops)
			{
				$row = 0;
				for ($Item = $ResFrom[$CurrentLook]; $Item <= $ResTo[$CurrentLook]; $Item++)
				{
					if (isset($resource[$Item]) && (($TargetPlanet[$resource[$Item]] > 0 && $Item < 600) || ($TargetPlanet[$resource[$Item]] > time() && $Item > 600)))
					{
						if ($row == 0)
						{
							$String .= "<tr>";
						}
						$String .= "<th width=40%>" . _getText('tech', $Item) . "</th><th width=10%>" . (($Item < 600) ? $TargetPlanet[$resource[$Item]] : '+') . "</th>";

						$Count += $TargetPlanet[$resource[$Item]];
						$row++;
						if ($row == SPY_REPORT_ROW)
						{
							$String .= "</tr>";
							$row = 0;
						}
					}
				}

				while ($row != 0)
				{
					$String .= "<th width=40%>&nbsp;</th><th width=10%>&nbsp;</th>";
					$row++;
					if ($row == SPY_REPORT_ROW)
					{
						$String .= "</tr>";
						$row = 0;
					}
				}
				$CurrentLook++;
			}

			if ($Count == 0)
			{
				$String .= "<tr><th>нет данных</th></tr>";
			}
		}
		else
			$Count = 0;

		$String .= "</table>";

		$return['String'] = $String;
		$return['Count'] = $Count;

		return $return;
	}

	public function ReturnFleet ($update = array(), $fleetId = false)
	{
		$update['fleet_mess'] = 1;
		$update['fleet_time'] = $this->_fleet['fleet_end_time'];

		sql::build()->update('game_fleets')->set($update);

		if (!$fleetId)
			sql::build()->where('fleet_id', '=', $this->_fleet['fleet_id'])->execute();
		else
			sql::build()->where('fleet_id', '=', $fleetId)->execute();

		if ($this->_fleet['fleet_group'] != 0)
		{
			db::query("DELETE FROM game_aks WHERE id = " . $this->_fleet['fleet_group'] . ";");
			db::query("DELETE FROM game_aks_user WHERE aks_id = " . $this->_fleet['fleet_group'] . ";");
		}
	}

	public function StayFleet ($update = array())
	{
		$update['fleet_mess'] = 3;
		$update['fleet_time'] = $this->_fleet['fleet_end_stay'];

		sql::build()->update('game_fleets')->set($update)->where('fleet_id', '=', $this->_fleet['fleet_id'])->execute();
	}

	public function convertFleetToDebris ($fleet)
	{
		global $pricelist;

		$debris = array('metal' => 0, 'crystal' => 0);

		foreach ($fleet AS $fleetId => $fleetData)
		{
			$res = $pricelist[$fleetId];

			$debris['metal'] 	+= floor($fleetData['cnt'] * $res['metal'] * core::getConfig('fleetDebrisRate', 0));
			$debris['crystal'] 	+= floor($fleetData['cnt'] * $res['crystal'] * core::getConfig('fleetDebrisRate', 0));
		}

		return $debris;
	}
}

?>