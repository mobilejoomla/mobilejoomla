<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_articles_category
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<ul data-role="listview" class="category-module<?php echo $moduleclass_sfx; ?>">
<?php if ($grouped) : ?>
	<?php foreach ($list as $group_name => $group) : ?>
	<li>
		<h<?php echo $item_heading; ?>><?php echo $group_name; ?></h<?php echo $item_heading; ?>>
		<ul>
			<?php foreach ($group as $item) : ?>
<li>
<?php if ($item->displayHits) :?><span class="ui-li-count mod-articles-category-hits"><?php echo $item->displayHits; ?></span><?php endif; ?>
<?php if ($params->get('link_titles') == 1) : ?><a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>"><?php endif; ?>
<h<?php echo $item_heading+1; ?>><?php echo $item->title; ?></h<?php echo $item_heading+1; ?>>
<?php if ($params->get('show_author') || $item->displayCategoryTitle) :?>
<p>
<?php if ($params->get('show_author')) :?>
<strong class="mod-articles-category-writtenby"><?php echo $item->displayAuthorName; ?></strong>
<?php endif;?>
<?php if ($item->displayCategoryTitle) :?>
<span class="mod-articles-category-category">(<?php echo $item->displayCategoryTitle; ?>)</span>
<?php endif; ?>
</p>
<?php endif; ?>
<?php if ($params->get('show_introtext')) :?>
<p class="mod-articles-category-introtext"><?php echo $item->displayIntrotext; ?></p>
<?php endif; ?>
<?php if ($params->get('show_readmore')) :?>
<p class="mod-articles-category-readmore"><a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
<?php if ($item->params->get('access-view')== FALSE) :
		echo rtrim(JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE'), ' .');
	elseif ($readmore = $item->alternative_readmore) :
		echo $readmore;
		echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit'));
		if ($params->get('show_readmore_title', 0) != 0) :
			echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
		endif;
	elseif ($params->get('show_readmore_title', 0) == 0) :
		echo rtrim(JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'), ' .');
	else :
		echo JText::_('MOD_ARTICLES_CATEGORY_READ_MORE');
		echo JHtml::_('string.truncate', ($item->title), $params->get('readmore_limit'));
	endif; ?>
</a></p>
<?php endif; ?>
<?php if ($item->displayDate) : ?>
<p class="ui-li-aside mod-articles-category-date"><?php echo $item->displayDate; ?></p>
<?php endif; ?>
<?php if ($params->get('link_titles') == 1) : ?>
</a>
<?php endif; ?>
</li>
			<?php endforeach; ?>
		</ul>
	</li>
	<?php endforeach; ?>
<?php else : ?>
	<?php foreach ($list as $item) : ?>
		<li>
		<?php if ($params->get('link_titles') == 1) : ?>
			<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
		<?php endif; ?>
		   	<h<?php echo $item_heading; ?>>
				<?php echo $item->title; ?>
			</h<?php echo $item_heading; ?>>
			<?php if ($item->displayHits) :?>
				<span class="ui-li-count mod-articles-category-hits"><?php echo $item->displayHits; ?></span>
			<?php endif; ?>

		<?php if ($params->get('show_author') || $item->displayCategoryTitle) :?>
		<p>
		<?php if ($params->get('show_author')) :?>
			<strong class="mod-articles-category-writtenby">
			<?php echo $item->displayAuthorName; ?>
			</strong>
		<?php endif;?>
		<?php if ($item->displayCategoryTitle) :?>
			<span class="mod-articles-category-category">
			(<?php echo $item->displayCategoryTitle; ?>)
			</span>
		<?php endif; ?>
		</p>
		<?php endif; ?>

		<?php if ($item->displayDate) : ?>
			<p class="ui-li-aside mod-articles-category-date"><?php echo $item->displayDate; ?></p>
		<?php endif; ?>

		<?php if ($params->get('show_introtext')) :?>
			<p class="mod-articles-category-introtext">
			<?php echo $item->displayIntrotext; ?>
			</p>
		<?php endif; ?>

		<?php if ($params->get('show_readmore')) :?>
			<p class="mod-articles-category-readmore">
				<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
				<?php if ($item->params->get('access-view')== FALSE) :
						echo rtrim(JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE'), ' .');
					elseif ($readmore = $item->alternative_readmore) :
						echo $readmore;
						echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit'));
					elseif ($params->get('show_readmore_title', 0) == 0) :
						echo rtrim(JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'), ' .');
					else :
						echo JText::_('MOD_ARTICLES_CATEGORY_READ_MORE');
						echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit'));
					endif; ?>
				</a>
			</p>
		<?php endif; ?>
	   	<?php if ($params->get('link_titles') == 1) : ?>
			</a>
		<?php endif; ?>
	</li>
	<?php endforeach; ?>
<?php endif; ?>
</ul>
