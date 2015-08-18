<?php
// no direct access
defined('_JEXEC') or die;
?>
<?php if (count($this->children[$this->category->id]) > 0) : ?>
<ul data-role="listview">
<?php foreach($this->children[$this->category->id] as $id => $child) : ?>
<li>
<span class="item-title"><a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($child->id));?>"><?php echo $this->escape($child->title); ?></a></span>
<?php if ($this->params->get('show_subcat_desc') == 1) :?>
<?php if ($child->description) : ?>
<div class="category-desc"><?php echo JHtml::_('content.prepare', $child->description, '', 'com_content.category'); ?></div>
<?php endif; ?>
<?php endif; ?>
<?php if ( $this->params->get('show_cat_num_articles',1)) : ?>
<div>
<span><?php echo JText::_('COM_CONTENT_NUM_ITEMS') ; ?></span>
<?php echo $child->getNumItems(true); ?>
</div>
<?php endif ; ?>
<?php if (count($child->getChildren()) > 0 ) :
		$this->children[$child->id] = $child->getChildren();
		$this->category = $child;
		$this->maxLevel--;
		if ($this->maxLevel != 0) :
			echo $this->loadTemplate('children');
		endif;
		$this->category = $child->getParent();
		$this->maxLevel++;
	endif; ?>
</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>