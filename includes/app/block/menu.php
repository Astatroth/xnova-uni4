<?php

/**
 * @author AlexPro
 * @copyright 2011 - 2013.ru
 * @var $Display \Xcms\templateDisplay
 * @var $this \Xcms\templateDisplay
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

$Display = $this->_HSTemplate->getDisplay('block');
$Display->addTemplate('menu', 'menu.php');

global $user;

$admin = (\Xcms\request::G('set', '') == 'admin' && isset($user->data['id']) && $user->data['authlevel'] > 0);

$Display->assign('adminlevel', $user->data['authlevel'], 'menu');
$Display->assign('uid', $user->data['id'], 'menu');
$Display->assign('mess', $user->data['new_message'], 'menu');
$Display->assign('mess_ally', ($user->data['ally_id'] != 0) ? $user->data['mnl_alliance'] : '', 'menu');
$Display->assign('tutorial', $user->data['tutorial'], 'menu');
$Display->assign('admin', $admin, 'menu');
$Display->assign('set', \Xcms\request::G('set', \Xcms\core::getConfig('defaultController', 'main')).(\Xcms\request::G('set') == 'buildings' ? \Xcms\request::G('mode', '') : ''), 'menu');

$Display->display();

?>