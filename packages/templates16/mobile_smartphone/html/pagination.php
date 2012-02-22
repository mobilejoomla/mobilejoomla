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

function pagination_list_footer($list)
{
	$html = '<div class="list-footer">';
	$html .= "\n".'<div class="limit">'.JText::_('JGLOBAL_DISPLAY_NUM').$list['limitfield'].'</div>';
	$html .= $list['pageslinks'];
	$html .= "\n".'<div class="counter">'.$list['pagescounter'].'</div>';
	$html .= "\n".'<input type="hidden" name="'.$list['prefix'].'limitstart" value="'.$list['limitstart'].'" />';
	$html .= "\n".'</div>';
	return $html;
}

function pagination_list_render($list)
{
	$html  = $list['start']['data'].' '.$list['previous']['data'];
	foreach( $list['pages'] as $page )
		$html .= ' '.$page['data'];
	$html .= ' '.$list['next']['data'].' '.$list['end']['data'];
	return $html;
}

function pagination_item_active(&$item)
{
	return '<a title="'.$item->text.'" href="'.$item->link.'" class="pagenav">'.$item->text.'</a>';
}

function pagination_item_inactive(&$item)
{
	return '<span class="pagenav">'.$item->text.'</span>';
}
