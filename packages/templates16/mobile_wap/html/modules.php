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
		echo '<p>'.$module->content.'<br /></p>';
	}
}

function modChrome_wmlcards($module, &$params, &$attribs)
{
	if(!empty($module->content))
	{
		echo '<card name="'.$module->module."\">\n";
		if($module->showtitle)
		{
			?><p><strong><?php echo $module->title; ?></strong></p><?php
		}
		echo $module->content;
		echo "</card>\n";
	}
}
