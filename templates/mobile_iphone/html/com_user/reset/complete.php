<?php defined('_JEXEC') or die; ?>

<div class="h3 componentheading">
	<?php echo JText::_('Reset your Password'); ?>
</div>

<form action="<?php echo JRoute::_( 'index.php?option=com_user&task=completereset' ); ?>" method="post" class="josForm form-validate">
	<p class="textview"><?php echo JText::_('RESET_PASSWORD_COMPLETE_DESCRIPTION'); ?></p>

	<ul>
		<li>	
			<input id="password1" name="password1" type="password" class="required validate-password" title="<?php echo JText::_('RESET_PASSWORD_PASSWORD1_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_PASSWORD1_TIP_TEXT'); ?>" placeholder="<?php echo JText::_('Password'); ?>" />
		</li>
		<li>
			<input id="password2" name="password2" type="password" class="required validate-password" title="<?php echo JText::_('RESET_PASSWORD_PASSWORD2_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_PASSWORD2_TIP_TEXT'); ?>" placeholder="<?php echo JText::_('Verify Password'); ?>" />
		</li>
	</ul>

	<div class="buttonWrapper">
		<button type="submit" class="button loginButton whiteButton validate"><?php echo JText::_('Submit'); ?></button>
	</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
