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

/** @var $params Joomla\Registry\Registry */

// check that MJ plugin was loaded
if (!class_exists('MobileJoomla')) {
    return;
}

/** @var JApplicationSite $app */
$app = JFactory::getApplication();
/** @var MobileJoomla $mj */
$mj_list = $app->triggerEvent('onGetMobileJoomla');
$mj = array_pop($mj_list);

if (!defined('_MJ')) {
    if ($params->get('hide_on_pc', 1)) {
        /** @var MjDevice $mjDevice */
        $mjDevice = $mj->device;
        if (empty($mjDevice->real_markup)) {
            return;
        }
    }
    $markup = '';
} else {
    $markup = $mj->getMarkup();
}

$show_chosen_markup = (bool)$params->get('show_choosen', 0);

$links = array();

if ($params->get('auto_show', 0)) {
    $chosen = $mj->isCurrentMarkup('auto');
    if ($show_chosen_markup || !$chosen) {
        $text = $params->get('auto_text', 'Automatic Version');
        $link = $chosen ? false : $mj->getDeviceViewURI('auto');
        $links[] = array('url' => $link, 'text' => $text);
    }
}

if ($params->get('mobile_show', 1)) {
    $chosen = $mj->isCurrentMarkup('mobile');
    if ($show_chosen_markup || !$chosen) {
        $text = $params->get('mobile_text', 'Mobile Version');
        $link = $chosen ? false : $mj->getDeviceViewURI('mobile');
        $links[] = array('url' => $link, 'text' => $text);
    }
}

if ($params->get('web_show', 1)) {
    $chosen = $mj->isCurrentMarkup('desktop');
    if ($show_chosen_markup || !$chosen) {
        $text = $params->get('web_text', 'Desktop Version');
        $link = $chosen ? false : $mj->getDeviceViewURI('desktop');
        $links[] = array('url' => $link, 'text' => $text);
    }
}

$layout_file = JModuleHelper::getLayoutPath('mod_mj_switcher', $markup ? $markup : 'default');
if (!is_file($layout_file)) {
    $layout_file = JModuleHelper::getLayoutPath('mod_mj_switcher', 'mobile');
}

require $layout_file;
