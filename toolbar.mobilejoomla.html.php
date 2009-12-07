<?php
/**
 * Kuneri Mobile Joomla! for Joomla!1.5
 * http://www.mobilejoomla.com/
 *
 * @version		0.9.0
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	Copyright (C) 2008-2009 Kuneri Ltd. All rights reserved.
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class TOOLBAR_mobilejoomla {
	function _DEFAULT() {
		JToolBarHelper::title( JText::_( 'Mobile Joomla Configuration' ), 'config.php' );
		JToolBarHelper::save();
		JToolBarHelper::cancel('cancel');
	}
	function _ABOUT() {
		JToolBarHelper::title( JText::_( 'About Mobile Joomla' ) );
		JToolBarHelper::cancel('cancel');
	}
	function _WURFL() {
		JToolBarHelper::title( JText::_( 'WURFL Settings' ) );
		JToolBarHelper::cancel('cancel');
	}
}
?>