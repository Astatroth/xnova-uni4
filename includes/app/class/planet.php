<?php

namespace Xnova;

use Xcms\Core;
use Xcms\Db;
use Xcms\Sql;
use Xcms\strings;

/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class planet
{
	/**
	 * @var \Xnova\User $user
	 */
	private $user;
	public $data;

	function __construct($planet = null)
	{
		if (!is_null($planet))
		{
			if (is_numeric($planet))
				$this->load_from_id($planet);
			elseif (is_array($planet))
				$this->load_from_array($planet);
		}
	}

	public function __get($key)
	{
		return $this->__isset($key) ? $this->data[$key] : null;
	}

	public function __isset($key)
	{
		return isset($this->data[$key]);
	}

	public function getById ($planetId)
	{
		$data = db::query("SELECT * FROM game_planets WHERE `id` = ".intval($planetId), true);

		if (isset($data['id']))
			return $data;
		else
			return false;
	}

	public function getByCoords ($galaxy, $system, $planet, $type = 1)
	{
		$data = db::query("SELECT *
							FROM
								game_planets
							WHERE
								`galaxy` = '" . intval($galaxy) . "' AND
								`system` = '" . intval($system) . "' AND
								`planet` = '" . intval($planet) . "' AND
								`planet_type` = '" . intval($type) . "'", true);

		if (isset($data['id']))
			return $data;
		else
			return false;
	}

	public function load_from_id ($planet_id)
	{
		$this->data = $this->getById($planet_id);

		if ($this->data !== false)
			$this->copyTempParams();
	}

	public function load_from_coords ($galaxy, $system, $planet, $type)
	{
		$this->data = $this->getByCoords($galaxy, $system, $planet, $type);

		if ($this->data !== false)
			$this->copyTempParams();
	}

	public function load_from_array ($array)
	{
		$this->data = $array;

		$this->copyTempParams();
	}

	public function load_user_info ($array)
	{
		$this->user = $array;
	}

	public function copyTempParams ()
	{
		if (is_array($this->data))
		{
			foreach ($this->data AS $key => $value)
			{
				$this->data['~'.$key] = $value;
			}

			$this->data['energy_max'] = 0;
		}
	}

	public function checkOwnerPlanet ()
	{
		if ($this->data['id_owner'] != $this->user->data['id'] && $this->data['id_ally'] > 0 && ($this->data['id_ally'] != $this->user->data['ally_id'] || !$this->user->data['ally']['rights']['planet']))
		{
			sql::build()->update('game_users')->setField('current_planet', $this->user->data['id_planet'])->where('id', '=', $this->user->data['id'])->execute();

			$this->data['current_planet'] = $this->user->data['id_planet'];

			$this->load_from_id($this->user->data['id_planet']);

			return false;
		}

		return true;
	}

	public function getProductionLevel ($Element, $BuildLevel, $BuildLevelFactor = 10)
	{
		global $ProdGrid, $reslist;

		$return = array('energy' => 0);

		foreach ($reslist['res'] AS $res)
			$return[$res] = 0;

		if (isset($ProdGrid[$Element]))
		{
			/** @noinspection PhpUnusedLocalVariableInspection */
			$energyTech 	= $this->user->data['energy_tech'];
			/** @noinspection PhpUnusedLocalVariableInspection */
			$BuildTemp		= $this->data['temp_max'];

			foreach ($reslist['res'] AS $res)
				$return[$res] = floor(eval($ProdGrid[$Element][$res]) * core::getConfig('resource_multiplier') * $this->user->bonusValue($res));

			$energy = floor(eval($ProdGrid[$Element]['energy']));

			if ($Element < 4)
				$return['energy'] = $energy;
			elseif ($Element == 4 || $Element == 12)
				$return['energy'] = floor($energy * $this->user->bonusValue('energy'));
			elseif ($Element == 212)
				$return['energy'] = floor($energy * $this->user->bonusValue('solar'));
		}

		return $return;
	}

	public function getProductions ()
	{
		global $resource, $reslist;

		$Caps = array();

		foreach ($reslist['res'] AS $res)
			$Caps[$res.'_perhour'] = 0;

		$Caps['energy_used'] 	= 0;
		$Caps['energy_max'] 	= 0;

		foreach ($reslist['prod'] AS $ProdID)
		{
			$BuildLevelFactor = $this->data[$resource[$ProdID] . '_porcent'];
			$BuildLevel = $this->data[$resource[$ProdID]];

			if ($ProdID == 12 && $this->data['deuterium'] < 100)
				$BuildLevelFactor = 0;

			$result = $this->getProductionLevel($ProdID, $BuildLevel, $BuildLevelFactor);

			foreach ($reslist['res'] AS $res)
				$Caps[$res.'_perhour'] += $result[$res];

			if ($ProdID < 4)
				$Caps['energy_used'] 	+= $result['energy'];
			else
				$Caps['energy_max'] 	+= $result['energy'];
		}

		if ($this->data['planet_type'] == 3 || $this->data['planet_type'] == 5)
		{
			foreach ($reslist['res'] AS $res)
			{
				core::setConfig($res.'_basic_income', 0);
				$this->data[$res.'_perhour'] = 0;
			}

			$this->data['energy_used'] 	= 0;
			$this->data['energy_max'] 	= 0;
		}
		else
		{
			foreach ($reslist['res'] AS $res)
				$this->data[$res.'_perhour'] = $Caps[$res.'_perhour'];

			$this->data['energy_used'] 	= $Caps['energy_used'];
			$this->data['energy_max'] 	= $Caps['energy_max'];
		}
	}

	public function PlanetResourceUpdate ($updateTime = 0, $simultion = false)
	{
		if (!$this->user instanceof User)
			return false;

		global $resource, $reslist;

		if ($this->user->data['urlaubs_modus_time'] != 0)
			$simultion = true;

		if (!$updateTime)
			$updateTime = time();

		if ($updateTime < $this->data['last_update'])
			return false;

		$this->data['planet_updated'] = true;

		foreach ($reslist['res'] AS $res)
		{
			$this->data[$res.'_max']  = floor((BASE_STORAGE_SIZE + floor(50000 * round(pow(1.6, $this->data[$res.'_store'])))) * $this->user->bonusValue('storage'));
			$this->data[$res.'_max'] *= MAX_OVERFLOW;
		}

		$this->data['battery_max'] = floor(250 * $this->data[$resource[4]]);

		$this->getProductions();

		$productionTime = $updateTime - $this->data['last_update'];
		$this->data['last_update'] = $updateTime;

		if (!defined('CRON'))
			$this->data['last_active'] = $this->data['last_update'];

		if ($this->data['energy_max'] == 0)
		{
			foreach ($reslist['res'] AS $res)
				$this->data[$res.'_perhour'] = core::getConfig($res.'_basic_income');

			$production_level = 0;
		}
		elseif ($this->data['energy_max'] >= abs($this->data['energy_used']))
		{
			$production_level = 100;
			$akk_add = round(($this->data['energy_max'] - abs($this->data['energy_used'])) * ($productionTime / 3600), 2);

			if ($this->data['battery_max'] > ($this->data['energy_ak'] + $akk_add))
				$this->data['energy_ak'] += $akk_add;
			else
				$this->data['energy_ak'] = $this->data['battery_max'];
		}
		else
		{
			if ($this->data['energy_ak'] > 0)
			{
				$need_en = ((abs($this->data['energy_used']) - $this->data['energy_max']) / 3600) * $productionTime;

				if ($this->data['energy_ak'] > $need_en)
				{
					$production_level = 100;
					$this->data['energy_ak'] -= round($need_en, 2);
				}
				else
				{
					$production_level = round((($this->data['energy_max'] + $this->data['energy_ak'] * 3600) / abs($this->data['energy_used'])) * 100, 1);
					$this->data['energy_ak'] = 0;
				}
			}
			else
				$production_level = round(($this->data['energy_max'] / abs($this->data['energy_used'])) * 100, 1);
		}

		$production_level = min(max($production_level, 0), 100);

		$this->data['production_level'] = $production_level;

		foreach ($reslist['res'] AS $res)
		{
			$this->data[$res.'_production'] = 0;

			if ($this->data[$res] <= $this->data[$res.'_max'])
			{
				$this->data[$res.'_production'] = (($productionTime * ($this->data[$res.'_perhour'] / 3600))) * (0.01 * $production_level);
				$this->data[$res.'_base'] 		= (($productionTime * (core::getConfig($res.'_basic_income', 0) / 3600)) * core::getConfig('resource_multiplier', 1));

				$this->data[$res.'_production'] = $this->data[$res.'_production'] + $this->data[$res.'_base'];

				if (($this->data[$res] + $this->data[$res.'_production']) > $this->data[$res.'_max'])
					$this->data[$res.'_production'] = $this->data[$res.'_max'] - $this->data[$res];
			}

			$this->data[$res.'_perhour'] = round($this->data[$res.'_perhour'] * (0.01 * $production_level));
			$this->data[$res] += $this->data[$res.'_production'];

			if ($this->data[$res] < 0)
				$this->data[$res] = 0;
		}

		if ($simultion)
		{
			$Builded = $this->HandleElementBuildingQueue($productionTime);

			$check = false;

			if (is_array($Builded))
			{
				foreach ($Builded AS $count)
				{
					if ($count > 0)
					{
						$check = true;
						break;
					}
				}
			}

			if ($check)
				$simultion = false;
		}

		if (!$simultion)
		{
			if (!isset($Builded))
				$Builded = $this->HandleElementBuildingQueue($productionTime);

			$arFields = array();

			if ($this->data['planet_type'] == 1)
			{
				foreach ($reslist['res'] AS $res)
				{
					if ($this->data[$res] != $this->data['~'.$res])
						$arFields[$res] = $this->data[$res];
				}

				if ($this->data['~energy_ak'] != $this->data['energy_ak'])
					$arFields['energy_ak'] = $this->data['energy_ak'];
			}

			if ($this->data['queue'] != $this->data['~queue'])
				$arFields['queue'] = $this->data['queue'];

			if ($Builded != '')
			{
				foreach ($Builded as $Element => $Count)
					if ($Element <> '' && $this->data[$resource[$Element]] != $this->data['~'.$resource[$Element]])
						$arFields[$resource[$Element]] = $this->data[$resource[$Element]];
			}

			if (count($arFields) > 0 || ($this->data['last_update'] - $this->data['~last_update']) >= 60)
			{
				$arFields['last_update'] = $this->data['last_update'];

				if ($this->data['~last_active'] != $this->data['last_active'])
					$arFields['last_active'] = $this->data['last_active'];

				sql::build()->update('game_planets')->set($arFields)->where('id', '=', $this->data['id'])->addAND()->where('last_update', '!=', $this->data['last_update'])->execute();
			}
		}

		return true;
	}

	public function CheckPlanetUsedFields ()
	{
		global $resource, $reslist;

		$cnt = 0;

		foreach ($reslist['allowed'][$this->data['planet_type']] AS $type)
			$cnt += $this->data[$resource[$type]];

		if ($this->data['field_current'] != $cnt)
		{
			$this->data['field_current'] = $cnt;

			$this->saveData(Array('field_current' => $this->data['field_current']));
		}
	}

	private function HandleElementBuildingQueue ($ProductionTime)
	{
		global $resource;

		if ($this->data['queue'] != '[]')
		{
			$queueManager = new queueManager($this->data['queue']);
			$queueArray = $queueManager->get();

			if ($queueManager->getCount($queueManager::QUEUE_TYPE_SHIPYARD))
			{
				$BuildQueue = $queueManager->get($queueManager::QUEUE_TYPE_SHIPYARD);

				$this->data['b_hangar'] = $BuildQueue[0]['s'];
				$this->data['b_hangar'] += $ProductionTime;

				$MissilesSpace = ($this->data[$resource[44]] * 10) - ($this->data['interceptor_misil'] + (2 * $this->data['interplanetary_misil']));
				$Shield_1 = $this->data['small_protection_shield'];
				$Shield_2 = $this->data['big_protection_shield'];

				$BuildArray = array();
				$Builded = array();

				foreach ($BuildQueue as $Node => $Item)
				{
					if ($Item['i'] == 502 || $Item['i'] == 503)
					{
						if ($Item['i'] == 502)
						{
							if ($Item['l'] > $MissilesSpace)
								$Item['l'] = $MissilesSpace;
							else
								$MissilesSpace -= $Item['l'];
						}
						else
						{
							if ($Item['l'] > floor($MissilesSpace / 2))
								$Item['l'] = floor($MissilesSpace / 2);
							else
								$MissilesSpace -= $Item['l'];
						}
					}

					if ($Item['i'] == 407 || $Item['i'] == 408)
					{
						if ($Item['l'] > 1)
							$Item['l'] = 1;

						if ($Item['i'] == 407)
						{
							if ($Shield_1 == 1)
								$Item['l'] = 0;
							else
								$Shield_1 = 1;
						}
						else
						{
							if ($Shield_2 == 1)
								$Item['l'] = 0;
							else
								$Shield_2 = 1;
						}
					}

					$BuildArray[$Node] = array($Item['i'], $Item['l'], GetBuildingTime($this->user, $this->data, $Item['i']));
				}

				$UnFinished = false;

				$queueArray[$queueManager::QUEUE_TYPE_SHIPYARD] = array();

				foreach ($BuildArray as $Item)
				{
					if (!isset($resource[$Item[0]]))
						continue;

					$Element = $Item[0];
					$Count = $Item[1];
					$BuildTime = $Item[2];

					if (!isset($Builded[$Element]))
						$Builded[$Element] = 0;

					while ($this->data['b_hangar'] >= $BuildTime && !$UnFinished)
					{
						$this->data['b_hangar'] -= $BuildTime;
						$Builded[$Element]++;
						$this->data[$resource[$Element]]++;
						$Count--;

						if ($Count <= 0)
							break;
						elseif ($this->data['b_hangar'] < $BuildTime)
							$UnFinished = true;
					}

					if ($Count > 0)
					{
						$UnFinished = true;

						$queueArray[$queueManager::QUEUE_TYPE_SHIPYARD][] = array('i' => $Element, 'l' => $Count, 't' => 0, 's' => count($queueArray[$queueManager::QUEUE_TYPE_SHIPYARD]) == 0 ? $this->data['b_hangar'] : 0, 'e' => 0);
					}
				}

				if (!count($queueArray[$queueManager::QUEUE_TYPE_SHIPYARD]))
					unset($queueArray[$queueManager::QUEUE_TYPE_SHIPYARD]);

				$this->data['queue'] = json_encode($queueArray);

				return $Builded;
			}
			else
				return '';
		}
		else
			return '';
	}

	public function UpdatePlanetBatimentQueueList ()
	{
		$RetValue = false;

		if ($this->data['queue'] != '[]')
		{
			$queueManager = new queueManager($this->data['queue']);

			$build_count = $queueManager->getCount($queueManager::QUEUE_TYPE_BUILDING);

			if ($build_count)
			{
				for ($i = 0; $i < $build_count; $i++)
				{
					if ($this->CheckPlanetBuildingQueue($queueManager))
					{
						if (!$this->data['planet_updated'])
							$this->PlanetResourceUpdate();

						$this->SetNextQueueElementOnTop();
						$RetValue = true;
					}
					else
						break;
				}
			}

			if ($queueManager->getCount($queueManager::QUEUE_TYPE_RESEARCH) > 0 && $this->user->data['b_tech_planet'] == 0)
			{
				sql::build()->update('game_users')->setField('b_tech_planet', $this->data['id'])->where('id', '=', $this->user->data['id'])->execute();

				$this->user->data['b_tech_planet'] = $this->data['id'];
			}
		}

		if ($this->checkTechnologieBuild())
			$RetValue = true;

		return $RetValue;
	}

	private function checkTechnologieBuild ()
	{
		global $resource;

		if ($this->user->data['b_tech_planet'] != 0)
		{
			if ($this->user->data['b_tech_planet'] != $this->data['id'])
				$WorkingPlanet = db::query("SELECT id, queue FROM game_planets WHERE `id` = '" . $this->user->data['b_tech_planet'] . "';", true);

			if (isset($WorkingPlanet))
				$ThePlanet = $WorkingPlanet;
			else
				$ThePlanet = $this->data;

			$queueManager = new queueManager($ThePlanet['queue']);
			$queueArray = $queueManager->get($queueManager::QUEUE_TYPE_RESEARCH);
			
			if (count($queueArray))
			{
				if ($queueArray[0]['e'] <= time())
				{
					$this->user->data[$resource[$queueArray[0]['i']]]++;

					$newQueue = $queueManager->get();
					unset($newQueue[$queueManager::QUEUE_TYPE_RESEARCH]);

					sql::build()->update('game_planets')->setField('queue', json_encode($newQueue))->where('id', '=', $ThePlanet['id'])->execute();

					sql::build()->update('game_users')->set(Array
					(
						$resource[$queueArray[0]['i']]	=> $this->user->data[$resource[$queueArray[0]['i']]],
						'b_tech_planet'					=> 0
					))
					->where('id', '=', $this->user->data['id'])->execute();

					$this->user->data['b_tech_planet'] = 0;

					if (!isset($WorkingPlanet))
					{
						$this->data['queue'] = json_encode($newQueue);
					}
				}
			}
			else
			{
				sql::build()->update('game_users')->setField('b_tech_planet', 0)->where('id', '=', $this->user->data['id'])->execute();

				$this->user->data['b_tech_planet'] = 0;
			}
		}
		else
			return false;

		return true;
	}

	public function HandleTechnologieBuild ()
	{
		global $resource;

		$Result['WorkOn'] = "";
		$Result['OnWork'] = false;

		if ($this->user->data['b_tech_planet'] != 0)
		{
			if ($this->user->data['b_tech_planet'] != $this->data['id'])
				$WorkingPlanet = db::query("SELECT * FROM game_planets WHERE `id` = '" . $this->user->data['b_tech_planet'] . "';", true);

			if (isset($WorkingPlanet))
				$ThePlanet = $WorkingPlanet;
			else
				$ThePlanet = $this->data;

			$queueManager 	= new queueManager($ThePlanet['queue']);
			$queueArray 	= $queueManager->get($queueManager::QUEUE_TYPE_RESEARCH);

			if (count($queueArray))
			{
				if ($queueArray[0]['e'] <= time())
				{
					$this->user->data['b_tech_planet'] = 0;
					$this->user->data[$resource[$queueArray[0]['i']]]++;

					$newQueue = $queueManager->get();
					unset($newQueue[$queueManager::QUEUE_TYPE_RESEARCH]);

					db::query("UPDATE game_planets SET `queue` = '".json_encode($newQueue)."' WHERE `id` = '" . $ThePlanet['id'] . "';");
					db::query("UPDATE game_users SET `" . $resource[$queueArray[0]['i']] . "` = '" . $this->user->data[$resource[$queueArray[0]['i']]] . "', `b_tech_planet` = '0' WHERE `id` = '" . $this->user->data['id'] . "';");

					if (!isset($WorkingPlanet))
						$this->data = $ThePlanet;
				}
				else
				{
					$Result['WorkOn'] = $ThePlanet;
					$Result['OnWork'] = true;
				}
			}
			else
			{
				db::query("UPDATE game_users SET `b_tech_planet` = '0'  WHERE `id` = '" . $this->user->data['id'] . "';");

				$this->user->data['b_tech_planet'] = 0;
			}
		}

		return $Result;
	}

	private function CheckPlanetBuildingQueue (queueManager $queueManager)
	{
		if ($queueManager->getCount($queueManager::QUEUE_TYPE_BUILDING))
		{
			$QueueArray = $queueManager->get($queueManager::QUEUE_TYPE_BUILDING);

			$BuildArray = $QueueArray[0];
			$Element = $BuildArray['i'];

			array_shift($QueueArray);

			$ForDestroy = ($BuildArray['d'] == 1);

			if ($BuildArray['e'] <= time())
			{
				global $resource;

				$Needed = GetBuildingPrice($this->user, $this->data, $Element, true, $ForDestroy);
				$Units = $Needed['metal'] + $Needed['crystal'] + $Needed['deuterium'];

				// Мирный опыт за строения
				$XPBuildings = array(1, 2, 3, 5, 22, 23, 24, 25);
				$XP = 0;

				if (in_array($Element, $XPBuildings))
				{
					if (!$ForDestroy)
						$XP += floor($Units / core::getConfig('buildings_exp_mult', 1000));
					else
						$XP -= floor($Units / core::getConfig('buildings_exp_mult', 1000));
				}

				if (!$ForDestroy)
				{
					$this->data['field_current']++;
					$this->data[$resource[$Element]]++;
				}
				else
				{
					$this->data['field_current']--;
					$this->data[$resource[$Element]]--;
				}

				$NewQueue = $queueManager->get();
				$NewQueue[$queueManager::QUEUE_TYPE_BUILDING] = $QueueArray;

				$queueManager->loadQueue($NewQueue);

				$this->data['queue'] = json_encode($NewQueue);

				$this->saveData(Array
				(
					$resource[$Element]	=> $this->data[$resource[$Element]],
					'queue'				=> $this->data['queue']
				));

				if ($XP != 0 && $this->user->data['lvl_minier'] < core::getConfig('level.max_ind', 100))
				{
					$this->user->data['xpminier'] += $XP;

					if ($this->user->data['xpminier'] < 0)
						$this->user->data['xpminier'] = 0;

					sql::build()->update('game_users')->set(Array
					(
						'xpminier' => $this->user->data['xpminier']
					))
					->where('id', '=', $this->user->data['id'])->execute();
				}

				return true;
			}
			else
				return false;
		}

		return false;
	}

	public function SetNextQueueElementOnTop ()
	{
		$queueManager = new queueManager($this->data['queue']);

		if ($queueManager->getCount($queueManager::QUEUE_TYPE_BUILDING))
		{
			global $resource;

			$QueueArray = $queueManager->get($queueManager::QUEUE_TYPE_BUILDING);

			if ($QueueArray[0]['s'] > 0)
				return;

			$Loop = true;

			while ($Loop)
			{
				$ListIDArray = $QueueArray[0];

				$HaveNoMoreLevel = false;

				$ForDestroy = ($ListIDArray['d'] == 1);

				if ($ForDestroy && $this->data[$resource[$ListIDArray['i']]] == 0)
				{
					$HaveRessources = false;
					$HaveNoMoreLevel = true;
				}
				else
					$HaveRessources = IsElementBuyable($this->user, $this->data, $ListIDArray['i'], true, $ForDestroy);

				if ($HaveRessources && IsTechnologieAccessible($this->user->data, $this->data, $ListIDArray['i']))
				{
					$Needed = GetBuildingPrice($this->user, $this->data, $ListIDArray['i'], true, $ForDestroy);

					$this->data['metal'] 		-= $Needed['metal'];
					$this->data['crystal'] 		-= $Needed['crystal'];
					$this->data['deuterium'] 	-= $Needed['deuterium'];

					$QueueArray[0]['s'] = time();

					$Loop = false;

					if (core::getConfig('log.buildings', false) == true)
					{
						sql::build()->insert('game_log_history')->set(array
						(
							'user_id' 			=> $this->user->data['id'],
							'time' 				=> time(),
							'operation' 		=> ($ForDestroy ? 2 : 1),
							'planet' 			=> $this->data['id'],
							'from_metal' 		=> $this->data['metal'] + $Needed['metal'],
							'from_crystal' 		=> $this->data['crystal'] + $Needed['crystal'],
							'from_deuterium' 	=> $this->data['deuterium'] + $Needed['deuterium'],
							'to_metal' 			=> $this->data['metal'],
							'to_crystal' 		=> $this->data['crystal'],
							'to_deuterium' 		=> $this->data['deuterium'],
							'build_id' 			=> $ListIDArray['i'],
							'level' 			=> ($this->data[$resource[$ListIDArray['i']]] + 1)
						))->execute();
					}
				}
				else
				{
					if ($HaveNoMoreLevel)
						$Message = sprintf(_getText('sys_nomore_level'), _getText('tech', $ListIDArray['i']));
					elseif (!$HaveRessources)
					{
						$Needed = GetBuildingPrice($this->user, $this->data, $ListIDArray['i'], true, $ForDestroy);

						$Message = 'У вас недостаточно ресурсов чтобы начать строительство здания ' . _getText('tech', $ListIDArray['i']) . '.<br>Вам необходимо ещё: <br>';
						if ($Needed['metal'] > $this->data['metal'])
							$Message .= strings::pretty_number($Needed['metal'] - $this->data['metal']) . ' металла<br>';
						if ($Needed['crystal'] > $this->data['crystal'])
							$Message .= strings::pretty_number($Needed['crystal'] - $this->data['crystal']) . ' кристалла<br>';
						if ($Needed['deuterium'] > $this->data['deuterium'])
							$Message .= strings::pretty_number($Needed['deuterium'] - $this->data['deuterium']) . ' дейтерия<br>';
						if (isset($Needed['energy_max']) && isset($this->data['energy_max']) && $Needed['energy_max'] > $this->data['energy_max'])
							$Message .= strings::pretty_number($Needed['energy_max'] - $this->data['energy_max']) . ' энергии<br>';
					}

					if (isset($Message))
						user::get()->sendMessage($this->user->data['id'], 0, 0, 99, _getText('sys_buildlist'), $Message);

					array_shift($QueueArray);

					if (count($QueueArray) == 0)
						$Loop = false;
				}
			}

			$newQueue = $queueManager->get();

			$BuildEndTime = time();

			foreach ($QueueArray as &$ListIDArray)
			{
				$ListIDArray['t'] = GetBuildingTime($this->user, $this->data, $ListIDArray['i']);

				if ($ListIDArray['d'])
					$ListIDArray['t'] = ceil($ListIDArray['t'] / 2);

				$BuildEndTime += $ListIDArray['t'];
				$ListIDArray['e'] = $BuildEndTime;
			}

			unset($ListIDArray);

			$newQueue[$queueManager::QUEUE_TYPE_BUILDING] = $QueueArray;
			$newQueue = json_encode($newQueue);
		}

		if (isset($newQueue) && $this->data['queue'] != $newQueue)
		{
			$this->data['queue'] = $newQueue;

			db::query("LOCK TABLES game_planets WRITE");

			$this->saveData(Array
			(
				'metal'		=> $this->data['metal'],
				'crystal'	=> $this->data['crystal'],
				'deuterium'	=> $this->data['deuterium'],
				'queue'		=> $this->data['queue']
			));

			db::query("UNLOCK TABLES");
		}
	}

	function checkAbandonMoonState (&$lunarow)
	{
		if ($lunarow['luna_destruyed'] <= time())
		{
			db::query("DELETE FROM game_planets WHERE `id` = " . $lunarow['luna_id'] . "");

			sql::build()->update('game_planets')->setField('parent_planet', 0)->where('parent_planet', '=', $lunarow['luna_id'])->execute();

			$lunarow['id_luna'] = 0;
		}
	}

	function checkAbandonPlanetState (&$planet)
	{
		if ($planet['destruyed'] <= time())
		{
			db::query("DELETE FROM game_planets WHERE id = " . $planet['id_planet'] . ";");
			if ($planet['parent_planet'] != 0)
				db::query("DELETE FROM game_planets WHERE id = " . $planet['parent_planet'] . ";");
		}
	}

	public function getNetworkLevel()
	{
		global $resource;

		$researchLevelList = array($this->data[$resource[31]]);

		if ($this->user->data[$resource[123]] > 0)
		{
			$researchResult = db::query("SELECT ".$resource[31]." FROM game_planets WHERE id_owner='" . $this->user->data['id'] . "' AND id != '" . $this->data['id'] . "' AND ".$resource[31]." > 0 AND destruyed = 0 AND planet_type = 1 ORDER BY ".$resource[31]." DESC LIMIT ".$this->user->data[$resource[123]]."");

			while ($researchRow = db::fetch($researchResult))
			{
				$researchLevelList[] = $researchRow[$resource[31]];
			}
		}

		return $researchLevelList;
	}

	public function saveData ($fields, $planetId = 0)
	{
		sql::build()->update('game_planets')->set($fields)->where('id', '=', ($planetId > 0 ? $planetId : $this->data['id']))->execute();
	}
}

?>