<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version    ###VERSION###
 * @license    ###LICENSE###
 * @copyright  ###COPYRIGHT###
 * @date       ###DATE###
 */
defined('_JEXEC') or die;

class JElementJqmSlider extends JElement
{
    var $_name = 'jqmSlider';

    function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
    {
        return '<{jqmstart}/><div class="ui-field-contain">' . parent::fetchTooltip($label, $description, $xmlElement, $control_name, $name) . '<{jqmend}/>';
    }

    function fetchElement($name, $value, &$xmlElement, $control_name)
    {
        $html = array();
        $html[] = '<{jqmstart}/>';
        $html[] = '<input type="number" data-type="range" name="' . $control_name . '[' . $name . ']' . '" id="' . $control_name . $name . '"' .
            ' min="0" max="100" value="' . intval($value) .
            '" data-mini="true" data-highlight="true" />';
        $html[] = '</div><{jqmend}/>';

        return implode($html);
    }
}
