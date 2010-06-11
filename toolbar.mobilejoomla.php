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

require_once(JApplicationHelper::getPath('toolbar_html'));

switch($task)
{
	case 'about':
		TOOLBAR_mobilejoomla::_ABOUT();
		break;
	case 'extensions':
		TOOLBAR_mobilejoomla::_EXT();
		break;
	case 'settings':
	default:
		TOOLBAR_mobilejoomla::_DEFAULT();
		break;
}