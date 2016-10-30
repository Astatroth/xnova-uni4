<?php

namespace Xnova\missions;

use Xnova\User;
use Xnova\fleet_engine;

class MissionCaseStayAlly extends fleet_engine implements Mission
{
	function __construct($Fleet)
	{
			$this->_fleet = $Fleet;
	}

	public function TargetEvent()
	{
		$this->StayFleet();

		$Message = sprintf(_getText('sys_stay_mess_user'),
					$this->_fleet['fleet_owner_name'], GetStartAdressLink($this->_fleet, ''),
					$this->_fleet['fleet_target_owner_name'], GetTargetAdressLink($this->_fleet, ''));

		user::get()->sendMessage($this->_fleet['fleet_owner'], 0, $this->_fleet['fleet_start_time'], 0, _getText('sys_mess_tower'), $Message);
	}

	public function EndStayEvent()
	{
		$this->ReturnFleet();
	}

	public function ReturnEvent()
	{
		$this->RestoreFleetToPlanet();
		$this->KillFleet();
	}
}

?>