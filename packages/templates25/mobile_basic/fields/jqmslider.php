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

class JFormFieldJqmSlider extends JFormField
{
    protected $type = 'jqmSlider';

    protected function getLabel()
    {
        return '<{jqmstart}/><div class="ui-field-contain">' . parent::getLabel() . '<{jqmend}/>';
    }

    protected function getInput()
    {
        $html = array();
        $html[] = '<{jqmstart}/>';
        $html[] = '<input type="number" data-type="range" name="' . $this->name . '" id="' . $this->id . '"' .
            ' min="0" max="100" value="' . intval($this->value) .
            '" data-mini="true" data-highlight="true" />';
        $html[] = '</div><{jqmend}/>';

        return implode($html);
    }
}
