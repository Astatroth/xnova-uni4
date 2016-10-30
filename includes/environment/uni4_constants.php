<?php

if (!defined('INSIDE'))
	die("Hacking attempt");

define('SITE_TITLE'               , 'XnovaGame');
define('COOKIE_NAME'              , 'x');
define('SPY_REPORT_ROW'           , 2);
define('FIELDS_BY_MOONBASIS_LEVEL', 4);
define('MAX_PLAYER_PLANETS'       , 9);
define('MAX_BUILDING_QUEUE_SIZE'  , 1);
define('MAX_FLEET_OR_DEFS_PER_ROW', 99999);
define('MAX_OVERFLOW'             , 1);
define('MAX_GALAXY_IN_WORLD'      , 5);
define('MAX_SYSTEM_IN_GALAXY'     , 499);
define('MAX_PLANET_IN_SYSTEM'     , 15);
define('BASE_STORAGE_SIZE'        , 50000);
define('BUILD_METAL'              , 5000);
define('BUILD_CRISTAL'            , 5000);
define('BUILD_DEUTERIUM'          , 5000);
define('ONLINETIME'         	  , 60);
define('UNIVERSE'         	  	  , 4);

define('SHOP_LOGIN'				  , '');
define('SHOP_MERCHANT'			  ,	'');
define('SHOP_SECRET'			  , '');

define('CACHE_DRIVER'			  , 'memcache');
define('CACHE_DIR'				  , 'cache/'.SERVER_CODE.'');
define('MEMCACHE_HOST'			  , 'localhost');
define('MEMCACHE_PORT'			  , 11211);

define('SQL_SERVER'				  , 'localhost');
define('SQL_PORT'				  , '3306');
define('SQL_LOGIN'				  , '');
define('SQL_PASSWORD'			  , '');
define('SQL_DB_NAME'			  , '');
define('SQL_PREFIX'				  , 'game_');
define('DB_DRIVER'				  , 'mysqli');

define('APPID'					  , '');
define('APPPUBLIC'				  , '');
define('APPSECRET'				  , '');

define('VK_APPID'				  , '');
define('VK_APPSECRET'			  , '');

define('SMS_APPID'				  , '');
define('SMS_LOGIN'				  , '');
define('SMS_PASSWORD'			  , '');
define('SMS_FROM'				  , '');

define('ALLOW_LOGIN', 			true);
define('ALLOW_REGISTRATION', 	true);

$config = array
(
	'DEBUG'					=> false,
	'game_name' 			=> 'Звездная Империя',				// Название игры
	'forum_url'				=> 'http://forum.xnova.su/',	// УРЛ форума
	'noobprotection'		=> 1,							// Защита новичков
	'noobprotectiontime'	=> 50,
	'noobprotectionmulti'	=> 5,
	'fleetDebrisRate'		=> 0.3,							// Флот в обломки
	'defsDebrisRate'		=> 0,							// Оборона в обломки
	'initial_fields'		=> 163,							// Поля на главной планете
	'initial_base_fields'	=> 10,							// Поля на военной базе
	'BuildLabWhileRun'		=> 0,							// Разрешить апгрейд лабы при идущем исследовании
	'vocationModeTime'		=> 172800,						// Время ухода в отпуск
	'metal_basic_income'	=> 20,							// Базовое производство на планете
	'crystal_basic_income'	=> 10,
	'deuterium_basic_income'=> 0,
	'energy_basic_income'	=> 0,
	'game_speed'			=> 5000,						// Скорость строительства и исследований /2500
	'fleet_speed'			=> 5000,						// Скорость полётов /2500
	'resource_multiplier'	=> 3,							// Скорость добычи ресурсов
	'openRaportInNewWindow'	=> 1,							// Открывать отчетыв новом окне
	'overviewListView'		=> 1,							// Вид панели обзора, 0 планеты внизу, 1 планеты сбоку
	'gameTemplate'			=> 'main',						// Игровой шаблон
	'showPlanetListSelect'	=> 0,							// Показывать селект выбора планет
	'gameActivityList'		=> 1,							// Показывать игровую активность
	'planetFactor'			=> 1,							// Множитель размера колонизируемых планет
	'hallPoints'			=> 1000000,						// Порог лома для попадания в зал славы
	'maxMoonChance'			=> 20,							// Максимальный ШВЛ
	'ajaxNavigation'		=> 1,							// full off, 1 - auto, 2 - always on
	'socialIframeView'		=> 0,							// Вид для соц сетей
	'refersCreditBonus'		=> 5,							// Ежедневный бонус за рефералов
	'maxRegPlanetsInSystem'	=> 3,
	'buildings_exp_mult'	=> 1500,
	'maxSlotsInSim'			=> 10,
	'defaultController'		=> 'overview',
	'stat.deleteTime'		=> 7 * 86400,
	'stat.inactiveTime'		=> 21 * 86400,
	'level.max_ind'			=> 100,
	'level.max_war'			=> 100,
	'level.credits'			=> 10,
	'log.research'			=> true,
	'log.buildings'			=> true,
	'log.factory'			=> true,
	'profilertoolbar' 		=> array
	(
		'showTotalInfo' => true,
		'showSql' 		=> true,
		'showCache' 	=> true,
		'showVars' 		=> true,
		'showRoutes' 	=> true,
		'showIncFiles' 	=> true,
		'showCustom' 	=> true,
		'format.memory' => 'kb',
		'format.time' 	=> 's',
		'enabled' 		=> true
	)
);
 
?>
