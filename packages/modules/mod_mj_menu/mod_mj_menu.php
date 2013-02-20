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

if(!defined('_MJ'))
	return;

require_once(dirname(__FILE__).'/helper.php'); 

$MobileJoomla = MobileJoomla::getInstance();

/** @var $params JRegistry */
$params->def('menutype', 'mainmenu');
$params->def('layout', '');
$params->def('type', 'submenu');
$params->def('class_sfx', '');
$params->def('excludemenu', '');
$params->def('format', 0);
$params->def('accesskey', 1);

if($params->get('layout')=='')
{
	if($MobileJoomla->getMarkup() == 'iphone')
		$params->set('layout', 'v');
	else
		$params->set('layout', 'h');
}

$menutype = $params->get('menutype');
$active = JMobileMenuHelper::getActive($menutype);

if(isset($active) && $active->menutype==$menutype)
{
	$rows = (array)JMobileMenuHelper::getSiblings($active);
	$subrows = (array)JMobileMenuHelper::getChildrens($active);
	if($params->get('type')=='submenu')
	{
		$app = JFactory::getApplication();
		$sitemenu = $app->getMenu();
		$home = $sitemenu->getDefault();
		if($active->id != $home->id)
			$rows = $subrows;
		$subrows = array();
	}
}
else
{
	$rows = (array)JMobileMenuHelper::getRoot($menutype);
	$subrows = array();
}

$exclude_menu_ids = explode(',', $params->get('excludemenu'));
JMobileMenuHelper::prepareMenu($rows, $exclude_menu_ids, $params);
JMobileMenuHelper::prepareMenu($subrows, $exclude_menu_ids, $params);

if(count($rows)==0)
	return;

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
