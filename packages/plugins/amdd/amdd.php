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

class plgMobileAmdd extends JPlugin
{
    public function plgMobileAmdd(& $subject, $config)
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

        if ($mjDevice->markup !== false) {
            return;
        }

        $cache = (bool)$this->params->get('cache', 1);
        $cachesize = (int)$this->params->get('cachesize', 1000);
        $options = array(
            'handler' => 'joomla',
            'dbTableName' => '#__mj_amdd',
            'cacheSize' => $cache ? $cachesize : 0
        );

        try {
            require_once(JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/ress/vendor/amdd/amdd.php');
            $amddObj = Amdd::getCapabilities(null, false, $options);
            if (!is_object($amddObj)) {
                return;
            }
        } catch (AmddDatabaseException $e) {
            error_log("Caught exception 'Exception' with message '" . $e->getMessage() . "' in " . $e->getFile() . ':' . $e->getLine());
            return;
        }

        $mjDevice->amdd = $amddObj;
        switch ($amddObj->markup) {
            case 'tv':
            case 'gametv':
                $mjDevice->markup = '';
                break;
            case 'gameport':
            case 'xhtml':
            case 'iphone':
            case 'chtml':
            case 'wml':
                $mjDevice->markup = 'mobile';
                break;
            case 'tablet':
                $mjDevice->markup = '';
                break;
            default:
                $mjDevice->markup = $amddObj->markup;
        }
        if (isset($amddObj->screenWidth)) {
            $mjDevice->screenwidth = $amddObj->screenWidth;
        }
        if (isset($amddObj->screenHeight)) {
            $mjDevice->screenheight = $amddObj->screenHeight;
        }
        if (isset($amddObj->imageFormats)) {
            $mjDevice->imageformats = $amddObj->imageFormats;
        }
        if (isset($amddObj->pixelRatio)) {
            $mjDevice->pixelratio = $amddObj->pixelRatio;
        }
    }

    public function onGetDatabaseSize()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        // @TODO: use getConfig()
        $db->setQuery('SHOW TABLE STATUS FROM `' . $app->getCfg('db') . '` LIKE ' . $db->quote($app->getCfg('dbprefix') . 'mj_amdd%'));
        $result = $db->loadObjectList();

        $size = 0;
        foreach ($result as $row) {
            $size += $row->Data_length;
        }

        $date = null;
        $xml = simplexml_load_file(dirname(__FILE__) . '/amdd.xml');
        if (isset($xml->creationdate)) {
            $date = (string)$xml->creationdate;
        }

        return $size ? array('Mobile - AMDD', $size, $date) : null;
    }
}
