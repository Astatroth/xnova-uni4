<?php

namespace Xnova\controllers;

use Xcms\core;
use Xcms\db;
use Xcms\request;
use Xcms\strings;
use Xnova\pageHelper;

class showLostpasswordPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();
	}
	
	public function show ()
	{
		$step = (isset($_GET['step']) ? intval($_GET['step']) : 0);

		if (isset($_GET['id']) && isset($_GET['passw']) && is_numeric($_GET['id']) && $_GET['id'] > 0 && $_GET['passw'] != "")
		{
			$id = intval($_GET['id']);
			$key = addslashes($_GET['passw']);

			$Lost = db::query("SELECT * FROM game_lostpwd WHERE ks = '" . $key . "' AND u_id = '" . $id . "' AND time > " . time() . "-3600 AND activ = 0 LIMIT 1;", true);

			if ($Lost['u_id'] != "")
				$Mail = db::query("SELECT u.username, ui.email FROM game_users u, game_users_info ui WHERE ui.id = u.id AND u.id = '" . $Lost['u_id'] . "'", true);
			else
				$this->message('Действие данной ссылки истекло, попробуйте пройти процедуру заново!', 'Ошибка', '', 0, false);

			if (!preg_match("/^[А-Яа-яЁёa-zA-Z0-9]+$/u", $key))
				$this->message('Ошибка выборки E-mail адреса!', 'Ошибка', '', 0, false);
			elseif (empty($Mail['email']))
				$this->message('Ошибка выборки E-mail адреса!', 'Ошибка', '', 0, false);
			else
			{
				$NewPass = strings::randomSequence();

				core::loadLib('mail');

				$mail = new \PHPMailer();

				$mail->IsMail();
				$mail->IsHTML(true);
				$mail->CharSet = 'utf-8';
				$mail->SetFrom(ADMINEMAIL, SITE_TITLE);
				$mail->AddAddress($Mail['email'], SITE_TITLE);
				$mail->Subject = 'Новый пароль в Xnova Game: '.UNIVERSE.' вселенная';
				$mail->Body = "Ваш новый пароль от игрового аккаунта: " . $Mail['username'] . ": " . $NewPass;
				$mail->Send();

				db::query("UPDATE game_users_info SET `password` ='" . md5($NewPass) . "' WHERE `id`='" . $id . "' LIMIT 1;");
				db::query("DELETE FROM game_lostpwd WHERE u_id = '" . $id . "'");

				$this->message('Ваш новый пароль: ' . $NewPass . '. Копия пароля отправлена на почтовый ящик!', 'Восстановление пароля', '', 0, false);
			}
		}
		else
		{
			$error = '';

			if ($step == 2)
			{
				if (isset($_POST['login']) && $_POST['login'] != "")
				{
					$login = addslashes($_POST['login']);

					$inf = db::query("SELECT u.id, u.username, ui.email FROM game_users u, game_users_info ui WHERE ui.id = u.id AND ui.email = '" . $login . "' LIMIT 1;", true);

					if (isset($inf['id']))
					{
						core::loadLib('mail');

						$ip = request::getClientIp();

						$key = md5($inf['id'] . date("d-m-Y H:i:s", time()) . "ыыы");
						db::query("INSERT INTO game_lostpwd (u_id, ks, time, ip, activ) VALUES (" . $inf['id'] . ",'" . $key . "'," . time() . ", '" . $ip . "',0)");

						$mail = new \PHPMailer();

						$mail->IsMail();
						$mail->IsHTML(true);
						$mail->CharSet = 'utf-8';
						$mail->SetFrom(ADMINEMAIL, SITE_TITLE);
						$mail->AddAddress($inf['email'], SITE_TITLE);
						$mail->Subject = 'Восстановление забытого пароля';

						$body = "Доброго времени суток Вам!\nКто то с IP адреса " . $ip . " запросил пароль к персонажу " . $inf['username'] . " в онлайн-игре Xnova.su.\nТак как в анкете у персонажа указан данный e-mail, то именно Вы получили это письмо.\n\n
						Для восстановления пароля перейдите по ссылке: <a href='http://".$_SERVER['HTTP_HOST']."/?set=lostpassword&id=" . $inf['id'] . "&passw=" . $key . "'>http://".$_SERVER['HTTP_HOST']."/?set=lostpassword&id=" . $inf['id'] . "&passw=" . $key . "</a>";

						$mail->Body = $body;

						if ($mail->Send())
							$error = 'Ссылка на восстановления пароля отправлена на ваш E-mail';
						else
							$error = 'Произошла ошибка при отправке сообщения. Обратитесь к администратору сайта за помощью.';
					}
					else
					{
						$error = 'Персонаж не найден в базе';
					}
				}
				else
				{
					$error = 'Персонаж не найден в базе';
				}
			}

			$this->setTemplate('lostpassword');
			$this->set('error', $error);

			$this->setTitle('Восстановление пароля');
			$this->showTopPanel(false);
			$this->showLeftPanel(false);
			$this->display();
		}
	}
}

?>