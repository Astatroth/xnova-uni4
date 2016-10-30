<?php

namespace Xnova;

use Xcms\db;
use Xcms\sql;

function scmp( $a, $b ) {
	 mt_srand((double)microtime()*1000000);
     return mt_rand(-1,1);
}

class gameBot
{
	/**
	 * @var user
	 */
	private $user;
	/**
	 * @var planet
	 */
	private $planet;
	/**
	 * @var string
	 */
	private $log = '';

	public function __construct($userId)
	{
		$this->user = new user();
		$this->user->load_from_id($userId);

		$this->insertLog('///// BOT UID: '.$userId.' NAME: '.$this->user->data['username'].' STARTED '.date("d.m.Y H:i:s").'');
	}

	public function getLog()
	{
		return $this->log;
	}

	private function insertLog($msg = '')
	{
		$this->log .= $msg."<br>";
	}

	public function play ()
	{
		global $resource;

		$allPlanets = db::query("SELECT * FROM game_planets WHERE `id_owner` = '".$this->user->getId()."' AND `planet_type` = '1' ORDER BY `id` ASC");

		$planetsCount = db::num_rows($allPlanets);

		$fleetColonize 		= db::first(db::query("SELECT COUNT(fleet_owner) AS `total` FROM game_fleets WHERE `fleet_owner` = '".$this->user->getId()."' AND `fleet_mission` = '7';", true));
		$maxFlyingFleets  	= db::first(db::query("SELECT COUNT(fleet_owner) AS `total` FROM game_fleets WHERE `fleet_owner` = '".$this->user->getId()."';", true));
		$maxFlottes         = $this->user->data[$resource[108]];

		while ($p = db::fetch($allPlanets))
		{
			$this->planet = new planet($p);
			$this->planet->load_user_info($this->user);

			$this->planet->CheckPlanetUsedFields();

			$this->planet->PlanetResourceUpdate();

			if ($this->planet->UpdatePlanetBatimentQueueList())
				$this->planet->PlanetResourceUpdate(time(), true);

			$this->insertLog('planet uid: '.$this->planet->data['id'].' name: '.$this->planet->data['name'].' updated');

			if ($this->planet->data["field_current"] < CalculateMaxPlanetFields($this->planet->data))
			{
				$this->insertLog('try to build stores');
				$this->buildStores();

				$r = mt_rand(0, 1);

				if ($r == 0 || $this->planet->data[$resource[4]] <= 5)
					$this->buildProductions();
				else
					$this->buildBuilds();

				if ($this->planet->data[$resource[31]] > 0)
					$this->researchTech();

				$r = mt_rand(0, 2);

				if ($r == 0)
					$this->buildDefence();
				elseif ($r == 1)
					$this->buildFleet();
			}

			if ($planetsCount < MAX_PLAYER_PLANETS and $fleetColonize < (MAX_PLAYER_PLANETS - $fleetColonize) and $maxFlyingFleets < $maxFlottes and $this->planet->data[$resource[208]] >= 1)
				$this->colonize($planetsCount);
		}

		sql::build()->update('game_bots_users')->setField('last_update', time())->where('user_id', '=', $this->user->getId())->execute();
		sql::build()->update('game_users')->setField('onlinetime', time())->where('id', '=', $this->user->getId())->execute();
	}

	private function buildProductions()
	{
		$allowBuild = array(1, 2, 3);

		$this->insertLog('try to build productions');

		$queueManager = new queueManager($this->planet->data['queue']);
		$queueManager->setUserObject($this->user);
		$queueManager->setPlanetObject($this->planet);

		$ActualCount = $queueManager->getCount($queueManager::QUEUE_TYPE_BUILDING);

		$MaxBuidSize = MAX_BUILDING_QUEUE_SIZE;
		if ($this->user->data['rpg_constructeur'] > time())
			$MaxBuidSize += 2;

		if ($this->planet->data['production_level'] < 100)
			$elementId = 4;
		else
			$elementId = $allowBuild[array_rand($allowBuild)];

		if ($ActualCount < $MaxBuidSize && IsElementBuyable($this->user, $this->planet->data, $elementId))
		{
			$queueManager->add($elementId);

			$this->insertLog('insert build '.$elementId.' to queue');

			unset($queueManager);
		}

		$this->planet->SetNextQueueElementOnTop();
	}

