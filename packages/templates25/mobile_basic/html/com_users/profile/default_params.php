<?php
/**
 * @version		$Id: default_params.php 20214 2011-01-09 20:25:57Z chdemko $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */
defined('_JEXEC') or die;

JLoader::register('JHtmlUsers', JPATH_COMPONENT . '/helpers/html/users.php');
JHtml::register('users.spacer', array('JHtmlUsers','spacer'));
JHtml::register('users.helpsite', array('JHtmlUsers','helpsite'));
JHtml::register('users.templatestyle', array('JHtmlUsers','templatestyle'));
JHtml::register('users.admin_language', array('JHtmlUsers','admin_language'));
JHtml::register('users.language', array('JHtmlUsers','language'));
JHtml::register('users.editor', array('JHtmlUsers','editor'));

?>
<?php $fields = $this->form->getFieldset('params'); ?>
<?php if(count($fields)): ?>
<table id="users-profile-custom">
	<caption><?php echo JText::_('COM_USERS_SETTINGS_FIELDSET_LABEL'); ?></caption>
	<?php foreach ($fields as $field):
		if (!$field->hidden) :?>
		<tr><td><?php echo $field->title; ?></td>
		<td>
			<?php if (JHtml::isRegistered('users.'.$field->id)):?>
				<?php echo JHtml::_('users.'.$field->id, $field->value);?>
			<?php elseif (JHtml::isRegistered('users.'.$field->fieldname)):?>
				<?php echo JHtml::_('users.'.$field->fieldname, $field->value);?>
			<?php elseif (JHtml::isRegistered('users.'.$field->type)):?>
				<?php echo JHtml::_('users.'.$field->type, $field->value);?>
			<?php else:?>
				<?php echo JHtml::_('users.value', $field->value);?>
			<?php endif;?>
		</td></tr>
		<?php endif;?>
	<?php endforeach;?>
</table>
<?php endif;?>

