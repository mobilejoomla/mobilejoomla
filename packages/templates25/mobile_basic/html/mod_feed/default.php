<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_feed
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if ($feed != false)
{
	//image handling
	$iUrl	= isset($feed->image->url)	? $feed->image->url	: null;
	$iTitle = isset($feed->image->title) ? $feed->image->title : null;
?>
<div style="direction: <?php echo $rssrtl ? 'rtl' :'ltr'; ?>; text-align: <?php echo $rssrtl ? 'right' :'left'; ?> ! important"  class="feed<?php echo $moduleclass_sfx; ?>">
	<ul data-role="listview" class="newsfeed<?php echo $params->get('moduleclass_sfx'); ?>">
<?php
	if ((!is_null($feed->title) && $params->get('rsstitle', 1)) || $params->get('rssdesc', 1) || ($params->get('rssimage', 1) && $iUrl)) :
?>
	<li class="ui-btn-active">
<?php
	// feed description
	if (!is_null($feed->title) && $params->get('rsstitle', 1)) {
?>
		<a href="<?php echo str_replace('&', '&amp', $feed->link); ?>" target="_blank">
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
		echo '<p>'.$feed->description.'</p>';
	}

	if (!is_null($feed->title) && $params->get('rsstitle', 1)) {
?>
		</a>
<?php
	}
?>
	</li>
<?php
	endif;

	$actualItems = count($feed->items);
	$setItems	= $params->get('rssitems', 5);

	if ($setItems > $actualItems) {
		$totalItems = $actualItems;
	} else {
		$totalItems = $setItems;
	}
	?>
			<?php
			$words = $params->def('word_count', 0);
			for ($j = 0; $j < $totalItems; $j ++)
			{
				$currItem = & $feed->items[$j];
				// item title
				?>
				<li class="newsfeed-item">
					<?php	if (!is_null($currItem->get_link())) {	?>
				<a href="<?php echo $currItem->get_link(); ?>" target="_blank">
				<?php if (!is_null($feed->title) && $params->get('rsstitle', 1))

					{ echo '<h5 class="feed-link">';}
				else
				{
				echo '<h4 class="feed-link">';
				}
				?>

					<?php echo $currItem->get_title(); ?>
					<?php if (!is_null($feed->title) && $params->get('rsstitle', 1))

					{ echo '</h5>';}
						else
						{ echo '</h4>';}
				?>
</a>
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

						<p><?php echo $text; ?></p>

					<?php
				}
				?>
				</li>
				<?php
			}
			?>
	</ul>
</div>
<?php } ?>
