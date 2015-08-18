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

/** @var MjController $this */
/** @var array $params */
/** @var string $controllerName */
/** @var string $viewName */

$viewName = $params['viewName'];
$controllerName = $params['controllerName'];

$active = $controllerName;
if ($viewName !== 'default') {
    $active .= '/' . $viewName;
}

$menu = array(
    'settings' => array(
        'general' => 'default',
        'advanced' => 'default/advanced'
    ),
    'devices' => array(
//        'desktop' => 'devices/'
    ),
    'extensions' => array(
        'plugins' => 'extensions/plugins',
        'modules' => 'extensions/modules',
//        'positions' => 'extensions/positions',
//        'templates' => 'extensions/templates',
//        'menus' => 'extensions/menus'
    ),
    'testing' => array(
        'preview' => 'preview/preview'
    )
);

include_once JPATH_COMPONENT . '/classes/mjhelper.php';

$devices = MjHelper::getDeviceList();
foreach ($devices as $device => $title) {
    if (!empty($device) && $device !== 'desktop') {
        $menu['devices'][$title] = 'devices/' . $device;
    }
}
//$menu['devices']['mobile'] = 'devices/mobile';
if (!isset($devices['tablet'])) {
    $menu['devices']['tablet'] = 'devices/tablet';
}

function MjRecursiveMenu($menu, $active)
{
    foreach ($menu as $name => $action) {
        $title = $name === 'extensions' ? 'Settings' : JText::_(ucfirst($name));
        if (is_array($action)) {
            echo '<li class="nav-header">';
            echo $title;
            echo '</li>';
            MjRecursiveMenu($action, $active);
            echo '<li class="divider"></li>';
        } else {
            list($itemController, $itemView) = explode('/', $action . '/default');

            $url = 'index.php?option=com_mobilejoomla';
            if ($itemController !== '') {
                $url .= '&controller=' . $itemController;
            }
            if ($itemView !== 'default') {
                $url .= '&view=' . $itemView;
            }
            echo $action === $active ? '<li class="active">' : '<li>';
            echo '<a href="' . JRoute::_($url) . '">' . $title . '</a>';
            echo '</li>';
        }
    }
}

?>
<ul class="nav nav-list"><?php
MjRecursiveMenu($menu, $active);
?></ul><?php

