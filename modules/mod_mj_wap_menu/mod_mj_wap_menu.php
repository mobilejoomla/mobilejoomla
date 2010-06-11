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

/** @var JParameter $params */

$params->def('menutype', 'mainmenu');

require_once(dirname(__FILE__).DS.'legacy.php');

require(JModuleHelper::getLayoutPath('mod_mj_wap_menu'));