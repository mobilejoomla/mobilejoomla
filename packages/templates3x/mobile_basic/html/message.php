<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version		###VERSION###
 * @license		###LICENSE###
 * @copyright	###COPYRIGHT###
 * @date		###DATE###
 */
defined('_JEXEC') or die;

function renderMessage($msgList)
{
	if(!is_array($msgList))
		return null;

	$count = 0;
	foreach($msgList as $type=>$msgs)
		$count += count($msgs);
	if($count == 0)
		return null;

	/** @var $document JDocumentHTML */
	$document = JFactory::getDocument();

	$theme_title   = $document->params->get('theme_messagetitle');
	$theme_content = $document->params->get('theme_messagetext');
	if($theme_title)
		$theme_title = ' data-divider-theme="'.$theme_title.'"';
	if($theme_content)
		$theme_content = ' data-theme="'.$theme_content.'"';

	$buffer = '<div id="system-message-container">';
	$buffer .= '<ul data-role="listview" data-inset="true"'.$theme_content.$theme_title.' id="system-message">';
	foreach($msgList as $type=>$msgs)
	{
		if(count($msgs))
		{
			$buffer .= '<li data-role="list-divider" class="' . strtolower($type) . '">' . JText::_($type) . '</li>';
			foreach($msgs as $msg)
				$buffer .= '<li>' . $msg . '</li>';
		}
	}
	$buffer .= '</ul>';
	$buffer .= '</div>';

	return $buffer;
}
