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

function renderToolbar($toolbar)
{
    $output = '';
    foreach ($toolbar as $button)
        $output .= renderToolbarButton($button);
    return $output;
}

function renderToolbarButton($button)
{
    static $icons_map = array('left' => 'arrow-l', 'up' => 'arrow-u', 'home' => 'home');

    $output = '<a href="' . $button->url . '" data-role="button"';
    if ($button->icon) {
        $icon = isset($icons_map[$button->icon]) ? $icons_map[$button->icon] : $button->icon;
        $output .= ' data-icon="' . $icon . '"';

        if ($button->title === '') {
            $output .= ' data-iconpos="notext"';
        }
    }
    if (isset($button->options['attrib'])) {
        $output .= ' ' . $button->options['attrib'];
    }
    if (isset($button->options['class'])) {
        $output .= ' class="' . $button->options['class'] . '"';
    }
    $output .= '>' . $button->title . '</a>';
    return $output;
}