	private function buildBuilds()
	{
		$allowBuild = array(14, 15, 21, 31);

		$this->insertLog('try to build mines');

		$queueManager = new queueManager($this->planet->data['queue']);
		$queueManager->setUserObject($this->user);
		$queueManager->setPlanetObject($this->planet);

		$ActualCount = $queueManager->getCount($queueManager::QUEUE_TYPE_BUILDING);

		$MaxBuidSize = MAX_BUILDING_QUEUE_SIZE;
		if ($this->user->data['rpg_constructeur'] > time())
			$MaxBuidSize += 2;

		$elementId = $allowBuild[array_rand($allowBuild)];

		if ($ActualCount < $MaxBuidSize && IsTechnologieAccessible($this->user->data, $this->planet->data, $elementId) && IsElementBuyable($this->user, $this->planet->data, $elementId))
		{
			$queueManager->add($elementId);

			$this->insertLog('insert build '.$elementId.' to queue');

			unset($queueManager);
		}

		$this->planet->SetNextQueueElementOnTop();
	}

	private function buildStores ()
	{
		$allowBuild = array(22, 23, 24);

		$queueManager = new queueManager($this->planet->data['queue']);
		$queueManager->setUserObject($this->user);
		$queueManager->setPlanetObject($this->planet);

		$ActualCount = $queueManager->getCount($queueManager::QUEUE_TYPE_BUILDING);

		$MaxBuidSize = MAX_BUILDING_QUEUE_SIZE;
		if ($this->user->data['rpg_constructeur'] > time())
			$MaxBuidSize += 2;

		foreach ($allowBuild AS $elementId)
		{
			if ($elementId == 22)
			{
				if ($ActualCount < $MaxBuidSize && $this->planet->data['metal'] >= $this->planet->data['metal_max'] && IsElementBuyable($this->user, $this->planet->data, $elementId))
				{
					$queueManager->add($elementId);

					$this->insertLog('insert build '.$elementId.' to queue');
				}
			}
			elseif ($elementId == 23)
			{
				if ($ActualCount < $MaxBuidSize && $this->planet->data['crystal'] >= $this->planet->data['crystal_max'] && IsElementBuyable($this->user, $this->planet->data, $elementId))
				{
					$queueManager->add($elementId);

					$this->insertLog('insert build '.$elementId.' to queue');
				}
			}
			elseif ($elementId = 24)
			{
				if ($ActualCount < $MaxBuidSize && $this->planet->data['deuterium'] >= $this->planet->data['deuterium_max'] && IsElementBuyable($this->user, $this->planet->data, $elementId))
				{
					$queueManager->add($elementId);

					$this->insertLog('insert build '.$elementId.' to queue');
				}
			}
		}

		unset($queueManager);
	}

	private function researchTech()
	{
		global $reslist, $resource;

		$this->insertLog('try to research');

		if (CheckLabSettingsInQueue($this->planet->data) == true)
		{
			$techLevel = $reslist['tech'];
			shuffle($techLevel);

			foreach ($techLevel as $elementId)
			{
				if ($this->user->data["b_tech_planet"] == 0 && IsTechnologieAccessible($this->user->data, $this->planet->data, $elementId) && IsElementBuyable($this->user, $this->planet->data, $elementId) && !(isset($pricelist[$elementId]['max']) && $this->user->data[$resource[$elementId]] >= $pricelist[$elementId]['max']))
				{
					$this->research($elementId);
				}
			}
		}
	}

