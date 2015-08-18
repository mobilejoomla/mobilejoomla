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

class MjDevicesController extends MjController
{
    public function display()
    {
        $this->loadFramework();

        $viewName = $this->joomlaWrapper->getRequestWord('view', '');

        $actualViewName = 'default';

        switch ($viewName) {
            case '':
                $actualViewName = 'desktop';
                break;
            case 'tablet':
                require_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/classes/mobilejoomla.php';
                /** @var MobileJoomla $mj */
                $mj = new MobileJoomla($this->joomlaWrapper);
                if ($mj->checkMarkup('tablet') === false) /*check tablet mode doesn't exist*/ {
                    $actualViewName = 'mjpro';
                }
                break;
        }

        echo $this->renderView('global/page', array(
            'sidebar' => $this->renderView('global/sidebar', array(
                'controllerName' => $this->name,
                'viewName' => $viewName
            )),
            'content' => $this->renderView($actualViewName, array(
                'viewName' => $viewName
            ))
        ));
    }

    public function save($msg = '')
    {
        include_once JPATH_COMPONENT . '/models/settings.php';
        $mjSettings = new MjSettingsModel($this->joomlaWrapper);

        $bindData = array();
        foreach ($_POST as $key => $value) {
            if (substr($key, 0, 3) === 'mj_') {
                $param = substr($key, 3);
                $param = str_replace('-', '.', $param);
                $bindData[$param] = (string)$value;
            }
        }

        if (!$mjSettings->bind($bindData)) {
            $msg = 'Error in data.';
        } elseif (!$mjSettings->save()) {
            $msg = 'Cannot save data.';
        } else {
            $msg = 'Data have been saved successfully.';
        }

        parent::save($msg);
    }
}