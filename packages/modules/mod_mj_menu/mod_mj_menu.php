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

require_once(dirname(__FILE__).DS.'helper.php'); 

/** @var JParameter $params */
$params->def('menutype', 'mainmenu');
$params->def('layout', 'h');
$params->def('type', 'submenu');
$params->def('class_sfx', '');
$params->def('excludemenu', '');
$params->def('format', 0);

/** @var JMenuSite $sitemenu */
$sitemenu =& JSite::getMenu();
$active	= $sitemenu->getActive();

if(isset($active) && $active->menutype==$params->get('menutype'))
{
	$rows = (array)JMobileMenuHelper::getSiblings($active);
	$subrows = (array)JMobileMenuHelper::getChildrens($active);
}
else
{
	$rows = (array)JMobileMenuHelper::getRoot($params->get('menutype'));
	$subrows = array();
}

if($params->get('type')=='submenu')
{
	$MobileJoomla =& MobileJoomla::getInstance();
	if(!$MobileJoomla->_ishomepage)
		$rows = $subrows;
	$subrows = array();
}

$exclude_menu_ids = explode(',', $params->get('excludemenu'));
JMobileMenuHelper::prepareMenu(&$rows, $exclude_menu_ids);
JMobileMenuHelper::prepareMenu(&$subrows, $exclude_menu_ids);

$params->set('has_submenu', count($subrows)?1:0);

if($params->get('layout')=='v')
{
	JMobileMenuHelper::renderMenu($rows, $params, $subrows);
}
else
{
	JMobileMenuHelper::renderMenu($rows, $params);
	if(count($subrows))
		JMobileMenuHelper::renderSubmenu($subrows, $params);
}

