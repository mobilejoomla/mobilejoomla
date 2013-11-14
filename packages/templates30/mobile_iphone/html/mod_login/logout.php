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
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" name="login" id="form-login">
<?php if ($params->get('greeting')) : ?>
	<ul>
	<li>
	<?php if ($params->get('name')) : {
		echo JText::sprintf( 'MOD_LOGIN_HINAME', $user->get('name') );
	} else : {
		echo JText::sprintf( 'MOD_LOGIN_HINAME', $user->get('username') );
	} endif; ?>
	</li>
	</ul>
<?php endif; ?>
	<div class="loginButtonWrapper">
		<input type="submit" name="Submit" class="button whiteButton loginButton" value="<?php echo JText::_( 'JLOGOUT'); ?>" />
	</div>
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.logout" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>