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

/** @var $params JRegistry */
/** @var $links array */

echo $params->get('show_text', ' ');

$parts = array();
foreach($links as $link)
{
	if($link['url'])
		$parts[] = '<a class="markupchooser" href="'.$link['url'].'">'.$link['text'].'</a>';
	else
		$parts[] = '<span class="markupchooser">'.$link['text'].'</span>';
}

echo implode('<span class="markupchooser"> | </span>', $parts);
