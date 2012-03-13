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

class TOOLBAR_mobilejoomla
{
	static function _DEFAULT()
	{
		JToolBarHelper::title(JText::_('COM_MJ__MOBILE_JOOMLA_SETTINGS'), 'config.php');
		JToolBarHelper::apply();
		JToolBarHelper::cancel('cancel');
		$version = substr(JVERSION,0,3);
		$user = JFactory::getUser();
		if($version != '1.5' && $user->authorise('core.admin', 'com_mobilejoomla'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_mobilejoomla');
		}
	}
}
