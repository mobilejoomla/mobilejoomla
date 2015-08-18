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

/** @var MjExtensionsController $this */
/** @var array $params */
/** @var string $controllerName */
/** @var string $viewName */

echo $this->renderView('global/header');

JToolbarHelper::cancel();

// load plugins list
$joomlaWrapper = MjJoomlaWrapper::getInstance();
$db = $joomlaWrapper->getDbo();

$query = new MjQueryBuilder($db);
if (substr(JVERSION, 0, 3) === '1.5') {
    $plugins = $query
        ->select('id')
        ->select($query->qn('name') . ' AS ' . $query->qn('title'))
        ->select('folder', 'element')
        ->from('#__plugins')
        ->where($query->qn('published') . '=1')
        ->where($query->qn('folder') . ' NOT IN (' . $query->q('system') . ', ' . $query->q('mobile') . ')')
        ->order('folder', 'ordering', 'element')
        ->setQuery()
        ->loadObjectList('id');
} else {
    $plugins = $query
        ->select($query->qn('extension_id') . ' AS ' . $query->qn('id'))
        ->select($query->qn('name') . ' AS ' . $query->qn('title'))
        ->select('folder', 'element')
        ->from('#__extensions')
        ->where($query->qn('enabled') . '=1')
        ->where($query->qn('type') . '=' . $query->q('plugin'))
        ->where($query->qn('folder') . ' NOT IN (' . $query->q('system') . ', ' . $query->q('mobile') . ')')
        ->order('folder', 'ordering', 'element')
        ->setQuery()
        ->loadObjectList('id');
    // translate
    $lang = JFactory::getLanguage();
    foreach ($plugins as &$item) {
        $source = JPATH_PLUGINS . '/' . $item->folder . '/' . $item->element;
        $file = 'plg_' . $item->folder . '_' . $item->element . '.sys';
        $lang->load($file, JPATH_ADMINISTRATOR, null, false, false)
        || $lang->load($file, $source, null, false, false)
        || $lang->load($file, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
        || $lang->load($file, $source, $lang->getDefault(), false, false);
        $item->title = JText::_($item->title);
    }
    unset($item);
}

// load mj settings
$query = new MjQueryBuilder($db);
$mj_plugins = $query
    ->select('* ')
    ->from('#__mj_plugins')
    ->setQuery()
    ->loadObjectList();
if (empty($mj_plugins)) {
    $mj_plugins = array();
}

include_once JPATH_COMPONENT . '/classes/mjhelper.php';

$modes = MjHelper::getDeviceList();

$row = array('desktop' => 1);
foreach ($modes as $device => $title) {
    if (!empty($device) && $device !== 'desktop') {
        $row[$device] = 1;
    }
}

$table = array();
if (count($plugins)) {
    foreach ($plugins as $plugin) {
        $table[$plugin->id] = $row;
    }
}

foreach ($mj_plugins as $plugin) {
    $table[$plugin->id][$plugin->device] = 0;
}

$doc = JFactory::getDocument();
$doc->addStyleSheet('components/com_mobilejoomla/assets/css/extmanager.css');
$doc->addScript('components/com_mobilejoomla/assets/js/extmanager.js');
$doc->addScriptDeclaration("mj_extmanager_action = 'set_plugin_state';");
?>
<p><?php echo JText::_('COM_MJ__PLUGINS_PAGE_INFO'); ?></p>
<table class="table table-striped table-hover table-condensed">
    <thead>
    <tr>
        <th><?php echo JText::_('COM_MJ__PLUGINS_PLUGIN'); ?></th>
        <th><?php echo JText::_('COM_MJ__PLUGINS_TYPE'); ?></th>
        <th><?php echo JText::_('COM_MJ__PLUGINS_NAME'); ?></th>
        <?php foreach ($modes as $title) : ?>
            <th class="vert">
                <p><?php echo $title; ?></p>
            </th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <?php foreach ($plugins as $plugin): ?>
        <tr>
            <td><?php echo $plugin->title; ?></td>
            <td><?php echo $plugin->folder; ?></td>
            <td><?php echo $plugin->element; ?></td>
            <?php foreach ($row as $device => $title) : ?>
                <td><a class="link" onclick="change(<?php echo $plugin->id; ?>, '<?php echo $device; ?>', this);">
                        <?php echo $this->getImage($table[$plugin->id][$device]); ?>
                    </a></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
</table>
<form method="post" action="index.php" id="adminForm" name="adminForm">
    <input type="hidden" name="option" value="com_mobilejoomla">
    <input type="hidden" name="task" value="cancel">
</form>