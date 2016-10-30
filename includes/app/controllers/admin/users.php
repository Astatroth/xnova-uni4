<?php

/**
 * @var $this \Xnova\pageHelper
 * $Revision$
 * $Date$
 */

use Xcms\db;
use Xcms\request;
use Xcms\sql;
use Xcms\strings;

$action = request::R('action', '');
$error = '';

switch ($action)
{
	case 'edit':

		$this->setTemplate('users_edit');

		$info = db::fetch(db::query("SELECT * FROM game_users WHERE id = ".request::R('id', 0, VALUE_INT).""));

		if (isset($info['id']))
		{
			if (request::P('save', '') != '')
			{
				if (!request::P('username', ''))
					$error = 'Не указано имя пользователя';
				else
				{
					sql::build()->update('game_users')->set(Array
					(
						'group_id' 	=> strings::CheckString(request::P('group_id', 0, VALUE_INT)),
						'username' 	=> strings::CheckString(request::P('username', ''))
					))
					->where('id', '=', $info['id'])->execute();

					request::redirectTo('/admin/mode/users/action/edit/id/'.$info['id'].'/');
				}
			}

			$groups = db::extractResult(db::query("SELECT * FROM game_users_groups WHERE 1 ORDER BY id ASC"));

			$this->set('info', $info);
			$this->set('groups', $groups);
		}

		break;

	default:

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
			$TypeSort = "u.id";

		$p = @intval($_GET['p']);
		if ($p < 1)
			$p = 1;

		$this->setTemplate('users_list');

		$list = db::extractResult(db::query("SELECT u.`id`, u.`username`, ui.`email`, u.`user_lastip`, ui.`register_time`, u.`onlinetime`, u.`banaday` FROM game_users u, game_users_info ui WHERE ui.id = u.id ORDER BY " . $TypeSort . " LIMIT " . (($p - 1) * 25) . ", 25"));

		$this->set('list', $list);

		$total = db::first(db::query("SELECT COUNT(*) FROM game_users", true));

		$this->set('pagination', strings::pagination($total, 25, '?set=admin&mode=userlist', $p));
}

$this->globals('error', $error);
$this->setTitle('Список пользователей');
$this->showTopPanel(false);
$this->display();
 
?>