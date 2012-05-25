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

/** @var $active_id integer */
/** @var $is_vertical bool */
/** @var $is_submenu bool */
/** @var $menu array */
/** @var $params JRegistry */
/** @var $submenu array */

$separator = $is_vertical ? '<br/>' : ' | ';

if($is_submenu)
	echo '<br/>';

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

	if($firstpass)
		$firstpass = false;
	else
		echo $separator;

	if($is_submenu && $is_vertical)
		echo '- ';

	echo $outline[0] . $text . $outline[1];

	if($is_active && count($submenu))
		JMobileMenuHelper::renderSubmenu($submenu, $params);
}
