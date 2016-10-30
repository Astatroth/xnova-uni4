<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xcms\strings;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] < 3)
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

strings::includeLang('admin/fleets');

function BuildFlyingFleetTable ()
{
	$table = array();

	$FlyingFleets = db::query("SELECT * FROM game_fleets ORDER BY `fleet_end_time` ASC;");
	while ($CurrentFleet = db::fetch_assoc($FlyingFleets))
	{

		$Bloc['Id'] = $CurrentFleet['fleet_id'];
		$Bloc['Mission'] = CreateFleetPopupedMissionLink($CurrentFleet, _getText('type_mission', $CurrentFleet['fleet_mission']), '');
		$Bloc['Mission'] .= "<br>" . (($CurrentFleet['fleet_mess'] == 1) ? "R" : "A");

		$Bloc['Fleet'] = CreateFleetPopupedFleetLink($CurrentFleet, _getText('tech', 200), '');
		$Bloc['St_Owner'] = "[" . $CurrentFleet['fleet_owner'] . "]<br>" . $CurrentFleet['fleet_owner_name'];
		$Bloc['St_Posit'] = "[" . $CurrentFleet['fleet_start_galaxy'] . ":" . $CurrentFleet['fleet_start_system'] . ":" . $CurrentFleet['fleet_start_planet'] . "]<br>" . (($CurrentFleet['fleet_start_type'] == 1) ? "[P]" : (($CurrentFleet['fleet_start_type'] == 2) ? "D" : "L")) . "";
		$Bloc['St_Time'] = datezone('H:i:s d/n/Y', $CurrentFleet['fleet_start_time']);
		if (!empty($CurrentFleet['fleet_target_owner']))
		{
			$Bloc['En_Owner'] = "[" . $CurrentFleet['fleet_target_owner'] . "]<br>" . $CurrentFleet['fleet_target_owner_name'];
		}
		else
		{
			$Bloc['En_Owner'] = "";
		}
		$Bloc['En_Posit'] = "[" . $CurrentFleet['fleet_end_galaxy'] . ":" . $CurrentFleet['fleet_end_system'] . ":" . $CurrentFleet['fleet_end_planet'] . "]<br>" . (($CurrentFleet['fleet_end_type'] == 1) ? "[P]" : (($CurrentFleet['fleet_end_type'] == 2) ? "D" : "L")) . "";

		$Bloc['En_Time'] = datezone('H:i:s d/n/Y', $CurrentFleet['fleet_end_time']);

		$table[] = $Bloc;
	}
	return $table;
}

$this->setTemplate('fleets');
$this->set('flt_table', BuildFlyingFleetTable());
$this->setTitle(_getText('flt_title'));
$this->display();

?>