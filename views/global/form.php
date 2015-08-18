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

$form = $params['form'];

$controllerName = $params['controllerName'];
$viewName = $params['viewName'];
$mjSettings = $params['settings'];

$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onMJDisplayConfig', array($controllerName . '/' . $viewName, &$form, &$mjSettings));

$hidden = isset($params['options']) ? $params['options'] : array();

$colClass = array();
switch (count($form)) {
    case 1:
        $colClass = array('span12');
        break;
    case 2:
        $colClass = array('span6', 'span6');
        break;
}

$ratio = array(
    0 => array(4, 8),
    1 => array(5, 7)
)

?>
<form method="post" action="index.php" id="adminForm" name="adminForm">
    <input type="hidden" name="option" value="com_mobilejoomla">
    <input type="hidden" name="task" value="save">
    <input type="hidden" name="controller" value="<?php echo $controllerName; ?>">
    <input type="hidden" name="view" value="<?php echo $viewName; ?>">
    <?php foreach ($hidden as $key => $value) : ?>
        <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
    <?php endforeach; ?>
    <div class="row-fluid">
        <?php foreach ($form as $i => $column) : ?>
            <div class="<?php echo $colClass[$i]; ?>" id="mj-column-<?php echo (int)$i; ?>">
                <?php foreach ($column as $legend => $fields) : if (count($fields)) : ?>
                    <fieldset class="form-horizontal clerfix">
                        <legend><?php echo JText::_($legend); ?></legend>
                        <?php foreach ($fields as $field) : ?>
                            <div class="control-group clearfix<?php if (isset($field['class'])) {
                                    echo ' ' . $field['class'];
                                } ?>">
                                <?php if (!isset($field['input'])) : /*label only*/ ?>
                                    <?php echo $field['label']; ?>
                                <?php elseif (!isset($field['label'])) : /*input only*/ ?>
                                    <div class="controls">
                                        <?php echo $field['input']; ?>
                                    </div>
                                <?php
                                else : /*both*/
                                    ?>
                                    <?php echo $field['label']; ?>
                                    <div class="controls">
                                        <?php echo $field['input']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </fieldset>
                <?php endif; endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</form>