<?php

namespace Xnova\controllers;

use Xcms\core;
use Xcms\db;
use Xcms\request;
use Xnova\User;
use Xnova\pageHelper;

class showLoginPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();
	}
	
	public function show ()
	{
		if (!defined('ALLOW_LOGIN') || !ALLOW_LOGIN)
			$this->message('Вход в игру закрыт');
	
		if (isset($_REQUEST['emails']))
		{
			if ($_REQUEST['emails'] != '')
			{
				$login = db::query("SELECT u.id, u.options_toggle, ui.password FROM game_users u, game_users_info ui WHERE ui.id = u.id AND ui.`email` = '" . db::escape_string($_REQUEST['emails']) . "' LIMIT 1", true);

				if (isset($login['id']))
				{
					if ($login['password'] == md5($_REQUEST['password']))
					{
						global $session;

						$options = user::get()->unpackOptions($login['options_toggle']);

						$expiretime = (isset($_REQUEST["rememberme"])) ? (time() + 2419200) : 0;
						$passw_string = $session->getCookiePassword($login['id'], $login['password'], $options['security']);

						if (isset($_REQUEST['mobile']))
							echo json_encode(Array('id' => $login['id'], 'secret' => $passw_string));
						else
						{
							setcookie(COOKIE_NAME."_id", 		$login['id'], 	$expiretime, "/", $_SERVER["SERVER_NAME"], 0);
							setcookie(COOKIE_NAME."_secret", 	$passw_string, 	$expiretime, "/", $_SERVER["SERVER_NAME"], 0);
							setcookie(COOKIE_NAME."_uni", 		"uni".UNIVERSE, $expiretime, "/", ".xnova.su", 0);

							request::redirectTo("?set=overview");
						}
					}
					else
						echo 'Неверный E-mail и/или пароль';
				}
				else
					echo 'Игрока с таким E-mail адресом не найдено';
			}
			else
				echo 'Введите хоть что-нибудь!';
		}
		else
		{
			$this->page->header = 'login_';
			$this->setTemplate('login');

			$parse = array();
			$parse['online_users'] = core::getConfig('online');
			$parse['users_amount'] = core::getConfig('users_amount');

			$this->set('parse', $parse);

			unset($_GET['set']);

			$this->setAtribute('Description', 'Вы являетесь межгалактическим императором, который распространяет своё влияние посредством различных стратегий на множество галактик. Вы начинаете на своей собственой планете и строите там экономическую и военную инфраструктуру. Исследования дают Вам доступ к новым технологиям и более совершенным системам вооружения. На всём протяжении игры Вы будете колонизировать множество планет, заключать альянсы с другими владыками и вести с ними торговлю или войну.');
			$this->setAtribute('Keywords', 'XNova, Стратегия, Космос, Ogame, Online, Game, Игра, Огама, Огейм, Икснова, Хнова, браузер, браузерная, прокачка, кредиты');

			$this->setTitle(core::getConfig('game_name').' - браузерная онлайн стратегия');
			$this->showTopPanel(false);
			$this->showLeftPanel(false);
			$this->display();
		}
	}
}

?>