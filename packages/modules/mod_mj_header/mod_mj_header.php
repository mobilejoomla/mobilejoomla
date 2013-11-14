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
defined('_JEXEC') or die ('Restricted access');

/** @var $params JRegistry */

// Include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');

$showTitle = $params->get('show_title', 1);
$imgURL = $params->get('img_url', '{{root}}templates/mobile_smartphone/resources/images/mw_joomla_logo.png');
$siteTitle = $params->get('site_title', '{{sitename}}');
$pageTitle = $params->get('page_title', '');
$cutTitle = $params->get('cut_title', 1);

$imgURL = JString::str_ireplace('{{root}}', JUri::base(), $imgURL);

$app = JFactory::getApplication();
$doc = JFactory::getDocument();

$sitename = $app->getCfg('sitename');
if(substr(JVERSION,0,3) == '1.5')
	$sitename = htmlspecialchars_decode($sitename);

$siteTitle = JString::str_ireplace('{{sitename}}', $sitename, $siteTitle);
$siteTitle = JString::str_ireplace('{{pagetitle}}', $doc->getTitle(), $siteTitle);

$pageTitle = JString::str_ireplace('{{sitename}}', $sitename, $pageTitle);
$pageTitle = JString::str_ireplace('{{pagetitle}}', $doc->getTitle(), $pageTitle);

if($cutTitle)
{
	if(JString::strlen($siteTitle) > 35)
		$siteTitle = JString::substr($siteTitle, 0, 34).'...';

	if(JString::strlen($pageTitle) > 39)
		$siteTitle = JString::substr($siteTitle, 0, 37).'...';
}


require (JModuleHelper::getLayoutPath('mod_mj_header'));
