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

jimport('joomla.html');

class JHTMLMjconfig
{
	function formName($param_name)
	{
		return 'mjconfig_'.str_replace('.', '-', $param_name);
	}

	function booleanParam($param_name, $MobileJoomla_Settings)
	{
		$name = JHTMLMjconfig::formName($param_name);
		return JHTML::_('select.booleanlist', $name, 'class="inputradio"', $MobileJoomla_Settings[$param_name]);
	}

	function listParam($param_name, $values, $MobileJoomla_Settings)
	{
		$name = JHTMLMjconfig::formName($param_name);
		return JHTML::_('select.genericlist', $values, $name, 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings[$param_name]);
	}

	function radioParam($param_name, $values, $MobileJoomla_Settings)
	{
		$name = JHTMLMjconfig::formName($param_name);
		return JHTML::_('select.radiolist', $values, $name, 'class="inputradio"', 'value', 'text', $MobileJoomla_Settings[$param_name]);
	}

	function templateParam($param_name, $templates, $MobileJoomla_Settings)
	{
		$name = JHTMLMjconfig::formName($param_name);
		return JHTML::_('mjconfig.selectarray', $templates, $name, 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings[$param_name]);
	}

	function positionParam($param_name, $positions, $MobileJoomla_Settings)
	{
		$name = JHTMLMjconfig::formName($param_name);
		return JHTML::_('select.genericlist', $positions, $name, 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings[$param_name]);
	}

	function label($label, $tooltip = '', $for_input = '')
	{
		$label = htmlspecialchars(JText::_($label), ENT_QUOTES, 'UTF-8');
		if($tooltip)
		{
			$tooltip = 'class="hasTip" title="'.addslashes($label).'::'.addslashes(htmlspecialchars(JText::_($tooltip), ENT_QUOTES, 'UTF-8')).'"';
		}
		if($for_input)
		{
			$name = JHTMLMjconfig::formName($for_input);
			$for_input = 'for="'.$name.'"';
		}
		return "<label $for_input $tooltip>$label</label>";
	}

	function textinput($param_name, $value, $size=16, $attrs = NULL)
	{
		$name = JHTMLMjconfig::formName($param_name);
		$value = addslashes(htmlspecialchars(JText::_($value), ENT_QUOTES, 'UTF-8'));
		if(!$attrs)
		{
			$attrs = array();
		}
		if(!$attrs['id'])
		{
			$attrs['id'] = $name;
		}
		$attrs['name'] = $name;
		$attrs['value'] = $value;
		$attrs['size'] = $size;
        $attr_list = array();
		foreach($attrs as $attr=>$val)
		{
			$attr_list[] = "$attr=\"$val\"";
		}
		$attr_str = join(' ',$attr_list);
		return "<input $attr_str>";
	}

	function selectArray(&$arr, $tag_name, $tag_attribs, $key, $text, $selected = NULL)
	{
		reset($arr);
		$html = "<select name=\"$tag_name\" $tag_attribs>";
		$count = count($arr);
		for($i = 0; $i < $count; $i++)
		{
			$k = $arr[$i][$key];
			$extra = ($k == $selected ? " selected=\"selected\"" : '');
			$html .= "<option value=\"".$k."\"$extra>".$arr[$i][$text]."</option>";
		}
		$html .= "</select>";
		return $html;
	}

	function menuList($menuoptions, $param_name, $value)
	{
		$name = JHTMLMjconfig::formName($param_name);

		static $is_joomla15;
		if(!isset($is_joomla15))
			$is_joomla15 = (substr(JVERSION,0,3) == '1.5');

		if(!$is_joomla15)
			return JHTML::_('select.genericlist',
							$menuoptions,
							$name.'_tmp',
							array('list.attr' => 'size="7" onchange="document.getElementById(\''.$name.'\').value=this.value" ',
								  'list.select' => $value,
								  'option.text.toHtml' => false));
		else
			return JHTML::_('select.genericlist',
							$menuoptions,
							$name.'_tmp',
							'size="7" onchange="document.getElementById(\''.$name.'\').value=this.value" ',
							'value',
							'text',
							$value);
	}
}
