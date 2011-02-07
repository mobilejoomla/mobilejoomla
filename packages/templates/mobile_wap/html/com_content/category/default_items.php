<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<script language="javascript" type="text/javascript">
function tableOrdering( order, dir, task )
{
	var form = document.adminForm;
	form.filter_order.value 	= order;
	form.filter_order_Dir.value	= dir;
	document.adminForm.submit( task );
}
</script>
<form action="<?php echo $this->action; ?>" method="post" name="adminForm">
<div>
<?php if ($this->params->get('filter') || $this->params->get('show_pagination_limit')) : ?>
<div>
<?php if ($this->params->get('filter')) : ?>
<?php echo JText::_('Filter').' '; ?>
<input type="text" name="filter" value="<?php echo $this->lists['filter'];?>" class="inputbox" onchange="document.adminForm.submit();" />
<?php endif; ?>
<?php if ($this->params->get('show_pagination_limit')) : ?>
<?php
				echo ' '.JText::_('Display Num').' ';
				echo $this->pagination->getLimitBox();
?>
<?php endif; ?>
</div>
<?php endif; ?>
<?php if ($this->params->get('show_headings')) : ?>
<div class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<?php echo JText::_('Num'); ?>
<?php if ($this->params->get('show_title')) : ?>
<?php echo JHTML::_('grid.sort',  'Item Title', 'a.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
<?php endif; ?>
<?php if ($this->params->get('show_date')) : ?>
<?php echo JHTML::_('grid.sort',  'Date', 'a.created', $this->lists['order_Dir'], $this->lists['order'] ); ?>
<?php endif; ?>
<?php if ($this->params->get('show_author')) : ?>
<?php echo JHTML::_('grid.sort',  'Author', 'author', $this->lists['order_Dir'], $this->lists['order'] ); ?>
<?php endif; ?>
<?php if ($this->params->get('show_hits')) : ?>
<?php echo JHTML::_('grid.sort',  'Hits', 'a.hits', $this->lists['order_Dir'], $this->lists['order'] ); ?>
<?php endif; ?>
</div>
<?php endif; ?>
<?php foreach ($this->items as $item) : ?>
<div class="sectiontableentry<?php echo ($item->odd +1 ) . $this->params->get( 'pageclass_sfx' ); ?>" >
<?php echo $this->pagination->getRowOffset( $item->count ); ?>
<?php if ($this->params->get('show_title')) : ?>
<?php if ($item->access <= $this->user->get('aid', 0)) : ?>
<a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
<?php $this->item = $item; echo JHTML::_('icon.edit', $item, $this->params, $this->access) ?>
<?php else : ?>
<?php
			echo $this->escape($item->title).' : ';
			$link = JRoute::_('index.php?option=com_user&view=login');
?>
<a href="<?php echo $link; ?>"><?php echo JText::_( 'Register to read more...' ); ?></a>
<?php endif; ?>
<?php endif; ?>
<?php if ($this->params->get('show_date')) : ?>
<?php echo $item->created; ?>
<?php endif; ?>
<?php if ($this->params->get('show_author')) : ?>
<?php echo $item->created_by_alias ? $item->created_by_alias : $item->author; ?>
<?php endif; ?>
<?php if ($this->params->get('show_hits')) : ?>
<?php echo $item->hits ? $item->hits : '-'; ?>
<?php endif; ?>
</div>
<?php endforeach; ?>
<?php if ($this->params->get('show_pagination')) : ?>
<div> </div>
<div align="center" class="sectiontablefooter<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<div align="right">
<?php echo $this->pagination->getPagesCounter(); ?>
</div>
<?php endif; ?>
</div>
<input type="hidden" name="id" value="<?php echo $this->category->id; ?>" />
<input type="hidden" name="sectionid" value="<?php echo $this->category->sectionid; ?>" />
<input type="hidden" name="task" value="<?php echo $this->lists['task']; ?>" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>