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

/** @var $params JRegistry */

// check that MJ plugin was loaded
if(!class_exists('MobileJoomla'))
	return;

if(!defined('_MJ'))
{
	if($params->get('hide_on_pc', 1))
	{
		$MobileJoomla_Device =& MobileJoomla::getDevice();
		if(empty($MobileJoomla_Device['real_markup']))
			return;
	}
	$markup = '';
}
else
{
	$MobileJoomla = MobileJoomla::getInstance();
	$markup = $MobileJoomla->getMarkup();
}

$show_chosen_markup = $params->get('show_choosen', 1);

$links = array();

if($params->get('auto_show', 0))
{
	$chosen = MobileJoomla::isCurrentMarkup('auto');
	if($show_chosen_markup || !$chosen)
	{
		$text = $params->get('auto_text', 'Automatic Version');
		$link = $chosen ? false : MobileJoomla::getDeviceViewURI('auto');
		$links[] = array('url'=>$link, 'text'=>$text);
	}
}

if($params->get('mobile_show', 1))
{
	$chosen = MobileJoomla::isCurrentMarkup('mobile');
	if($show_chosen_markup || !$chosen)
	{
		$text = $params->get('mobile_text', 'Mobile Version');
		$link = $chosen ? false : MobileJoomla::getDeviceViewURI('mobile');
		$links[] = array('url'=>$link, 'text'=>$text);
	}
}

if($params->get('web_show', 1))
{
	$chosen = MobileJoomla::isCurrentMarkup('desktop');
	if($show_chosen_markup || !$chosen)
	{
		$text = $params->get('web_text', 'Standard Version');
		$link = $chosen ? false : MobileJoomla::getDeviceViewURI('desktop');
		$links[] = array('url'=>$link, 'text'=>$text);
	}
}

if($params->get('xhtml_show', 0))
{
	$chosen = MobileJoomla::isCurrentMarkup('xhtml');
	if($show_chosen_markup || !$chosen)
	{
		$text = $params->get('xhtml_text', 'Smartphone Version');
		$link = $chosen ? false : MobileJoomla::getDeviceViewURI('xhtml');
		$links[] = array('url'=>$link, 'text'=>$text);
	}
}

if($params->get('iphone_show', 0))
{
	$chosen = MobileJoomla::isCurrentMarkup('iphone');
	if($show_chosen_markup || !$chosen)
	{
		$text = $params->get('iphone_text', 'iPhone Version');
		$link = $chosen ? false : MobileJoomla::getDeviceViewURI('iphone');
		$links[] = array('url'=>$link, 'text'=>$text);
	}
}

if($params->get('wml_show', 0))
{
	$chosen = MobileJoomla::isCurrentMarkup('wml');
	if($show_chosen_markup || !$chosen)
	{
		$text = $params->get('wml_text', 'WAP Version');
		$link = $chosen ? false : MobileJoomla::getDeviceViewURI('wml');
		$links[] = array('url'=>$link, 'text'=>$text);
	}
}

if($params->get('chtml_show', 0))
{
	$chosen = MobileJoomla::isCurrentMarkup('chtml');
	if($show_chosen_markup || !$chosen)
	{
		$text = $params->get('chtml_text', 'iMode Version');
		$link = $chosen ? false : MobileJoomla::getDeviceViewURI('chtml');
		$links[] = array('url'=>$link, 'text'=>$text);
	}
}

$layout_file = JModuleHelper::getLayoutPath('mod_mj_markupchooser', $markup?$markup:'default');
if(!is_file($layout_file))
	$layout_file = JModuleHelper::getLayoutPath('mod_mj_markupchooser', 'xhtml');
require($layout_file);
