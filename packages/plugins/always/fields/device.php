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

include_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/classes/mjhelper.php';

if (version_compare(JVERSION, '1.6', '>=')) {

    jimport('joomla.form.formfield');

    class JFormFieldDevice extends JFormField
    {
        public $type = 'device';

        protected function getInput()
        {
            $devices = MjHelper::getDeviceList();

            $list = array();
            $list[] = JHtml::_('select.option', '', JText::_('PLG_MOBILE_ALWAYS__AUTO'));

            foreach ($devices as $device => $title) {
                if (!empty($device) && $device !== 'desktop') {
                    $list[] = JHtml::_('select.option', $device, $title);
                }
            }

            return JHtml::_('select.genericlist', $list, $this->name, '', 'id', 'title', $this->value);
        }
    }

} else {

    class JElementDevice extends JElement
    {
        public $_name = 'device';

        public function fetchElement($name, $value, &$node, $control_name)
        {
            $devices = MjHelper::getDeviceList();

            $list = array();
            $list[] = JHtml::_('select.option', '', JText::_('PLG_MOBILE_ALWAYS__AUTO'));

            foreach ($devices as $device => $title) {
                if (!empty($device) && $device !== 'desktop') {
                    $list[] = JHtml::_('select.option', $device, $title);
                }
            }

            return JHtml::_('select.genericlist', $list, $control_name . '[' . $name . ']', 'class="inputbox"', 'id', 'title', $value, $control_name . $name);
        }
    }

}