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
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
<?php if ($this->params->get('show_headings') || $this->params->get('filter_field') != 'hide' || $this->params->get('show_pagination_limit')) :?>
<div>
<?php if ($this->params->get('filter_field') != 'hide') :?>	
<div class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></div>
<div class="filter-search">
<span class="filter-search-lbl"><?php echo JText::_('COM_CONTENT_'.$this->params->get('filter_field').'_FILTER_LABEL').'&#160;'; ?></span>
<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" />
<input type="submit" value="<?php echo $this->escape(JText::_('JSEARCH_FILTER_SUBMIT')); ?>" />
</div>
<?php endif; ?>
<?php if ($this->params->get('show_pagination_limit')) : ?>
<div class="display-limit">
<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
<?php echo $this->pagination->getLimitBox(); ?>
</div>
<?php endif; ?>
<input type="hidden" name="filter_order" value="" />
<input type="hidden" name="filter_order_Dir" value="" />
<input type="hidden" name="limitstart" value="" />
<?php endif; ?>
<div class="category">
<?php if ($this->params->get('show_headings')) :?>
<div>
<span class="list-title"><?php  echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder) ; ?></span>
<?php if ($date = $this->params->get('list_show_date')) : ?>
<span class="list-date"><?php echo JHtml::_('grid.sort', 'COM_CONTENT_'.$date.'_DATE', 'a.created', $listDirn, $listOrder); ?></span>
<?php endif; ?>
<?php if ($this->params->get('list_show_author',1)) : ?>
<span class="list-author"><?php echo JHtml::_('grid.sort', 'JAUTHOR', 'author', $listDirn, $listOrder); ?></span>
<?php endif; ?>
<?php if ($this->params->get('list_show_hits',1)) : ?>
<span class="list-hits"><?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?></span>
<?php endif; ?>
</div>
<?php endif; ?>
<?php foreach ($this->items as $i => $article) : ?>
<?php if ($this->items[$i]->state == 0) : ?>
<div class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
<?php else: ?>
<div class="cat-list-row<?php echo $i % 2; ?>" >
<?php endif; ?>
<?php if (in_array($article->access, $this->user->getAuthorisedViewLevels())) : ?>
<span class="list-title"><a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid)); ?>"><?php echo $this->escape($article->title); ?></a></span>
<?php if ($this->params->get('list_show_date')) : ?>
<span class="list-date"><?php echo JHtml::_('date',$article->displayDate, $this->escape($this->params->get('date_format', JText::_('DATE_FORMAT_LC3')))); ?></span>
<?php endif; ?>
<?php if ($this->params->get('list_show_author',1) && !empty($article->author )) : ?>
<span class="list-author">
<?php $author =  $article->author ?>
<?php $author = ($article->created_by_alias ? $article->created_by_alias : $author);?>
<?php if (!empty($article->contactid ) &&  $this->params->get('link_author') == true):?>
<?php 	echo JHtml::_('link', JRoute::_('index.php?option=com_contact&view=contact&id='.$article->contactid), $author); ?>
<?php else :?>
<?php 	echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
<?php endif; ?>
</span>
<?php endif; ?>
<?php if ($this->params->get('list_show_hits',1)) : ?>
<span class="list-hits"><?php echo $article->hits; ?></span>
<?php endif; ?>
<?php else : // Show unauth links. ?>
<span>
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
<a href="<?php echo $fullURL; ?>" class="register"><?php echo JText::_( 'COM_CONTENT_REGISTER_TO_READ_MORE' ); ?></a>
</span>
<?php endif; ?>
</div>
<?php endforeach; ?>
</div>
<?php if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
<div class="pagination">
<?php if ($this->params->def('show_pagination_results', 1)) : ?>
<p class="counter"><?php echo $this->pagination->getPagesCounter(); ?></p>
<?php endif; ?>
<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<?php endif; ?>
</div>
</form>
<?php endif; ?>