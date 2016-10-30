<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Xcms\cache;
use Xcms\core;
use Xcms\db;
use Xcms\session;
use Xcms\sql;
use Xcms\strings;
use Xnova\User;

session_start();
define('INSIDE', true);

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__.'../');

include($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/core.php');
core::init();

$user = user::get();

$session = new session();
$session->CheckTheUser();

if (!$session->isAuthorized())
	die('access denied');

strings::setLang('ru');

//cache::delete('game_chat');

if (isset($_REQUEST["msg"]))
{
	$msg_text = trim(htmlspecialchars(addslashes($_REQUEST['msg'])));

	if ($msg_text == '')
		die();

	if ($session->user['silence'] > time())
		die();

	$msg_text = str_replace('\\', '', $msg_text);
	$msg_text = str_replace('\\\'', '\'', $msg_text);
	$msg_text = str_replace('\\\\', '\\', $msg_text);
	$msg_text = str_replace('\\&quot;', '&quot;', $msg_text);

	sql::build()->insert('game_log_chat')->set(Array
	(
		'user' => $session->user['id'],
		'time' => time(),
		'text' => db::escape_string($msg_text)
	))->execute();

	$lastId = db::insert_id();

	$now = time();

	if (preg_match("/приватно \[(.*?)\]/u", $msg_text, $private))
	{
		$msg_text = str_replace('приватно [' . $private['1'] . ']', ' ', $msg_text);
	}
	elseif (preg_match("/для \[(.*?)\]/u", $msg_text, $to_login))
	{
		$msg_text = str_replace('для [' . $to_login['1'] . ']', ' ', $msg_text);
	}

	$msg_text = trim($msg_text);
	$msg_text = strtr($msg_text, strings::getText('stopwords'));

	$username = $session->user['username'];

	$config = json_decode($_SESSION['config'], true);

	if ($session->user['authlevel'] > 0 && (strpos($msg_text, '/kick') !== false || $msg_text == '/speak') && isset($to_login['1']))
	{
		$check = db::query("SELECT id, authlevel FROM game_users WHERE username = '".$to_login['1']."' LIMIT 1", true);

		if (isset($check['id']) && $check['authlevel'] == 0)
		{
			if ($msg_text == '/speak')
			{
				db::query("UPDATE game_users SET silence = 0 WHERE id = ".$check['id']."");

				$msg_text = 'Модератор '.$session->user['username'].' разрешил общение пользователю '.$to_login['1'].'.';
			}
			else
			{
				$time = 15;

				if (strpos($msg_text, '30') !== false)
					$time = 30;
				elseif (strpos($msg_text, '60') !== false)
					$time = 60;
				elseif (strpos($msg_text, '1440') !== false)
					$time = 1440;

				db::query("UPDATE game_users SET silence = ".(time() + $time * 60)." WHERE id = ".$check['id']."");

				$msg_text = 'Модератор '.$session->user['username'].' запретил общение пользователю '.$to_login['1'].' на '.$time.' минут.';
			}

			$username = 'Система';
			$private['1'] = '';
			$to_login['1'] = '';
			$config['color'] = 0;
		}
	}

	if ($msg_text == '/joke')
	{
		$msg_text =  'Нет меня, я устал';

		$username = 'Бомж';
		$private['1'] = '';
		$to_login['1'] = '';
		$config['color'] = 0;
	}

	$chat = json_decode(cache::get("game_chat"), true);

	if (count($chat) > 0)
	{
		foreach ($chat AS $id => $message)
		{
			if ($message[0] == $now)
				$now++;
		}
	}

	if (!isset($to_login['1']))
		$to_login['1'] = '';
	if (!isset($private['1']))
		$private['1'] = '';

	$chat = array_reverse($chat);

	foreach ($chat AS $i => $mess)
	{
		if ($i >= 25 && $mess[0] < (time() - 120))
			unset($chat[$i]);
	}

	$chat = array_reverse($chat);

	//if ($session->user['authlevel'] == 0)
	//	$msg_text = 'цветочки';

	$chat[] = array($now, $username, $to_login['1'], $private['1'], $msg_text, ($config['color'] + 0), $lastId);

	cache::set("game_chat", json_encode($chat), 86400);

	die('1');
}

if (isset($_GET['message_id']))
{
	$room_messages = json_decode(cache::get("game_chat"));

	$mess_id = intval($_GET['message_id']);
	$mess_id_t = $mess_id;

	if (count($room_messages) > 0)
	{
		$now = time();

		$color_massive = _getText('colors');

		foreach ($room_messages as $id => $message)
		{
			$message[4] = preg_replace("[\n\r]", "", $message[4]);
			$message[4] = nl2br($message[4]);

			$original = $message[4];

			$message[4] = "<font color=\"" . $color_massive[$message[5]][0] . "\">" . $message[4] . "</font>";

			if ($message[6] > $mess_id)
			{
				if ($message[2] != "")
				{
					if ($message[1] == $session->user['username'])
						print "ChatMsg(" . $message[0] . ",'" . $message[1] . "','<FONT class=player onclick=\'to(\"" . $message[2] . "\");\'>для [" . $message[2] . "]</FONT> " . $message[4] . "', 0, 1);\n";
					elseif ($message[2] == $session->user['username'])
						print "ChatMsg(" . $message[0] . ",'" . $message[1] . "','<FONT class=player onclick=\'to(\"" . $message[2] . "\");\'>для [" . $message[2] . "]</FONT> " . $message[4] . "', 1, 0);\n";
					else
						print "ChatMsg(" . $message[0] . ",'" . $message[1] . "','<FONT class=player onclick=\'to(\"" . $message[2] . "\");\'>для [" . $message[2] . "]</FONT> " . $message[4] . "', 0, 0);\n";
				}
				elseif (!empty($message[3]) && ($message[1] == $session->user['username'] || $message[3] == $session->user['username'] || $session->user['authlevel'] == 3))
				{

					if ($message[1] == $session->user['username'])
						print "ChatMsg(" . $message[0] . ",'" . $message[1] . "','<FONT class=private onclick=\'pp(\"" . $message[3] . "\");\'>приватно [" . $message[3] . "]</FONT> " . $message[4] . "', 0, 1);\n";
					else
						print "ChatMsg(" . $message[0] . ",'" . $message[1] . "','<FONT class=private onclick=\'pp(\"" . $message[3] . "\");\'>приватно [" . $message[3] . "]</FONT> " . $message[4] . "', 1, 0);\n";
				}
				elseif ($message[3] == "" && $message[2] == "")
				{

					if ($message[1] == $_SESSION['unm'])
						print "ChatMsg(" . $message[0] . ",'" . $message[1] . "','" . $message[4] . "', 0, 1);\n";
					else
						print "ChatMsg(" . $message[0] . ",'" . $message[1] . "','" . $message[4] . "', 0, 0);\n";
				}

				$mess_id_t = $message[6];
			}
		}
	}

	echo "MsgSent('" . $mess_id_t . "');";

	die();
}

?>