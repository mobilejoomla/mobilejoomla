<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php if ($this->params->get('show_page_title', 1)) : ?>
<div class="componentheading<?php echo $this->params->get('pageclass_sfx') ?>">
<?php echo $this->escape($this->params->get('page_title')); ?>
</div>
<?php endif; ?>
<div class="blog<?php echo $this->params->get('pageclass_sfx') ?>">
<?php if ($this->params->def('num_leading_articles', 1)) : ?>
<div>
<?php for ($i = $this->pagination->limitstart; $i < ($this->pagination->limitstart + $this->params->get('num_leading_articles')); $i++) : ?>
<?php if ($i >= $this->total) : break; endif; ?>
<div>
<?php
			$this->item =& $this->getItem($i, $this->params);
			echo $this->loadTemplate('item');
?>
</div>
<?php endfor; ?>
</div>
<?php else : $i = $this->pagination->limitstart; endif; ?>
<?php
$startIntroArticles = $this->pagination->limitstart + $this->params->get('num_leading_articles');
$numIntroArticles = $startIntroArticles + $this->params->get('num_intro_articles', 4);
if (($numIntroArticles != $startIntroArticles) && ($i < $this->total)) : ?>
<div>
<?php
			if ($this->params->def('multi_column_order',1)) : // order across as before
				    $rows = (int) $this->params->get('num_intro_articles', 4);
?>
<div class="article_column">
<?php
				for ($y = 0; $y < $rows; $y ++) :
					$target = $i + $y;
					if ($target < $this->total && $target < ($numIntroArticles)) :
						$this->item =& $this->getItem($target, $this->params);
						echo $this->loadTemplate('item');
					endif;
				endfor;
?></div>
<?php 
						$i = $i + $this->params->get('num_intro_articles') ; 
			else : // otherwise, order down columns, like old category blog
?>
<div class="article_column">
<?php			for ($y = 0; $y < $this->params->get('num_intro_articles'); $y ++) :
					if ($i < $this->total && $i < ($numIntroArticles)) :
						$this->item =& $this->getItem($i, $this->params);
						echo $this->loadTemplate('item');
						$i ++;
					endif;
				endfor; ?>
</div>
<?php
		endif;?>
</div>
<?php endif; ?>
<?php if ($this->params->def('num_links', 4) && ($i < $this->total)) : ?>
<div class="blog_more<?php echo $this->params->get('pageclass_sfx') ?>">
<?php
				$this->links = array_splice($this->items, $i - $this->pagination->limitstart);
				echo $this->loadTemplate('links');
?>
</div>
<?php endif; ?>
<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1)) : ?>
<div align="center">
<?php echo $this->pagination->getPagesLinks(); ?>
<br /><br />
</div>
<?php if ($this->params->def('show_pagination_results', 1)) : ?>
<div align="center">
<?php echo $this->pagination->getPagesCounter(); ?>
</div>
<?php endif; ?>
<?php endif; ?>
</div>