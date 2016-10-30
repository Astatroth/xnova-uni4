<?php

namespace Xnova;

use Xcms\Core;
use Xcms\db;
use Xcms\Request;
use Xcms\sql;

require_once(ROOT_DIR.APP_PATH."varsGlobal.php");
require_once(ROOT_DIR.APP_PATH."functions/functions.php");

class app
{
	/**
	 * @var user $user
	 */
	static $user;
	/**
	 * @var planet $planetrow
	 */
	static $planetrow;

	public function __construct () {}

	public static function init ()
	{
		global $page;

		if (function_exists('sys_getloadavg'))
		{
			$load = sys_getloadavg();

			if ($load[0] > 15)
			{
				header('HTTP/1.1 503 Too busy, try again later');
				die('Server too busy. Please try again later.');
			}
		}

		if (self::$user instanceof user)
			die('kernel panic');

		self::$user = user::get();

		if (user::get()->isAuthorized())
		{
			// Кэшируем настройки профиля в сессию
			if (!isset($_SESSION['config']) || strlen($_SESSION['config']) < 10)
			{
				$inf = db::query("SELECT planet_sort, planet_sort_order, color, timezone, spy FROM game_users_info WHERE id = " . user::get()->getId() . ";", true);
				$_SESSION['config'] = json_encode($inf);
			}

			if (!core::getConfig('showPlanetListSelect', 0))
				core::setConfig('showPlanetListSelect', user::get()->getUserOption('planetlistselect'));

			if (request::G('fullscreen') == 'Y')
			{
				setcookie(COOKIE_NAME."_full", "Y", (time() + 30 * 86400), "/", $_SERVER["SERVER_NAME"], 0);
				$_COOKIE[COOKIE_NAME."_full"] = 'Y';
			}

			if ($_SERVER['SERVER_NAME'] == 'ok1.xnova.su')
			{
				core::setConfig('socialIframeView', 2);
				core::setConfig('ajaxNavigation', 2);

				define('RPATH', '/uni4/');
			}

			if ($_SERVER['SERVER_NAME'] == 'vk.xnova.su')
			{
				core::setConfig('socialIframeView', 2);
				core::setConfig('ajaxNavigation', 2);
			}

			if (isset($_COOKIE[COOKIE_NAME."_full"]) && $_COOKIE[COOKIE_NAME."_full"] = 'Y')
			{
				core::setConfig('socialIframeView', 0);
				core::setConfig('overviewListView', 1);
				core::setConfig('showPlanetListSelect', 0);
			}

			// Заносим настройки профиля в основной массив
			$inf = json_decode($_SESSION['config'], true);
			user::get()->data = array_merge(user::get()->data, $inf);
			user::get()->getAllyInfo();

			self::checkUserLevel();

			if (SERVER_CODE == 'OK1U')
			{
				$points = db::first(db::query("SELECT `total_points` FROM game_statpoints WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . user::get()->getId() . "';", true));

				if (!$points || $points < 1000)
				{
					core::setConfig('game_speed', core::getConfig('game_speed') * 5);
					core::setConfig('resource_multiplier', core::getConfig('resource_multiplier') * 3);
					core::setConfig('noob', 1);
				}
			}

			// Выставляем планету выбранную игроком из списка планет
			user::get()->setSelectedPlanet();

			if ((user::get()->data['race'] == 0 || user::get()->data['avatar'] == 0) && $page != 'infos' && $page != 'content')
				$page = 'start';
		}
	}

	static public function loadPlanet ()
	{
		if (app::$planetrow instanceof planet)
			return;

		global $page;

		if (user::get()->data['current_planet'] == 0 && user::get()->data['id_planet'] == 0)
		{
			if (user::get()->data['race'] > 0)
			{
				user::get()->data['id_planet'] = system::CreateRegPlanet(user::get()->getId());
				user::get()->data['current_planet'] = user::get()->data['id_planet'];
			}
		}

		if (user::get()->data['current_planet'] > 0 && user::get()->data['id_planet'] > 0)
		{
			// Выбираем информацию о планете
			self::$planetrow = new planet(user::get()->data['current_planet']);
			self::$planetrow->load_user_info(user::get());
			self::$planetrow->checkOwnerPlanet();

			// Проверяем корректность заполненных полей
			self::$planetrow->CheckPlanetUsedFields();

			if (isset(self::$planetrow->data['id']))
			{
				// Обновляем ресурсы на планете когда это необходимо
				if ((($page == "overview" || ($page == "fleet" && @$_GET['page'] != 'fleet_3') || $page == "galaxy" || $page == "resources" || $page == "imperium" || $page == "infokredits" || $page == "tutorial" || $page == "techtree" || $page == "search" || $page == "support" || $page == "sim" || $page == "tutorial" || !$page) && self::$planetrow->data['last_update'] > (time() - 60)))
					self::$planetrow->PlanetResourceUpdate(time(), true);
				else
					self::$planetrow->PlanetResourceUpdate();
			}

			// Проверка наличия законченных построек и исследований
			if (self::$planetrow->UpdatePlanetBatimentQueueList())
				self::$planetrow->PlanetResourceUpdate(time(), true);
		}
	}

	static function checkUserLevel ()
	{
		if (!isset(app::$user->data['id']))
			return;

		$indNextXp = pow(app::$user->data['lvl_minier'], 3);
		$warNextXp = pow(app::$user->data['lvl_raid'], 2);

		$giveCredits = 0;

		if (app::$user->data['xpminier'] >= $indNextXp && app::$user->data['lvl_minier'] < core::getConfig('level.max_ind', 100))
		{
			app::$user->saveData(array
			(
				'+lvl_minier' 	=> 1,
				'+credits' 		=> core::getConfig('level.credits', 10),
				'-xpminier' 	=> $indNextXp
			));

			app::$user->sendMessage(app::$user->getId(), 0, 0, 1, '', '<a href=?set=officier>Получен новый промышленный уровень</a>');

			app::$user->data['lvl_minier'] 	+= 1;
			app::$user->data['xpminier'] 	-= $indNextXp;

			$giveCredits += core::getConfig('level.credits', 10);
		}

		if (app::$user->data['xpraid'] >= $warNextXp && app::$user->data['lvl_raid'] < core::getConfig('level.max_war', 100))
		{
			app::$user->saveData(array
			(
				'+lvl_raid' => 1,
				'+credits' 	=> core::getConfig('level.credits', 10),
				'-xpraid' 	=> $warNextXp
			));

			app::$user->sendMessage(app::$user->getId(), 0, 0, 1, '', '<a href=?set=officier>Получен новый военный уровень</a>');

			app::$user->data['lvl_raid'] 	+= 1;
			app::$user->data['xpraid'] 		-= $warNextXp;

			$giveCredits += core::getConfig('level.credits', 10);
		}

		if ($giveCredits != 0)
		{
			sql::build()->insert('game_log_credits')->set(Array
			(
				'uid' 		=> app::$user->data['id'],
				'time' 		=> time(),
				'credits' 	=> $giveCredits,
				'type' 		=> 4,
			))
			->execute();

			$reffer = db::query("SELECT u_id FROM game_refs WHERE r_id = " . app::$user->data['id'] . "", true);

			if (isset($reffer['u_id']))
			{
				db::query("UPDATE game_users SET credits = credits + " . round($giveCredits / 2) . " WHERE id = " . $reffer['u_id'] . "");
				db::query("INSERT INTO game_log_credits (uid, time, credits, type) VALUES (" . $reffer['u_id'] . ", " . time() . ", " . round($giveCredits / 2) . ", 3)");
			}
		}
	}
}