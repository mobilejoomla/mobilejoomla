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

    class JFormFieldModule extends JFormField
    {
        public $type = 'module';

        protected function getInput()
        {
            $joomlaWrapper = MjJoomlaWrapper::getInstance();
            $db = $joomlaWrapper->getDbo();

            $query = new MjQueryBuilder($db);
            $modules = $query
                ->select('id', 'title', 'position', 'module')
                ->from('#__modules')
                ->where($query->qn('published') . '=1')
                ->where($query->qn('client_id') . '=0')
                // prevent infinite loop
                ->where($query->qn('module') . '<>' . $query->q('mod_mj_proxyposition'))
                ->order('position', 'ordering', 'title')
                ->setQuery()
                ->loadObjectList();

            $list = array();
            $prev_position = false;
            foreach ($modules as $module) {
                if ($prev_position !== $module->position) {
                    if (!empty($list)) {
                        $list[] = JHtml::_('select.option', '</OPTGROUP>');
                    }
                    $list[] = JHtml::_('select.option', '<OPTGROUP>', ($module->position === '') ? '&nbsp;' : $module->position);
                    $prev_position = $module->position;
                }
                $list[] = JHtml::_('select.option', $module->id, $module->title);
            }
            if (!empty($list)) {
                $list[] = JHtml::_('select.option', '</OPTGROUP>');
            }

            return JHtml::_('select.genericlist', $list, $this->name, null, 'value', 'text', $this->value);
        }
    }

} else {

    class JElementModule extends JElement
    {
        public $_name = 'module';

        public function fetchElement($name, $value, &$node, $control_name)
        {
            $joomlaWrapper = MjJoomlaWrapper::getInstance();
            $db = $joomlaWrapper->getDbo();

            $query = new MjQueryBuilder($db);
            $modules = $query
                ->select('id', 'title', 'position', 'module')
                ->from('#__modules')
                ->where($query->qn('published') . '=1')
                ->where($query->qn('client_id') . '=0')
                ->order('position', 'ordering', 'title')
                ->setQuery()
                ->loadObjectList();

            $list = array();
            $prev_position = false;
            foreach ($modules as $module) {
                if ($prev_position !== $module->position) {
                    if (!empty($list)) {
                        $list[] = JHtml::_('select.option', '</OPTGROUP>');
                    }
                    $list[] = JHtml::_('select.option', '<OPTGROUP>', $module->position);
                    $prev_position = $module->position;
                }
                $list[] = JHtml::_('select.option', $module->id, $module->title);
            }
            if (!empty($list)) {
                $list[] = JHtml::_('select.option', '</OPTGROUP>');
            }

            return JHtml::_('select.genericlist', $list, $control_name . '[' . $name . ']', 'class="inputbox"', 'value', 'text', $value, $control_name . $name);
        }
    }

}