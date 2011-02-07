<?php defined('_JEXEC') or die; ?>

<div class="h3 componentheading">
	<?php echo JText::_('Confirm your Account'); ?>
</div>

<form action="<?php echo JRoute::_( 'index.php?option=com_user&task=confirmreset' ); ?>" method="post" class="josForm form-validate">
	<p class="textview"><?php echo JText::_('RESET_PASSWORD_CONFIRM_DESCRIPTION'); ?></p>

	<ul>
		<li>
			<input id="username" name="username" type="text" class="required" size="36" title="<?php echo JText::_('RESET_PASSWORD_USERNAME_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_USERNAME_TIP_TEXT'); ?>" placeholder="<?php echo JText::_('User Name'); ?>" />
		</li>
		<li>
			<input id="token" name="token" type="text" class="required" size="36" title="<?php echo JText::_('RESET_PASSWORD_TOKEN_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_TOKEN_TIP_TEXT'); ?>" placeholder="<?php echo JText::_('Token'); ?>" />
		</li>
	</ul>

	<div class="buttonWrapper">
		<button type="submit" class="button loginButton whiteButton validate"><?php echo JText::_('Submit'); ?></button>
	</div>

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
