<?php

namespace Xcms\ExtAuth;

use Xcms\core;
use Xcms\db;
use Xcms\request;
use Xcms\sql;
use Xnova\User;

class ulogin
{
	private $token = '';
	private $data = array();
	private $isLogin = false;

	function __construct($token)
	{
		$s = file_get_contents('http://u-login.com/token.php?token=' . $token . '&host=' . $_SERVER['HTTP_HOST']);
		$this->data = json_decode($s, true);

		$this->token = $token;

		if (isset($this->data['identity']))
		{
			$this->isLogin = true;
			$this->login();
		}
	}

	public function isAuthorized ()
	{
		return $this->isLogin;
	}

	public function login ()
	{
		if (!$this->isAuthorized())
			return false;

		$check = db::query("SELECT u.options_toggle, ui.id, ui.password, a.id AS auth_id FROM game_users u, game_users_info ui, game_users_auth a WHERE ui.id = u.id AND a.user_id = u.id AND a.external_id = '".$this->data['identity']."'", true);

		if (isset($check['id']))
		{
			sql::build()->update('game_users_auth')->setField('enter_time', time())->where('id', '=', $check['auth_id'])->execute();

			global $session;

			$options = user::get()->unpackOptions($check['options_toggle']);

			$session->auth($check['id'], $check['password'],  $options['security'], (time() + 24192000));

			return true;
		}
		else
			return $this->register();
	}

	public function register ()
	{
		$check = db::query("SELECT user_id FROM game_users_auth WHERE external_id = '".$this->data['identity']."'", true);

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

		$refer = (isset($_SESSION['ref']) ? intval($_SESSION['ref']) : 0);

		sql::build()->insert('game_users')->set(Array
		(
			'username' 		=> trim($this->data['first_name']." ".$this->data['last_name']),
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
				'password' 		=> md5($this->token)
			))
			->execute();

			sql::build()->insert('game_users_auth')->set(Array('user_id' => $iduser, 'external_id' => $this->data['identity'], 'register_time' => time(), 'enter_time' => time()))->execute();

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

			global $session;

			$session->auth($iduser, md5($this->token));

			return true;
		}
		else
			return false;
	}
}
 
?>