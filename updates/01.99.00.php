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

$configfile = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/config.php';
if (is_file($configfile)) {
    include($configfile);
    /** @var $MobileJoomla_Settings array */
    $map = array(
        'caching' => 'caching',
        'httpcaching' => 'httpcaching',
        'jpegquality' => 'jpegquality',
        'mobile_sitename' => 'mobile_sitename',
        'global.removetags' => '.removetags',
        'global.img' => '.img',
        'global.img_addstyles' => '.img_addstyles',
        'global.homepage' => '.homepage',
        'global.componenthome' => '.componenthome',
        'global.gzip' => '.gzip',
        'xhtml.buffer_width' => '.buffer_width',
        'xhtml.homepage' => 'mobile.homepage',
        'xhtml.gzip' => 'mobile.gzip',
        'xhtml.domain' => 'mobile.domain',
        'xhtml.componenthome' => 'mobile.componenthome',
        'xhtml.jfooter' => 'mobile.jfooter',
        'xhtml.removetags' => 'mobile.removetags',
        'xhtml.img' => 'mobile.img',
        'xhtml.img_addstyles' => 'mobile.img_addstyles'
    );
    if (!in_array($MobileJoomla_Settings['xhtml.template'],
        array('mobile_iphone', 'mobile_smartphone', 'mobile_imode', 'mobile_wap'))
    ) {
        $map['xhtml.template'] = 'mobile.template';
    }
    foreach ($map as $old => $new) {
        if (isset($MobileJoomla_Settings[$old])) {
            $mjSettings->set($new, $MobileJoomla_Settings[$old]);
        }
    }
    if (isset($MobileJoomla_Settings['global.img'])) {
        $mjSettings->set('.img', ($MobileJoomla_Settings['global.img'] > 1) ? 1 : 0);
    }
    if (isset($MobileJoomla_Settings['xhtml.img']) && $MobileJoomla_Settings['xhtml.img'] !== '') {
        $mjSettings->set('mobile.img', ($MobileJoomla_Settings['xhtml.img'] > 1) ? 1 : 0);
    }

    MjInstaller::UninstallPlugin('system', 'mobilebot');

    // @todo relocate Select Markup modules
    MjInstaller::UninstallModule('mod_mj_markupchooser');

    // @todo replace mod_mj_menu modules by mod_menu one
    //MjInstaller::UninstallModule('mod_mj_menu');

    try {
        $db = JFactory::getDbo();

        $query = "ALTER TABLE `#__mj_modules` CHANGE `markup` `device` varchar(32) NOT NULL";
        $db->setQuery($query);
        $db->query();

        $query = "ALTER TABLE `#__mj_plugins` CHANGE `markup` `device` varchar(32) NOT NULL";
        $db->setQuery($query);
        $db->query();
    } catch (Exception $e) {
    }

    // @todo release old templates with hardcoded template position names (or custome in template's settings)
    // @todo release detecting plugin with support of old modes
}
