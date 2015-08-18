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

class JElementJqmList extends JElement
{
    var $_name = 'jqmList';

    function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
    {
        return '<{jqmstart}/><div class="ui-field-contain">' . parent::fetchTooltip($label, $description, $xmlElement, $control_name, $name) . '<{jqmend}/>';
    }

    function fetchElement($name, $value, &$xmlElement, $control_name)
    {
        $html = array();

        $options = (array)$this->getOptions($xmlElement);

        $html[] = '<{jqmstart}/>';
        $html[] = JHtml::_('select.genericlist', $options, $control_name . '[' . $name . ']', ' data-mini="true"', 'value', 'text', $value, $control_name . $name);
        $html[] = '</div><{jqmend}/>';

        return implode($html);
    }

    protected function getOptions(&$element)
    {
        $options = array();

        foreach ($element->children() as $option) {
            if ($option->name() !== 'option') {
                continue;
            }
            $options[] = JHtml::_(
                'select.option', (string)$option->attributes('value'),
                JText::_(trim($option->data()))
            );
        }

        reset($options);

        return $options;
    }
}
