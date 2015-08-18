<?php defined('_JEXEC') or die('Restricted access'); ?>
<div class="login<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
<?php if(JPluginHelper::isEnabled('authentication', 'openid')) :
		$lang = &JFactory::getLanguage();
		$lang->load( 'plg_authentication_openid', JPATH_ADMINISTRATOR );
		$langScript = 	'var JLanguage = {};'.
						' JLanguage.WHAT_IS_OPENID = \''.JText::_( 'WHAT_IS_OPENID' ).'\';'.
						' JLanguage.LOGIN_WITH_OPENID = \''.JText::_( 'LOGIN_WITH_OPENID' ).'\';'.
						' JLanguage.NORMAL_LOGIN = \''.JText::_( 'NORMAL_LOGIN' ).'\';'.
						' var comlogin = 1;';
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration( $langScript );
		JHTML::_('script', 'openid.js');
endif; ?>
	<div class="login-description">
		<?php if ( $this->params->get( 'show_login_title' ) ) : ?>
			<h2><?php echo $this->params->get( 'header_login' ); ?></h2>
		<?php endif; ?>
		<?php if ( $this->params->get( 'description_login' ) ) : ?>
			<?php echo $this->params->get( 'description_login_text' ); ?>
		<?php endif; ?>
	</div>
	<form action="<?php echo JRoute::_( 'index.php', true, $this->params->get('usesecure')); ?>" method="post">
		<fieldset class="ui-field-contain">
		<div class="ui-field-contain">
			<label for="username"><?php echo JText::_('Username') ?></label>
			<input name="username" id="username" type="text" class="inputbox" alt="username" size="18" />
		</div>
		<div class="ui-field-contain">
			<label for="passwd"><?php echo JText::_('Password') ?></label>
			<input type="password" id="passwd" name="passwd" class="inputbox" size="18" alt="password" />
		</div>
	<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
		<div class="ui-field-contain">
			<label for="remember"><?php echo JText::_('Remember me') ?></label>
			<input type="checkbox" id="remember" name="remember" class="inputbox" value="yes" alt="Remember Me" />
		</div>
	<?php endif; ?>
			<div class="ui-field-contain">
				<button type="submit" class="button"><?php echo JText::_('LOGIN'); ?></button>
			</div>
			<input type="hidden" name="option" value="com_user" />
			<input type="hidden" name="task" value="login" />
			<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</fieldset>
	</form>
	<ul data-role="listview" data-inset="true">
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_user&view=reset'); ?>">
			<?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a>
		</li>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_user&view=remind'); ?>">
			<?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></a>
		</li>
		<?php
		$usersConfig = JComponentHelper::getParams('com_users');
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_user&view=register'); ?>">
				<?php echo JText::_('REGISTER'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
</div>
