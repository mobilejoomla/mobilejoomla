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

$separator = $is_vertical ? "\n" : ' | ';

echo $is_vertical ? '<ul>' : '<p>';

$firstpass = true;
foreach($menu as $item)
{
	$is_active = $item->id == $active_id;
	if($item->type == 'separator')
		$outline = array('', '');
	else
		$outline = array('<a href="'.$item->flink.'">', '</a>');
	$text = $item->title;
	if($is_active)
		$text = '<b>'. $text .'</b>';
	$img  = $item->menu_image ? '<img src="'.$item->menu_image.'" border="0" />' : '';
	switch($params->get('format'))
	{
	case 0: break;
	case 1: $text = $img; break;
	case 2: $text = $img.$text; break;
	case 3: $text = $text.$img; break;
	case 4: $text = $img.'<br />'.$text; break;
	case 5: $text = $text.'<br />'.$img; break;
	}

	if($firstpass) $firstpass = false;
	else echo $separator;

	if($is_vertical) echo '<li>';

	echo $outline[0] . $text . $outline[1];

	if($is_vertical) echo '</li>';

	if($is_active && count($submenu))
		JMobileMenuHelper::renderSubmenu($submenu, $params);
}

echo $is_vertical ? '</ul>' : '</p>';
