<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= 3)
{
	if (isset($_GET['u']) && isset($_GET['email']))
	{
		$email = db::query("SELECT user_id FROM game_log_email WHERE user_id = " . intval($_GET['u']) . " AND email = '" . addslashes($_GET['email']) . "' AND ok = 0;", true);

		if (isset($email['user_id']))
		{
			db::query("UPDATE game_users_info SET email = '" . addslashes($_GET['email']) . "' WHERE id = " . intval($_GET['u']) . ";");
			db::query("UPDATE game_log_email SET ok = 1 WHERE user_id = " . intval($_GET['u']) . " AND email = '" . addslashes($_GET['email']) . "' AND ok = 0;");
		}
	}

	$planetes = '';
	$query = db::query("SELECT e.*, u.username FROM game_log_email e LEFT JOIN game_users u ON u.id = e.user_id WHERE ok = 0");
	$i = 0;
	while ($u = db::fetch_assoc($query))
	{
		$planetes .= "<tr>"
				. "<td>" . $u['username'] . "</td>"
				. "<td>" . datezone("d.m H:i", $u['time']) . "</td>"
				. "<td>" . $u['email'] . "</td>"
				. "<td><a href=\"?set=admin&mode=email&u=" . $u['user_id'] . "&email=" . $u['email'] . "\">сменить</a></td>"
				. "</tr>";
		$i++;
	}

	$this->setTemplate('email');
	$this->set('planetes', $planetes, 'emaillist');
	$this->setTitle('Список email');
	$this->display();
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>