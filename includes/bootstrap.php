<?php
/**
 * Файл полной инициализации игрового движка
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * @global $user user Объект пользователя
 * @global $session session Объект сессии
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Xcms\Core;
use Xcms\Request;
use Xcms\Session;
use Xnova\User;
use Xnova\App;

if (!defined('INSIDE'))
	die("Hacking attempt");

header("Content-type: text/html; charset=utf-8");
header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
header('Access-Control-Allow-Origin: *');

if (core::getConfig('DEBUG'))
{
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}
else
	error_reporting(E_ALL ^ E_NOTICE);

include_once(ROOT_DIR.APP_PATH.'init.php');

request::parseUrl();

$page = request::G('set');
$mode = request::G('mode');

require(ROOT_DIR.'includes/core/vars.php');

if (!isset($game_modules))
	die('Повреждение модулей');

$user = user::get();

$session = new session();
$session->checkExtAuth();
$session->CheckTheUser();

if ($session->isAuthorized())
	$user->load_from_array($session->user);
else
	$session->CheckReferLink();

if (user::get()->isAuthorized())
{
	if (!$user->isAdmin())
		core::setConfig('DEBUG', false);

	require_once(ROOT_DIR.APP_PATH.'class/app.php');

	app::init();
}
else
	core::setConfig('DEBUG', false);

if (!defined('DPATH'))
	define('DPATH', DEFAULT_SKINPATH);

if (!defined('RPATH'))
	define('RPATH', '/');

$page = trim(str_replace(array('_', '\\', '/', '.', "\0"), '', $page));

if (!isset($game_modules[(string) $page]))
	$page = ($session->isAuthorized()) ? core::getConfig('defaultController', 'main') : 'login';

if ($game_modules[(string) $page] == 1 && !$session->isAuthorized())
	$page = 'login';
elseif ($game_modules[(string) $page] == 2 && $session->isAuthorized())
	$page = core::getConfig('defaultController', 'main');

$pageClass	= '\Xnova\controllers\show'.ucfirst($page).'Page';
$path       = ROOT_DIR.APP_PATH.'controllers/show'.ucfirst($page).'Page.php';

if (!file_exists($path))
	message('Controller "'.(string) $page.'" not found in application', 'Fatal Error');

request::setG('set', $page);

if (core::getConfig('DEBUG'))
	core::addLogEvent('Bootstrap', 'Load controller '.$pageClass.'');

require($path);

if (!$mode)
	$mode = core::getConfig('defaultAction', 'show');

$mode = str_replace(Array("__call", "__construct", "display"), "", $mode);

global $pageObj;

$pageObj = new $pageClass;
$pageObj->name = $page;
$pageObj->mode = $mode;

if (!method_exists($pageObj, $mode) || (method_exists($pageObj, $mode) && !in_array($mode, get_class_methods($pageObj))))
{
	$mode = core::getConfig('defaultAction', 'show');
}

$pageObj->{$mode}();