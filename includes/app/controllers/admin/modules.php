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

		$this->setTemplate('modules_add');

		if (request::P('save', '') != '')
		{
			if (!request::P('alias', ''))
				$error = 'Не указан алиас модуля';
			elseif (!request::P('name', ''))
				$error = 'Не указано название модуля';
			else
			{
				$active = request::P('active', '') != '' ? 1 : 0;

				sql::build()->insert('game_cms_modules')->set(Array
				(
					'active' 	=> $active,
					'alias' 	=> strings::CheckString(request::P('alias', '')),
					'name' 		=> strings::CheckString(request::P('name', ''))
				))
				->execute();

				request::redirectTo('/admin/mode/modules/action/edit/id/'.db::insert_id().'/');
			}
		}

		break;

	case 'edit':

		$this->setTemplate('modules_edit');

		$info = db::fetch(db::query("SELECT * FROM game_cms_modules WHERE id = ".request::R('id', 0, VALUE_INT).""));

		if (isset($info['id']))
		{
			if (request::P('save', '') != '')
			{
				if (!request::P('alias', ''))
					$error = 'Не указан алиас модуля';
				elseif (!request::P('name', ''))
					$error = 'Не указано название модуля';
				else
				{
					$active = request::P('active', '') != '' ? 1 : 0;

					sql::build()->update('game_cms_modules')->set(Array
					(
						'active' 	=> $active,
						'alias' 	=> strings::CheckString(request::P('alias', '')),
						'name' 		=> strings::CheckString(request::P('name', ''))
					))
					->where('id', '=', $info['id'])->execute();

					request::redirectTo('/admin/mode/modules/action/edit/id/'.$info['id'].'/');
				}
			}

			$this->set('info', $info);
		}

		break;

	default:

		$this->setTemplate('modules_list');

		$list = db::extractResult(db::query("SELECT * FROM game_cms_modules WHERE 1"));

		$this->set('list', $list);
}

$this->globals('error', $error);
$this->setTitle('Настройка модулей');
$this->showTopPanel(false);
$this->display();
 
?>