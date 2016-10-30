<?php

if (!defined('INSIDE'))
	die("Hacking attempt");

$serverList['UNI4'] = Array
(
	'LOCATION'	=> '',
	'ROOT_DIR'	=> '/var/www/xnova/www/uni4.xnova.su/'
);

define('VERSION'				  , '2.6 REV27');
define('DEFAULT_SKINPATH'		  , 'skins/default/');
define('ADMINEMAIL'               , "info@xnova.su");
define('TIMEZONE'				  , 'Europe/Moscow');
define('UTF8_SUPPORT'             , true);
define('CORE_PATH'				  , 'includes/core/');
define('APP_PATH'				  , 'includes/app/');
define('LIB_PATH'				  , 'includes/lib/');

?>