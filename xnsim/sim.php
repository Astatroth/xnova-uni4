<?
use Xcms\core;
use Xcms\db;
use Xcms\session;
use Xcms\sql;
use Xcms\strings;
use Xnova\User;

define('INSIDE', true);

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__.'../');

include($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/core.php');
core::init();

include(ROOT_DIR.APP_PATH.'functions/functions.php');
include(ROOT_DIR.APP_PATH.'varsGlobal.php');
include(ROOT_DIR.APP_PATH.'functions/formatCombatReport.php');

strings::setLang('ru');
strings::includeLang('tech');

$user = user::get();

$session = new session();
$session->CheckTheUser();

$user->load_from_array($session->user);

if (isset($_GET['sid']))
{
	$log = db::query("SELECT * FROM game_log_sim WHERE sid = '".addslashes(htmlspecialchars($_GET['sid']))."' LIMIT 1", true);

	if (!isset($log['id']))
		die('Лога не существует');

	$result = json_decode($log['data'], true);

	$sid = $log['sid'];
}
else
{
	$r = (isset($_GET['r'])) ? explode("|", $_GET['r']) : explode("|", $_POST['r']);

	if (!isset($r['0']) || !isset($r['10']))
		die('Нет данных для симуляции боя');

	core::loadLib('opbe');

	define('MAX_SLOTS', core::getConfig('maxSlotsInSim', 5));

	global $usersInfo;
	$usersInfo = array();

	$attackers = getAttackers(0);
	$defenders = getAttackers(MAX_SLOTS);

	$engine = new Battle($attackers, $defenders);

	$report = $engine->getReport();

	$result = array();
	$result[0] = array('time' => time(), 'rw' => array());

	$result[1] = convertPlayerGroupToArray($report->getResultAttackersFleetOnRound('START'));
	$result[2] = convertPlayerGroupToArray($report->getResultDefendersFleetOnRound('START'));

	for ($_i = 0; $_i <= $report->getLastRoundNumber(); $_i++)
	{
		$result[0]['rw'][] = convertRoundToArray($report->getRound($_i));
	}

	if ($report->attackerHasWin())
		$result[0]['won'] = 1;
	if ($report->defenderHasWin())
		$result[0]['won'] = 2;
	if ($report->isAdraw())
		$result[0]['won'] = 0;

	$result[0]['lost'] = array('att' => $report->getTotalAttackersLostUnits(), 'def' => $report->getTotalDefendersLostUnits());

	$debris = $report->getDebris();

	$result[0]['debree']['att'] = $debris;
	$result[0]['debree']['def'] = array(0, 0);

	$result[3] = array('metal' => 0, 'crystal' => 0, 'deuterium' => 0);
	$result[4] = $report->getMoonProb();
	$result[5] = '';

	$result[6] = array();

	foreach ($report->getDefendersRepaired() as $_id => $_player)
	{
		foreach ($_player as $_idFleet => $_fleet)
		{
			/**
			 * @var ShipType $_ship
			 */
			foreach ($_fleet as $_shipID => $_ship)
			{
				$result[6][$_idFleet][$_shipID] = $_ship->getCount();
			}
		}
	}

	$statistics = array();

	for ($i = 0; $i < 50; $i++)
	{
		$engine = new Battle($attackers, $defenders);

		$report = $engine->getReport();

		$statistics[] = array('att' => $report->getTotalAttackersLostUnits(), 'def' => $report->getTotalDefendersLostUnits());

		unset($report);
		unset($engine);
	}

	uasort($statistics, function($a, $b)
	{
		return ($a['att'] > $b['att'] ? 1 : -1);
	});

	$sid = md5(time().\Xcms\request::$client_ip);

	$check = db::first(db::query("SELECT COUNT(*) AS NUM FROM game_log_sim WHERE sid = '".$sid."'", true));

	if ($check == 0)
	{
		sql::build()->insert('game_log_sim')->set(array
		(
			'sid' => $sid,
			'time' => time(),
			'data' => addslashes(json_encode($result))
		))->execute();
	}
}

if (isset($_GET['ingame']))
{
	$formatted_cr = formatCombatReport($result[0], $result[1], $result[2], $result[3], $result[4], $result[5], $result[6]);

	echo stripslashes( $formatted_cr['html'] );
}
else
{
	$formatted_cr = formatCombatReport($result[0], $result[1], $result[2], $result[3], $result[4], $result[5], $result[6]);

	?>
	<html>
		<head>
			<title>Симуляция боя</title>
			<link rel="stylesheet" type="text/css" href="/<?=DEFAULT_SKINPATH ?>report_v2.css?v=<?=substr(md5(VERSION), 0, 3) ?>">
			<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		</head>
		<body>
			<center>
				<table width="99%">
					<tr>
						<td><?=stripslashes( $formatted_cr['html'] ) ?></td>
					</tr>
				</table>
				Ссылка на результат симуляции<br><br>
				<input type="text" value="http://<?=$_SERVER['SERVER_NAME'] ?>/xnsim/sim.php?sid=<?=$sid ?>" style="width:500px;padding:5px;text-align: center;">
				<br><br>
				<? if (isset($statistics)): ?>
					Результаты потерь после 50 симуляций:
					<table>
						<tr>
							<th>№</th>
							<th>Потери атакующего</th>
							<th>Потери защитника</th>
						</tr>
						<? foreach ($statistics AS $i => $s): ?>
							<tr>
								<th><?=$i ?></th>
								<th><?=strings::pretty_number($s['att']) ?></th>
								<th><?=strings::pretty_number($s['def']) ?></th>
							</tr>
						<? endforeach; ?>
					</table>
				<? endif; ?>
				<br><br>
				Made by AlexPro for <a href="http://xnova.su/" target="_blank">XNova - <?=UNIVERSE ?> UNIVERSE</a>
			</center>
		</body>
	</html>
	<?
}

function convertPlayerGroupToArray (\PlayerGroup $_playerGroup)
{
	global $usersInfo;

	$result = array();

	foreach ($_playerGroup as $_player)
	{
		/**
		 * @var Player $_player
		 */
		$result[$_player->getId()] = array
		(
			'username' => $_player->getName(),
			'fleet' => array($_player->getId() => array('galaxy' => 1, 'system' => 1, 'planet' => 1)),
			'tech' => array
			(
				'military_tech' => isset($usersInfo[$_player->getId()][109]) ? $usersInfo[$_player->getId()][109] : 0,
				'shield_tech' 	=> isset($usersInfo[$_player->getId()][110]) ? $usersInfo[$_player->getId()][110] : 0,
				'defence_tech' 	=> isset($usersInfo[$_player->getId()][111]) ? $usersInfo[$_player->getId()][111] : 0,
				'laser_tech'	=> isset($usersInfo[$_player->getId()][120]) ? $usersInfo[$_player->getId()][120] : 0,
				'ionic_tech'	=> isset($usersInfo[$_player->getId()][121]) ? $usersInfo[$_player->getId()][121] : 0,
				'buster_tech'	=> isset($usersInfo[$_player->getId()][122]) ? $usersInfo[$_player->getId()][122] : 0
			),
			'flvl' => $usersInfo[$_player->getId()],
		);
	}

	return $result;
}

function convertRoundToArray(Round $round)
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
			 * @var ShipType $_ship
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
			 * @var ShipType $_ship
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

function getAttackers($s = 0)
{
	global $r, $usersInfo;

	$playerGroupObj = new PlayerGroup();

	for ($i = $s; $i < MAX_SLOTS * 2; $i++)
	{
	    if ($i <= MAX_SLOTS && $i < (MAX_SLOTS + $s) && $r[$i] != "")
		{
			$res = array();
			$fleets = array();

			$fleetData = unserializeFleet($r[$i]);

			foreach ($fleetData as $shipId => $shipArr)
			{
				if ($shipId > 200)
					$fleets[$shipId] = array($shipArr['cnt'], $shipArr['lvl']);

				$res[$shipId] = $shipArr['cnt'];

				if ($shipArr['lvl'] > 0)
					$res[($shipId > 400 ? ($shipId - 50) : ($shipId + 100))] = $shipArr['lvl'];
			}

			$fleetId = $i;
			$playerId = $i;

			$playerObj = new Player($playerId);
			$playerObj->setName('Игрок ' . ($playerId + 1));
			$playerObj->setTech(0, 0, 0);

			$usersInfo[$playerId] = $res;

			$fleetObj = new Fleet($fleetId);

			foreach ($fleets as $id => $count)
			{
				$id = floor($id);

				if ($count[0] > 0 && $id > 0)
					$fleetObj->add(getShipType($id, $count, $res));
			}

			if (!$fleetObj->isEmpty())
				$playerObj->addFleet($fleetObj);

			if (!$playerGroupObj->existPlayer($playerId))
				$playerGroupObj->addPlayer($playerObj);
		}
	}

	return $playerGroupObj;
}

function getShipType($id, $count, $res)
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

?>