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

include_once dirname(dirname(__FILE__)) . '/classes/mjmodel.php';

class MjSettingsModel extends MjModel
{
    /** @var array */
    private $data;

    private $defaults = array();

    /**
     * @param $joomlaWrapper MjJoomlaWrapper
     */
    public function __construct($joomlaWrapper)
    {
        parent::__construct($joomlaWrapper);

        $this->data = $this->joomlaWrapper->dbSelectAll('#__mj_settings', 'name', 'value');
        $this->data['enabled'] = $this->joomlaWrapper->isMjPluginEnabled();
        if (isset($this->data['nomjitems'])) {
            $this->data['nomjitems'] = explode(',', $this->data['nomjitems']);
        }

        $this->defaults = json_decode(file_get_contents(dirname(dirname(__FILE__)) . '/defconfig.json'));
        if (!is_object($this->defaults)) {
            $this->defaults = new stdClass;
        }
    }

    /**
     * Get list of all key-value pairs
     * @return array
     */
    public function getAll()
    {
        return $this->data;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        if ($default === null && isset($this->defaults->$name)) {
            return $this->defaults->$name;
        }
        return $default;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param $data array
     * @return boolean
     */
    public function bind($data)
    {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
        return true;
    }

    /**
     * @param $keys string|array
     * @param $value mixed
     * @return boolean
     */
    public function def($keys, $value = null)
    {
        if (is_array($keys) || is_object($keys)) {
            foreach ($keys as $key => $val) {
                if (!isset($this->data[$key])) {
                    $this->data[$key] = $val;
                }
            }
        } elseif (!isset($this->data[$keys])) {
            $this->data[$keys] = $value;
        }
        return true;
    }

    /**
     * @return boolean
     */
    public function save()
    {
        $data = $this->data;
        unset($data['enabled']);
        if (isset($data['nomjitems'])) {
            $data['nomjitems'] = implode(',', $data['nomjitems']);
        }

        if (!$this->joomlaWrapper->dbSaveAll($data, '#__mj_settings', 'name', 'value')) {
            return false;
        }

        if (!$this->joomlaWrapper->enableMjPlugin($this->data['enabled'])) {
            return false;
        }

        // @todo check unlink/copy results, chmod for .htaccess
        $srcDir = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/assets/ress';
        if (!is_dir($srcDir)) {
            //
            return false;
        }

        $destDir = JPATH_ROOT . '/media/mj';
        switch ($data['distribmode']) {
            case '':
                @unlink($destDir . '/.htaccess');
                break;
            case 'php':
                @unlink($destDir . '/.htaccess');
                copy($srcDir . '/get.php.sample', $destDir . '/get.php');
                break;
            case 'apache':
                copy($srcDir . '/sample_apache.htaccess', $destDir . '/.htaccess');
                break;
            case 'apachephp':
                copy($srcDir . '/sample_php.htaccess', $destDir . '/.htaccess');
                copy($srcDir . '/get.php.sample', $destDir . '/get.php');
                break;
        }

        return true;
    }
}