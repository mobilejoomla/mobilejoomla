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
/** @var $links array */

$parts = array();
foreach ($links as $link) {
    if ($link['url']) {
        $parts[] = '<a href="' . $link['url'] . '" rel="nofollow">' . $link['text'] . '</a>';
    } else {
        $parts[] = '<span class="active">' . $link['text'] . '</span>';
    }
}
?>
<div class="mjswitcher">
    <?php echo $params->get('show_text', ' '); ?>
    <?php echo implode('<span> | </span>', $parts); ?>
</div>