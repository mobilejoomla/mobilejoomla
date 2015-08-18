<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_weblinks
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<ul data-role="listview" class="weblinks<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) :	?>
<li>
	<?php
	if ($params->get('hits', 0))
		echo '<span class="ui-li-count">' . $item->hits . '</span>';
	echo '<a href="'. $item->link .'" rel="'.$params->get('follow', 'no follow').'">'.
		htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8');
	echo '</a>';
	?>
	<?php if ($params->get('description', 0)) : ?>
		<?php echo nl2br($item->description); ?>
	<?php endif; ?>
</li>
<?php endforeach; ?>
</ul>
