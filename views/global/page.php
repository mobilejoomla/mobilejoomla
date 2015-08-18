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

$bootstrapTemplate = version_compare(JVERSION, '3.0', '>=');

?>
<div id="mj">
    <?php if (!$bootstrapTemplate) : ?><div class="container-fluid clearfix"><?php endif; ?>
        <div class="row-fluid">
            <div class="span2 sidebar-nav">
                <?php echo $params['sidebar']; ?>
            </div>
            <div class="span10">
                <div id="mjmsgarea"></div>
                <div id="mjupdatearea"></div>
                <div id="mjnotification"></div>
                <?php echo $params['content']; ?>
            </div>
        </div>
    <?php if (!$bootstrapTemplate) : ?></div><?php endif; ?>
</div>