<?php

use Xcms\core;
use Xcms\db;

if (!extension_loaded('memcache'))
{
	dl('memcache.so');
}

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__.'../');

define('INSIDE', true);

include($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/core.php');
core::init('UNI4');

$online = db::first(db::query("SELECT COUNT(*) as `online` FROM game_users WHERE `onlinetime` > '" . (time() - ONLINETIME * 60) . "';", true));

core::updateConfig('online', $online);
core::clearConfig();

echo 'true';

?>