<?php defined('_JEXEC') or die('Restricted access'); ?>
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
<form action="<?php echo JRoute::_( 'index.php', true, $this->params->get('usesecure')); ?>" method="post" name="com-login" id="com-form-login">
	<?php if ( $this->params->get( 'show_login_title' ) ) : ?>
	<div class="h3 componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo $this->params->get( 'header_login' ); ?>
	</div>
	<?php endif; ?>
	<p class="textview">
		<?php echo $this->image; ?>
		<?php if ( $this->params->get( 'description_login' ) ) : ?>
			<?php echo $this->params->get( 'description_login_text' ); ?>
		<?php endif; ?>
		<div style="clear:both;"></div>
	</p>

	<ul>
	
	<li id="form-login-username">
		<!--<label for="modlgn_username"><?php echo JText::_('Username') ?></label>-->
		<input id="modlgn_username" type="text" name="username" class="inputbox" alt="username" size="18"
			placeholder="<?php echo JText::_('Username') ?>" value="" />
	</li>
	<li id="form-login-password">
		<!--<label for="modlgn_passwd"><?php echo JText::_('Password') ?></label>-->
		<input id="modlgn_passwd" type="password" name="passwd" class="inputbox" size="18" alt="password"
			placeholder="<?php echo JText::_('Password') ?>" value ="" />
	</li>
	<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
	<li id="form-login-remember">
		<label for="modlgn_remember"><?php echo JText::_('Remember me') ?></label>
		<span class="toggle">
			<input id="modlgn_remember" type="checkbox" name="remember" class="inputbox" value="yes" alt="Remember Me" />
		</span>
	</li>
	<?php endif; ?>
	</ul>

	<div class="loginButtonWrapper">
		<input type="submit" name="Submit" class="button whiteButton loginButton" value="<?php echo JText::_('LOGIN') ?>" />
	</div>
	
	<ul>
		<li>
			<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=reset' ); ?>" class="mainlevel">
			<?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a>
		</li>
		<li>
			<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=remind' ); ?>" class="mainlevel">
			<?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></a>
		</li>
		<?php
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a href="<?php echo JRoute::_( 'index.php?option=com_user&task=register' ); ?>" class="mainlevel">
				<?php echo JText::_('REGISTER'); ?></a>
		</li>
		<?php endif; ?>
	</ul>

	<input type="hidden" name="option" value="com_user" />
	<input type="hidden" name="task" value="login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

