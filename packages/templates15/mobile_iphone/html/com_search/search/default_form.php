<?php

defined('_JEXEC') or die('Restricted access');

$state			= $this->get('state');
$custom_lists		= array();
$searchphrases 		= array();
$searchphrases[] 	= JHTML::_('select.option',  'all', JText::_( 'All words' ) );
$searchphrases[] 	= JHTML::_('select.option',  'any', JText::_( 'Any words' ) );
$searchphrases[] 	= JHTML::_('select.option',  'exact', JText::_( 'Exact phrase' ) );
$custom_lists['searchphrase' ] = JHTML::_('select.genericlist',  $searchphrases, 'searchphrase', '', 'value', 'text', $state->get('match') );

?>

<form id="searchForm" action="<?php echo JRoute::_( 'index.php?option=com_search' );?>" method="post" name="searchForm">
	<div class="search<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<ul class="toolbar edgetoedge grayToolbar">
			<li>
				<table>
					<tr>
						<td class="text">
							<input type="text" name="searchword" id="search_searchword" size="20" maxlength="20" value="<?php echo $this->escape($this->searchword); ?>" class="left inputbox" />
						</td>
						<td class="button">
							<button name="Search" onclick="this.form.submit()" class="right button"><?php echo JText::_( 'Search' );?></button>
						</td>
					</tr>
				</table>
			</li>
		</ul>
		<div id="advancedsearch" style="display:none;">
			<ul>
				<li><?php echo $custom_lists['searchphrase']; ?></li>
				<li><?php echo $this->lists['ordering']; ?></li>
			</ul>
			<ul>
				<?php if ($this->params->get( 'search_areas', 1 )) : ?>
					<li><?php echo JText::_( 'Search Only' );?>:</li>
					<?php foreach ($this->searchareas['search'] as $val => $txt) :
						$checked = is_array( $this->searchareas['active'] ) && in_array( $val, $this->searchareas['active'] ) ? 'checked="checked"' : '';
					?>
					<li>
						<input type="checkbox" name="areas[]" value="<?php echo $val;?>" id="area_<?php echo $val;?>" <?php echo $checked;?> title="<?php echo JText::_($txt); ?>" />
					</li>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
<?php if ($this->total > 0) : ?>
		<ul style="display:none;">
			<li><?php echo JText::_('Display Num'); ?>:</li>
			<li><?php echo $this->pagination->getLimitBox(); ?></li>

			<?php
			$pagescounter = $this->pagination->getPagesCounter();
			if (!empty($pagescounter)) {
				?>
				<li><?php echo $pagescounter; ?></li>
				<?php
			}
			?>
		</ul>
<?php endif ?>
	</div>

<?php if ($this->total === 0) : ?>
	<div class="info">
		<?php echo $this->result; ?>
	</div>
<?php endif; ?>

<?php /*
	<table class="searchintro<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<tr>
		<td colspan="3" >
			<br />
			<?php echo JText::_( 'Search Keyword' ) .' <b>'. $this->escape($this->searchword) .'</b>'; ?>
		</td>
	</tr>
	<tr>
		<td>
			<br />
			<?php echo $this->result; ?>
		</td>
	</tr>
</table>
 */?>

<input type="hidden" name="task"   value="search" />
</form>
