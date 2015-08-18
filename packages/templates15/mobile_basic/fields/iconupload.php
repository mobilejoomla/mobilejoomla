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
define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))) . '/administrator');
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
    $lang->load('lib_joomla');

    $user = JFactory::getUser();
    if (!$user->authorize('login', 'administrator')) {
        sendMessage(JText::_('E_NOLOGIN_ACCESS'));
    }

    $upload = JRequest::getVar('file', null, 'files', 'array');
    $name = basename(JRequest::getString('name'));
    if (!preg_match('/\.(png|ico)$/', $name)) {
        sendMessage(JText::_('Warning: No data written'));
    }

    $icons = array(
        'favicon.ico' => array(),
        'touch-icon-152x152.png' => array(152, 152), // iPad 3+, iOS 7
        'touch-icon-76x76.png' => array(76, 76), // iPad 1-2, iOS 7
        'touch-icon-120x120.png' => array(120, 120), // iPhone 4+, iOS 7
        'touch-icon-precomposed-144x144.png' => array(144, 144), // iPad 3+
        'touch-icon-precomposed-72x72.png' => array(72, 72), // iPad 1-2
        'touch-icon-144x144.png' => array(144, 144), // iPad 3+
        'touch-icon-72x72.png' => array(72, 72), // iPad 1-2
        'touch-icon-precomposed-114x114.png' => array(114, 114), // iPhone 4+
        'touch-icon-precomposed-57x57.png' => array(57, 57), // iPhone 1-3
        'touch-icon-114x114.png' => array(114, 114), // iPhone 4+
        'touch-icon-57x57.png' => array(), // iPhone 1-3
        'touch-startup-image-320x460.png' => array(320, 460), // iPhone 1-3
        'touch-startup-image-640x920.png' => array(640, 920), // iPhone 4
        'touch-startup-image-640x1096.png' => array(640, 1096), // iPhone 5
        'touch-startup-image-768x1004.png' => array(768, 1004), // iPad 1-2
        'touch-startup-image-1024x748.png' => array(1024, 748), // iPad 1-2
        'touch-startup-image-1536x2008.png' => array(1536, 2008), // iPad 3+
        'touch-startup-image-2048x1496.png' => array(2048, 1496), // iPad 3+
    );
    if (!isset($icons[$name])) {
        sendMessage('');
    }

    $delete = JRequest::getInt('delete');

    jimport('joomla.filesystem.file');
    jimport('joomla.filesystem.path');

    $dest = JPATH_ROOT . '/templates/' . $template . '/' . $name;
    $dest = JPath::clean($dest);

    if ($delete) {
        if (!JFile::delete($dest)) {
            sendMessage('');
//			sendMessage(JText::sprintf('JLIB_FILESYSTEM_DELETE_FAILED', $name));
        }
    } else {
        if (!(bool)ini_get('file_uploads')) {
            sendMessage(JText::_('WARNINSTALLFILE'));
        }
        if (!extension_loaded('zlib')) {
            sendMessage(JText::_('WARNINSTALLZLIB'));
        }
        if (!is_array($upload)) {
            sendMessage(JText::_('No file selected'));
        }
        if ($upload['error'] || $upload['size'] < 1) {
            sendMessage(JText::_('WARNINSTALLUPLOADERROR'));
        }

        $tmp_src = $upload['tmp_name'];

        if (count($icons[$name])) {
            $size = getimagesize($tmp_src);
            if ($size[0] !== $icons[$name][0] || $size[1] !== $icons[$name][1]) {
                sendMessage('Wrong image size');
            }
        }
        $uploaded = JFile::upload($tmp_src, $dest);
        if (!$uploaded) {
            sendMessage(JText::_('WARNINSTALLUPLOADERROR'));
        }
    }

    echo '*';
} catch (Exception $e) {
    sendMessage($e->getMessage());
}