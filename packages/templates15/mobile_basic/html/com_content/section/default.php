<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$cparams =& JComponentHelper::getParams('com_media');
?>
<div class="categories-list<?php echo $this->escape($this->params->get('pageclass_sfx'));?>">
<div class="category-desc">
<?php if ($this->params->get('show_description_image') && $this->section->image) : ?>
	<img src="<?php echo $this->baseurl.'/'.$cparams->get('image_path').'/'.$this->section->image;?>" align="<?php echo $this->section->image_position;?>" hspace="6" alt="<?php echo $this->section->image;?>" />
<?php endif; ?>
<?php if ($this->params->get('show_description') && $this->section->description) : ?>
	<?php echo $this->section->description; ?>
<?php endif; ?>
</div>

<?php if ($this->params->get('show_categories', 1)) :
	$class = ' class="first"'; ?>
<ul data-role="listview">
<?php
foreach ($this->categories as $id => $category) :
	if (!$this->params->get('show_empty_categories') && !$category->numitems) {
        continue;
    }
	if (!isset($this->categories[$id + 1])) {
        $class = ' class="last"';
    }
?>
<li<?php echo $class; ?>><?php $class = ''; ?>
<span class="item-title"><a href="<?php echo $category->link;?>"><?php echo $this->escape($category->title); ?></a></span>
<?php if ($this->params->def('show_category_description', 1) && $category->description) : ?>
<div class="category-desc"><?php echo $category->description; ?></div>
<?php endif; ?>
<?php if ($this->params->get('show_cat_num_articles')) : ?>
<div>
<?php
if ($category->numitems == 1) {
    echo $category->numitems . " <span>" . JText::_('item') . "</span>";
} else {
    echo $category->numitems . " <span>" . JText::_('items') . "</span>";
}
?>
</div>
<?php endif; ?>
</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

</div>