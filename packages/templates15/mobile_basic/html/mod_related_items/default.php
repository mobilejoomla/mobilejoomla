<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<ul data-role="listview" class="relateditems<?php echo $params->get('moduleclass_sfx'); ?>">
<?php foreach ($list as $item) :	?>
<li>
	<a href="<?php echo $item->route; ?>"><?php echo $item->title; ?></a>
	<?php if ($showDate) echo '<p class="ui-li-aside">'.$item->created.'</p>'; ?>
</li>
<?php endforeach; ?>
</ul>