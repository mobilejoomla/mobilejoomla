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

class JElementJqmEnhance extends JElement
{
    var $_name = 'jqmEnhance';

    function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
    {
        return '<{jqmstart}/><div class="ui-field-contain">'
        . '<label id="' . $control_name . $name . '-lbl" for="' . $control_name . $name . '"'
        . ($description ? ' class="hasTip ui-select" title="' . JText::_($label) . '::' . JText::_($description) . '"' : ' class="ui-select"')
        . '>' . JText::_($label) . '</label>'
        . '<{jqmend}/>';
    }

    function fetchElement($name, $value, &$xmlElement, $control_name)
    {
        // get component list
        jimport('joomla.filesystem.folder');
        $components = JFolder::folders(JPATH_ROOT . '/components', '^com_');
        sort($components);
        $enabled = explode(',', $value);

        $enhance_id = $control_name . 'enhance';
        $select_id = $control_name . $name . '_select';

        $html = array();
        $html[] = '<{jqmstart}/><div style="display:inline-block">';
        $html[] = '<select id="' . $select_id . '" size="7" multiple data-enhance="false">';
        foreach ($components as $component) {
            $html[] = '<option value="' . $component . '"'
                . (in_array($component, $enabled, true) ? ' selected' : '')
                . '>' . $component . '</option>';
        }
        $html[] = '</select>';
        $html[] = '<input type="hidden" name="' . $control_name . '[' . $name . ']' . '" id="' . $control_name . $name . '"'
            . ' value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"'
            . '/>';
        $html[] = '</div></div>';
        $html[] = "<script>
function onEnhanceChange(){
	var disabled = (jqm('#$enhance_id').prop('selectedIndex')==0);
 	jqm('#$select_id').prop('disabled', disabled ? 'disable' : false);
}
jqm(document).on('pagecreate', function(){
	jqm('#$enhance_id').on('change', onEnhanceChange);
	var oldOnSubmit = document.forms.adminForm.onsubmit;
	document.forms.adminForm.onsubmit = function(e){
		jqm('#$control_name$name').val((jqm('#$select_id').val() || []).join(','));
		if(oldOnSubmit) oldOnSubmit(e);
	};
	onEnhanceChange();
});
</script>\n";
        $html[] = '<{jqmend}/>';

        return implode($html);
    }
}
