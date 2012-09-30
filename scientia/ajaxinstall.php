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

function _initStatus()
{
	JError::setErrorHandling(E_ERROR, 'Message');
	@set_time_limit(1200);
	@ini_set('max_execution_time', 1200);
}
function _sendStatus()
{
	$msg = array();
	/** @var JException $error */
	foreach(JError::getErrors() as $error)
		if($error->get('level'))
			$msg[] = $error->get('message');
	if(count($msg))
		$msg = '<p>'.implode('</p><p>', $msg).'</p>';
	else
		$msg = 'ok';
	echo $msg;
	jexit();
}

	jimport('joomla.plugin.helper');
	jimport('joomla.installer.helper');
	jimport('joomla.installer.installer');
	$app = JFactory::getApplication();

	_initStatus();
	$dir = $app->getUserState( "com_mobilejoomla.scientiaupdatedir", false );
	if($dir)
	{
		$installer = new JInstaller();
		$installer->install($dir);
		$app->setUserState( "com_mobilejoomla.scientiaupdatedir", false );
		JFolder::delete($dir);
		global $isJoomla15;
		if($isJoomla15)
		{
			require_once JPATH_ROOT.'/plugins/mobile/scientia/scientia_helper.php';
			ScientiaHelper::installDatabase();
			ScientiaHelper::enablePlugin();
		}
	}
	else
		JError::raiseWarning(1, JText::_('COM_MJ__UPDATE_UNKNOWN_PATH'));
	_sendStatus();
