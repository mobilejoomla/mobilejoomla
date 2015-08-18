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

include_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/models/settings.php';
include_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/classes/mjdevice.php';
include_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/classes/mjmarkupgenerator.php';

class MobileJoomla
{
    /** @var MjSettingsModel */
    public $settings;

    /** @var MjDevice */
    public $device;

    /** @var MjMarkupGenerator */
    public $generator;

    /** @var bool */
    private $ishomepage = false;

    /**
     * @param $joomlaWrapper MjJoomlaWrapper
     */
    public function __construct($joomlaWrapper)
    {
        $this->joomlaWrapper = $joomlaWrapper;
        $this->settings = new MjSettingsModel($joomlaWrapper);
        $this->device = new MjDevice;
    }

    /**
     * @deprecated
     * @return MobileJoomla
     * @todo Q: rename method to differ from usual Singleton implementation
     */
    static public function getInstance()
    {
        /** @var JApplicationSite $app */
        $app = JFactory::getApplication();
        /** @var MobileJoomla $mj */
        $mj_list = $app->triggerEvent('onGetMobileJoomla');
        $mj = array_pop($mj_list);
        return $mj;
    }

    /**
     * @param string $markup
     */
    public function setMarkup($markup = '')
    {
        if ($markup == '') {
            $this->generator = null;
        } else {
            $class = 'MjMarkupGenerator_' . strtoupper($markup);
            if (!class_exists($class)) {
                require_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/markup/' . $markup . '.php';
                if (!class_exists($class)) {
                    $this->joomlaWrapper->raiseWarning('Class not found: ' . $class, 500);
                }
            }
            $this->generator = new $class($this);
        }
    }

    public function loadLanguageFile($extension, $path = JPATH_BASE)
    {
        $lang = JFactory::getLanguage();
        $lang->load($extension, $path, 'en-GB', true);
        $lang->load($extension, $path, null, true);
    }

    public function getToolbar()
    {
        static $instance = null;
        if ($instance === null) {
            include_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/classes/mjtoolbar.php';
            $instance = new MJToolbar;
        }
        return $instance;
    }

    public function getMarkup()
    {
        return $this->device->markup;
    }

    public function isHome()
    {
        return $this->ishomepage;
    }

    public function setHome($ishome)
    {
        $this->ishomepage = $ishome;
    }

    public function getParam($name, $default = null)
    {
        $full_value = $this->settings->get($this->getMarkup() . '.' . $name);
        if ($full_value !== '' && $full_value !== null) {
            return $full_value;
        }

        $global_value = $this->settings->get('.' . $name);
        if ($global_value !== null) {
            return $global_value;
        }

        return $this->settings->get($name, $default);
    }

    public function setParam($name, $value)
    {
        $full_name = $this->getMarkup() . '.' . $name;
        $this->settings->set($full_name, $value);
    }

    public function hideModules($scope = '')
    {
        // @todo consider to remove hideModules API (hide modules on certain menu items instead)
        switch ($scope) {
            case 'all':
                // @todo hide modules mj_footer, mj_footer2
                $this->setParam('hidefooter', 1);
                $this->setParam('footer1', '');
                $this->setParam('footer2', '');
                $this->setParam('footer3', '');
            case '':
                // @todo hide modules mj_top, mj_top2, mj_middle, mj_middle2
                $this->setParam('header1', '');
                $this->setParam('header2', '');
                $this->setParam('header3', '');
                $this->setParam('middle1', '');
                $this->setParam('middle2', '');
                $this->setParam('middle3', '');
                $this->setParam('cards', '');
        }
    }

    public function getPosition($pos)
    {
        // @todo refactor as fixed position names are used in MJ2
        if (!isset($this->config)) {
            return '';
        }

        switch ($pos) {
            case 'header':
                return $this->getParam('header1');
            case 'middle':
                return $this->getParam('middle1');
            case 'footer':
                return $this->getParam('footer1');
            case 'header2':
            case 'header3':
            case 'middle2':
            case 'middle3':
            case 'footer2':
            case 'footer3':
            case 'cards':
                return $this->getParam($pos);
        }
        return '';
    }

    public function isCurrentMarkup($markup)
    {
        if ($markup === 'auto') {
            $markup = $this->device->real_markup;
        } elseif ($markup === 'desktop' || $markup === '') {
            $markup = false;
        }
        return $markup === $this->device->markup;
    }

    public function getDeviceViewURI($device)
    {
        jimport('joomla.environment.uri');

        $uri = clone(JUri::getInstance());
        if ($uri->getVar('format') === 'html') {
            $uri->delVar('format');
        }
        $uri->delVar('device');

        $uri->setHost($this->settings->get('desktop_domain'));

        if ($device === 'auto') {
            $device = $this->device->real_markup === '' ? 'desktop' : $this->device->real_markup;
        }

        switch ($device) {
            case 'desktop':
                break;
            default:
                if ($this->settings->get($device . '.domain')) {
                    $uri->setHost($this->settings->get($device . '.domain'));
                }
        }

        if ($device !== false) {
            $uri->setVar('device', $device);
        }

        return htmlspecialchars($uri->toString());
    }

    public function getCanonicalURI()
    {
        jimport('joomla.environment.uri');

        $desktop_domain = $this->settings->get('desktop_domain');
        $uri = clone(JUri::getInstance());

        $uri_host = preg_replace('#^www\.#', '', $uri->getHost());
        $desktop_host = preg_replace('#^www\.#', '', $desktop_domain);
        if (($uri_host === $desktop_host) && ($this->device->markup == $this->device->default_markup)) {
            return false;
        }

        $uri->delVar('device');
        $uri->delVar('format');
        $uri->setHost($desktop_domain);

        return htmlspecialchars($uri->toString());
    }

    public function getAccessKey()
    {
        static $last_keynum = 0;
        if ($last_keynum >= 10) {
            return false;
        }
        $last_keynum++;
        return $last_keynum === 10 ? '0' : $last_keynum;
    }

    public function getCacheKey()
    {
        $cachekey = array();
        $cachekey[] = $this->device->markup;
        $cachekey[] = $this->device->default_markup;
        $cachekey[] = $this->device->screenwidth;
        $cachekey[] = $this->device->screenheight;
        $cachekey[] = isset($this->device->pixelratio)
            ? $this->device->pixelratio
            : '1';
        $imageformats = $this->device->imageformats;
        if (is_array($imageformats)) {
            sort($imageformats);
            $cachekey[] = implode('', $imageformats);
        }
        return implode('_', $cachekey);
    }

    public function checkMarkup($markup)
    {
        if (($markup === false) || ($markup === null)) {
            return false;
        }
        static $markup_path;
        if (!isset($markup_path)) {
            $markup_path = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/markup/';
        }
        switch ($markup) {
            case 'desktop':
            case '':
                return '';
            default:
                if (class_exists('MobileJoomla_' . $markup, false) || file_exists($markup_path . $markup . '.php')) {
                    return $markup;
                }
        }
        return false;
    }
}
