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

class JFormFieldJqmText extends JFormField
{
	protected $type = 'jqmText';
	protected function getLabel()
	{
		return '<{jqmstart}/><div class="ui-field-contain">'.parent::getLabel().'<{jqmend}/>';
	}
	protected function getInput()
	{
		// Initialize some field attributes.
		$size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';

		$html = array();
		$html[] = '<{jqmstart}/>';
		$html[] = '<input type="text" name="' . $this->name . '" id="' . $this->id . '"'
			. ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"'
			. ' placeholder="' . htmlspecialchars((string)$this->element['default'], ENT_COMPAT, 'UTF-8') . '"'
			.' data-mini="true"' . $size . $maxLength . '/>';
		$html[] = '</div><{jqmend}/>';

		return implode($html);
	}
}
