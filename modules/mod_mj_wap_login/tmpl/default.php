<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php if($type == 'logout') : ?>
<do type="accept" label="<?php echo JText::_( 'BUTTON_LOGOUT'); ?>">
<go method="post" href="index.php">
<postfield name="option" value="logout&amp;" />
<postfield name="task" value="logout&amp;" />
<postfield name="return" value="<?php echo $return; ?>" />
</go>
</do>
<?php if ($params->get('greeting')) : ?>
<p>
<?php if ($params->get('name')) : {
		echo JText::sprintf( 'HINAME', $user->get('name') );
	} else : {
		echo JText::sprintf( 'HINAME', $user->get('username') );
	} endif; ?>
</p>
<?php endif; ?>
<?php else : ?>
<do type="accept" label="<?php echo JText::_('LOGIN') ?>">
<go method="post" href="<?php echo JRoute::_( 'index.php', true, $params->get('usesecure')); ?>">
<postfield name="username" value="$(username)&amp;" />
<postfield name="passwd" value="$(passwd)&amp;" />
<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
<postfield name="remember" value="$(remember)&amp;" />
<?php endif; ?>
<postfield name="option" value="com_user&amp;" />
<postfield name="task" value="login&amp;" />
<postfield name="return" value="<?php echo $return; ?>&amp;" />
<postfield name="<?php echo JUtility::getToken(); ?>" value="1" />
</go>
</do>
<?php echo $params->get('pretext'); ?><br />
<?php echo JText::_('Username'); ?>: <input type="text" name="username" /><br />
<?php echo JText::_('Password'); ?>: <input type="password" name="passwd" /><br />
<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
<?php echo JText::_('Remember me'); ?>: <select name="remember" value="yes"><option value="yes">Yes</option><option value="no">No</option></select><br />
<?php endif; ?>
<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=reset' ); ?>"><?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a><br />
<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=remind' ); ?>"><?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></a>
<?php
	$usersConfig = &JComponentHelper::getParams( 'com_users' );
	if ($usersConfig->get('allowUserRegistration')) : ?>
<br /><a href="<?php echo JRoute::_( 'index.php?option=com_user&task=register' ); ?>"><?php echo JText::_('REGISTER'); ?></a>
<?php endif; ?>
<br /><?php echo $params->get('posttext'); ?>
<?php endif; ?>