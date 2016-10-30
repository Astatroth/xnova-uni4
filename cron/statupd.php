<?php

use Xcms\core;
use Xnova\statUpdate;

if (!extension_loaded('memcache'))
{
	dl('memcache.so');
}

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__.'../');

define('INSIDE', true);

include($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/core.php');

core::init('UNI4');
core::loadConfig();

ini_set('max_execution_time', '600');
ini_set('log_errors', 'On');
ini_set('display_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'].'/php_errors.log');

include(ROOT_DIR.APP_PATH.'functions/functions.php');
include(ROOT_DIR.APP_PATH.'varsGlobal.php');
include(ROOT_DIR.APP_PATH.'init.php');

$statUpdate = new statUpdate();

$statUpdate->inactiveUsers();
$statUpdate->deleteUsers();
$statUpdate->clearOldStats();
$statUpdate->update();
$statUpdate->addToLog();
$statUpdate->clearGame();
$statUpdate->buildRecordsCache();

core::clearConfig();

echo 'OK';

?>