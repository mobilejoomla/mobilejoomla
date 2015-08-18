<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

if ( ! defined('modMainMenuXMLCallbackDefined') )
{
function modMainMenuXMLCallback(&$node, $args)
{
	$user	= &JFactory::getUser();
	$menu	= &JSite::getMenu();
	$active	= $menu->getActive();
	$path	= isset($active) ? array_reverse($active->tree) : null;

	if (($args['end']) && ($node->attributes('level') >= $args['end']))
	{
		$children = $node->children();
		foreach ($node->children() as $child)
		{
			if ($child->name() == 'ul') {
				$node->removeChild($child);
			}
		}
	}

	if ($node->name() == 'ul') {
		$node->addAttribute('data-role', 'listview');
		foreach ($node->children() as $child)
		{
			if ($child->attributes('access') > $user->get('aid', 0)) {
				$node->removeChild($child);
			}
		}
	}

	if (($node->name() == 'li') && isset($node->ul)) {
		$node->addAttribute('class', 'parent');
		$node->ul[0]->addAttribute('data-shadow', 'false');
		$node->ul[0]->addAttribute('data-corners', 'false');
		if (isset($node->span)) {
			$node->h6 = $node->span;
			unset($node->span);
			foreach($node->children() as $child) {
                if ($child->name() === 'span') {
                    $child->_name = 'h6';
                }
            }
			$node->addAttribute('data-role', 'collapsible');
			$node->addAttribute('data-inset', 'true');
			$node->addAttribute('data-iconpos', 'right');
			$node->addAttribute('data-shadow', 'false');
			$node->addAttribute('data-corners', 'false');
		} elseif (!isset($node->a)) {
			$node->addAttribute('data-role', 'list-divider');
		}
	}

	if (($node->name() == 'li') && isset($node->a)) {
		$href = $node->a[0]->attributes('href');
		if (strpos($href, 'mailto:') === 0) {
			$node->addAttribute('data-icon', 'mail');
		}
		if (strpos($href, 'tel:') === 0) {
			$node->addAttribute('data-icon', 'phone');
		}
		if (strpos($href, 'geo:') === 0) {
			$node->addAttribute('data-icon', 'location');
		}
	}

	if (isset($path) && (in_array($node->attributes('id'), $path) || in_array($node->attributes('rel'), $path)))
	{
		if ($node->attributes('class')) {
			$node->addAttribute('class', $node->attributes('class').' ui-btn-active active');
		} else {
			$node->addAttribute('class', 'ui-btn-active active');
		}
	}
	else
	{
		if (isset($args['children']) && !$args['children'])
		{
			$children = $node->children();
			foreach ($node->children() as $child)
			{
				if ($child->name() == 'ul') {
					$node->removeChild($child);
				}
			}
		}
	}

	if (($node->name() == 'li') && ($id = $node->attributes('id'))) {
		if ($node->attributes('class')) {
			$node->addAttribute('class', $node->attributes('class').' item'.$id);
		} else {
			$node->addAttribute('class', 'item'.$id);
		}
	}

	if (isset($path) && $node->attributes('id') == $path[0]) {
		$node->addAttribute('id', 'current');
	} else {
		$node->removeAttribute('id');
	}
	$node->removeAttribute('rel');
	$node->removeAttribute('level');
	$node->removeAttribute('access');
}
	define('modMainMenuXMLCallbackDefined', true);
}

modMainMenuHelper::render($params, 'modMainMenuXMLCallback');
