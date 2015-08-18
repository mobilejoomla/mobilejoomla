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

require_once JPATH_COMPONENT . '/classes/mjcontroller.php';

class MjPreviewController extends MjController
{
    public function display()
    {
        $this->loadFramework();

        $viewName = $this->joomlaWrapper->getRequestWord('view', '');

        echo $this->renderView('global/page', array(
            'sidebar' => $this->renderView('global/sidebar', array(
                'controllerName' => $this->name,
                'viewName' => $viewName
            )),
            'content' => $this->renderView('preview', array(
                'viewName' => $viewName
            ))
        ));
    }
}