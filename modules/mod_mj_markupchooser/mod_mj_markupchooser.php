<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$markup=false;

if(defined('_MJ'))
{
	$MobileJoomla  =& MobileJoomla::getInstance();
	$markup        = $MobileJoomla->getMarkup();
}

global $mainframe;
$saved_markup = $mainframe->getUserState('mobilejoomla.markup',false);

switch ($saved_markup)
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

include(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php');

$uri                = JFactory::getURI ();
$uri->delVar ('naked');
$return             = base64_encode ($uri->toString());
$show_chosen_markup = $params->get('show_choosen', 0);
$show_sep           = false;

echo $params->get('show_text', ' ');

if ($params->get ('auto_show', 0))
{
    $show_sep = true;

    echo '<a href="'.JURI::base().'index.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=-&amp;return='. $return .'">' . $params->get ('auto_text', 'Auto') . '</a>';
}

if ($params->get ('mobile_show', 1) && ($show_chosen_markup || ($markup != 'xhtml' && $markup != 'iphone' && $markup != 'wml' && $markup != 'imode')))
{
    if ($show_sep)
        echo ' | ';

    $show_sep = true;

    if ($show_chosen_markup && ($markup == 'mobile' || $markup == 'xhtml' || $markup == 'iphone' || $markup == 'wml' || $markup == 'imode'))
        echo '<span>' . $params->get ('mobile_text', 'Mobile') . '</span>';
    else
        echo '<a href="'.JURI::base().'index.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=mobile&amp;return='. $return .'">' . $params->get ('mobile_text', 'Mobile') . '</a>';
}

if ($params->get ('web_show', 1) && ($show_chosen_markup || $markup != ''))
{
    if ($show_sep)
        echo ' | ';

    $show_sep = true;

    if ($show_chosen_markup && $markup == '')
        echo '<span>' . $params->get ('web_text', 'Standard') . '</span>';
    else
        echo '<a href="'.JURI::base().'index.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=&amp;return='. $return .'">' . $params->get ('web_text', 'Standard') . '</a>';
}

if ($params->get ('xhtml_show', 0) && ($show_chosen_markup || $markup != 'xhtml'))
{
    if ($show_sep)
        echo ' | ';

    $show_sep = true;

    if ($show_chosen_markup && $markup == 'xhtml')
        echo '<span>' . $params->get ('xhtml_text', 'Smartphone') . '</span>';
    else
        echo '<a href="'.JURI::base().'index.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=xhtml&amp;return='. $return .'">' . $params->get ('xhtml_text', 'Smartphone') . '</a>';
}

if ($params->get ('iphone_show', 0))
{
    if ($show_sep)
        echo ' | ';

    $show_sep = true;

    if ($show_chosen_markup && $markup == 'iphone')
        echo '<span>' . $params->get ('iphone_text', 'iPhone') . '</span>';
    else
        echo '<a href="'.JURI::base().'index.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=iphone&amp;return='. $return .'">' . $params->get ('iphone_text', 'iPhone') . '</a>';
}

if ($params->get ('wml_show', 0))
{
    if ($show_sep)
        echo ' | ';

    $show_sep = true;

    if ($show_chosen_markup && $markup == 'wml')
        echo '<span>' . $params->get ('wml_text', 'WAP') . '</span>';
    else
        echo '<a href="'.JURI::base().'index.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=wml&amp;return='. $return .'">' . $params->get ('wml_text', 'WAP') . '</a>';
}

if ($params->get ('imode_show', 0))
{
    if ($show_sep)
        echo ' | ';

    $show_sep = true;

    if ($show_chosen_markup && $markup == 'imode')
        echo '<span>' . $params->get ('imode_text', 'iMode') . '</span>';
    else
        echo '<a href="'.JURI::base().'index.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=imode&amp;return='. $return .'">' . $params->get ('imode_text', 'iMode') . '</a>';
}