<?php
/**
 * @version
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Create shortcut
$urls = isset($this->item->urls) ? json_decode($this->item->urls) : null;

// Create shortcuts to some parameters.
$params = $this->item->params;
if ($urls && (!empty($urls->urla) || !empty($urls->urlb) || !empty($urls->urlc))) :
?>
<div class="content-links">
<ul data-role="listview" data-inset="true">
<?php
	$urlarray = array(
		array($urls->urla, $urls->urlatext, $urls->targeta, 'a'),
		array($urls->urlb, $urls->urlbtext, $urls->targetb, 'b'),
		array($urls->urlc, $urls->urlctext, $urls->targetc, 'c')
	);
	foreach($urlarray as $url) :
		$link = $url[0];
		if(!$link)
			continue;
		$label = $url[1];
		$target = $url[2];
		$id = $url[3];

		// If no label is present, take the link
		$label = ($label) ? $label : $link;

		// If no target is present, use the default
		$target = $target ? $target : $params->get('target'.$id);
?>
	<li class="content-links-<?php echo $id; ?>">
<?php
		switch ($target)
		{
		case 1:
		case 2:
		case 3:
			// open in a new window
			echo '<a href="'. htmlspecialchars($link) .'" target="_blank"  rel="nofollow">'.
					htmlspecialchars($label) .'</a>';
			break;
		default:
			// open in parent window
			echo '<a href="'.  htmlspecialchars($link) . '" rel="nofollow">'.
					htmlspecialchars($label) . ' </a>';
		}
?>
	</li>
	<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>