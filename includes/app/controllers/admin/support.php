<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xcms\db;
use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] < 1)
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

$ID = (isset($_REQUEST['id'])) ? intval($_REQUEST['id']) : 0;

if (isset($_REQUEST['action']) && isset($_REQUEST['id']))
{
	switch ($_REQUEST['action'])
	{
		case 'send':

			$text = nl2br($_POST['text']);

			if (!$text)
				$this->message('Не заполнены все поля', 'Ошибка', '?set=admin&mode=support', 3);

			$ticket = db::query("SELECT `player_id`, `text` FROM game_support WHERE `id` = '" . $ID . "';", true);

			if (isset($ticket['player_id']))
			{
				$newtext = $ticket['text'].'<br><br><hr>' . user::get()->data['username'].'  ответил в '.date("d.m.Y H:i:s", time()).':<br>' . $text;

				db::query("UPDATE game_support SET `text` = '".addslashes($newtext)."',`status` = '2' WHERE `id` = '".$ID."'");

				user::get()->sendMessage($ticket['player_id'], false, time(), 4, user::get()->data['username'], 'Поступил ответ на тикет №' . $ID);
			}

			break;

		case 'open':

			$ticket = db::query("SELECT id, text, player_id FROM game_support WHERE `id` = '" . $ID . "';", true);

			if (isset($ticket['id']))
			{
				$newtext = $ticket['text'] . '<br><br><hr>' . user::get()->data['username'] . ' открыл тикет в ' . date("j. M Y H:i:s", time());

				db::query("UPDATE game_support SET `text` = '" . addslashes($newtext) . "', `status` = '2' WHERE `id` = '" . $ID . "'");

				user::get()->sendMessage($ticket['player_id'], false, time(), 4, user::get()->data['username'], 'Был открыт тикет №' . $ID);
			}

			break;

		case 'close':

			$ticket = db::query("SELECT id, text, player_id FROM game_support WHERE `id` = '" . $ID . "';", true);

			if (isset($ticket['id']))
			{
				$newtext = $ticket['text'] . '<br><br><hr>' . user::get()->data['username'] . ' закрыл тикет в ' . date("j. M Y H:i:s", time());

				db::query("UPDATE game_support SET `text` = '" . addslashes($newtext) . "', `status` = '0' WHERE `id` = '" . $ID . "'");

				user::get()->sendMessage($ticket['player_id'], false, time(), 4, user::get()->data['username'], 'Тикет №'.$ID.' закрыт');
			}

			break;
	}
}

$tickets = array('open' => array(), 'closed' => array());

$query = db::query("SELECT s.*, u.username FROM game_support s, game_users u WHERE u.id = s.player_id AND status != 0 ORDER BY s.time LIMIT 100;");

while ($ticket = db::fetch_assoc($query))
{
	switch ($ticket['status'])
	{
		case 0:
			$status = '<font color="red">закрыто</font>';
			break;
		case 1:
			$status = '<font color="green">открыто</font>';
			break;
		case 2:
			$status = '<font color="orange">ответ админа</font>';
			break;
		case 3:
			$status = '<font color="green">ответ игрока</font>';
			break;
	}

	if (isset($_GET['action']) && $_GET['action'] == 'detail' && $ID == $ticket['ID'])
		$TINFO = $ticket;

	if ($ticket['status'] == 0)
	{
		if (isset($_GET['action']) && $_GET['action'] == 'detail')
			continue;

		$tickets['closed'][] = array(
			'id' => $ticket['ID'],
			'username' => $ticket['username'],
			'subject' => $ticket['subject'],
			'status' => $status,
			'date' => date("d.m.Y H:i:s", $ticket['time'])
		);
	}
	else
	{
		$tickets['open'][] = array(
			'id' => $ticket['ID'],
			'username' => $ticket['username'],
			'subject' => $ticket['subject'],
			'status' => $status,
			'date' => date("d.m.Y H:i:s", $ticket['time'])
		);
	}
}

$this->setTemplate('support');

if (isset($_GET['action']) && $_GET['action'] == 'detail' && isset($TINFO))
{
	switch ($TINFO['status'])
	{
		case 0:
			$status = '<font color="red">закрыто</font>';
			break;
		case 1:
			$status = '<font color="green">открыто</font>';
			break;
		case 2:
			$status = '<font color="orange">ответ админа</font>';
			break;
		case 3:
			$status = '<font color="green">ответ игрока</font>';
			break;
	}

	$parse = array(
		't_id' => $TINFO['ID'],
		't_username' => $TINFO['username'],
		't_statustext' => $status,
		't_status' => $TINFO['status'],
		't_text' => strtr($TINFO['text'], Array('\n\r' => '<br>', '\n' => '<br>')),
		't_subject' => $TINFO['subject'],
		't_date' => date("j. M Y H:i:s", $TINFO['time']),
	);

	$this->set('parse', $parse);
}

$this->set('tickets', $tickets);
$this->setTitle('Техподдержка');
$this->display();

?>