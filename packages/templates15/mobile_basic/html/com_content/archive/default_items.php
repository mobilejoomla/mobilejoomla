<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$params =& $this->params;
?>
<ul id="archive-items" data-role="listview">
<?php foreach ($this->items as $i=>$item) : ?>
<li class="row<?php echo $i % 2; ?>">
<h2>
<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug)); ?>"><?php echo $this->escape($item->title); ?></a>
</h2>
<?php if (($params->get('show_author')) or ($params->get('show_section')) or ($params->get('show_category')) or ($params->get('show_create_date'))) : ?>
<div class="article-info">
<?php endif; ?>
<?php if ($params->get('show_section') && $item->sectionid && isset($item->section)) : ?>
<div class="parent-category-name">
<?php $title = $this->escape($item->section); ?>
<?php if ($params->get('link_section')) : ?>
<?php 	echo '<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($item->sectionid)).'">'.$title.'</a>'; ?>
<?php else : ?>
<?php 	echo $title; ?>
<?php endif; ?>
</div>
<?php endif; ?>
<?php if ($params->get('show_category') && $item->catid) : ?>
<div class="category-name">
<?php $title = $this->escape($item->category);?>
<?php if ($params->get('link_category')) : ?>
<?php 	echo '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($item->catslug, $item->sectionid)).'">'.$title.'</a>'; ?>
<?php else : ?>
<?php 	echo $title; ?>
<?php endif; ?>
</div>
<?php endif; ?>
<?php if ($params->get('show_create_date')) : ?>
<div class="create"><?php echo JText::_('Created') .': '.  JHTML::_( 'date', $item->created, JText::_('DATE_FORMAT_LC2')); ?></div>
<?php endif; ?>
<?php if ($params->get('show_author') && !empty($item->author )) : ?>
<div class="createdby"> 
<?php $author =  $item->author; ?>
<?php $author = ($item->created_by_alias ? $item->created_by_alias : $author);?>
<?php echo JText::_('Author').': '.$this->escape($author); ?>
</div>
<?php endif; ?>	
<?php if (($params->get('show_author')) or ($params->get('show_section')) or ($params->get('show_category')) or ($params->get('show_create_date'))) :?>
</div>
<?php endif; ?>
<div class="intro"><?php echo substr(strip_tags($item->introtext), 0, 255);  ?>...</div>
</li>
<?php endforeach; ?>
</ul>
<div class="pagination">
<?php echo $this->pagination->getPagesLinks(); ?>
</div>
