<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xcms\request;
use Xcms\sql;
use Xcms\strings;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] < 3)
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

$action = request::R('action', '');

switch ($action)
{
	case 'add':

		if (request::P('username', '') != '')
		{
			$username = request::P('username');

			$info = db::query("SELECT id FROM game_users u WHERE ".(is_numeric($username) ? "`id` = '" . $username . "'" : "`username` = '" . $username . "'")." LIMIT 1;", true);

			if (!isset($info['id']))
				$this->message('Такого игрока не существует', 'Ошибка', '/admin/mode/money/action/add/', 2);

			$money = request::P('money', 0, VALUE_INT);

			if ($money > 0)
			{
				sql::build()->update('game_users')->setField('+credits', $money)->where('id', '=', $info['id'])->execute();

				sql::build()->insert('game_log_credits')->set(array
				(
					'uid' => $info['id'],
					'time' => time(),
					'credits' => $money,
					'type' => 6
				))
				->execute();

				$this->message('Начисление '.$money.' кредитов прошло успешно', 'Всё ок!', '/admin/mode/money/action/add/', 2);
			}
		}

		$this->setTemplate('money_add');
		$this->display('', "Начисление кредитов", false, true);

		break;
	case 'transactions':

		$parse = array();
		$parse['list'] = array();

		$start = request::G('p', 0, VALUE_INT);
		$limit = 25;

		$elements = db::query("SELECT p.*, u.username FROM game_users_payments p LEFT JOIN game_users u ON u.id = p.user ORDER BY p.id DESC LIMIT ".$start.",".$limit."");

		while ($element = db::fetch($elements))
		{
			$parse['list'][] = $element;
		}

		$total = db::first(db::query("SELECT COUNT(*) AS num FROM game_users_payments", true));

		$parse['total'] = $total;
		$parse['pagination'] = strings::pagination($total, 25, '/admin/mode/money/action/transactions/', $start);

		$this->setTemplate('money_transactions');
		$this->set('parse', $parse);
		$this->setTitle("Транзакции");
		$this->display();

		break;

	default:

		request::redirectTo('/admin/');
}
 
?>