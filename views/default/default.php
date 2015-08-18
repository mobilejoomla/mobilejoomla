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

include_once JPATH_COMPONENT . '/models/settings.php';
include_once JPATH_COMPONENT . '/classes/mjhelper.php';
include_once JPATH_COMPONENT . '/classes/mjinspection.php';

echo $this->renderView('global/header');

JToolbarHelper::apply();
JToolbarHelper::save();
JToolbarHelper::cancel();

$user = JFactory::getUser();
if (version_compare(JVERSION, '1.6', '>=') && $user->authorise('core.admin', 'com_mobilejoomla')) {
    JToolbarHelper::divider();
    JToolbarHelper::preferences('com_mobilejoomla');
}

// populate $settings array
$mjSettings = new MjSettingsModel($this->joomlaWrapper);

$mjVersion = '###VERSION###';
if (strpos($mjVersion, '.pro') !== false) {
    $mjVersion = 'Pro ' . str_replace('.pro', '', $mjVersion);
}

$subscription_title = 'Community';
$version_file = JPATH_COMPONENT . '/packages/version.dat';
if (is_file($version_file)) {
    $version_type = file_get_contents($version_file);
    if (in_array($version_type, array('Community', 'Basic', 'Pro'), true)) {
        $subscription_title = $version_type;
    }
}

$support_ads = ($subscription_title === 'Community');

MjHelper::jsGetNotification();
MjHelper::jsGetRecommendation($mjSettings);

$template_fields = array();
$devices = MjHelper::getDeviceList();
foreach ($devices as $device => $title) {
    if ($device !== 'desktop') {
        $template_fields[] = array(
            'label' => MjHtml::label($device . '.template', JText::sprintf('COM_MJ__TEMPLATE_NAME_FORMAT', $title), JText::sprintf('COM_MJ__TEMPLATE_NAME_FORMAT_DESC', $title)),
            'input' => MjHtml::template($device . '.template', $mjSettings->get($device . '.template', ''))
        );
    }
}

$inspection = new MjInspection();
$warnings = $inspection->getWarnings($mjSettings);
unset($inspection);

