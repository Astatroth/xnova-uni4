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
	case 'add':

		break;

	case 'edit':

		$this->setTemplate('groups_edit');

		$info = db::fetch(db::query("SELECT * FROM game_users_groups WHERE id = ".request::R('id', 0, VALUE_INT).""));

		if (isset($info['id']))
		{
			if (request::P('save', '') != '')
			{
				if (!request::P('name', ''))
					$error = 'Не указано имя пользователя';
				else
				{
					sql::build()->update('game_users_groups')->set(Array
					(
						'name' 	=> strings::CheckString(request::P('name', ''))
					))
					->where('id', '=', $info['id'])->execute();

					if (is_array(request::P('module', '', VALUE_INT)))
					{
						$m = request::P('module', '', VALUE_INT);

						foreach ($m as $moduleId => $rightId)
						{
							$check = db::query("SELECT id FROM game_cms_modules WHERE active = '1' AND id = ".intval($moduleId)."", true);

							if (isset($check['id']))
							{
								$rightId = min(2, max(0, $rightId));

								$f = db::query("SELECT id FROM game_cms_rights WHERE group_id = '".$info['id']."' AND module_id = ".$check['id']."", true);

								if (!isset($f['id']))
								{
									sql::build()->insert('game_cms_rights')->set(Array
									(
										'group_id' 	=> $info['id'],
										'module_id' => $check['id'],
										'right_id' 	=> $rightId
									))
									->execute();
								}
								else
								{
									sql::build()->insert('game_cms_rights')->set(Array
									(
										'group_id' 	=> $info['id'],
										'module_id' => $check['id'],
										'right_id' 	=> $rightId
									))
									->where('id', '=', $f['id'])->execute();
								}
							}
						}
					}

					request::redirectTo('/admin/mode/groups/action/edit/id/'.$info['id'].'/');
				}
			}

			$modules = db::extractResult(db::query("SELECT * FROM game_cms_modules WHERE active = '1' ORDER BY id ASC"));

			$rights = db::extractResult(db::query("SELECT * FROM game_cms_rights WHERE group_id = '".$info['id']."'"), 'module_id');

			$this->set('rights', $rights);
			$this->set('modules', $modules);
			$this->set('info', $info);
		}
		else
			$error = 'Группа не найдена';

		break;

	default:

		$this->setTemplate('groups_list');

		$list = db::extractResult(db::query("SELECT * FROM game_users_groups WHERE 1 ORDER BY id ASC"));

		$this->set('list', $list);
}

$this->globals('error', $error);
$this->setTitle('Группы пользователей');
$this->showTopPanel(false);
$this->display();

?>