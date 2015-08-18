<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_feed
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if(!empty($feed) && is_string($feed))
{
	echo $feed;
}
else
{
	$myrtl = $params->get('rssrtl');
	if($myrtl == 0)
	{
		$lang = JFactory::getLanguage();
		$rssrtl = $lang->isRTL();
	}
	if($myrtl == 1)
		$rssrtl = false;
	elseif($myrtl == 2)
		$rssrtl = true;

	if ($feed != false)
	{
		//image handling
		$iUrl	= isset($feed->image)	? $feed->image	: null;
		$iTitle = isset($feed->imagetitle) ? $feed->imagetitle : null;
?>
<div style="direction: <?php echo $rssrtl ? 'rtl' :'ltr'; ?>; text-align: <?php echo $rssrtl ? 'right' :'left'; ?> !important" class="feed<?php echo $moduleclass_sfx; ?>">
	<ul data-role="listview">
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
			if ($params->get('rssdesc', 1))
			{
				echo '<p>'.$feed->description.'</p>';
			}

			if (!is_null($feed->title) && $params->get('rsstitle', 1)) {
?>
		</a>
<?php
			}
			?></li><?php
		endif;
		for($i = 0; $i < $params->get('rssitems', 5); $i++)
		{
			$uri = (!empty($feed[$i]->guid) || !is_null($feed[$i]->guid)) ? $feed[$i]->guid : $feed[$i]->uri;

			$uri = substr($uri, 0, 4) != 'http' ? $params->get('rsslink') : $uri;
			$text = !empty($feed[$i]->content) ||  !is_null($feed[$i]->content) ? $feed[$i]->content : $feed[$i]->description;
			?><li><?php
			if (!empty($uri)) :
				?><h5 class="feed-link"><a href="<?php echo $uri; ?>" target="_blank"><?php echo $feed[$i]->title; ?></a></h5><?php
			else :
				?><h5 class="feed-link"><?php echo $feed[$i]->title; ?></h5><?php
			endif;
			
			if ($params->get('rssitemdesc') && !empty($text)) :
				// Strip the images.
				$text = JFilterOutput::stripImages($text);
				$text = JHtml::_('string.truncate', $text, $params->get('word_count'));
				?><p><?php echo str_replace('&apos;', "'", $text); ?></p><?php
			endif;
			?></li><?php
		}
?>
	</ul>
</div>
<?php
	}
}
