<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Xcms\db;
use Xcms\strings;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

strings::includeLang('fleet');

$BoxTitle = _getText('fl_error');
$TxtColor = "red";
$BoxMessage = _getText('fl_notback');

if (is_numeric($_POST['fleetid']))
{
	$fleetid = intval($_POST['fleetid']);

	$FleetRow = db::query("SELECT * FROM game_fleets WHERE `fleet_id` = '" . $fleetid . "';", true);
	$i = 0;

	if ($FleetRow['fleet_owner'] == user::get()->data['id'])
	{
		if (($FleetRow['fleet_mess'] == 0 || ($FleetRow['fleet_mess'] == 3 && $FleetRow['fleet_mission'] != 15) && $FleetRow['fleet_mission'] != 20 && $FleetRow['fleet_target_owner'] != 1))
		{
			if ($FleetRow['fleet_end_stay'] != 0)
			{

				if ($FleetRow['fleet_start_time'] > time())
					$CurrentFlyingTime = time() - $FleetRow['start_time'];
				else
					$CurrentFlyingTime = $FleetRow['fleet_start_time'] - $FleetRow['start_time'];
			}
			else
				$CurrentFlyingTime = time() - $FleetRow['start_time'];

			$ReturnFlyingTime = $CurrentFlyingTime + time();

			$QryUpdateFleet = "UPDATE game_fleets SET ";
			$QryUpdateFleet .= "`fleet_start_time` = '" . (time() - 1) . "', ";
			$QryUpdateFleet .= "`fleet_end_stay` = '0', ";
			$QryUpdateFleet .= "`fleet_end_time` = '" . ($ReturnFlyingTime + 1) . "', ";
			$QryUpdateFleet .= "`fleet_target_owner` = '" . user::get()->data['id'] . "', ";
			$QryUpdateFleet .= "`fleet_group` = 0, ";
			$QryUpdateFleet .= "fleet_time = fleet_end_time, `fleet_mess` = '1' ";
			$QryUpdateFleet .= "WHERE ";
			$QryUpdateFleet .= "`fleet_id` = '" . $fleetid . "';";
			db::query($QryUpdateFleet);

			if ($FleetRow['fleet_group'] != 0 && $FleetRow['fleet_mission'] == 1)
			{
				db::query("DELETE FROM game_aks WHERE id = " . $FleetRow['fleet_group'] . ";");
				db::query("DELETE FROM game_aks_user WHERE aks_id = " . $FleetRow['fleet_group'] . ";");
			}

			$BoxTitle = _getText('fl_sback');
			$TxtColor = "lime";
			$BoxMessage = _getText('fl_isback');
		}
		else
		{
			$BoxMessage = _getText('fl_notback');
		}
	}
	else
	{
		$BoxMessage = _getText('fl_onlyyours');
	}
}

$this->message("<font color=\"" . $TxtColor . "\">" . $BoxMessage . "</font>", $BoxTitle, "?set=fleet", 2);

?>