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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgMobileAlways extends JPlugin
{
    public function plgMobileAlways(& $subject, $config)
    {
        parent::__construct($subject, $config);
        if (!isset($this->params)) {
            $this->params = new JParameter(null);
        }
    }

    public function onMjGetDeviceList()
    {
        return array(
            'desktop' => 'Desktop',
            'mobile' => 'Mobile'
        );
    }

    public function onDeviceDetection($mj)
    {
        /** @var MjDevice $mjDevice */
        $mjDevice = $mj->device;

        $markup = $this->params->get('markup', '');
        if ($markup) {
            $mjDevice->markup = $markup;
        }
    }
}
