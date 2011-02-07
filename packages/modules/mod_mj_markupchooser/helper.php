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
        
	var $base;
	var $return;

	var $show_chosen_markup;

        
        function modMarkupChooserHelper($base, $return, $show_chosen_markup) {
                $this->base = $base;     
                $this->return = $return;
                $this->show_chosen_markup = $show_chosen_markup;
        }

        /**
         * No idea what this chould do, but it was definitely broken by how it used static variables and did not run on PHP 5.3 -mikko
         */
	function getChangeLink($user_markup, $test_markup, $text)
	{
		if($user_markup == $test_markup)
			return $this->show_chosen_markup?'':false;
		else
			return $this->base.'index2.php?option=com_mobilejoomla&amp;task=setmarkup&amp;markup='.$test_markup.'&amp;return='.$this->return;
	}
}
