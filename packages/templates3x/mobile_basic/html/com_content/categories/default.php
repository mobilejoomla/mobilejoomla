<?php
// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>
<div class="categories-list<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->get('show_base_description')) : ?>
<?php if($this->params->get('categories_description')) : ?>
<?php 	echo JHtml::_('content.prepare', $this->params->get('categories_description'), '', 'com_content.categories'); ?>
<?php else: ?>
<?php 	if ($this->parent->description) : ?>
<div class="category-desc"><?php  echo JHtml::_('content.prepare', $this->parent->description, '', 'com_content.categories'); ?></div>
<?php 	endif; ?>
<?php endif; ?>
<?php endif; ?>
<?php echo $this->loadTemplate('items'); ?>
</div>