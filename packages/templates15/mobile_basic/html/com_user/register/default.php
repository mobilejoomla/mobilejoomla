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
<div class="registration<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
<form id="member-registration" action="<?php echo JRoute::_( 'index.php?option=com_user' ); ?>" method="post" class="form-validate">
	<fieldset>

		<div class="ui-field-contain">
		<label id="namemsg" for="name"><?php echo JText::_( 'Name' ); ?>:</label>
		<input type="text" name="name" id="name" size="40" value="<?php echo $this->escape($this->user->get( 'name' ));?>" class="inputbox required" maxlength="50" />
		</div>

		<div class="ui-field-contain">
		<label id="usernamemsg" for="username"><?php echo JText::_( 'User name' ); ?>:</label>
		<input type="text" id="username" name="username" size="40" value="<?php echo $this->escape($this->user->get( 'username' ));?>" class="inputbox required validate-username" maxlength="25" />
		</div>

		<div class="ui-field-contain">
		<label id="emailmsg" for="email"><?php echo JText::_( 'Email' ); ?>:</label>
		<input type="text" id="email" name="email" size="40" value="<?php echo $this->escape($this->user->get( 'email' ));?>" class="inputbox required validate-email" maxlength="100" />
		</div>

		<div class="ui-field-contain">
		<label id="pwmsg" for="password"><?php echo JText::_( 'Password' ); ?>:</label>
  		<input class="inputbox required validate-password" type="password" id="password" name="password" size="40" value="" />
		</div>

		<div class="ui-field-contain">
		<label id="pw2msg" for="password2"><?php echo JText::_( 'Verify Password' ); ?>:</label>
		<input class="inputbox required validate-passverify" type="password" id="password2" name="password2" size="40" value="" />
		</div>

	</fieldset>

	<div class="ui-grid-a">
		<div class="ui-block-a">
			<button class="button validate" type="submit"><?php echo JText::_('Register'); ?></button>
		</div>
		<div class="ui-block-b">
			<a data-role="button" href="<?php echo JRoute::_('');?>" title="<?php echo JText::_('Cancel');?>"><?php echo JText::_('Cancel');?></a>
		</div>
	</div>

	<input type="hidden" name="task" value="register_save" />
	<input type="hidden" name="id" value="0" />
	<input type="hidden" name="gid" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>