<?php

/**
 * @var $this \Xnova\pageHelper
 * @var $error string
 * $Revision$
 * $Date$
 */

use Xcms\cms;
use Xcms\request;

$action = request::R('action', '');

switch ($action)
{
	case 'node':

		header('Content-type: application/json; charset=utf-8');

		$result = array();

		$parent = request::G('parent', 0, VALUE_INT);

		$nodes = cms::getMenu($parent, 2);

		foreach ($nodes AS $node)
		{
			$result[] = array
			(
				'id' 		=> $node['id'],
				'text' 		=> $node['name'],
				'type'		=> (count($node['children']) > 0) ? 'folder' : 'file',
				'children' 	=> (count($node['children']) > 0),
				'state'		=> array('opened' => false)
			);
		}

		echo json_encode($result);
		die();

		break;

	default:

		$this->setTemplate('tree_list');

}

$this->globals('error', $error);
$this->setTitle('Структура');
$this->showTopPanel(false);
$this->display();

?>