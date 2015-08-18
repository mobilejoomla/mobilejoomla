<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php if ($this->item->state == 0) : ?>
<div class="system-unpublished">
<?php endif; ?>

<?php if ($this->item->params->get('show_title')) : ?>
<h2>
<?php if ($this->item->params->get('link_titles') && $this->item->readmore_link != '') : ?>
<a href="<?php echo $this->item->readmore_link; ?>"><?php echo $this->escape($this->item->title); ?></a>
<?php else : ?>
<?php echo $this->escape($this->item->title); ?>
<?php endif; ?>
</h2>
<?php endif; ?>

<?php  if (!$this->item->params->get('show_intro')) :
	echo $this->item->event->afterDisplayTitle;
endif; ?>
<?php echo $this->item->event->beforeDisplayContent; ?>

<?php if (($this->item->params->get('show_author')) or ($this->item->params->get('show_category')) or ($this->item->params->get('show_create_date')) or ($this->item->params->get('show_section'))) : ?>
<div class="article-info">
<?php endif; ?>

		<?php if ($this->item->params->get('show_section') && $this->item->sectionid && isset($this->section->title)) : ?>
<div class="parent-category-name">
<?php $title = $this->escape($this->section->title); ?>
<?php if ($this->item->params->get('link_parent_category')) : ?>
<?php 	echo '<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($this->item->sectionid)).'">'.$title.'</a>'; ?>
<?php else : ?>
<?php 	echo $title; ?>
<?php endif; ?>
</div>
		<?php endif; ?>

		<?php if ($this->item->params->get('show_category') && $this->item->catid) : ?>
<div class="category-name">
<?php $title = $this->escape($this->item->category);?>
<?php if ($this->item->params->get('link_category')) : ?>
<?php 	echo '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug, $this->item->sectionid)).'">'.$title.'</a>'; ?>
<?php else : ?>
<?php 	echo $title; ?>
<?php endif; ?>
</div>
		<?php endif; ?>
<?php if ($this->item->params->get('show_create_date')) : ?>
<div class="create"><?php echo JHTML::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2')); ?></div>
<?php endif; ?>
<?php if ( (int)$this->item->modified !== 0 && $this->item->params->get('show_modify_date')) : ?>
<div class="modified"><?php echo JText::sprintf('LAST_UPDATED2', JHTML::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?></div>
<?php endif; ?>
<?php if (($this->item->params->get('show_author')) && ($this->item->author != "")) : ?>
<div class="createdby"> 
<?php $author =  $this->item->author; ?>
<?php $author = ($this->item->created_by_alias ? $this->item->created_by_alias : $author);?>
<?php JText::printf( 'Written by', $this->escape($author)); ?>
</div>
<?php endif; ?>
<?php if ($this->item->params->get('show_url') && $this->item->urls) : ?>
<a href="http://<?php echo $this->escape($this->item->urls) ; ?>" target="_blank"><?php echo $this->escape($this->item->urls); ?></a>
<?php endif; ?>

<?php if (($this->item->params->get('show_author')) or ($this->item->params->get('show_category')) or ($this->item->params->get('show_create_date')) or ($this->item->params->get('show_section'))) : ?>
</div>
<?php endif; ?>

<?php echo $this->item->text; ?>

<?php if ($this->item->params->get('show_readmore') && $this->item->readmore) : ?>
<p class="readmore">
	<a href="<?php echo $this->item->readmore_link; ?>">
		<?php if ($this->item->readmore_register) :
			echo rtrim(JText::_('Register to read more...'), ' .');
		elseif ($readmore = $this->item->params->get('readmore')) :
			echo $readmore;
		else :
			echo rtrim(JText::sprintf('Read more...'), ' .');
		endif; ?></a>
</p>
<?php endif; ?>

<?php if ($this->item->state == 0) : ?>
</div>
<?php endif; ?>

<div class="item-separator"></div>
<?php echo $this->item->event->afterDisplayContent; ?>
