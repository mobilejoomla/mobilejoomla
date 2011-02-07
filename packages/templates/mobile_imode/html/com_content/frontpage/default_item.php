<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$canEdit	= ($this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own'));
?>
<?php if ($this->item->state == 0) : ?>
<div class="system-unpublished">
<?php endif; ?>
<?php if ($canEdit || $this->item->params->get('show_title') || $this->item->params->get('show_pdf_icon') || $this->item->params->get('show_print_icon') || $this->item->params->get('show_email_icon')) : ?>
<div class="contentpaneopen<?php echo $this->item->params->get( 'pageclass_sfx' ); ?>">
<?php if ($this->item->params->get('show_title')) : ?>
<div class="contentheading<?php echo $this->item->params->get( 'pageclass_sfx' ); ?>">
<?php if ($this->item->params->get('link_titles') && $this->item->readmore_link != '') : ?>
<a href="<?php echo $this->item->readmore_link; ?>" class="contentpagetitle<?php echo $this->item->params->get( 'pageclass_sfx' ); ?>"><?php echo $this->item->title; ?></a>
<?php else : ?>
<?php echo $this->escape($this->item->title); ?>
<?php endif; ?>
</div>
<?php endif; ?>
<?php if ($canEdit) : ?>
<div align="right"><?php echo JHTML::_('icon.edit', $this->item, $this->item->params, $this->access); ?></div>
<?php endif; ?>
</div>
<?php endif; ?>
<?php  if (!$this->item->params->get('show_intro')) :
	echo $this->item->event->afterDisplayTitle;
endif; ?>
<?php echo $this->item->event->beforeDisplayContent; ?>
<div class="contentpaneopen<?php echo $this->item->params->get( 'pageclass_sfx' ); ?>">
<?php if (($this->item->params->get('show_section') && $this->item->sectionid) || ($this->item->params->get('show_category') && $this->item->catid)) : ?>
<div>
<?php if ($this->item->params->get('show_section') && $this->item->sectionid && isset($this->item->section)) : ?>
<span>
<?php if ($this->item->params->get('link_section')) : ?>
<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($this->item->sectionid)).'">'; ?>
<?php endif; ?>
<?php echo $this->item->section; ?>
<?php if ($this->item->params->get('link_section')) : ?>
<?php echo '</a>'; ?>
<?php endif; ?>
<?php if ($this->item->params->get('show_category')) : ?>
<?php echo ' - '; ?>
<?php endif; ?>
</span>
<?php endif; ?>
<?php if ($this->item->params->get('show_category') && $this->item->catid) : ?>
<span>
<?php if ($this->item->params->get('link_category')) : ?>
<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug, $this->item->sectionid)).'">'; ?>
<?php endif; ?>
<?php echo $this->item->category; ?>
<?php if ($this->item->params->get('link_category')) : ?>
<?php echo '</a>'; ?>
<?php endif; ?>
</span>
<?php endif; ?>
</div>
<?php endif; ?>
<?php if (($this->item->params->get('show_author')) && ($this->item->author != "")) : ?>
<div>
<span class="small">
<?php JText::printf( 'Written by', ($this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author) ); ?>
</span>
&nbsp;&nbsp;
</div>
<?php endif; ?>
<?php if ($this->item->params->get('show_create_date')) : ?>
<div class="createdate">
<?php echo JHTML::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2')); ?>
</div>
<?php endif; ?>
<?php if ($this->item->params->get('show_url') && $this->item->urls) : ?>
<div>
<a href="http://<?php echo $this->item->urls ; ?>"><?php echo $this->item->urls; ?></a>
</div>
<?php endif; ?>
<div>
<?php if (isset ($this->item->toc)) : ?>
<?php echo $this->item->toc; ?>
<?php endif; ?>
<?php echo $this->item->text; ?>
</div>
<?php if ( intval($this->item->modified) != 0 && $this->item->params->get('show_modify_date')) : ?>
<div class="modifydate">
<?php echo JText::sprintf('LAST_UPDATED2', JHTML::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
</div>
<?php endif; ?>
<?php if ($this->item->params->get('show_readmore') && $this->item->readmore) : ?>
<div>
<a href="<?php echo $this->item->readmore_link; ?>" class="readon<?php echo $this->item->params->get('pageclass_sfx'); ?>">
<?php		if ($this->item->readmore_register) :
				echo JText::_('Register to read more...');
			elseif ($readmore = $this->item->params->get('readmore')) :
				echo $readmore;
			else :
				echo JText::sprintf('Read more...');
			endif; ?></a>
</div>
<?php endif; ?>
</div>
<?php if ($this->item->state == 0) : ?>
</div>
<?php endif; ?>
<span class="article_separator">&nbsp;</span>
<?php echo $this->item->event->afterDisplayContent; ?>