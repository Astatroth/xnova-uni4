<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xcms\request;
use Xcms\sql;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (request::P('name', '') != '')
{
	$name = htmlspecialchars(request::P('name', ''));
	$reas = htmlspecialchars(request::P('why', ''));

	$days = request::P('days', 0, VALUE_INT);
	$hour = request::P('hour', 0, VALUE_INT);
	$mins = request::P('mins', 0, VALUE_INT);

	$userz = db::query("SELECT id FROM game_users WHERE username = '" . $name . "';", true);

	if (!isset($userz['id']))
		$this->message(_getText('sys_noalloaw'), 'Игрок не найден');

	$BanTime = $days * 86400;
	$BanTime += $hour * 3600;
	$BanTime += $mins * 60;
	$BanTime += time();

	sql::build()->insert('game_banned')->set(array
	(
		'who'		=> $userz['id'],
		'theme'		=> $reas,
		'time'		=> time(),
		'longer'	=> $BanTime,
		'author'	=> user::get()->getId()
	))->execute();

	sql::build()->update('game_users')->setField('banaday', $BanTime);

	if (request::P('ro', 0, VALUE_INT) == 1)
		sql::build()->setField('urlaubs_modus_time', 1);

	sql::build()->where('id', '=', $userz['id'])->execute();

	if (request::P('ro', 0, VALUE_INT) == 1)
	{
		global $reslist, $resource;

		$arFields = array
		(
			$resource[4].'_porcent' 	=> 0,
			$resource[12].'_porcent' 	=> 0,
			$resource[212].'_porcent' 	=> 0
		);

		foreach ($reslist['res'] AS $res)
			$arFields[$res.'_mine_porcent'] = 0;

		sql::build()->update('game_planets')->set($arFields)->where('id_owner', '=', $userz['id'])->execute();
	}

	$this->message(_getText('adm_bn_thpl') . " " . $name . " " . _getText('adm_bn_isbn'), _getText('adm_bn_ttle'));
}

$this->setTemplate('banned');
$this->setTitle(_getText('adm_bn_ttle'));
$this->display();

?>