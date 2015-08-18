<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version    ###VERSION###
 * @license    ###LICENSE###
 * @copyright  ###COPYRIGHT###
 * @date       ###DATE###
 */
defined('_JEXEC') or die('Restricted access');

/** @var MjController $this */
/** @var array $params */
/** @var string $controllerName */
/** @var string $viewName */

echo $this->renderView('global/header');

JToolbarHelper::apply();
JToolbarHelper::save();
JToolbarHelper::cancel();

?>
<p class="well">
    <?php echo JText::_('COM_MJ__TABLET_MODE_MJPRO'); ?>
    <a href="http://www.mobilejoomla.com/upgrade-mjpro?utm_source=mjbackend&amp;utm_medium=Tablet-tab-upgrade&amp;utm_campaign=Admin-upgrade" target="_blank" class="btn btn-primary"><?php echo JText::_('COM_MJ__UPGRADE'); ?></a>
</p>
<form method="post" action="index.php" id="adminForm" name="adminForm">
    <input type="hidden" name="option" value="com_mobilejoomla">
    <input type="hidden" name="task" value="cancel">
</form>