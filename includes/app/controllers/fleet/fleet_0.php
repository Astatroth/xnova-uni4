<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * @var $this \Xnova\pageHelper
 * @var $user user
 * @var $resource array
 * @var $reslist array
 * @var $CombatCaps array
 * @var app::$planetrow planet
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Xcms\db;
use Xcms\strings;
use Xnova\User;
use Xnova\app;

if (!defined("INSIDE"))
	die("attemp hacking");

if (!app::$planetrow)
	$this->message(_getText('fl_noplanetrow'), _getText('fl_error'));

$MaxFlyingFleets = db::first(db::query("SELECT COUNT(fleet_owner) AS `actcnt` FROM game_fleets WHERE `fleet_owner` = '" . user::get()->data['id'] . "';", true));

$MaxExpedition = user::get()->data[$resource[124]];
$ExpeditionEnCours = 0;
$EnvoiMaxExpedition = 0;

if ($MaxExpedition >= 1)
{
	$ExpeditionEnCours = db::first(db::query("SELECT COUNT(fleet_owner) AS `expedi` FROM game_fleets WHERE `fleet_owner` = '" . user::get()->data['id'] . "' AND `fleet_mission` = '15';", true));
	$EnvoiMaxExpedition = 1 + floor($MaxExpedition / 3);
}

$MaxFlottes = 1 + user::get()->data[$resource[108]];
if (user::get()->data['rpg_admiral'] > time())
	$MaxFlottes += 2;

strings::includeLang('fleet');

$galaxy = (isset($_GET['galaxy'])) ? intval($_GET['galaxy']) : 0;
$system = (isset($_GET['system'])) ? intval($_GET['system']) : 0;
$planet = (isset($_GET['planet'])) ? intval($_GET['planet']) : 0;
$planettype = (isset($_GET['planettype'])) ? intval($_GET['planettype']) : 0;
$target_mission = (isset($_GET['target_mission'])) ? intval($_GET['target_mission']) : 0;

if (!$galaxy)
	$galaxy = app::$planetrow->data['galaxy'];

if (!$system)
	$system = app::$planetrow->data['system'];

if (!$planet)
	$planet = app::$planetrow->data['planet'];

if (!$planettype)
	$planettype = 1;

$parse = array();
$parse['maxFlyingFleets'] = $MaxFlyingFleets;
$parse['maxFlottes'] = $MaxFlottes;
$parse['currentExpeditions'] = $ExpeditionEnCours;
$parse['maxExpeditions'] = $EnvoiMaxExpedition;
$parse['galaxy'] = $galaxy;
$parse['system'] = $system;
$parse['planet'] = $planet;
$parse['planettype'] = $planettype;
$parse['mission'] = $target_mission;

$fq = db::query("SELECT * FROM game_fleets WHERE fleet_owner=" . user::get()->data['id'] . "");

$parse['fleets'] = array();

while ($f = db::fetch($fq))
{
	$f['fleet_count'] = 0;

	$fleetArray = unserializeFleet($f['fleet_array']);

	foreach ($fleetArray as $fleetId => $fleetData)
		$f['fleet_count'] += $fleetData['cnt'];

	$f['fleet_array'] = $fleetArray;

	$parse['fleets'][]= $f;
}

$parse['mission_text'] = '';

if ($target_mission > 0)
	$parse['mission_text'] = ' для миссии "' . _getText('type_mission', $target_mission) . '"';
if (($system > 0 && $galaxy > 0 && $planet > 0) && ($galaxy != app::$planetrow->data['galaxy'] || $system != app::$planetrow->data['system'] || $planet != app::$planetrow->data['planet']))
	$parse['mission_text'] = ' на координаты [' . $galaxy . ':' . $system . ':' . $planet . ']';

$parse['ships'] = array();

foreach ($reslist['fleet'] as $n => $i)
{
	if (app::$planetrow->data[$resource[$i]] > 0)
	{
		$ship = array
		(
			'id' => $i,
			'count' => app::$planetrow->data[$resource[$i]],
			'consumption' => GetShipConsumption($i, user::get()),
			'speed' => GetFleetMaxSpeed("", $i, user::get())
		);

		if (isset(user::get()->data['fleet_' . $i]) && isset($CombatCaps[$i]['power_consumption']) && $CombatCaps[$i]['power_consumption'] > 0)
			$ship['capacity'] = round($CombatCaps[$i]['capacity'] * (1 + user::get()->data['fleet_' . $i] * ($CombatCaps[$i]['power_consumption'] / 100)));
		else
			$ship['capacity'] = $CombatCaps[$i]['capacity'];

		$parse['ships'][] = $ship;
	}

	$have_ships = true;
}

$this->setTemplate('fleet/stage_0');
$this->set('parse', $parse);
$this->setTitle(_getText('fl_title_0'));
$this->display();

?>