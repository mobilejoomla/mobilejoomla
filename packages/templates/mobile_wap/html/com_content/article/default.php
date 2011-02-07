<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$canEdit = ($this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own'));
?>
<?php if ($this->params->get('show_page_title', 1) && $this->params->get('page_title') != $this->article->title) : ?>
<strong><?php echo $this->escape($this->params->get('page_title')); ?></strong><br />
<?php endif; ?>
<?php if ($canEdit || $this->params->get('show_title') || $this->params->get('show_pdf_icon') || $this->params->get('show_print_icon') || $this->params->get('show_email_icon')) : ?>
<?php if ($this->params->get('show_title')) : ?>
<strong><?php if ($this->params->get('link_titles') && $this->article->readmore_link != '') : ?>
<a href="<?php echo $this->article->readmore_link; ?>"><?php echo $this->escape($this->article->title); ?></a>
<?php else : ?>
<?php echo $this->escape($this->article->title); ?>
<?php endif; ?>
</strong><br />
<?php endif; ?>
<?php if (!$this->print) : ?>
<?php if ($canEdit) : ?>
<?php echo JHTML::_('icon.edit', $this->article, $this->params, $this->access); ?><br />
<?php endif; ?>
<?php endif; ?>
<br />
<?php endif; ?>
<?php  if (!$this->params->get('show_intro')) :
echo $this->article->event->afterDisplayTitle;
endif; ?>
<?php echo $this->article->event->beforeDisplayContent; ?>
<?php if (($this->params->get('show_section') && $this->article->sectionid) || ($this->params->get('show_category') && $this->article->catid)) : ?>
<?php if ($this->params->get('show_section') && $this->article->sectionid && isset($this->article->section)) : ?>
<small>
<?php if ($this->params->get('link_section')) : ?><?php echo '<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($this->article->sectionid)).'">'; ?><?php endif; ?>
<?php echo $this->article->section; ?>
<?php if ($this->params->get('link_section')) : ?><?php echo '</a>'; ?><?php endif; ?>
<?php if ($this->params->get('show_category')) : ?><?php echo ' - '; ?><?php endif; ?>
</small>
<?php endif; ?>
<?php if ($this->params->get('show_category') && $this->article->catid) : ?>
<small>
<?php if ($this->params->get('link_category')) : ?><?php echo '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->article->catslug, $this->article->sectionid)).'">'; ?><?php endif; ?>
<?php echo $this->article->category; ?>
<?php if ($this->params->get('link_category')) : ?><?php echo '</a>'; ?><?php endif; ?>
</small>
<?php endif; ?>
<br />
<?php endif; ?>
<?php if (($this->params->get('show_author')) && ($this->article->author != "")) : ?>
<small><?php JText::printf( 'Written by', ($this->article->created_by_alias ? $this->article->created_by_alias : $this->article->author) ); ?></small><br />
<?php endif; ?>
<?php if ($this->params->get('show_create_date')) : ?>
<small><?php echo JHTML::_('date', $this->article->created, JText::_('DATE_FORMAT_LC2')) ?></small><br />
<?php endif; ?>
<?php if ($this->params->get('show_url') && $this->article->urls) : ?>
<a href="http://<?php echo $this->article->urls ; ?>"><?php echo $this->article->urls; ?></a><br />
<?php endif; ?>
<?php if (isset ($this->article->toc)) : ?><?php echo $this->article->toc; ?><?php endif; ?>
<?php echo $this->article->text; ?>
<br />
<?php if ( intval($this->article->modified) !=0 && $this->params->get('show_modify_date')) : ?>
<small><?php echo JText::sprintf('LAST_UPDATED2', JHTML::_('date', $this->article->modified, JText::_('DATE_FORMAT_LC2'))); ?></small><br />
<?php endif; ?>
<br />
<?php echo $this->article->event->afterDisplayContent; ?>