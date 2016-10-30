<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Xcms\Core;
use Xcms\eventManager;
use Xcms\Strings;

$_SERVER['DOCUMENT_ROOT'] = __DIR__;

define('INSIDE', true);
define('BASE_DIR', '');

session_start();

require($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/core.php');

core::init();
core::loadConfig();
strings::setLang('ru');

ini_set('log_errors', 'On');
ini_set('display_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'].'/php_errors.log');

require(ROOT_DIR.'includes/bootstrap.php');
?>