<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xcms\request;
use Xcms\strings;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= 1)
{
	global $reslist, $resource;

	strings::includeLang('admin/adminpanel');

	if (request::R('result', '') != '')
	{
		$result = request::R('result');

		switch ($result)
		{
			case 'usr_data':

				if (user::get()->data['authlevel'] >= 1)
				{
					$username = request::R('username');
					
					$SelUser = db::query("SELECT u.*, ui.* FROM game_users u, game_users_info ui WHERE ui.id = u.id AND ".(is_numeric($username) ? "u.`id` = '" . $username . "'" : "u.`username` = '" . $username . "'")." LIMIT 1;", true);

					if (!isset($SelUser['id']))
						$this->message('Такого игрока не существует', 'Ошибка', '/?set=admin&mode=paneladmina', 2);

					$parse = array();
					$parse['answer1'] = $SelUser['id'];
					$parse['answer2'] = $SelUser['username'];
					$parse['answer3'] = long2ip($SelUser['user_lastip']);
					$parse['answer4'] = $SelUser['email'];
					$parse['answer5'] = _getText('user_level', $SelUser['authlevel']);
					$parse['answer6'] = _getText('adm_usr_genre', $SelUser['sex']);
					$parse['answer7'] = date('d.m.Y H:i:s', $SelUser['urlaubs_modus_time']);
					$parse['answer9'] = date('d.m.Y H:i:s', $SelUser['register_time']);
					$parse['answer8'] = "[" . $SelUser['galaxy'] . ":" . $SelUser['system'] . ":" . $SelUser['planet'] . "] ";

					$parse['planet_list'] = array();
					$parse['planet_fields'] = $resource;

					if (user::get()->data['authlevel'] > 1)
						$parse['planet_list'] = db::extractResult(db::query("SELECT * FROM game_planets WHERE `id_owner` = '" . $SelUser['id'] . "' ORDER BY id ASC"));

					$parse['history_actions'] = array
					(
						1 => 'Постройка здания',
						2 => 'Снос здания',
						3 => 'Отмена постройки',
						4 => 'Отмена сноса',
						5 => 'Исследование',
						6 => 'Отмена исследования',
						7 => 'Постройка обороны/флота',
					);

					$parse['transfer_list'] = array();

					$transfers = db::extractResult(db::query("SELECT t.*, u.username AS target FROM game_log_transfers t LEFT JOIN game_users u ON u.id = t.target_id WHERE t.`user_id` = '" . $SelUser['id'] . "' ORDER BY id DESC"));

					foreach ($transfers AS $transfer)
					{
						preg_match("/s\:\[(.*?)\:(.*?)\:(.*?)\((.*?)\)\];e\:\[(.*?)\:(.*?)\:(.*?)\((.*?)\)\];f\:\[(.*?)\];m\:(.*?);c\:(.*?);d\:(.*?);/", $transfer['data'], $t);

						$parse['transfer_list'][] = array
						(
							'time' 	=> $transfer['time'],
							'start' => '<a href="/?set=galaxy&r=3&galaxy='.$t[1].'&system='.$t[2].'&planet='.$t[3].'" target="_blank">'.$t[1].':'.$t[2].':'.$t[3].' ('._getText('type_planet', $t[4]).')</a>',
							'end' 	=> '<a href="/?set=galaxy&r=3&galaxy='.$t[5].'&system='.$t[6].'&planet='.$t[7].'" target="_blank">'.$t[5].':'.$t[6].':'.$t[7].' ('._getText('type_planet', $t[8]).')</a>',
							'metal'	=> $t[10],
							'crystal'	=> $t[11],
							'deuterium'	=> $t[12],
							'target'	=> $transfer['target'],
						);
					}

					$parse['transfer_list_income'] = array();

					$transfers = db::extractResult(db::query("SELECT t.*, u.username AS target FROM game_log_transfers t LEFT JOIN game_users u ON u.id = t.user_id WHERE t.`target_id` = '" . $SelUser['id'] . "' ORDER BY id DESC"));

					foreach ($transfers AS $transfer)
					{
						preg_match("/s\:\[(.*?)\:(.*?)\:(.*?)\((.*?)\)\];e\:\[(.*?)\:(.*?)\:(.*?)\((.*?)\)\];f\:\[(.*?)\];m\:(.*?);c\:(.*?);d\:(.*?);/", $transfer['data'], $t);

						$parse['transfer_list_income'][] = array
						(
							'time' 	=> $transfer['time'],
							'start' => '<a href="/?set=galaxy&r=3&galaxy='.$t[1].'&system='.$t[2].'&planet='.$t[3].'" target="_blank">'.$t[1].':'.$t[2].':'.$t[3].' ('._getText('type_planet', $t[4]).')</a>',
							'end' 	=> '<a href="/?set=galaxy&r=3&galaxy='.$t[5].'&system='.$t[6].'&planet='.$t[7].'" target="_blank">'.$t[5].':'.$t[6].':'.$t[7].' ('._getText('type_planet', $t[8]).')</a>',
							'metal'	=> $t[10],
							'crystal'	=> $t[11],
							'deuterium'	=> $t[12],
							'target'	=> $transfer['target'],
						);
					}

					$parse['history_list'] = db::extractResult(db::query("SELECT * FROM game_log_history WHERE user_id = ".$SelUser['id']." AND time > ".(time() - 86400 * 7)." ORDER BY time"));

					if (user::get()->data['authlevel'] > 1)
					{
						$parse['adm_sub_form3'] = "<table class='table'><tr><th colspan=\"4\">" . _getText('adm_technos') . "</th></tr>";

						foreach ($reslist['tech'] AS $Item)
						{
							if (isset($resource[$Item]))
								$parse['adm_sub_form3'] .= "<tr><td>" . _getText('tech', $Item) . "</td><td>" . $SelUser[$resource[$Item]] . "</td></tr>";
						}

						$parse['adm_sub_form3'] .= "</table>";
					}

					$logs = db::query("SELECT ip, time FROM game_log_ip WHERE id = " . $SelUser['id'] . " ORDER BY time DESC");

					$parse['adm_sub_form4'] = "<table class='table'><tr><th colspan=\"2\">Смены IP</th></tr>";

					while ($log = db::fetch($logs))
					{
						$parse['adm_sub_form4'] .= "<tr><td>".long2ip($log['ip'])."</td><td>".datezone("d.m.Y H:i", $log['time'])."</td></tr>";
					}

					$parse['adm_sub_form4'] .= "</table>";

					$logs_lang = array('', 'WMR', 'Ресурсы', 'Реферал', 'Уровень', 'Офицер', 'Админка', 'Смена фракции');

					if (user::get()->data['authlevel'] > 1)
					{
						$logs = db::query("SELECT time, credits, type FROM game_log_credits WHERE uid = " . $SelUser['id'] . " ORDER BY time DESC");

						$parse['adm_sub_form4'] .= "<table class='table'><tr><th colspan=\"4\">Кредитная история</th></tr>";

						while ($log = db::fetch_assoc($logs))
						{
							$parse['adm_sub_form4'] .= "<tr><td width=40%>" . datezone("d.m.Y H:i", $log['time']) . "</td>";
							$parse['adm_sub_form4'] .= "<td>" . $log['credits'] . "</td>";
							$parse['adm_sub_form4'] .= "<td width=40%>" . $logs_lang[$log['type']] . "</td></tr>";
						}

						$parse['adm_sub_form4'] .= "</table>";
					}

					$logs = db::query("SELECT time, planet_start, planet_end, fleet, battle_log FROM game_log_attack WHERE uid = " . $SelUser['id'] . " ORDER BY time DESC");

					$parse['adm_sub_form4'] .= "<table class='table'><tr><th colspan=\"4\">Логи атак</th></tr>";

					while ($log = db::fetch_assoc($logs))
					{
						$parse['adm_sub_form4'] .= "<tr><td width=40%>" . datezone("d.m.Y H:i", $log['time']) . "</td>";
						$parse['adm_sub_form4'] .= "<td>S:" . $log['planet_start'] . "</td>";
						$parse['adm_sub_form4'] .= "<td width=30%>E:" . $log['planet_end'] . "</td></tr>";

						$parse['adm_sub_form4'] .= "<tr><td colspan=3><a href=\"/?set=rw&r=" . $log['battle_log'] . "&amp;k=" . md5('xnovasuka' . $log['battle_log']) . "\" target=\"_blank\">" . $log['fleet'] . "</a></td></tr>";
					}

					$parse['adm_sub_form4'] .= "</table>";

					$logs = db::query("SELECT ip FROM game_log_ip WHERE id = " . $SelUser['id'] . " GROUP BY ip");

					$parse['adm_sub_form5'] = "<table class='table'><tr><th colspan=\"3\">Пересечения по IP</th></tr>";

					while ($log = db::fetch_assoc($logs))
					{
						$ips = db::query("SELECT u.id, u.username, l.time FROM game_log_ip l LEFT JOIN game_users u ON u.id = l.id WHERE l.ip = " . $log['ip'] . " AND l.id != " . $SelUser['id'] . " GROUP BY l.id;");

						while ($ip = db::fetch_assoc($ips))
						{
							$parse['adm_sub_form5'] .= "<tr><td width=40%>" . datezone("d.m.Y H:i", $ip['time']) . "</td>";
							$parse['adm_sub_form5'] .= "<td>" . long2ip($log['ip']) . "</td>";
							$parse['adm_sub_form5'] .= "<td width=30%><a href='/?set=players&id=" . $ip['id'] . "' target='_blank'>" . $ip['username'] . "</a></td></tr>";
						}
					}

					$parse['adm_sub_form5'] .= "</table>";

					$logs = db::query("SELECT u_id, a_id, text, time FROM game_private WHERE u_id = " . $SelUser['id'] . " ORDER BY time DESC");

					$parse['adm_sub_form5'] .= "<table class='table'><tr><th colspan=\"3\">Записи в личном деле</th></tr>";

					while ($log = db::fetch_assoc($logs))
					{
						$parse['adm_sub_form5'] .= "<tr><td width=25%>" . datezone("d.m.Y H:i", $log['time']) . "</td>";
						$parse['adm_sub_form5'] .= "<td width=20%><a href='/?set=players&id=" . $log['a_id'] . "' target='_blank'>" . $log['a_id'] . "</a></td>";
						$parse['adm_sub_form5'] .= "<td>" . $log['text'] . "</td></tr>";
					}

					$parse['adm_sub_form5'] .= "</table>";

					$this->setTemplate('adminpanel_ans1');
					$this->set('parse', $parse);

				}

				break;

			case 'usr_level':

				if (user::get()->data['authlevel'] >= 3)
				{

					$Player = addslashes($_POST['player']);
					$NewLvl = intval($_POST['authlvl']);

					$QryUpdate = db::query("UPDATE game_users SET `authlevel` = '" . $NewLvl . "' WHERE `username` = '" . $Player . "';");
					$Message = _getText('adm_mess_lvl1') . " " . $Player . " " . _getText('adm_mess_lvl2');
					$Message .= "<font color=\"red\">" . _getText('adm_usr_level', $NewLvl) . "</font>!";

					$this->message($Message, _getText('adm_mod_level'));

				}
				break;

			case 'ip_search':
				$Pattern = addslashes($_POST['ip']);
				$SelUser = db::query("SELECT * FROM game_users WHERE `user_lastip` = INET_ATON('" . $Pattern . "');");
				$parse = array();
				$parse['adm_this_ip'] = $Pattern;
				$parse['adm_plyer_lst'] = '';
				while ($Usr = db::fetch_assoc($SelUser))
				{
					$UsrMain = db::query("SELECT `name` FROM game_planets WHERE `id` = '" . $Usr['id_planet'] . "';", true);
					$parse['adm_plyer_lst'] .= "<tr><th>" . $Usr['username'] . "</th><th>[" . $Usr['galaxy'] . ":" . $Usr['system'] . ":" . $Usr['planet'] . "] " . $UsrMain['name'] . "</th></tr>";
				}
				$this->setTemplate('adminpanel_ans2');
				$this->set('parse', $parse);
				break;
			default:
				break;
		}
	}
	elseif (request::R('action', '') != '')
	{
		switch (request::R('action', ''))
		{
			case 'usr_data':
			
				if (user::get()->data['authlevel'] >= 1)
					$this->setTemplate('adminpanel_f4');
				else
					$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));
				
				break;

			case 'usr_level':
			
				if (user::get()->data['authlevel'] >= 3)
					$this->setTemplate('adminpanel_f3');
				else
					$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));
				
				break;

			case 'ip_search':
			
				$this->setTemplate('adminpanel_f2');

				break;
		}
	}
	else
		$this->setTemplate('adminpanel');

	$this->setTitle(_getText('panel_mainttl'));
	$this->display();
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>