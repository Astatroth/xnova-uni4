<?php

namespace Xnova\missions;

use Xcms\core;
use Xcms\db;
use Xcms\sql;
use Xcms\strings;
use Xnova\User;
use Xnova\fleet_engine;
use Xnova\planet;
use Xnova\system;

class MissionCaseAttack extends fleet_engine implements Mission
{
	public $usersTech = array();
	public $usersInfo = array();

	function __construct($Fleet)
	{
			$this->_fleet = $Fleet;
	}

	public function TargetEvent()
	{
		global $resource, $CombatCaps, $reslist;
		
		$target = new planet();
		$target->load_from_coords($this->_fleet['fleet_end_galaxy'], $this->_fleet['fleet_end_system'], $this->_fleet['fleet_end_planet'], $this->_fleet['fleet_end_type']);

		if (!isset($target->data['id']) || !$target->data['id_owner'] || $target->data['destruyed'] > 0)
		{
			$this->ReturnFleet();

			return false;
		}

		$owner = user::get()->getById($this->_fleet['fleet_owner'], Array('id', 'username', 'military_tech', 'defence_tech', 'shield_tech', 'laser_tech', 'ionic_tech', 'buster_tech', 'rpg_admiral', 'rpg_komandir'));

		if (!isset($owner['id']))
		{
			$this->ReturnFleet();

			return false;
		}

		$targetUser = new user;
		$targetUser->load_from_id($target->data['id_owner']);

		if (!isset($targetUser->data['id']))
		{
			$this->ReturnFleet();

			return false;
		}

		$target->load_user_info($targetUser);
		$target->PlanetResourceUpdate($this->_fleet['fleet_start_time']);

		core::loadLib('opbe');

		$attackers = new \PlayerGroup();
		$defenders = new \PlayerGroup();

		$this->getGroupFleet($this->_fleet, $attackers);

		if ($this->_fleet['fleet_group'] != 0)
		{
			$fleets = db::query('SELECT * FROM game_fleets WHERE fleet_id != ' . $this->_fleet['fleet_id'] . ' AND fleet_group = ' . $this->_fleet['fleet_group']);

			while ($fleet = db::fetch($fleets))
			{
				$this->getGroupFleet($fleet, $attackers);
			}
		}

		$def = db::query('SELECT * FROM game_fleets WHERE `fleet_end_galaxy` = ' . $this->_fleet['fleet_end_galaxy'] . ' AND `fleet_end_system` = ' . $this->_fleet['fleet_end_system'] . ' AND `fleet_end_type` = ' . $this->_fleet['fleet_end_type'] . ' AND `fleet_end_planet` = ' . $this->_fleet['fleet_end_planet'] . ' AND fleet_mess = 3');

		while ($fleet = db::fetch_assoc($def))
		{
			$this->getGroupFleet($fleet, $defenders);
		}

		$res = array();

		for ($i = 200; $i < 500; $i++)
		{
			if (isset($resource[$i]) && isset($target->data[$resource[$i]]) && $target->data[$resource[$i]] > 0)
			{
				$res[$i] = $target->data[$resource[$i]];

				$l = $i > 400 ? ($i - 50) : ($i + 100);

				if (isset($resource[$l]) && isset($targetUser->data[$resource[$l]]) && $targetUser->data[$resource[$l]] > 0)
					$res[$l] = $targetUser->data[$resource[$l]];
			}
		}

		if ($targetUser->data['rpg_komandir'] > time())
		{
			$targetUser->data['military_tech'] 	+= 2;
			$targetUser->data['defence_tech'] 	+= 2;
			$targetUser->data['shield_tech'] 	+= 2;
		}

		foreach ($reslist['tech'] AS $techId)
		{
			if (isset($targetUser->data[$resource[$techId]]) && $targetUser->data[$resource[$techId]] > 0)
				$res[$techId] = $targetUser->data[$resource[$techId]];
		}

		$this->usersTech[$targetUser->data['id']] = $res;

		$homeFleet = new \HomeFleet(0);

		for ($i = 200; $i < 500; $i++)
		{
			if (isset($resource[$i]) && isset($target->data[$resource[$i]]) && $target->data[$resource[$i]] > 0)
			{
				$l = $i > 400 ? ($i - 50) : ($i + 100);

				$homeFleet->add($this->getShipType($i, array($target->data[$resource[$i]], (isset($resource[$l]) && isset($targetUser->data[$resource[$l]]) ? $targetUser->data[$resource[$l]] : 0)), $res));
			}
		}

		if (!$defenders->existPlayer($targetUser->data['id']))
		{
			$player = new \Player($targetUser->data['id'], array($homeFleet));
			$player->setTech(0, 0, 0);
			$player->setName($targetUser->data['username']);
			$defenders->addPlayer($player);

			if (!isset($this->usersInfo[$targetUser->data['id']]))
				$this->usersInfo[$targetUser->data['id']] = array();

			$this->usersInfo[$targetUser->data['id']][0] = array
			(
				'galaxy' => $target->data['galaxy'],
				'system' => $target->data['system'],
				'planet' => $target->data['planet']
			);
		}
		else
			$defenders->getPlayer($targetUser->data['id'])->addDefense($homeFleet);

		if ($targetUser->data['rpg_ingenieur'])
			core::setConfig('repairDefenceFactor', 0.8);
		else
			core::setConfig('repairDefenceFactor', 0.7);

		if (!$this->_fleet['raunds'])
			$this->_fleet['raunds'] = 6;

		$engine = new \Battle($attackers, $defenders, $this->_fleet['raunds']);
		$report = $engine->getReport();
		$result = array('version' => 2, 'time' => time(), 'rw' => array());

		$attackUsers 	= $this->convertPlayerGroupToArray($report->getResultAttackersFleetOnRound('START'));
		$defenseUsers 	= $this->convertPlayerGroupToArray($report->getResultDefendersFleetOnRound('START'));

		for ( $_i = 0; $_i <= $report->getLastRoundNumber(); $_i++)
		{
			$result['rw'][] = $this->convertRoundToArray($report->getRound($_i));
		}

		$result['won'] = 0;

		if ($report->attackerHasWin())
			$result['won'] = 1;
		if ($report->defenderHasWin())
			$result['won'] = 2;
		if ($report->isAdraw())
			$result['won'] = 0;

		$result['lost'] = array('att' => $report->getTotalAttackersLostUnits(), 'def' => $report->getTotalDefendersLostUnits());

		$debris = $report->getDebris();

		$result['debree']['att'] = $debris;
		$result['debree']['def'] = array(0,0);

		$attackFleets 	= $this->getResultFleetArray($report->getPresentationAttackersFleetOnRound('START'), $report->getAfterBattleAttackers());
		$defenseFleets 	= $this->getResultFleetArray($report->getPresentationDefendersFleetOnRound('START'), $report->getAfterBattleDefenders());

		$repairFleets = array();

		foreach ($report->getDefendersRepaired() as $_player)
		{
			foreach ($_player as $_idFleet => $_fleet)
			{
				/**
				 * @var \ShipType $_ship
				 */
				foreach($_fleet as $_shipID => $_ship)
				{
					$repairFleets[$_idFleet][$_shipID] = $_ship->getCount();
				}
			}
		}

		$fleetToUser = array();

		foreach ($report->getPresentationAttackersFleetOnRound('START') as $idPlayer => $player)
		{
			/**
			 * @var $player \Player
			 */
			foreach ($player->getIterator() as $idFleet => $fleet)
			{
				$fleetToUser[$idFleet] = $idPlayer;
			}
		}

		$steal = array('metal' => 0, 'crystal' => 0, 'deuterium' => 0);

		if ($result['won'] == 1)
		{
			$max_resources = 0;
			$max_fleet_res = array();

			foreach ($attackFleets AS $fleet => $arr)
			{
				$max_fleet_res[$fleet] = 0;

				foreach ($arr as $Element => $amount)
				{
					if ($Element == 210)
						continue;

					if (isset($attackUsers[$fleetToUser[$fleet]]['flvl'][$Element]) && isset($CombatCaps[$Element]['power_consumption']) && $CombatCaps[$Element]['power_consumption'] > 0)
						$capacity = $CombatCaps[$Element]['capacity'] * $amount * (1 + $attackUsers[$fleetToUser[$fleet]]['flvl'][$Element] * ($CombatCaps[$Element]['power_consumption'] / 100));
					else
						$capacity = $CombatCaps[$Element]['capacity'] * $amount;

					$max_resources += $capacity;
					$max_fleet_res[$fleet] += $capacity;
				}
			}

			$res_correction = $max_resources;
			$res_procent = array();

			if ($max_resources > 0)
			{
				foreach ($max_fleet_res AS $id => $res)
				{
					$res_procent[$id] = $max_fleet_res[$id] / $res_correction;
				}
			}

			$steal = $this->getSteal($target->data, $max_resources);
		}

		$totalDebree = $result['debree']['def'][0] + $result['debree']['def'][1] + $result['debree']['att'][0] + $result['debree']['att'][1];

		if ($totalDebree > 0)
		{
			sql::build()->update('game_planets')->set(Array('+debris_metal' => ($result['debree']['att'][0] + $result['debree']['def'][0]), '+debris_crystal' => ($result['debree']['att'][1] + $result['debree']['def'][1])))
					->where('galaxy', '=', $target->data['galaxy'])->addAND()
					->where('system', '=', $target->data['system'])->addAND()
					->where('planet', '=', $target->data['planet'])->addAND()
					->where('planet_type', '!=', 3)->execute();
		}
		
		foreach ($attackFleets as $fleetID => $attacker)
		{
			$fleetArray = '';
			$totalCount = 0;

			foreach ($attacker as $element => $amount)
			{
				if (!is_numeric($element) || !$amount)
					continue;

				$fleetArray .= $element . ',' . $amount . '!0;';
				$totalCount += $amount;
			}

			if ($totalCount <= 0)
				$this->KillFleet($fleetID);
			else
			{
				sql::build()->update('game_fleets')->set(Array
				(
					'fleet_array' 	=> substr($fleetArray, 0, -1),
					'@fleet_time' 	=> 'fleet_end_time',
					'fleet_mess'	=> 1,
					'fleet_group'	=> 0,
					'won'			=> $result['won']
				));

				if ($result['won'] == 1 && ($steal['metal'] > 0 || $steal['crystal'] > 0 || $steal['deuterium'] > 0))
				{
					if (isset($res_procent[$fleetID]))
					{
						sql::build()->set(Array
						(
							'+fleet_resource_metal' 	=> round($res_procent[$fleetID] * $steal['metal']),
							'+fleet_resource_crystal' 	=> round($res_procent[$fleetID] * $steal['crystal']),
							'+fleet_resource_deuterium' => round($res_procent[$fleetID] * $steal['deuterium']),
						));
					}
				}

				sql::build()->where('fleet_id', '=', $fleetID)->execute();
			}
		}

		foreach ($defenseFleets as $fleetID => $defender)
		{
			if ($fleetID != 0)
			{
				$fleetArray = '';
				$totalCount = 0;

				foreach ($defender as $element => $amount)
				{
					if (!is_numeric($element) || !$amount)
						continue;

					$fleetArray .= $element . ',' . $amount . '!0;';
					$totalCount += $amount;
				}

				if ($totalCount <= 0)
					$this->KillFleet($fleetID);
				else
				{
					sql::build()->update('game_fleets')->set(Array
					(
						'fleet_array' => substr($fleetArray, 0, -1),
						'@fleet_time' => 'fleet_end_time'
					))
					->where('fleet_id', '=', $fleetID)->execute();
				}
			}
			else
			{
				$arFields = array();

				if ($steal['metal'] > 0 || $steal['crystal'] > 0 || $steal['deuterium'] > 0)
				{
					$arFields = array
					(
						'-metal' 		=> $steal['metal'],
						'-crystal' 		=> $steal['crystal'],
						'-deuterium' 	=> $steal['deuterium']
					);
				}

				for ($i = 200; $i < 500; $i++)
				{
					if (isset($resource[$i]) && isset($defender[$i]) && isset($target->data[$resource[$i]]) && $defender[$i] != $target->data['~'.$resource[$i]])
						$arFields[$resource[$i]] = $defender[$i];
				}

				if (count($arFields) > 0)
					sql::build()->update('game_planets')->set($arFields)->where('id', '=', $target->data['id'])->execute();
			}
		}
		
		$moonChance = $report->getMoonProb();

		if ($target->data['planet_type'] != 1)
			$moonChance = 0;

		$userChance = mt_rand(1, 100);

		if ($this->_fleet['fleet_end_type'] == 5)
			$userChance = 0;

		if ($target->data['parent_planet'] == 0 && $userChance && $userChance <= $moonChance)
		{
			$TargetPlanetName = system::CreateOneMoonRecord($this->_fleet['fleet_end_galaxy'], $this->_fleet['fleet_end_system'], $this->_fleet['fleet_end_planet'], $target->data['id_owner'], $moonChance);

			if ($TargetPlanetName)
				$GottenMoon = sprintf(_getText('sys_moonbuilt'), $this->_fleet['fleet_end_galaxy'], $this->_fleet['fleet_end_system'], $this->_fleet['fleet_end_planet']);
			else
				$GottenMoon = 'Предпринята попытка образования луны, но данные координаты уже заняты другой луной';
		}
		else
			$GottenMoon = '';

		// Очки военного опыта
		$warPoints 		= round($totalDebree / 25000);
		$AddWarPoints 	= ($result['won'] != 2) ? $warPoints : 0;
		// Сборка массива ID участников боя
		$FleetsUsers = array();

		$tmp = array();

		foreach ($attackUsers AS $info)
		{
			if (!in_array($info['tech']['id'], $tmp))
			{
				$tmp[] = $info['tech']['id'];
			}
		}

		$realAttackersUsers = count($tmp);
		unset($tmp);

		foreach ($attackUsers AS $info)
		{
			if (!in_array($info['tech']['id'], $FleetsUsers))
			{
				$FleetsUsers[] = $info['tech']['id'];

				if ($this->_fleet['fleet_mission'] != 6)
				{
					sql::build()->update('game_users');

					if ($result['won'] == 1)
						sql::build()->setField('+raids_win', 1);
					elseif ($result['won'] == 2)
						sql::build()->setField('+raids_lose', 1);

					if ($AddWarPoints > 0)
						sql::build()->setField('+xpraid', ceil($AddWarPoints / $realAttackersUsers));

					sql::build()->setField('+raids', 1)->where('id', '=', $info['tech']['id'])->execute();
				}
			}
		}
		foreach ($defenseUsers AS $info)
		{
			if (!in_array($info['tech']['id'], $FleetsUsers))
			{
				$FleetsUsers[] = $info['tech']['id'];

				if ($this->_fleet['fleet_mission'] != 6)
				{
					sql::build()->update('game_users');

					if ($result['won'] == 2)
						sql::build()->setField('+raids_win', 1);
					elseif ($result['won'] == 1)
						sql::build()->setField('+raids_lose', 1);

					sql::build()->setField('+raids', 1)->where('id', '=', $info['tech']['id'])->execute();
				}
			}
		}

		// Упаковка в строку
		$users = json_encode($FleetsUsers);
		// Упаковка в строку
		$raport = json_encode(array($result, $attackUsers, $defenseUsers, $steal, $moonChance, $GottenMoon, $repairFleets));
		// Уничтожен в первой волне
		$no_contact = (count($result['rw']) <= 2 && $result['won'] == 2) ? 1 : 0;
		// Добавление в базу
		db::query("INSERT INTO game_rw SET `time` = " . time() . ", `id_users` = '" . $users . "', `no_contact` = '" . $no_contact . "', `raport` = '" . addslashes($raport) . "';");
		// Ключи авторизации доклада
		$ids = db::insert_id();

		if ($this->_fleet['fleet_group'] != 0)
		{
			db::query("DELETE FROM game_aks WHERE id = " . $this->_fleet['fleet_group'] . ";");
			db::query("DELETE FROM game_aks_user WHERE aks_id = " . $this->_fleet['fleet_group'] . ";");
		}

		$lost = $result['lost']['att'] + $result['lost']['def'];

		if ($lost >= core::getConfig('hallPoints', 1000000))
		{
			$sab = 0;

			$UserList = array();

			foreach ($attackUsers AS $info)
			{
				if (!in_array($info['username'], $UserList))
					$UserList[] = $info['username'];
			}

			if (count($UserList) > 1)
				$sab = 1;

			$title_1 = implode(',', $UserList);

			$UserList = array();

			foreach ($defenseUsers AS $info)
			{
				if (!in_array($info['username'], $UserList))
					$UserList[] = $info['username'];
			}

			if (count($UserList) > 1)
				$sab = 1;

			$title_2 = implode(',', $UserList);

			$title = '' . $title_1 . ' vs ' . $title_2 . ' (П: ' . strings::pretty_number($lost) . ')';

			sql::build()->insert('game_savelog')->set(Array
			(
				'user' 	=> 0,
				'title' => $title,
				'log' 	=> addslashes($raport)
			))
			->execute();

			$id = db::insert_id();

			sql::build()->insert('game_hall')->set(Array
			(
				'title' 	=> $title,
				'debris' 	=> floor($lost / 1000),
				'time' 		=> time(),
				'won' 		=> $result['won'],
				'sab' 		=> $sab,
				'log' 		=> $id
			))
			->execute();
		}

		$raport = "<center><a ".(core::getConfig('openRaportInNewWindow', 0) == 1 ? 'target="_blank"' : '')." href=\"?set=rw&r=" . $ids . "&k=" . md5('xnovasuka' . $ids) . "\">";

		if ($result['won'] == 1)
			$raport .= "<font color=\"green\">";
		elseif ($result['won'] == 0)
			$raport .= "<font color=\"orange\">";
		elseif ($result['won'] == 2)
			$raport .= "<font color=\"red\">";

		$raport .= _getText('sys_mess_attack_report') . " [" . $this->_fleet['fleet_end_galaxy'] . ":" . $this->_fleet['fleet_end_system'] . ":" . $this->_fleet['fleet_end_planet'] . "]</font></a>";

		$raport2  = $raport . '<br><br><font color=\'red\'>' . _getText('sys_perte_attaquant') . ': ' . strings::pretty_number($result['lost']['att']) . '</font><font color=\'green\'>   ' . _getText('sys_perte_defenseur') . ': ' . strings::pretty_number($result['lost']['def']) . '</font><br>';
		$raport2 .= _getText('sys_gain') . ' м: <font color=\'#adaead\'>' . strings::pretty_number($steal['metal']) . '</font>, к: <font color=\'#ef51ef\'>' . strings::pretty_number($steal['crystal']) . '</font>, д: <font color=\'#f77542\'>' . strings::pretty_number($steal['deuterium']) . '</font><br>';
		$raport2 .= _getText('sys_debris') . ' м: <font color=\'#adaead\'>' . strings::pretty_number($result['debree']['att'][0] + $result['debree']['def'][0]) . '</font>, к: <font color=\'#ef51ef\'>' . strings::pretty_number($result['debree']['att'][1] + $result['debree']['def'][1]) . '</font></center>';

		$UserList = array();

		foreach ($attackUsers AS $info)
		{
			if (!in_array($info['tech']['id'], $UserList))
				$UserList[] = $info['tech']['id'];
		}

		foreach ($UserList AS $info)
			user::get()->sendMessage($info, 0, time(), 3, 'Боевой доклад', $raport2);

		$UserList = array();

		foreach ($defenseUsers AS $info)
		{
			if (!in_array($info['tech']['id'], $UserList))
				$UserList[] = $info['tech']['id'];
		}

		foreach ($UserList AS $info)
			user::get()->sendMessage($info, 0, time(), 3, 'Боевой доклад', $raport);

		sql::build()->insert('game_log_attack')->set(Array
		(
			'uid' 			=> $this->_fleet['fleet_owner'],
			'time'			=> time(),
			'planet_start' 	=> 0,
			'planet_end'	=> $target->data['id'],
			'fleet' 		=> $this->_fleet['fleet_array'],
			'battle_log'	=> $ids
		))
		->execute();

		return true;
	}

