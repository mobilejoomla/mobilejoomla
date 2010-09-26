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

require_once(dirname(__FILE__).DS.'helper.php');

if(!defined('_MJ'))
{
	$markup = '';
	include_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.class.php');
	$config =& MobileJoomla::getConfig();
	$base = $config['desktop_url'];
}
else
{
	/** @var MobileJoomla $MobileJoomla */
	$MobileJoomla =& MobileJoomla::getInstance();
	$markup = $MobileJoomla->getMarkup();
	$base = $MobileJoomla->config['desktop_url'];
}

/** @var JSite $mainframe */
global $mainframe;
$saved_markup = $mainframe->getUserState('mobilejoomla.markup', false);
switch($saved_markup)
{
	case '':
	case 'xhtml':
	case 'iphone':
	case 'mobile':
	case 'wml':
	case 'imode':
		break;
	default:
		$saved_markup = false;
}

/** @var JURI $uri */
$uri = clone(JFactory::getURI());
$uri->delVar('naked');
modMarkupChooserHelper::$base = $base;
modMarkupChooserHelper::$return = base64_encode($uri->toString(array('path', 'query')));
modMarkupChooserHelper::$show_chosen_markup = $params->get('show_choosen', 1);


$links = array();

if($params->get('auto_show', 0))
{
	$text = $params->get('auto_text', 'Automatic Version');
	$link = modMarkupChooserHelper::getChangeLink($saved_markup===false?'-':'', '-', $text);
	if($link!==false) $links[] = array('url'=>$link, 'text'=>$text);
}

if($params->get('mobile_show', 1))
{
	$text = $params->get('mobile_text', 'Mobile Version');
	$is_mobile_markup = $markup == 'xhtml' || $markup == 'iphone' ||
						$markup == 'wml' || $markup == 'imode' ||
						$saved_markup=='mobile';
	$link = modMarkupChooserHelper::getChangeLink($is_mobile_markup?'mobile':'', 'mobile', $text);
	if($link!==false) $links[] = array('url'=>$link, 'text'=>$text);
}

if($params->get('web_show', 1))
{
	$text = $params->get('web_text', 'Standard Version');
	$link = modMarkupChooserHelper::getChangeLink($markup, '', $text);
	if($link!==false) $links[] = array('url'=>$link, 'text'=>$text);
}

if($params->get('xhtml_show', 0))
{
	$text = $params->get('xhtml_text', 'Smartphone Version');
	$link = modMarkupChooserHelper::getChangeLink($markup, 'xhtml', $text);
	if($link!==false) $links[] = array('url'=>$link, 'text'=>$text);
}

if($params->get('iphone_show', 0))
{
	$text = $params->get('iphone_text', 'iPhone Version');
	$link = modMarkupChooserHelper::getChangeLink($markup, 'iphone', $text);
	if($link!==false) $links[] = array('url'=>$link, 'text'=>$text);
}

if($params->get('wml_show', 0))
{
	$text = $params->get('wml_text', 'WAP Version');
	$link = modMarkupChooserHelper::getChangeLink($markup, 'wml', $text);
	if($link!==false) $links[] = array('url'=>$link, 'text'=>$text);
}

if($params->get('imode_show', 0))
{
	$text = $params->get('imode_text', 'iMode Version');
	$link = modMarkupChooserHelper::getChangeLink($markup, 'imode', $text);
	if($link!==false) $links[] = array('url'=>$link, 'text'=>$text);
}

require(JModuleHelper::getLayoutPath('mod_mj_markupchooser', $markup?$markup:'default'));
