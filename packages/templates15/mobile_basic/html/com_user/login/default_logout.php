<?php defined('_JEXEC') or die('Restricted access'); ?>
<div class="logout<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<div class="logout-description">
		<?php
			if ($this->params->get('description_logout')) :
				echo $this->escape($this->params->get('description_logout_text'));
			endif;
		?>
		<?php if ( $this->params->get( 'show_logout_title' ) ) : ?>
			<h2><?php echo $this->escape($this->params->get( 'header_logout' )); ?></h2>
		<?php endif; ?>
	</div>

	<form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post">
		<button type="submit" class="button"><?php echo JText::_('Logout'); ?></button>
		<input type="hidden" name="option" value="com_user" />
		<input type="hidden" name="task" value="logout" />
		<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
	</form>
</div<
