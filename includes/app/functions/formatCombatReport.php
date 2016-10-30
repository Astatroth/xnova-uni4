<?php

use Xcms\strings;
use Xnova\User;

function formatCombatReport ($result_array, $attackUsers, $defenseUsers, $steal_array, $moon_int = 0, $moon_string = '', $repair = array())
{
	global $CombatCaps, $pricelist;

	$usersInfo = array();

	foreach ($attackUsers AS $userId => $u)
	{
		foreach ($u['fleet'] AS $id => $f)
		{
			if (!is_numeric($id))
				continue;

			$usersInfo[$id] = $f;
			$usersInfo[$id]['user_id'] = $userId;
		}
	}

	foreach ($defenseUsers AS $userId => $u)
	{
		foreach ($u['fleet'] AS $id => $f)
		{
			if (!is_numeric($id))
				continue;

			$usersInfo[$id] = $f;
			$usersInfo[$id]['user_id'] = $userId;
		}
	}

	$html = "<center>";
	$bbc = "";

	$html .= "В " . datezone("d-m-Y H:i:s", $result_array['time']) . " произошёл бой между следующими флотами:<div class='separator'></div><table align='center'><tr>";

	if (is_array($attackUsers))
	{
		$checkName = array();

		foreach ($attackUsers AS $info)
		{
			if (in_array($info['username'], $checkName))
				continue;

			$html .= '<td><table class="info" align="center">
						<tr><td class="c" colspan="3"><span class="negative">' . $info['username'] . '</span></td></tr>
						<tr><th>Технология</th><th>Ур.</th><th>%</th></tr>
						<tr><th>Оружие</th><th>' . $info['tech']['military_tech'] . '</th><th>' . ($info['tech']['military_tech'] * 5) . '</th></tr>
						<tr><th>Щиты</th><th>' . $info['tech']['shield_tech'] . '</th><th>' . ($info['tech']['shield_tech'] * 5) . '</th></tr>
						<tr><th>Броня</th><th>' . $info['tech']['defence_tech'] . '</th><th>' . ($info['tech']['defence_tech'] * 5) . '</th></tr>
						<tr><th>Лазер</th><th>' . $info['tech']['laser_tech'] . '</th><th>' . ($info['tech']['laser_tech'] * 5) . '</th></tr>
						<tr><th>Ион</th><th>' . $info['tech']['ionic_tech'] . '</th><th>' . ($info['tech']['ionic_tech'] * 5) . '</th></tr>
						<tr><th>Плазма</th><th>' . $info['tech']['buster_tech'] . '</th><th>' . ($info['tech']['buster_tech'] * 5) . '</th></tr></table></td>';

			$checkName[] = $info['username'];
		}
	}

	if (is_array($defenseUsers))
	{
		$checkName = array();

		foreach ($defenseUsers AS $info)
		{
			if (in_array($info['username'], $checkName))
				continue;

			$html .= '<td><table class="info" align="center">
						<tr><td class="c" colspan="3"><span class="positive">' . $info['username'] . '</span></td></tr>
						<tr><th>Технология</th><th>Ур.</th><th>%</th></tr>
						<tr><th>Оружие</th><th>' . $info['tech']['military_tech'] . '</th><th>' . ($info['tech']['military_tech'] * 5) . '</th></tr>
						<tr><th>Щиты</th><th>' . $info['tech']['shield_tech'] . '</th><th>' . ($info['tech']['shield_tech'] * 5) . '</th></tr>
						<tr><th>Броня</th><th>' . $info['tech']['defence_tech'] . '</th><th>' . ($info['tech']['defence_tech'] * 5) . '</th></tr>
						<tr><th>Лазер</th><th>' . $info['tech']['laser_tech'] . '</th><th>' . ($info['tech']['laser_tech'] * 5) . '</th></tr>
						<tr><th>Ион</th><th>' . $info['tech']['ionic_tech'] . '</th><th>' . ($info['tech']['ionic_tech'] * 5) . '</th></tr>
						<tr><th>Плазма</th><th>' . $info['tech']['buster_tech'] . '</th><th>' . ($info['tech']['buster_tech'] * 5) . '</th></tr></table></td>';

			$checkName[] = $info['username'];
		}
	}

	$html .= '</tr></table><br>';

	$round_no = 1;

	foreach ($result_array['rw'] as $round => $data)
	{
		if ($data['attackA']['total'] > 0 && $data['defenseA']['total'] > 0)
		{
			$html .= "<div class='separator'></div><center>Атакующий флот делает " . strings::pretty_number($data['attackA']['total']) . " выстрела(ов) с общей мощностью " . strings::pretty_number($data['attack']['total']) . " по защитнику. Щиты защитника поглощают " . strings::pretty_number($data['defShield']) . " мощности.<br />";
			$html .= "Защитный флот делает " . strings::pretty_number($data['defenseA']['total']) . " выстрела(ов) с общей мощностью " . strings::pretty_number($data['defense']['total']) . " по атакующему. Щиты атакующего поглащают " . strings::pretty_number($data['attackShield']) . " мощности.</center><div class='separator'></div>";
		}

		$attackers = $data['attackers'];
		$defenders = $data['defenders'];


		if (!count($attackers))
		{
			$html .= '<div class="fleet"><div class="separator"></div><center>Атакующий флот уничтожен</center><div class="separator"></div></div>';
		}

		foreach ($attackers as $fleet_id => $data2)
		{
			$user = $usersInfo[$fleet_id]['user_id'];

			$html .= "<div class='fleet'>";
			$html .= "<span class='negative'>Атакующий " . $attackUsers[$user]['username'] . " [" . $usersInfo[$fleet_id]['galaxy'] . ":" . $usersInfo[$fleet_id]['system'] . ":" . $usersInfo[$fleet_id]['planet'] . "]</span><div class='separator'></div>";
			$html .= "<table border=1>";

			if ($data['attackA'][$fleet_id] > 0)
			{
				$raport1 = "<tr><th>Тип</th>";
				$raport2 = "<tr><th>Кол-во</th>";
				$raport3 = "<tr><th>Атака</th>";
				$raport4 = "<tr><th>Корпус</th>";

				foreach ($data2 as $ship_id => $ship_count)
				{
					if ($ship_count > 0)
					{
						$l = $ship_id > 400 ? ($ship_id - 50) : ($ship_id + 100);

						$raport1 .= "<th>" . _getText('tech', $ship_id) . "".(user::get()->isAdmin() ? ' ('.(isset($attackUsers[$user]['flvl'][$l]) ? $attackUsers[$user]['flvl'][$l] : 0).')' : '')."</th>";

						if ($round == 0)
							$raport2 .= "<th>" . strings::pretty_number(ceil($ship_count)) . "</th>";
						else
						{
							$raport2 .= "<th>" . strings::pretty_number(ceil($ship_count)) . "";

							if (ceil($result_array['rw'][$round - 1]['attackers'][$fleet_id][$ship_id]) - ceil($ship_count) > 0)
								$raport2 .= " <small><font color='red'>-" . (ceil($result_array['rw'][$round - 1]['attackers'][$fleet_id][$ship_id]) - ceil($ship_count)) . "</font></small>";

							$raport2 .= "</th>";
						}

						$attTech = 1 + $attackUsers[$user]['tech']['military_tech'] * 0.05 + ((isset($attackUsers[$user]['flvl'][$l]) ? $attackUsers[$user]['flvl'][$l] : 0) * ($CombatCaps[$ship_id]['power_up'] / 100));

						if ($CombatCaps[$ship_id]['type_gun'] == 1)
							$attTech += $attackUsers[$user]['tech']['laser_tech'] * 0.05;
						elseif ($CombatCaps[$ship_id]['type_gun'] == 2)
							$attTech += $attackUsers[$user]['tech']['ionic_tech'] * 0.05;
						elseif ($CombatCaps[$ship_id]['type_gun'] == 3)
							$attTech += $attackUsers[$user]['tech']['buster_tech'] * 0.05;

						$raport3 .= "<th>" . strings::pretty_number(round($CombatCaps[$ship_id]['attack'] * $attTech)) . "</th>";
						$raport4 .= "<th>" . strings::pretty_number(round((($pricelist[$ship_id]['metal'] + $pricelist[$ship_id]['crystal']) / 10) * (1 + (($CombatCaps[$ship_id]['power_armour'] * (isset($attackUsers[$user]['flvl'][$l]) ? $attackUsers[$user]['flvl'][$l] : 0)) / 100) + $attackUsers[$user]['tech']['defence_tech'] * 0.05))) . "</th>";
					}
				}

				$raport1 .= "</tr>";
				$raport2 .= "</tr>";
				$raport3 .= "</tr>";
				$raport4 .= "</tr>";

				$html .= $raport1 . $raport2 . $raport3 . $raport4;
			}
			else
				$html .= "<br>уничтожен";

			$html .= "</table>";
			$html .= "</div>";
		}

		$html .= '<div class="separator"></div>';

		if (!count($defenders))
		{
			$html .= '<div class="fleet"><div class="separator"></div><center>Защитный флот уничтожен</center><div class="separator"></div></div>';
		}

		foreach ($defenders as $fleet_id => $data2)
		{
			$user = $usersInfo[$fleet_id]['user_id'];

			$html .= "<div class='fleet'>";
			$html .= "<span class='positive'>Защитник " .$defenseUsers[$user]['username'] . " [" . $usersInfo[$fleet_id]['galaxy'] . ":" . $usersInfo[$fleet_id]['system'] . ":" . $usersInfo[$fleet_id]['planet'] . "]</span><div class='separator'></div>";

			$html .= "<table border=1 align=\"center\">";

			if ($data['defenseA'][$fleet_id] > 0)
			{
				$raport1 = "<tr><th>Тип</th>";
				$raport2 = "<tr><th>Кол-во</th>";
				$raport3 = "<tr><th>Атака</th>";
				$raport4 = "<tr><th>Корпус</th>";

				foreach ($data2 as $ship_id => $ship_count)
				{
					if ($ship_count > 0)
					{
						$l = $ship_id > 400 ? ($ship_id - 50) : ($ship_id + 100);

						$raport1 .= "<th>" . _getText('tech', $ship_id) . "".(user::get()->isAdmin() ? ' ('.(isset($defenseUsers[$user]['flvl'][$l]) ? $defenseUsers[$user]['flvl'][$l] : 0).')' : '')."</th>";

						if ($round == 0)
							$raport2 .= "<th>" . strings::pretty_number(ceil($ship_count)) . "</th>";
						else
						{
							$raport2 .= "<th>" . strings::pretty_number(ceil($ship_count)) . "";

							if (ceil($result_array['rw'][$round - 1]['defenders'][$fleet_id][$ship_id]) - ceil($ship_count) > 0)
								$raport2 .= " <small><font color='red'>-" . (ceil($result_array['rw'][$round - 1]['defenders'][$fleet_id][$ship_id]) - ceil($ship_count)) . "</font></small>";

							$raport2 .= "</th>";
						}

						$attTech = 1 + $defenseUsers[$user]['tech']['military_tech'] * 0.05 + ((isset($defenseUsers[$user]['flvl'][$l]) ? $defenseUsers[$user]['flvl'][$l] : 0) * ($CombatCaps[$ship_id]['power_up'] / 100));

						if ($CombatCaps[$ship_id]['type_gun'] == 1)
							$attTech += $defenseUsers[$user]['tech']['laser_tech'] * 0.05;
						elseif ($CombatCaps[$ship_id]['type_gun'] == 2)
							$attTech += $defenseUsers[$user]['tech']['ionic_tech'] * 0.05;
						elseif ($CombatCaps[$ship_id]['type_gun'] == 3)
							$attTech += $defenseUsers[$user]['tech']['buster_tech'] * 0.05;

						$raport3 .= "<th>" . strings::pretty_number(round($CombatCaps[$ship_id]['attack'] * $attTech)) . "</th>";
						$raport4 .= "<th>" . strings::pretty_number(round((($pricelist[$ship_id]['metal'] + $pricelist[$ship_id]['crystal']) / 10) * (1 + (($CombatCaps[$ship_id]['power_armour'] * (isset($defenseUsers[$user]['flvl'][$l]) ? $defenseUsers[$user]['flvl'][$l] : 0)) / 100) + $defenseUsers[$user]['tech']['defence_tech'] * 0.05))) . "</th>";
					}
				}

				$raport1 .= "</tr>";
				$raport2 .= "</tr>";
				$raport3 .= "</tr>";
				$raport4 .= "</tr>";

				$html .= $raport1 . $raport2 . $raport3 . $raport4;
			}
			else
				$html .= "<br>уничтожен";

			$html .= "</table>";
			$html .= "</div>";
		}

		$round_no++;
	}

	if ($result_array['won'] == 2)
	{
		$result1 = "Обороняющийся выиграл битву!<br />";
	}
	elseif ($result_array['won'] == 1)
	{
		$result1 = "Атакующий выиграл битву!<br />";
		$result1 .= "Он получает " . strings::pretty_number($steal_array['metal']) . " металла, " . strings::pretty_number($steal_array['crystal']) . " кристалла и " . strings::pretty_number($steal_array['deuterium']) . " дейтерия<br />";
	}
	else
	{
		$result1 = "Бой закончился ничьёй!<br />";
	}

	$html .= "<br><br><table class='result'><tr><td class='c'>" . $result1 . "</td></tr>";

	$debirs_meta = ($result_array['debree']['att'][0] + $result_array['debree']['def'][0]);
	$debirs_crys = ($result_array['debree']['att'][1] + $result_array['debree']['def'][1]);

	$html .= "<tr><th>Атакующий потерял " . strings::pretty_number($result_array['lost']['att']) . " единиц.</th></tr>";
	$html .= "<tr><th>Обороняющийся потерял " . strings::pretty_number($result_array['lost']['def']) . " единиц.</th></tr>";
	$html .= "<tr><td class='c'>Поле обломков: " . strings::pretty_number($debirs_meta) . " металла и " . strings::pretty_number($debirs_crys) . " кристалла.</td></tr>";

	$html .= "<tr><th>Шанс появления луны составляет " . $moon_int . "%<br>";
	$html .= $moon_string . "</th></tr>";

	$html .= "</table><br><br>";

	if (count($repair))
	{
		foreach ($repair as $data2)
		{
			$html .= "<div class='fleet'><span class='neutral'>Восстановленная оборона:</span><div class='separator'></div>";
			$html .= "<table border=1 align=\"center\">";

			$raport1 = "";
			$raport2 = "";

			foreach ($data2 as $ship_id => $ship_count)
			{
				if ($ship_count > 0)
				{
					$raport1 .= "<th>" . _getText('tech', $ship_id) . "</th>";
					$raport2 .= "<th>" . strings::pretty_number(ceil($ship_count)) . "</th>";
				}
			}
			$raport1 .= "</tr>";
			$raport2 .= "</tr>";
			$html .= $raport1 . $raport2;

			$html .= "</table>";
			$html .= "</div>";
		}
	}

	$html .= '<br><br>';
	$html .= '<a href="'.convertReportToSimLink(Array($result_array, $attackUsers, $defenseUsers)).'" target="_blank">Симуляция</a>';

	$html .= "</center><br>";

	return array('html' => $html, 'bbc' => $bbc);
}

