<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<script type="text/javascript">
<!--
	Window.onDomReady(function(){
		document.formvalidator.setHandler('passverify', function (value) { return ($('password').value == value); }	);
	});
// -->
</script>

<div class="profile-edit<?php echo $this->escape($this->params->get('pageclass_sfx'));?>">
	<form id="member-profile" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate">
		<fieldset>

			<div class="ui-field-contain">
			<label for="username"><?php echo JText::_( 'User Name' ); ?>:</label>
			<span><?php echo $this->user->get('username');?></span>
			</div>

			<div class="ui-field-contain">
			<label for="name"><?php echo JText::_( 'Your Name' ); ?>:</label>
			<input class="inputbox required" type="text" id="name" name="name" value="<?php echo $this->escape($this->user->get('name'));?>" size="40" />
			</div>

			<div class="ui-field-contain">
			<label for="email"><?php echo JText::_( 'email' ); ?>:</label>
			<input class="inputbox required validate-email" type="text" id="email" name="email" value="<?php echo $this->escape($this->user->get('email'));?>" size="40" />
			</div>

<?php if($this->user->get('password')) : ?>
			<div class="ui-field-contain">
			<label for="password"><?php echo JText::_( 'Password' ); ?>:</label>
			<input class="inputbox validate-password" type="password" id="password" name="password" value="" size="40" />
			</div>

			<div class="ui-field-contain">
			<label for="password2"><?php echo JText::_( 'Verify Password' ); ?>:</label>
			<input class="inputbox validate-passverify" type="password" id="password2" name="password2" size="40" />
			</div>
<?php endif; ?>
<?php if(isset($this->params)) :  echo $this->params->render( 'params' ); endif; ?>

		</fieldset>
		<div class="ui-grid-a">
			<div class="ui-block-a">
				<button type="submit" class="validate" onclick="submitbutton( this.form );return false;"><span><?php echo JText::_('Save'); ?></span></button>
			</div>
			<div class="ui-block-b">
				<a data-role="button" href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('Cancel'); ?>"><?php echo JText::_('Cancel'); ?></a>
			</div>
			<input type="hidden" name="username" value="<?php echo $this->user->get('username');?>" />
			<input type="hidden" name="id" value="<?php echo $this->user->get('id');?>" />
			<input type="hidden" name="gid" value="<?php echo $this->user->get('gid');?>" />
			<input type="hidden" name="option" value="com_user" />
			<input type="hidden" name="task" value="save" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</div>
	</form>
</div>