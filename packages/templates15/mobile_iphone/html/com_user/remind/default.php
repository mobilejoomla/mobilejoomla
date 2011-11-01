<?php defined('_JEXEC') or die; ?>

<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
	<div class="h3 componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
<?php endif; ?>

<form action="<?php echo JRoute::_( 'index.php?option=com_user&task=remindusername' ); ?>" method="post" class="josForm form-validate">
	<p class="textview"><?php echo JText::_('REMIND_USERNAME_DESCRIPTION'); ?></p>

	<ul>
		<li><input id="email" name="email" type="text" class="required validate-email" title="<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TITLE'); ?>::<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TEXT'); ?>" placeholder="<?php echo JText::_('Email Address'); ?>" /></li>
	</ul>

	<div class="buttonWrapper">
		<button type="submit" class="button loginButton whiteButton validate"><?php echo JText::_('Submit'); ?></button>
	</div>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
