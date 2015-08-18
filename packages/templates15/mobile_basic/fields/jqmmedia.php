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
defined('_JEXEC') or die;

class JElementJqmMedia extends JElement
{
    var $_name = 'jqmMedia';
    protected static $initialised = false;

    function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
    {
        return '<{jqmstart}/><div class="ui-field-contain">' . parent::fetchTooltip($label, $description, $xmlElement, $control_name, $name) . '<{jqmend}/>';
    }

    function fetchElement($name, $value, &$xmlElement, $control_name)
    {
        if (!self::$initialised) {
            JHtml::_('behavior.modal');
            $script = array();
            $script[] = 'function mjInsertFieldValue(value, id) {';
            $script[] = '	var old_value = document.getElementById(id).value;';
            $script[] = '	if (old_value != value) {';
            $script[] = '		var elem = document.getElementById(id);';
            $script[] = '		elem.value = value;';
            $script[] = '		elem.fireEvent("change");';
            $script[] = '		mjMediaRefreshPreview(id);';
            $script[] = '	}';
            $script[] = '}';
            $script[] = 'function mjMediaRefreshPreview(id) {';
            $script[] = '	var value = document.getElementById(id).value,';
            $script[] = '		img = document.getElementById(id + "_preview");';
            $script[] = '	if (img) {';
            $script[] = '		if (value && value !== "-") {';
            $script[] = '			if(value.indexOf("//")<0) value = "' . JUri::root() . '" + value;';
            $script[] = '			img.src = value;';
            $script[] = '			document.getElementById(id + "_preview_empty").setStyle("display", "none");';
            $script[] = '			document.getElementById(id + "_preview_img").setStyle("display", "");';
            $script[] = '		} else { ';
            $script[] = '			img.src = ""';
            $script[] = '			document.getElementById(id + "_preview_empty").setStyle("display", "");';
            $script[] = '			document.getElementById(id + "_preview_img").setStyle("display", "none");';
            $script[] = '		} ';
            $script[] = '	} ';
            $script[] = '}';
            $script[] = 'function mjMediaRefreshPreviewTip(tip)';
            $script[] = '{';
            $script[] = '	tip.setStyle("visibility", "visible");';
            $script[] = '	var id = tip.getElement("img.media-preview").getProperty("id");';
            $script[] = '	id = id.substring(0, id.length - "_preview".length);';
            $script[] = '	mjMediaRefreshPreview(id);';
            $script[] = '}';
            $script[] = 'function jInsertEditorText(tag, editor)';
            $script[] = '{';
            $script[] = '	var src = tag.match(/src="(.*?)"/im)[1];';
            $script[] = '	document.getElementById("' . $control_name . $name . '").value = "/" + src;';
            $script[] = '}';
            JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
            self::$initialised = true;
        }

        $html = array();

        $html[] = '<div class="jqmgroup">';
        $html[] = '<input type="text" data-mini="true" name="' . $control_name . '[' . $name . ']' . '" id="' . $control_name . $name . '"'
            . ' value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"'
            . ' placeholder="' . htmlspecialchars($xmlElement->attributes('default'), ENT_COMPAT, 'UTF-8') . '"'
            . ($xmlElement->attributes('size') ? ' size="' . (int)$xmlElement->attributes('size') . '"' : '')
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
            . '<a class="ui-btn ui-corner-all ui-mini modal"'
            . ' title="' . JText::_('Select') . '"'
            . ' href="'
            . 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=com_templates'
            . '&amp;fieldid=' . $control_name . $name . '&amp;folder=' . $folder . '"'
            . ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
        $html[] = JText::_('Select');
        $html[] = '</a></div>';

        // PREVIEW
        if ($value && file_exists(JPATH_ROOT . '/' . $value)) {
            $src = JUri::root() . $value;
        } else {
            $src = '';
        }

        $attr = array(
            'id' => $control_name . $name . '_preview',
            'class' => 'media-preview',
            'style' => 'max-width:160px; max-height:100px;'
        );
        $img = JHtml::image($src, JText::_('Selected image'), $attr);
        $previewImg = '<div id="' . $control_name . $name . '_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
        $previewImgEmpty = '<div id="' . $control_name . $name . '_preview_empty"' . ($src ? ' style="display:none"' : '') . '>'
            . JText::_('No image selected.') . '</div>';
        $tooltip = $previewImgEmpty . $previewImg;

        JHtml::_('behavior.tooltip', '.hasTipPreview', array('onShow' => 'mjMediaRefreshPreviewTip'));
        $tooltip = addslashes(htmlspecialchars($tooltip, ENT_QUOTES, 'UTF-8'));
        $html[] = '<div class="ui-block-b media-preview">';
        $html[] = '<span class="hasTipPreview" title="' . JText::_('Selected image') . '::' . $tooltip . '" >';
        $html[] = '<a class="ui-btn ui-mini">Preview</a>';
        $html[] = '</span>';

        $html[] = '</div>';

        // CLEAR
        $html[] = '<div class="ui-block-c"><a class="ui-btn ui-corner-all ui-mini ui-icon-delete" title="' . JText::_('Clear') . '"' . ' href="#" onclick="';
        $html[] = 'mjInsertFieldValue(\'-\', \'' . $control_name . $name . '\');';
        $html[] = 'return false;';
        $html[] = '">' . JText::_('Clear') . '</a></div>';

        $html[] = '</div>';
        $html[] = '</div>';

        return '<{jqmstart}/>' . implode("\n", $html) . '</div><{jqmend}/>';
    }
}
