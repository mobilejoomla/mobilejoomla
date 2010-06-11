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
function mosGetMenuLink_wap($mitem, $level = 0, & $params, $open = null)
{
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

	// replace & with amp; for xhtml compliance
	// remove slashes from excaped characters
	$mitem->name = stripslashes(htmlspecialchars($mitem->name));

	switch($mitem->browserNav)
	{
		case 3 :
			// don't link it
			$txt = $mitem->name;
			break;

		default : // formerly case 2
			// open in parent window
			$txt = '<a href="'.$mitem->url.'">'.$mitem->name.'</a>';
			break;
	}

	return $txt;
}

/**
 * Draws a horizontal 'flat' style menu (very simple case)
 */
function mosShowWAPMenu(&$params)
{
	/** @var JParameter $params */
	/** @var JMenuSite $menu */
	$menu = & JSite::getMenu();
	/** @var JUser $user */
	$user = & JFactory::getUser();
	$activeMenu = $menu->getActive();

	//get menu items
	$rows = $menu->getItems('menutype', $params->get('menutype'));

	$exclude_menu_ids = explode(',', $params->get('excludemenu'));
	$links = array ();
	if(is_array($rows) && count($rows))
	{
		foreach($rows as $row)
		{
			if($activeMenu->id == $row->parent && !($exclude_menu_ids && in_array($row->id, $exclude_menu_ids)))
				$sublinks[] = mosGetMenuLink_wap($row, 0, $params);

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
					$links[] = mosGetMenuLink_wap($row, 0, $params);
				}
			}
		}
	}

	if(count($links))
	{
		echo '<p>';
		foreach($links as $link)
		{
			echo $link."<br/>\n";
		}
		echo '</p>';
	}

	if(count($sublinks))
	{
		echo '<p>';
		echo '<b>'.$activeMenu->name."</b><br/>\n";
		foreach($sublinks as $sublink)
		{
			echo '- '.$sublink."<br/>\n";
		}
		echo '</p>';
	}
}