	public function EndStayEvent()
	{
		return;
	}

	public function ReturnEvent()
	{
		$this->RestoreFleetToPlanet();
		$this->KillFleet();
	}

	public function getGroupFleet ($fleet, \PlayerGroup $playerGroup)
	{
		global $reslist, $resource;

		$fleetData = unserializeFleet($fleet['fleet_array']);

		if (!count($fleetData))
		{
			if ($fleet['fleet_mission'] == 1 || ($fleet['fleet_mission'] == 2 && count($fleetData) == 1 && isset($fleetData[210])))
				$this->ReturnFleet(array(), $fleet['fleet_id']);

			return;
		}

		if (!isset($this->usersInfo[$fleet['fleet_owner']]))
			$this->usersInfo[$fleet['fleet_owner']] = array();

		$this->usersInfo[$fleet['fleet_owner']][$fleet['fleet_id']] = array
		(
			'galaxy' => $fleet['fleet_start_galaxy'],
			'system' => $fleet['fleet_start_system'],
			'planet' => $fleet['fleet_start_planet']
		);

		$res = array();

		foreach ($fleetData as $shipId => $shipArr)
		{
			if ($shipId < 100 || $shipId > 300)
				continue;

			$res[$shipId] = $shipArr['cnt'];
			$res[$shipId + 100] = $shipArr['lvl'];
		}

		if (!isset($this->usersTech[$fleet['fleet_owner']]))
		{
			$info = db::query('SELECT `id`, `username`, `military_tech`, `defence_tech`, `shield_tech`, `laser_tech`, `ionic_tech`, `buster_tech`, `rpg_admiral`, `rpg_komandir` FROM game_users WHERE id = ' . $fleet['fleet_owner'], true);

			$playerObj = new \Player($fleet['fleet_owner']);
			$playerObj->setName($info['username']);
			$playerObj->setTech(0, 0, 0);

			if ($info['rpg_komandir'] > time())
			{
				$info['military_tech'] 	+= 2;
				$info['defence_tech'] 	+= 2;
				$info['shield_tech'] 	+= 2;
			}

			foreach ($reslist['tech'] AS $techId)
			{
				if (isset($info[$resource[$techId]]) && $info[$resource[$techId]] > 0)
					$res[$techId] = $info[$resource[$techId]];
			}

			$this->usersTech[$fleet['fleet_owner']] = $res;
		}
		else
		{
			$playerObj = $playerGroup->getPlayer($fleet['fleet_owner']);

			if ($playerObj === false)
			{
				$info = db::query('SELECT `id`, `username` FROM game_users WHERE id = ' . $fleet['fleet_owner'], true);

				$playerObj = new \Player($fleet['fleet_owner']);
				$playerObj->setName($info['username']);
				$playerObj->setTech(0, 0, 0);
			}
			
			foreach ($res AS $shipId => $lvl)
			{
				if ($shipId < 300 || $shipId > 400)
					continue;
			
				if (!isset($this->usersTech[$fleet['fleet_owner']][$shipId]))
				{
					$this->usersTech[$fleet['fleet_owner']][$shipId] = $lvl;
				}
			}

			foreach ($this->usersTech[$fleet['fleet_owner']] AS $rId => $rVal)
			{
				if (!isset($res[$rId]))
					$res[$rId] = $rVal;
			}
		}

		$fleetObj = new \Fleet($fleet['fleet_id']);

		foreach ($fleetData as $shipId => $shipArr)
		{
			if ($shipId < 100 || $shipId > 300 || !$shipArr['cnt'])
				continue;

			$fleetObj->add($this->getShipType($shipId, array($shipArr['cnt'], $shipArr['lvl']), $res));
		}

		if (!$fleetObj->isEmpty())
			$playerObj->addFleet($fleetObj);

		if (!$playerGroup->existPlayer($fleet['fleet_owner']))
			$playerGroup->addPlayer($playerObj);
	}

