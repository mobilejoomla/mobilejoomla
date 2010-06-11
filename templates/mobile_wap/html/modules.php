<?php
defined('_JEXEC') or die('Restricted access');

function modChrome_wml($module, &$params, &$attribs)
{
	if(!empty($module->content))
	{
		if($module->showtitle)
		{
			?><p><strong><?php echo $module->title; ?></strong></p><?php

		}
		echo $module->content.'<br />';
	}
}

function modChrome_wmlcards($module, &$params, &$attribs)
{
	echo '<card name="'.$module->module."\">\n";
	if(!empty($module->content))
	{
		if($module->showtitle)
		{
			?><p><strong><?php echo $module->title; ?></strong></p><?php

		}
		echo $module->content;
	}
	echo "</card>\n";
}
