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

//trick to autocreate params.ini
$tplPath = dirname(dirname(__FILE__)) . '/params.ini';
if (!is_file($tplPath)) {
    jimport('joomla.filesystem.file');
    JFile::write($tplPath, '');
}
unset($tplPath);

class JElementJqmSection extends JElement
{
    var $_name = 'jqmSection';

    function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
    {
        return '';
    }

    function fetchElement($name, $value, &$xmlElement, $control_name)
    {
        $html = array();

        static $js = true;
        if ($js) {
            $js = false;
            $template = basename(dirname(dirname(__FILE__)));
            $templateURL = JUri::root(true) . '/templates/' . $template;
            $jqmBase = $templateURL . '/vendor/jqm/';
            /** @var $doc JDocumentHTML */
            $doc = JFactory::getDocument();
            $jqmVer = '1.4.5';
            $doc->addStyleSheet($jqmBase . 'jquery.mobile-' . $jqmVer . '.css');
            $doc->addStyleSheet($templateURL . '/fields/fix.css');
            $doc->addScript($jqmBase . 'jquery-1.9.1.js');
            $doc->addScript($templateURL . '/fields/jqminit.js');
            $doc->addScript($jqmBase . 'jquery.mobile-' . $jqmVer . '.js');
            $doc->addScript($templateURL . '/fields/fix.js');
            $doc->addScript($templateURL . '/fields/helper.js');

            $html[] = '<{jqmstartmarker}/>';
            $html[] = '<{jqmstart}/>';
            $html[] = '<div id="mobile_jqm_params" data-theme="b"><div data-role="collapsible-set" data-theme="a" data-content-theme="a"><div data-role="collapsible" data-collapsed="false">';
        } else {
            $html[] = '<{jqmstart}/>';
            $html[] = '</div><div data-role="collapsible">';
        }

        $html[] = '<h3>' . JText::_($xmlElement->attributes('label')) . '</h3>';
        $html[] = '<{jqmend}/>';
        return implode($html);
    }
}
