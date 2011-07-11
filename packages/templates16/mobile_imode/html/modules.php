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
	/** @var JParameter $params */
	if(!empty($module->content))
	{
		?><div class="moduletable<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>"><?php
		if($module->showtitle)
		{
			?><h3><?php echo $module->title; ?></h3><?php
		}
		echo $module->content;
		?></div><?php
	}
}
