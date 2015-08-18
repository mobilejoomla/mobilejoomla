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

abstract class MjJoomlaWrapper
{
    /**
     * @return MjJoomlaWrapper
     */
    public static function getInstance()
    {
        static $joomlaWrapper;

        if (!isset($joomlaWrapper)) {
            $legacy = array('3.0', '1.7', '1.6', '1.5');

            foreach ($legacy as $version) {
                if (version_compare(JVERSION, $version, '>=')) {
                    require_once dirname(__FILE__) . "/joomlawrapper-{$version}.php";
                    $className = 'MjJoomlaWrapper' . str_replace('.', '', $version);
                    $joomlaWrapper = new $className;
                    break;
                }
            }
        }

        return $joomlaWrapper;
    }

    /**
     * @return bool
     * */
    abstract public function checkACL();

    /**
     * @param string $name
     * @param string $default
     * @return string
     */
    abstract public function getRequestVar($name, $default = null);

    /**
     * @param string $name
     * @param string $default
     * @return string
     */
    abstract public function getRequestWord($name, $default = null);

    /**
     * @param string $name
     * @param int $default
     * @return int
     */
    abstract public function getRequestInt($name, $default = null);

    /**
     * @param string $langString
     * @param int $code
     */
    abstract public function raiseWarning($langString, $code = 403);

    /**
     * @param string $extension
     * @param string $path
     */
    abstract public function loadLanguageFile($extension, $path = JPATH_BASE);

    /**
     * @param string $table
     * @param string $nameColumn
     * @param string $valueColumn
     * @return array
     */
    abstract public function dbSelectAll($table, $nameColumn = 'name', $valueColumn = 'value');

    /**
     * @param array $data
     * @param string $table
     * @param string $nameColumn
     * @param string $valueColumn
     * @return bool
     */
    abstract public function dbSaveAll($data, $table, $nameColumn = 'name', $valueColumn = 'value');

    /**
     * @return bool
     */
    abstract public function isMjPluginEnabled();

    /**
     * @param bool $enabled
     */
    abstract public function enableMjPlugin($enabled);

    abstract public function loadMootools();

    /**
     * @param string $table
     * @param int $id
     * @param string $device
     * @return bool
     */
    abstract public function changeState($table, $id, $device);

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    abstract public function getConfig($name, $default = null);

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    abstract public function setConfig($name, $value);

    /**
     * @return JDatabaseDriver
     */
    abstract public function getDbo();
}
