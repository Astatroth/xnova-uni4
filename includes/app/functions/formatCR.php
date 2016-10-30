<?php

use Xcms\strings;

function formatCR ($result_array, $attackUsers, $defenseUsers, $steal_array, $moon_int, $moon_string)
{
	global $CombatCaps, $pricelist;

	$html = "<center><table><tr><td>";
	$bbc = "";

	$html .= "В " . datezone("d-m-Y H:i:s", $result_array['time']) . " произошёл бой между следующими флотами:<br><table align='center'><tr>";

	if (is_array($attackUsers))
	{
		$checkName = array();

		foreach ($attackUsers AS $info)
		{
			if (in_array($info['username'], $checkName))
				continue;

			$html .= '<td><table align="center">
						<tr><td class="c" colspan="3">' . $info['username'] . ' (Атакующий) [' . $info['fleet'][0] . ':' . $info['fleet'][1] . ':' . $info['fleet'][2] . ']</td></tr>
						<tr><th>Технология</th><th>Уровень</th><th>%</th></tr>
						<tr><th>Оружие</th><th>' . $info['tech']['military_tech'] . '</th><th>' . ($info['tech']['military_tech'] * 5) . '</th></tr>
						<tr><th>Щиты</th><th>' . $info['tech']['shield_tech'] . '</th><th>' . ($info['tech']['shield_tech'] * 3) . '</th></tr>
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

			$html .= '<td><table align="center">
						<tr><td class="c" colspan="3">' . $info['username'] . ' (Защитник) [' . $info['fleet'][0] . ':' . $info['fleet'][1] . ':' . $info['fleet'][2] . ']</td></tr>
						<tr><th>Технология</th><th>Уровень</th><th>%</th></tr>
						<tr><th>Оружие</th><th>' . $info['tech']['military_tech'] . '</th><th>' . ($info['tech']['military_tech'] * 5) . '</th></tr>
						<tr><th>Щиты</th><th>' . $info['tech']['shield_tech'] . '</th><th>' . ($info['tech']['shield_tech'] * 3) . '</th></tr>
						<tr><th>Броня</th><th>' . $info['tech']['defence_tech'] . '</th><th>' . ($info['tech']['defence_tech'] * 5) . '</th></tr>
						<tr><th>Лазер</th><th>' . $info['tech']['laser_tech'] . '</th><th>' . ($info['tech']['laser_tech'] * 5) . '</th></tr>
						<tr><th>Ион</th><th>' . $info['tech']['ionic_tech'] . '</th><th>' . ($info['tech']['ionic_tech'] * 5) . '</th></tr>
						<tr><th>Плазма</th><th>' . $info['tech']['buster_tech'] . '</th><th>' . ($info['tech']['buster_tech'] * 5) . '</th></tr></table></td>';

			$checkName[] = $info['username'];
		}
	}

	$html .= '</tr></table><br>';

	$round_no = 1;
	foreach ($result_array['rw'] as $round => $data1)
	{

		$attackers1 = $data1['attackers'];
		$defenders1 = $data1['defenders'];

		$html .= "<table width=100%><tr>";

		foreach ($attackers1 as $fleet_id1 => $data2)
		{

			$html .= "<td class='c'><table border=1 width=100%><tr><th><center>";
			$html .= "Атакующий " . $attackUsers[$fleet_id1]['username'] . " ([" . $attackUsers[$fleet_id1]['fleet'][0] . ":" . $attackUsers[$fleet_id1]['fleet'][1] . ":" . $attackUsers[$fleet_id1]['fleet'][2] . "])<br />";

			$html .= "<table border=1>";

			if ($data1['attackA'][$fleet_id1] > 0)
			{
				$raport1 = "<tr><th>Тип</th>";
				$raport2 = "<tr><th>Кол-во</th>";
				$raport3 = "<tr><th>Вооружение</th>";
				$raport4 = "<tr><th>Броня</th>";

				foreach ($data2 as $ship_id1 => $ship_count1)
				{
					if ($ship_count1 > 0)
					{
						$raport1 .= "<th>" . _getText('tech', $ship_id1) . "</th>";

						if ($round == 0)
							$raport2 .= "<th>" . strings::pretty_number(ceil($ship_count1)) . "</th>";
						else
						{
							$raport2 .= "<th>" . strings::pretty_number(ceil($ship_count1)) . "";

							if (ceil($result_array['rw'][$round - 1]['attackers'][$fleet_id1][$ship_id1]) - ceil($ship_count1) > 0)
								$raport2 .= " <small><font color='red'>-" . (ceil($result_array['rw'][$round - 1]['attackers'][$fleet_id1][$ship_id1]) - ceil($ship_count1)) . "</font></small>";

							$raport2 .= "</th>";
						}

						$attTech = 1 + $attackUsers[$fleet_id1]['tech']['military_tech'] * 0.05 + ($attackUsers[$fleet_id1]['flvl'][$ship_id1] * ($CombatCaps[$ship_id1]['power_up'] / 100));

						if ($CombatCaps[$ship_id1]['type_gun'] == 1)
							$attTech += $attackUsers[$fleet_id1]['tech']['laser_tech'] * 0.05;
						elseif ($CombatCaps[$ship_id1]['type_gun'] == 2)
							$attTech += $attackUsers[$fleet_id1]['tech']['ionic_tech'] * 0.05;
						elseif ($CombatCaps[$ship_id1]['type_gun'] == 3)
							$attTech += $attackUsers[$fleet_id1]['tech']['buster_tech'] * 0.05;

						$raport3 .= "<th>" . strings::pretty_number(round($CombatCaps[$ship_id1]['attack'] * $attTech)) . "</th>";
						$raport4 .= "<th>" . strings::pretty_number(round((($pricelist[$ship_id1]['metal'] + $pricelist[$ship_id1]['crystal']) / 10) * (1 + (($CombatCaps[$ship_id1]['power_up'] * $attackUsers[$fleet_id1]['flvl'][$ship_id1]) / 100) + $attackUsers[$fleet_id1]['tech']['defence_tech'] * 0.05))) . "</th>";
					}
				}

				$raport1 .= "</tr>";
				$raport2 .= "</tr>";
				$raport3 .= "</tr>";
				$raport4 .= "</tr>";
				$html .= $raport1 . $raport2 . $raport3 . $raport4;
			}
			else $html .= "<br>уничтожен";
			$html .= "</table><br>";

			if (isset($data1['logA']) && count($data1['logA']) > 0)
				$html .= "<span onclick='show(\"r" . $round . "A\")' style='cursor:pointer'>Подробности</span>";

			$html .= "</center></th></tr></table></td>";
		}

		$html .= "</tr></table>";

		if (isset($data1['logA']) && count($data1['logA']) > 0)
		{
			$html .= "<div id=\"r" . $round . "A\" style=\"display:none;\">";
			foreach ($data1['logA'] AS $log)
			{
				$html .= '<table width="100%"><tr><th>' . ceil($log[0]) . ' <u>' . _getText('tech', $log[1]) . '</u> (Атакующий) атакуют <u>' . _getText('tech', $log[2]) . '</u> мощностью ' . $log[3] . ' МДж</th></tr>
				<tr><th>' . floor($log[4]) . ' ед. <u>' . _getText('tech', $log[2]) . '</u> уничтожено (Защитник) Поглощение атаки: ' . $log[5] . '</th></tr></table>';
			}
			$html .= '</div>';
		}

		$html .= "<table width=100%><tr>";

		foreach ($defenders1 as $fleet_id1 => $data2)
		{
			$html .= "<td class='c'><table border=1 width=100%><tr><th><center>";
			$html .= "Защитник " .$defenseUsers[$fleet_id1]['username'] . " ([" . $defenseUsers[$fleet_id1]['fleet'][0] . ":" . $defenseUsers[$fleet_id1]['fleet'][1] . ":" . $defenseUsers[$fleet_id1]['fleet'][2] . "])<br />";

			$html .= "<table border=1 align=\"center\">";

			if ($data1['defenseA'][$fleet_id1] > 0)
			{
				$raport1 = "<tr><th>Тип</th>";
				$raport2 = "<tr><th>Кол-во</th>";
				$raport3 = "<tr><th>Вооружение</th>";
				$raport4 = "<tr><th>Броня</th>";

				foreach ($data2 as $ship_id1 => $ship_count1)
				{
					if ($ship_count1 > 0)
					{
						$raport1 .= "<th>" . _getText('tech', $ship_id1) . "</th>";

						if ($round == 0)
							$raport2 .= "<th>" . strings::pretty_number(ceil($ship_count1)) . "</th>";
						else
						{
							$raport2 .= "<th>" . strings::pretty_number(ceil($ship_count1)) . "";

							if (ceil($result_array['rw'][$round - 1]['defenders'][$fleet_id1][$ship_id1]) - ceil($ship_count1) > 0)
								$raport2 .= " <small><font color='red'>-" . (ceil($result_array['rw'][$round - 1]['defenders'][$fleet_id1][$ship_id1]) - ceil($ship_count1)) . "</font></small>";

							$raport2 .= "</th>";
						}

						$attTech = 1 + $defenseUsers[$fleet_id1]['tech']['military_tech'] * 0.05 + ($defenseUsers[$fleet_id1]['flvl'][$ship_id1] * ($CombatCaps[$ship_id1]['power_up'] / 100));

						if ($CombatCaps[$ship_id1]['type_gun'] == 1)
							$attTech += $defenseUsers[$fleet_id1]['tech']['laser_tech'] * 0.05;
						elseif ($CombatCaps[$ship_id1]['type_gun'] == 2)
							$attTech += $defenseUsers[$fleet_id1]['tech']['ionic_tech'] * 0.05;
						elseif ($CombatCaps[$ship_id1]['type_gun'] == 3)
							$attTech += $defenseUsers[$fleet_id1]['tech']['buster_tech'] * 0.05;

						$raport3 .= "<th>" . strings::pretty_number(round($CombatCaps[$ship_id1]['attack'] * $attTech)) . "</th>";
						$raport4 .= "<th>" . strings::pretty_number(round((($pricelist[$ship_id1]['metal'] + $pricelist[$ship_id1]['crystal']) / 10) * (1 + (($CombatCaps[$ship_id1]['power_up'] * $defenseUsers[$fleet_id1]['flvl'][$ship_id1]) / 100) + $defenseUsers[$fleet_id1]['tech']['defence_tech'] * 0.05))) . "</th>";
					}
				}
				$raport1 .= "</tr>";
				$raport2 .= "</tr>";
				$raport3 .= "</tr>";
				$raport4 .= "</tr>";
				$html .= $raport1 . $raport2 . $raport3 . $raport4;
			}
			else $html .= "<br>уничтожен";
			$html .= "</table><br>";

			if (isset($data1['logD']) && count($data1['logD']) > 0)
				$html .= "<span onclick='show(\"r" . $round . "D\")' style='cursor:pointer'>Подробности</span>";

			$html .= "</center></th></tr></table></td>";
		}
		$html .= "</tr></table>";

		if (isset($data1['logD']) && count($data1['logD']) > 0)
		{
			$html .= "<div id=\"r" . $round . "D\" style=\"display:none;\">";
			foreach ($data1['logD'] AS $log)
			{
				$html .= '<table width="100%"><tr><th>' . ceil($log[0]) . ' <u>' . _getText('tech', $log[1]) . '</u> (Защитник) атакуют <u>' . _getText('tech', $log[2]) . '</u> мощностью ' . $log[3] . ' МДж</th></tr>
				<tr><th>' . floor($log[4]) . ' ед. <u>' . _getText('tech', $log[2]) . '</u> уничтожено (Атакующий) Поглощение атаки: ' . $log[5] . '</th></tr></table>';
			}
			$html .= "</div>";
		}

		if ($round_no < 7 && $data1['attackA']['total'] > 0 && $data1['defenseA']['total'] > 0)
		{
			$html .= "<center>Атакующий флот делает " . strings::pretty_number($data1['attackA']['total']) . " выстрела(ов) с общей мощностью " . strings::pretty_number($data1['attack']['total']) . " по защитнику. Щиты защитника поглощают " . strings::pretty_number($data1['defShield']) . " мощности.<br />";
			//if (isset($result_array['rw'][$round+1]['defenseA']['total']) && $result_array['rw'][$round+1]['defenseA']['total'] > 0)
			$html .= "Защитный флот делает " . strings::pretty_number($data1['defenseA']['total']) . " выстрела(ов) с общей мощностью " . strings::pretty_number($data1['defense']['total']) . " по атакующему. Щиты атакующего поглащают " . strings::pretty_number($data1['attackShield']) . " мощности.</center>";
			//else
			//	$html .= "Защитный флот уничтожен</center>";
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

	$html .= "<br><br><table width='100%'><tr><td class='c'>" . $result1 . "</td></tr>";

	$debirs_meta = ($result_array['debree']['att'][0] + $result_array['debree']['def'][0]);
	$debirs_crys = ($result_array['debree']['att'][1] + $result_array['debree']['def'][1]);

	$html .= "<tr><th>Атакующий потерял " . strings::pretty_number($result_array['lost']['att']) . " единиц.</th></tr>";
	$html .= "<tr><th>Обороняющийся потерял " . strings::pretty_number($result_array['lost']['def']) . " единиц.</th></tr>";
	$html .= "<tr><td class='c'>Поле обломков: " . strings::pretty_number($debirs_meta) . " металла и " . strings::pretty_number($debirs_crys) . " кристалла.</td></tr>";

	$html .= "<tr><th>Шанс появления луны составляет " . $moon_int . "%<br>";
	$html .= $moon_string . "</th></tr>";

	$html .= "</table><br></center>";

	return array('html' => $html, 'bbc' => $bbc);
}

function formatCREx ($result_array, $attackUsers, $defenseUsers, $steal_array, $moon_int, $moon_string)
{
	global $CombatCaps, $pricelist;

	$bbc = "";

	$html = "<center><div class='separator'></div>В " . datezone("d-m-Y H:i:s", $result_array['time']) . " произошёл бой между следующими флотами:<div class='separator'></div>";

	$users = array();

	foreach ($attackUsers AS $info)
	{
		if (!in_array($info['tech']['id'], $users))
			$users[] = $info['tech']['id'];
		else
			continue;

		$html .= '<div class="raportUser"><table align="center">
					<tr><td class="c" colspan="3">' . $info['username'] . ' [' . $info['fleet'][0] . ':' . $info['fleet'][1] . ':' . $info['fleet'][2] . ']</td></tr>
					<tr><th></th><th>Уровень</th><th>%</th></tr>
					<tr><th>Оружие</th><th>' . $info['tech']['military_tech'] . '</th><th>' . ($info['tech']['military_tech'] * 5) . '</th></tr>
					<tr><th>Щиты</th><th>' . $info['tech']['shield_tech'] . '</th><th>' . ($info['tech']['shield_tech'] * 3) . '</th></tr>
					<tr><th>Броня</th><th>' . $info['tech']['defence_tech'] . '</th><th>' . ($info['tech']['defence_tech'] * 5) . '</th></tr>
					<tr><th>Лазер</th><th>' . $info['tech']['laser_tech'] . '</th><th>' . ($info['tech']['laser_tech'] * 5) . '</th></tr>
					<tr><th>Ион</th><th>' . $info['tech']['ionic_tech'] . '</th><th>' . ($info['tech']['ionic_tech'] * 5) . '</th></tr>
					<tr><th>Плазма</th><th>' . $info['tech']['buster_tech'] . '</th><th>' . ($info['tech']['buster_tech'] * 5) . '</th></tr></table></div>';
	}

	foreach ($defenseUsers AS $info)
	{
		if (!in_array($info['tech']['id'], $users))
			$users[] = $info['tech']['id'];
		else
			continue;

		$html .= '<div class="raportUser"><table align="center">
					<tr><td class="c" colspan="3">' . $info['username'] . ' [' . $info['fleet'][0] . ':' . $info['fleet'][1] . ':' . $info['fleet'][2] . ']</td></tr>
					<tr><th></th><th>Уровень</th><th>%</th></tr>
					<tr><th>Оружие</th><th>' . $info['tech']['military_tech'] . '</th><th>' . ($info['tech']['military_tech'] * 5) . '</th></tr>
					<tr><th>Щиты</th><th>' . $info['tech']['shield_tech'] . '</th><th>' . ($info['tech']['shield_tech'] * 3) . '</th></tr>
					<tr><th>Броня</th><th>' . $info['tech']['defence_tech'] . '</th><th>' . ($info['tech']['defence_tech'] * 5) . '</th></tr>
					<tr><th>Лазер</th><th>' . $info['tech']['laser_tech'] . '</th><th>' . ($info['tech']['laser_tech'] * 5) . '</th></tr>
					<tr><th>Ион</th><th>' . $info['tech']['ionic_tech'] . '</th><th>' . ($info['tech']['ionic_tech'] * 5) . '</th></tr>
					<tr><th>Плазма</th><th>' . $info['tech']['buster_tech'] . '</th><th>' . ($info['tech']['buster_tech'] * 5) . '</th></tr></table></div>';
	}

	$html .= '<div class="separator"></div><div id="raportRaw">';

	$round_no = 1;
	foreach ($result_array['rw'] as $round => $data1)
	{

		$attackers1 = $data1['attackers'];
		$defenders1 = $data1['defenders'];

		$html .= "<h3><a link=\"yes\" href=\"#\">Раунд №".($round + 1)."</a></h3><div><table width=100%><tr>";

		foreach ($attackers1 as $fleet_id1 => $data2)
		{

			$html .= "<th>";
			$html .= "Атакующий " . $attackUsers[$fleet_id1]['username'] . " ([" . $attackUsers[$fleet_id1]['fleet'][0] . ":" . $attackUsers[$fleet_id1]['fleet'][1] . ":" . $attackUsers[$fleet_id1]['fleet'][2] . "])<br />";

			$html .= "<center><table border='0'>";

			if ($data1['attackA'][$fleet_id1] > 0 && count($data2))
			{
				$raport1 = "<tr><td class='c'></td>";
				$raport2 = "<tr><td class='c'>К-во</td>";
				$raport3 = "<tr><td class='c'>Мощь</td>";
				$raport4 = "<tr><td class='c'>Броня</td>";

				foreach ($data2 as $ship_id1 => $ship_count1)
				{
					if ($ship_count1 > 0)
					{
						$sN = explode(' ', _getText('tech', $ship_id1));
						foreach ($sN AS &$t)
							$t = mb_strtoupper(mb_substr($t, 0, 1, 'UTF-8'), 'UTF-8').'.';

						if ($ship_id1 == 208)
							$sN = Array('Кл.');

						$raport1 .= "<th>" . implode('', $sN) . "</th>";

						if ($round == 0)
							$raport2 .= "<th>" . strings::pretty_number(ceil($ship_count1)) . "</th>";
						else
						{
							$raport2 .= "<th>" . strings::pretty_number(ceil($ship_count1)) . "";

							if (ceil($result_array['rw'][$round - 1]['attackers'][$fleet_id1][$ship_id1]) - ceil($ship_count1) > 0)
								$raport2 .= " <small><font color='red'>-" . (ceil($result_array['rw'][$round - 1]['attackers'][$fleet_id1][$ship_id1]) - ceil($ship_count1)) . "</font></small>";

							$raport2 .= "</th>";
						}

						$attTech = 1 + $attackUsers[$fleet_id1]['tech']['military_tech'] * 0.05 + ($attackUsers[$fleet_id1]['flvl'][$ship_id1] * ($CombatCaps[$ship_id1]['power_up'] / 100));

						if ($CombatCaps[$ship_id1]['type_gun'] == 1)
							$attTech += $attackUsers[$fleet_id1]['tech']['laser_tech'] * 0.05;
						elseif ($CombatCaps[$ship_id1]['type_gun'] == 2)
							$attTech += $attackUsers[$fleet_id1]['tech']['ionic_tech'] * 0.05;
						elseif ($CombatCaps[$ship_id1]['type_gun'] == 3)
							$attTech += $attackUsers[$fleet_id1]['tech']['buster_tech'] * 0.05;

						$raport3 .= "<th>" . strings::pretty_number(round($CombatCaps[$ship_id1]['attack'] * $attTech)) . "</th>";
						$raport4 .= "<th>" . strings::pretty_number(round(($pricelist[$ship_id1]['metal'] + $pricelist[$ship_id1]['crystal'] + $pricelist[$ship_id1]['deuterium']) * (1 + (($CombatCaps[$ship_id1]['power_up'] * $attackUsers[$fleet_id1]['flvl'][$ship_id1]) / 100) + $attackUsers[$fleet_id1]['tech']['defence_tech'] * 0.05))) . "</th>";
					}
				}

				$raport1 .= "</tr>";
				$raport2 .= "</tr>";
				$raport3 .= "</tr>";
				$raport4 .= "</tr>";
				$html .= $raport1 . $raport2 . $raport3 . $raport4;
			}
			else
				$html .= "<tr><td><br>уничтожен</td></tr>";

			$html .= "</table>";
			$html .= "</center></th>";
		}

		$html .= "</tr></table>";

		$html .= "<div class='separator'></div><table width=100%><tr>";

		foreach ($defenders1 as $fleet_id1 => $data2)
		{
			$html .= "<th>";
			$html .= "Защитник " . $defenseUsers[$fleet_id1]['username'] . " ([" . $defenseUsers[$fleet_id1]['fleet'][0] . ":" . $defenseUsers[$fleet_id1]['fleet'][1] . ":" . $defenseUsers[$fleet_id1]['fleet'][2] . "])<br />";

			$html .= "<center><table border='0'>";

			if ($data1['defenseA'][$fleet_id1] > 0 && count($data2))
			{
				$raport1 = "<tr><td class='c'></td>";
				$raport2 = "<tr><td class='c'>К-во</td>";
				$raport3 = "<tr><td class='c'>Мощь</td>";
				$raport4 = "<tr><td class='c'>Броня</td>";

				foreach ($data2 as $ship_id1 => $ship_count1)
				{
					if ($ship_count1 > 0)
					{
						$sN = explode(' ', _getText('tech', $ship_id1));
						foreach ($sN AS &$t)
							$t = mb_strtoupper(mb_substr($t, 0, 1, 'UTF-8'), 'UTF-8').'.';

						if ($ship_id1 == 208)
							$sN = Array('Кл.');

						$raport1 .= "<th>" . implode('', $sN) . "</th>";

						if ($round == 0)
							$raport2 .= "<th>" . strings::pretty_number(ceil($ship_count1)) . "</th>";
						else
						{
							$raport2 .= "<th>" . strings::pretty_number(ceil($ship_count1)) . "";

							if (ceil($result_array['rw'][$round - 1]['defenders'][$fleet_id1][$ship_id1]) - ceil($ship_count1) > 0)
								$raport2 .= " <small><font color='red'>-" . (ceil($result_array['rw'][$round - 1]['defenders'][$fleet_id1][$ship_id1]) - ceil($ship_count1)) . "</font></small>";

							$raport2 .= "</th>";
						}

						$attTech = 1 + $defenseUsers[$fleet_id1]['tech']['military_tech'] * 0.05 + ($defenseUsers[$fleet_id1]['flvl'][$ship_id1] * ($CombatCaps[$ship_id1]['power_up'] / 100));

						if ($CombatCaps[$ship_id1]['type_gun'] == 1)
							$attTech += $defenseUsers[$fleet_id1]['tech']['laser_tech'] * 0.05;
						elseif ($CombatCaps[$ship_id1]['type_gun'] == 2)
							$attTech += $defenseUsers[$fleet_id1]['tech']['ionic_tech'] * 0.05;
						elseif ($CombatCaps[$ship_id1]['type_gun'] == 3)
							$attTech += $defenseUsers[$fleet_id1]['tech']['buster_tech'] * 0.05;

						$raport3 .= "<th>" . strings::pretty_number(round($CombatCaps[$ship_id1]['attack'] * $attTech)) . "</th>";
						$raport4 .= "<th>" . strings::pretty_number(round(($pricelist[$ship_id1]['metal'] + $pricelist[$ship_id1]['crystal'] + $pricelist[$ship_id1]['deuterium']) * (1 + (($CombatCaps[$ship_id1]['power_up'] * $defenseUsers[$fleet_id1]['flvl'][$ship_id1]) / 100) + $defenseUsers[$fleet_id1]['tech']['defence_tech'] * 0.05))) . "</th>";
					}
				}
				$raport1 .= "</tr>";
				$raport2 .= "</tr>";
				$raport3 .= "</tr>";
				$raport4 .= "</tr>";
				$html .= $raport1 . $raport2 . $raport3 . $raport4;
			}
			else
				$html .= "<tr><td><br>уничтожен</td></tr>";

			$html .= "</table>";

			$html .= "</center></th>";
		}

		$html .= "</tr></table></div>";

		$round_no++;
	}

	$html .= '</div>';

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

	$html .= "<div class='separator'></div><table width='100%'><tr><td class='c'>" . $result1 . "</td></tr>";

	$debirs_meta = ($result_array['debree']['att'][0] + $result_array['debree']['def'][0]);
	$debirs_crys = ($result_array['debree']['att'][1] + $result_array['debree']['def'][1]);

	$html .= "<tr><th>Атакующий потерял " . strings::pretty_number($result_array['lost']['att']) . " единиц.</th></tr>";
	$html .= "<tr><th>Обороняющийся потерял " . strings::pretty_number($result_array['lost']['def']) . " единиц.</th></tr>";
	$html .= "<tr><td class='c'>Поле обломков: " . strings::pretty_number($debirs_meta) . " металла и " . strings::pretty_number($debirs_crys) . " кристалла.</td></tr>";

	$html .= "<tr><th>Шанс появления луны составляет " . $moon_int . "%<br>";
	$html .= $moon_string . "</th></tr>";

	$html .= "</table></center>";

	return array('html' => $html, 'bbc' => $bbc);
}

?>