<?php

namespace Xnova;
use Xcms\core;
use Xcms\db;
use Xcms\sql;

/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class system
{
	static function GetMissileRange ()
	{
		global $resource, $user;

		if ($user->data[$resource[117]] > 0)
		{
			$MissileRange = ($user->data[$resource[117]] * 5) - 1;
		}
		else
		{
			$MissileRange = 0;
		}

		return $MissileRange;
	}

	static function GetPhalanxRange ($PhalanxLevel)
	{
		$PhalanxRange = 0;
		if ($PhalanxLevel > 1)
		{
			for ($Level = 2; $Level < $PhalanxLevel + 1; $Level++)
			{
				$lvl = ($Level * 2) - 1;
				$PhalanxRange += $lvl;
			}
		}

		return $PhalanxRange;
	}

	static function isPositionFree ($galaxy, $system, $position, $type = false)
	{
		if (!$galaxy || !$system || !$position)
			return false;
	
		$QrySelectPlanet = "SELECT `id` FROM game_planets WHERE ";

		if ($type !== false)
			$QrySelectPlanet .= "`planet_type` = '" . $type . "' AND ";

		$QrySelectPlanet .= "`galaxy` = '" . $galaxy . "' AND `system` = '" . $system . "' AND `planet` = '" . $position . "';";

		$PlanetExist = db::query($QrySelectPlanet, true);

		return (!isset($PlanetExist['id']));
	}

	static function PlanetSizeRandomiser ($Position, $HomeWorld = false, $Base = false)
	{
		$planetData = array();
		require(ROOT_DIR.APP_PATH.'varsPlanet.php');

		$return = array();

		if ($HomeWorld)
			$return['field_max'] = core::getConfig('initial_fields', 163);
		elseif ($Base)
			$return['field_max'] = core::getConfig('initial_base_fields', 10);
		else
			$return['field_max'] = (int) floor($planetData[$Position]['fields'] * core::getConfig('planetFactor', 1));

		$return['diameter'] = (int) floor(1000 * sqrt($return['field_max']));

		$return['temp_max'] = $planetData[$Position]['temp'];
		$return['temp_min'] = $return['temp_max'] - 40;

		if ($Base)
		{
			$return['image'] = 'baseplanet01';
		}
		else
		{
			$imageNames = array_keys($planetData[$Position]['image']);
			$imageNameType = $imageNames[array_rand($imageNames)];

			$return['image']  = $imageNameType;
			$return['image'] .= 'planet';
			$return['image'] .= $planetData[$Position]['image'][$imageNameType] < 10 ? '0' : '';
			$return['image'] .= $planetData[$Position]['image'][$imageNameType];
		}

		return $return;
	}

	static function CreateOnePlanetRecord ($Galaxy, $System, $Position, $PlanetOwnerID, $PlanetName = '', $HomeWorld = false, $Base = false)
	{
		if (self::isPositionFree($Galaxy, $System, $Position))
		{
			$planet = self::PlanetSizeRandomiser($Position, $HomeWorld, $Base);

			$planet['metal'] 		= BUILD_METAL;
			$planet['crystal'] 		= BUILD_CRISTAL;
			$planet['deuterium'] 	= BUILD_DEUTERIUM;

			$planet['galaxy'] = $Galaxy;
			$planet['system'] = $System;
			$planet['planet'] = $Position;

			$planet['planet_type'] = 1;

			if ($Base)
				$planet['planet_type'] = 5;

			$planet['id_owner'] = $PlanetOwnerID;
			$planet['last_update'] = time();
			$planet['name'] = ($PlanetName == '') ? _getText('sys_colo_defaultname') : $PlanetName;

			sql::build()->insert('game_planets')->set($planet)->execute();

			$planetId = db::insert_id();

			if (isset($_SESSION['fleet_shortcut']))
				unset($_SESSION['fleet_shortcut']);

			return $planetId;
		}
		else
			return false;
	}

	static function CreateOneMoonRecord ($Galaxy, $System, $Planet, $Owner, $Chance)
	{
		$QryGetMoonPlanetData = "SELECT * FROM game_planets ";
		$QryGetMoonPlanetData .= "WHERE ";
		$QryGetMoonPlanetData .= "`galaxy` = '" . $Galaxy . "' AND ";
		$QryGetMoonPlanetData .= "`system` = '" . $System . "' AND ";
		$QryGetMoonPlanetData .= "`planet` = '" . $Planet . "' AND planet_type = 1;";
		$MoonPlanet = db::query($QryGetMoonPlanetData, true);

		if ($MoonPlanet['parent_planet'] == 0 && $MoonPlanet['id'] != 0)
		{
			$maxtemp = $MoonPlanet['temp_max'] - rand(10, 45);
			$mintemp = $MoonPlanet['temp_min'] - rand(10, 45);

			$size = floor(pow(mt_rand(10, 20) + 3 * $Chance, 0.5) * 1000);

			sql::build()->insert('game_planets')->set(Array
			(
				'name' 			=> _getText('sys_moon'),
				'id_owner' 		=> $Owner,
				'galaxy' 		=> $Galaxy,
				'system' 		=> $System,
				'planet' 		=> $Planet,
				'planet_type' 	=> 3,
				'last_update' 	=> time(),
				'image' 		=> 'mond',
				'diameter' 		=> $size,
				'field_max' 	=> 1,
				'temp_min' 		=> $maxtemp,
				'temp_max' 		=> $mintemp,
				'metal' 		=> 0,
				'crystal' 		=> 0,
				'deuterium' 	=> 0
			))
			->execute();

			$QryGetMoonId = db::insert_id();

			sql::build()->update('game_planets')->setField('parent_planet', $QryGetMoonId)->where('id', '=', $MoonPlanet['id'])->execute();

			return $QryGetMoonId;
		}
		else
			return false;
	}

	static function CreateRegPlanet ($user_id)
	{
		$Galaxy = core::getConfigFromDB('LastSettedGalaxyPos');
		$System = core::getConfigFromDB('LastSettedSystemPos');
		$Planet = core::getConfigFromDB('LastSettedPlanetPos');

		do
		{
			$free = self::getFreePositions($Galaxy, $System, round(MAX_PLANET_IN_SYSTEM * 0.2), round(MAX_PLANET_IN_SYSTEM * 0.8));

			if (count($free) > 0)
				$position = $free[array_rand($free)];
			else
				$position = 0;

            if ($position > 0 && $Planet < core::getConfig('maxRegPlanetsInSystem', 3))
				$Planet += 1;
            else
			{
				$Planet = 1;

				if ($System >= MAX_SYSTEM_IN_GALAXY)
				{
					$System = 1;

					if ($Galaxy >= MAX_GALAXY_IN_WORLD)
						$Galaxy = 1;
					else
						$Galaxy += 1;
				}
				else
					$System += 1;
            }
		}
		while (self::isPositionFree($Galaxy, $System, $position) === false);

		if (system::CreateOnePlanetRecord($Galaxy, $System, $position, $user_id, _getText('sys_plnt_defaultname'), true) !== false)
		{
			core::updateConfig('LastSettedGalaxyPos', $Galaxy);
			core::updateConfig('LastSettedSystemPos', $System);
			core::updateConfig('LastSettedPlanetPos', $Planet);

			core::clearConfig();

			$PlanetID = db::first(db::query("SELECT `id` FROM game_planets WHERE `id_owner` = '" . $user_id . "' LIMIT 1;", true));

			sql::build()->update('game_users')->set(Array
			(
				'id_planet'		 => $PlanetID,
				'current_planet' => $PlanetID,
				'galaxy'		 => $Galaxy,
				'system'		 => $System,
				'planet'		 => $position
			))
			->where('id', '=', $user_id)->execute();

			return $PlanetID;
		}
		else
			return false;
	}

	static function getFreePositions ($galaxy, $system, $start = 1, $end = MAX_PLANET_IN_SYSTEM)
	{
		$search = db::extractResult(db::query("SELECT id, planet FROM game_planets WHERE galaxy = '".$galaxy."' AND system = '".$system."' AND planet >= '".$start."' AND planet <= '".$end."'"), 'planet');

		$result = array();

		for ($i = $start; $i <= $end; $i++)
		{
			if (!isset($search[$i]))
				$result[] = $i;
		}

		return $result;
	}

	static function getResourceSpeed ()
	{
		return core::getConfig('resource_multiplier', 1);
	}
	
	static function getGameSpeed ()
	{
		return round(core::getConfig('game_speed', 2500) / 2500, 1);
	}

	static function startOfDay ($timestamp = 0)
	{
		if (!$timestamp)
			$timestamp = time();

		return mktime(0, 0, 0, date("n", $timestamp), date("j", $timestamp), date("Y", $timestamp));
	}

	static function endOfDay ($timestamp = 0)
	{
		if (!$timestamp)
			$timestamp = time();

		return mktime(23, 59, 59, date("n", $timestamp), date("j", $timestamp), date("Y", $timestamp));
	}
}

?>