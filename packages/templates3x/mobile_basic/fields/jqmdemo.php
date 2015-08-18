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

class JFormFieldJqmDemo extends JFormField
{
    protected $type = 'jqmDemo';

    protected function getLabel()
    {
        return '<{jqmstart}/><div class="ui-field-contain">' . parent::getLabel() . '<{jqmend}/>';
    }

    protected function getInput()
    {
        $html = array();
        $html[] = '<{jqmstart}/>';
        $html[] = '<a href="http://www.mobilejoomla.com/templates.html" target="_blank">Available in Premium Templates</a>';
        $html[] = '</div><{jqmend}/>';

        return implode($html);
    }
}
