<?php

namespace Xcms;

/**
 * Вспомогательные методы для реализации мини CMS
 * $Revision$
 * $Date$
 * @author AlexPro
 * @copyright 2011 - 2014
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */
class cms
{
	public static function getMenu ($parent_id, $lvl = 1, $all = false)
	{
		$array = array();

		if ($lvl > 0)
		{
			$childrens = db::extractResult(db::query("SELECT id, name, alias, icon, image, active FROM game_cms_menu WHERE parent_id = ".$parent_id." ".($all ? '' : "AND active = '1'")." ORDER BY priority ASC"));

			if (count($childrens) > 0)
			{
				foreach ($childrens AS $children)
				{
					$array[] = array(
						'id' 		=> $children['id'],
						'alias' 	=> $children['alias'],
						'name' 		=> $children['name'],
						'children' 	=> ($lvl > 1) ? self::getMenu($children['id'], ($lvl - 1), $all) : array(),
						'active' 	=> $children['active'],
						'icon' 		=> $children['icon'],
						'image' 	=> $children['image']
					);
				}
			}
		}

		return $array;
	}
}
 
?>