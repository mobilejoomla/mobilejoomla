<?php
defined('_JEXEC') or die('Restricted access');
$cparams =& JComponentHelper::getParams('com_media');
?>
<div class="blog<?php echo $this->escape($this->params->get('pageclass_sfx'));?>">
<?php if ($this->params->def('show_description', 1) || $this->params->def('show_description_image', 1)) :?>
<div class="category-desc">
	<?php if ($this->params->get('show_description_image') && $this->section->image) : ?>
		<img src="<?php echo $this->baseurl.'/'.$cparams->get('image_path').'/'.$this->section->image;?>" align="<?php echo $this->section->image_position;?>" hspace="6" alt="" />
	<?php endif; ?>
	<?php if ($this->params->get('show_description') && $this->section->description) : ?>
		<?php echo $this->section->description; ?>
	<?php endif; ?>
<div class="clr"></div>
</div>
<?php endif; ?>

<?php if ($this->params->def('num_leading_articles', 1)) : ?>
<?php $leadingcount=0 ; ?>
<div class="items-leading">
<?php for ($i = $this->pagination->limitstart; $i < ($this->pagination->limitstart + $this->params->get('num_leading_articles')); $i++) : ?>
	<?php if ($i >= $this->total) : break; endif; ?>
<div class="leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>">
	<?php
		$this->item =& $this->getItem($i, $this->params);
		echo $this->loadTemplate('item');
	?>
</div>
<?php $leadingcount++; ?>
<?php endfor; ?>
</div>
<?php else : $i = $this->pagination->limitstart; endif; ?>

<?php
$leadingcount = $this->pagination->limitstart + $this->params->get('num_leading_articles');
$introcount = $leadingcount + $this->params->get('num_intro_articles', 4);
$counter=0;
if (($introcount != $leadingcount) && ($i < $this->total)) :
	for (;$i < $this->total && $i < $introcount; $i++) :
		$key= ($i-$leadingcount)+1;
		$rowcount=( ((int)$key-1) %	(int) $this->params->get('num_columns')) +1;
		$row = $counter / $this->params->get('num_columns') ;
		if ($rowcount==1) : ?>
<div class="items-row cols-<?php echo (int) $this->params->get('num_columns');?> <?php echo 'row-'.$row ; ?>">
<?php 	endif; ?>
<div class="item column-<?php echo $rowcount;?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>">
<?php
		$this->item =& $this->getItem($i, $this->params);
		echo $this->loadTemplate('item');
?>
</div>
<?php 	$counter++; ?>
<?php 	if (($rowcount == $this->params->get('num_columns')) or ($counter ==$introcount)): ?>
<span class="row-separator"></span>
</div>
<?php 	endif;
	endfor;
endif;
?>

<?php if ($this->params->def('num_links', 4) && ($i < $this->total)) : ?>
<?php
	$this->links = array_splice($this->items, $i - $this->pagination->limitstart);
	echo $this->loadTemplate('links');
?>
<?php endif; ?>

<?php if ($this->params->def('show_pagination', 2)) : ?>
<div class="pagination">
<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<?php endif; ?>

</div>