	public function getShipType($id, $count, $res)
	{
		global $CombatCaps, $pricelist, $reslist;

		$attDef 	= ($count[1] * ($CombatCaps[$id]['power_armour'] / 100)) + (isset($res[111]) ? $res[111] : 0) * 0.05;
		$attTech 	= (isset($res[109]) ? $res[109] : 0) * 0.05 + ($count[1] * ($CombatCaps[$id]['power_up'] / 100));

		if ($CombatCaps[$id]['type_gun'] == 1)
			$attTech += (isset($res[120]) ? $res[120] : 0) * 0.05;
		elseif ($CombatCaps[$id]['type_gun'] == 2)
			$attTech += (isset($res[121]) ? $res[121] : 0) * 0.05;
		elseif ($CombatCaps[$id]['type_gun'] == 3)
			$attTech += (isset($res[122]) ? $res[122] : 0) * 0.05;

		$cost = array($pricelist[$id]['metal'], $pricelist[$id]['crystal']);

		if (in_array($id, $reslist['fleet']))
			return new \Ship($id, $count[0], $CombatCaps[$id]['sd'], $CombatCaps[$id]['shield'], $cost, $CombatCaps[$id]['attack'], $attTech, ((isset($res[110]) ? $res[110] : 0) * 0.05), $attDef);

		return new \Defense($id, $count[0], $CombatCaps[$id]['sd'], $CombatCaps[$id]['shield'], $cost, $CombatCaps[$id]['attack'], $attTech, ((isset($res[110]) ? $res[110] : 0) * 0.05), $attDef);
	}

