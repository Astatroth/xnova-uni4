<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xcms\request;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= 2)
{
	$query = db::query("SELECT a.`id`, a.`ally_name`, a.`ally_tag`,  a.`ally_owner`, a.`ally_register_time`, a.`ally_description`, a.`ally_text`, a.`ally_members`, u.`username` FROM game_alliance a, game_users u WHERE u.id = a.ally_owner");

	$parse = array();
	$parse['alliance'] = array();
	
	$parse['desc'] = '';
	$parse['edit'] = '';
	$parse['name'] = '';
	$parse['member'] = '';
	$parse['member_row'] = '';
	$parse['mail'] = '';
	$parse['leader'] = '';

	while ($u = db::fetch_assoc($query))
	{
		$parse['alliance'][] = $u;
	}

	if (isset($_GET['desc']))
	{
		$ally_id = intval($_GET['desc']);
		$info = db::query("SELECT `ally_description` FROM game_alliance WHERE id='" . $ally_id . "'");
		$ally_text = db::fetch_assoc($info);

		$parse['desc'] = "<tr>"
				. "<th colspan=9>Описание альянса</th></tr>"
				. "<tr>"
				. "<td class=b colspan=9>" . $ally_text['ally_description'] . "</td>"
				. "</tr>";
	}

	if (isset($_GET['edit']))
	{
		$ally_id = intval($_GET['edit']);
		$info = db::query("SELECT `ally_description` FROM game_alliance WHERE id='" . $ally_id . "'");
		$ally_text = db::fetch_assoc($info);

		$parse['desc'] = "<tr>"
				. "<th colspan=9>Реактирование описание альянса</th></tr>"
				. "<tr>"
				. "<form action=?set=admin&mode=alliancelist&edit=" . $ally_id . " method=POST>"
				. "<td class=b colspan=9><center><b><textarea name=desc cols=50 rows=10 >" . $ally_text['ally_description'] . "</textarea></center></b></td>"
				. "</tr>"
				. "<tr>"
				. "<td class=b colspan=9><center><b><input type=submit value=Speichern></center></b></td>"
				. "</form></tr>";

		if (isset($_POST['desc']))
		{
			$query = db::query("UPDATE game_alliance SET `ally_description` = '" . addslashes($_POST['desc']) . "' WHERE `id` = '" . intval($_GET['edit']) . "'");
			
			request::redirectTo('/admin/mode/alliancelist/');
		}
	}


	if (isset($_GET['allyname']))
	{
		$ally_id = intval($_GET['allyname']);
		
		$u = db::query("SELECT `ally_image`, `ally_web`, `ally_name`, `ally_tag` FROM game_alliance WHERE `id` = '" . $ally_id . "'", true);

		$parse['name'] = "<tr>"
				. "<td colspan=9 class=c>Название / обозначение / лого / сайт</td></tr>"
				. "<form action=?set=admin&mode=alliancelist&allyname=" . $ally_id . " method=POST>"
				. "<tr>"
				. "<th colspan=4><center><b>Название альянса</center></b></th>   <th colspan=5><center><b><input type=text name=name value='" . addslashes($u['ally_name']) . "'></center></b></th>"
				. "</tr>"
				. "<tr>"
				. "<th colspan=4><center><b>Обозначение</center></b></th>   <th colspan=5><center><b><input type=text name=tag value=" . $u['ally_tag'] . "></center></b></th>"
				. "</tr>"
				. "<tr>"
				. "<th colspan=3><center><b>Логотип альянса</center></b></th>   <th colspan=3><center><b><input type=text size=38 name=image value=" . $u['ally_image'] . "></center></b></th>  <th colspan=3><center><b><a href=" . $u['ally_image'] . ">Смотреть</a></center></b></th>"
				. "</tr>"
				. "<tr>"
				. "<th colspan=3><center><b>Сайт альянса</center></b></th>   <th colspan=3><center><b><input type=text size=38 name=web value=" . $u['ally_web'] . "></center></b></th>  <th colspan=3><center><b><a href=" . $u['ally_web'] . ">Смотреть</a></center></b></th>"
				. "</tr>"
				. "<tr>"
				. "<td class=b colspan=9><center><b><input type=submit value=Сохранить></center></b></td>"
				. "</form></tr>";

		if (isset($_POST['name']))
		{
			$query = db::query("UPDATE game_alliance SET `ally_name` = '" . addslashes($_POST['name']) . "', `ally_tag` = '" . addslashes($_POST['tag']) . "', `ally_image` = '" . addslashes($_POST['image']) . "', `ally_web` = '" . addslashes($_POST['web']) . "' WHERE `id` = '" . intval($_GET['allyname']) . "'");
			request::redirectTo('/admin/mode/alliancelist/');
		}

	}

	if (isset($_GET['mitglieder']))
	{
		$ally_id = intval($_GET['mitglieder']);

		$users = db::query("SELECT `id`, `username` FROM game_users WHERE ally_id='" . $ally_id . "'");

		$parse['member_row'] = '';

		$i = 0;
		while ($u = db::fetch_assoc($users))
		{
			$parse['member_row'] .= "<tr>"
					. "<td class=b colspan=2><center><b>" . $u['id'] . "</center></b></td>"
					. "<td class=b  colspan=5><center><b><a href=?set=messages&mode=write&id=" . $u['id'] . ">" . $u['username'] . "</a></center></b></td>"
					. "<td class=b  colspan=2><center><b><a href=?set=admin&mode=alliancelist&ent=" . $u['id'] . "> X </a></center></b></td>"
					. "</tr>";
			$i++;
		}
	}

	if (isset($_GET['ent']))
	{
		$user_id = intval($_GET['ent']);

		$parse['name'] .= "<tr>"
				. "<th colspan=9>Удаление участника из альянса</th></tr>"
				. "<form action=?set=admin&mode=alliancelist&ent=" . $user_id . " method=POST>"
				. "<tr>"
				. "<th colspan=9><center><b>После нажатия кнопки Удалить, выбранный вами участник выйдет из альянса. <br>Ты действительно хочешь это сделать?</center></b></th>"
				. "</tr>"
				. "<td class=b colspan=9><center><b><input type=submit value=Удалить name=ent></center></b></td>"
				. "</form></tr>";

		if (isset($_POST['ent']))
		{
			$user_id = $_GET['ent'];
			db::query("UPDATE game_users SET `ally_id`=0, `ally_name` = '' WHERE `id`='" . $user_id . "'");
			request::redirectTo('/admin/mode/alliancelist/');
		}

	}

	if (isset($_GET['mail']))
	{
		$ally_id = $_GET['mail'];

		$parse['mail'] = "<tr>"
				. "<th colspan=9>Собщение участникам альянса</th></tr>"
				. "<tr>"
				. "<form action=?set=admin&mode=alliancelist&mail=" . $ally_id . " method=POST>"
				. "<tr>"
				. "<td class=b colspan=9><center><b><textarea name=text cols=50 rows=10 ></textarea></center></b></td>"
				. "</tr>"
				. "<tr>"
				. "<td class=b colspan=9><center><b><input type=submit value=Отправить></center></b></td>"
				. "</form></tr>";

		if (isset($_POST['text']))
		{
			$ally_id = intval($_GET['mail']);
			$sq = db::query("SELECT id FROM game_users WHERE ally_id='" . $ally_id . "'");
			while ($u = db::fetch($sq))
			{
				db::query("INSERT INTO game_messages SET
										`message_owner`='{$u['id']}',
										`message_sender`='Администрация' ,
										`message_time`='" . time() . "',
										`message_type`='2',
										`message_from`='Сообщение альянса (Admin)',
										`message_text`='" . addslashes($_POST['text']) . "'
										");
			}
			request::redirectTo('/admin/mode/alliancelist/');
		}
	}

	if (isset($_GET['leader']))
	{
		$ally_id = intval($_GET['leader']);

		$query = db::query("SELECT `ally_owner` FROM game_alliance");
		$u = db::fetch($query);
		$users = db::query("SELECT `username` FROM game_users WHERE id='" . $u['ally_owner'] . "'");
		$a = db::fetch($users);
		$leader = $a['username'];

		$parse['leader'] = "<tr>"
				. "<td colspan=9 class=c>Смена лидера альянса</td></tr>"
				. "<form action=?set=admin&mode=alliancelist&leader=" . $ally_id . " method=POST>"
				. "<tr>"
				. "<th colspan=4><center><b>Сейчас лидер:</center></b></th>   <th colspan=5><center><b>$leader</center></b></th>"
				. "</tr>"
				. "<tr>"
				. "<th colspan=4><center><b><u>ID</u> нового лидера</center></b></th>   <th colspan=5><center><b><input type=text size=8 name=leader></center></b></th>"
				. "</tr>"
				. "<tr>"
				. "<td class=b colspan=9><center><b><input type=submit value=Сохранить></center></b></td>"
				. "</form></tr>";

		if (isset($_POST['leader']))
		{
			$sq = db::query("SELECT ally_id FROM game_users WHERE id='" . intval($_POST['leader']) . "'");
			$a = db::fetch($sq);

			if ($a['ally_id'] == $_GET['leader'])
			{
				$query = db::query("UPDATE game_alliance SET `ally_owner` = '" . intval($_POST['leader']) . "' WHERE `id` = '" . intval($_GET['leader']) . "'");
				request::redirectTo('/admin/mode/alliancelist/');
			}
			else
			{
				request::redirectTo('/admin/mode/alliancelist/');
			}
		}
	}

	$this->setTemplate('alliance');
	$this->set('parse', $parse);
	$this->setTitle('Список альянсов');
	$this->display();
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>
