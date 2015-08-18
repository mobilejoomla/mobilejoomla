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

jimport('joomla.installer.installer');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

$installDir = dirname(dirname(__FILE__));
if (!class_exists('MjJoomlaWrapper', false)) {
    include_once $installDir . '/legacy/joomlawrapper.php';
}
if (!class_exists('MjSettingsModel', false)) {
    include_once $installDir . '/models/settings.php';
}

class MjInstaller
{
    /**
     * @return string
     */
    public static function MJ_version()
    {
        return '###VERSION###';
    }

    /**
     * @return string
     */
    public static function MJ_publicVersion()
    {
        $ver = '###VERSION###';
        if (strpos($ver, '.pro') !== false) {
            $ver = str_replace('.pro', '', '###VERSION###');
            $type = 'Pro';
        } else {
            $file = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/version.dat';
            $type = file_exists($file) ? preg_replace('/\W+/', '', file_get_contents($file)) : 'Community';
        }
        return "$type $ver";
    }

    /**
     * @return bool
     */
    public static function isJoomla15()
    {
        static $is_joomla15;
        if (!isset($is_joomla15)) {
            $is_joomla15 = (substr(JVERSION, 0, 3) === '1.5');
        }
        return $is_joomla15;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function getConfig($name, $default = null)
    {
        $config = JFactory::getConfig();
        if (self::isJoomla15()) {
            return $config->getValue('config.' . $name, $default);
        } else {
            return $config->get($name, $default);
        }
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $group
     * @return int|bool
     */
    public static function getExtensionId($type, $name, $group = '')
    {
        $joomlaWrapper = MjJoomlaWrapper::getInstance();
        $db = $joomlaWrapper->getDbo();
        $query = new MjQueryBuilder($db);

        if (!self::isJoomla15()) {
            if ($type === 'plugin') {
                $query
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where($query->qn('type') . '=' . $query->q($type))
                    ->where($query->qn('folder') . '=' . $query->q($group))
                    ->where($query->qn('element') . '=' . $query->q($name))
                    ->setQuery();
            } else {
                $query
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where($query->qn('type') . '=' . $query->q($type))
                    ->where($query->qn('element') . '=' . $query->q($name))
                    ->setQuery();
            }
            return $db->loadResult();
        }
        //Joomla!1.5
        switch ($type) {
            case 'plugin':
                return $query
                    ->select('id')
                    ->from('#__plugins')
                    ->where($query->qn('folder') . '=' . $query->q($group))
                    ->where($query->qn('element') . '=' . $query->q($name))
                    ->setQuery()
                    ->loadResult();
            case 'module':
                return $query
                    ->select('id')
                    ->from('#__modules')
                    ->where($query->qn('module') . '=' . $query->q($name))
                    ->setQuery()
                    ->loadResult();
            case 'template':
                return $name;
            default:
                return false;
        }
    }

    /**
     * @param string $group
     * @param string $sourcedir
     * @param string $name
     * @param int $publish
     * @param int $ordering
     * @return bool
     */
    public static function InstallPlugin($group, $sourcedir, $name, $publish = 1, $ordering = 0)
    {
        try {
            $upgrade = self::getExtensionId('plugin', $name, $group);
            $installer = new JInstaller();
            if (!$installer->install($sourcedir . '/' . $name)) {
                JError::raiseError(0, JText::_('COM_MJ__CANNOT_INSTALL') . ' ' . $name . ' (plugin)');
                return false;
            }
            if (!$upgrade) {
                $joomlaWrapper = MjJoomlaWrapper::getInstance();
                $db = $joomlaWrapper->getDbo();
                $query = new MjQueryBuilder($db);

                if (!self::isJoomla15()) {
                    $query
                        ->update('#__extensions')
                        ->set($query->qn('enabled') . "=$publish")
                        ->set($query->qn('ordering') . "=$ordering")
                        ->where($query->qn('type') . '=' . $query->q('plugin'))
                        ->where($query->qn('element') . '=' . $query->q($name))
                        ->where($query->qn('folder') . '=' . $query->q($group))
                        ->setQuery();
                } else {
                    $query
                        ->update('#__plugins')
                        ->set($query->qn('published') . "=$publish")
                        ->set($query->qn('ordering') . "=$ordering")
                        ->where($query->qn('element') . '=' . $query->q($name))
                        ->where($query->qn('folder') . '=' . $query->q($group))
                        ->setQuery();
                }
                $db->query();
            }
            return true;
        } catch (Exception $e) {
            JError::raiseError(0, $e->getMessage());
            return false;
        }
    }

    /**
     * @param string $group
     * @param string $name
     * @return bool
     */
    public static function UninstallPlugin($group, $name)
    {
        try {
            $id = self::getExtensionId('plugin', $name, $group);
            $installer = new JInstaller();
            if (!$installer->uninstall('plugin', $id)) {
                JError::raiseError(0, JText::_('COM_MJ__CANNOT_UNINSTALL') . ' ' . $name . ' (plugin)');
                return false;
            }
            return true;
        } catch (Exception $e) {
            JError::raiseError(0, $e->getMessage());
            return false;
        }
    }

    public static function cleanPluginsCache()
    {
        $cache = JFactory::getCache('com_plugins', '');
        if (isset($cache->cache)) {
            $cache->cache->clean();
        } else {
            $cache->clean();
        }
    }

    /**
     * @param string $sourcedir
     * @param string $name
     * @return bool
     */
    public static function InstallTemplate($sourcedir, $name)
    {
        try {
            //hide warnings of template installing in Joomla!2.5.0-2.5.3
            $bugfix = (JVERSION >= '2.5.0' && JVERSION <= '2.5.3');
            if ($bugfix) {
                $error_reporting = error_reporting();
                error_reporting($error_reporting & (E_ALL ^ E_WARNING));
            }

            $installer = new JInstaller();
            if (!$installer->install($sourcedir . '/' . $name)) {
                JError::raiseError(0, JText::_('COM_MJ__CANNOT_INSTALL') . ' ' . $name . ' (template)');
                return false;
            }

            $joomlaWrapper = MjJoomlaWrapper::getInstance();
            $db = $joomlaWrapper->getDbo();

            $qName = $db->quote($name);

            if ($bugfix) {
                error_reporting($error_reporting);

                $query = new MjQueryBuilder($db);
                $query
                    ->select('MIN(' . $query->qn('id') . ') ')
                    ->from('#__template_styles')
                    ->where($query->qn('template') . '=' . $qName)
                    ->where($query->qn('client_id') . '=0')
                    ->group('template')
                    ->setQuery();
                $id = $db->loadResult();

                $query = new MjQueryBuilder($db);
                $query
                    ->delete('#__template_styles')
                    ->where($query->qn('template') . '=' . $qName)
                    ->where($query->qn('client_id') . '=0')
                    ->where($query->qn('id') . '<>' . (int)$id)
                    ->setQuery()
                    ->query();

                $query = new MjQueryBuilder($db);
                $id = $query
                    ->select('MAX(' . $query->qn('extension_id') . ') ')
                    ->from('#__extensions')
                    ->where($query->qn('element') . '=' . $qName)
                    ->where($query->qn('type') . '=' . $query->q('template'))
                    ->where($query->qn('client_id') . '=0')
                    ->group('element')
                    ->setQuery()
                    ->loadResult();

                $query = new MjQueryBuilder($db);
                $query
                    ->delete('#__extensions')
                    ->where($query->qn('element') . '=' . $qName)
                    ->where($query->qn('type') . '=' . $query->q('template'))
                    ->where($query->qn('client_id') . '=0')
                    ->where($query->qn('extension_id') . '<>' . (int)$id)
                    ->setQuery()
                    ->query();
            }

            if (self::isJoomla15()) {
                $query = new MjQueryBuilder($db);
                $query
                    ->select('COUNT(*) ')
                    ->from('#__templates_menu')
                    ->where($query->qn('template') . '=' . $qName)
                    ->setQuery();
                if ($db->loadResult() === 0) {
                    $query = new MjQueryBuilder($db);
                    $query
                        ->insert('#__templates_menu')
                        ->set($query->qn('template') . '=' . $qName)
                        ->set($query->qn('menuid') . '=-1')
                        ->setQuery()
                        ->query();
                }
                $params_ini = JPATH_SITE . '/templates/' . $name . '/params.ini';
                if (!is_file($params_ini)) {
                    $data = '';
                    JFile::write($params_ini, $data);
                }
            }
            $path_css = JPATH_SITE . '/templates/' . $name . '/css';
            if (is_dir($path_css)) {
                $custom_css = $path_css . '/custom.css';
                if (!is_file($custom_css)) {
                    $data = '';
                    JFile::write($custom_css, $data);
                }
            }
            return true;
        } catch (Exception $e) {
            JError::raiseError(0, $e->getMessage());
            return false;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function UninstallTemplate($name)
    {
        try {
            $id = self::getExtensionId('template', $name);
            $installer = new JInstaller();
            if (!$installer->uninstall('template', $id)) {
                JError::raiseError(0, JText::_('COM_MJ__CANNOT_UNINSTALL') . ' ' . $name . ' (template)');
                return false;
            }
            if (self::isJoomla15()) {
                $joomlaWrapper = MjJoomlaWrapper::getInstance();
                $db = $joomlaWrapper->getDbo();
                $query = new MjQueryBuilder($db);
                $query
                    ->delete('#__templates_menu')
                    ->where($query->qn('template') . '=' . $query->q($name))
                    ->setQuery()
                    ->query();
            }
            return true;
        } catch (Exception $e) {
            JError::raiseError(0, $e->getMessage());
            return false;
        }
    }

    /**
     * @param string $sourcedir
     * @param string $name
     * @param string $title
     * @param string[]|string $position
     * @param int $published
     * @param int $showtitle
     * @param int $admin
     * @return bool
     */
    public static function InstallModule($sourcedir, $name, $title, $position = array(), $published = 1, $showtitle = 1, $admin = 0)
    {
        try {
            $upgrade = self::getExtensionId('module', $name);
            $installer = new JInstaller();
            if (!$installer->install($sourcedir . '/' . $name)) {
                JError::raiseError(0, JText::_('COM_MJ__CANNOT_INSTALL') . ' ' . $name . ' (module)');
                return false;
            }
            if (!$upgrade) {
                self::createModules($name, $title, $position, $published, $showtitle, $admin);
            }
            return true;
        } catch (Exception $e) {
            JError::raiseError(0, $e->getMessage());
            return false;
        }
    }

    /**
     * @param string $name
     * @param string $title
     * @param string[]|string $positions
     * @param int $published
     * @param int $showtitle
     * @param int $admin
     * @param array $params
     * @return bool
     */
    public static function createModules($name, $title, $positions = array(), $published = 1, $showtitle = 1, $admin = 0, $params = array())
    {
        try {
            $positions = (array)$positions;
            $published = $published ? 1 : 0;

            if (count($positions) === 0) {
                return true;
            }

            $id = self::getExtensionId('module', $name);
            if (!$id) {
                return false;
            }

            $joomlaWrapper = MjJoomlaWrapper::getInstance();
            $db = $joomlaWrapper->getDbo();

            jimport('joomla.registry.registry');
            $newparams = new JRegistry;

            $query = new MjQueryBuilder($db);
            if (!self::isJoomla15()) {
                $query
                    ->select('params')
                    ->from('#__extensions')
                    ->where($query->qn('extension_id') . '=' . $id)
                    ->setQuery();
                $newparams->loadString($db->loadResult());
                foreach ($params as $key => $value) {
                    $newparams->set($key, $value);
                }
            } else {
                $query
                    ->select('params')
                    ->from('#__modules')
                    ->where($query->qn('id') . '=' . $id)
                    ->setQuery();
                $newparams->loadINI($db->loadResult());
                foreach ($params as $key => $value) {
                    $newparams->setValue($key, $value);
                }
            }

            if ($admin) {
                $access = self::isJoomla15() ? 2 : 3;
            } else {
                $access = self::isJoomla15() ? 0 : 1;
            }

            foreach ($positions as $pos) {
                if (empty($pos)) {
                    continue;
                }

                $query = new MjQueryBuilder($db);
                $count = $query
                    ->select('COUNT(*) ')
                    ->from('#__modules')
                    ->where($query->qn('position') . '=' . $query->q($pos))
                    ->where($query->qn('module') . '=' . $query->q($name))
                    ->setQuery()
                    ->loadResult();
                if ($count > 0) {
                    continue;
                }

                $query = new MjQueryBuilder($db);
                $ordering = $query
                    ->select('MAX(' . $query->qn('ordering') . ') ')
                    ->from('#__modules')
                    ->where($query->qn('position') . '=' . $query->q($pos))
                    ->setQuery()
                    ->loadResult();
                ++$ordering;

                $query = new MjQueryBuilder($db);
                $query
                    ->insert('#__modules')
                    ->set($query->qn('title') . '=' . $query->q($title))
                    ->set($query->qn('ordering') . '=' . $ordering)
                    ->set($query->qn('position') . '=' . $query->q($pos))
                    ->set($query->qn('published') . '=' . $published)
                    ->set($query->qn('module') . '=' . $query->q($name))
                    ->set($query->qn('showtitle') . '=' . $showtitle)
                    ->set($query->qn('params') . '=' . $query->q($newparams->toString()))
                    ->set($query->qn('access') . '=' . $access)
                    ->set($query->qn('client_id') . '=' . $admin);
                if (!self::isJoomla15()) {
                    $query->set($query->qn('language') . '=' . $query->q('*'));
                }
                $query
                    ->setQuery()
                    ->query();
                $id = (int)$db->insertid();

                $query = new MjQueryBuilder($db);
                $query
                    ->insert('#__modules_menu')
                    ->set($query->qn('moduleid') . '=' . $id)
                    ->set($query->qn('menuid') . '=0')
                    ->setQuery()
                    ->query();
            }
            return true;
        } catch (Exception $e) {
            JError::raiseError(0, $e->getMessage());
            return false;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function UninstallModule($name)
    {
        try {
            $id = self::getExtensionId('module', $name);
            $installer = new JInstaller();
            if (!$installer->uninstall('module', $id)) {
                JError::raiseError(0, JText::_('COM_MJ__CANNOT_UNINSTALL') . ' ' . $name . ' (module)');
                return false;
            }
            return true;
        } catch (Exception $e) {
            JError::raiseError(0, $e->getMessage());
            return false;
        }
    }

    /**
     * @param string $prev_version
     * @return bool
     */
    public static function UpdateConfig($prev_version)
    {
        $upgrade = (boolean)$prev_version;

        $joomlaWrapper = MjJoomlaWrapper::getInstance();

        $mjSettings = new MjSettingsModel($joomlaWrapper);

        $defconfig = json_decode(file_get_contents(dirname(dirname(__FILE__)) . '/defconfig.json'));
        $mjSettings->def($defconfig);

        if (!$upgrade) { // first install
            $mjSettings->set('enabled', 1);

            // @todo check that current template is responsive (e.g. by viewport meta-tag)
            $template_id = 'mobile_basic';
            if (!self::isJoomla15()) {
                $query = new MjQueryBuilder($joomlaWrapper->getDbo());
                $template_id = $query
                    ->select('id')
                    ->from('#__template_styles')
                    ->where($query->qn('template') . '=' . $query->q($template_id))
                    ->where('client_id=0')
                    ->setQuery()
                    ->loadResult();
            }
            $mjSettings->set('mobile.template', $template_id);

            $mjSettings->set('mobile_sitename', self::getConfig('sitename'));
            $mjSettings->set('.gzip', self::getConfig('gzip'));
            $mjSettings->set('distribmode', 'php');
            if (isset($_SERVER['SERVER_SOFTWARE'])
                && function_exists('apache_get_modules')
                && strpos($_SERVER['SERVER_SOFTWARE'], 'Apache/') !== false
            ) {
                $apache_modules = apache_get_modules();
                if (in_array('mod_rewrite', $apache_modules, true)) {
                    if (in_array('mod_mime', $apache_modules, true) && in_array('mod_headers', $apache_modules, true)) {
                        $mjSettings->set('distribmode', 'apache');
                    } else {
                        $mjSettings->set('distribmode', 'apachephp');
                    }
                }
            }
        } else { // update from previous version
            $updates_dir = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/updates';
            /** @var string[]|false $updates */
            $updates = scandir($updates_dir);
            foreach ($updates as $file) {
                if ($file[0] === '.') {
                    continue;
                }
                $version = str_replace('.php', '', $file);
                if (version_compare($version, $prev_version, '>')) {
                    include $updates_dir . '/' . $file;
                }
            }
        }

        $mjSettings->set('desktop_domain', @$_SERVER['HTTP_HOST']);

        // check for GD2 library
        if (!function_exists('imagecopyresized')) {
            JError::raiseWarning(0, JText::_('COM_MJ__GD2_LIBRARY_IS_NOT_LOADED'));
            $mjSettings->set('.img', 0);
            $mjSettings->set('mobile.img', '');
        }

        if (function_exists('MJAddonUpdateConfig')) {
            MJAddonUpdateConfig($mjSettings);
        }

        $mjSettings->save();

        return true;
    }

    public static function create_amdd_db()
    {
        include_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/ress/vendor/amdd/amdd.php';
        $file = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/ress/setup/amdd_data.gz';
        $options = array('handler' => 'joomla', 'dbTableName' => '#__mj_amdd');
        Amdd::updateDatabaseFromFile($file, $options);
    }

    public static function clear_amdd_db()
    {
        include_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/ress/vendor/amdd/amdd.php';
        $options = array('handler' => 'joomla', 'dbTableName' => '#__mj_amdd');
        Amdd::dropDatabase($options);
    }

    /**
     * @param string $str
     * @return int
     */
    private static function str2int($str)
    {
        $unit = strtoupper(substr($str, -1));
        $num = (int)substr($str, 0, -1);
        switch ($unit) {
            case 'G':
                $num *= 1024;
            case 'M':
                $num *= 1024;
            case 'K':
                $num *= 1024;
                break;
            default:
                $num = (int)$str;
        }
        return $num;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public static function install()
    {
        JError::setErrorHandling(E_ERROR, 'Message');

        set_time_limit(1200);
        ini_set('max_execution_time', 1200);
        ignore_user_abort(true);

        $mj_memory_limit = '32M';
        $memory_limit = ini_get('memory_limit');
        if ($memory_limit && self::str2int($memory_limit) < self::str2int($mj_memory_limit)) {
            ini_set('memory_limit', $mj_memory_limit);
        }

        $lang = JFactory::getLanguage();
        $lang->load('com_mobilejoomla');

        // check for upgrade
        $upgrade = false;
        $prev_version = '';
        $manifest = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/mobilejoomla.xml';
        if (is_file($manifest)) {
            $xml = simplexml_load_file($manifest);
            if (isset($xml->version)) {
                $prev_version = (string)$xml->version;
            }
            if ($prev_version) {
                $upgrade = true;
            }
        }

        $xm_files = JFolder::files(JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages', '\.xm_$', 2, true);
        if (!empty($xm_files)) {
            foreach ($xm_files as $file) {
                $newfile = str_replace('.xm_', '.xml', $file);
                JFile::move($file, $newfile);
                if (self::isJoomla15()) {
                    $content = file_get_contents($newfile);
                    $content = str_replace(array('<extension ', '</extension>'), array('<install ', '</install>'), $content);
                    JFile::write($newfile, $content);
                }
            }
        }

        $joomlaWrapper = MjJoomlaWrapper::getInstance();
        $db = $joomlaWrapper->getDbo();

        $addons_installer = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/install.addons.php';
        if (JFile::exists($addons_installer)) {
            include($addons_installer);
        }

        jimport('joomla.filesystem.folder');

        $query = new MjQueryBuilder($db);

        //table for settings
        $query->createTable(
            '#__mj_settings',
            array(
                'name' => array(
                    'type' => 'varchar',
                    'size' => 32,
                    'notnull' => true
                ),
                'value' => array(
                    'type' => 'varchar',
                    'size' => 255,
                    'notnull' => true
                )
            ),
            array(),
            array('if_not_exists' => true, 'charset' => 'utf8')
        );

        //tables for extmanager
        $query->createTable(
            '#__mj_modules',
            array(
                'id' => array(
                    'type' => 'bigint',
                    'notnull' => true
                ),
                'device' => array(
                    'type' => 'varchar',
                    'size' => 32,
                    'notnull' => true
                )
            ),
            array(
                '@primary' => array('device', 'id')
            ),
            array('if_not_exists' => true)
        );

        $query->createTable(
            '#__mj_plugins',
            array(
                'id' => array(
                    'type' => 'bigint',
                    'notnull' => true
                ),
                'device' => array(
                    'type' => 'varchar',
                    'size' => 32,
                    'notnull' => true
                )
            ),
            array(
                '@primary' => array('device', 'id')
            ),
            array('if_not_exists' => true)
        );

        //directory for ress cache
        JFolder::create(JPATH_ROOT . '/cache/mj');
        JFolder::create(JPATH_ROOT . '/media/mj');

        // install templates
        if (version_compare(JVERSION, '3.0', '>=')) {
            $TemplateSource = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/templates3x';
            JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/templates15');
            JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/templates25');
        } elseif (version_compare(JVERSION, '1.6', '>=')) {
            $TemplateSource = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/templates25';
            $TemplateSource30 = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/templates3x';
            JFolder::move($TemplateSource30 . '/mobile_basic/vendor', $TemplateSource . '/mobile_basic/vendor');
            JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/templates15');
            JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/templates3x');
        } else {
            $TemplateSource = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/templates15';
            $TemplateSource30 = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/templates3x';
            JFolder::move($TemplateSource30 . '/mobile_basic/vendor', $TemplateSource . '/mobile_basic/vendor');
            JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/templates25');
            JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/templates3x');
        }

        $templates = array('mobile_basic');
        $status = true;
        foreach ($templates as $template) {
            if (!self::InstallTemplate($TemplateSource, $template)) {
                $status = false;
                JError::raiseError(0, JText::_('COM_MJ__CANNOT_INSTALL') . " Mobile Joomla! '$template' template.");
            }
        }

        if (function_exists('MJAddonInstallTemplates')) {
            $status = MJAddonInstallTemplates($TemplateSource) && $status;
        }

        if ($status) {
            JFolder::delete($TemplateSource);
        }

        //install modules
        $ModuleSource = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/modules';
        $status = true;
        $status = self::InstallModule($ModuleSource, 'mod_mj_proxymodule', 'Proxy Module') && $status;
        $status = self::InstallModule($ModuleSource, 'mod_mj_proxyposition', 'Proxy Position') && $status;
        $status = self::InstallModule($ModuleSource, 'mod_mj_switcher', 'Mobile Switcher',
                array('footer', 'mj_footer2'), 1, 0) && $status;
        if (version_compare(JVERSION, '2.5', '<')) {
            $status = self::InstallModule($ModuleSource, 'mod_mj_adminicon', 'Mobile Joomla! CPanel Icons', 'icon', 1, 0, 1) && $status;
        }

        // create mobile menu module
        if (!$upgrade) {
            $menutype = 'mainmenu';

            $query = new MjQueryBuilder($db);
            $query
                ->select('COUNT(*) ')
                ->from('#__menu')
                ->where($query->qn('menutype') . '=' . $query->q('mainmenu'))
                ->where($query->qn('published') . '=1');
            if (!self::isJoomla15()) {
                $query->where($query->qn('client_id') . '=0');
            }
            $count = (int)$query->setQuery()->loadResult();
            if ($count === 0) {
                $query = new MjQueryBuilder($db);
                $query
                    ->select('menutype')
                    ->from('#__menu')
                    ->where($query->qn('published') . '=1')
                    ->where($query->qn('home') . '=1');
                if (!self::isJoomla15()) {
                    $query
                        ->where($query->qn('client_id') . '=0')
                        ->where($query->qn('language') . '=' . $query->q('*'));
                }
                $type = $query->setQuery()->loadResult();
                if ($type !== null) {
                    $menutype = $type;
                }
            }
            $status = self::createModules(
                    self::isJoomla15() ? 'mod_mainmenu' : 'mod_menu',
                    'Menu', 'mj_panel', 1, 0, 0,
                    array('menutype' => $menutype)
                ) && $status;
        }

        if (function_exists('MJAddonInstallModules')) {
            $status = MJAddonInstallModules($ModuleSource) && $status;
        }

        if ($status) {
            JFolder::delete($ModuleSource);
        } else {
            JError::raiseError(0, JText::_('COM_MJ__CANNOT_INSTALL') . ' Mobile Joomla! modules.');
        }

        //install plugins
        $PluginSource = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/plugins';
        $status = true;

        if (!self::InstallPlugin('system', $PluginSource, 'mobilejoomla', 1, -99)) {
            $status = false;
            JError::raiseError(0, JText::_('COM_MJ__CANNOT_INSTALL') . ' Mobile Joomla! Plugin.');
        }

        $plugin_table = self::isJoomla15() ? '#__plugins' : '#__extensions';
        $query = new MjQueryBuilder($db);
        $rows = $query
            ->select('element', 'ordering')
            ->from($plugin_table)
            ->where($query->qn('element') . ' IN (' . $query->q('mobilejoomla') . ', ' . $query->q('cache') . ')')
            ->where($query->qn('folder') . '=' . $query->q('system'))
            ->setQuery()
            ->loadObjectList('element');
        if (isset($rows['cache']) && $rows['cache']->ordering <= $rows['mobilejoomla']->ordering) {
            $ordering = max(0, $rows['mobilejoomla']->ordering + 1);
            $query = new MjQueryBuilder($db);
            $query
                ->update($plugin_table)
                ->set($query->qn('ordering') . '=' . $ordering)
                ->where($query->qn('element') . '=' . $query->q('cache'))
                ->where($query->qn('folder') . '=' . $query->q('system'))
                ->setQuery()
                ->query();
        }

        // install quickicon plugin
        if (!self::InstallPlugin('quickicon', $PluginSource, 'mjcpanel')) {
            $status = false;
            JError::raiseError(0, JText::_('COM_MJ__CANNOT_INSTALL') . ' Quickicon - Mobile Joomla! CPanel Icon.');
        }

        // install mobile plugins
        if (!JFolder::create(JPATH_PLUGINS . '/mobile')) {
            $status = false;
            JError::raiseError(0, JText::_('COM_MJ__CANNOT_CREATE_DIRECTORY') . ' ' . JPATH_PLUGINS . '/mobile');
        }
        $checkers = array('simple' => 7, 'always' => 8, 'domains' => 9);
        foreach ($checkers as $plugin => $order)
            if (!self::InstallPlugin('mobile', $PluginSource, $plugin, 1, $order)) {
                $status = false;
                JError::raiseError(0, JText::_('COM_MJ__CANNOT_INSTALL') . ' Mobile - ' . ucfirst($plugin) . '.');
            }

        // install amdd plugin
        if (!self::InstallPlugin('mobile', $PluginSource, 'amdd')) {
            $status = false;
            JError::raiseError(0, JText::_('COM_MJ__CANNOT_INSTALL') . ' Mobile - AMDD');
        } else {
            self::create_amdd_db();
        }

        if (function_exists('MJAddonInstallPlugins')) {
            $status = MJAddonInstallPlugins($PluginSource) && $status;
        }

        if ($status) {
            JFolder::delete($PluginSource);
        }

        //update config & files
        self::UpdateConfig($prev_version);

        self::cleanPluginsCache();

        //Show install status
        $msg = '';
        $count = 0;
        /** @var JException $error */
        foreach (JError::getErrors() as $error) {
            if ($error->get('level') & E_ERROR) {
                $count++;
            }
        }
        if ($count === 0) {
            $msg .= str_replace('[VER]', self::MJ_publicVersion(), JText::_('COM_MJ__INSTALL_OK'));
        }
        ?>
        <link rel="stylesheet" type="text/css" href="components/com_mobilejoomla/assets/css/j3x_template.css"/>
        <link rel="stylesheet" type="text/css"
              href="http://www.mobilejoomla.com/checker.php?v=<?php echo urlencode(self::MJ_version()); ?>&amp;s=1&amp;j=<?php echo urlencode(JVERSION); ?>"/>
        <div id="mj">
            <div class="well"><a href="http://www.mobilejoomla.com/" id="mjupdate" target="_blank"></a>
                <?php echo $msg; ?>
            </div>
        </div>
        <?php
        $postInstallActions = array();
        if (function_exists('MJAddonPostInstall')) {
            MJAddonPostInstall($postInstallActions);
        }

        return true;
    }

    /**
     * @return bool
     */
    public static function uninstall()
    {
        JError::setErrorHandling(E_ERROR, 'Message');

        $lang = JFactory::getLanguage();
        $lang->load('com_mobilejoomla');

        $addons_installer = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/packages/install.addons.php';
        if (JFile::exists($addons_installer)) {
            include($addons_installer);
        }

        //uninstall plugins
        if (function_exists('MJAddonUninstallPlugins')) {
            MJAddonUninstallPlugins();
        }
        if (!self::UninstallPlugin('system', 'mobilejoomla')) {
            JError::raiseError(0, JText::_('COM_MJ__CANNOT_UNINSTALL') . ' Mobile Joomla! Plugin.');
        }
        if (!self::UninstallPlugin('quickicon', 'mjcpanel')) {
            JError::raiseError(0, JText::_('COM_MJ__CANNOT_UNINSTALL') . ' Quickicon - Mobile Joomla! CPanel Icon.');
        }
        $checkers = array('simple', 'always', 'domains');
        foreach ($checkers as $plugin) {
            if (!self::UninstallPlugin('mobile', $plugin)) {
                JError::raiseError(0, JText::_('COM_MJ__CANNOT_UNINSTALL') . ' Mobile - ' . ucfirst($plugin) . '.');
            }
        }
        //uninstall amdd
        if (self::getExtensionId('plugin', 'amdd', 'mobile') !== null) {
            if (!self::UninstallPlugin('mobile', 'amdd')) {
                JError::raiseError(0, JText::_('COM_MJ__CANNOT_UNINSTALL') . ' Mobile - AMDD.');
            }
            self::clear_amdd_db();
        }

        //uninstall templates
        if (function_exists('MJAddonUninstallTemplates')) {
            MJAddonUninstallTemplates();
        }
        $templateslist = array('mobile_basic', 'mobile_smartphone', 'mobile_wap', 'mobile_imode', 'mobile_iphone');
        foreach ($templateslist as $t) {
            if (JFolder::exists(JPATH_SITE . '/templates/' . $t) && !self::UninstallTemplate($t)) {
                JError::raiseError(0, JText::_('COM_MJ__CANNOT_UNINSTALL') . " Mobile Joomla! '$t' template.");
            }
        }

        //uninstall modules from previous MJ releases
        $moduleslist = array('mod_mj_pda_menu', 'mod_mj_wap_menu', 'mod_mj_imode_menu', 'mod_mj_iphone_menu', 'mod_mj_header');
        foreach ($moduleslist as $m) {
            if (JFolder::exists(JPATH_SITE . '/modules/' . $m) && !self::UninstallModule($m)) {
                JError::raiseError(0, JText::_('COM_MJ__CANNOT_UNINSTALL') . " Mobile Joomla! '$m' module.");
            }
        }

        if (function_exists('MJAddonUninstallModules')) {
            MJAddonUninstallModules();
        }
        $moduleslist = array('mod_mj_switcher', 'mod_mj_proxymodule', 'mod_mj_proxyposition');
        if (version_compare(JVERSION, '2.5', '<')) {
            $moduleslist[] = 'mod_mj_adminicon';
        }
        if (self::getExtensionId('module', 'mod_mj_menu')) {
            $moduleslist[] = 'mod_mj_menu';
        }
        foreach ($moduleslist as $m) {
            if (!self::UninstallModule($m)) {
                JError::raiseError(0, JText::_('COM_MJ__CANNOT_UNINSTALL') . " Mobile Joomla! '$m' module.");
            }
        }

        // remove extmanager tables
        $joomlaWrapper = MjJoomlaWrapper::getInstance();
        $db = $joomlaWrapper->getDbo();
        $query = new MjQueryBuilder($db);
        $query->dropTable('#__mj_settings', '#__mj_modules', '#__mj_plugins');

        self::cleanPluginsCache();

        // remove directories
        JFolder::delete(JPATH_ROOT . '/media/mj');
        JFolder::delete(JPATH_ROOT . '/cache/mj');

        //Show uninstall status
        $msg = '';
        $count = 0;
        /** @var JException $error */
        foreach (JError::getErrors() as $error) {
            if ($error->get('level') & E_ERROR) {
                $count++;
            }
        }
        if ($count === 0) {
            $msg .= str_replace('[VER]', self::MJ_publicVersion(), JText::_('COM_MJ__UNINSTALL_OK'));
        }
        ?>
        <link rel="stylesheet" type="text/css" href="components/com_mobilejoomla/assets/css/j3x_template.css"/>
        <link rel="stylesheet" type="text/css"
              href="http://www.mobilejoomla.com/checker.php?v=<?php echo urlencode(self::MJ_version()); ?>&amp;s=2&amp;j=<?php echo urlencode(JVERSION); ?>"/>
        <div id="mj">
            <div class="well"><a href="http://www.mobilejoomla.com/" id="mjupdate" target="_blank"></a>
                <?php echo $msg; ?>
            </div>
        </div>
        <?php
        return true;
    }

}
