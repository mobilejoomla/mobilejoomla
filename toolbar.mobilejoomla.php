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
	case 'settings':
	default:
		TOOLBAR_mobilejoomla::_DEFAULT();
		break;
}