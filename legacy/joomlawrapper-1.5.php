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

require_once dirname(__FILE__) . '/joomlawrapper.php';

class MjJoomlaWrapper15 extends MjJoomlaWrapper
{
    public function checkACL()
    {
        return true;
    }

    public function getRequestVar($name, $default = null)
    {
        return JRequest::getVar($name, $default);
    }

    public function getRequestWord($name, $default = null)
    {
        return JRequest::getWord($name, $default);
    }

    public function getRequestInt($name, $default = null)
    {
        return JRequest::getInt($name, $default);
    }

    public function raiseWarning($langString, $code = 403)
    {
        JError::raiseWarning($code, JText::_($langString));
    }

    public function loadLanguageFile($extension, $path = JPATH_BASE)
    {
        $lang = JFactory::getLanguage();
        $lang->load($extension, $path, 'en-GB', true);
        $lang->load($extension, $path, null, true);
    }

    public function dbSelectAll($table, $nameColumn = 'name', $valueColumn = 'value')
    {
        $result = array();

        $db = JFactory::getDbo();
        $query = "SELECT `$nameColumn`, `$valueColumn` FROM `$table`";
        $db->setQuery($query);
        /** @var array $rows */
        $rows = $db->loadAssocList();
        foreach ($rows as $row) {
            $result[$row[$nameColumn]] = $row[$valueColumn];
        }

        return $result;
    }

    /**
     * @param array $data
     * @param string $table
     * @param string $nameColumn
     * @param string $valueColumn
     * @return bool
     */
    public function dbSaveAll($data, $table, $nameColumn = 'name', $valueColumn = 'value')
    {
        $db = JFactory::getDbo();

        $origData = $this->dbSelectAll($table, $nameColumn, $valueColumn);

        foreach ($data as $key => $value) {
            if (isset($origData[$key])) {
                if ($origData[$key] !== $value) {
                    $query = "UPDATE `$table` SET `$valueColumn`=" . $db->Quote($value) . " WHERE `$nameColumn`=" . $db->Quote($key);
                    $db->setQuery($query);
                    $db->query();
                }
            } else {
                $query = "INSERT INTO `$table` (`$nameColumn`, `$valueColumn`) VALUES (" . $db->Quote($key) . ", " . $db->Quote($value) . ")";
                $db->setQuery($query);
                $db->query();
            }
        }

        return true;
    }

    public function isMjPluginEnabled()
    {
        jimport('joomla.plugin.helper');
        return JPluginHelper::isEnabled('system', 'mobilejoomla');
    }

    public function enableMjPlugin($enabled)
    {
        $db = JFactory::getDbo();
        $query = "UPDATE `#__plugins` SET `published`=" . ($enabled ? '1' : '0')
            . " WHERE `folder`='system' AND `element`='mobilejoomla'";
        $db->setQuery($query);
        return $db->query();
    }

    public function loadMootools()
    {
        JHtml::_('behavior.mootools');
    }

    public function changeState($table, $id, $device)
    {
        $db = JFactory::getDBO();

        $query = "SELECT COUNT(*) FROM $table WHERE id=$id AND device=" . $db->Quote($device);
        $db->setQuery($query);
        $unpublished = $db->loadResult();

        if ($unpublished) {
            $query = "DELETE FROM $table WHERE id=$id AND device=" . $db->Quote($device);
            $db->setQuery($query);
            $db->query();
            return true;
        } else {
            $query = "INSERT INTO $table (id, device) VALUES ($id, " . $db->Quote($device) . ")";
            $db->setQuery($query);
            $db->query();
            return false;
        }
    }

    public function getConfig($name, $default = null)
    {
        /** @var JRegistry $config */
        $config = JFactory::getConfig();
        return $config->getValue('config.' . $name, $default);
    }

    public function setConfig($name, $value)
    {
        /** @var JRegistry $config */
        $config = JFactory::getConfig();
        return $config->setValue('config.' . $name, $value);
    }

    public function getDbo()
    {
        static $database;
        if (!$database) {
            $__dir__ = dirname(__FILE__);
            require_once $__dir__ . '/databasewrapper.php';
            require_once dirname($__dir__) . '/classes/mjquerybuilder.php';
            $database = new MjDatabaseWrapper(JFactory::getDbo());
        }
        return $database;
    }
}