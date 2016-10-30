<?php

namespace Xnova\controllers;

use Xcms\core;
use Xcms\db;
use Xcms\request;
use Xcms\sql;
use Xcms\strings;
use Xnova\pageHelper;

class showRegPage extends pageHelper
{
	function __construct ()
	{
		if (!defined('ALLOW_REGISTRATION') || !ALLOW_REGISTRATION)
			die('Регистрация закрыта');

		parent::__construct();
	}
	
	public function show ()
	{
		global $session;

		strings::includeLang('reg');

		if ($_POST)
		{
			$errors = 0;
			$errorlist = "";

			$_POST['email'] = strip_tags(trim($_POST['email']));

			if (!is_email($_POST['email']))
			{
				$errorlist .= "\"" . $_POST['email'] . "\" " . _getText('error_mail');
				$errors++;
			}

			$girilen = trim($_REQUEST["captcha"]);

			if (!isset($_SESSION['captcha']) || ($_SESSION['captcha'] != $girilen && $_SESSION['captcha'] != ""))
			{
				$errorlist .= _getText('error_captcha');
				$errors++;
			}

			if (mb_strlen($_POST['passwrd'], 'UTF-8') < 4)
			{
				$errorlist .= _getText('error_password');
				$errors++;
			}

			if (!isset($_POST['rgt']) || !isset($_POST['sogl']) || $_POST['rgt'] != 'on' || $_POST['sogl'] != 'on')
			{
				$errorlist .= _getText('error_rgt');
				$errors++;
			}

			$ExistMail = db::query("SELECT `id` FROM game_users_info WHERE `email` = '" . db::escape_string(trim($_POST['email'])) . "' LIMIT 1;", true);

			if (isset($ExistMail['id']))
			{
				$errorlist .= _getText('error_emailexist');
				$errors++;
			}

			if ($errors != 0)
			{
				$this->setTemplate('reg');
				$this->set('errors', $errorlist);

				$this->setTitle(_getText('registry'));
				$this->showTopPanel(false);
				$this->showLeftPanel(false);
				$this->display();
			}
			else
			{
				$newpass 	= trim($_POST['passwrd']);
				$UserEmail 	= trim($_POST['email']);

				$md5newpass = md5($newpass);

				sql::build()->insert('game_users')->set(Array
				(
					'username' 		=> '',
					'sex' 			=> 0,
					'id_planet' 	=> 0,
					'user_lastip' 	=> request::getClientIp(true),
					'bonus' 		=> time(),
					'onlinetime' 	=> time()
				))
				->execute();

				$iduser = db::insert_id();

				sql::build()->insert('game_users_info')->set(Array
				(
					'id' 			=> $iduser,
					'email' 		=> db::escape_string($UserEmail),
					'register_time' => time(),
					'password' 		=> $md5newpass
				))
				->execute();

				if (isset($_SESSION['ref']))
				{
					$refe = db::query("SELECT id FROM game_users WHERE id = ".$_SESSION['ref'], true);

					if (isset($refe['id']))
					{
						sql::build()->insert('game_refs')->set(Array('r_id' => $iduser, 'u_id' => $_SESSION['ref']))->execute();
					}
				}

				core::updateConfig('users_amount', (core::getConfigFromDB('users_amount', 0) + 1));
				core::clearConfig();

				core::loadLib('mail');

				$mail = new \PHPMailer();
				$mail->SetFrom(ADMINEMAIL, SITE_TITLE);
				$mail->AddAddress($UserEmail);
				$mail->IsHTML(true);
				$mail->CharSet = 'utf-8';
				$mail->Subject = "Регистрация в игре XNova";
				$mail->Body = "Вы успешно зарегистрировались в игре XNova.<br>Ваши данные для входа в игру:<br>Email: " . $UserEmail . "<br>Пароль:" . $newpass . "";
				$mail->Send();

				$passw_string = $session->getCookiePassword($iduser, $md5newpass);
				$expiretime = 0;

				setcookie(COOKIE_NAME."_id", 		$iduser, 		$expiretime, "/", $_SERVER["SERVER_NAME"], 0);
				setcookie(COOKIE_NAME."_secret", 	$passw_string, 	$expiretime, "/", $_SERVER["SERVER_NAME"], 0);
				setcookie(COOKIE_NAME."_uni", 		"uni".UNIVERSE, $expiretime, "/", ".xnova.su", 0);

				request::redirectTo("?set=overview");
			}
		}
		else
		{
			$this->setTemplate('reg');

			$this->setTitle(_getText('registry'));
			$this->showTopPanel(false);
			$this->showLeftPanel(false);
			$this->display();
		}
	}
}

?>