$desktop_domain = $mjSettings->get('desktop_domain');
$current_domain = @$_SERVER['HTTP_HOST'];
$changeDomain = '';
if (!empty($current_domain) && $desktop_domain !== $current_domain) {
    $doc = JFactory::getDocument();
    $doc->addScriptDeclaration('
    function mjChangeDomain(){
        jQuery("#' . MjHtml::id('desktop_domain') . '").val("' . $current_domain . '");
        jQuery("#text_desktop_domain").val("' . $current_domain . '");
        jQuery("#domainchange").hide();
        jQuery("#domainalert").show();
    }');
    $changeDomain = MjHtml::hidden('desktop_domain', $mjSettings->get('desktop_domain'));
    $changeDomain .=
        '<a href="#" onclick="mjChangeDomain();return false;" id="domainchange" class="btn btn-primary btn-small">'
        . 'Change to ' . $current_domain
        . '</a>'
        . '<span id="domainalert" class="badge">' . JText::_('COM_MJ__SAVE_DOMAIN_ALERT') . '</span>';
}

$form = array(
    //left
    array(
        'COM_MJ__SETTINGS' => array(
            array(
                'label' => MjHtml::label('enabled', 'COM_MJ__ENABLE_MJ', 'COM_MJ__ENABLE_MJ_DESC'),
                'input' => MjHtml::onoff('enabled', $mjSettings->get('enabled'))
            ),
            array(
                'label' => MjHtml::label('autoupdate', 'COM_MJ__AUTOUPDATE', 'COM_MJ__AUTOUPDATE_DESC'),
                'input' => MjHtml::onoff('autoupdate', $mjSettings->get('autoupdate'))
            )
        ),
        'COM_MJ__TEMPLATES' => $template_fields,
        'COM_MJ__MOBILE_SITE' => array(
            array(
                'label' => MjHtml::label('mobile_sitename', 'COM_MJ__MOBILE_SITENAME', 'COM_MJ__MOBILE_SITENAME_DESC'),
                'input' => MjHtml::textinput('mobile_sitename', $mjSettings->get('mobile_sitename'))
            ),
            array(
                'label' => MjHtml::label('.homepage', 'COM_MJ__FORCED_HOMEPAGE', 'COM_MJ__FORCED_HOMEPAGE_DESC'),
                'input' => MjHtml::textinput('.homepage', $mjSettings->get('.homepage')),
                'class' => 'withnext'
            ),
            array(
                'input' => MjHtml::menulist(false, $mjSettings->get('.homepage'), false, '.homepage')
            )
        ),
        'COM_MJ__IMAGE' => array(
            array(
                'label' => MjHtml::label('.img', 'COM_MJ__RESCALE_IMAGES', 'COM_MJ__RESCALE_IMAGES_DESC'),
                'input' => MjHtml::onoff('.img', $mjSettings->get('.img'))
            ),
            array(
                'label' => MjHtml::label('jpegquality', 'COM_MJ__IMAGE_QUALITY', 'COM_MJ__IMAGE_QUALITY_DESC'),
                'input' => MjHtml::slider('jpegquality', $mjSettings->get('jpegquality'))
            )
        )
    ),
    //right
    array(
        'COM_MJ__WARNINGS' => $warnings,
        'COM_MJ__INFORMATION' => array(
            array(
                'label' => MjHtml::label('desktop_domain', 'COM_MJ__DESKTOP_URL', 'COM_MJ__DESKTOP_URL_DESC'),
                'input' => MjHtml::text(
                    '<span id="text_desktop_domain">'
                    . htmlspecialchars($mjSettings->get('desktop_domain'))
                    . '</span> '
                    . $changeDomain
                )
            ),
            array(
                'label' => MjHtml::label('mjconfig.label', 'COM_MJ__SUBSCRIPTION'),
                'input' =>
                    '<div class="row-fluid"><p class="span7 help-inline">' .
                    '<b>' . $subscription_title . '</b>' .
                    '</p>' . ($subscription_title === 'Pro' ? '' : '<p class="span3 help-inline">' .
                        '<a href="http://www.mobilejoomla.com/upgrade-mjpro?utm_source=mjbackend&amp;utm_medium=General-tab-upgrade&amp;utm_campaign=Admin-upgrade" target="_blank" class="btn btn-primary btn-small">' .
                        JText::_('COM_MJ__UPGRADE') .
                        '</a>' .
                        '</p>') . '</div>'
            ),
            array(
                'label' => MjHtml::label('mjconfig.label', 'COM_MJ__CURRENT_VERSION'),
                'input' => MjHtml::text('<span id="mjversion">' . $mjVersion . '</span>')
            ),
            array(
                'label' => MjHtml::label('mjconfig.label', 'COM_MJ__LATEST_VERSION'),
                'input' =>
                    '<div class="row-fluid"><p class="span7 help-inline">' .
                    '<span id="mjconfig_latestver"><span id="mjlatestver"></span></span>' .
                    '</p><p class="span3 help-inline">' .
                    '<a class="btn btn-default btn-small modal show-if-update"' .
                    ' href="index.php?tmpl=component&option=com_mobilejoomla&controller=update"' .
                    ' rel="{handler: \'iframe\', size: {x: 480, y: 320}}">' .
                    JText::_('COM_MJ__UPDATE') .
                    '</a>' .
                    '</p></div>'
            ),
            array(
                'label' => MjHtml::label('mjconfig.label', 'COM_MJ__SUPPORT_ADS', 'COM_MJ__SUPPORT_ADS_DESC'),
                'input' =>
                    '<div class="row-fluid"><p class="span7 help-inline">' .
                    JText::_($support_ads ? 'COM_MJ__ON' : 'COM_MJ__OFF') .
                    '</p>' . ($support_ads ? '<p class="span3 help-inline">' .
                        '<a href="http://www.mobilejoomla.com/upgrade-mjpro?utm_source=mjbackend&amp;utm_medium=General-tab-upgrade&amp;utm_campaign=Admin-upgrade" target="_blank" class="btn btn-primary btn-small">' .
                        JText::_('COM_MJ__UPGRADE') .
                        '</a>' .
                        '</p>' : '') . '</div>'
            ),
            array(
                'label' => MjHtml::label('mjconfig.label', 'COM_MJ__DEVICE_DATABASE', 'COM_MJ__DEVICE_DATABASE_DESC'),
                'input' =>
                    '<div class="row-fluid"><p class="span7 help-inline">' .
                    JText::_('COM_MJ__DEVICE_DATABASE_OFFLINE') .
                    '</p><p class="span3 help-inline">' .
                    '<a class="btn btn-default btn-small modal show-if-update"' .
                    ' href="index.php?tmpl=component&option=com_mobilejoomla&controller=update"' .
                    ' rel="{handler: \'iframe\', size: {x: 480, y: 320}}">' .
                    JText::_('COM_MJ__UPDATE') .
                    '</a>' .
                    '</p></div>'
            )
        ),
        'COM_MJ__SUPPORT' => array(
            array(
                'label' => '<ul class="nav nav-tabs nav-stacked clearfix">'
                    . '<li><a target="_blank" href="http://www.mobilejoomla.com/documentation.html?ref=info"><i class="icon-chevron-right"></i>'
                    . JText::_('COM_MJ__DOCUMENTATION')
                    . '</a></li>'
                    . '<li><a target="_blank" href="http://www.mobilejoomla.com/forums.html?ref=info"><i class="icon-chevron-right"></i>'
                    . JText::_('COM_MJ__FORUMS')
                    . '</a></li>'
                    . '<li><a target="_blank" href="http://www.mobilejoomla.com/forum/18-premium-support.html?ref=info"><i class="icon-chevron-right"></i>'
                    . JText::_('COM_MJ__PREMIUM_SUPPORT_FORUMS')
                    . '</a></li>'
                    . '<li><a target="_blank" href="http://www.mobilejoomla.com/blog.html?ref=info"><i class="icon-chevron-right"></i>'
                    . JText::_('COM_MJ__LATEST_NEWS')
                    . '</a></li>'
                    . '<li><a target="_blank" href="http://www.mobilejoomla.com/account.html?ref=info"><i class="icon-chevron-right"></i>'
                    . JText::_('COM_MJ__MJ_ACCOUNT')
                    . '</a></li>'
                    . '</ul>'
            )
        )
    )
);

echo $this->renderView('global/form', array(
    'form' => $form,
    'controllerName' => $controllerName,
    'viewName' => $viewName,
    'settings' => $mjSettings
));
