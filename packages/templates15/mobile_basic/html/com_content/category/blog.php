<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$cparams =& JComponentHelper::getParams('com_media');
?>
<div class="blog<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">

<?php if ($this->params->def('show_description', 1) || $this->params->def('show_description_image', 1)) :?>
<div class="category-desc">
	<?php if ($this->params->get('show_description_image') && $this->category->image) : ?>
		<img src="<?php echo $this->baseurl.'/'.$cparams->get('image_path').'/'.$this->category->image;?>" align="<?php echo $this->category->image_position;?>" hspace="6" alt="" />
	<?php endif; ?>
	<?php if ($this->params->get('show_description') && $this->category->description) : ?>
		<?php echo $this->category->description; ?>
	<?php endif; ?>
<div class="clr"></div>
</div>
<?php endif; ?>

<?php $leadingcount=0 ; ?>
<?php if ($this->params->get('num_leading_articles')) : ?>
<div class="items-leading">
	<?php for ($i = $this->pagination->limitstart; $i < ($this->pagination->limitstart + $this->params->get('num_leading_articles')); $i++) : ?>
		<?php if ($i >= $this->total) : break; endif; ?>
		<div class="leading-<?php echo $leadingcount; ?>">
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
<div class="item column-<?php echo $rowcount;?>">
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

<?php if ($this->params->get('num_links') && ($i < $this->total)) : ?>
<?php
	$this->links = array_splice($this->items, $i - $this->pagination->limitstart);
	echo $this->loadTemplate('links');
?>
<?php endif; ?>

<?php if ($this->params->get('show_pagination')) : ?>
<div class="pagination">
<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<?php endif; ?>
</div>
