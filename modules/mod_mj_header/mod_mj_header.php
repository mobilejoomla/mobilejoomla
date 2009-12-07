<?php
/**
 * @package			Mobile Joomla!
 * @subpackage      Modules
 * @author          Jarkko Pesonen <jarkko@kuneri.net>
 * @copyright		Kuneri.net
 */

defined ('_JEXEC') or die ('Restricted access');

// Include the syndicate functions only once
require_once (dirname (__FILE__).DS.'helper.php');

$showTitle  = $params->get ('show_title', 1);
$imgURL     = $params->get ('img_url', '{{root}}templates/mobile_pda/resources/images/mw_joomla_logo.png');
$siteTitle  = $params->get ('site_title', '{{sitename}}');
$pageTitle  = $params->get ('page_title', '{{pagetitle}}');

$imgURL     = str_replace ('{{root}}', JURI::base (), $imgURL);

$siteTitle  = str_ireplace ('{{sitename}}', JFactory::getApplication ()->getCfg ('sitename'), $siteTitle);
$siteTitle  = str_ireplace ('{{pagetitle}}', JFactory::getDocument ()->getTitle (), $siteTitle);

$pageTitle  = str_ireplace ('{{sitename}}', JFactory::getApplication ()->getCfg ('sitename'), $pageTitle);
$pageTitle  = str_ireplace ('{{pagetitle}}', JFactory::getDocument ()->getTitle (), $pageTitle);


require (JModuleHelper::getLayoutPath ('mod_mj_header'));
