<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xcms\strings;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] > 2)
{
	strings::includeLang('admin');

	if (isset($_GET['cmd']) && $_GET['cmd'] == 'sort')
	{
		if ($_GET['type'] == 'id')
			$TypeSort = "u.id";
		elseif ($_GET['type'] == 'username')
			$TypeSort = "u.username";
		elseif ($_GET['type'] == 'email')
			$TypeSort = "ui.email";
		elseif ($_GET['type'] == 'user_lastip')
			$TypeSort = "u.user_lastip";
		elseif ($_GET['type'] == 'register_time')
			$TypeSort = "ui.register_time";
		elseif ($_GET['type'] == 'onlinetime')
			$TypeSort = "u.onlinetime";
		elseif ($_GET['type'] == 'banaday')
			$TypeSort = "u.banaday";
		else
			$TypeSort = "u.id";
	}
	else
	{
		$TypeSort = "u.id";
	}

	$p = @intval($_GET['p']);
	if ($p < 1)
		$p = 1;

	$query = db::query("SELECT u.`id`, u.`username`, ui.`email`, u.`user_lastip`, ui.`register_time`, u.`onlinetime`, u.`banaday` FROM game_users u, game_users_info ui WHERE ui.id = u.id ORDER BY " . $TypeSort . " LIMIT " . (($p - 1) * 25) . ", 25");

	$parse = array();
	$parse['adm_ul_table'] = array();
	$Color = "lime";
	$PrevIP = '';

	while ($u = db::fetch_assoc($query))
	{
		if ($PrevIP != "")
		{
			if ($PrevIP == $u['user_lastip'])
			{
				$Color = "red";
			}
			else
			{
				$Color = "lime";
			}
		}
		
		$Bloc['adm_ul_data_id'] = $u['id'];
		$Bloc['adm_ul_data_name'] = $u['username'];
		$Bloc['adm_ul_data_mail'] = $u['email'];
		$Bloc['adm_ul_data_adip'] = "<font color=\"" . $Color . "\">" . long2ip($u['user_lastip']) . "</font>";
		$Bloc['adm_ul_data_regd'] = date("d.m.Y H:i:s", $u['register_time']);
		$Bloc['adm_ul_data_lconn'] = date("d.m.Y H:i:s", $u['onlinetime']);
		$Bloc['adm_ul_data_banna'] = ($u['banaday'] > 0) ? "<a href=\"#\" title=\"" . date("d.m.Y H:i:s", $u['banaday']) . "\">" . _getText('adm_ul_yes') . "</a>" : _getText('adm_ul_no');
		
		$PrevIP = $u['user_lastip'];
		
		$parse['adm_ul_table'][] = $Bloc;
	}
	
	$total = db::first(db::query("SELECT COUNT(*) FROM game_users", true));

	$parse['adm_ul_count'] = strings::pagination($total, 25, '?set=admin&mode=userlist', $p);

	$this->setTemplate('userlist');
	$this->set('parse', $parse);
	$this->setTitle(_getText('adm_ul_title'));
	$this->display();
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>