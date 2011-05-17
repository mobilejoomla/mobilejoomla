<?php
// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');
?>
<div class="archive<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<form id="adminForm" action="<?php echo JRoute::_('index.php')?>" method="post">
<div>
<span class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></span>
<div class="filter-search">
<?php if ($this->params->get('filter_field') != 'hide') : ?>
<span class="filter-search-lbl"><?php echo JText::_('COM_CONTENT_'.$this->params->get('filter_field').'_FILTER_LABEL').'&#160;'; ?></span>
<input type="text" name="filter-search" value="<?php echo $this->escape($this->filter); ?>" class="inputbox" />
<?php endif; ?>
<?php echo $this->form->monthField; ?>
<?php echo $this->form->yearField; ?>
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