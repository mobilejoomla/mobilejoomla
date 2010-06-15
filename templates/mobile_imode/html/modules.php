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
