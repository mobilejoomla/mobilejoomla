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

class JFormFieldJqmMedia extends JFormField
{
	protected $type = 'jqmMedia';
	protected static $initialised = false;
	protected function getLabel()
	{
		return '<{jqmstart}/><div class="ui-field-contain">'.parent::getLabel().'<{jqmend}/>';
	}
	protected function getInput()
	{
		if(!self::$initialised)
		{
			JHtml::_('behavior.modal');
			$script = array();
			$script[] = 'function mjInsertFieldValue(value, id) {';
			$script[] = '	var old_value = document.id(id).value;';
			$script[] = '	if (old_value != value) {';
			$script[] = '		var elem = document.id(id);';
			$script[] = '		elem.value = value;';
			$script[] = '		elem.fireEvent("change");';
			$script[] = '		mjMediaRefreshPreview(id);';
			$script[] = '	}';
			$script[] = '}';
			$script[] = 'function mjMediaRefreshPreview(id) {';
			$script[] = '	var value = document.id(id).value,';
			$script[] = '		img = document.id(id + "_preview");';
			$script[] = '	if (img) {';
			$script[] = '		if (value && value !== "-") {';
			$script[] = '			if(value.indexOf("//")<0) value = "' . JUri::root() . '" + value;';
			$script[] = '			img.src = value;';
			$script[] = '			document.id(id + "_preview_empty").setStyle("display", "none");';
			$script[] = '			document.id(id + "_preview_img").setStyle("display", "");';
			$script[] = '		} else { ';
			$script[] = '			img.src = ""';
			$script[] = '			document.id(id + "_preview_empty").setStyle("display", "");';
			$script[] = '			document.id(id + "_preview_img").setStyle("display", "none");';
			$script[] = '		} ';
			$script[] = '	} ';
			$script[] = '}';
			$script[] = 'function mjMediaRefreshPreviewTip(tip)';
			$script[] = '{';
			$script[] = '	tip.setStyle("display", "block");';
			$script[] = '	var id = tip.getElement("img.media-preview").getProperty("id");';
			$script[] = '	id = id.substring(0, id.length - "_preview".length);';
			$script[] = '	mjMediaRefreshPreview(id);';
			$script[] = '}';
			$script[] = 'function jInsertFieldValue(src, editor)';
			$script[] = '{';
			$script[] = '	document.getElementById("'.$this->id.'").value = "/" + src;';
			$script[] = '}';
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
			self::$initialised = true;
		}

		$html = array();

		$html[] = '<div class="jqmgroup">';
		$html[] = '<input type="text" data-mini="true" name="' . $this->name . '" id="' . $this->id . '"'
				. ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"'
				. ' placeholder="' . htmlspecialchars((string)$this->element['default'], ENT_COMPAT, 'UTF-8') . '"'
				. ($this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '')
				. ' />';

		$html[] = '<div class="ui-grid-b">';

		// SELECT
		$folder = '';
/*		if($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
		{
			$folder = explode('/', $this->value);
			array_shift($folder);
			array_pop($folder);
			$folder = implode('/', $folder);
		}*/

		$html[] = '<div class="ui-block-a">'
                    .'<a class="ui-btn ui-corner-all ui-mini modal"'
						.' title="' . JText::_('JLIB_FORM_BUTTON_SELECT') . '"'
						. ' href="'
							. 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=com_templates'
							. '&amp;fieldid=' . $this->id . '&amp;folder=' . $folder . '"'
						. ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
		$html[] = JText::_('JLIB_FORM_BUTTON_SELECT');
		$html[] = '</a></div>';

		// PREVIEW
		if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
			$src = JUri::root() . $this->value;
		else
			$src = '';

		$attr = array(
			'id' => $this->id . '_preview',
			'class' => 'media-preview',
			'style' => 'max-width:160px; max-height:100px;'
		);
		$img = JHtml::image($src, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $attr);
		$previewImg = '<div id="' . $this->id . '_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
		$previewImgEmpty = '<div id="' . $this->id . '_preview_empty"' . ($src ? ' style="display:none"' : '') . '>'
			. JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '</div>';
		$tooltip = $previewImgEmpty . $previewImg;
		$options = array(
			'title' => JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'),
			'text' => '<a class="ui-btn ui-mini">' . JText::_('JLIB_FORM_MEDIA_PREVIEW_TIP_TITLE') . '</a>',
			'class' => 'hasTipPreview'
		);

		JHtml::_('behavior.tooltip', '.hasTipPreview', array('onShow' => 'mjMediaRefreshPreviewTip'));
		$html[] = '<div class="ui-block-b media-preview">';
		$html[] = JHtml::tooltip($tooltip, $options);
		$html[] = '</div>';

		// CLEAR
		$html[] = '<div class="ui-block-c"><a class="ui-btn ui-corner-all ui-mini ui-icon-delete" title="' . JText::_('JLIB_FORM_BUTTON_CLEAR') . '"' . ' href="#" onclick="';
		$html[] = 'mjInsertFieldValue(\'-\', \'' . $this->id . '\');';
		$html[] = 'return false;';
		$html[] = '">' . JText::_('JLIB_FORM_BUTTON_CLEAR') . '</a></div>';

		$html[] = '</div>';
		$html[] = '</div>';

		return '<{jqmstart}/>'.implode("\n", $html).'</div><{jqmend}/>';
	}
}
