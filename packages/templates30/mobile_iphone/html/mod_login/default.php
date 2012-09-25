<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version		###VERSION###
 * @license		###LICENSE###
 * @copyright	###COPYRIGHT###
 * @date		###DATE###
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" name="login" id="form-login" >
	<ul>
	<?php echo $params->get('pretext'); ?>
	<li id="form-login-username">
		<!--<label for="modlgn_username"><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?></label>-->
		<input id="modlgn_username" type="text" name="username" class="inputbox" alt="username" size="18"
			placeholder="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>" value="" />
	</li>
	<li id="form-login-password">
		<!--<label for="modlgn_passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>-->
		<input id="modlgn_passwd" type="password" name="password" class="inputbox" size="18" alt="password"
			placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" value ="" />
	</li>
	<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
	<li id="form-login-remember">
		<label for="modlgn_remember"><?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?></label>
		<span class="toggle">
			<input id="modlgn_remember" type="checkbox" name="remember" class="inputbox" value="yes" alt="Remember Me" />
		</span>
	</li> 
	<?php endif; ?>
	</ul>
	<div class="loginButtonWrapper">
		<input type="submit" name="Submit" class="button whiteButton loginButton" value="<?php echo JText::_('JLOGIN') ?>" />
	</div>
	<ul>
		<li>
			<a href="<?php echo JRoute::_( 'index.php?option=com_users&view=reset' ); ?>" class="mainlevel">
			<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
		</li>
		<li>
			<a href="<?php echo JRoute::_( 'index.php?option=com_users&view=remind' ); ?>" class="mainlevel">
			<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
		</li>
		<?php
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a href="<?php echo JRoute::_( 'index.php?option=com_users&view=registration' ); ?>" class="mainlevel">
				<?php echo JText::_('MOD_LOGIN_REGISTER'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
	<?php echo $params->get('posttext'); ?>
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>