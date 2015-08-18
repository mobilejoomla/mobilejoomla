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

class MjJoomlaWrapper30 extends MjJoomlaWrapper
{
    public function checkACL()
    {
        return JFactory::getUser()->authorise('core.manage', 'com_mobilejoomla');
    }

    public function getRequestVar($name, $default = null)
    {
        return JFactory::getApplication()->input->get($name, $default);
    }

    public function getRequestWord($name, $default = null)
    {
        return JFactory::getApplication()->input->getWord($name, $default);
    }

    public function getRequestInt($name, $default = null)
    {
        return JFactory::getApplication()->input->getInt($name, $default);
    }

    public function raiseWarning($langString, $code = 403)
    {
        $app = JFactory::getApplication();
        $app->enqueueMessage(JText::_($langString), 'error');
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

        $query = $db->getQuery(true);
        $query->select($db->quoteName($nameColumn));
        $query->select($db->quoteName($valueColumn));
        $query->from($table);

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
                    $query = $db->getQuery(true);
                    $query->update($table);
                    $query->set($db->quoteName($valueColumn) . '=' . $db->quote($value));
                    $query->where($db->quoteName($nameColumn) . '=' . $db->quote($key));
                    $db->setQuery($query);
                    $db->execute();
                }
            } else {
                $query = $db->getQuery(true);
                $query->insert($table);
                $query->set($db->quoteName($nameColumn) . '=' . $db->quote($key));
                $query->set($db->quoteName($valueColumn) . '=' . $db->quote($value));
                $db->setQuery($query);
                $db->execute();
            }
        }

        return true;
    }

    public function isMjPluginEnabled()
    {
        return JPluginHelper::isEnabled('system', 'mobilejoomla');
    }

    public function enableMjPlugin($enabled)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->update('#__extensions');
        $query->set($db->quoteName('enabled') . '=' . ($enabled ? '1' : '0'));
        $query->where($db->quoteName('type') . '=' . $db->quote('plugin'));
        $query->where($db->quoteName('folder') . '=' . $db->quote('system'));
        $query->where($db->quoteName('element') . '=' . $db->quote('mobilejoomla'));

        $db->setQuery($query);
        return $db->execute();
    }

    public function loadMootools()
    {
        JHtml::_('behavior.framework', true);
    }

    public function changeState($table, $id, $device)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('COUNT(*)');
        $query->from($db->quoteName($table));
        $query->where($db->quoteName('id') . '=' . (int)$id);
        $query->where($db->quoteName('device') . '=' . $db->quote($device));

        $db->setQuery($query);
        $unpublished = $db->loadResult();

        $query = $db->getQuery(true);
        if ($unpublished) {
            $query->delete($db->quoteName($table));
            $query->where($db->quoteName('id') . '=' . (int)$id);
            $query->where($db->quoteName('device') . '=' . $db->quote($device));

            $db->setQuery($query);
            $db->execute();

            return true;
        } else {
            $query->insert($db->quoteName($table));
            $query->columns($db->quoteName(array('id', 'device')));
            $query->values(implode(',', array((int)$id, $db->quote($device))));

            $db->setQuery($query);
            $db->execute();

            return false;
        }
    }

    public function getConfig($name, $default = null)
    {
        /** @var Joomla\Registry\Registry $config */
        $config = JFactory::getConfig();
        return $config->get($name, $default);
    }

    public function setConfig($name, $value)
    {
        /** @var Joomla\Registry\Registry $config */
        $config = JFactory::getConfig();
        return $config->set($name, $value);
    }

    public function getDbo()
    {
        if (!class_exists('MjQueryBuilder')) {
            require_once dirname(dirname(__FILE__)) . '/classes/mjquerybuilder.php';
        }
        return JFactory::getDbo();
    }
}