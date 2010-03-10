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

// no direct access
defined('_JEXEC') or die('Restricted access');

$markup=false;

if(!defined('_MJ'))
{
    include JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.class.php';
    $config =& MobileJoomla::getConfig ();
    $base = $config['desktop_url'];
}
else
{
    $MobileJoomla  =& MobileJoomla::getInstance();
    $markup        = $MobileJoomla->getMarkup();
    $base = $MobileJoomla->config['desktop_url'];
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




//dont display for desktop user displayin desktop page
//forgedMarkup means user wanted to see some other version (like mobile wants desktop version)
$forgedMarkup = $mainframe->getUserState('mobilejoomla.forged_markup',false);
$desktopUserDesktopPage = ('yes' != $forgedMarkup) && ($markup == '') ;
if( !$desktopUserDesktopPage )
{

 $uri                = JFactory::getURI ();
 $uri->delVar ('naked');
 $return             = base64_encode ($base . $uri->getQuery());
 $show_chosen_markup = $params->get('show_choosen', 1);
 $show_sep           = false;

 echo $params->get('show_text', ' ');

 if ($params->get ('auto_show', 0))
 {
     $show_sep = true;

     echo '<a  class="markupchooser" href="'.$base.'index.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=-&amp;return='. $return .'">' . $params->get ('auto_text', 'Automatic Version') . '</a>';
 }

 if ($params->get ('mobile_show', 1) && ($show_chosen_markup || ($markup != 'xhtml' && $markup != 'iphone' && $markup != 'wml' && $markup != 'imode')))
 {
     if ($show_sep)
         echo '<span class="markupchooser"> | </span>';

     $show_sep = true;

     if ($show_chosen_markup && ($markup == 'mobile' || $markup == 'xhtml' || $markup == 'iphone' || $markup == 'wml' || $markup == 'imode'))
         echo '<span class="markupchooser">' . $params->get ('mobile_text', 'Mobile Version') . '</span>';
     else
         echo '<a  class="markupchooser" href="'.$base.'index.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=mobile&amp;return='. $return .'">' . $params->get ('mobile_text', 'Mobile Version') . '</a>';
 }

 if ($params->get ('web_show', 1) && ($show_chosen_markup || $markup != ''))
 {
     if ($show_sep)
         echo '<span class="markupchooser"> | </span>';

     $show_sep = true;

     if ($show_chosen_markup && $markup == '')
         echo '<span class="markupchooser">' . $params->get ('web_text', 'Standard Version') . '</span>';
     else
         echo '<a  class="markupchooser" href="'.$base.'index.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=&amp;return='. $return .'">' . $params->get ('web_text', 'Standard Version') . '</a>';
 }

 if ($params->get ('xhtml_show', 0) && ($show_chosen_markup || $markup != 'xhtml'))
 {
     if ($show_sep)
         echo '<span class="markupchooser"> | </span>';

     $show_sep = true;

     if ($show_chosen_markup && $markup == 'xhtml')
         echo '<span class="markupchooser">' . $params->get ('xhtml_text', 'Smartphone Version') . '</span>';
     else
         echo '<a  class="markupchooser" href="'.$base.'index.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=xhtml&amp;return='. $return .'">' . $params->get ('xhtml_text', 'Smartphone Version') . '</a>';
 }

 if ($params->get ('iphone_show', 0))
 {
     if ($show_sep)
         echo '<span class="markupchooser"> | </span>';

     $show_sep = true;

     if ($show_chosen_markup && $markup == 'iphone')
         echo '<span class="markupchooser">' . $params->get ('iphone_text', 'iPhone Version') . '</span>';
     else
         echo '<a  class="markupchooser" href="'.$base.'index.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=iphone&amp;return='. $return .'">' . $params->get ('iphone_text', 'iPhone Version') . '</a>';
 }

 if ($params->get ('wml_show', 0))
 {
     if ($show_sep)
         echo '<span class="markupchooser"> | </span>';

     $show_sep = true;

     if ($show_chosen_markup && $markup == 'wml')
         echo '<span class="markupchooser">' . $params->get ('wml_text', 'WAP Version') . '</span>';
     else
         echo '<a  class="markupchooser" href="'.$base.'index.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=wml&amp;return='. $return .'">' . $params->get ('wml_text', 'WAP Version') . '</a>';
 }

 if ($params->get ('imode_show', 0))
 {
     if ($show_sep)
         echo '<span class="markupchooser"> | </span>';

     $show_sep = true;

     if ($show_chosen_markup && $markup == 'imode')
         echo '<span class="markupchooser">' . $params->get ('imode_text', 'iMode Version') . '</span>';
     else
         echo '<a  class="markupchooser" href="'.$base.'index.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup=imode&amp;return='. $return .'">' . $params->get ('imode_text', 'iMode Version') . '</a>';
 }
}
