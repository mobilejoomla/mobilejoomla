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

class modMarkupChooserHelper
{
	var $base = '';
	var $return = '';
	var $show_chosen_markup = true;

	function getChangeLink($user_markup, $test_markup, $text)
	{
		if($user_markup == $test_markup)
			return modMarkupChooserHelper::$show_chosen_markup?'':false;
		else
			return modMarkupChooserHelper::$base.'index2.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup='.$test_markup.'&amp;return='.modMarkupChooserHelper::$return;
	}
}
