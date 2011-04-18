<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$cparams =& JComponentHelper::getParams('com_media');
?>
<?php if ($this->params->get('show_page_title', 1)) : ?>
<strong><?php echo $this->escape($this->params->get('page_title')); ?></strong>
<br />
<?php endif; ?>
<?php if ($this->params->get('show_description_image') && $this->section->image) : ?>
<img src="<?php echo $this->baseurl . '/' . $cparams->get('image_path') . '/'.  $this->section->image;?>" align="<?php echo $this->section->image_position;?>" hspace="6" alt="<?php echo $this->section->image;?>" />
<?php endif; ?>
<?php if ($this->params->get('show_description') && $this->section->description) : ?>
<?php echo $this->section->description; ?>
<?php endif; ?>
<br />
<?php if ($this->params->get('show_categories', 1)) : ?>
<?php foreach ($this->categories as $category) : ?>
<?php if (!$this->params->get('show_empty_categories') && !$category->numitems) continue; ?>
<a href="<?php echo $category->link; ?>">
<?php echo $category->title;?></a>
<?php if ($this->params->get('show_cat_num_articles')) : ?>
<small>
(<?php if ($category->numitems==1) {
				echo $category->numitems ." ". JText::_( 'item' );}
				else {
				echo $category->numitems ." ". JText::_( 'items' );} ?>)
</small>
<?php endif; ?>
<?php if ($this->params->def('show_category_description', 1) && $category->description) : ?>
<br />
<?php echo $category->description; ?>
<?php endif; ?>
<br />
<?php endforeach; ?>
<?php endif; ?>
<br />
<br />