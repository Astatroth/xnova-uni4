<?php

/**
 * Игровые массивы
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * @global $game_modules array Список модулей игры
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if (!defined('INSIDE'))
	die();

// Маски доступа: 0 - возможно без авторизации, 1 - только с авторизацией, 2 - только без авторизации
$game_modules = array
(
	'content'		=> 0,
	'overview' 		=> 1,
	'imperium' 		=> 1,
	'galaxy' 		=> 1,
	'chat' 			=> 1,
	'alliance' 		=> 0,
	'buildings' 	=> 1,
	'fleet' 		=> 1,
	'stat' 			=> 0,
	'messages' 		=> 1,
	'options' 		=> 1,
	'admin' 		=> 1,
	'hall' 			=> 1,
	'support' 		=> 1,
	'rw' 			=> 1,
	'raketenangriff'=> 1,
	'infos' 		=> 1,
	'search' 		=> 1,
	'techtree' 		=> 1,
	'race' 			=> 1,
	'phalanx' 		=> 1,
	'jumpgate' 		=> 1,
	'resources' 	=> 1,
	'records' 		=> 1,
	'log' 			=> 0,
	'logs' 			=> 1,
	'officier' 		=> 1,
	'tutorial' 		=> 1,
	'infokredits' 	=> 1,
	'pay' 			=> 1,
	'sim' 			=> 1,
	'banned' 		=> 0,
	'refers' 		=> 1,
	'avatar' 		=> 1,
	'players' 		=> 0,
	'contact' 		=> 0,
	'logout' 		=> 1,
	'marchand' 		=> 1,
	'news' 			=> 0,
	'links' 		=> 1,
	'buddy' 		=> 1,
	'notes' 		=> 1,
	'lostpassword' 	=> 2,
	'reg' 			=> 2,
	'login' 		=> 2,
	'mobile' 		=> 0,
	'calculate'		=> 1,
	'start' => 1
);

?>