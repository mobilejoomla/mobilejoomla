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

	jimport('joomla.installer.helper');
	jimport('joomla.installer.installer');
	$app = JFactory::getApplication();

	_initStatus();
	$filename = $app->getUserState( "com_mobilejoomla.scientiaupdatefilename", false );
	$config = JFactory::getConfig();
	$c = (substr(JVERSION,0,3)=='1.5') ? 'config.' : '';
	$path = $config->getValue($c.'tmp_path').DS.$filename;
	if($path)
	{
		$result = JInstallerHelper::unpack($path);
		$app->setUserState( "com_mobilejoomla.scientiaupdatefilename", false );
		if($result!==false)
		{
			$app->setUserState( "com_mobilejoomla.scientiaupdatedir", $result['dir'] );
			JFile::delete($path);
		}
	}
	else
		JError::raiseWarning(1, JText::_('COM_MJ__UPDATE_UNKNOWN_PATH'));
	_sendStatus();
