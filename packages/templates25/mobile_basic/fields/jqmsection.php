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

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldJqmSection extends JFormField
{
    protected $type = 'jqmSection';

    protected function getLabel()
    {
        return '';
    }

    protected function getInput()
    {
        $html = array();

        static $js = true;
        if ($js) {
            $js = false;

            $templateURL = JUri::root(true) . '/templates/' . $this->form->getValue('template');
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

        $html[] = '<h3>' . JText::_((string)$this->element['label']) . '</h3>';
        $html[] = '<{jqmend}/>';
        return implode($html);
    }
}
