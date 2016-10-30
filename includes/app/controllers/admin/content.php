<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xcms\request;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= 3)
{
	$result = array();

	if (request::G('edit') > 0)
	{
		$result['info'] = db::query("SELECT * FROM game_content WHERE id = '".request::G('edit', VALUE_INT, 0)."'", true);

		$this->setTemplate('content_edit');
	}
	else
	{
		$result['rows'] = array();

		$query = db::query("SELECT * FROM game_content");

		$result['total'] = db::num_rows($query);

		while ($e = db::fetch($query))
		{
			$result['rows'][] = $e;
		}

		$this->setTemplate('content_row');
	}

	$this->set('parse', $result);
	$this->setTitle("Контент");
	$this->display();
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>