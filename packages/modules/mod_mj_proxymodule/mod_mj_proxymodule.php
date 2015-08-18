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
defined('_JEXEC') or die ('Restricted access');

/** @var $params Joomla\Registry\Registry */
/** @var $module stdClass */
/** @var $attribs array */

$module_id = $params->get('module_id', 0);

$joomlaWrapper = MjJoomlaWrapper::getInstance();
$db = $joomlaWrapper->getDbo();

$query = new MjQueryBuilder($db);
$result = $query
    ->select('module', 'title')
    ->from('#__modules')
    ->where($query->qn('id') . '=' . (int)$module_id)
    ->setQuery()
    ->loadObject();
if (!is_object($result)) {
    return;
}

$module->showtitle = 0;
echo mjProxyModuleRender($result->module, $result->title, $attribs);

/**
 * @param string $module
 * @param string $title
 * @param array $attribs
 * @return string
 */
function mjProxyModuleRender($module, $title, $attribs)
{
    $module =& JModuleHelper::getModule($module, $title);
    if (!is_object($module)) {
        return '';
    }

    $doc = JFactory::getDocument();
    /** @var JDocumentRendererModule $renderer */
    $renderer = $doc->loadRenderer('module');
    return $renderer->render($module, $attribs);
}
