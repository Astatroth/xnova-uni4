<?php

use Xcms\core;
use Xcms\db;
use Xcms\strings;
use Xnova\User;

define('INSIDE', true);

include($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/core.php');
core::init();

error_reporting(E_ALL);

header ("Content-type: image/jpeg");

$id = db::escape_string($_SERVER['QUERY_STRING']);

if (is_numeric($id) && strlen($id) > 0)
{
	if (!file_exists(ROOT_DIR.CACHE_DIR.'/userbars'))
		mkdir(ROOT_DIR.CACHE_DIR.'/userbars');

	if (file_exists(ROOT_DIR.CACHE_DIR.'/userbars/userbar_'.$id.'.jpg'))
	{
		echo file_get_contents(ROOT_DIR.CACHE_DIR.'/userbars/userbar_'.$id.'.jpg');

		$changeTime = filectime(ROOT_DIR.CACHE_DIR.'/userbars/userbar_'.$id.'.jpg');

		if ($changeTime < time() - 3600)
		{
			unlink(ROOT_DIR.CACHE_DIR.'/userbars/userbar_'.$id.'.jpg');
		}
	}
	else
	{
		$image = imagecreatefrompng(ROOT_DIR.'images/userbar.png');

		core::loadConfig();

		$lang = array();
		$lang[1]  = "Конфедерация";
		$lang[2]  = "Бионики";
		$lang[3]  = "Сайлоны";
		$lang[4]  = "Древние";

		$user = user::get()->getById($id, Array('id', 'username', 'race', 'lvl_minier', 'lvl_raid'));

		if (isset($user['id']))
		{
			$planet = db::query("SELECT name, galaxy, system, planet FROM game_planets WHERE id_owner = ".$id." AND planet_type = 1 ORDER BY id LIMIT 1", true);

			$stats = db::query("SELECT `total_points`, `total_rank` FROM game_statpoints WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . $id . "';", true);

			$color = "FFFFFF";
			$red = hexdec(substr($color,0,2));
			$green = hexdec(substr($color,2,4));
			$blue = hexdec(substr($color,4,6));
			$select = imagecolorallocate($image,$red,$green,$blue);
			$txt_shadow = imagecolorallocatealpha($image, 255, 255, 255, 255);
			$txt_color = imagecolorallocatealpha($image, 255, 255, 255, 2);
			$txt_shadow2 = imagecolorallocatealpha($image, 255, 255, 255, 255);
			$txt_color2 = imagecolorallocatealpha($image, 255, 255, 255, 40);

			// Имя пользователя
			imagettftext($image, 9, 0, 15, 25, $txt_shadow, ROOT_DIR."images/terminator.ttf", $user['username']);
			imagettftext($image, 9, 0, 13, 23, $txt_color, ROOT_DIR."images/terminator.ttf", $user['username']);

			// Вселенная
			imagettftext($image, 6, 0, 331, 76, $txt_shadow, ROOT_DIR."images/terminator.ttf", "XNOVA.SU UNI ".UNIVERSE);
			imagettftext($image, 6, 0, 330, 75, $txt_color, ROOT_DIR."images/terminator.ttf", "XNOVA.SU UNI ".UNIVERSE);

			// Планета
			imagettftext($image, 6, 0, 13, 37, $txt_color2, ROOT_DIR."images/KLMNFP2005.ttf", $planet['name']." [".$planet['galaxy'].":".$planet['system'].":".$planet['planet']."]");

			// Очки
			imagettftext($image, 6, 0, 13, 55, $txt_color, ROOT_DIR."images/KLMNFP2005.ttf", "Очки: ".strings::pretty_number(intval($stats['total_points']))."");
			imagettftext($image, 6, 0, 13, 70, $txt_color, ROOT_DIR."images/KLMNFP2005.ttf", "Место: ".strings::pretty_number(intval($stats['total_rank']))." из ".strings::pretty_number(core::getConfig('users_amount', 0))."");

			// Дата генерации
			imagettftext($image, 6, 0, 365, 13, $txt_color, ROOT_DIR."images/KLMNFP2005.ttf", date("d.m.Y"));
			imagettftext($image, 6, 0, 377, 25, $txt_color, ROOT_DIR."images/KLMNFP2005.ttf", date("H:i:s"));

			$m = user::get()->getRankId($user['lvl_minier']);
			$f = user::get()->getRankId($user['lvl_raid']);

			$img = imagecreatetruecolor(32, 32);
			$source = imagecreatefrompng(ROOT_DIR.'images/ranks/m'.$m.'.png');
			imageAlphaBlending($img, false);
			imageSaveAlpha($img, true);
			imagecopyresized($img, $source, 0, 0, 0, 0, 32, 32, 64, 64);

			imagecopy($image, $img, 250, 25, 0, 0, 32, 32);
			imagedestroy($img);
			imagedestroy($source);

			$img = imagecreatetruecolor(32, 32);
			$source = imagecreatefrompng(ROOT_DIR.'images/ranks/f'.$f.'.png');
			imageAlphaBlending($img, false);
			imageSaveAlpha($img, true);
			imagecopyresized($img, $source, 0, 0, 0, 0, 32, 32, 64, 64);

			imagecopy($image, $img, 280, 25, 0, 0, 32, 32);
			imagedestroy($img);
			imagedestroy($source);

			// Расса
			imagettftext($image, 6, 0, 245, 65, $txt_color, ROOT_DIR."images/KLMNFP2005.ttf", $lang[$user['race']]);
		}

		imagejpeg($image, ROOT_DIR.CACHE_DIR.'/userbars/userbar_'.$id.'.jpg', 90);
		imagejpeg($image, NULL, 90);
		imagedestroy($image);
	}
}
else
{
	$image = imagecreatefrompng(ROOT_DIR.'images/userbar.png');
	imagejpeg($image, NULL, 85);
	imagedestroy($image);
}
 
?>