<?php

namespace Xnova;
use Xcms\request;
use Xcms\strings;

/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class building
{
	public function pageBuilding ()
	{
		global $resource, $reslist;

		$parse = array();

		if (app::$planetrow->data['id_ally'] > 0 && app::$planetrow->data['id_ally'] == app::$user->data['ally_id'])
			$reslist['allowed']['5'] = array(14, 21, 34, 44);

		app::$planetrow->SetNextQueueElementOnTop();

		$Queue = $this->ShowBuildingQueue(app::$planetrow, app::$user);

		$MaxBuidSize = MAX_BUILDING_QUEUE_SIZE + app::$user->bonusValue('queue', 0);

		$CanBuildElement = ($Queue['lenght'] < $MaxBuidSize);

		if (isset($_GET['cmd']))
		{
			$Command 	= request::G('cmd', '');
			$Element 	= request::G('building', 0, VALUE_INT);
			$ListID 	= request::G('listid', 0, VALUE_INT);

			if (in_array($Element, $reslist['allowed'][app::$planetrow->data['planet_type']]) || ($ListID != 0 && ($Command == 'cancel' || $Command == 'remove')))
			{
				$queueManager = new queueManager(app::$planetrow->data['queue']);
				$queueManager->setUserObject(app::$user);
				$queueManager->setPlanetObject(app::$planetrow);

				switch ($Command)
				{
					case 'cancel':
						$queueManager->delete(1, 0);
						break;
					case 'remove':
						$queueManager->delete(1, ($ListID - 1));
						break;
					case 'insert':

						if ($CanBuildElement)
							$queueManager->add($Element);

						break;
					case 'destroy':

						if ($CanBuildElement)
							$queueManager->add($Element, 1, true);

						break;
				}

				request::redirectTo("?set=buildings");
			}
		}

		$CurrentMaxFields = CalculateMaxPlanetFields(app::$planetrow->data);
		$RoomIsOk = (app::$planetrow->data["field_current"] < ($CurrentMaxFields - $Queue['lenght']));

		$oldStyle = user::get()->getUserOption('only_available');

		$parse['BuildingsList'] = array();

		foreach ($reslist['build'] as $Element)
		{
			if (!in_array($Element, $reslist['allowed'][app::$planetrow->data['planet_type']]))
				continue;

			$isAccess = IsTechnologieAccessible(app::$user->data, app::$planetrow->data, $Element);

			if (!$isAccess && $oldStyle)
				continue;

			if (!checkTechnologyRace(app::$user->data, $Element))
				continue;

			$HaveRessources 	= IsElementBuyable(app::$user, app::$planetrow->data, $Element, true, false);
			$BuildingLevel 		= app::$planetrow->data[$resource[$Element]];

			$row = array();

			$row['access']= $isAccess;
			$row['i'] 	= $Element;
			$row['count'] = $BuildingLevel;
			$row['price'] = GetElementPrice(GetBuildingPrice(app::$user, app::$planetrow->data, $Element), app::$planetrow->data);

			if ($isAccess)
			{
				$row['time'] 	= GetBuildingTime(app::$user, app::$planetrow->data, $Element);
				$row['add'] 	= GetNextProduction($Element, $BuildingLevel);
				$row['click'] = '';

				if ($Element == 31)
				{
					if (app::$user->data["b_tech_planet"] != 0)
						$row['click'] = "<span class=\"resNo\">" . _getText('in_working') . "</span>";
				}

				if (!$row['click'])
				{
					if ($RoomIsOk && $CanBuildElement)
					{
						if ($Queue['lenght'] == 0)
						{
							if ($HaveRessources == true)
								$row['click'] = "<a href=\"?set=buildings&cmd=insert&building=" . $Element . "\"><span class=\"resYes\">".((!app::$planetrow->data[$resource[$Element]]) ? 'Построить' : 'Улучшить')."</span></a>";
							else
								$row['click'] = "<span class=\"resNo\">нет ресурсов</span>";
						}
						else
							$row['click'] = "<a href=\"?set=buildings&cmd=insert&building=" . $Element . "\"><span class=\"resYes\">В очередь</span></a>";
					}
					elseif ($RoomIsOk && !$CanBuildElement)
						$row['click'] = "<span class=\"resNo\">".((!app::$planetrow->data[$resource[$Element]]) ? 'Построить' : 'Улучшить')."</span>";
					else
						$row['click'] = "<span class=\"resNo\">нет места</span>";
				}
			}

			$parse['BuildingsList'][] = $row;
		}

		$parse['BuildList'] 			= $Queue['buildlist'];
		$parse['planet_field_current'] 	= app::$planetrow->data["field_current"];
		$parse['planet_field_max'] 		= $CurrentMaxFields;
		$parse['field_libre'] 			= $parse['planet_field_max'] - app::$planetrow->data['field_current'];

		return $parse;
	}

	public function pageResearch ($mode = '')
	{
		global $resource, $reslist, $pricelist, $CombatCaps;

		$TechHandle = app::$planetrow->HandleTechnologieBuild(app::$planetrow, app::$user);

		$NoResearchMessage = "";
		$bContinue = true;

		if (!CheckLabSettingsInQueue(app::$planetrow->data))
		{
			$NoResearchMessage = _getText('labo_on_update');
			$bContinue = false;
		}

		$spaceLabs = array();

		if (app::$user->data[$resource[123]] > 0)
			$spaceLabs = app::$planetrow->getNetworkLevel();

		app::$planetrow->data['spaceLabs'] = $spaceLabs;

		if ($mode == 'fleet')
			$res_array = $reslist['tech_f'];
		else
			$res_array = $reslist['tech'];

		$PageParse['mode'] = $_GET['mode'];

		$queueManager = new queueManager((isset($TechHandle['WorkOn']['queue']) ? $TechHandle['WorkOn']['queue'] : app::$planetrow->data['queue']));

		if (isset($_GET['cmd']) AND $bContinue != false)
		{
			$Command 	= request::G('cmd');
			$Techno 	= request::G('tech', 0, VALUE_INT);

			$queueManager->setUserObject(app::$user);
			$queueManager->setPlanetObject(app::$planetrow);

			if ($Techno > 0 && in_array($Techno, $res_array))
			{
				switch ($Command)
				{
					case 'cancel':
						$queueManager->delete($Techno);
						break;

					case 'search':
						$queueManager->add($Techno);
						break;
				}

				request::redirectTo("?set=buildings&mode=research".($mode != '' ? '_'.$mode : '')."");
			}
		}

		$queueArray = $queueManager->get($queueManager::QUEUE_TYPE_RESEARCH);

		if (count($queueArray) && isset($queueArray[0]))
			$queueArray = $queueArray[0];

		$oldStyle = user::get()->getUserOption('only_available');

		$PageParse['technolist'] = array();

		foreach ($res_array AS $Tech)
		{
			$isAccess = IsTechnologieAccessible(app::$user->data, app::$planetrow->data, $Tech);

			if (!$isAccess && $oldStyle)
				continue;

			if (!checkTechnologyRace(app::$user->data, $Tech))
				continue;

			$row = array();
			$row['access'] = $isAccess;
			$row['i'] = $Tech;

			$building_level = app::$user->data[$resource[$Tech]];

			$row['tech_level'] = ($building_level == 0) ? "<font color=#FF0000>" . $building_level . "</font>" : "<font color=#00FF00>" . $building_level . "</font>";

			if (isset($pricelist[$Tech]['max']))
				$row['tech_level'] .= ' из <font color=yellow>' . $pricelist[$Tech]['max'] . '</font>';

			$row['tech_price'] = GetElementPrice(GetBuildingPrice(app::$user, app::$planetrow->data, $Tech), app::$planetrow->data);

			if ($isAccess)
			{
				if ($Tech > 300 && $Tech < 400)
				{
					$l = ($Tech < 350 ? ($Tech - 100) : ($Tech + 50));

					if (isset($CombatCaps[$l]['power_up']) && $CombatCaps[$l]['power_up'] > 0)
					{
						$row['add'] = '+' . ($CombatCaps[$l]['power_up'] * $building_level) . '% атака<br>';
						$row['add'] .= '+' . ($CombatCaps[$l]['power_armour'] * $building_level) . '% прочность<br>';
					}
					if (isset($CombatCaps[$l]['power_consumption']) && $CombatCaps[($Tech < 350 ? ($Tech - 100) : ($Tech + 50))]['power_consumption'] > 0)
						$row['add'] = '+' . ($CombatCaps[$l]['power_consumption'] * $building_level) . '% вместимость<br>';
				}
				elseif ($Tech >= 120 && $Tech <= 122)
					$row['add'] = '+' . (5 * $building_level) . '% атака<br>';
				elseif ($Tech == 115)
					$row['add'] = '+' . (10 * $building_level) . '% скорости РД<br>';
				elseif ($Tech == 117)
					$row['add'] = '+' . (20 * $building_level) . '% скорости ИД<br>';
				elseif ($Tech == 118)
					$row['add'] = '+' . (30 * $building_level) . '% скорости ГД<br>';
				elseif ($Tech == 108)
					$row['add'] = '+' . ($building_level + 1) . ' слотов флота<br>';
				elseif ($Tech == 109)
					$row['add'] = '+' . (5 * $building_level) . '% атаки<br>';
				elseif ($Tech == 110)
					$row['add'] = '+' . (3 * $building_level) . '% защиты<br>';
				elseif ($Tech == 111)
					$row['add'] = '+' . (5 * $building_level) . '% прочности<br>';
				elseif ($Tech == 123)
					$row['add'] = '+' . ($building_level) . '% лабораторий<br>';

				$SearchTime = GetBuildingTime(app::$user, app::$planetrow->data, $Tech);
				$row['search_time'] = $SearchTime;
				$CanBeDone = IsElementBuyable(app::$user, app::$planetrow->data, $Tech);

				if (!$TechHandle['OnWork'])
				{
					$LevelToDo = 1 + app::$user->data[$resource[$Tech]];
					if (isset($pricelist[$Tech]['max']) && app::$user->data[$resource[$Tech]] >= $pricelist[$Tech]['max'])
					{
						$TechnoLink = '<font color=#FF0000>максимальный уровень</font>';
					}
					elseif ($CanBeDone)
					{
						if (!CheckLabSettingsInQueue(app::$planetrow->data))
						{
							if ($LevelToDo == 1)
								$TechnoLink = "<font color=#FF0000>Исследовать</font>";
							else
								$TechnoLink = "<font color=#FF0000>Улучшить</font>";
						}
						else
						{
							$TechnoLink = "<a href=\"?set=buildings&mode=" . $_GET['mode'] . "&cmd=search&tech=" . $Tech . "\">";

							if ($LevelToDo == 1)
								$TechnoLink .= "<font color=#00FF00>Исследовать</font>";
							else
								$TechnoLink .= "<font color=#00FF00>Улучшить</font>";

							$TechnoLink .= "</a>";
						}
					}
					else
						$TechnoLink = '<span class="resNo">нет ресурсов</span>';
				}
				else
				{
					if (isset($queueArray['i']) && $queueArray['i'] == $Tech)
					{
						$bloc = array();

						if ($TechHandle['WorkOn']['id'] != app::$planetrow->data['id'])
							$bloc['tech_name'] 	= ' на ' . $TechHandle['WorkOn']["name"];
						else
							$bloc['tech_name'] 	= "";

						$bloc['tech_time'] 	= $queueArray['e'] - time();
						$bloc['tech_home'] 	= $TechHandle['WorkOn']["id"];
						$bloc['tech_id'] 	= $queueArray['i'];

						$TechnoLink = $bloc;
					}
					else
						$TechnoLink = "<center>-</center>";
				}
				$row['tech_link'] = $TechnoLink;
			}

			$PageParse['technolist'][] = $row;
		}

		$PageParse['noresearch'] = $NoResearchMessage;

		return $PageParse;
	}

	public function pageShipyard ($mode = 'fleet')
	{
		global $resource, $reslist, $pricelist;

		$queueManager = new queueManager(app::$planetrow->data['queue']);

		if($mode == 'defense')
			$elementIDs     = $reslist['defense'];
		else
			$elementIDs     = $reslist['fleet'];

		if (isset($_POST['fmenge']))
		{
			$queueManager->setUserObject(app::$user);
			$queueManager->setPlanetObject(app::$planetrow);

			foreach ($_POST['fmenge'] as $Element => $Count)
			{
				$Element 	= intval($Element);
				$Count 		= abs(intval($Count));

				if (!in_array($Element, $elementIDs))
					continue;

				$queueManager->add($Element, $Count);
			}

			app::$planetrow->data['queue'] = $queueManager->get();
		}

		$queueArray = $queueManager->get($queueManager::QUEUE_TYPE_SHIPYARD);

		$BuildArray = $this->extractHangarQueue($queueArray);

		$oldStyle = user::get()->getUserOption('only_available');

		$parse = array();
		$parse['buildlist'] = array();

		foreach ($elementIDs AS $Element)
		{
			$isAccess = IsTechnologieAccessible(app::$user->data, app::$planetrow->data, $Element);

			if (!$isAccess && $oldStyle)
				continue;

			if (!checkTechnologyRace(app::$user->data, $Element))
				continue;

			$row = array();

			$row['access']	= $isAccess;
			$row['i'] 		= $Element;
			$row['count'] 	= app::$planetrow->data[$resource[$Element]];
			$row['price'] 	= GetElementPrice(GetBuildingPrice(app::$user, app::$planetrow->data, $Element, false), app::$planetrow->data);

			if ($isAccess)
			{
				$row['time'] 		= GetBuildingTime(app::$user, app::$planetrow->data, $Element);
				$row['can_build'] = IsElementBuyable(app::$user, app::$planetrow->data, $Element, false);

				if ($row['can_build'])
				{
					$row['maximum'] = false;

					if (isset($pricelist[$Element]['max']))
					{
						$total = app::$planetrow->data[$resource[$Element]];

						if (isset($BuildArray[$Element]))
							$total += $BuildArray[$Element];

						if ($total >= $pricelist[$Element]['max'])
							$row['maximum'] = true;
					}

					$row['max'] = GetMaxConstructibleElements($Element, app::$planetrow->data, app::$user);
				}

				$row['add'] = GetNextProduction($Element, 0);
			}

			$parse['buildlist'][] = $row;
		}

		return $parse;
	}

	private function extractHangarQueue ($queue = '')
	{
		$result = array();

		if (is_array($queue) && count($queue))
		{
			foreach ($queue AS $element)
			{
				$result[$element['i']] = $element['l'];
			}
		}

		return $result;
	}

	private function ShowBuildingQueue ()
	{
		$queueManager = new queueManager(app::$planetrow->data['queue']);

		$ActualCount = $queueManager->getCount($queueManager::QUEUE_TYPE_BUILDING);

		$ListIDRow = array();

		if ($ActualCount != 0)
		{
			$PlanetID = app::$planetrow->data['id'];

			$QueueArray = $queueManager->get($queueManager::QUEUE_TYPE_BUILDING);

			foreach ($QueueArray AS $i => $item)
			{
				if ($item['e'] >= time())
				{
					$ListIDRow[] = Array
					(
						'ListID' 		=> ($i + 1),
						'ElementTitle' 	=> _getText('tech', $item['i']),
						'BuildLevel' 	=> $item['l'],
						'BuildMode' 	=> $item['d'],
						'BuildTime' 	=> ($item['e'] - time()),
						'PlanetID' 		=> $PlanetID,
						'BuildEndTime' 	=> $item['e']
					);
				}
			}
		}

		$RetValue['lenght'] 	= $ActualCount;
		$RetValue['buildlist'] 	= $ListIDRow;

		return $RetValue;
	}

	public function ElementBuildListBox ()
	{
		$queueManager = new queueManager(app::$planetrow->data['queue']);

		$ElementQueue = $queueManager->get($queueManager::QUEUE_TYPE_SHIPYARD);
		$NbrePerType = "";
		$NamePerType = "";
		$TimePerType = "";
		$QueueTime = 0;

		$parse = array();

		if (count($ElementQueue))
		{
			foreach ($ElementQueue as $queueArray)
			{
				$ElementTime = GetBuildingTime(app::$user, app::$planetrow->data, $queueArray['i']);

				$QueueTime += $ElementTime * $queueArray['l'];

				$TimePerType .= "" . $ElementTime . ",";
				$NamePerType .= "'" . html_entity_decode(_getText('tech', $queueArray['i'])) . "',";
				$NbrePerType .= "" . $queueArray['l'] . ",";
			}


			$parse['a'] = $NbrePerType;
			$parse['b'] = $NamePerType;
			$parse['c'] = $TimePerType;
			$parse['b_hangar_id_plus'] = $ElementQueue[0]['s'];

			$parse['time'] = strings::pretty_time($QueueTime - $ElementQueue[0]['s']);
		}

		$parse['count'] = count($ElementQueue);

		return $parse;
	}
}

?>