<?php

use Xcms\core;
use Xcms\strings;
use Xnova\User;
use Xnova\app;
use Xnova\queueManager;

function GetGameSpeedFactor ()
{
	return core::getConfig('fleet_speed') / 2500;
}

function GetTargetDistance ($OrigGalaxy, $DestGalaxy, $OrigSystem, $DestSystem, $OrigPlanet, $DestPlanet)
{
	if (($OrigGalaxy - $DestGalaxy) != 0)
		return abs($OrigGalaxy - $DestGalaxy) * 20000;

	if (($OrigSystem - $DestSystem) != 0)
		return abs($OrigSystem - $DestSystem) * 95 + 2700;

	if (($OrigPlanet - $DestPlanet) != 0)
		return abs($OrigPlanet - $DestPlanet) * 5 + 1000;

	return 5;
}

/**
 * @param int $fleetSpeedFactor скорость полёта, от 1 до 10
 * @param int $maxFleetSpeed
 * @param int $distance
 * @param float $gameFleetSpeed множитель скорости полётов
 * @return float
 */
function GetMissionDuration ($fleetSpeedFactor, $maxFleetSpeed, $distance, $gameFleetSpeed)
{
	if (!$fleetSpeedFactor || !$maxFleetSpeed || !$gameFleetSpeed || !$distance)
	{
		global $user;

		$user->sendMessage(1, false, 0, 0, 'cdv', '' . json_encode($_GET) . '---' . json_encode($_POST) . '');
	}

	return round(((35000 / $fleetSpeedFactor) * sqrt($distance * 10 / $maxFleetSpeed) + 10) / $gameFleetSpeed);
}

/**
 * @param  $FleetArray
 * @param  $Fleet
 * @param  $user user
 * @return array|int
 */
function GetFleetMaxSpeed ($FleetArray, $Fleet, $user)
{
	global $CombatCaps;

	$speedalls = array();

	if ($Fleet != 0)
	{
		$FleetArray[$Fleet] = 1;
	}

	foreach ($FleetArray as $Ship => $Count)
	{
		switch ($CombatCaps[$Ship]['type_engine'])
		{
			case 1:
				$speedalls[$Ship] = $CombatCaps[$Ship]['speed'] * (1 + ($user->data['combustion_tech'] * 0.1));
				break;
			case 2:
				$speedalls[$Ship] = $CombatCaps[$Ship]['speed'] * (1 + ($user->data['impulse_motor_tech'] * 0.2));
				break;
			case 3:
				$speedalls[$Ship] = $CombatCaps[$Ship]['speed'] * (1 + ($user->data['hyperspace_motor_tech'] * 0.3));
				break;
			default:
				$speedalls[$Ship] = $CombatCaps[$Ship]['speed'];
		}

		if ($user->bonusValue('fleet_speed') != 1)
			$speedalls[$Ship] = round($speedalls[$Ship] * $user->bonusValue('fleet_speed'));
	}

	if ($Fleet != 0)
		$speedalls = $speedalls[$Fleet];

	return $speedalls;
}

function SetShipsEngine ($user)
{
	global $CombatCaps, $reslist, $resource;

	foreach ($reslist['fleet'] as $Ship)
	{
		if (isset($CombatCaps[$Ship]) && isset($CombatCaps[$Ship]['engine_up']))
		{
			if ($user[$resource[$CombatCaps[$Ship]['engine_up']['tech']]] >= $CombatCaps[$Ship]['engine_up']['lvl'])
			{
				$CombatCaps[$Ship]['type_engine']++;
				$CombatCaps[$Ship]['speed'] = $CombatCaps[$Ship]['engine_up']['speed'];

				unset($CombatCaps[$Ship]['engine_up']);
			}
		}
	}
}

/**
 * @param  $Ship
 * @param  $user user
 * @return float
 */
function GetShipConsumption ($Ship, $user)
{
	global $CombatCaps;

	return ceil($CombatCaps[$Ship]['consumption'] * $user->bonusValue('fleet_fuel'));
}

