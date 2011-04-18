<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<script type="text/javascript">
<!--
	Window.onDomReady(function(){
		document.formvalidator.setHandler('passverify', function (value) { return ($('password').value == value); }	);
	});
// -->
</script>

<?php
	if(isset($this->message)){
		$this->display('message');
	}
?>

<form action="<?php echo JRoute::_( 'index.php?option=com_user' ); ?>" method="post" id="josForm" name="josForm" class="form-validate">

<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
<div class="h3 componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->escape($this->params->get('page_title')); ?></div>
<?php endif; ?>

	<ul>

		<li><input type="text" name="name" id="name" size="40" value="<?php echo $this->escape($this->user->get( 'name' ));?>" class="inputbox required" maxlength="50" placeholder="<?php echo JText::_('Name'); ?> *" /></li>
		<li><input type="text" id="username" name="username" size="40" value="<?php echo $this->escape($this->user->get( 'username' ));?>" class="inputbox required validate-username" maxlength="25" placeholder="<?php echo JText::_( 'User name' ); ?> *" /></li>
		<li><input type="text" id="email" name="email" size="40" value="<?php echo $this->escape($this->user->get( 'email' ));?>" class="inputbox required validate-email" maxlength="100" placeholder="<?php echo JText::_( 'Email' ); ?> *" /></li>
  		<li><input class="inputbox required validate-password" type="password" id="password" name="password" size="40" value="" placeholder="<?php echo JText::_( 'Password' ); ?> *" /></li>
		<li><input class="inputbox required validate-passverify" type="password" id="password2" name="password2" size="40" value="" placeholder="<?php echo JText::_( 'Verify Password' ); ?> *" /></li>
	</ul>

	<div class="buttonWrapper">
		<button class="button loginButton whiteButton validate" type="submit"><?php echo JText::_('Register'); ?></button>
	</div>

	<input type="hidden" name="task" value="register_save" />
	<input type="hidden" name="id" value="0" />
	<input type="hidden" name="gid" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<div class="info"><p><?php echo JText::_( 'REGISTER_REQUIRED' ); ?></p></div>