	public function convertPlayerGroupToArray (\PlayerGroup $_playerGroup)
	{
		$result = array();

		foreach ($_playerGroup as $_player)
		{
			/**
			 * @var \Player $_player
			 */
			$result[$_player->getId()] = array
			(
				'username' 	=> $_player->getName(),
				'fleet' 	=> $this->usersInfo[$_player->getId()],
				'tech' 		=> array
				(
					'id' 			=> $_player->getId(),
					'military_tech' => isset($this->usersTech[$_player->getId()][109]) ? $this->usersTech[$_player->getId()][109] : 0,
					'shield_tech' 	=> isset($this->usersTech[$_player->getId()][110]) ? $this->usersTech[$_player->getId()][110] : 0,
					'defence_tech' 	=> isset($this->usersTech[$_player->getId()][111]) ? $this->usersTech[$_player->getId()][111] : 0,
					'laser_tech'	=> isset($this->usersTech[$_player->getId()][120]) ? $this->usersTech[$_player->getId()][120] : 0,
					'ionic_tech'	=> isset($this->usersTech[$_player->getId()][121]) ? $this->usersTech[$_player->getId()][121] : 0,
					'buster_tech'	=> isset($this->usersTech[$_player->getId()][122]) ? $this->usersTech[$_player->getId()][122] : 0
				),
				'flvl' => $this->usersTech[$_player->getId()],
			);
		}

		return $result;
	}

