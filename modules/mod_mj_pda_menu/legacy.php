<?php
/**
 * @version		$Id: legacy.php 10856 2008-08-30 06:35:08Z willebil $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Utility function for writing a menu link
 */
function mosGetMenuLink_pda($mitem, $level = 0, & $params, $open = null)
{
	/** @var JParameter $params */
	global $Itemid;
	$txt = '';
	//needed to break reference to prevent altering the actual menu item
	$mitem = clone($mitem);
	// Menu Link is a special type that is a link to another item
	if($mitem->type == 'menulink')
	{
		/** @var JMenuSite $menu */
		$menu = &JSite::getMenu();
		if($tmp = $menu->getItem($mitem->query['Itemid']))
		{
			$name = $mitem->name;
			$mid = $mitem->id;
			$parent = $mitem->parent;
			$mitem = clone($tmp);
			$mitem->name = $name;
			$mitem->mid = $mid;
			$mitem->parent = $parent;
		}
		else
		{
			return;
		}
	}

	switch($mitem->type)
	{
		case 'separator' :
			$mitem->browserNav = 3;
			break;

		case 'url' :
			if(eregi('index.php\?', $mitem->link))
			{
				if(!eregi('Itemid=', $mitem->link))
				{
					$mitem->link .= '&amp;Itemid='.$mitem->id;
				}
			}
			break;

		default :
			$mitem->link = 'index.php?Itemid='.$mitem->id;
			break;
	}

	// Active Menu highlighting
	$current_itemid = intval($Itemid);
	if(!$current_itemid)
	{
		$id = '';
	}
	else
	{
		if($current_itemid == $mitem->id)
		{
			$id = 'id="active_menu'.$params->get('class_sfx').'"';
		}
		else
		{
			if($params->get('activate_parent') && isset ($open) && in_array($mitem->id, $open))
			{
				$id = 'id="active_menu'.$params->get('class_sfx').'"';
			}
			else
			{
				if($mitem->type == 'url' && ItemidContained_pda($mitem->link, $current_itemid))
				{
					$id = 'id="active_menu'.$params->get('class_sfx').'"';
				}
				else
				{
					$id = '';
				}
			}
		}
	}

	if($params->get('full_active_id'))
	{
		// support for `active_menu` of 'Link - Url' if link is relative
		if($id == '' && $mitem->type == 'url' && strpos($mitem->link, 'http') === false)
		{
			$url = array ();
			if(strpos($mitem->link, '&amp;') !== false)
			{
				$mitem->link = str_replace('&amp;', '&', $mitem->link);
			}

			parse_str($mitem->link, $url);
			if(isset ($url['Itemid']))
			{
				if($url['Itemid'] == $current_itemid)
				{
					$id = 'id="active_menu'.$params->get('class_sfx').'"';
				}
			}
		}
	}

	// replace & with amp; for xhtml compliance
	$menu_params = new stdClass();
	$menu_params = new JParameter($mitem->params);
	$menu_secure = $menu_params->def('secure', 0);

	if(strcasecmp(substr($mitem->link, 0, 4), 'http'))
	{
		$mitem->url = JRoute::_($mitem->link, true, $menu_secure);
	}
	else
	{
		$mitem->url = $mitem->link;
	}

	$menuclass = 'mainlevel'.$params->get('class_sfx');
	if($level > 0)
	{
		$menuclass = 'sublevel'.$params->get('class_sfx');
	}

	// replace & with amp; for xhtml compliance
	// remove slashes from excaped characters
	$mitem->name = stripslashes(htmlspecialchars($mitem->name));

	switch($mitem->browserNav)
	{
		case 3 :
			// don't link it
			$txt = '<span class="'.$menuclass.'" '.$id.'>'.$mitem->name.'</span>';
			break;

		default : // formerly case 2
			// open in parent window
			$accesskey = '';
			if($params->get('accesskey'))
			{
				$num = 0;
				if(isset($GLOBALS['mod_pda_menu_accesskey']))
					$num = $GLOBALS['mod_pda_menu_accesskey'];
				$num = $num+1;
				if($num <= 10)
				{
					$accesskey = 'accesskey="'.($num%10).'" ';
					$GLOBALS['mod_pda_menu_accesskey'] = $num;
				}
			}
			$txt = '<a '.$accesskey.'href="'.$mitem->url.'" class="'.$menuclass.'" '.$id.'>'.$mitem->name.'</a>';
			break;
	}

	if($params->get('menu_images'))
	{
		$menu_params = new stdClass();
		$menu_params = new JParameter($mitem->params);

		$menu_image = $menu_params->def('menu_image', -1);
		if(($menu_image <> '-1') && $menu_image)
		{
			$image = '<img src="'.JURI::base(true).'/images/stories/'.$menu_image.'" border="0" alt="'.$mitem->name.'"/>';
			if($params->get('menu_images_align'))
			{
				$txt = $txt.' '.$image;
			}
			else
			{
				$txt = $image.' '.$txt;
			}
		}
	}

	return $txt;
}

/**
 * Draws a horizontal 'flat' style menu (very simple case)
 */
function mosShowPDAMenu(&$params)
{
	/** @var JParameter $params */
	/** @var JMenuSite $menu */
	$menu = & JSite::getMenu();
	/** @var JUser $user */
	$user = & JFactory::getUser();
	$activemenu =& $menu->getActive();
	$activeId = $activemenu->id;

	//get menu items
	$rows = $menu->getItems('menutype', $params->get('menutype'));

	$exclude_menu_ids = explode(',', $params->get('excludemenu'));
	$links = array ();
	$sublinks = array ();

	if(is_array($rows) && count($rows))
	{
		foreach($rows as $row)
		{
			if($activeId == $row->parent && !($exclude_menu_ids && in_array($row->id, $exclude_menu_ids)))
				$sublinks[] = mosGetMenuLink_pda($row, 0, $params);

			if($row->parent != '0')
				continue;

			if($row->access <= $user->get('aid', 0))
			{
				if($exclude_menu_ids && in_array($row->id, $exclude_menu_ids))
				{
					//dont add
				}
				else
				{
					$links[] = mosGetMenuLink_pda($row, 0, $params);
				}
			}
		}
	}

	$menuclass = 'mainlevel'.$params->get('class_sfx');

	if(count($links))
	{
		echo '<ul id="'.$menuclass.'">';
		foreach($links as $link)
		{
			echo '<li>'.$link.'</li>';
		}
		echo '</ul>';
	}

	if(count($sublinks))
	{
		echo '<ul id="'.$menuclass.'">';
		foreach($sublinks as $sublink)
		{
			echo '<li>'.$sublink.'</li>';
		}
		echo '</ul>';
	}
}

/**
 * Search for Itemid in link
 */
function ItemidContained_pda($link, $Itemid)
{
	$link = str_replace('&amp;', '&', $link);
	$temp = explode("&", $link);
	$linkItemid = "";
	foreach($temp as $value)
	{
		$temp2 = explode("=", $value);
		if($temp2[0] == "Itemid")
		{
			$linkItemid = $temp2[1];
			break;
		}
	}
	if($linkItemid != "" && $linkItemid == $Itemid)
	{
		return true;
	}
	else
	{
		return false;
	}
}
