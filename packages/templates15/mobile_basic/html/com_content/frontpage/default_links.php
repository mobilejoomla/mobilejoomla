<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div data-role="collapsible">
<h3><?php echo JText::_( 'More Articles...' ); ?></h3>
<ol data-role="listview">
<?php foreach ($this->links as &$item) : ?>
<li>
<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid)); ?>"><?php echo $this->escape($item->title); ?></a>
</li>
<?php endforeach; unset($item); ?>
</ol>
</div>