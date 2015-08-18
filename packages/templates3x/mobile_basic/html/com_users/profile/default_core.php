<?php
/**
 * @version		$Id: default_core.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

jimport('joomla.user.helper');
?>
<table id="users-profile-core">
	<caption><?php echo JText::_('COM_USERS_PROFILE_CORE_LEGEND'); ?></caption>
	<tr><td><?php echo JText::_('COM_USERS_PROFILE_NAME_LABEL'); ?></td>
	<td><?php echo $this->data->name; ?></td></tr>
	<tr><td><?php echo JText::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?></td>
	<td><?php echo htmlspecialchars($this->data->username); ?></td></tr>
	<tr><td><?php echo JText::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?></td>
	<td><?php echo JHTML::_('date',$this->data->registerDate); ?></td></tr>
	<tr><td><?php echo JText::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?></td>
	<td>
	<?php if ($this->data->lastvisitDate != '0000-00-00 00:00:00'):
			echo JHTML::_('date',$this->data->lastvisitDate);
		else:
			echo JText::_('COM_USERS_PROFILE_NEVER_VISITED');
		endif; ?>
	</td></tr>
</table>
