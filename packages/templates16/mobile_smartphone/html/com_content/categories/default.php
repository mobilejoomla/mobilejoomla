<?php
// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>
<div class="categories-list<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<?php if ($this->params->get('show_base_description')) : ?>
<?php if($this->params->get('categories_description')) : ?>
<?php 	echo  JHtml::_('content.prepare',$this->params->get('categories_description')); ?>
<?php else: ?>
<?php 	if ($this->parent->description) : ?>
<div class="category-desc"><?php  echo JHtml::_('content.prepare', $this->parent->description); ?></div>
<?php 	endif; ?>
<?php endif; ?>
<?php endif; ?>
<?php echo $this->loadTemplate('items'); ?>
</div>