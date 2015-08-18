<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$cparams =& JComponentHelper::getParams('com_media');
?>
<div class="category-list<?php echo $this->escape($this->params->get('pageclass_sfx'));?>">
<div class="category-desc">
<?php if ($this->category->image) : ?>
	<img src="<?php echo $this->baseurl.'/'.$cparams->get('image_path').'/'.$this->category->image;?>" align="<?php echo $this->category->image_position;?>" hspace="6" alt="<?php echo $this->category->image;?>" />
<?php endif; ?>
<?php echo $this->category->description; ?>
<div class="clr"></div>
</div>
<?php
	$this->items =& $this->getItems();
	echo $this->loadTemplate('items');
?>
</div>