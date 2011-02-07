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

/**
 * @param  JModule
 * @param  JParameter
 * @param  array
 * @return void
 */
function modChrome_iphone($module, &$params, &$attribs)
{
	/** @var JParameter $params */
	if(!empty($module->content))
	{
		?>
		<div class="container moduletable<?php echo $params->get('moduleclass_sfx'); ?>"><?php
		if($module->showtitle)
		{
			?>
				<div class="h3"><?php echo $module->title; ?></div><?php

		}
		echo $module->content;
		?></div><?php

	}
}
