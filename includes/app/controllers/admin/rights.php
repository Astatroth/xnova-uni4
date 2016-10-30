<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

user::get()->data['authlevel'] = 1;

$rights = array
(
	'overview' 		=> 1,
	'support' 		=> 1,
	'server' 		=> 3,
	'settings' 		=> 3,
	'userlist' 		=> 3,
	'paneladmina' 	=> 1,
	'planetlist' 	=> 3,
	'activeplanet' 	=> 2,
	'moonlist' 		=> 2,
	'flyfleettable' => 3,
	'alliancelist' 	=> 2,
	'banned' 		=> 1,
	'unbanned' 		=> 2,
	'md5changepass' => 3,
	'email' 		=> 2,
	'messagelist' 	=> 3,
	'messall' 		=> 2,
	'errors' 		=> 3,
);

$result = array();

foreach ($rights AS $t => $r)
{
	if (user::get()->data['authlevel'] >= $r)
		$result[] = Array('text' => _getText('category', $t), 'type' => $t, 'leaf' => true);
}

echo json_encode($result);

?>