	public function convertRoundToArray(\Round $round)
	{
		$result = array
		(
				'attackers' 	=> array(),
				'defenders' 	=> array(),
				'attack'		=> array('total' => $round->getAttackersFirePower()),
				'defense' 		=> array('total' => $round->getDefendersFirePower()),
				'attackA' 		=> array('total' => $round->getAttackersFireCount()),
				'defenseA' 		=> array('total' => $round->getDefendersFireCount())
		);

		$attackers = $round->getAfterBattleAttackers();
		$defenders = $round->getAfterBattleDefenders();

		foreach ($attackers as $_player)
		{
			foreach ($_player as $_idFleet => $_fleet)
			{
				/**
				 * @var \ShipType $_ship
				 */
				foreach($_fleet as $_shipID => $_ship)
				{
					$result['attackers'][$_idFleet][$_shipID] = $_ship->getCount();

					if (!isset($result['attackA'][$_idFleet]['total']))
						$result['attackA'][$_idFleet]['total'] = 0;

					$result['attackA'][$_idFleet]['total'] += $_ship->getCount();
				}
			}
		}

		foreach ($defenders as $_player)
		{
			foreach ($_player as $_idFleet => $_fleet)
			{
				/**
				 * @var \ShipType $_ship
				 */
				foreach($_fleet as $_shipID => $_ship)
				{
					$result['defenders'][$_idFleet][$_shipID] = $_ship->getCount();

					if (!isset($result['defenseA'][$_idFleet]['total']))
						$result['defenseA'][$_idFleet]['total'] = 0;

					$result['defenseA'][$_idFleet]['total'] += $_ship->getCount();
				}
			}
		}

		$result['attackShield'] = $round->getAttachersAssorbedDamage();
		$result['defShield'] 	= $round->getDefendersAssorbedDamage();

		return $result;
	}

