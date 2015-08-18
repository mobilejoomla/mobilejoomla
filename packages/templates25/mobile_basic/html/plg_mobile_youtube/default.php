<?php
$protocol = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS'])!='off')) ? 'https:' : 'http:';
?>
<ul data-role="listview" class="youtube">
<li>
	<a href="<?php echo $protocol; ?>//m.youtube.com/watch?v=\1">
		<img src="<?php echo $protocol; ?>//i.ytimg.com/vi/\1/default.jpg" width="120" height="90" alt="">
		<span class="ui-li-heading"><?php echo JText::_('Watch on YouTube'); ?></span>
	</a>
</li>
</ul>