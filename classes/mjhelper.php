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

/* @todo: merge with MobileJoomla class */
class MjHelper
{
    /**
     * @return array
     */
    static public function getDeviceList()
    {
        static $deviceList;
        if (!isset($deviceList)) {
            $dispatcher = JDispatcher::getInstance();
            $result = $dispatcher->trigger('onMjGetDeviceList');

            array_unshift($result, array('desktop' => 'desktop'));
            $deviceList = call_user_func_array('array_merge', $result);
        }

        return $deviceList;
    }

    static public function currentVersion()
    {
        // community / basic / pro
        $version = '###VERSION###';

        $version_dat = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/version.dat';
        if (is_file($version_dat)) {
            $edition = file_get_contents($version_dat);
        } else {
            $edition = 'Community';
        }

        switch ($edition) {
            case 'Community':
            case 'Pro':
                break;
            case 'Basic':
                $version .= '.' . strtolower($edition);
                break;
        }

        return $version;
    }

    static public function cssCheckUpdate()
    {
        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_mobilejoomla/assets/css/mjbanner.css');

        jimport('joomla.plugins.helper');
        if (JPluginHelper::isEnabled('mobile', 'scientia')) {
            $detector = 'wurfl';
        } elseif (JPluginHelper::isEnabled('mobile', 'amdd')) {
            $detector = 'amdd';
        } else {
            $detector = 'simple';
        }
        $js = '(function(d){'
            . 'var s=d.createElement("link");'
            . 's.href="//www.mobilejoomla.com/checker.php?v=' . urlencode(self::currentVersion())
            . '&amp;j=' . urlencode(JVERSION)
            . '&amp;d=' . $detector . '";'
            . 's.rel="stylesheet";'
            . 's.type="text/css";'
            . 's.media="only x";'
            . 'd.getElementsByTagName("head")[0].appendChild(s);'
            . 'setTimeout(function(){s.media="all"});'
            . '})(document);';
        $document->addScriptDeclaration($js);
    }

    static public function jsGetNotification()
    {
        // @todo by analogy to joomla's updater
        include_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/legacy/joomlawrapper.php';
        $joomlaWrapper = MjJoomlaWrapper::getInstance();
        $joomlaWrapper->loadLanguageFile('com_mobilejoomla', JPATH_ADMINISTRATOR);

        /** @var JDocumentHtml $doc */
        $doc = JFactory::getDocument();
        //COM_MJ__NEW_VERSION_AVAILABLE="Mobile Joomla! <span class='label label-important'>%s</span> is available: <a class='btn btn-primary' href='index.php?component=com_mobilejoomla&controller=update'>Update now</a>"
        $doc->addScriptDeclaration('var mj_updater_text="' . addslashes(JText::_('COM_MJ__NEW_VERSION_AVAILABLE')) . '";');
        $doc->addScript('components/com_mobilejoomla/assets/js/mj_ui.js?v=' . self::currentVersion() . '&j=' . JVERSION);
    }

    /**
     * @param string $folder
     * @param string $name
     * @return string
     */
    static private function getPluginPath($folder, $name)
    {
        return
            JPATH_PLUGINS
            . '/' . $folder
            . (version_compare(JVERSION, '1.6', '>=') ? '/' . $name : '')
            . '/' . $name . '.php';
    }

    /**
     * @param MjSettingsModel $mjSettings
     */
    static public function jsGetRecommendation($mjSettings)
    {
        $domain = $mjSettings->get('desktop_domain');

        $stdTemplates = array('mobile_basic', 'mobile_smartphone', 'mobile_iphone', 'mobile_imode', 'mobile_wap');
        $isStdTpl = true;
        foreach ($mjSettings->getAll() as $key => $value) {
            if (preg_match('#\.template$#', $key) && !in_array($value, $stdTemplates, true)) {
                $isStdTpl = false;
                break;
            }
        }

        $recommend = array(
            'mj' => self::currentVersion(),
            'j' => JVERSION,
            'domain' => $domain,
            'stdtpl' => (int)$isStdTpl,
            'jcomments' => (int)(file_exists(JPATH_ROOT . '/components/com_jcomments')
                && !file_exists(self::getPluginPath('mobile', 'mobilejcomments'))),
            'kunena' => (int)(file_exists(JPATH_ROOT . '/components/com_kunena')
                && !file_exists(self::getPluginPath('mobile', 'mobilekunena')))
        );

        /** @var JDocumentHtml $doc */
        $doc = JFactory::getDocument();
        $doc->addScript('//www.mobilejoomla.com/recommend.php?' . http_build_query($recommend, '', '&amp;'),
            'text/javascript', true, true);
    }

    static public function jsGetExpiration()
    {

    }
}