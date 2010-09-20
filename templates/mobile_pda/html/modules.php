<?php
/**
 * Mobile Joomla!
 * http://www.mobilejoomla.com
 *
 * @version		0.9.8
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	(C) 2008-2010 MobileJoomla!
 * @date		September 2010
 */
defined('_JEXEC') or die('Restricted access');

/**
 * @param  JModule
 * @param  JParameter
 * @param  array
 * @return void
 */
function modChrome_xhtml_m($module, &$params, &$attribs)
{
	/** @var JParameter $params */
	if(!empty($module->content))
	{
		?>
		<div class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>"><?php
		if($module->showtitle)
		{
			?>
				<div class="h3"><?php echo $module->title; ?></div><?php

		}
		echo $module->content;
		?></div><?php

	}
}
