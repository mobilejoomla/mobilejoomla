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

echo $this->renderView('global/header');

JToolbarHelper::apply();
JToolbarHelper::save();
JToolbarHelper::cancel();

$viewName = $params['viewName'];
$device = $viewName;

// populate $settings array
include_once JPATH_COMPONENT . '/models/settings.php';
$mjSettings = new MjSettingsModel($this->joomlaWrapper);

$form = array(
    //left
    array(
        'COM_MJ__SETTINGS' => array(
            array(
                'label' => MjHtml::label($device . '.template', 'COM_MJ__TEMPLATE_NAME', 'COM_MJ__TEMPLATE_NAME_DESC'),
                'input' => MjHtml::template($device . '.template', $mjSettings->get($device . '.template', ''))
            )
        ),
        'COM_MJ__HOMEPAGE' => array(
            array(
                'label' => MjHtml::label($device . '.homepage', 'COM_MJ__FORCED_HOMEPAGE', 'COM_MJ__FORCED_HOMEPAGE_DESC', 'COM_MJ__OVERWRITES_GLOBAL'),
                'input' => MjHtml::textinput($device . '.homepage', $mjSettings->get($device . '.homepage', '')),
                'class' => 'withnext'
            ),
            array(
                'input' => MjHtml::menulist(false, $mjSettings->get($device . '.homepage', ''), false, $device . '.homepage')
            )
        ),
        'COM_MJ__ADVANCED' => array(
            array(
                'label' => MjHtml::label($device . '.gzip', 'COM_MJ__GZIP_COMPRESSION', 'COM_MJ__GZIP_COMPRESSION_DESC', 'COM_MJ__OVERWRITES_GLOBAL'),
                'input' => MjHtml::gonoff($device . '.gzip', $mjSettings->get($device . '.gzip', ''))
            ),
            array(
                'label' => MjHtml::label($device . '.removetags', 'COM_MJ__REMOVE_UNSUPPORTED_TAGS', 'COM_MJ__REMOVE_UNSUPPORTED_TAGS_DESC', 'COM_MJ__OVERWRITES_GLOBAL'),
                'input' => MjHtml::gonoff($device . '.removetags', $mjSettings->get($device . '.removetags', ''))
            )
        )
    ),
    //right
    array(
        'COM_MJ__HTML' => array(
            array(
                'label' => MjHtml::label($device . '.html_removecomments', 'COM_MJ__HTML_REMOVE_COMMENTS', 'COM_MJ__HTML_REMOVE_COMMENTS_DESC', 'COM_MJ__OVERWRITES_GLOBAL'),
                'input' => MjHtml::gonoff($device . '.html_removecomments', $mjSettings->get($device . '.html_removecomments', ''))
            ),
            array(
                'label' => MjHtml::label($device . '.html_mergespace', 'COM_MJ__HTML_MERGE_SPACES', 'COM_MJ__HTML_MERGE_SPACES_DESC', 'COM_MJ__OVERWRITES_GLOBAL'),
                'input' => MjHtml::gonoff($device . '.html_mergespace', $mjSettings->get($device . '.html_mergespace', ''))
            ),
            array(
                'label' => MjHtml::label($device . '.html_minifyurl', 'COM_MJ__HTML_MINIFY_URL', 'COM_MJ__HTML_MINIFY_URL_DESC', 'COM_MJ__OVERWRITES_GLOBAL'),
                'input' => MjHtml::gonoff($device . '.html_minifyurl', $mjSettings->get($device . '.html_minifyurl', ''))
            )
        ),
        'COM_MJ__IMAGE' => array(
            array(
                'label' => MjHtml::label($device . '.img', 'COM_MJ__RESCALE_IMAGES', 'COM_MJ__RESCALE_IMAGES_DESC', 'COM_MJ__OVERWRITES_GLOBAL'),
                'input' => MjHtml::gonoff($device . '.img', $mjSettings->get($device . '.img', ''))
            ),
            array(
                'label' => MjHtml::label($device . '.buffer_width', 'COM_MJ__DECREASE_IMAGE_WIDTH', 'COM_MJ__DECREASE_IMAGE_WIDTH_DESC'),
                'input' => '<div class="input-append">' .
                    MjHtml::numberinput($device . '.buffer_width', $mjSettings->get($device . '.buffer_width', ''), array('size' => '5', 'class' => 'text-right'), $mjSettings->get('.buffer_width')) .
                    '<span class="add-on">px</span>' .
                    '</div>'
            ),
            array(
                'label' => MjHtml::label($device . '.img_addstyles', 'COM_MJ__STYLE_IMAGE_SIZE', 'COM_MJ__STYLE_IMAGE_SIZE_DESC', 'COM_MJ__OVERWRITES_GLOBAL'),
                'input' => MjHtml::gonoff($device . '.img_addstyles', $mjSettings->get($device . '.img_addstyles', ''))
            )
        ),
        'COM_MJ__CSS' => array(
            array(
                'label' => MjHtml::label($device . '.css_optimize', 'COM_MJ__CSS_OPTIMIZE', 'COM_MJ__CSS_OPTIMIZE_DESC', 'COM_MJ__OVERWRITES_GLOBAL'),
                'input' => MjHtml::gonoff($device . '.css_optimize', $mjSettings->get($device . '.css_optimize', ''))
            ),
            array(
                'label' => MjHtml::label($device . '.css_inlinelimit', 'COM_MJ__CSS_INLINE_LIMIT', 'COM_MJ__CSS_INLINE_LIMIT_DESC', 'COM_MJ__OVERWRITES_GLOBAL'),
                'input' => '<div class="input-append">' .
                    MjHtml::numberinput($device . '.css_inlinelimit', $mjSettings->get($device . '.css_inlinelimit', ''), array('class' => 'text-right'), $mjSettings->get('.css_inlinelimit')) .
                    '<span class="add-on">bytes</span>' .
                    '</div>'
            )
        ),
        'COM_MJ__JS' => array(
            array(
                'label' => MjHtml::label($device . '.js_optimize', 'COM_MJ__JS_OPTIMIZE', 'COM_MJ__JS_OPTIMIZE_DESC', 'COM_MJ__OVERWRITES_GLOBAL'),
                'input' => MjHtml::gonoff($device . '.js_optimize', $mjSettings->get($device . '.js_optimize', ''))
            ),
            array(
                'label' => MjHtml::label($device . '.js_inlinelimit', 'COM_MJ__JS_INLINE_LIMIT', 'COM_MJ__JS_INLINE_LIMIT_DESC', 'COM_MJ__OVERWRITES_GLOBAL'),
                'input' => '<div class="input-append">' .
                    MjHtml::numberinput($device . '.js_inlinelimit', $mjSettings->get($device . '.js_inlinelimit', ''), array('class' => 'text-right'), $mjSettings->get('.js_inlinelimit')) .
                    '<span class="add-on">bytes</span>' .
                    '</div>'
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
