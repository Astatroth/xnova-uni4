<?php

use Xcms\core;
use Xcms\db;
use Xcms\strings;
use Xnova\gameBot;

if (!extension_loaded('memcache'))
{
	dl('memcache.so');
}

define('INSIDE', true);
define('IS_CRON', true);

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__.'../');

include($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/core.php');
core::init('UNI4');

error_reporting(E_ALL);
ini_set('display_errors', 1);

strings::setLang('ru');

include_once(ROOT_DIR.APP_PATH."varsGlobal.php");
include_once(ROOT_DIR.APP_PATH."functions/functions.php");

/*
$newpass 	= 'qwerty777';
$UserName 	= 'Borne';
$UserEmail 	= $UserName.'@xnova.su';

$md5newpass = md5($newpass);

$sex = mt_rand(1, 2);

sql::build()->insert('game_users')->set(Array
(
	'username' 		=> db::escape_string(strip_tags($UserName)),
	'sex' 			=> $sex,
	'id_planet' 	=> 0,
	'user_lastip' 	=> request::getClientIp(true),
	'bonus' 		=> time(),
	'onlinetime' 	=> time()
))
->execute();

$iduser = db::insert_id();

sql::build()->insert('game_users_info')->set(Array
(
	'id' 			=> $iduser,
	'email' 		=> db::escape_string($UserEmail),
	'register_time' => time(),
	'password' 		=> $md5newpass
))
->execute();

system::CreateRegPlanet($iduser);
core::updateConfig('users_amount', (core::getConfigFromDB('users_amount', 0) + 1));
core::clearConfig();

db::query("INSERT INTO game_bots_users (user_id) VALUES (".$iduser.")");

die();
*/

$users = db::query("SELECT * FROM game_bots_users WHERE 1 ORDER BY last_update ASC");

while ($u = db::fetch($users))
{
	$bot = new gameBot($u['user_id']);
	$bot->play();

	echo $bot->getLog();

	unset($bot);
}

echo 'true';
 
?>