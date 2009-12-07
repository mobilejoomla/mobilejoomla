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

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch ( $task ) {
	case 'about':
		TOOLBAR_mobilejoomla::_ABOUT();
		break;
	case 'wurfl':
		TOOLBAR_mobilejoomla::_WURFL();
		break;
	case 'update':
		break;
	case 'settings':
	default:
		TOOLBAR_mobilejoomla::_DEFAULT();
		break;
}
?>