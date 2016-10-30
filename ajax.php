<?php
/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Xcms\core;
use Xcms\db;
use Xcms\session;
use Xcms\strings;
use Xnova\User;

if (!isset($_REQUEST['action']))
	die('сасай лолка');

define('INSIDE', true);

session_start();

require($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/core.php');
core::init();
core::loadConfig();
strings::setLang('ru');

require(ROOT_DIR.APP_PATH.'varsGlobal.php');
require(ROOT_DIR.APP_PATH.'functions/functions.php');

$session = new session();

$session->CheckTheUser();

if ($session->isAuthorized())
{
	$user = user::get();
	$user->load_from_array($session->user);
}
else
	die('сасай, залогинись');

switch ($_REQUEST['action'])
{
	case 'getPlanetList':

		$Sort = $user->data['planet_sort'];

		$QryPlanets = 'ORDER BY ';

		if ($Sort == 0)
		{
			$QryPlanets .= "`id` ";
		}
		elseif ($Sort == 1)
		{
			$QryPlanets .= "`galaxy`, `system`, `planet`, `planet_type` ";
		}
		elseif ($Sort == 2)
		{
			$QryPlanets .= "`name` ";
		}
		elseif ($Sort == 3)
		{
			$QryPlanets .= "`planet_type` ";
		}
		else
			$QryPlanets .= "`id` ";

		$QryPlanets .= ($user->data['planet_sort_order'] == 1) ? "DESC" : "ASC";

		$planets_query = db::query("SELECT id, name, image, galaxy, system, planet, planet_type FROM game_planets WHERE id_owner='" . $user->data['id'] . "' AND destruyed = 0 " . $QryPlanets . ";");

		$list = db::extractResult($planets_query);

		foreach ($list AS $i => $planet): ?>
			<div class="planet type_<?=$planet['planet_type'] ?> <?=($user->data['current_planet'] == $planet['id'] ? 'current' : '') ?>">
				<? if ($i == 0): ?>
					<div class="reload" onclick="reloadPlanetList()" title="Обновить"></div>
				<? endif; ?>
				<a href="?set=overview&amp;cp=<?=$planet['id'] ?>&amp;re=0" title="<?=$planet['name'] ?>">
					<img src="<?=DPATH ?>planeten/small/s_<?=$planet['image'] ?>.jpg" height="50" width="50" alt="<?=$planet['name'] ?>">
				</a>
				<div>
					<?=$planet['name'] ?>
					<br>
					<?=BuildPlanetAdressLink($planet) ?>
				</div>
				<div class="clear"></div>
			</div>
		<? endforeach;

		break;
}

?>