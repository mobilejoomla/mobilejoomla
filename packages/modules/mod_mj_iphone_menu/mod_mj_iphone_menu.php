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
$params->def('class_sfx', '');
$params->def('menu_images', 0);
$params->def('menu_images_align', 0);
$params->def('activate_parent', 0);
$params->def('full_active_id', 0);

require_once(dirname(__FILE__).DS.'legacy.php');

require(JModuleHelper::getLayoutPath('mod_mj_iphone_menu'));