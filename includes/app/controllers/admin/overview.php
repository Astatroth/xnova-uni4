<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xcms\strings;
use Xnova\User;

if (isset($_GET['cmd']) && $_GET['cmd'] == 'sort')
	$TypeSort = $_GET['type'];
else
	$TypeSort = "user_lastip";

$parse = array();
$parse['adm_ov_data_yourv'] = VERSION;
$parse['adm_ov_data_table'] = array();

$Count = 0;
$Color = "lime";
$PrevIP = '';

if (user::get()->data['authlevel'] >= 2)
{
	$Last15Mins = db::query("SELECT `id`, `username`, `user_lastip`, `ally_name`, `onlinetime` FROM game_users WHERE `onlinetime` >= '" . (time() - 15 * 60) . "' ORDER BY `" . $TypeSort . "` ASC;");

	while ($TheUser = db::fetch($Last15Mins))
	{
		if ($PrevIP != "")
		{
			if ($PrevIP == $TheUser['user_lastip'])
				$Color = "red";
			else
				$Color = "lime";
		}

		$PrevIP = $TheUser['user_lastip'];

		$Bloc['adm_ov_altpm'] = _getText('adm_ov_altpm');
		$Bloc['adm_ov_wrtpm'] = _getText('adm_ov_wrtpm');
		$Bloc['adm_ov_data_id'] = $TheUser['id'];
		$Bloc['adm_ov_data_name'] = $TheUser['username'];
		$Bloc['adm_ov_data_clip'] = $Color;
		$Bloc['adm_ov_data_adip'] = long2ip($TheUser['user_lastip']);
		$Bloc['adm_ov_data_ally'] = $TheUser['ally_name'];
		$Bloc['adm_ov_data_activ'] = strings::pretty_time(time() - $TheUser['onlinetime']);

		$parse['adm_ov_data_table'][] = $Bloc;
		$Count++;
	}
}

$parse['adm_ov_data_count'] = $Count;

$this->setTemplate('overview');
$this->set('parse', $parse);
$this->setTitle('Активность на сервере');
$this->display();

?>