function convertReportToSimLink ($result)
{
	$usersInfo = array();

	foreach ($result[1] AS $userId => $u)
	{
		foreach ($u['fleet'] AS $id => $f)
		{
			if (!is_numeric($id))
				continue;

			$usersInfo[$id] = $userId;
		}
	}

	foreach ($result[2] AS $userId => $u)
	{
		foreach ($u['fleet'] AS $id => $f)
		{
			if (!is_numeric($id))
				continue;

			$usersInfo[$id] = $userId;
		}
	}

	$att = array();

	$j = 0;

	foreach ($result[0]['rw'][0]['attackers'] AS $i => $a)
	{
		$j++;

		$t = array();

		foreach ($result[1][$usersInfo[$i]]['flvl'] AS $j => $l)
		{
			if ($j < 200)
				$t[] = $j.','.$l;
		}

		foreach ($a AS $s => $c)
			$t[] = $s.','.$c.'!'.(isset($result[1][$usersInfo[$i]]['flvl'][$s+100]) ? $result[1][$usersInfo[$i]]['flvl'][$s+100] : 0).'';

		$att[] = implode(';', $t);

		if ($j == 10)
			break;
	}

	for ($i = count($att); $i < 10; $i++)
	{
		$att[] = '';
	}

	$def = array();

	$j = 0;

	foreach ($result[0]['rw'][0]['defenders'] AS $i => $a)
	{
		$j++;

		$t = array();

		foreach ($result[2][$usersInfo[$i]]['flvl'] AS $j => $l)
		{
			if ($j < 200)
				$t[] = $j.','.$l;
		}

		foreach ($a AS $s => $c)
			$t[] = $s.','.$c.'!'.(isset($result[2][$usersInfo[$i]]['flvl'][$s+100]) ? $result[2][$usersInfo[$i]]['flvl'][$s+100] : 0).'';

		$def[] = implode(';', $t);

		if ($j == 10)
			break;
	}

	for ($i = count($def); $i < 10; $i++)
	{
		$def[] = '';
	}

	return 'http://uni'.UNIVERSE.'.xnova.su/xnsim/sim.php?r='.implode('|', $att).'|'.implode('|', $def).'';
}

?>