function GetFleetConsumption ($FleetArray, $gameFleetSpeed, $MissionDuration, $MissionDistance, $Player)
{
	$consumption = 0;

	if ($MissionDuration <= 1)
		$MissionDuration = 2;

	foreach ($FleetArray as $Ship => $Count)
	{
		if ($Ship > 0)
		{
			$spd = 35000 / ($MissionDuration * $gameFleetSpeed - 10) * sqrt($MissionDistance * 10 / GetFleetMaxSpeed("", $Ship, $Player));

			$consumption += (GetShipConsumption($Ship, $Player) * $Count) * $MissionDistance / 35000 * (($spd / 10) + 1) * (($spd / 10) + 1);
		}
	}

	$consumption = round($consumption) + 1;

	return $consumption;
}

function GetFleetStay ($FleetArray)
{
	global $CombatCaps;

	$stay = 0;

	foreach ($FleetArray as $Ship => $Count)
	{
		if ($Ship > 0)
		{
			$stay += $CombatCaps[$Ship]['stay'] * $Count;
		}
	}

	return $stay;
}

function unserializeFleet ($fleetAmount)
{
	$fleetTyps = explode(';', $fleetAmount);

	$fleetAmount = array();

	foreach ($fleetTyps as $fleetTyp)
	{
		$temp = explode(',', $fleetTyp);

		if (empty($temp[0]))
			continue;

		if (!isset($fleetAmount[$temp[0]]))
		{
			$fleetAmount[$temp[0]] = array('cnt' => 0, 'lvl' => 0);
		}

		$lvl = explode("!", $temp[1]);

		$fleetAmount[$temp[0]]['cnt'] += $lvl[0];
		$fleetAmount[$temp[0]]['lvl']  = $lvl[1];
	}

	return $fleetAmount;
}

function CalculateMaxPlanetFields (&$planet)
{
	global $resource;

	return $planet["field_max"] + ($planet[$resource[33]] * 5) + (FIELDS_BY_MOONBASIS_LEVEL * $planet[$resource[41]]);
}

