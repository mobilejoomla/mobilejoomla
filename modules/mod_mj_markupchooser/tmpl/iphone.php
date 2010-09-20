<?php
/**
 * Mobile Joomla!
 * http://www.mobilejoomla.com
 *
 * @version		0.9.8
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	(C) 2008-2010 MobileJoomla!
 * @date		September 2010
 */
defined('_JEXEC') or die('Restricted access');

$links = array ();

$count = count($url);
for($i = 0; $i < $count; $i++)
{
	$links[] = ($url[$i] == '') ? ('<span>'.$text[$i].'</span>') : ('<a href="'.$url[$i].'">'.$text[$i].'</a>');
}

echo implode(' | ', $links);
