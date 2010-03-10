<?php
/**
 * Kuneri Mobile Joomla! for Joomla!1.5
 * http://www.mobilejoomla.com/
 *
 * @version		0.9.0
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	Copyright (C) 2008-2009 Kuneri Ltd. All rights reserved.
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

defined( '_MJ' ) or die( 'Incorrect using of Kuneri Mobile Joomla.' );

$MobileJoomla =& MobileJoomla::getInstance();

$modulepos=$MobileJoomla->getPosition('header');
$MobileJoomla->loadModules($modulepos);

$MobileJoomla->showPathway();

$modulepos=$MobileJoomla->getPosition('middle');
$MobileJoomla->loadModules($modulepos);

$MobileJoomla->showMainBody();

$modulepos=$MobileJoomla->getPosition('footer');
$MobileJoomla->loadModules($modulepos);

$MobileJoomla->showFooter();

$modulepos=$MobileJoomla->getPosition('cards');
$MobileJoomla->loadModules($modulepos);
