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

defined('_MJ') or die('Incorrect usage of Mobile Joomla.');

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
