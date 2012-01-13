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
defined('_JEXEC') or die('Restricted Access');

$document =& JFactory::getDocument();
$lang =& JFactory::getLanguage();

$document->addStyleSheet('modules/mod_mj_adminicon/css/mod_mj_adminicon.css');
$lang->load('com_mobilejoomla', JPATH_ADMINISTRATOR);

switch(substr(JVERSION,0,3))
{
case '1.5': $iconclass = 'icon15'; break;
case '1.6': $iconclass = 'icon16'; break;
default:    $iconclass = 'icon17'; break; // 1.7 & 2.6
}

include_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'admin.mobilejoomla.html.php';
HTML_MobileJoomla::CheckForUpdate();

JHTML::_('behavior.modal', 'a.modal');
?>
<div id="mjicon">
	<div id="mjnoupdate" class="icon-wrapper" style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon <?php echo $iconclass; ?>">
			<a href="index.php?option=com_mobilejoomla">
				<img src="modules/mod_mj_adminicon/images/mj-cpanel.png" />
				<span><?php echo JText::_('COM_MJ__MOBILEJOOMLA'); ?></span>
			</a>
		</div>
	</div>
	<div id="mjupdate" class="icon-wrapper" style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon <?php echo $iconclass; ?>">
			<a class="modal" href="index.php?tmpl=component&option=com_mobilejoomla&task=update" rel="{handler: 'iframe', size: {x: 480, y: 320}}">
				<img src="modules/mod_mj_adminicon/images/mj-update.png" />
				<span><?php echo JText::_('COM_MJ__UPDATE_AVAILABLE'); ?></span>
			</a>
		</div>
	</div>
</div>
