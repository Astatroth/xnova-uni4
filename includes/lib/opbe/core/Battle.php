<?php
use Xcms\core;

/**
 *  OPBE
 *  Copyright (C) 2013  Jstar
 *
 * This file is part of OPBE.
 *
 * OPBE is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPBE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with OPBE.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OPBE
 * @author Jstar <frascafresca@gmail.com>
 * @copyright 2013 Jstar <frascafresca@gmail.com>
 * @license http://www.gnu.org/licenses/ GNU AGPLv3 License
 * @version beta(26-10-2013)
 * @link https://github.com/jstar88/opbe
 */
class Battle
{
	private $attackers;
	private $defenders;
	private $report;
	private $battleStarted;
	private $rounds = 6;

	/**
	 * @param PlayerGroup $attackers
	 * @param PlayerGroup $defenders
	 * @param int $rounds
	 * @return Battle
	 */
	public function __construct (PlayerGroup $attackers, PlayerGroup $defenders, $rounds = 6)
	{
		$this->attackers = $attackers;
		$this->defenders = $defenders;
		$this->battleStarted = false;
		$this->report = new BattleReport();
		$this->rounds = $rounds;
	}

	/**
	 * @param bool $debug
	 * @param int $rounds
	 * @return bool
	 */
	public function startBattle ($debug = false, $rounds = 6)
	{
		if (!$debug)
			ob_start();

		$this->battleStarted = true;
		//only for initial fleets presentation
		if (BATTLE_DEBUG)
			echo $this->attackers . '<br>' . PHP_EOL . $this->defenders;

		$round = new Round($this->attackers, $this->defenders, 0);
		$this->report->addRound($round);

		for ($i = 1; $i <= $rounds; $i++)
		{
			$attLose = $this->attackers->isEmpty();
			$defLose = $this->defenders->isEmpty();

			//if one of they are empty then battle is ended, so update the status
			if ($attLose || $defLose)
			{
				$this->checkWhoWon($attLose, $defLose);
				$this->report->setBattleResult($this->attackers->battleResult, $this->defenders->battleResult);

				if (!$debug)
					ob_get_clean();

				return false;
			}

			//initialize the round
			$round = new Round($this->attackers, $this->defenders, $i);
			$round->startRound();
			//add the round to the combatReport
			$this->report->addRound($round);

			//update the attackers and defenders after round
			$this->attackers = $round->getAfterBattleAttackers();
			$this->defenders = $round->getAfterBattleDefenders();
		}
		//check status after all rounds
		$this->checkWhoWon($this->attackers->isEmpty(), $this->defenders->isEmpty());

		if (!$debug)
			ob_get_clean();

		return true;
	}

	/**
	 * Assign to groups the status win,lose or draw
	 * @param boolean $att_lose
	 * @param boolean $deff_lose
	 * @return null
	 */
	private function checkWhoWon ($att_lose, $deff_lose)
	{
		if ($att_lose && !$deff_lose)
		{
			$this->attackers->battleResult = BATTLE_LOSE;
			$this->defenders->battleResult = BATTLE_WIN;
		}
		elseif (!$att_lose && $deff_lose)
		{
			$this->attackers->battleResult = BATTLE_WIN;
			$this->defenders->battleResult = BATTLE_LOSE;
		}
		else
		{
			$this->attackers->battleResult = BATTLE_DRAW;
			$this->defenders->battleResult = BATTLE_DRAW;
		}
	}

	/**
	 * @return string
	 */
	public function __toString ()
	{
		return $this->report->__toString();
	}

	/**
	 * Start the battle if not and return the report.
	 * @return BattleReport
	 */
	public function getReport ()
	{
		if (!$this->battleStarted)
		{
			$this->startBattle(BATTLE_DEBUG, $this->rounds);
		}

		return $this->report;
	}
}
