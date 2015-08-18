<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div class="archive<?php echo $this->escape($this->params->get('pageclass_sfx'));?>">
<form id="adminForm" action="<?php echo JRoute::_('index.php')?>" method="post" data-ajax="true">
<div>
<div class="filter-search">
<?php if ($this->params->get('filter')) : ?>
<label for="filter-search" class="filter-search-lbl"><?php echo JText::_('Filter'); ?></label>
<input type="text" name="filter" id="filter-search" value="<?php echo $this->escape($this->filter); ?>" class="inputbox" onchange="document.jForm.submit();" />
<?php endif; ?>
<div class="ui-field-contain">
<fieldset data-role="controlgroup" data-type="horizontal">
<?php echo $this->form->monthField; ?>
<?php echo $this->form->yearField; ?>
</fieldset>
</div>
<?php echo $this->form->limitField; ?>
<input type="submit" class="button" value="<?php echo $this->escape(JText::_('Filter')); ?>" />
</div>
<input type="hidden" name="view" value="archive" />
<input type="hidden" name="option" value="com_content" />
<input type="hidden" name="viewcache" value="0" />
<?php echo $this->loadTemplate('items'); ?>
</div>
</form>
</div>