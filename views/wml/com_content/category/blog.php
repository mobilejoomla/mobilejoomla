<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$cparams =& JComponentHelper::getParams('com_media');
?>
<?php if ($this->params->get('show_page_title', 1)) : ?>
<strong><?php echo $this->escape($this->params->get('page_title')); ?></strong>
<br />
<?php endif; ?>
<?php if ($this->params->def('show_description', 1) || $this->params->def('show_description_image', 1)) :?>
<?php if ($this->params->get('show_description_image') && $this->category->image) : ?>
<img src="<?php echo $this->baseurl . '/' . $cparams->get('image_path') . '/'. $this->category->image;?>" align="<?php echo $this->category->image_position;?>" hspace="6" alt="" />
<?php endif; ?>
<?php if ($this->params->get('show_description') && $this->category->description) : ?>
<?php echo $this->category->description; ?>
<?php endif; ?>
<br /><br />
<?php endif; ?>
<?php if ($this->params->get('num_leading_articles')) : ?>
<?php for ($i = $this->pagination->limitstart; $i < ($this->pagination->limitstart + $this->params->get('num_leading_articles')); $i++) : ?>
<?php if ($i >= $this->total) : break; endif; ?>
<?php
			$this->item =& $this->getItem($i, $this->params);
			echo $this->loadTemplate('item');
?>
<br />
<?php endfor; ?>
<br />
<?php else : $i = $this->pagination->limitstart; endif; ?>
<?php
$startIntroArticles = $this->pagination->limitstart + $this->params->get('num_leading_articles');
$numIntroArticles = $startIntroArticles + $this->params->get('num_intro_articles');
if (($numIntroArticles != $startIntroArticles) && ($i < $this->total)) : ?>
<?php
			if ($this->params->def('multi_column_order', 0)) : // order across, like front page
					$rows = (int) $this->params->get('num_intro_articles', 4);
						for ($y = 0; $y < $rows; $y ++) :
							$target = $i + $y;
							if ($target < $this->total && $target < ($numIntroArticles)) :
								$this->item =& $this->getItem($target, $this->params);
								echo $this->loadTemplate('item');
							endif;
						endfor;
?><br />
<?php
						$i = $i + $this->params->get('num_intro_articles') ; 
			else : // otherwise, order down, same as before (default behaviour)
				for ($y = 0; $y < $this->params->get('num_intro_articles'); $y ++) :
					if ($i < $this->total && $i < ($numIntroArticles)) :
						$this->item =& $this->getItem($i, $this->params);
						echo $this->loadTemplate('item');
						$i ++;
					endif;
				endfor; ?>
<br />
<?php		endif; ?> 
<br />
<?php endif; ?>
<?php if ($this->params->get('num_links') && ($i < $this->total)) : ?>
<?php
				$this->links = array_splice($this->items, $i - $this->pagination->limitstart);
				echo $this->loadTemplate('links');
?>
<br />
<?php endif; ?>
<?php if ($this->params->get('show_pagination')) : ?>
<?php echo $this->pagination->getPagesLinks(); ?><br /><br />
<?php endif; ?>
<?php if ($this->params->get('show_pagination_results')) : ?>
<?php echo $this->pagination->getPagesCounter(); ?><br />
<?php endif; ?>