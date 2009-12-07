<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php if($type == 'logout') : ?>
<form action="index.php" method="post" name="login">
<?php if ($params->get('greeting')) : ?>
	<div>
	<?php if ($params->get('name')) : {
		echo JText::sprintf( 'HINAME', $user->get('name') );
	} else : {
		echo JText::sprintf( 'HINAME', $user->get('username') );
	} endif; ?>
	</div>
<?php endif; ?>
	<div align="center">
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_( 'BUTTON_LOGOUT'); ?>">
	</div>

	<input type="hidden" name="option" value="com_user">
	<input type="hidden" name="task" value="logout">
	<input type="hidden" name="return" value="<?php echo $return; ?>">
</form>
<?php else : ?>
<form action="<?php echo JRoute::_( 'index.php', true, $params->get('usesecure')); ?>" method="post" name="login"><div>
<?php echo $params->get('pretext'); ?><br>
<?php echo JText::_('Username') ?><br>
<input type="text" name="username" class="inputbox" size="10"><br>
<?php echo JText::_('Password') ?><br>
<input type="password" name="passwd" class="inputbox" size="10"><br>
<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
<?php echo JText::_('Remember me') ?><br>
<input type="checkbox" name="remember" class="inputbox" value="yes" alt="Remember Me"><br>
<?php endif; ?>
<input type="submit" name="Submit" class="button" value="<?php echo JText::_('LOGIN') ?>"><br>
<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=reset' ); ?>"><?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a><br>
<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=remind' ); ?>"><?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></a>
<?php
	$usersConfig = &JComponentHelper::getParams( 'com_users' );
	if ($usersConfig->get('allowUserRegistration')) : ?>
<br><a href="<?php echo JRoute::_( 'index.php?option=com_user&task=register' ); ?>"><?php echo JText::_('REGISTER'); ?></a>
<?php endif; ?>
<br><?php echo $params->get('posttext'); ?>
<input type="hidden" name="option" value="com_user">
<input type="hidden" name="task" value="login">
<input type="hidden" name="return" value="<?php echo $return; ?>">
<input type="hidden" name="<?php JUtility::getToken(); ?>" value="1">
</div></form>
<?php endif; ?>