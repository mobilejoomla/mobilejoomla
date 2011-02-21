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
	function _DEFAULT()
	{
		JToolBarHelper::title(JText::_('COM_MJ__MOBILE_JOOMLA_SETTINGS'), 'config.php');
		JToolBarHelper::apply();
		JToolBarHelper::cancel('cancel');
	}

	function _ABOUT()
	{
		JToolBarHelper::title(JText::_('COM_MJ__ABOUT_MOBILE_JOOMLA'));
		JToolBarHelper::cancel('cancel');
	}

	function _EXT()
	{
		JToolBarHelper::title(JText::_('COM_MJ__EXTENSIONS'));
		JToolBarHelper::save();
		JToolBarHelper::cancel('cancel');
	}
}
