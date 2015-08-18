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

class JFormFieldJqmList extends JFormField
{
    protected $type = 'jqmList';

    protected function getLabel()
    {
        return '<{jqmstart}/><div class="ui-field-contain">' . parent::getLabel() . '<{jqmend}/>';
    }

    protected function getInput()
    {
        $html = array();

        $options = (array)$this->getOptions();

        $html[] = '<{jqmstart}/>';
        $html[] = JHtml::_('select.genericlist', $options, $this->name, ' data-mini="true" data-chosen="done" class="chzn-done"', 'value', 'text', $this->value, $this->id);
        $html[] = '</div><{jqmend}/>';

        return implode($html);
    }

    protected function getOptions()
    {
        $options = array();

        foreach ($this->element->children() as $option) {
            if ($option->getName() != 'option')
                continue;

            $options[] = JHtml::_(
                'select.option', (string)$option['value'],
                JText::alt(trim((string)$option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname))
            );
        }

        reset($options);

        return $options;
    }
}