function ShowTopNavigationBar ()
{
	global $reslist, $resource;

	$parse = array();

	$parse['image'] = app::$planetrow->data['image'];
	$parse['name'] = app::$planetrow->data['name'];
	$parse['time'] = time();

	$parse['planetlist'] = '';

	if (core::getConfig('showPlanetListSelect', 0))
	{
		$planetsList = \Xcms\Cache::get('app::planetlist_'.user::get()->getId().'');

		if ($planetsList === false)
		{
			$planetsList = user::get()->getUserPlanets(app::$user->data['id']);

			\Xcms\Cache::set('app::planetlist_'.user::get()->getId().'', $planetsList, 300);
		}

		foreach ($planetsList AS $CurPlanet)
		{
			if ($CurPlanet['destruyed'] > 0)
				continue;

			$parse['planetlist'] .= "\n<option ";

			if ($CurPlanet['planet_type'] == 3)
				$parse['planetlist'] .= "style=\"color:red;\" ";
			elseif ($CurPlanet['planet_type'] == 5)
				$parse['planetlist'] .= "style=\"color:yellow;\" ";

			if ($CurPlanet['id'] == app::$user->data['current_planet'])
			{
				$parse['planetlist'] .= "selected=\"selected\" ";
			}
			if (isset($_GET['set']))
				$parse['planetlist'] .= "value=\"?set=" . $_GET['set'] . "";
			else
				$parse['planetlist'] .= "value=\"?set=overview";
			if (isset($_GET['mode']))
				$parse['planetlist'] .= "&amp;mode=" . $_GET['mode'];

			$parse['planetlist'] .= "&amp;cp=" . $CurPlanet['id'] . "&amp;re=0\">";

			$parse['planetlist'] .= "" . $CurPlanet['name'];
			$parse['planetlist'] .= "&nbsp;[" . $CurPlanet['galaxy'] . ":" . $CurPlanet['system'] . ":" . $CurPlanet['planet'];
			$parse['planetlist'] .= "]&nbsp;&nbsp;</option>";
		}
	}

	foreach ($reslist['res'] AS $res)
	{
		$parse[$res] = floor(app::$planetrow->data[$res]);

		$parse[$res.'_m'] = app::$planetrow->data[$res.'_max'];

		if (app::$planetrow->data[$res.'_max'] <= app::$planetrow->data[$res])
			$parse[$res.'_max'] = '<font class="full">';
		else
			$parse[$res.'_max'] = '<font color="#00ff00">';

		$parse[$res.'_max'] .= strings::pretty_number(app::$planetrow->data[$res.'_max']) . "</font>";
		$parse[$res.'_ph'] 	= app::$planetrow->data[$res.'_perhour'] + floor(core::getConfig($res.'_basic_income', 0) * core::getConfig('resource_multiplier', 1));
		$parse[$res.'_mp'] 	= app::$planetrow->data[$res.'_mine_porcent'] * 10;
	}

	$parse['energy_max'] 	= strings::pretty_number(app::$planetrow->data["energy_max"]);
	$parse['energy_total'] 	= strings::colorNumber(strings::pretty_number(app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"]));

	$parse['credits'] = strings::pretty_number(app::$user->data['credits']);

	$parse['officiers'] = array();

	foreach ($reslist['officier'] AS $officier)
	{
		$parse['officiers'][$officier] = app::$user->data[$resource[$officier]];
	}

	$parse['energy_ak'] = (app::$planetrow->data['battery_max'] > 0 ? round(app::$planetrow->data['energy_ak'] / app::$planetrow->data['battery_max'], 2) * 100 : 0);
	$parse['energy_ak'] = min(100, max(0, $parse['energy_ak']));

	$parse['ak'] = round(app::$planetrow->data['energy_ak']) . " / " . app::$planetrow->data['battery_max'];

	if ($parse['energy_ak'] > 0 && $parse['energy_ak'] < 100)
	{
		if ((app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"]) > 0)
			$parse['ak'] .= '<br>Заряд: ' . strings::pretty_time(round(((round(250 * app::$planetrow->data[$resource[4]]) - app::$planetrow->data['energy_ak']) / (app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"])) * 3600)) . '';
		elseif ((app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"]) < 0)
			$parse['ak'] .= '<br>Разряд: ' . strings::pretty_time(round((app::$planetrow->data['energy_ak'] / abs(app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"])) * 3600)) . '';
	}

	$parse['messages'] = app::$user->data['new_message'];

	if (app::$user->data['mnl_alliance'] > 0 && app::$user->data['ally_id'] == 0)
	{
		app::$user->data['mnl_alliance'] = 0;
		\Xcms\Sql::build()->update('game_users')->setField('mnl_alliance', 0)->where('id', '=', app::$user->data['id'])->execute();
	}

	$parse['ally_messages'] = (app::$user->data['ally_id'] != 0) ? app::$user->data['mnl_alliance'] : '';

	return $parse;
}

/**
 * @param  $CurrentUser user
 * @param  $CurrentPlanet
 * @param  $Element
 * @param bool $Incremental
 * @param bool $ForDestroy
 * @return bool
 */
function IsElementBuyable ($CurrentUser, $CurrentPlanet, $Element, $Incremental = true, $ForDestroy = false)
{
	$RetValue = true;

	$cost = GetBuildingPrice($CurrentUser, $CurrentPlanet, $Element, $Incremental, $ForDestroy);

	foreach ($cost AS $ResType => $ResCount)
	{
		if (!isset($CurrentPlanet[$ResType]) || $ResCount > $CurrentPlanet[$ResType])
		{
			$RetValue = false;
			break;
		}
	}

	return $RetValue;
}

function IsTechnologieAccessible ($user, $planet, $Element)
{
	global $requeriments, $resource;

	if (isset($requeriments[$Element]))
	{
		$enabled = true;
		foreach ($requeriments[$Element] as $ReqElement => $EleLevel)
		{
			if ($ReqElement == 700 && $user[$resource[$ReqElement]] != $EleLevel)
			{
				return false;
			}
			elseif (isset($user[$resource[$ReqElement]]) && $user[$resource[$ReqElement]] >= $EleLevel)
			{
				// break;
			}
			elseif (isset($planet[$resource[$ReqElement]]) && $planet[$resource[$ReqElement]] >= $EleLevel)
			{
				$enabled = true;
			}
			elseif (isset($planet['planet_type']) && $planet['planet_type'] == 5 && ($Element == 43 || $Element == 502 || $Element == 503) && ($ReqElement == 21 || $ReqElement == 41))
			{
				$enabled = true;
			}
			else
			{
				return false;
			}
		}
		return $enabled;
	}
	else
		return true;
}

function checkTechnologyRace ($user, $Element)
{
	global $requeriments, $resource;

	if (isset($requeriments[$Element]))
	{
		foreach ($requeriments[$Element] as $ReqElement => $EleLevel)
		{
			if ($ReqElement == 700 && $user[$resource[$ReqElement]] != $EleLevel)
			{
				return false;
			}
		}

		return true;
	}
	else
		return true;
}

function CheckLabSettingsInQueue ($CurrentPlanet)
{
	$queueManager = new queueManager($CurrentPlanet['queue']);

	if ($queueManager->getCount($queueManager::QUEUE_TYPE_BUILDING))
	{
		$BuildQueue = $queueManager->get($queueManager::QUEUE_TYPE_BUILDING);

		if ($BuildQueue[0]['i'] == 31 && core::getConfig('BuildLabWhileRun', 0) != 1)
			$return = false;
		else
			$return = true;
	}
	else
		$return = true;

	return $return;
}

function GetStartAdressLink ($FleetRow, $FleetType = '')
{
	$Link = "<a href=\"?set=galaxy&amp;r=3&amp;galaxy=" . $FleetRow['fleet_start_galaxy'] . "&amp;system=" . $FleetRow['fleet_start_system'] . "\" " . $FleetType . " >";
	$Link .= "[" . $FleetRow['fleet_start_galaxy'] . ":" . $FleetRow['fleet_start_system'] . ":" . $FleetRow['fleet_start_planet'] . "]</a>";
	return $Link;
}

function GetTargetAdressLink ($FleetRow, $FleetType = '')
{
	$Link = "<a href=\"?set=galaxy&amp;r=3&amp;galaxy=" . $FleetRow['fleet_end_galaxy'] . "&amp;system=" . $FleetRow['fleet_end_system'] . "\" " . $FleetType . " >";
	$Link .= "[" . $FleetRow['fleet_end_galaxy'] . ":" . $FleetRow['fleet_end_system'] . ":" . $FleetRow['fleet_end_planet'] . "]</a>";
	return $Link;
}

function BuildPlanetAdressLink ($CurrentPlanet)
{
	$Link = "<a href=\"?set=galaxy&amp;r=3&amp;galaxy=" . $CurrentPlanet['galaxy'] . "&amp;system=" . $CurrentPlanet['system'] . "\">";
	$Link .= "[" . $CurrentPlanet['galaxy'] . ":" . $CurrentPlanet['system'] . ":" . $CurrentPlanet['planet'] . "]</a>";
	return $Link;
}

function BuildHostileFleetPlayerLink ($FleetRow)
{
	$PlayerName = \Xcms\Db::query("SELECT `username` FROM game_users WHERE `id` = '" . $FleetRow['fleet_owner'] . "';", true);

	$Link = $PlayerName['username'] . " ";
	$Link .= "<a href=\"?set=messages&amp;mode=write&amp;id=" . $FleetRow['fleet_owner'] . "\" title=\"" . _getText('ov_message') . "\"><span class='sprite skin_m'></span></a>";

	return $Link;
}

function GetNextJumpWaitTime ($CurMoon)
{
	global $resource;

	$JumpGateLevel = $CurMoon[$resource[43]];
	$LastJumpTime = $CurMoon['last_jump_time'];
	if ($JumpGateLevel > 0)
	{
		$WaitBetweenJmp = (60 * 60) * (1 / $JumpGateLevel);
		$NextJumpTime = $LastJumpTime + $WaitBetweenJmp;
		if ($NextJumpTime >= time())
		{
			$RestWait = $NextJumpTime - time();
			$RestString = " " . strings::pretty_time($RestWait);
		}
		else
		{
			$RestWait = 0;
			$RestString = "";
		}
	}
	else
	{
		$RestWait = 0;
		$RestString = "";
	}
	$RetValue['string'] = $RestString;
	$RetValue['value'] = $RestWait;

	return $RetValue;
}

function InsertJavaScriptChronoApplet ($Type, $Ref, $Value)
{
	return "<script>FlotenTime('bxx" . $Type . $Ref . "', " . $Value . ");</script>";
}

function CreateFleetPopupedFleetLink ($FleetRow, $Texte, $FleetType)
{
	global $user;

	$FleetRec = explode(";", $FleetRow['fleet_array']);

	$FleetPopup = "<table width=200>";
	$r = 'javascript:;';
	$Total = 0;

	if ($FleetRow['fleet_owner'] != $user->data['id'] && $user->data['spy_tech'] < 2)
	{
		$FleetPopup .= "<tr><td width=100% align=center><font color=white>Нет информации<font></td></tr>";
	}
	elseif ($FleetRow['fleet_owner'] != $user->data['id'] && $user->data['spy_tech'] < 4)
	{
		foreach ($FleetRec as $Group)
		{
			if ($Group != '')
			{
				$Ship = explode(",", $Group);
				$Count = explode("!", $Ship[1]);
				$Total = $Total + $Count[0];
			}
		}
		$FleetPopup .= "<tr><td width=50% align=left><font color=white>Численность:<font></td><td width=50% align=right><font color=white>" . strings::pretty_number($Total) . "<font></td></tr>";
	}
	elseif ($FleetRow['fleet_owner'] != $user->data['id'] && $user->data['spy_tech'] < 8)
	{
		foreach ($FleetRec as $Group)
		{
			if ($Group != '')
			{
				$Ship = explode(",", $Group);
				$Count = explode("!", $Ship[1]);
				$Total = $Total + $Count[0];
				$FleetPopup .= "<tr><td width=100% align=center colspan=2><font color=white>" . _getText('tech', $Ship[0]) . "<font></td></tr>";
			}
		}
		$FleetPopup .= "<tr><td width=50% align=left><font color=white>Численность:<font></td><td width=50% align=right><font color=white>" . strings::pretty_number($Total) . "<font></td></tr>";
	}
	else
	{
		if ($FleetRow['fleet_target_owner'] == $user->data['id'] && $FleetRow['fleet_mission'] == 1)
			$r = '?set=sim&r=';

		foreach ($FleetRec as $Group)
		{
			if ($Group != '')
			{
				$Ship = explode(",", $Group);
				$Count = explode("!", $Ship[1]);
				$FleetPopup .= "<tr><td width=75% align=left><font color=white>" . _getText('tech', $Ship[0]) . ":<font></td><td width=25% align=right><font color=white>" . strings::pretty_number($Count[0]) . "<font></td></tr>";

				if ($r != 'javascript:;')
					$r .= $Group . ';';
			}
		}
	}

	$FleetPopup .= "</table>";
	$FleetPopup .= "' class=\"" . $FleetType . "\">" . $Texte . "</a>";

	$FleetPopup = "<a href='" . $r . "' class=\"tooltip\" data-tooltip-content='" . $FleetPopup;

	return $FleetPopup;

}

function CreateFleetPopupedMissionLink ($FleetRow, $Texte, $FleetType)
{
	$FleetTotalC = $FleetRow['fleet_resource_metal'] + $FleetRow['fleet_resource_crystal'] + $FleetRow['fleet_resource_deuterium'];

	if ($FleetTotalC != 0)
	{
		$FRessource = "<table width=200>";
		$FRessource .= "<tr><td width=50% align=left><font color=white>" . _getText('Metal') . "<font></td><td width=50% align=right><font color=white>" . strings::pretty_number($FleetRow['fleet_resource_metal']) . "<font></td></tr>";
		$FRessource .= "<tr><td width=50% align=left><font color=white>" . _getText('Crystal') . "<font></td><td width=50% align=right><font color=white>" . strings::pretty_number($FleetRow['fleet_resource_crystal']) . "<font></td></tr>";
		$FRessource .= "<tr><td width=50% align=left><font color=white>" . _getText('Deuterium') . "<font></td><td width=50% align=right><font color=white>" . strings::pretty_number($FleetRow['fleet_resource_deuterium']) . "<font></td></tr>";
		$FRessource .= "</table>";
	}
	else
	{
		$FRessource = "";
	}

	if ($FRessource <> "")
	{
		$MissionPopup = "<a href='javascript:;' data-tooltip-content='" . $FRessource . "' class=\"tooltip " . $FleetType . "\">" . $Texte . "</a>";
	}
	else
	{
		$MissionPopup = $Texte . "";
	}

	return $MissionPopup;
}

function getTechTree ($Element)
{
	global $requeriments, $resource, $planetrow;

	$result = '';

	if (isset($requeriments[$Element]))
	{
		$result = "";

		foreach ($requeriments[$Element] as $ResClass => $Level)
		{
			if ($ResClass != 700)
			{
				if (isset(app::$user->data[$resource[$ResClass]]) && app::$user->data[$resource[$ResClass]] >= $Level)
				{
					$result .= "<span class=\"positive\">";
				}
				elseif (isset(app::$planetrow->data[$resource[$ResClass]]) && app::$planetrow->data[$resource[$ResClass]] >= $Level)
				{
					$result .= "<span class=\"positive\">";
				}
				else
				{
					$result .= "<span class=\"negative\">";
				}
				$result .= _getText('tech', $ResClass) . " (" . _getText('level') . " " . $Level . "";

				if (isset(app::$user->data[$resource[$ResClass]]) && app::$user->data[$resource[$ResClass]] < $Level)
				{
					$minus = $Level - app::$user->data[$resource[$ResClass]];
					$result .= " + <b>" . $minus . "</b>";
				}
				elseif (isset(app::$planetrow->data[$resource[$ResClass]]) && app::$planetrow->data[$resource[$ResClass]] < $Level)
				{
					$minus = $Level - app::$planetrow->data[$resource[$ResClass]];
					$result .= " + <b>" . $minus . "</b>";
				}
			}
			else
			{
				$result .= _getText('tech', $ResClass) . " (";

				if (app::$user->data['race'] != $Level)
					$result .= "<span class=\"negative\">" . _getText('race', $Level);
				else
					$result .= "<span class=\"positive\">" . _getText('race', $Level);
			}

			$result .= ")</span><br>";
		}
	}

	return $result;
}

/**
 * @param  $user user
 * @param  $planet array
 * @param  $Element integer
 * @return int
 */
function GetBuildingTime ($user, $planet, $Element)
{
	global $resource, $reslist;

	$time = 0;

	$cost = GetBuildingPrice($user, $planet, $Element, !(in_array($Element, $reslist['defense']) || in_array($Element, $reslist['fleet'])), false, false);
	$cost = $cost['metal'] + $cost['crystal'];

	if (in_array($Element, $reslist['build']))
	{
		$time = ($cost / core::getConfig('game_speed')) * (1 / ($planet[$resource['14']] + 1)) * pow(0.5, $planet[$resource['15']]);
		$time = floor($time * 3600 * $user->bonusValue('time_building'));
	}
	elseif (in_array($Element, $reslist['tech']) || in_array($Element, $reslist['tech_f']))
	{
		if (isset($planet['spaceLabs']) && count($planet['spaceLabs']))
		{
			$lablevel = 0;

			global $requeriments;

			foreach ($planet['spaceLabs'] as $Levels)
			{
				if (!isset($requeriments[$Element][31]) || $Levels >= $requeriments[$Element][31])
					$lablevel += $Levels;
			}
		}
		else
			$lablevel = $planet[$resource['31']];

		$time = ($cost / core::getConfig('game_speed')) / (($lablevel + 1) * 2);
		$time = floor($time * 3600 * $user->bonusValue('time_research'));
	}
	elseif (in_array($Element, $reslist['defense']))
	{
		$time = ($cost / core::getConfig('game_speed')) * (1 / ($planet[$resource['21']] + 1)) * pow(1 / 2, $planet[$resource['15']]);
		$time = floor($time * 3600 * $user->bonusValue('time_defence'));
	}
	elseif (in_array($Element, $reslist['fleet']))
	{
		$time = ($cost / core::getConfig('game_speed')) * (1 / ($planet[$resource['21']] + 1)) * pow(1 / 2, $planet[$resource['15']]);
		$time = floor($time * 3600 * $user->bonusValue('time_fleet'));
	}

	if ($time < 1)
		$time = 1;

	return $time;
}

/**
 * @param $cost array
 * @param  $planet array
 * @return string
 */
function GetElementPrice ($cost, $planet)
{
	$array = array(
		'metal' 	=> array(_getText('Metal'), 'metall'),
		'crystal' 	=> array(_getText('Crystal'), 'kristall'),
		'deuterium' => array(_getText('Deuterium'), 'deuterium'),
		'energy_max'=> array(_getText('Energy'), 'energie')
	);

	$text = "";

	foreach ($array as $ResType => $ResTitle)
	{
		if (isset($cost[$ResType]) && $cost[$ResType] != 0)
		{
			$text .= "<div><img src='" . DPATH . "images/s_" . $ResTitle[1] . ".png' align=\"absmiddle\" class=\"tooltip\" data-tooltip-content='" . $ResTitle[0] . "'>";

			if ($cost[$ResType] > $planet[$ResType])
			{
				$text .= "<span class=\"resNo tooltip\" data-tooltip-content=\"необходимо: ".strings::pretty_number($cost[$ResType] - $planet[$ResType])."\">" . strings::pretty_number($cost[$ResType]) . "</span> ";
			}
			else
			{
				$text .= "<span class=\"resYes\">" . strings::pretty_number($cost[$ResType]) . "</span> ";
			}
			$text .= "</div>";
		}
	}

	return $text;
}

/**
 * @param $user user
 * @param $planet array
 * @param $Element
 * @param bool $Incremental
 * @param bool $ForDestroy
 * @param bool $withBonus
 * @return array
 */
function GetBuildingPrice ($user, $planet, $Element, $Incremental = true, $ForDestroy = false, $withBonus = true)
{
	global $pricelist, $resource, $reslist;

	if ($Incremental)
		$level = (isset($planet[$resource[$Element]])) ? $planet[$resource[$Element]] : $user->data[$resource[$Element]];
	else
		$level = 0;

	$array 	= array('metal', 'crystal', 'deuterium', 'energy_max');
	$cost 	= array();

	foreach ($array as $ResType)
	{
		if (!isset($pricelist[$Element][$ResType]))
			continue;

		if ($Incremental)
			$cost[$ResType] = floor($pricelist[$Element][$ResType] * pow($pricelist[$Element]['factor'], $level));
		else
			$cost[$ResType] = floor($pricelist[$Element][$ResType]);

		if ($withBonus)
		{
			if (in_array($Element, $reslist['build']))
				$cost[$ResType] = round($cost[$ResType] * $user->bonusValue('res_building'));
			elseif (in_array($Element, $reslist['tech']))
				$cost[$ResType] = round($cost[$ResType] * $user->bonusValue('res_research'));
			elseif (in_array($Element, $reslist['tech_f']))
				$cost[$ResType] = round($cost[$ResType] * $user->bonusValue('res_levelup'));
			elseif (in_array($Element, $reslist['fleet']))
				$cost[$ResType] = round($cost[$ResType] * $user->bonusValue('res_fleet'));
			elseif (in_array($Element, $reslist['defense']))
				$cost[$ResType] = round($cost[$ResType] * $user->bonusValue('res_defence'));
		}

		if ($ForDestroy)
			$cost[$ResType] = floor($cost[$ResType] / 2);
	}

	return $cost;
}

/**
 * @param int $Element
 * @param int $Level
 * @return string
 */
function GetNextProduction ($Element, $Level)
{
	$Res = array();

	$resFrom = app::$planetrow->getProductionLevel($Element, ($Level + 1));

	$Res['m'] = $resFrom['metal'];
	$Res['c'] = $resFrom['crystal'];
	$Res['d'] = $resFrom['deuterium'];
	$Res['e'] = $resFrom['energy'];

	$resTo = app::$planetrow->getProductionLevel($Element, $Level);

	$Res['m'] -= $resTo['metal'];
	$Res['c'] -= $resTo['crystal'];
	$Res['d'] -= $resTo['deuterium'];
	$Res['e'] -= $resTo['energy'];

	$text = '';

	if ($Res['m'] != 0)
		$text .= "<br>Металл: <span class=" . (($Res['m'] > 0) ? 'positive' : 'negative') . ">" . (($Res['m'] > 0) ? '+' : '') . $Res['m'] . "</span>";

	if ($Res['c'] != 0)
		$text .= "<br>Кристалл:  <span class=" . (($Res['c'] > 0) ? 'positive' : 'negative') . ">" . (($Res['c'] > 0) ? '+' : '') . $Res['c'] . "</span>";

	if ($Res['d'] != 0)
		$text .= "<br>Дейтерий:  <span class=" . (($Res['d'] > 0) ? 'positive' : 'negative') . ">" . (($Res['d'] > 0) ? '+' : '') . $Res['d'] . "</span>";

	if ($Res['e'] != 0)
		$text .= "<br>Энергия:  <span class=" . (($Res['e'] > 0) ? 'positive' : 'negative') . ">" . (($Res['e'] > 0) ? '+' : '') . $Res['e'] . "</span>";

	return $text;
}

function getFleetMissions ($fleetArray, $target = array(1, 1, 1, 1), $isYouPlanet = false, $isActivePlanet = false, $isAcs = false)
{
	$result = array();

	if ($target[2] == 16)
	{
		if (!(count($fleetArray) == 1 && isset($fleetArray[210])))
			$result[15] = _getText('type_mission', 15);
	}
	else
	{
		if ($target[3] == 2 && isset($fleetArray[209]))
			$result[8] = _getText('type_mission', 8); // Переработка
		elseif ($target[3] == 1 || $target[3] == 3 || $target[3] == 5)
		{
			if (isset($fleetArray[216]) && !$isActivePlanet && $target[3] == 1)
				$result[10] = _getText('type_mission', 10); // Создать базу

			if (isset($fleetArray[210]) && !$isYouPlanet)
				$result[6] = _getText('type_mission', 6); // Шпионаж

			if (isset($fleetArray[208]) && !$isActivePlanet)
				$result[7] = _getText('type_mission', 7); // Колонизировать

			if (!$isYouPlanet && $isActivePlanet && !isset($fleetArray[208]) && !isset($fleetArray[209]) && !isset($fleetArray[216]))
				$result[1] = _getText('type_mission', 1); // Атаковать

			if ($isActivePlanet && !$isYouPlanet && !(count($fleetArray) == 1 && isset($fleetArray[210])))
				$result[5] = _getText('type_mission', 5); // Удерживать

			if (isset($fleetArray[202]) || isset($fleetArray[203]))
				$result[3] = _getText('type_mission', 3); // Транспорт

			if ($isYouPlanet)
				$result[4] = _getText('type_mission', 4); // Оставить

			if ($isAcs > 0 && $isActivePlanet)
				$result[2] = _getText('type_mission', 2); // Объединить

			if ($target[3] == 3 && isset($fleetArray[214]) && !$isYouPlanet && $isActivePlanet)
				$result[9] = _getText('type_mission', 9);
		}
	}

	return $result;
}

/**
 * @param $Element int
 * @param $Count int
 * @param $user user
 * @return mixed
 */
function GetElementRessources ($Element, $Count, $user)
{
	global $pricelist, $reslist;

	$ResType['metal'] 		= ($pricelist[$Element]['metal'] * $Count);
	$ResType['crystal'] 	= ($pricelist[$Element]['crystal'] * $Count);
	$ResType['deuterium'] 	= ($pricelist[$Element]['deuterium'] * $Count);

	foreach ($ResType AS &$cost)
	{
		if (in_array($Element, $reslist['fleet']))
			$cost = round($cost * $user->bonusValue('res_fleet'));
		elseif (in_array($Element, $reslist['defense']))
			$cost = round($cost * $user->bonusValue('res_defence'));
	}

	return $ResType;
}

/**
 * @param $Element int
 * @param $Ressources array
 * @param $user user
 * @return float|int
 */
function GetMaxConstructibleElements ($Element, $Ressources, $user)
{
	global $pricelist, $reslist;

	$MaxElements = -1;

	foreach ($pricelist[$Element] AS $need_res => $need_count)
	{
		if (in_array($need_res, array('metal', 'crystal', 'deuterium', 'energy_max')) && $need_count != 0)
		{
			$count = 0;

			if (in_array($Element, $reslist['fleet']))
				$count = round($need_count * $user->bonusValue('res_fleet'));
			elseif (in_array($Element, $reslist['defense']))
				$count = round($need_count * $user->bonusValue('res_defence'));

			$count = floor($Ressources[$need_res] / $count);

			if ($MaxElements == -1)
				$MaxElements = $count;
			elseif ($MaxElements > $count)
				$MaxElements = $count;
		}
	}

	if (isset($pricelist[$Element]['max']) && $MaxElements > $pricelist[$Element]['max'])
		$MaxElements = $pricelist[$Element]['max'];

	return $MaxElements;
}

?>