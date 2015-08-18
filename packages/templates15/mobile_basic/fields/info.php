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

class JElementInfo extends JElement
{
    var $_name = 'Info';

    function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
    {
        return '';
    }

    function fetchElement($name, $value, &$xmlElement, $control_name)
    {
        $title = $xmlElement->attributes('title');
        $docurl = $xmlElement->attributes('docurl');
        $extid = $xmlElement->attributes('extid');

        $html = array();

        $html[] = '<{jqmstart}/>';
        $html[] = '<p><h2>' . $title . ' ###VERSION###</h2></p>';
		$html[] = '<p><b>Expiration</b>: <span class="mjactive"><span id="mjsubscription"></span> days left</span><span class="mjexpired">Expired</span> <a target="_blank" class="mjrenewurl ui-btn ui-mini" href="http://www.mobilejoomla.com/orders.html">Renew</a></p>';
        $html[] = '<p>Template for <a target="_blank" href="http://www.mobilejoomla.com/">Mobile Joomla!</a> extension</p>';
        $html[] = '<p><a target="_blank" href="' . $docurl . '">Documentation</a></p>';
        $html[] = '<p><a target="_blank" href="http://www.mobilejoomla.com/forum/18-premium-support.html">Premium support forum</a></p>';
        $html[] = '<{jqmend}/>';

        $css = ".mjactive,.mjexpired,.mjrenewurl{display:none}";
        $js = "
window.addEvent('domready',function(){
	function updateSubscription(expires){
		if(expires==''){//expired
			$$('.mjexpired').setStyle('display', 'inline');
			$$('.mjrenewurl').setStyle('display', 'inline');
		}else{//active
			var mjsubscription=$('mjsubscription');
			if(mjsubscription!=null){
				if(MooTools.version>='1.2')
					mjsubscription.set('html', expires);
				else
					mjsubscription.setHTML(expires);
			}
			$$('.mjactive').setStyle('display', 'inline');
			if(parseInt(expires)<=30)
				$$('.mjrenewurl').setStyle('display', 'inline');
		}
	}

	function checkSubscription(){
		if(typeof Request == 'function'){
			new Request.HTML( {
				url: 'http://www.mobilejoomla.com/getsubs.php?app=$extid&domain=' + document.domain + '&v=' + escape('###VERSION###'),
				method: 'get',
				onSuccess : function(tree, elements, response){
					updateSubscription(response);
				}
			}).send();
		} else if(typeof Ajax == 'function'){
			new Ajax( 'http://www.mobilejoomla.com/getsubs.php?app=$extid&domain=' + document.domain + '&v=' + escape('###VERSION###'), {
				method: 'get',
				onComplete: function(response){
					updateSubscription(response);
				}
			}).request();
		}
	}

	try{
		checkSubscription();
	}catch(e){}
});
";
        $doc = JFactory::getDocument();
        $doc->addStyleDeclaration($css);
        $doc->addScriptDeclaration($js);

        return implode($html);
    }
}
