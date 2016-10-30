<?php

namespace Xcms\ExtAuth;

use Xcms\core;
use Xcms\db;
use Xcms\request;
use Xcms\socials;
use Xcms\sql;
use Xcms\strings;

class ok
{
	private $isLogin = false;
	private $data = array();

	function __construct()
	{
		if ($_POST['application_key'] != '' && $_POST['api_server'] != '')
		{
			socials::okConnect($_POST['application_key'], $_POST['api_server']);

			$uInfo = socials::okLoad('users/getInfo', array('uids' => intval($_POST['logged_user_id']), 'fields' => 'first_name,last_name,name,gender,birthday,age,locale,location,current_location,online,pic128x128'));
			$this->data = $uInfo[0];

			$this->isLogin = true;

			$this->login();
		}
	}

	public function isAuthorized ()
	{
		return $this->isLogin;
	}

	public function login()
	{
		if (!$this->isAuthorized())
			return false;

		global $session;

		if (md5($_POST['logged_user_id'].$_POST['session_key'].APPSECRET) != $_POST['auth_sig'])
		{
			die('<script type="text/javascript">alert("Параметры авторизации являются некорректными!")</script>');
		}
		else
		{
			$Row = db::query("SELECT u.id, u.tutorial, ui.password, a.id AS auth_id FROM game_users u, game_users_info ui, game_users_auth a WHERE ui.id = u.id AND a.user_id = u.id AND a.external_id = 'http://odnoklassniki.ru/".intval($_POST['logged_user_id'])."';", true);

			if (!isset($Row['id']))
			{
				$this->register();
			}
			else
			{
				sql::build()->update('game_users_auth')->setField('enter_time', time())->where('id', '=', $Row['auth_id'])->execute();

				$session->auth($Row['id'], $Row['password'], 0, (time() + 2419200));

				setcookie(COOKIE_NAME."_uni", "uni".UNIVERSE, (time() + 2419200), "/", ".xnova.su", 0);
			}

			setcookie(COOKIE_NAME."_full", "", 0, "/", $_SERVER["SERVER_NAME"], 0);

			session_start();
			unset($_SESSION['OKAPI']);
			$_SESSION['OKAPI'] = $_POST;

			$set = 'overview';

			echo '<center>Загрузка...-</center>'.print_r($Row, true).'<script>parent.location.href="?set='.$set.'&'.http_build_query($_POST).'";</script>';
			die();
		}
	}

	public function register ()
	{
		$uid = intval($_POST['logged_user_id']);

		if (!$uid)
			return false;

		if (isset($_POST['custom_args']))
		{
			parse_str($_POST['custom_args'], $cArgs);

			$refer = (isset($cArgs['userId']) ? intval($cArgs['userId']) : 0);
		}
		else
			$refer = 0;

		global $session;

		$NewPass = strings::randomSequence();

		if ($refer != 0)
		{
			$refe = db::query("SELECT id FROM game_users_info WHERE id = '".$refer."'", true);

			if (!isset($refe['id']))
				$refer = 0;
		}
		
		$check = db::query("SELECT user_id FROM game_users_auth WHERE external_id = 'http://odnoklassniki.ru/".$uid."'", true);
		
		if (isset($check['user_id']))
		{
			$find = db::query("SELECT id FROM game_users WHERE id = ".$check['user_id']."", true);

			if (!isset($find['id']))
			{
				db::query("DELETE FROM game_users_auth WHERE user_id = ".$check['user_id']."");
			}
			else
				return false;
		}

		db::query("LOCK TABLES game_users_info WRITE, game_users WRITE, game_users_auth WRITE");

		sql::build()->insert('game_users')->set(Array
		(
			'username' 		=> addslashes(str_replace('\'', '', $this->data['name'])),
			'sex' 			=> 0,
			'id_planet' 	=> 0,
			'user_lastip' 	=> request::getClientIp(true),
			'bonus' 		=> time(),
			'onlinetime' 	=> time()
		))
		->execute();

		$iduser = db::insert_id();

		if ($iduser > 0)
		{
			sql::build()->insert('game_users_info')->set(Array
			(
				'id' 			=> $iduser,
				'email' 		=> '',
				'register_time' => time(),
				'password' 		=> md5($NewPass)
			))
			->execute();

			sql::build()->insert('game_users_auth')->set(Array('user_id' => $iduser, 'external_id' => 'http://odnoklassniki.ru/'.$uid, 'register_time' => time(), 'enter_time' => time()))->execute();

			db::query("UNLOCK TABLES");
			
			if ($refer != 0)
			{
				$ref = db::query("SELECT id FROM game_users_info WHERE id = '".$refer."'", true);

				if (isset($ref['id']))
				{
					sql::build()->insert('game_refs')->set(Array('r_id' => $iduser, 'u_id' => $refer))->execute();
				}
			}

			core::updateConfig('users_amount', (core::getConfigFromDB('users_amount', 0) + 1));
			core::clearConfig();

			$session->auth($iduser, md5($NewPass));

			return true;
		}
		else
		{
			db::query("UNLOCK TABLES");
			
			return false;
		}
	}
}

?>