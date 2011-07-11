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
<ul class="<?php echo $params->get('classprefix') . htmlspecialchars($params->get('class_sfx')); ?>">
<?php
foreach($menu as $item)
{
	$is_active = $item->id == $active_id;
	if($item->type == 'separator')
		$outline = array('<span>', '</span>');
	else
		$outline = array('<a href="'.$item->flink.'">', '</a>');
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
<?php
	echo $outline[0] . $text . $outline[1];
	if($is_active && count($submenu))
	{
		$prev = $params->get('class_prefix');
		$params->set('class_prefix', 'submenu');
		JMobileMenuHelper::renderMenu($submenu, $params);
		$params->set('class_prefix', $prev);
	}
?>
</li>
<?php } ?>
</ul>