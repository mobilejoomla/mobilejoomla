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

class MjDefaultController extends MjController
{
    public function save($msg = '')
    {
        include_once JPATH_COMPONENT . '/models/settings.php';
        $mjSettings = new MjSettingsModel($this->joomlaWrapper);

        $bindData = array();
        foreach ($_POST as $key => $value) {
            if (substr($key, 0, 3) === 'mj_') {
                $param = substr($key, 3);
                $param = str_replace('-', '.', $param);
                $bindData[$param] = $value;
//                $mjSettings->set($param, $bindData[$param]);
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