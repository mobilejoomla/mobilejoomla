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

class JFormFieldIcons extends JFormField
{
    protected $type = 'Icons';

    protected function getLabel()
    {
        return '';
    }

    protected function getInput()
    {
        $html = array();

        $base = JUri::root(true) . '/templates/' . $this->form->getValue('template') . '/';

        $doc = JFactory::getDocument();
        $doc->addStyleDeclaration("
.mj-image {
	position: relative;
	display: inline-block;
	vertical-align: top;
	font-size: 11px;
	line-height: 14px;
}
.mj-image > .mj-wrap {
	position: relative;
	overflow: hidden;
	background-color: #eee;
}
.mj-image .mj-delete {
	position: absolute;
	overflow: hidden;
	top: 0;
	cursor: pointer;
	padding: 0;
	margin: 0;
	line-height: 14px;
	font-size: 24px;
	font-weight: bold;
	font-family: Arial;
	color: #f00;
	text-shadow: 0 0 1px #fff;
}
.mj-image .mj-delete:hover {
	text-shadow: 0 0 2px #f00;
	box-shadow: inset 0 0 2px #fff;
}
.mj-image > .mj-delete {
	left: 18px;
}
.mj-image > .mj-wrap > .mj-delete {
	right: 0;
	display: none;
}
.mj-image > .mj-wrap > input:hover + .mj-delete,
.mj-image > .mj-wrap > .mj-delete:hover {
	display: block;
}
.mj-image > .mj-wrap > input {
	cursor: pointer;
	position: absolute;
	top: 0;
	right: 0;
	padding: 0;
	margin: 0;
	opacity: 0;
}
/* Browse button is on the left in WebKit */
@media screen and (-webkit-min-device-pixel-ratio:0) {
	.mj-image > .mj-wrap > input {
		left: 0;
		right: auto;
	}
}
.mj-image input[type='file']::-webkit-file-upload-button {
	cursor: pointer;
}
.mj-image > .mj-wrap > img {
	margin: 0;
}
		");
        $doc->addScriptDeclaration("
jqm(document).ready(function(){
	jqm('img.mj-icon').replaceWith(function(){
		var w = this.width, h = this.height,
			accept = jqm(this).attr('data-accept') || 'image/png',
			title = jqm(this).attr('alt') || '',
			html = '',
			src = this.getAttribute('data-src');
		html += '<form action=\"{$base}fields/iconupload.php\" method=\"post\" enctype=\"multipart/form-data\" class=\"mj-image\" style=\"width:'+(w+15)+'px\" target=\"iconUploader-iframe\">';
		html += '<input type=\"hidden\" name=\"name\" value=\"'+src.substring(src.lastIndexOf('/')+1)+'\">';
		html += '<input type=\"hidden\" name=\"delete\" value=\"0\">';
		html += '<div class=\"mj-wrap\" style=\"width:'+w+'px;height:'+h+'px\">';
		html += '<img src=\"'+this.src+'\" width=\"'+w+'\" height=\"'+h+'\" style=\"width:'+w+'px;height:'+h+'px\">';
		html += '<input type=\"file\" name=\"file\" style=\"font-size:'+h+'px\" accept=\"'+accept+'\" title=\"" . JText::_('TPL_MOBILE_JQM__ICONUPLOADTITLE') . "\">';
		if(w<32 && h<32) {
			html += '</div>';
			html += '<div class=\"mj-delete\" title=\"Delete\">&times;</div>';
		} else {
			html += '<div class=\"mj-delete\" title=\"Delete\">&times;</div>';
			html += '</div>';
		}
		html += '<span>'+title+'</span>';
		html += '</form>';
		return html;
	});
});
function iconUploaderLoad(el) {
	jqm.mobile.loading('hide');
	var response = el.contentWindow.document.body.innerHTML;
	if(response == '')
		return;
	jqm('.mj-image > .mj-wrap > input').each(function(){
		jqm(this).replaceWith('<input type=\"file\" name=\"file\" style=\"font-size:'+jqm(this).css('font-size')+'\" accept=\"'+(this.accept||'')+'\">');
	});
	if(response == '*') {
		jqm('.mj-image > .mj-wrap > img').each(function(){
			this.src = this.getAttribute('data-src');
		});
	} else {
		alert(response);
	}
}
jqm(document).on('click', '.mj-image .mj-delete', function(e){
	var form = jqm(e.target).closest('form');
	form.get(0).delete.value=1;
	form.submit();
});
jqm(document).on('change', '.mj-image input', function(e){
	if(e.target.value)
		jqm(e.target).closest('form').submit();
});
jqm(document).on('submit', '.mj-image', function(e){
	jqm.mobile.loading('show');
});
		");

        $html[] = '<{jqmstart}/>';
        $html[] = '<iframe width="0" height="0" style="display:none;" name="iconUploader-iframe" id="iconUploader-iframe" onload="iconUploaderLoad(this)"/></iframe>';
        $html[] = '<div class="mj-icons">';
        $html[] = '<h2>Favicon</h2>';
		$html[] = '<div>'
			. $this->image('favicon.ico', 16, 16, 'favicon.ico, 16x16', array('data-accept' => 'image/vnd.microsoft.icon'))
			. '</div>';
		$html[] = '<h2>iPhone icons, pre iOS 7</h2>';
		$html[] = '<div>'
			. $this->image('touch-icon-57x57.png', 57, 57, 'Standard, 57x57')
			. $this->image('touch-icon-114x114.png', 57, 57, 'Retina, 114x114')
			. $this->image('touch-icon-precomposed-57x57.png', 57, 57, 'Precomposed, 57x57')
			. $this->image('touch-icon-precomposed-114x114.png', 57, 57, 'Precomposed Retina, 114x114')
			. '</div>';
		$html[] = '<h2>iPhone icons, iOS 7</h2>';
		$html[] = '<div>'
			. $this->image('touch-icon-120x120.png', 60, 60, 'Retina, 120x120')
			. '</div>';
		$html[] = '<h2>iPad icons, pre iOS 7</h2>';
		$html[] = '<div>'
			. $this->image('touch-icon-72x72.png', 72, 72, 'Standard, 72x72')
			. $this->image('touch-icon-144x144.png', 72, 72, 'Retina, 144x144')
			. $this->image('touch-icon-precomposed-72x72.png', 72, 72, 'Precomposed, 72x72')
			. $this->image('touch-icon-precomposed-144x144.png', 72, 72, 'Precomposed Retina, 144x144')
			. '</div>';
		$html[] = '<h2>iPad icons, iOS 7</h2>';
		$html[] = '<div>'
			. $this->image('touch-icon-76x76.png', 76, 76, 'Standard, 76x76')
			. $this->image('touch-icon-152x152.png', 76, 76, 'Retina, 152x152')
			. '</div>';
		$html[] = '<h2>Startup images for iPhone</h2>';
		$html[] = '<div>'
			. $this->image('touch-startup-image-320x460.png', 80, 115, 'Standard, 320x460')
			. $this->image('touch-startup-image-640x920.png', 80, 115, 'Retina, 640x920')
			. $this->image('touch-startup-image-640x1096.png', 80, 127, 'iPhone 5, 640x1096')
			. '</div>';
		$html[] = '<h2>Startup images for iPad</h2>';
		$html[] = '<div>'
			. $this->image('touch-startup-image-768x1004.png', 77, 100, 'Portrait, 768x1004')
			. $this->image('touch-startup-image-1536x2008.png', 77, 100, 'Portrait Retina, 1536x2008')
			. $this->image('touch-startup-image-1024x748.png', 102, 75, 'Landscape, 1024x748')
			. $this->image('touch-startup-image-2048x1496.png', 102, 75, 'Landscape Retina, 2048x1496')
			. '</div>';
		$html[] = '</div>';
        $html[] = '<{jqmend}/>';

        return implode($html);
    }

	private function image($filename, $width, $height, $title, $attribs = array())
	{
		$base = JUri::root(true) . '/templates/' . $this->form->getValue('template') . '/';
		$base_path = JPATH_ROOT . '/templates/' . $this->form->getValue('template') . '/';

		$html = '<img class="mj-icon" data-src="' . $base . $filename . '"';

		if (file_exists($base_path . $filename)) {
			$filename = $base . $filename;
		} else {
			$filename = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
		}
		$html .= ' src="' . $filename . '"';
		$html .= ' width="' . $width . '" height="' . $height . '"';
		foreach ($attribs as $key => $value) {
			$html .= ' ' . $key . '="' . $value . '"';
		}
		$html .= ' alt="' . $title . '"';
		$html .= '>';

		return $html;
	}
}
