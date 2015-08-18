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

class plgMobileDomains extends JPlugin
{
    private $domain_markup;

    public function plgMobileDomains(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }

    /**
     * @param string $name
     * @param string $value
     * @return mixed
     * @TODO move to wrapper
     */
    private function setConfig($name, $value)
    {
        static $is_joomla15;
        if (!isset($is_joomla15)) {
            $is_joomla15 = (substr(JVERSION, 0, 3) === '1.5');
        }

        /** @var Joomla\Registry\Registry $config */
        $config = JFactory::getConfig();
        if ($is_joomla15) {
            return $config->setValue('config.' . $name, $value);
        } else {
            return $config->set($name, $value);
        }
    }

    /**
     * @param MobileJoomla $mj
     * @throws Exception
     */
    public function onAfterDeviceDetection($mj)
    {
        /** @var MjSettingsModel $mjSettings */
        $mjSettings = $mj->settings;
        /** @var MjDevice $mjDevice */
        $mjDevice = $mj->device;

        $host = $_SERVER['HTTP_HOST'];
        if (empty($host)) {
            return;
        }

        $this->getSchemePath($http, $base);

        // Check for current domain
        $markup = $mjDevice->markup;
        $domain = $mjSettings->get($markup . '.domain');
        if ($domain !== null && $host === $domain) {
            $this->setConfig('live_site', $http . '://' . $host . $base);
            $this->domain_markup = $markup;
            return;
        }

        // Mobile domains
        include_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/classes/mjhelper.php';
        $markups_list = MjHelper::getDeviceList();
        unset($markups_list['desktop']);
        foreach ($markups_list as $markup => $t) {
            $domain = $mjSettings->get($markup . '.domain');
            if ($domain !== null
                && $host === $domain
                && $mj->checkMarkup($markup) !== false
            ) {
                $this->domain_markup = $mjDevice->markup;
                $mjDevice->markup = $markup;
                $this->setConfig('live_site', $http . '://' . $host . $base);
                return;
            }
        }

        // Desktop domain
        $app = JFactory::getApplication();
        // is it non-first visit? Then don't redirect
        if ($app->getUserState('mobilejoomla.markup') !== null) {
            $markup = $mjDevice->markup;
            $domain = $mjSettings->get($markup . '.domain');
            if (empty($domain)) {
                return;
            }
            $mjDevice->markup = '';
        }
    }

    public function onBeforeMobileMarkupInit($mj)
    {
        /** @var MjSettingsModel $mjSettings */
        $mjSettings = $mj->settings;
        /** @var MjDevice $mjDevice */
        $mjDevice = $mj->device;

        $host = $_SERVER['HTTP_HOST'];
        if (empty($host)) {
            return;
        }

        $markup = $mjDevice->markup;
        $domain = $mjSettings->get($markup . '.domain');

        if ($this->domain_markup !== null
            && ($domain === null || $host !== $domain)
        ) {
            $mjDevice->markup = $this->domain_markup;
        }

        if ($markup == '' || $_SERVER['REQUEST_METHOD'] === 'POST') {
            return;
        }

        $app = JFactory::getApplication();
        if ($domain !== null) {
            $domain_markup = $domain;
            if (!empty($domain_markup) && $host !== $domain_markup) {
                $uri = JUri::getInstance();
                $protocol = $uri->toString(array('scheme'));
                $path = $uri->toString(array('path', 'query'));

                $app->redirect($protocol . $domain_markup . $path);
            }
        }
    }

    private function getSchemePath(&$http, &$base)
    {
        if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) !== 'off')) {
            $http = 'https';
        } else {
            $http = 'http';
        }

        $app = JFactory::getApplication();
        $live_url = $app->getCfg('live_site');
        if ($live_url) {
            $parsed = parse_url($live_url);
            if ($parsed !== false) {
                $base = isset($parsed['path']) ? $parsed['path'] : '/';
                return;
            }
        }

        if (strpos(PHP_SAPI, 'cgi') !== false && !empty($_SERVER['REQUEST_URI']) &&
            (!ini_get('cgi.fix_pathinfo') || version_compare(PHP_VERSION, '5.2.4', '<'))
        ) {
            $base = rtrim(dirname(str_replace(array('"', '<', '>', "'"), '', $_SERVER['PHP_SELF'])), '/\\');
        } else {
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        }
    }
}
