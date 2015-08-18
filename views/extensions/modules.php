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

// load modules list
$joomlaWrapper = MjJoomlaWrapper::getInstance();
$db = $joomlaWrapper->getDbo();

$query = new MjQueryBuilder($db);
$modules = $query
    ->select('id', 'title', 'module', 'position')
    ->from('#__modules')
    ->where($query->qn('client_id') . '=0')
    ->where($query->qn('published') . '=1')
    ->where($query->qn('position') . '<>' . $query->q(''))
    ->order('position', 'ordering', 'title')
    ->setQuery()
    ->loadObjectList('id');

// load mj settings
$query = new MjQueryBuilder($db);
$mj_modules = $query
    ->select('* ')
    ->from('#__mj_modules')
    ->setQuery()
    ->loadObjectList();
if (empty($mj_modules)) {
    $mj_modules = array();
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
if (count($modules)) {
    foreach ($modules as $module) {
        $table[$module->id] = $row;
    }
}

foreach ($mj_modules as $module) {
    $table[$module->id][$module->device] = 0;
}

$doc = JFactory::getDocument();
$doc->addStyleSheet('components/com_mobilejoomla/assets/css/extmanager.css');
$doc->addScript('components/com_mobilejoomla/assets/js/extmanager.js');
$doc->addScriptDeclaration("mj_extmanager_action = 'set_module_state';");
?>
<p><?php echo JText::_('COM_MJ__MODULES_PAGE_INFO'); ?></p>
<table class="table table-striped table-hover table-condensed">
    <thead>
    <tr>
        <th><?php echo JText::_('COM_MJ__MODULES_MODULE'); ?></th>
        <th><?php echo JText::_('COM_MJ__MODULES_TYPE'); ?></th>
        <th><?php echo JText::_('COM_MJ__MODULES_POSITION'); ?></th>
        <?php foreach ($modes as $title) : ?>
            <th class="vert">
                <p><?php echo $title; ?></p>
            </th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <?php foreach ($modules as $module): ?>
        <tr>
            <td>
                <?php echo $module->title; ?>
            </td>
            <td>
                <?php echo $module->module; ?>
            </td>
            <td><?php echo $module->position; ?></td>
            <?php foreach ($modes as $device => $title) : ?>
                <td><a class="link" onclick="change(<?php echo $module->id; ?>, '<?php echo $device; ?>', this);">
                        <?php echo $this->getImage($table[$module->id][$device]); ?>
                    </a></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
</table>
<form method="post" action="index.php" id="adminForm" name="adminForm">
    <input type="hidden" name="option" value="com_mobilejoomla">
    <input type="hidden" name="task" value="cancel">
</form>