	public function getResultFleetArray (\PlayerGroup $playerGroupBeforeBattle, \PlayerGroup $playerGroupAfterBattle)
	{
		$result = array();

		foreach ($playerGroupBeforeBattle->getIterator() as $idPlayer => $player)
		{
			/**
			 * @var $player \Player
			 * @var $Xplayer \Player
			 */
			$existPlayer = $playerGroupAfterBattle->existPlayer($idPlayer);

			$Xplayer = null;

			if ($existPlayer)
				$Xplayer = $playerGroupAfterBattle->getPlayer($idPlayer);

			foreach ($player->getIterator() as $idFleet => $fleet)
			{
				/**
				 * @var $fleet \Fleet
				 * @var $Xfleet \Fleet
				 */
				$existFleet = $existPlayer && $Xplayer->existFleet($idFleet);
				$Xfleet = null;

				$result[$idFleet] = array();

				if ($existFleet)
					$Xfleet = $Xplayer->getFleet($idFleet);

				foreach ($fleet as $idShipType => $fighters)
				{
					$existShipType 	= $existFleet && $Xfleet->existShipType($idShipType);

					if ($existShipType)
					{
						$XshipType = $Xfleet->getShipType($idShipType);
						/**
						 * @var $XshipType \ShipType
						 */
						$result[$idFleet][$idShipType] = $XshipType->getCount();
					}
					else
						$result[$idFleet][$idShipType] = 0;
				}
			}
		}

		return $result;
	}

	private function getSteal ($planet, $capacity = 0)
	{
		$steal = array('metal' => 0, 'crystal' => 0, 'deuterium' => 0);

		if ($capacity > 0)
		{
			$metal 		= $planet['metal'] / 2;
			$crystal 	= $planet['crystal'] / 2;
			$deuter 	= $planet['deuterium'] / 2;

			$steal['metal'] 	= min($capacity / 3, $metal);
			$capacity -= $steal['metal'];

			$steal['crystal'] 	= min($capacity / 2, $crystal);
			$capacity -= $steal['crystal'];

			$steal['deuterium'] = min($capacity, $deuter);
			$capacity -= $steal['deuterium'];

			if ($capacity > 0)
			{
				$oldStealMetal = $steal['metal'];

				$steal['metal'] += min(($capacity / 2), ($metal - $steal['metal']));
				$capacity -= $steal['metal'] - $oldStealMetal;

				$steal['crystal'] += min($capacity, ($crystal - $steal['crystal']));
			}
		}

		$steal['metal'] 	= max($steal['metal'], 0);
		$steal['crystal'] 	= max($steal['crystal'], 0);
		$steal['deuterium'] = max($steal['deuterium'], 0);

		return array_map('round', $steal);
	}
}

?>