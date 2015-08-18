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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die();
define('_JEXEC', 1);
define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'administrator');
require_once(JPATH_BASE . '/includes/defines.php');
require_once(JPATH_BASE . '/includes/framework.php');

@error_reporting(0);

$template = basename(dirname(dirname(__FILE__)));

function sendMessage($msg)
{
    foreach (JError::getErrors() as $error)
        echo $error->get('message') . "\n";
    echo $msg;
    die();
}

try {
    $app = JFactory::getApplication('administrator');

    $lang = JFactory::getLanguage();
    $lang->load('com_installer');

    $user = JFactory::getUser();
    if (!$user->authorise('core.login.admin'))
        sendMessage(JText::_('JERROR_LOGIN_DENIED'));

    $themeupload = JRequest::getVar('themeupload', null, 'files', 'array');

    if (!(bool)ini_get('file_uploads'))
        sendMessage(JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLFILE'));
    if (!extension_loaded('zlib'))
        sendMessage(JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB'));
    if (!is_array($themeupload))
        sendMessage(JText::_('COM_INSTALLER_MSG_INSTALL_NO_FILE_SELECTED'));
    if ($themeupload['error'] || $themeupload['size'] < 1)
        sendMessage(JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));

    jimport('joomla.filesystem.archive');
    jimport('joomla.filesystem.file');
    jimport('joomla.filesystem.path');

    $tmp_src = $themeupload['tmp_name'];

    $config = JFactory::getConfig();
    $tmp_dest = $config->get('tmp_path') . '/' . $themeupload['name'];
    $tmp_dest = JPath::clean($tmp_dest);

    $uploaded = JFile::upload($tmp_src, $tmp_dest);
    if (!$uploaded)
        sendMessage(JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));

    $extractdir = JPath::clean(dirname($tmp_dest) . '/' . uniqid('theme_'));
    $result = JArchive::extract($tmp_dest, $extractdir);
    if ($result === false)
        sendMessage('Cannot unpack archive');

    $themedir = $extractdir . '/themes/';
    $destdir = JPATH_ROOT . '/templates/' . $template . '/themes/';

    $files = JFolder::files($themedir, '\.min\.css$');
    if (!is_array($files) || !count($files))
        sendMessage(JText::_('COM_INSTALLER_UNABLE_TO_FIND_INSTALL_PACKAGE'));

    foreach ($files as $file) {
        $theme = basename($file, '.min.css');
        JFolder::copy($themedir . 'images', $destdir . $theme . '/images', '', true);
        JFile::copy($themedir . $theme . '.min.css', $destdir . $theme . '/' . $theme . '.min.css', '', true);
        JFile::copy($themedir . $theme . '.css', $destdir . $theme . '/' . $theme . '.css', '', true);
    }
    JFile::delete($tmp_dest);
    JFolder::delete($extractdir);

    echo '*';
} catch (Exception $e) {
    sendMessage($e->getMessage());
}