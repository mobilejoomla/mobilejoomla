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

class JFormFieldJqmEnd extends JFormField
{
	protected $type = 'jqmEnd';
	protected function getLabel()
	{
		return '';
	}
	protected function getInput()
	{
		$app = JFactory::getApplication();
		$app->registerEvent('onAfterDispatch', 'JqmEndPostProcessing');

		$html = array();
		$html[] = '<{jqmstart}/>';
		$html[] = '</div></div></div>';
		$html[] = '<{jqmend}/>';
		$html[] = '<{jqmendmarker}/>';
		return implode($html);
	}
}

jimport('joomla.event.event');
class JqmEndPostProcessing extends JEvent
{
	public function onAfterDispatch()
	{
		/** @var JDocumentHtml $doc */
		$doc = JFactory::getDocument();
		$buffer = $doc->getBuffer('component');
		$buffer = preg_replace_callback(
			'/<\{jqmstartmarker\}\/>(.*?)<\{jqmendmarker\}\/>/s',
			array($this, 'replaceJQM'),
			$buffer
		);
		$doc->setBuffer($buffer, 'component');
	}
	public function replaceJQM($text)
	{
		preg_match_all('/<\{jqmstart\}\/>(.*?)<\{jqmend\}\/>/s', $text[1], $matches);
		return implode($matches[1]);
	}
}