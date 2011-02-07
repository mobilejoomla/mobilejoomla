<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php if ($this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own')) : ?>
<?php echo JHTML::_('icon.edit', $this->item, $this->item->params, $this->access); ?>
<br />
<?php endif; ?>
<?php if ($this->item->params->get('show_title') || $this->item->params->get('show_pdf_icon') || $this->item->params->get('show_print_icon') || $this->item->params->get('show_email_icon')) : ?>
<?php if ($this->item->params->get('show_title')) : ?>
<?php if ($this->item->params->get('link_titles') && $this->item->readmore_link != '') : ?>
<strong><a href="<?php echo $this->item->readmore_link; ?>"><?php echo $this->escape($this->item->title); ?></a></strong>
<?php else : ?>
<strong><?php echo $this->escape($this->item->title); ?></strong>
<?php endif; ?>
<br />
<?php endif; ?>
<br />
<?php endif; ?>
<?php  if (!$this->item->params->get('show_intro')) :
	echo $this->item->event->afterDisplayTitle;
endif; ?>
<?php echo $this->item->event->beforeDisplayContent; ?>
<?php if (($this->item->params->get('show_section') && $this->item->sectionid) || ($this->item->params->get('show_category') && $this->item->catid)) : ?>
<?php if ($this->item->params->get('show_section') && $this->item->sectionid && isset($this->item->section)) : ?>
<small>
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
</small>
<?php endif; ?>
<?php if ($this->item->params->get('show_category') && $this->item->catid) : ?>
<small>
<?php if ($this->item->params->get('link_category')) : ?>
<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug, $this->item->sectionid)).'">'; ?>
<?php endif; ?>
<?php echo $this->item->category; ?>
<?php if ($this->item->params->get('link_category')) : ?>
<?php echo '</a>'; ?>
<?php endif; ?>
</small>
<?php endif; ?>
<br />
<?php endif; ?>
<?php if (($this->item->params->get('show_author')) && ($this->item->author != "")) : ?>
<small>
<?php JText::printf( 'Written by', ($this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author) ); ?>
</small>
<br />
<?php endif; ?>
<?php if ($this->item->params->get('show_create_date')) : ?>
<small>
<?php echo JHTML::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2')); ?>
</small><br />
<?php endif; ?>
<?php if ($this->item->params->get('show_url') && $this->item->urls) : ?>
<a href="http://<?php echo $this->item->urls ; ?>"><?php echo $this->item->urls; ?></a>
<br />
<?php endif; ?>
<?php if (isset ($this->item->toc)) : ?>
<?php echo $this->item->toc; ?>
<?php endif; ?>
<?php echo $this->item->text; ?>
<br />
<?php if ( intval($this->item->modified) != 0 && $this->item->params->get('show_modify_date')) : ?>
<small>
<?php echo JText::sprintf('LAST_UPDATED2', JHTML::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
</small>
<?php endif; ?>
<?php if ($this->item->params->get('show_readmore') && $this->item->readmore) : ?>
<a href="<?php echo $this->item->readmore_link; ?>">
<?php if ($this->item->readmore_register) :
				echo JText::_('Register to read more...');
			elseif ($readmore = $this->item->params->get('readmore')) :
				echo $readmore;
			else :
				echo JText::sprintf('Read more...');
			endif; ?></a>
<?php endif; ?>
<br />
<br />
<?php echo $this->item->event->afterDisplayContent; ?>