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

$markup = false;

if(!defined('_MJ'))
{
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


//dont display for desktop user displayin desktop page
//forgedMarkup means user wanted to see some other version (like mobile wants desktop version)
$forgedMarkup = $mainframe->getUserState('mobilejoomla.forged_markup', false);
$desktopUserDesktopPage = ('yes' != $forgedMarkup) && ($markup == '');
if(!$desktopUserDesktopPage)
{
	/** @var JURI $uri */
	$uri = JFactory::getURI();
	$uri->delVar('naked');
	$parse = parse_url($base);
	$return = base64_encode($parse['scheme'].'://'.$parse['host'].$uri->toString(array ('path', 'query')));
	$show_chosen_markup = $params->get('show_choosen', 1);

	echo $params->get('show_text', ' ');

	$links = array ();

	if($params->get('auto_show', 0))
	{
		$text = $params->get('auto_text', 'Automatic Version');
		$links[] = '<a class="markupchooser" href="'.$base.'index2.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=-&amp;return='.$return.'">'.$text.'</a>';
	}

	if($params->get('mobile_show', 1) && ($show_chosen_markup || ($markup != 'xhtml' && $markup != 'iphone' && $markup != 'wml' && $markup != 'imode')))
	{
		$text = $params->get('mobile_text', 'Mobile Version');
		if($markup == 'mobile' || $markup == 'xhtml' || $markup == 'iphone' || $markup == 'wml' || $markup == 'imode')
		{
			if($show_chosen_markup) $links[] = '<span class="markupchooser">'.$text.'</span>';
		}
		else
			$links[] = '<a class="markupchooser" href="'.$base.'index2.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=mobile&amp;return='.$return.'">'.$text.'</a>';
	}

	if($params->get('web_show', 1) && ($show_chosen_markup || $markup != ''))
	{
		$text = $params->get('web_text', 'Standard Version');
		if($markup == '')
		{
			if($show_chosen_markup) $links[] = '<span class="markupchooser">'.$text.'</span>';
		}
		else
			$links[] = '<a class="markupchooser" href="'.$base.'index2.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=&amp;return='.$return.'">'.$text.'</a>';
	}

	if($params->get('xhtml_show', 0) && ($show_chosen_markup || $markup != 'xhtml'))
	{
		$text = $params->get('xhtml_text', 'Smartphone Version');
		if($markup == 'xhtml')
		{
			if($show_chosen_markup) $links[] = '<span class="markupchooser">'.$text.'</span>';
		}
		else
			$links[] = '<a class="markupchooser" href="'.$base.'index2.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=xhtml&amp;return='.$return.'">'.$text.'</a>';
	}

	if($params->get('iphone_show', 0))
	{
		$text = $params->get('iphone_text', 'iPhone Version');
		if($markup == 'iphone')
		{
			if($show_chosen_markup) $links[] = '<span class="markupchooser">'.$text.'</span>';
		}
		else
			$links[] = '<a class="markupchooser" href="'.$base.'index2.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=iphone&amp;return='.$return.'">'.$text.'</a>';
	}

	if($params->get('wml_show', 0))
	{
		$text = $params->get('wml_text', 'WAP Version');
		if($markup == 'wml')
		{
			if($show_chosen_markup) $links[] = '<span class="markupchooser">'.$text.'</span>';
		}
		else
			$links[] = '<a class="markupchooser" href="'.$base.'index2.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=wml&amp;return='.$return.'">'.$text.'</a>';
	}

	if($params->get('imode_show', 0))
	{
		$text = $params->get('imode_text', 'iMode Version');
		if($markup == 'imode')
		{
			if($show_chosen_markup) $links[] = '<span class="markupchooser">'.$text.'</span>';
		}
		else
			$links[] = '<a class="markupchooser" href="'.$base.'index2.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=imode&amp;return='.$return.'">'.$text.'</a>';
	}

	echo implode('<span class="markupchooser"> | </span>', $links);
}
