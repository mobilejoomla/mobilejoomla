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

$position = $params->get('position', '');
$module->showtitle = 0;

// @todo: get chrome data

$doc = JFactory::getDocument();
/** @var JDocumentRendererModules $renderer */
$renderer = $doc->loadRenderer('modules');
echo $renderer->render($position, $attribs);
