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
defined('_JEXEC') or die('Restricted access'); ?>
<form id="jForm" action="<?php JRoute::_('index.php')?>" method="post">
<?php if ($this->params->get('show_page_title', 1)) : ?>
<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>"><?php echo $this->escape($this->params->get('page_title')); ?></div>
<?php endif; ?>
<p>
<?php if ($this->params->get('filter')) : ?>
<?php echo JText::_('Filter').'&nbsp;'; ?>
<input type="text" name="filter" value="<?php echo $this->escape($this->filter); ?>" class="inputbox" />
<?php endif; ?>
<?php echo $this->form->monthField; ?>
<?php echo $this->form->yearField; ?>
<?php echo $this->form->limitField; ?>
<input type="submit" class="button" value="<?php echo $this->escape(JText::_('Filter')); ?>" />
</p>
<?php echo $this->loadTemplate('items'); ?>
<input type="hidden" name="view" value="archive" />
<input type="hidden" name="option" value="com_content" />
</form>