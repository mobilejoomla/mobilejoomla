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
//	$html .= "\n".'<div class="counter">'.$list['pagescounter'].'</div>';
	$html .= "\n".'<input type="hidden" name="'.$list['prefix'].'limitstart" value="'.$list['limitstart'].'" />';
	$html .= "\n".'</div>';
	return $html;
}

function pagination_list_render($list)
{
	$html = '<div data-role="controlgroup" data-type="horizontal">';
//	$html .= $list['start']['data'].' '.$list['previous']['data'];
    $html .= str_replace('">', ' ui-icon-arrow-l ui-btn-icon-notext">', $list['previous']['data']);
	foreach( $list['pages'] as $page )
		$html .= ' '.$page['data'];
//	$html .= ' '.$list['next']['data'].' '.$list['end']['data'];
    $html .= str_replace('">', ' ui-icon-arrow-r ui-btn-icon-notext">', $list['next']['data']);
	$html .= '</div>';
	return $html;
}

function pagination_item_active(&$item)
{
	static $theme;
	if(!isset($theme))
	{
		$app = JFactory::getApplication();
		$template = $app->getTemplate(true);
		$theme = $template->params->get('theme_pagination');
		if($theme)
            $theme = ' ui-btn-'.$theme;
	}
    return '<a title="'.$item->text.'" href="'.$item->link.'" class="pagenav ui-btn'.$theme.'">'.$item->text.'</a>';
}

function pagination_item_inactive(&$item)
{
	static $theme;
	if(!isset($theme))
	{
		$app = JFactory::getApplication();
		$template = $app->getTemplate(true);
		$theme = $template->params->get('theme_pagination');
		if($theme)
            $theme = ' ui-btn-'.$theme;
	}
    return '<span class="pagenav ui-btn'.$theme.' ui-state-disabled">'.$item->text.'</span>';
}
