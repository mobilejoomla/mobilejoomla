<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version		###VERSION###
 * @license		###LICENSE###
 * @copyright	###COPYRIGHT###
 * @date		###DATE###
 */
defined('_JEXEC') or die('Restricted access');

?>
<?php if(!$is_submenu) : ?>
<div class="menu<?php echo htmlspecialchars($params->get('class_sfx')); ?>">
<?php endif; ?>
<ul class="<?php echo $params->get('layout') . $params->get('class_prefix') . htmlspecialchars($params->get('class_sfx')); ?>">
<?php
foreach($menu as $item)
{
	$is_active = $item->id == $active_id;

	$class = $item->anchor_css ? ' class="'.$item->anchor_css.'"' : ''; 
	if($item->type == 'separator')
		$outline = array('<span'.$class.'>', '</span>');
	else
		$outline = array('<a'.$class.' href="'.$item->flink.'"'.$item->accesskey.'>', '</a>');

	$text = $item->title;
	$img  = $item->menu_image ? '<img src="'.$item->menu_image.'" />' : '';
	switch($params->get('format'))
	{
	case 0: break;
	case 1: $text = $img; break;
	case 2: $text = $img.$text; break;
	case 3: $text = $text.$img; break;
	case 4: $text = $img.'<br />'.$text; break;
	case 5: $text = $text.'<br />'.$img; break;
	}
?>
<li<?php echo $is_active ? ' class="current"' : ''; ?>>
<?php echo $outline[0] . $text . $outline[1]; ?>
</li>
<?php
	if($is_active && count($submenu))
		JMobileMenuHelper::renderSubmenu($submenu, $params);
}
?>
</ul>
<?php if( ($is_vertical xor $is_submenu) || ((!$is_vertical) && (!$is_submenu) && !$params->get('has_submenu')) ) : ?>
</div>
<div class="clear"></div>
<?php endif; ?>
