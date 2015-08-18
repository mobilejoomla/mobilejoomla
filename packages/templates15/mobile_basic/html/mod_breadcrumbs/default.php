<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div class="breadcrumbs">
<div data-role="controlgroup" data-type="horizontal" class="pathway">
<?php
for ($i = 0; $i < $count; $i ++) {
    if ($i < $count - 1) {
        if (!empty($list[$i]->link)) {
            echo '<a data-role="button" data-icon="arrow-r" data-iconpos="right" href="' . $list[$i]->link . '">' . $list[$i]->name . '</a>';
        } else {
            echo '<span data-role="button" data-icon="arrow-r" data-iconpos="right" class="ui-btn ui-disabled">' . $list[$i]->name . '</span>';
        }
    } elseif ($params->get('showLast', 1)) {
        echo '<span data-role="button" class="last ui-btn ui-disabled">' . $list[$i]->name . '</span>';
    }
}
?>
</div>
</div>
