<?php
// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>
<div class="archive<?php echo $this->pageclass_sfx;?>">
<form id="adminForm" action="<?php echo JRoute::_('index.php')?>" method="post" data-ajax="true">
<div>
<div class="filter-search">
<?php if ($this->params->get('filter_field') != 'hide') : ?>
<label for="filter-search" class="filter-search-lbl"><?php echo JText::_('COM_CONTENT_'.$this->params->get('filter_field').'_FILTER_LABEL'); ?></label>
<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->filter); ?>" class="inputbox" />
<?php endif; ?>
<div class="ui-field-contain">
<fieldset data-role="controlgroup" data-type="horizontal">
<?php echo $this->form->monthField; ?>
<?php echo $this->form->yearField; ?>
</fieldset>
</div>
<?php echo $this->form->limitField; ?>
<input type="submit" class="button" value="<?php echo $this->escape(JText::_('JGLOBAL_FILTER_BUTTON')); ?>" />
</div>
<input type="hidden" name="view" value="archive" />
<input type="hidden" name="option" value="com_content" />
<input type="hidden" name="limitstart" value="0" />
<?php echo $this->loadTemplate('items'); ?>
</div>
</form>
</div>