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

include_once dirname(__FILE__) . '/classes/mjinstaller.php';

function com_install()
{
    return MjInstaller::install();
}

function com_uninstall()
{
    return MjInstaller::uninstall();
}
