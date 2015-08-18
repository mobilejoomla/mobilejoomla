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

class JFormFieldJqmEnhance extends JFormField
{
	protected $type = 'jqmEnhance';
	protected function getLabel()
	{
		$this->labelClass = 'ui-select';
		return '<{jqmstart}/><div class="ui-field-contain">'.parent::getLabel().'<{jqmend}/>';
	}
	protected function getInput()
	{
		// get component list
		jimport('joomla.filesystem.folder');
		$components = JFolder::folders(JPATH_ROOT.'/components', '^com_');
		sort($components);
		$enabled = explode(',', $this->value);

		$enhance_id = 'jform_params_enhance';
		$select_id  = $this->id . '_select';

		$html = array();
		$html[] = '<{jqmstart}/><div style="display:inline-block">';
		$html[] = '<select id="' . $select_id . '" size="7" multiple data-enhance="false">';
		foreach($components as $component)
		{
			$html[] = '<option value="' . $component . '"'
					. (in_array($component, $enabled, true) ? ' selected' : '')
					. '>' . $component . '</option>';
		}
		$html[] = '</select>';
		$html[] = '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '"'
			. ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"'
			.'/>';
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
		jqm('#{$this->id}').val((jqm('#$select_id').val() || []).join(','));
		if(oldOnSubmit) oldOnSubmit(e);
	};
	onEnhanceChange();
});
</script>\n";
		$html[] = '<{jqmend}/>';

		return implode($html);
	}
}
