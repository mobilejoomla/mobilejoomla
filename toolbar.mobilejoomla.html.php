<?php
/**
 * Mobile Joomla!
 * http://www.mobilejoomla.com
 *
 * @version		0.9.8
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	(C) 2008-2010 MobileJoomla!
 * @date		September 2010
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
