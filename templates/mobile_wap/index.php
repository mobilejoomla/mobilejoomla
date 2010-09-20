<?php
/**
 * Mobile Joomla!
 * http://www.mobilejoomla.com
 *
 * @version		0.9.8
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	(C) 2008-2010 MobileJoomla!
 * @date		September 2010
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
