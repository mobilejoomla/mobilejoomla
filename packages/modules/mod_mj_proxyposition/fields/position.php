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
defined('JPATH_BASE') or die;

jimport('joomla.html.html');

if (version_compare(JVERSION, '1.6', '>=')) {

    jimport('joomla.form.formfield');

    class JFormFieldPosition extends JFormField
    {
        public $type = 'position';

        protected function getInput()
        {
            $joomlaWrapper = MjJoomlaWrapper::getInstance();
            $db = $joomlaWrapper->getDbo();

            $query = new MjQueryBuilder($db);
            $positions = $query
                ->select('DISTINCT ' . $query->qn('position') . ' AS ' . $query->qn('id'))
                ->select($query->qn('position') . ' AS ' . $query->qn('title'))
                ->from('#__modules')
                ->order('position')
                ->setQuery()
                ->loadObjectList();

            return JHtml::_('select.genericlist', $positions, $this->name, '', 'id', 'title', $this->value);
        }
    }

} else {

    class JElementPosition extends JElement
    {
        public $_name = 'position';

        public function fetchElement($name, $value, &$node, $control_name)
        {
            $joomlaWrapper = MjJoomlaWrapper::getInstance();
            $db = $joomlaWrapper->getDbo();

            $query = new MjQueryBuilder($db);
            $positions = $query
                ->select('DISTINCT ' . $query->qn('position') . ' AS ' . $query->qn('id'))
                ->select($query->qn('position') . ' AS ' . $query->qn('title'))
                ->from('#__modules')
                ->order('position')
                ->setQuery()
                ->loadObjectList();

            return JHtml::_('select.genericlist', $positions, $control_name . '[' . $name . ']', 'class="inputbox"', 'id', 'title', $value, $control_name . $name);
        }
    }

}