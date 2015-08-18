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

require_once JPATH_COMPONENT . '/classes/mjcontroller.php';

jimport('joomla.installer.helper');
jimport('joomla.installer.installer');

class MjUpdateController extends MjController
{
    private function initStatus()
    {
        JError::setErrorHandling(E_ERROR, 'Message');
        set_time_limit(1200);
        ini_set('max_execution_time', 1200);
    }

    private function sendStatus()
    {
        $msg = array();
        /** @var JException $error */
        foreach (JError::getErrors() as $error) {
            if ($error->get('level')) {
                $msg[] = $error->get('message');
            }
        }
        if (count($msg)) {
            $msg = '<p>' . implode('</p><p>', $msg) . '</p>';
        } else {
            $msg = 'ok';
        }
        echo $msg;
        jexit();
    }

    public function download()
    {
        $app = JFactory::getApplication();
        $this->initStatus();
        $url = 'http://www.mobilejoomla.com/latest2.php';
        $app->triggerEvent('onMJBeforeDownload', array(&$url));
        $filename = JInstallerHelper::downloadPackage($url);
        if ($filename) {
            $app->setUserState('com_mobilejoomla.updatefilename', $filename);
        }
        $this->sendStatus();
    }

    public function unpack()
    {
        $app = JFactory::getApplication();
        $this->initStatus();
        $filename = $app->getUserState('com_mobilejoomla.updatefilename', false);
        $config = JFactory::getConfig();
        if (substr(JVERSION, 0, 3) === '1.5') {
            $path = $config->getValue('config.tmp_path');
        } else {
            $path = $config->get('tmp_path');
        }
        $path .= '/' . $filename;
        if ($path) {
            $result = JInstallerHelper::unpack($path);
            $app->setUserState('com_mobilejoomla.updatefilename', false);
            if ($result !== false) {
                $app->setUserState('com_mobilejoomla.updatedir', $result['dir']);
                JFile::delete($path);
            }
        } else {
            JError::raiseWarning(1, JText::_('COM_MJ__UPDATE_UNKNOWN_PATH'));
        }
        $this->sendStatus();
    }

    public function install()
    {
        $app = JFactory::getApplication();
        $this->initStatus();
        $dir = $app->getUserState('com_mobilejoomla.updatedir', false);
        if ($dir) {
            $installer = new JInstaller();
            $installer->install($dir);
            $app->setUserState('com_mobilejoomla.updatedir', false);
            JFolder::delete($dir);
        } else {
            JError::raiseWarning(1, JText::_('COM_MJ__UPDATE_UNKNOWN_PATH'));
        }
        $this->sendStatus();
    }
}