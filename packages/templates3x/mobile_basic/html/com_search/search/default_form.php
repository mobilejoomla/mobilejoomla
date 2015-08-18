<?php
/**
 * @version		$Id: default_form.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_search
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<form id="searchForm" action="<?php echo JRoute::_('index.php?option=com_search');?>" method="post" data-ajax="true">
	<fieldset class="ui-field-contain word">
		<label for="search-searchword"><?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?></label>
		<input type="text" data-type="search" name="searchword" id="search-searchword" size="30" maxlength="20" value="<?php echo $this->escape(isset($this->origkeyword) ? $this->origkeyword : $this->searchword); ?>" class="inputbox" />
<?php /*		<button name="Search" onclick="this.form.submit()" class="button" data-inline="true">< ?php echo JText::_('COM_SEARCH_SEARCH');? ></button> */ ?>
	</fieldset>

	<div class="searchintro<?php echo $this->params->get('pageclass_sfx'); ?>">
		<?php if (!empty($this->searchword)):?>
		<p><?php echo JText::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', $this->total);?></p>
		<?php endif;?>
	</div>

	<fieldset class="phrases-box" data-role="controlgroup" data-type="horizontal">
		<legend><?php echo JText::_('COM_SEARCH_FOR');?></legend>
		<?php echo $this->lists['searchphrase']; ?>
	</fieldset>

	<fieldset class="ordering-box" data-role="controlgroup">
		<label for="ordering" class="ordering"><?php echo JText::_('COM_SEARCH_ORDERING');?></label>
		<?php echo $this->lists['ordering'];?>
	</fieldset>

	<?php if ($this->params->get('search_areas', 1)) : ?>
		<fieldset class="only" data-role="controlgroup">
		<legend><?php echo JText::_('COM_SEARCH_SEARCH_ONLY');?></legend>
		<?php foreach ($this->searchareas['search'] as $val => $txt) :
			$checked = is_array($this->searchareas['active']) && in_array($val, $this->searchareas['active']) ? 'checked="checked"' : '';
		?>
			<label for="area-<?php echo $val;?>"><input type="checkbox" name="areas[]" value="<?php echo $val;?>" id="area-<?php echo $val;?>" <?php echo $checked;?> /> <?php echo JText::_($txt); ?></label>
		<?php endforeach; ?>
		</fieldset>
	<?php endif; ?>

<?php if ($this->total > 0) : ?>
	<div class="form-limit" data-role="controlgroup">
		<label for="limit"><?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?></label>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
<?php endif; ?>
	<input type="hidden" name="task" value="search" />
</form>
