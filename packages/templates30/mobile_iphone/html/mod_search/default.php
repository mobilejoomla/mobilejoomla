<?php
// no direct access
defined('_JEXEC') or die;
?>
<form action="<?php echo JRoute::_('index.php');?>" method="post">
<div class="search<?php echo $moduleclass_sfx;?>">
<ul class="toolbar edgetoedge grayToolbar">
<li>
<?php
	$output = '<input name="searchword" maxlength="'.$maxlength.'" class="inputbox'.$moduleclass_sfx.'" type="text" size="'.$width.'" placeholder="'.$text.'" value="'.($text==$label?'':$text).'" />';
	$button = '<input type="submit" value="'.$button_text.'" class="button'.$moduleclass_sfx.'" />';

	switch ($button_pos) :
		case 'none' :
			$output = '<table><tr><td class="text">'.$output.'</td></tr></table>';
			break;
		case 'right' :
			$output = '<table><tr><td class="text">'.$output.'</td><td class="button">'.$button.'</td></tr></table>';
			break;
		case 'left' :
		default :
			$output = '<table><tr><td class="button">'.$button.'</td><td class="text">'.$output.'</td></tr></table>';
			break;
	endswitch;

	echo $output;
?>
</li>
</ul>
</div>
<input type="hidden" name="task"   value="search" />
<input type="hidden" name="option" value="com_search" />
<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
</form> 