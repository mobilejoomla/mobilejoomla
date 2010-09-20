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

/** @var JParameter $params */

$params->def('menutype', 'mainmenu');

require_once(dirname(__FILE__).DS.'legacy.php');

require(JModuleHelper::getLayoutPath('mod_mj_wap_menu'));