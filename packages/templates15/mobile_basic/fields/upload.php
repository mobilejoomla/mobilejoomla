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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die();
}
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))) . DS . 'administrator');
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
    $mainframe = JFactory::getApplication('administrator');

    $lang = JFactory::getLanguage();
    $lang->load('com_installer');

    $user = JFactory::getUser();
    if (!$user->authorize('login', 'administrator')) {
        sendMessage(JText::_('E_NOLOGIN_ACCESS'));
    }

    $themeupload = JRequest::getVar('themeupload', null, 'files', 'array');

    if (!(bool)ini_get('file_uploads')) {
        sendMessage(JText::_('WARNINSTALLFILE'));
    }
    if (!extension_loaded('zlib')) {
        sendMessage(JText::_('WARNINSTALLZLIB'));
    }
    if (!is_array($themeupload)) {
        sendMessage(JText::_('No file selected'));
    }
    if ($themeupload['error'] || $themeupload['size'] < 1) {
        sendMessage(JText::_('WARNINSTALLUPLOADERROR'));
    }

    jimport('joomla.filesystem.archive');
    jimport('joomla.filesystem.file');
    jimport('joomla.filesystem.path');

    $tmp_src = $themeupload['tmp_name'];

    $config = JFactory::getConfig();
    $tmp_dest = $config->get('tmp_path') . '/' . $themeupload['name'];
    $tmp_dest = JPath::clean($tmp_dest);

    $uploaded = JFile::upload($tmp_src, $tmp_dest);
    if (!$uploaded) {
        sendMessage(JText::_('WARNINSTALLUPLOADERROR'));
    }

    $extractdir = JPath::clean(dirname($tmp_dest) . '/' . uniqid('theme_'));
    $result = JArchive::extract($tmp_dest, $extractdir);
    if ($result === false) {
        sendMessage('Cannot unpack archive');
    }

    $themedir = $extractdir . '/themes/';
    $destdir = JPATH_ROOT . '/templates/' . $template . '/themes/';

    $files = JFolder::files($themedir, '\.min\.css$');
    if (!is_array($files) || !count($files)) {
        sendMessage(JText::_('Unable to find install package'));
    }

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