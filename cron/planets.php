<?php

use Xcms\core;
use Xcms\db;
use Xcms\strings;
use Xnova\planet;
use Xnova\User;

if (!extension_loaded('memcache'))
{
	dl('memcache.so');
}

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__.'../');

define('INSIDE', true);
define('CRON', true);

include($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/core.php');
core::init('UNI4');
core::loadConfig();
strings::setLang('ru');

ini_set('log_errors', 'On');
ini_set('display_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'].'/php_errors.log');

include(ROOT_DIR.APP_PATH.'functions/functions.php');
include(ROOT_DIR.APP_PATH.'varsGlobal.php');
include(ROOT_DIR.APP_PATH.'init.php');

db::query("LOCK TABLES game_users WRITE, game_planets WRITE");

$users = db::query("SELECT * FROM game_users WHERE urlaubs_modus_time = 0 AND banaday = 0 AND onlinetime < ".(time() - 1600)."");

while ($user = db::fetch($users))
{
	$TargetUser = new user;
	$TargetUser->load_from_array($user);

	$planets = db::query("SELECT * FROM game_planets WHERE destruyed = 0 AND last_update < ".(time() - 1500)." AND id_owner = ".$user['id']."");

	while ($planet = db::fetch($planets))
	{
		$TargetPlanet = new planet();
		$TargetPlanet->load_from_array($planet);
		$TargetPlanet->load_user_info($TargetUser);
		$TargetPlanet->PlanetResourceUpdate(time());
		$TargetPlanet->UpdatePlanetBatimentQueueList();

		unset($TargetPlanet);
	}

	unset($TargetUser);
}

db::query("UNLOCK TABLES");

?>