	private function research ($elementId)
	{
		$costs = GetBuildingPrice($this->user, $this->planet->data, $elementId);

		$this->planet->data['metal']      -= $costs['metal'];
		$this->planet->data['crystal']    -= $costs['crystal'];
		$this->planet->data['deuterium']  -= $costs['deuterium'];

		$this->planet->data["b_tech_id"]   	= $elementId;
		$this->planet->data["b_tech"]      	= time() + GetBuildingTime($this->user, $this->planet->data, $elementId);
		$this->user->data["b_tech_planet"] 	= $this->planet->data["id"];

		sql::build()->update('game_planets')->set(Array
		(
			'b_tech_id'	=> $this->planet->data['b_tech_id'],
			'b_tech'	=> $this->planet->data['b_tech'],
			'metal'		=> $this->planet->data['metal'],
			'crystal'	=> $this->planet->data['crystal'],
			'deuterium'	=> $this->planet->data['deuterium']
		))
		->where('id', '=', $this->planet->data['id'])->execute();

		sql::build()->update('game_users')->setField('b_tech_planet', $this->user->data['b_tech_planet'])->where('id', '=', $this->user->data['id'])->execute();

		$this->insertLog('insert research '.$elementId.' to queue');
	}

	private function buildDefence ()
	{
		global $resource;

		$this->insertLog('try to build defence');

		$DefLevel = array(401 => 150, 402 => 150, 403 => 110 ,404 => 70,  406 => 50);

		$building = new building();
		$building->planet = $this->planet;
		$building->user = $this->user;

		foreach ($DefLevel as $elementId => $Max)
		{
			$MaxElements = GetMaxConstructibleElements($elementId, $this->planet->data, $this->user);

			$Count = $MaxElements;
			if ($Count > ($Max * $this->planet->data[$resource[21]]))
				$Count = ($Max * $this->planet->data[$resource[21]]);

			$Value = (1 + pow(10, 2) - pow($this->planet->data[$resource[21]], 2));

			if ($Value > 0)
				$Count = ceil($Count / $Value);
			else
				$Count = ceil($Count * $Value);

			if (IsTechnologieAccessible($this->user->data, $this->planet->data, $elementId) && IsElementBuyable($this->user, $this->planet->data, $elementId))
				$this->hangarBuild($elementId, $Count);
		}
	}

	private function buildFleet()
	{
		$this->insertLog('try to build defence');

		global $resource;

		$FleetLevel =  array(212 => 300, 215 => 150, 214 => 50, 211 => 200, 207 => 500, 209 => 500, 202 => 200,203 => 150, 204 => 345, 205 => 100, 206 => 30, 208 => 1, 210 => 20, 213 => 100);
		uasort( $FleetLevel, 'scmp' );

		$building = new building();
		$building->planet = $this->planet;
		$building->user = $this->user;

		foreach ($FleetLevel as $elementId => $Max)
		{
			if ($elementId == 0)
				continue;

			if ($elementId == 212)
				continue;

			$MaxElements = GetMaxConstructibleElements($elementId, $this->planet->data, $this->user);

			$Count = $MaxElements;
			if ($Count > ($Max * $this->planet->data[$resource[21]]))
				$Count = ($Max * $this->planet->data[$resource[21]]);

			$Value = (1 + pow(10, 2) - pow($this->planet->data[$resource[21]], 2));

			if ($Value > 0)
				$Count = ceil($Count / $Value);
			else
				$Count = ceil($Count * $Value);

			if (IsTechnologieAccessible($this->user->data, $this->planet->data, $elementId) && IsElementBuyable($this->user, $this->planet->data, $elementId))
				$this->HangarBuild($elementId, $Count);
		}
	}

	private function hangarBuild ($elementId, $Count = 1)
	{
		if ($Count > 0)
		{
			$this->insertLog('insert to hangar queue '.$elementId.':'.$Count.'');

			$Ressource = GetElementRessources($elementId, $Count, $this->user);

			$this->planet->data['metal'] 		-= $Ressource['metal'];
			$this->planet->data['crystal'] 		-= $Ressource['crystal'];
			$this->planet->data['deuterium'] 	-= $Ressource['deuterium'];
			$this->planet->data['b_hangar_id'] 	.= $elementId . "," . $Count . ";";

			sql::build()->update('game_planets')->set(Array
			(
				'metal' 		=> $this->planet->data['metal'],
				'crystal' 		=> $this->planet->data['crystal'],
				'deuterium' 	=> $this->planet->data['deuterium'],
				'b_hangar_id' 	=> $this->planet->data['b_hangar_id']
			))
			->where('id', '=', $this->planet->data['id'])->execute();
		}
	}

