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

jimport('joomla.plugin.plugin');
jimport('joomla.environment.uri');
JPluginHelper::importPlugin('mobile');
JHtml::_('behavior.modal', 'a.modal');

echo $this->renderView('global/header');

JToolbarHelper::cancel();

/** @var JApplicationSite $app */
$app = JFactory::getApplication();

/** @var JDocumentHtml $doc */
$doc = JFactory::getDocument();
$doc->addScript(JUri::root(true) . '/administrator/components/com_mobilejoomla/assets/js/sim_cleanup.js');

//load MobileJoomla class
require_once JPATH_COMPONENT . '/classes/mobilejoomla.php';

/** @var MobileJoomla $mj */
$mj = new MobileJoomla($this->joomlaWrapper);

/* @todo: move events to wrapper */
JPluginHelper::importPlugin('mobile');
$dispatcher = JDispatcher::getInstance();
$results = $dispatcher->trigger('onMJPreviewList', array($mj));

if (count($results) === 0) {
    $imagePath = JUri::root(true) . '/administrator/components/com_mobilejoomla/assets/previews/';
    $results = json_decode(file_get_contents(dirname(__FILE__) . '/preview.json'));
    /** @var stdClass[] $results */
    foreach ($results as &$section) {
        foreach ($section->items as &$device) {
            $device->thumb = $imagePath . $device->thumb;
            if (isset($device->image)) {
                $device->image = $imagePath . $device->image;
            }
        }
        unset($device);
    }
    unset($section);
    $results = array($results);
}

$previews = array();
foreach ($results as $result) {
    if (is_array($result)) {
        foreach ($result as $section) {
            if (!isset($section->section)) {
                continue;
            }
            $title = $section->section;
            if (isset($previews[$title])) {
                $previews[$title] = array_merge($previews[$title], $section->items);
            } else {
                $previews[$title] = $section->items;
            }
        }
    }
}

$desktop_domain = $mj->settings->get('desktop_domain');
$uri = new JUri(JUri::root());
$css = '';
$firstCol = true;
$columns = array(true => '', false => '');

$userAgent = $_SERVER['HTTP_USER_AGENT'];

foreach ($previews as $section => $items) {

    $columns[$firstCol] .=
        '<fieldset class="form-horizontal clearfix">' .
        '<legend>' . JText::_($section) . '</legend>' .
        '<ul class="thumbnails">';

    foreach ($items as $preview) {
        $htmlid = preg_replace('/[\W\s]/', '', $preview->title);

        if (isset($preview->userAgent)) {
            $mj->device->markup = false;
            $_SERVER['HTTP_USER_AGENT'] = $preview->userAgent;
            $app->triggerEvent('onDeviceDetection', array($mj));
            $markup = $mj->device->markup;

            $domain = $mj->settings->get($markup . '.domain');
            if ($domain !== null && $domain !== '') {
                $uri->setHost($domain);
            } else {
                $uri->setHost($desktop_domain);
            }
            $uri->setVar('device', $markup);
            $href = htmlspecialchars($uri->toString());

            $columns[$firstCol] .=
                '<li class="span3">'
                . '<a class="modal thumbnail" href="' . $href . '" rel="{'
                . 'handler:\'iframe\','
                . 'size:{x:' . $preview->frame[0] . ',y:' . $preview->frame[1] . '},'
                . 'classWindow:\'' . $htmlid . '\','
                . 'onClose:mobilesim_cleanup(\'' . $htmlid . '\')'
                . '}">'
                . '<img src="' . $preview->thumb . '" width="100" height="100">'
                . '<div class="caption">'
                . $preview->title . ' <span class="badge">' . $markup . '</span>'
                . '</div></a>' .
                '</li>';

            $padding_left = $preview->framePos[0];
            $padding_right = $preview->size[0] - $padding_left - $preview->frame[0];
            $padding_top = $preview->framePos[1];
            $padding_bottom = $preview->size[1] - $padding_top - $preview->frame[1];

            $css .= ".$htmlid #sbox-content"
                . '{'
                . 'position:relative;'
                . "top:{$preview->contentTop}px;"
                . "padding:{$padding_top}px {$padding_right}px {$padding_bottom}px {$padding_left}px;"
                . "margin:-{$padding_top}px -{$padding_right}px -{$padding_bottom}px -{$padding_left}px;"
                . "background:url('{$preview->image}');"
                . '}'
                . ".$htmlid #sbox-btn-close"
                . '{'
                . "top:{$preview->btnClose[0]}px;"
                . "right:{$preview->btnClose[1]}px;"
                . '}';
        } else {
            $columns[$firstCol] .=
                '<li class="span3">'
                . '<div class="thumbnail mjpro">'
                . '<img src="' . $preview->thumb . '" width="100" height="100">'
                . '<div class="caption">'
                . $preview->title
                . '</div>'
                . '<div class="mjpro">'
                . '<p>' . JText::_('COM_MJ__DEVICE_SIMULATOR_MJPRO') . '</p>'
                . '<a href="http://www.mobilejoomla.com/upgrade-mjpro?utm_source=mjbackend&amp;utm_medium=Preview-tab&amp;utm_campaign=Admin-upgrade" target="_blank" class="btn btn-success">' . JText::_('COM_MJ__UPGRADE') . '</a>'
                . '</div>'
                . '</div>'
                . '</li>';
        }
    }
    $columns[$firstCol] .= '</ul></fieldset>';

    $firstCol = !$firstCol;
}

$doc->addStyleDeclaration($css);

$_SERVER['HTTP_USER_AGENT'] = $userAgent;

?>
<div class="row-fluid">
    <div class="span6"><?php echo $columns[true]; ?></div>
    <div class="span6"><?php echo $columns[false]; ?></div>
</div>
<form method="post" action="index.php" id="adminForm" name="adminForm">
    <input type="hidden" name="option" value="com_mobilejoomla">
    <input type="hidden" name="task" value="cancel">
</form>
