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
	$html .= "\n".'<div class="limit">'.JText::_('Display Num').$list['limitfield'].'</div>';
	$html .= $list['pageslinks'];
	$html .= "\n".'<div class="counter">'.$list['pagescounter'].'</div>';
	$html .= "\n".'<input type="hidden" name="limitstart" value="'.$list['limitstart'].'" />';
	$html .= "\n".'</div>';
	return $html;
}

function pagination_list_render($list)
{
	$html = '<ul>';
	$html .= '<li class="pagination-start">'.$list['start']['data'].'</li>';
	$html .= '<li class="pagination-prev">'.$list['previous']['data'].'</li>';
	foreach($list['pages'] as $page)
		$html .= '<li>'.$page['data'].'</li>';
	$html .= '<li class="pagination-next">'. $list['next']['data'].'</li>';
	$html .= '<li class="pagination-end">'. $list['end']['data'].'</li>';
	$html .= '</ul>';
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
