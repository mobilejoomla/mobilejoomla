<?php
// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::core();
$params		= &$this->item->params;
$n			= count($this->items);
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<?php if (empty($this->items)) : ?>
	<?php if ($this->params->get('show_no_articles',1)) : ?>
		<p><?php echo JText::_('COM_CONTENT_NO_ARTICLES'); ?></p>
	<?php endif; ?>
<?php else : ?>
	<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm" data-ajax="true">

		<?php if ($this->params->get('filter_field') != 'hide') :?>
			<div class="filter-search ui-field-contain">
				<label for="filter-search" class="filter-search-lbl"><?php echo JText::_('COM_CONTENT_'.$this->params->get('filter_field').'_FILTER_LABEL'); ?></label>
				<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" />
			</div>
		<?php endif; ?>

		<?php if ($this->params->get('show_headings')) :?>
			<div data-role="controlgroup" data-type="horizontal" class="gridsort">
				<?php if ($date = $this->params->get('list_show_date')) : ?>
					<?php if ($date == "created") : ?>
						<?php 	$text = JHtml::_('grid.sort', 'COM_CONTENT_'.$date.'_DATE', 'a.created', $listDirn, $listOrder); ?>
					<?php elseif ($date == "modified") : ?>
						<?php 	$text = JHtml::_('grid.sort', 'COM_CONTENT_'.$date.'_DATE', 'a.modified', $listDirn, $listOrder); ?>
					<?php elseif ($date == "published") : ?>
						<?php 	$text = JHtml::_('grid.sort', 'COM_CONTENT_'.$date.'_DATE', 'a.publish_up', $listDirn, $listOrder); ?>
					<?php endif; ?>
					<?php echo preg_replace('/^<a /', '<a data-role="button" ', $text); ?>
				<?php endif; ?>
				<?php if ($this->params->get('list_show_author',1)) : ?>
					<?php echo preg_replace('/^<a /', '<a data-role="button" ', JHtml::_('grid.sort', 'JAUTHOR', 'author', $listDirn, $listOrder)); ?>
				<?php endif; ?>
				<?php if ($this->params->get('list_show_hits',1)) : ?>
					<?php echo preg_replace('/^<a /', '<a data-role="button" ', JHtml::_('grid.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder)); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<ul data-role="listview">
			<?php foreach ($this->items as $i => $article) : ?>
				<?php if ($this->items[$i]->state == 0) : ?>
					<li class="system-unpublished cat-list-row">
				<?php else: ?>
					<li class="cat-list-row" >
				<?php endif; ?>
				<?php if (in_array($article->access, $this->user->getAuthorisedViewLevels())) : ?>
					<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid)); ?>">
						<h2><?php echo $this->escape($article->title); ?></h2>
						<?php if ($this->params->get('list_show_date')) : ?>
							<p class="list-date"><?php echo JHtml::_('date',$article->displayDate, $this->escape($this->params->get('date_format', JText::_('DATE_FORMAT_LC3')))); ?></p>
						<?php endif; ?>
						<?php if ($this->params->get('list_show_author',1) && !empty($article->author )) : ?>
							<p class="list-author">
								<?php $author =  $article->author ?>
								<?php $author = ($article->created_by_alias ? $article->created_by_alias : $author);?>
								<?php if (!empty($article->contactid ) &&  $this->params->get('link_author') == true):?>
									<?php 	echo JHtml::_('link', JRoute::_('index.php?option=com_contact&view=contact&id='.$article->contactid), $author); ?>
								<?php else :?>
									<?php 	echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
								<?php endif; ?>
							</p>
						<?php endif; ?>
						<?php if ($this->params->get('list_show_hits',1)) : ?>
							<p class="list-hits"><?php echo $article->hits; ?></p>
						<?php endif; ?>
					</a>
				<?php else : // Show unauth links. ?>
					<?php
						echo $this->escape($article->title).' : ';
						$menu		= JFactory::getApplication()->getMenu();
						$active		= $menu->getActive();
						$itemId		= $active->id;
						$link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$itemId);
						$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug));
						$fullURL = new JURI($link);
						$fullURL->setVar('return', base64_encode($returnURL));
					?>
					<a href="<?php echo $fullURL; ?>" class="register"><?php echo rtrim(JText::_('COM_CONTENT_REGISTER_TO_READ_MORE'), ' .'); ?></a>
				<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
			<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php endif; ?>

		<?php if ($this->params->get('show_pagination_limit')) : ?>
			<div data-role="controlgroup" data-type="horizontal" class="display-limit">
				<label for="limit" data-inline="true"><?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?></label>
				<?php echo preg_replace('/^<select /', '<select data-inline="true" data-native-menu="false" ', $this->pagination->getLimitBox()); ?>
			</div>
		<?php endif; ?>

		<input type="hidden" name="filter_order" value="" />
		<input type="hidden" name="filter_order_Dir" value="" />
		<input type="hidden" name="limitstart" value="" />
	</form>
<?php endif; ?>