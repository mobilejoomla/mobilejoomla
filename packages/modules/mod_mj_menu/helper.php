<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version		###VERSION###
 * @license		###LICENSE###
 * @copyright	###COPYRIGHT###
 * @date		###DATE###
 */
defined('_JEXEC') or die('Restricted access');

class JMobileMenuHelper
{
	function _isJoomla15()
	{
		static $is_joomla15;
		if(!isset($is_joomla15))
			$is_joomla15 = substr(JVERSION,0,3) == '1.5';
		return $is_joomla15;
	}

	function getItems($attributes, $values)
	{
		$is_joomla15 = JMobileMenuHelper::_isJoomla15();
		/** @var JMenuSite $menu */
		$menu =& JSite::getMenu();
		if($is_joomla15)
		{
			$attribute = array_shift($attributes);
			$value = array_shift($values);
			$items = $menu->getItems($attribute, $value);
			if(!is_array($items))
				$items = array();
			foreach($items as $key=>$item)
				for($i=0, $count=count($attributes); $i<$count; $i++)
					if($item->$attributes[$i] != $values[$i])
					{
						unset($items[$key]);
						break;
					}
		}
		else
		{
			$items = $menu->getItems($attributes, $values);
			if(!is_array($items))
				$items = array();
		}
		return $items;
	}

	function getRoot($menutype)
	{
		$is_joomla15 = JMobileMenuHelper::_isJoomla15();
		if($is_joomla15)
			return JMobileMenuHelper::getItems(array('menutype', 'parent'),
											   array($menutype, 0));
		else
			return JMobileMenuHelper::getItems(array('menutype', 'parent_id'),
											   array($menutype, 1));
	}

	function getSiblings($item)
	{
		$is_joomla15 = JMobileMenuHelper::_isJoomla15();
		if($is_joomla15)
			return JMobileMenuHelper::getItems(array('menutype', 'parent'),
											   array($item->menutype, $item->parent));
		else
			return JMobileMenuHelper::getItems(array('menutype', 'parent_id'),
											   array($item->menutype, $item->parent_id));
	}

	function getChildrens($item)
	{
		$is_joomla15 = JMobileMenuHelper::_isJoomla15();
		return JMobileMenuHelper::getItems(array('menutype', $is_joomla15 ? 'parent' : 'parent_id'),
										   array($item->menutype, $item->id));
	}

	function prepareMenu(&$menu, $exclude_menu_ids, $params)
	{
		$MobileJoomla =& MobileJoomla::getInstance();
		$is_joomla15 = JMobileMenuHelper::_isJoomla15();

		/** @var JUser $user */
		$user = & JFactory::getUser();
		$aid = $user->get('aid', 0);

		/** @var JMenuSite $sitemenu */
		$sitemenu =& JSite::getMenu();
		$router =& JSite::getRouter();

		foreach($menu as $i=>$item)
			$menu[$i] = clone($item);

		foreach($menu as $i=>$item)
		{
			$allow = $is_joomla15 ? $sitemenu->authorize($item->id, $aid) : $sitemenu->authorise($item->id);
			if(!$allow || in_array($item->id, $exclude_menu_ids))
			{
				unset($menu[$i]);
				continue;
			}

			$item->title = htmlspecialchars($is_joomla15 ? $item->name : $item->title);

			if(!$is_joomla15)
			{
				$item->anchor_css = htmlspecialchars($item->params->get('menu-anchor_css', ''));
				$item->menu_image = htmlspecialchars($item->params->get('menu_image', ''));
			}
			else
			{
				$item->anchor_css = '';
				if(!is_object($item->params))
					$item->params = new JParameter($item->params);
				$menu_image = $item->params->get('menu_image');
				$item->menu_image = $menu_image ? JURI::base(true).'/images/stories/'.$menu_image : '';
			}

			$item->flink = $item->link;
			switch($item->type)
			{
			case 'separator':
				continue;
			case 'url':
				if((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false))
					$item->flink .= '&Itemid='.$item->id;
				break;
			case 'alias':
				$item->flink = 'index.php?Itemid='.$item->params->get('aliasoptions');
				break;
			default:
				if($router->getMode() == JROUTER_MODE_SEF)
					$item->flink = 'index.php?Itemid='.$item->id;
				else
					$item->flink .= '&Itemid='.$item->id;
			}
			if(strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false))
				$item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
			else
				$item->flink = JRoute::_($item->flink);

			$item->accesskey = '';
			if($params->get('accesskey'))
			{
				$key = $MobileJoomla->getAccessKey();
				if($key!==false)
					$item->accesskey = ' accesskey="'.$key.'"';
			}
		}
	}

	function _renderMenu($menu, &$params, $submenu = array())
	{
		/** @var MobileJoomla $MobileJoomla */
		$MobileJoomla =& MobileJoomla::getInstance();
		$markup = $MobileJoomla->getMarkup();
		switch($markup)
		{
		case 'wml':
		case 'chtml':
		case 'xhtml':
		case 'iphone':
			break;
		default:
			$markup = 'xhtml';
		}

		/** @var JMenuSite $sitemenu */
		$sitemenu =& JSite::getMenu();
		$active	= $sitemenu->getActive();
		$active_id = isset($active) ? $active->id : 0;

		$is_vertical = $params->get('layout')=='v';
		$is_submenu = $params->get('class_prefix')=='submenu';

		require(JModuleHelper::getLayoutPath('mod_mj_menu', $markup));
	}

	function renderMenu($menu, &$params, $submenu = array())
	{
		$prev = $params->get('class_prefix');
		$params->set('class_prefix', 'menu');
		JMobileMenuHelper::_renderMenu($menu, $params, $submenu);
		$params->set('class_prefix', $prev);
	}

	function renderSubmenu($submenu, &$params)
	{
		$prev = $params->get('class_prefix');
		$params->set('class_prefix', 'submenu');
		JMobileMenuHelper::_renderMenu($submenu, $params);
		$params->set('class_prefix', $prev);
	}
}