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

function modChrome_mobile($module, &$params, &$attribs)
{
	/** @var JParameter $params */
	if(!empty($module->content))
	{
		$moduleclass_sfx = $params->get('moduleclass_sfx');

		if(preg_match('#\binset\b#', $moduleclass_sfx))
		{ // inset
			$module->content = str_replace('<ul data-role="listview"', '<ul data-role="listview" data-inset="true"', $module->content);
		}

		$enhance = preg_match('#\bnoenhance\b#', $moduleclass_sfx) ? ' data-enhance="false"' : '';

		if($module->showtitle)
		{
			$doc = JFactory::getDocument();
			$theme_moduletitle   = $doc->params->get('theme_moduletitle');
			$theme_modulecontent = $doc->params->get('theme_modulecontent');
			
			$attr = '';
			if($theme_moduletitle)
				$attr .= ' data-theme="'.$theme_moduletitle.'"';
			if($theme_modulecontent)
				$attr .= ' data-content-theme="'.$theme_modulecontent.'"';

			if(preg_match('#\bmini\b#', $moduleclass_sfx))
			{
				$attr .= ' data-mini="true"';
			}

			if(preg_match('#\bnostyle\b#', $moduleclass_sfx))
			{ // nostyle
				?><div class="moduletable<?php echo $moduleclass_sfx; ?>"<?php echo $enhance; ?>><?php
					?><h3><?php echo $module->title; ?></h3><?php
					?><div><?php echo $module->content; ?></div><?php
				?></div><?php
			}
			elseif(preg_match('#\bpopup\b#', $moduleclass_sfx))
			{ // popup button
				$id = 'p-'.intval($module->id).'-'.time();
				?><a href="#<?php echo $id; ?>" data-rel="popup" class="ui-btn ui-btn-inline ui-corner-all moduletable<?php echo $moduleclass_sfx; ?>"><?php echo $module->title; ?></a><?php
				?><div data-role="popup" data-transition="flip" id="<?php echo $id; ?>"<?php echo $attr; ?> class="moduletable<?php echo $moduleclass_sfx; ?>"<?php echo $enhance; ?>><?php
					?><?php echo $module->content; ?><?php
				?></div><?php
			}
			elseif(preg_match('#\bopen\b#', $moduleclass_sfx))
			{ // opened collapsible
				?><div data-role="collapsible" data-collapsed="false"<?php echo $attr; ?> class="moduletable<?php echo $moduleclass_sfx; ?>"<?php echo $enhance; ?>><?php
					?><h3><?php echo $module->title; ?></h3><?php
					?><?php echo $module->content; ?><?php
				?></div><?php
			}
			else
			{ // closed collapsible
				?><div data-role="collapsible"<?php echo $attr; ?> class="moduletable<?php echo $moduleclass_sfx; ?>"<?php echo $enhance; ?>><?php
					?><h3><?php echo $module->title; ?></h3><?php
					?><?php echo $module->content; ?><?php
				?></div><?php
			}
		}
		else
		{
			?><div class="moduletable<?php echo $moduleclass_sfx; ?>"<?php echo $enhance; ?>><?php
				echo $module->content;
			?></div><?php
		}
	}
}
