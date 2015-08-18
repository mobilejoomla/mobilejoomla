<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="item-page<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
<?php if ($this->params->get('show_title')) : ?>
<h2>
<?php if ($this->params->get('link_titles') && $this->article->readmore_link != '') : ?>
<a href="<?php echo $this->article->readmore_link; ?>"><?php echo $this->escape($this->article->title); ?></a>
<?php else : ?>
<?php echo $this->escape($this->article->title); ?>
<?php endif; ?>
</h2>
<?php endif; ?>
<?php  if (!$this->params->get('show_intro')) :
	echo $this->article->event->afterDisplayTitle;
endif; ?>
<?php echo $this->article->event->beforeDisplayContent; ?>
<?php $useDefList = (($this->params->get('show_author')) OR ($this->params->get('show_category')) OR ($this->params->get('show_section'))
	OR ($this->params->get('show_create_date')) OR ($this->params->get('show_modify_date'))); ?>
<?php if ($useDefList) : ?>
<div class="article-info">
<?php endif; ?>
<?php if ($this->params->get('show_section') && $this->article->sectionid && isset($this->article->section)) : ?>
<div class="parent-category-name">
<?php $title = $this->escape($this->article->section); ?>
<?php if ($this->params->get('link_section')) : ?>
<?php 	echo '<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($this->article->sectionid)).'">'.$title.'</a>'; ?>
<?php else : ?>
<?php 	echo $title; ?>
<?php endif; ?>
</div>
<?php endif; ?>
<?php if ($this->params->get('show_category')) : ?>
<div class="category-name">
<?php $title = $this->escape($this->article->category); ?>
<?php if ($this->params->get('link_category')) : ?>
<?php 	echo '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->article->catslug, $this->article->sectionid)).'">'.$title.'</a>'; ?>
<?php else : ?>
<?php 	echo $title; ?>
<?php endif; ?>
</div>
<?php endif; ?>
<?php if ($this->params->get('show_create_date')) : ?>
<div class="create"><?php echo JHTML::_('date', $this->article->created, JText::_('DATE_FORMAT_LC2')) ?></div>
<?php endif; ?>
<?php if ((int)$this->article->modified !==0 && $this->params->get('show_modify_date')) : ?>
<div class="modified"><?php echo JText::sprintf('LAST_UPDATED2', JHTML::_('date', $this->article->modified, JText::_('DATE_FORMAT_LC2'))); ?></div>
<?php endif; ?>
<?php if ($this->params->get('show_author') && !empty($this->article->author)) : ?>
<div class="createdby"> 
<?php $author =  $this->article->author; ?>
<?php $author = ($this->article->created_by_alias ? $this->article->created_by_alias : $author);?>
<?php echo JText::printf( 'Written by', $this->escape($author) ); ?>
</div>
<?php endif; ?>	
<?php if ($this->params->get('show_url') && $this->article->urls) : ?>
<a href="http://<?php echo $this->article->urls ; ?>" target="_blank"><?php echo $this->escape($this->article->urls); ?></a>
<?php endif; ?>
<?php if ($useDefList) : ?>
</div>
<?php endif; ?>
<?php if (isset ($this->article->toc)) : ?>
<?php echo $this->article->toc; ?>
<?php endif; ?>
<?php echo $this->article->text; ?>
<?php echo $this->article->event->afterDisplayContent; ?>
</div>