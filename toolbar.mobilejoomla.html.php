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
		JToolBarHelper::title(JText::_('Mobile Joomla! Settings'), 'config.php');
		JToolBarHelper::save();
		JToolBarHelper::cancel('cancel');
	}

	function _ABOUT()
	{
		JToolBarHelper::title(JText::_('About Mobile Joomla'));
		JToolBarHelper::cancel('cancel');
	}

	function _EXT()
	{
		JToolBarHelper::title(JText::_('Extensions'));
		JToolBarHelper::save();
		JToolBarHelper::cancel('cancel');
	}
}
