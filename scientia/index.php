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

define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))) );
require_once( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once( JPATH_BASE .DS.'includes'.DS.'framework.php' );

$app = JFactory::getApplication('administrator');
$user = JFactory::getUser();

$lang =& JFactory::getLanguage();
$lang->load('com_mobilejoomla');

global $isJoomla15;
$isJoomla15 = (substr(JVERSION,0,3) == '1.5');

if($isJoomla15)
{
	if(!$user->authorize('login', 'administrator'))
		exit(0);
}
else
{
	if(!$user->authorise('core.login.admin'))
		exit(0);
}

global $mootools;
$mootools = '../../../../media/system/js/' . ($isJoomla15 ? 'mootools.js' : 'mootools-core.js');

$action = JRequest::getCmd('action', 'init');
$file = dirname(__FILE__).DS.$action.'.php';
if(!file_exists($file))
	exit(0);

require($file);
