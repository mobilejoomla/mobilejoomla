<?php
/**
 * Kuneri Mobile Joomla! for Joomla!1.5
 * http://www.mobilejoomla.com/
 *
 * @version	0.9.0
 * @license	http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	Copyright (C) 2008-2009 Kuneri Ltd. All rights reserved.
 */

defined('_JEXEC') or die('Restricted access');

function modChrome_chtml($module, &$params, &$attribs)
{
	if(!empty($module->content))
	{
		?>
		<div><?php
		if($module->showtitle)
		{
			?><h3><?php echo $module->title; ?></h3><?php

		}
		echo $module->content;
		?></div><?php

	}
}
