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
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

defined('_MJ') or die('Incorrect using of Kuneri Mobile Joomla.');

$MobileJoomla =& MobileJoomla::getInstance();

$modulepos = $MobileJoomla->getPosition('header');
$MobileJoomla->loadModules($modulepos);

$MobileJoomla->showPathway();

$modulepos = $MobileJoomla->getPosition('middle');
$MobileJoomla->loadModules($modulepos);

$MobileJoomla->showMainBody();

$modulepos = $MobileJoomla->getPosition('footer');
$MobileJoomla->loadModules($modulepos);

$MobileJoomla->showFooter();

$modulepos = $MobileJoomla->getPosition('cards');
$MobileJoomla->loadModules($modulepos);
