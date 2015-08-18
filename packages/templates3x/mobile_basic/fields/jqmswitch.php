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

class JFormFieldJqmSwitch extends JFormField
{
    protected $type = 'jqmSwitch';

    protected function getLabel()
    {
        return '<{jqmstart}/><div class="ui-field-contain">' . parent::getLabel() . '<{jqmend}/>';
    }

    protected function getInput()
    {
        $html = array();

        $options = array();
        $options[] = JHtml::_('select.option', '0',
            JText::alt('JNO', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname))
        );
        $options[] = JHtml::_('select.option', '1',
            JText::alt('JYES', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname))
        );

        $html[] = '<{jqmstart}/>';
        $html[] = JHtml::_('select.genericlist', $options, $this->name, ' data-role="flipswitch" data-mini="true" data-chosen="done" class="chzn-done"', 'value', 'text', $this->value, $this->id);
        $html[] = '</div><{jqmend}/>';

        return implode($html);
    }
}
