<?php // no direct access
defined('_JEXEC') or die('Restricted access');

if( $feed != false )
{
?>
<ul data-role="listview" class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
<?php
	//image handling
	$iUrl 	= isset($feed->image->url)   ? $feed->image->url   : null;
	$iTitle = isset($feed->image->title) ? $feed->image->title : null;

	if ((!is_null( $feed->title ) && $params->get('rsstitle', 1)) || $params->get('rssdesc', 1) || ($params->get('rssimage', 1) && $iUrl)) :
		?><li class="ui-btn-active"><?php
	// feed description
	if (!is_null( $feed->title ) && $params->get('rsstitle', 1)) {
?>
		<a href="<?php echo str_replace( '&', '&amp', $feed->link ); ?>">
			<h4><?php echo $feed->title; ?></h4>
<?php
	}

	// feed image
	if ($params->get('rssimage', 1) && $iUrl) {
	?>
		<img src="<?php echo $iUrl; ?>" alt="<?php echo @$iTitle; ?>"/>
	<?php
	}

	// feed description
	if ($params->get('rssdesc', 1)) {
	?>
		<p><?php echo $feed->description; ?></p>
	<?php
	}

	if (!is_null( $feed->title ) && $params->get('rsstitle', 1)) {
?>
		</a>
<?php
	}
	?></li><?php
	endif;

	$actualItems = count( $feed->items );
	$setItems    = $params->get('rssitems', 5);

	if ($setItems > $actualItems) {
		$totalItems = $actualItems;
	} else {
		$totalItems = $setItems;
	}

	$words = $params->def('word_count', 0);
	for ($j = 0; $j < $totalItems; $j ++)
	{
		$currItem = & $feed->items[$j];
		// item title
		?>
		<li>
		<?php
		if ( !is_null( $currItem->get_link() ) ) {
		?>
			<a href="<?php echo $currItem->get_link(); ?>">
				<h5><?php echo $currItem->get_title(); ?></h5>
		<?php
		}

		// item description
		if ($params->get('rssitemdesc', 1))
		{
			// item description
			$text = $currItem->get_description();
			$text = str_replace('&apos;', "'", $text);
			$text=strip_tags($text);

			// word limit check
			if ($words)
			{
				$texts = explode(' ', $text);
				$count = count($texts);
				if ($count > $words)
				{
					$text = '';
					for ($i = 0; $i < $words; $i ++) {
						$text .= ' '.$texts[$i];
					}
					$text .= '...';
				}
			}
			?>
			<p class="newsfeed_item<?php echo $params->get( 'moduleclass_sfx'); ?>"  >
				<?php echo $text; ?>
			</p>
			<?php
		}

		if ( !is_null( $currItem->get_link() ) ) {
		?>
			</a>
		<?php
		}

		?>
		</li>
		<?php
	}
	?>
</ul>
<?php } ?>
