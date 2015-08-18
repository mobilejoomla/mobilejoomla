<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php" method="post" data-ajax="true">
	<div class="ui-field-contain" class="search<?php echo $moduleclass_sfx ?>">
		<?php
			$output = '<label for="mod-search-searchword">'.$text.'</label>'
					.'<input type="text" data-type="search" name="searchword" id="mod-search-searchword" maxlength="'.$maxlength.'" class="inputbox'.$moduleclass_sfx.'" value="" data-inline="true" />';


			if ($button) :
				if ($imagebutton) :
					$button = '<input type="image" data-inline="true" value="'.$button_text.'" class="button'.$moduleclass_sfx.'" src="'.$img.'" onclick="this.form.searchword.focus();"/>';
				else :
					$button = '';
//					$button = '<input type="submit" data-inline="true" value="'.$button_text.'" class="button'.$moduleclass_sfx.'" onclick="this.form.searchword.focus();"/>';
				endif;
			else:
				$button = '';
			endif;

			switch ($button_pos) :
				case 'top' :
					$output = $button.'<br />'.$output;
					break;

				case 'bottom' :
					$output = $output.'<br />'.$button;
					break;

				case 'right' :
					$output = $output.$button;
					break;

				case 'left' :
				default :
					$output = $button.$output;
					break;
			endswitch;

			echo $output;
		?>
		<input type="hidden" name="task"   value="search" />
		<input type="hidden" name="option" value="com_search" />
		<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
	</div>
</form>