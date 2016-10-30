<?php

namespace Xcms;
use Xcms\ExtAuth\ok AS okAuth;
use Xcms\ExtAuth\vk AS vkAuth;
use Xcms\ExtAuth\ulogin AS uloginAuth;

/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class session
{
	// Флаг прохождения авторизации
	private $IsUserChecked = false;
	// Массив данных игрока
	public $user;

	public function checkExtAuth ()
	{
		// Авторизация через ulogin
		if (isset($_REQUEST['token']) && $_REQUEST['token'] != '' && request::G('set', '') != 'options')
		{
			new uloginAuth($_REQUEST['token']);
		}

		// Авторизация через iframe приложение однокласников
		if (request::G('set', '') == 'login' && isset($_POST['logged_user_id']) && isset($_POST['session_key']))
		{
			new okAuth();
		}

		// Авторизация через iframe приложение вк
		if (request::G('set', '') == 'login' && isset($_POST['viewer_id']) && isset($_POST['access_token']))
		{
			new vkAuth();
		}

		if (isset($_REQUEST['authId']) && isset($_REQUEST['authSecret']))
		{
			$_COOKIE[COOKIE_NAME.'_id'] 	= $_REQUEST['authId'];
			$_COOKIE[COOKIE_NAME.'_secret'] = $_REQUEST['authSecret'];
		}
	}

	public function isAuthorized()
	{
		return $this->IsUserChecked;
	}

	function CheckTheUser ()
	{
		$Result = $this->CheckCookies();

		if (isset($Result['id']))
		{
			if (!isset($_GET['set']) || $_GET['set'] != 'banned')
			{
				if ($Result['banaday'] > time())
					die('Ваш аккаунт заблокирован. Срок окончания блокировки: '.datezone("d.m.Y H:i:s", $Result['banaday']).'<br>Для получения дополнительной информации зайдите <a href="?set=banned">сюда</a>');
				elseif ($Result['banaday'] > 0 && $Result['banaday'] < time())
				{
					db::query("DELETE FROM game_banned WHERE `who` = '".$Result['id']."'");
					db::query("UPDATE game_users SET`banaday` = '0' WHERE `id` = '".$Result['id']."'");
					$Result['banaday'] = 0;
				}
			}
			$this->user = $Result;
		}
	}

	public function getCookiePassword ($uid, $password, $security = 0)
	{
		return ($security) ? md5("".$password."---".request::getClientIp()."---IPSECURITYFLAG_Y---".$uid."") : md5("".$password."---IPSECURITYFLAG_N---".$uid."");
	}

	private function CheckCookies ()
	{
		global $user;

		$UserRow = array();

		if (!isset($_SESSION['uid']) && isset($_COOKIE[COOKIE_NAME.'_id']) && isset($_COOKIE[COOKIE_NAME.'_secret']))
		{
			$UserResult = db::query("SELECT u.*, ui.password FROM game_users u, game_users_info ui WHERE ui.id = u.id AND u.`id` = '".intval($_COOKIE[COOKIE_NAME.'_id'])."';");

			if (db::num_rows($UserResult) == 0)
				$this->ClearSession();

			$UserRow = db::fetch_assoc($UserResult);

			$options = $user->unpackOptions($UserRow['options_toggle']);

			if ($this->getCookiePassword($UserRow['id'], $UserRow['password'], $options['security']) != $_COOKIE[COOKIE_NAME.'_secret'])
				$this->ClearSession();

			$_SESSION['uid'] = $UserRow['id'];
			$_SESSION['unm'] = $UserRow['username'];

			$this->IsUserChecked = true;
		}
		elseif (isset($_SESSION['uid']))
		{
			if (!isset($_COOKIE[COOKIE_NAME.'_id']) && !isset($_COOKIE[COOKIE_NAME.'_secret']))
				$this->ClearSession();

			$UserRow = $user->getById($_SESSION['uid']);

			if (!isset($UserRow['id']))
				$this->ClearSession();
			else
				$this->IsUserChecked = true;
		}

		if ($this->IsUserChecked)
		{
			if ($UserRow['onlinetime'] < (time() - 30) || $UserRow['user_lastip'] != request::getClientIp(true) || (request::G('set') == "chat" && ($UserRow['onlinetime'] < time() - 120 || $UserRow['chat'] == 0)) || (request::G('set') != "chat" && $UserRow['chat'] > 0))
			{
				sql::build()->update('game_users')->set(Array('onlinetime' => time()));

				if ($UserRow['user_lastip'] != request::getClientIp(true))
				{
					sql::build()->set(Array('user_lastip' => request::getClientIp(true)));

					$query = new sql;
					$query->insert('game_log_ip')->set(Array
					(
						'id'	=> $UserRow['id'],
						'time'	=> time(),
						'ip'	=> request::getClientIp(true)
					))->execute();

					unset($query);
				}

				if (request::G('set') == "chat" && $UserRow['chat'] == 0)
				{
					sql::build()->set(Array('chat' => 1));

					$UserRow['chat'] = 1;
				}
				elseif (request::G('set') != "chat" && $UserRow['chat'] > 0)
					sql::build()->set(Array('chat' => 0));

				sql::build()->where('id', '=', $UserRow['id'])->execute();
			}
		}

		return $UserRow;
	}

	public function CheckReferLink ()
	{
		if (!isset($_SESSION['uid']) && is_numeric($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0)
		{
			global $user;

			$id = intval($_SERVER['QUERY_STRING']);

			$login = $user->getById($id, Array('id'));

			if (isset($login['id']))
			{
				$ip = request::getClientIp();

				$res = db::query("SELECT `id` FROM game_moneys where `ip` = '" . $ip . "' AND `time` > '" . (time() - 86400) . "'", true);

				if (!isset($res['id']))
				{
					db::query("INSERT INTO game_moneys values ('" . $login['id'] . "', '" . $ip . "','" . time() . "','" . (isset($_SERVER['HTTP_REFERER']) ? addslashes($_SERVER['HTTP_REFERER']) : '') . "', '" . addslashes($_SERVER['HTTP_USER_AGENT']) . "')");
					db::query("UPDATE game_users SET links = links + 1, refers = refers + 1 WHERE id = '" . $login['id'] . "'");
				}
				$_SESSION['ref'] = $login['id'];
			}
		}
	}

	public function auth ($userId, $password, $security = 0, $expiretime = 0)
	{
		$secret = $this->getCookiePassword($userId, $password, $security);

		setcookie(COOKIE_NAME."_id", 		$userId, $expiretime, "/", $_SERVER["SERVER_NAME"], 0);
		setcookie(COOKIE_NAME."_secret", 	$secret, $expiretime, "/", $_SERVER["SERVER_NAME"], 0);
		setcookie(COOKIE_NAME."_uni", 		"uni".UNIVERSE, $expiretime, "/", ".xnova.su", 0);

		$_COOKIE[COOKIE_NAME.'_id']		= $userId;
		$_COOKIE[COOKIE_NAME.'_secret'] = $secret;

		session_destroy();
	}

	public function ClearSession($redirect = true)
	{
		session_destroy();

		setcookie(COOKIE_NAME."_id", "", 0, "/", $_SERVER["SERVER_NAME"], 0);
		setcookie(COOKIE_NAME."_secret", "", 0, "/", $_SERVER["SERVER_NAME"], 0);
		setcookie(COOKIE_NAME."_extid", "", 0, "/", $_SERVER["SERVER_NAME"], 0);
		setcookie(COOKIE_NAME."_uni", "", 0, "/", ".xnova.su", 0);

		if ($redirect)
        	request::redirectTo("?set=login");
	}
}
 
?>