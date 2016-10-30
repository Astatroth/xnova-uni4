<?php

use Xcms\core;

function calculateAttack (&$attackers, &$defenders, $attackUsers, $defenseUsers, $injener, $max_rounds = 6)
{
	global $pricelist, $CombatCaps, $gun_armour;

	if ($max_rounds == 0)
		$max_rounds = 6;

	$originalDef = array();

	// Общее количество структуры кораблей
	$totalResourcePoints = array('attacker' => 0, 'defender' => 0);
	// Количество структуры кораблей атакующего
	$resourcePointsAttacker = array('metal' => 0, 'crystal' => 0);
	foreach ($attackers as $attacker)
	{
		foreach ($attacker as $element => $amount)
		{
			// $element - тип корабля
			// $amount - количество кораблей данного типа
			$resourcePointsAttacker['metal'] += $pricelist[$element]['metal'] * $amount;
			$resourcePointsAttacker['crystal'] += $pricelist[$element]['crystal'] * $amount;

			$totalResourcePoints['attacker'] += $pricelist[$element]['metal'] * $amount;
			$totalResourcePoints['attacker'] += $pricelist[$element]['crystal'] * $amount;
		}
	}
	// Количество структуры кораблей защищаегося
	$resourcePointsDefender = array('metal' => 0, 'crystal' => 0);
	foreach ($defenders as $defender)
	{
		foreach ($defender as $element => $amount)
		{
			// Не считаем оборону как патери
			if ($element < 300)
			{
				$resourcePointsDefender['metal'] += $pricelist[$element]['metal'] * $amount;
				$resourcePointsDefender['crystal'] += $pricelist[$element]['crystal'] * $amount;
			}
			else
			{
				if (!isset($originalDef[$element]))
					$originalDef[$element] = 0;
				$originalDef[$element] += $amount;
			}
			$totalResourcePoints['defender'] += $pricelist[$element]['metal'] * $amount;
			$totalResourcePoints['defender'] += $pricelist[$element]['crystal'] * $amount;
		}
	}

	$rounds = array();

	for ($round = 0; $round < $max_rounds; $round++)
	{
		$attackDamage = array('total' => 0);
		$attackAmount = array('total' => 0);
		$defenseDamage = array('total' => 0);
		$defenseAmount = array('total' => 0);
		$attArray = array();
		$defArray = array();

		foreach ($attackers as $fleetID => $attacker)
		{
			$attackDamage[$fleetID] = 0;
			$attackAmount[$fleetID] = 0;

			foreach ($attacker as $element => $amount)
			{
				if ($amount < 0.1)
				{
					unset($attackers[$fleetID][$element]);
					continue;
				}

				$attTech = 1 + ($attackUsers[$fleetID]['flvl'][$element] * ($CombatCaps[$element]['power_up'] / 100)) + $attackUsers[$fleetID]['tech']['military_tech'] * 0.05;

				if ($CombatCaps[$element]['type_gun'] == 1)
					$attTech += $attackUsers[$fleetID]['tech']['laser_tech'] * 0.05;
				elseif ($CombatCaps[$element]['type_gun'] == 2)
					$attTech += $attackUsers[$fleetID]['tech']['ionic_tech'] * 0.05;
				elseif ($CombatCaps[$element]['type_gun'] == 3)
					$attTech += $attackUsers[$fleetID]['tech']['buster_tech'] * 0.05;

				$thisAtt = round(ceil($amount) * ($CombatCaps[$element]['attack']) * $attTech);

				$attArray[$fleetID][$element] = array('att' => $thisAtt);

				$attackDamage[$fleetID] += $thisAtt;
				$attackDamage['total'] += $thisAtt;

				$attackAmount[$fleetID] += ceil($amount);
				$attackAmount['total'] += ceil($amount);
			}
		}
		foreach ($defenders as $fleetID => $defender)
		{
			$defenseDamage[$fleetID] = 0;
			$defenseAmount[$fleetID] = 0;

			foreach ($defender as $element => $amount)
			{
				if ($amount < 0.1)
				{
					unset($defenders[$fleetID][$element]);
					continue;
				}

				$attTech = 1 + ($defenseUsers[$fleetID]['flvl'][$element] * ($CombatCaps[$element]['power_up'] / 100)) + $defenseUsers[$fleetID]['tech']['military_tech'] * 0.05;

				if ($CombatCaps[$element]['type_gun'] == 1)
					$attTech += $defenseUsers[$fleetID]['tech']['laser_tech'] * 0.05;
				elseif ($CombatCaps[$element]['type_gun'] == 2)
					$attTech += $defenseUsers[$fleetID]['tech']['ionic_tech'] * 0.05;
				elseif ($CombatCaps[$element]['type_gun'] == 3)
					$attTech += $defenseUsers[$fleetID]['tech']['buster_tech'] * 0.05;

				$thisAtt = round(ceil($amount) * ($CombatCaps[$element]['attack']) * $attTech);

				$defArray[$fleetID][$element] = array('att' => $thisAtt);

				if ($element == 407 || $element == 408)
					$defArray[$fleetID][$element]['shield'] = $CombatCaps[$element]['shield'];

				$defenseDamage[$fleetID] += $thisAtt;
				$defenseDamage['total'] += $thisAtt;

				$defenseAmount[$fleetID] += ceil($amount);
				$defenseAmount['total'] += ceil($amount);
			}
		}

		$rounds[$round] = array('attackers' => $attackers, 'defenders' => $defenders, 'attack' => $attackDamage, 'defense' => $defenseDamage, 'attackA' => $attackAmount, 'defenseA' => $defenseAmount, 'logA' => NULL, 'logD' => NULL);

		if ($defenseAmount['total'] <= 0 || $attackAmount['total'] <= 0)
		{
			break;
		}

		$attacker_shield = 0;
		$defender_shield = 0;

		$attacker_n = $attackers;
		$defender_n = $defenders;

		foreach ($attackers as $fleetID => $attacker)
		{
			if ($attackAmount[$fleetID] > 0)
			{
				foreach ($attacker as $element => $amount)
				{
					if ($amount > 0)
					{
						$amount_1 = array();
						$amount_3 = $amount;

						$amount_2 = 0;

						for ($i = 1; $i <= 5; $i++)
						{
							if ($amount < pow(5, ($i + 1)))
							{
								$amount_2 = $i;
								break;
							}
						}

						if (!$amount_2)
							$amount_2 = 5;

						for ($i = 0; $i < $amount_2; $i++)
						{
							$amount_1[] = ceil($amount_3 / ($amount_2 - $i));
							$amount_3 -= $amount_1[$i];
						}

						for ($i = 0; $i < count($amount_1); $i++)
						{
							$fire = true;
							$m_power = 0;

							while ($fire)
							{
								// Выбор атакуемой группы
								$enemy_id = array_rand($defender_n);
								if (count($defender_n[$enemy_id]) == 0)
								{
									foreach ($defender_n AS $id => $fleet)
										if (count($fleet) != 0)
											$enemy_id = $id;
										else
										{
											$fire = false;
											continue;
										}
								}
								$enemy_type = array_rand($defender_n[$enemy_id]);
								if (!$enemy_type)
								{
									$fire = false;
									continue;
								}
								// Вычисляем бонус к атаке против данного типа кораблей
						  		$amplify = isset($CombatCaps[$element]['amplify'][$enemy_type]) ? $CombatCaps[$element]['amplify'][$enemy_type] : 1;
								// Вычисление силы атаки кораблей
								$att = floor(($attArray[$fleetID][$element]['att'] / ceil($amount)) * $amount_1[$i] * $amplify - $m_power);
								// Поглащение атаки
								$shield = round(($att * (($CombatCaps[$enemy_type]['power_armour'] / 100) + $defenseUsers[$enemy_id]['tech']['shield_tech'] * 0.03)) * ((100 - $gun_armour[$CombatCaps[$element]['type_gun']][$CombatCaps[$enemy_type]['type_armour']]) / 100));
								// Мощность атаки нападающего
								/*if ($element != 211 || $enemy_type < 400)
								{
									if ($element == 204 && $enemy_type == 214)
										$power = $att * 2;
									else
										$power = $att;
								}
								else
									$power = $att * 3;
								*/

								$power = $att;
								$power -= $shield;
								// Щиты у обороны
								// Малый купол
								if (isset($defender_n[0]['407']) && $defender_n[0]['407'] > 0 && isset($defArray[0]['407']['shield']) && $defArray[0]['407']['shield'] > 0 && $power > 0)
								{
									if ($power > $defArray[0]['407']['shield'])
									{
										$power -= $defArray[0]['407']['shield'];
										$shield += $defArray[0]['407']['shield'];
										$defArray[0]['407']['shield'] = 0;
									}
									else
									{
										$shield += $power;
										$defArray[0]['407']['shield'] -= $power;
										$power = 0;
									}
								}
								// Большой купол
								if (isset($defender_n[0]['408']) && $defender_n[0]['408'] > 0 && isset($defArray[0]['408']['shield']) && $defArray[0]['408']['shield'] > 0 && $power > 0)
								{
									if ($power > $defArray[0]['408']['shield'])
									{
										$power -= $defArray[0]['408']['shield'];
										$shield += $defArray[0]['408']['shield'];
										$defArray[0]['408']['shield'] = 0;
									}
									else
									{
										$shield += $power;
										$defArray[0]['408']['shield'] -= $power;
										$power = 0;
									}
								}

								// Вычисление брони
								$def = (((($pricelist[$enemy_type]['metal'] + $pricelist[$enemy_type]['crystal']) / 10)) * (1 + $defenseUsers[$enemy_id]['tech']['defence_tech'] * 0.05 + (($CombatCaps[$enemy_type]['power_up'] * $defenseUsers[$enemy_id]['flvl'][$enemy_type]) / 100)));
								// Целочисленное количество убитых кораблий
								$removeShips = ($power / $def);
								$removeShips = round($removeShips, 3);
								// Анти-баг
								if ($removeShips < 0)
									$removeShips = 0;
								//if ($removeShips > $amount_1[$i])
								//	$removeShips = $amount_1[$i];
								if ($removeShips > $defender_n[$enemy_id][$enemy_type])
									$removeShips = ceil($defender_n[$enemy_id][$enemy_type]);
								// Добавление в лог
								$rounds[$round]['logA'][] = array($amount_1[$i], $element, $enemy_type, ($power + $shield), $removeShips, $shield);
								// Удаление кораблей из массива
								$defender_n[$enemy_id][$enemy_type] -= $removeShips;
								// Анти-баг
								if ($defender_n[$enemy_id][$enemy_type] <= 0)
								{
									$shield = 0;

									unset($defender_n[$enemy_id][$enemy_type]);

									$m_power += ($def + $shield);
								}
								else
									$fire = false;

								// Поглощение урона
								$defender_shield += $shield;
							}
						}
					}
				}
			}
		}

		foreach ($defenders as $fleetID => $defender)
		{
			if ($defenseAmount[$fleetID] > 0)
			{
				foreach ($defender as $element => $amount)
				{
					if ($amount > 0)
					{
						$amount_1 = array();
						$amount_3 = $amount;

						$amount_2 = 0;

						for ($i = 1; $i <= 5; $i++)
						{
							if ($amount < pow(5, ($i + 1)))
							{
								$amount_2 = $i;
								break;
							}
						}

						if (!$amount_2)
							$amount_2 = 5;

						for ($i = 0; $i < $amount_2; $i++)
						{
							$amount_1[] = ceil($amount_3 / ($amount_2 - $i));
							$amount_3 -= $amount_1[$i];
						}


						for ($i = 0; $i < count($amount_1); $i++)
						{
							$fire = true;
							$m_power = 0;

							while ($fire)
							{
								// Выбор атакуемой группы
								$enemy_id = array_rand($attacker_n);
								if (count($attacker_n[$enemy_id]) == 0)
								{
									foreach ($attacker_n AS $id => $fleet)
										if (count($fleet) != 0)
											$enemy_id = $id;
										else
										{
											$fire = false;
											continue;
										}
								}
								$enemy_type = array_rand($attacker_n[$enemy_id]);
								if (!$enemy_type)
								{
									$fire = false;
									continue;
								}
								// Вычисляем бонус к атаке против данного типа кораблей
								$amplify = isset($CombatCaps[$element]['amplify'][$enemy_type]) ? $CombatCaps[$element]['amplify'][$enemy_type] : 1;
								// Вычисление силы атаки кораблей
								$att = floor($defArray[$fleetID][$element]['att'] / ceil($amount)) * $amount_1[$i] * $amplify - $m_power;
								// Поглащение атаки
								$shield = round(($att * (($CombatCaps[$enemy_type]['power_armour'] / 100) + $attackUsers[$enemy_id]['tech']['shield_tech'] * 0.03)) * ((100 - $gun_armour[$CombatCaps[$element]['type_gun']][$CombatCaps[$enemy_type]['type_armour']]) / 100));
								//$shield = $attacker_n[$enemy_id][$enemy_type] * $CombatCaps[$enemy_type]['shield'];
								// Мощность атаки нападающего
								$power = $att;
								$power -= $shield;

								// Вычисление брони
								$def = (((($pricelist[$enemy_type]['metal'] + $pricelist[$enemy_type]['crystal']) / 10)) * (1 + $attackUsers[$enemy_id]['tech']['defence_tech'] * 0.05 + (($CombatCaps[$enemy_type]['power_up'] * $attackUsers[$enemy_id]['flvl'][$enemy_type]) / 100)));
								// Целочисленное количество убитых кораблий
								$removeShips = ($power / $def);
								$removeShips = round($removeShips, 3);

								// Анти-баг
								if ($removeShips < 0)
									$removeShips = 0;
								//if ($removeShips > $amount_1[$i])
								//	$removeShips = $amount_1[$i];
								if ($removeShips > $attacker_n[$enemy_id][$enemy_type])
									$removeShips = ceil($attacker_n[$enemy_id][$enemy_type]);
								// Добавление в лог
								$rounds[$round]['logD'][] = array($amount_1[$i], $element, $enemy_type, ($power + $shield), $removeShips, $shield);
								// Удаление кораблей из массива
								$attacker_n[$enemy_id][$enemy_type] -= $removeShips;
								// Анти-баг
								if ($attacker_n[$enemy_id][$enemy_type] <= 0)
								{
									$shield = 0;

									unset($attacker_n[$enemy_id][$enemy_type]);

									$m_power += ($def + $shield);
								}
								else
									$fire = false;

								// Поглощение урона
								$attacker_shield += $shield;
							}
						}
					}
				}
			}
		}

		$rounds[$round]['attackShield'] = round($attacker_shield);
		$rounds[$round]['defShield'] = round($defender_shield);

		$attackers = $attacker_n;
		$defenders = $defender_n;
	}

	$attackAmount['total'] = 0;
	foreach ($attackers as $fleetID => $attacker)
	{
		$attackers[$fleetID] = array_map('ceil', $attacker);
		foreach ($attacker AS $amount)
			$attackAmount['total'] += $amount;
	}

	$defenseAmount['total'] = 0;
	foreach ($defenders as $fleetID => $defender)
	{
		$defenders[$fleetID] = array_map('ceil', $defender);
		foreach ($defender AS $amount)
			$defenseAmount['total'] += $amount;
	}

	if ($attackAmount['total'] <= 0)
	{
		$won = 2; // defender
	}
	elseif ($defenseAmount['total'] <= 0)
	{
		$won = 1; // attacker
	}
	else
	{
		$won = 0; // draw
		$rounds[count($rounds)] = array('attackers' => $attackers, 'defenders' => $defenders, 'attack' => NULL, 'defense' => NULL, 'attackA' => $attackAmount, 'defenseA' => $defenseAmount);
	}

	foreach ($attackers as $attacker)
	{
		foreach ($attacker as $element => $amount)
		{
			$totalResourcePoints['attacker'] -= $pricelist[$element]['metal'] * $amount;
			$totalResourcePoints['attacker'] -= $pricelist[$element]['crystal'] * $amount;

			$resourcePointsAttacker['metal'] -= $pricelist[$element]['metal'] * $amount;
			$resourcePointsAttacker['crystal'] -= $pricelist[$element]['crystal'] * $amount;
		}
	}

	foreach ($originalDef AS $element => $amount)
	{
		if (!isset($defenders[0][$element]))
			$defenders[0][$element] = 0;
	}

	$resourceDefDebris = array('metal' => 0, 'crystal' => 0);

	foreach ($defenders as $fleetID => $defender)
	{
		foreach ($defender as $element => $amount)
		{
			if ($element < 300)
			{
				$resourcePointsDefender['metal'] -= $pricelist[$element]['metal'] * $amount;
				$resourcePointsDefender['crystal'] -= $pricelist[$element]['crystal'] * $amount;
			}
			else
			{
				$lost = $originalDef[$element] - $amount;
				$k = ($injener > time()) ? 0.85 : 0.7;
				$giveback = floor($lost * $k);
				$defenders[$fleetID][$element] += $giveback;

				$resourceDefDebris['metal'] += $pricelist[$element]['metal'] * ($lost - $giveback);
				$resourceDefDebris['crystal'] += $pricelist[$element]['crystal'] * ($lost - $giveback);;
			}
			$totalResourcePoints['defender'] -= $pricelist[$element]['metal'] * $amount;
			$totalResourcePoints['defender'] -= $pricelist[$element]['crystal'] * $amount;
		}
	}

	$totalLost = array('att' => $totalResourcePoints['attacker'], 'def' => $totalResourcePoints['defender']);

	$debAttMet = 0;
	$debAttCry = 0;
	$debDefMet = 0;
	$debDefCry = 0;

	$fleeDebrisRate = core::getConfig('fleetDebrisRate', 0);

	if ($fleeDebrisRate > 0 && $fleeDebrisRate <= 1)
	{
		$debAttMet = $resourcePointsAttacker['metal'] * $fleeDebrisRate;
		$debAttCry = $resourcePointsAttacker['crystal'] * $fleeDebrisRate;
		$debDefMet = $resourcePointsDefender['metal'] * $fleeDebrisRate;
		$debDefCry = $resourcePointsDefender['crystal'] * $fleeDebrisRate;
	}

	$defsDebrisRate = core::getConfig('defsDebrisRate', 0);

	if ($defsDebrisRate > 0 && $defsDebrisRate <= 1)
	{
		$debDefMet += $resourceDefDebris['metal'] * $defsDebrisRate;
		$debDefCry += $resourceDefDebris['crystal'] * $defsDebrisRate;
	}

	return array('time' => time(), 'won' => $won, 'debree' => array('att' => array($debAttMet, $debAttCry), 'def' => array($debDefMet, $debDefCry)), 'rw' => $rounds, 'lost' => $totalLost);
}

?>