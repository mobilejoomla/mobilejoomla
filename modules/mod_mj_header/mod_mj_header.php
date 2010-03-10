<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version		###VERSION###
 * @license		###LICENSE###
 * @copyright	###COPYRIGHT###
 * @date        ###DATE###
 */

defined ('_JEXEC') or die ('Restricted access');

// Include the syndicate functions only once
require_once (dirname (__FILE__).DS.'helper.php');

$showTitle  = $params->get ('show_title', 1);
$imgURL     = $params->get ('img_url', '{{root}}templates/mobile_pda/resources/images/mw_joomla_logo.png');
$siteTitle  = $params->get ('site_title', '{{sitename}}');
$pageTitle  = $params->get ('page_title', '');
$cutTitle   = $params->get ('cut_title', 1);

$imgURL     = str_replace ('{{root}}', JURI::base (), $imgURL);

$siteTitle  = str_ireplace ('{{sitename}}', JFactory::getApplication ()->getCfg ('sitename'), $siteTitle);
$siteTitle  = str_ireplace ('{{pagetitle}}', JFactory::getDocument ()->getTitle (), $siteTitle);

$pageTitle  = str_ireplace ('{{sitename}}', JFactory::getApplication ()->getCfg ('sitename'), $pageTitle);
$pageTitle  = str_ireplace ('{{pagetitle}}', JFactory::getDocument ()->getTitle (), $pageTitle);

if ($cutTitle)
{
    if (strlen($siteTitle) > 35)
        $siteTitle = substr ($siteTitle, 0, 34) . '...';
        
    if (strlen($pageTitle) > 39)
        $siteTitle = substr ($siteTitle, 0, 37) . '...';
}


require (JModuleHelper::getLayoutPath ('mod_mj_header'));