	private function colonize ($allPlanets = 1)
	{
		global $resource;

		if ($allPlanets >= 4)
		{
			$planet = mt_rand(1, MAX_PLANET_IN_SYSTEM);
			$system = mt_rand(1, MAX_SYSTEM_IN_GALAXY);
			$galaxy = mt_rand(1, MAX_GALAXY_IN_WORLD);
		}
		else
		{
			$planet = mt_rand(1, MAX_PLANET_IN_SYSTEM);
			$system = mt_rand(($this->planet->data['system'] - 10), ($this->planet->data['system'] + 10));
			$galaxy = $this->planet->data['galaxy'];
		}

		if (system::isPositionFree($galaxy, $system, $planet))
		{
			$fleetarray = array(208 => 1);

			$MaxFleetSpeed  = min(GetFleetMaxSpeed($fleetarray, 0, $this->user));

			$distance      = GetTargetDistance($this->planet->data['galaxy'], $galaxy, $this->planet->data['system'], $system, $this->planet->data['planet'], $planet);
			$duration      = GetMissionDuration(10, $MaxFleetSpeed, $distance, GetGameSpeedFactor());
			$consumption   = GetFleetConsumption($fleetarray, GetGameSpeedFactor(), $duration, $distance, $this->user);

			$ShipArray 			 = '';
			$FleetSubQRY         = "";

			foreach ($fleetarray as $Ship => $Count)
			{
				$ShipArray     .= $Ship .",". $Count ."!0;";
				$FleetSubQRY   .= "`".$resource[$Ship] . "` = `" . $resource[$Ship] . "` - " . $Count . " , ";
			}

			$QryInsertFleet  = "INSERT INTO game_fleets SET ";
			$QryInsertFleet .= "`fleet_owner` = '". $this->user->getId() ."', ";
			$QryInsertFleet .= "`fleet_owner_name` = '" . $this->planet->data['name'] . "', ";
			$QryInsertFleet .= "`fleet_mission` = '7', ";
			$QryInsertFleet .= "`fleet_array` = '". $ShipArray ."', ";
			$QryInsertFleet .= "`fleet_start_time` = '". ($duration + time()) ."', ";
			$QryInsertFleet .= "`fleet_start_galaxy` = '". $this->planet->data['galaxy'] ."', ";
			$QryInsertFleet .= "`fleet_start_system` = '". $this->planet->data['system'] ."', ";
			$QryInsertFleet .= "`fleet_start_planet` = '". $this->planet->data['planet'] ."', ";
			$QryInsertFleet .= "`fleet_start_type` = '". $this->planet->data['planet_type'] ."', ";
			$QryInsertFleet .= "`fleet_end_time` = '". (($duration * 2) + time()) ."', ";
			$QryInsertFleet .= "`fleet_end_galaxy` = '". $galaxy ."', ";
			$QryInsertFleet .= "`fleet_end_system` = '". $system ."', ";
			$QryInsertFleet .= "`fleet_end_planet` = '". $planet ."', ";
			$QryInsertFleet .= "`fleet_end_type` = '1', ";
			$QryInsertFleet .= "`fleet_target_owner` = '0', ";
			$QryInsertFleet .= "`start_time` = '". time() ."', ";
			$QryInsertFleet .= "`fleet_time` = '" . ($duration + time()) . "';";

			$this->insertLog('try to colonize planet '.$galaxy.':'.$system.':'.$planet.'');

			db::query( $QryInsertFleet);

			db::query("UPDATE game_planets SET " . $FleetSubQRY . " deuterium = deuterium - " . $consumption . " WHERE `id` = '" . $this->planet->data['id'] . "'");

			$this->planet->data["deuterium"]  -= $consumption;
		}
		else
			$this->insertLog('try to colonize planet '.$galaxy.':'.$system.':'.$planet.'. position not free');
	}
}
 
?>