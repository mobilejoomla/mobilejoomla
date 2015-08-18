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
<form action="<?php echo $this->action; ?>" method="post" name="adminForm" data-ajax="true">

	<?php if ($this->params->get('filter')) : ?>
		<div class="filter-search ui-field-contain">
			<label for="filter-search" class="filter-search-lbl"><?php echo JText::_($this->params->get('filter_type') . ' Filter'); ?></label>
			<input type="text" id="filter-search" name="filter" value="<?php echo $this->escape($this->lists['filter']);?>" class="inputbox" onchange="document.adminForm.submit();" />
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('show_headings')) : ?>
		<div data-role="controlgroup" data-type="horizontal" class="gridsort">
			<?php if ($this->params->get('show_title')) : ?>
				<?php echo preg_replace('/^<a /', '<a data-role="button" ', JHTML::_('grid.sort',  'Item Title', 'a.title', $this->lists['order_Dir'], $this->lists['order'] )); ?>
			<?php endif; ?>
			<?php if ($this->params->get('show_date')) : ?>
				<?php echo preg_replace('/^<a /', '<a data-role="button" ', JHTML::_('grid.sort',  'Date', 'a.created', $this->lists['order_Dir'], $this->lists['order'] )); ?>
			<?php endif; ?>
			<?php if ($this->params->get('show_author')) : ?>
				<?php echo preg_replace('/^<a /', '<a data-role="button" ', JHTML::_('grid.sort',  'Author', 'author', $this->lists['order_Dir'], $this->lists['order'] )); ?>
			<?php endif; ?>
			<?php if ($this->params->get('show_hits')) : ?>
				<?php echo preg_replace('/^<a /', '<a data-role="button" ', JHTML::_('grid.sort',  'Hits', 'a.hits', $this->lists['order_Dir'], $this->lists['order'] )); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<ul data-role="listview">
		<?php foreach ($this->items as $item) : ?>
			<li class="cat-list-row">

				<?php if ($item->access <= $this->user->get('aid', 0)) : ?>
					<?php $link = $item->link; ?>
				<?php else :
					echo $this->escape($item->title).' : ';
					$link = JRoute::_('index.php?option=com_user&view=login');
					$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid), false);
					$fullURL = new JURI($link);
					$fullURL->setVar('return', base64_encode($returnURL));
					$link = $fullURL->toString();
				?>
				<?php endif; ?>
				<a href="<?php echo $link; ?>">
					<h2><?php echo $this->escape($item->title); ?></h2>

					<?php if ($this->params->get('show_date')) : ?>
						<p class="list-date"><?php echo JText::_('Created'); ?>: <?php echo $item->created; ?></p>
					<?php endif; ?>

					<?php if ($this->params->get('show_author')) : ?>
						<p class="list-author"><?php echo JText::_('Author'); ?>:
							<?php $author =  $item->author;
								$author = ($item->created_by_alias ? $item->created_by_alias : $author);
								echo $this->escape($author); ?>
						</p>
					<?php endif; ?>

					<?php if ($this->params->get('show_hits')) : ?>
						<p class="list-hits"><?php echo JText::_('Hits'); ?>: <?php echo $this->escape($item->hits) ? $this->escape($item->hits) : '-'; ?></p>
					<?php endif; ?>

				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php if ($this->params->get('show_pagination')) : ?>
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('show_pagination_limit')) : ?>
		<div data-role="controlgroup" data-type="horizontal" class="display-limit">
			<label for="limit" data-inline="true"><?php echo JText::_('Display Num'); ?></label>
			<?php echo preg_replace('/^<select /', '<select data-inline="true" data-native-menu="false" ', $this->pagination->getLimitBox()); ?>
		</div>
	<?php endif; ?>

	<input type="hidden" name="id" value="<?php echo $this->category->id; ?>" />
	<input type="hidden" name="sectionid" value="<?php echo $this->category->sectionid; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->lists['task']; ?>" />
	<input type="hidden" name="filter_order" value="" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<input type="hidden" name="limitstart" value="0" />
	<input type="hidden" name="viewcache" value="0" />
</form>
