<?php

/**
 * @author AlexPro
 * @copyright 2011 - 2013
 * @var $this \Xcms\templateDisplay
 * @var $params array
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

$Display = $this->_HSTemplate->getDisplay('block');

global $user;

$planetsList = \Xcms\cache::get('app::planetlist_'.\Xnova\user::get()->getId());

if ($planetsList === false)
{
	$planetsList = \Xnova\user::get()->getUserPlanets(\Xnova\app::$user->data['id']);

	if (count($planetsList))
		\Xcms\cache::set('app::planetlist_'.\Xnova\user::get()->getId(), $planetsList, 600);
}

$parse = array();
$parse['list'] = $planetsList;
$parse['current'] = $user->data['current_planet'];

if (!$params['ajax'])
	$Display->addTemplate('planets', 'planets.php');
else
	$Display->addTemplate('planets', 'planets_ajax.php');

$Display->assign('parse', $parse, 'planets');

$Display->display();
 
?>