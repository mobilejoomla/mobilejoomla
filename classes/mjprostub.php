<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version        ###VERSION###
 * @license        ###LICENSE###
 * @copyright    ###COPYRIGHT###
 * @date        ###DATE###
 */
defined('_JEXEC') or die('Restricted access');

class plgMobileMJProStub extends JPlugin
{
    private $banner = 'This feature is available in <a href="http://www.mobilejoomla.com/upgrade-mjpro?utm_source=mjbackend&amp;utm_medium=Advanced-tab-upgrade&amp;utm_campaign=Admin-upgrade" target="_blank">Mobile Joomla! Pro</a>';

    public function onMJRenderView($viewName, &$params)
    {
        $this->backup_onMJRenderView($viewName, $params);
        $this->features_onMJRenderView($viewName, $params);
    }

    private function backup_onMJRenderView($viewName, &$params)
    {
        if ($viewName !== 'global/form' || $params['viewName'] !== 'advanced') {
            return;
        }

        $this->injectBanner();

        $config_blobs =& $params['form'];

        $blob = array(
            array(
                'label' => MjHtml::prolabel('COM_MJ__BACKUP_SETTINGS'),
                'input' => '<div class="mjpro"><p id="backup-settings"><a href="#" onclick="return false;">' . JText::_('COM_MJ__BACKUP') . '</a></p></div>'
            ),
            array(
                'label' => MjHtml::prolabel('COM_MJ__RESTORE'),
                'input' => '<div class="mjpro"><p id="restore_file"><input type="file" disabled /><input type="button" disabled class="backup-restore" value="' . JText::_('COM_MJ__RESTORE_BTN') . '" /></p></div>'
            )
        );
        array_unshift_assoc($config_blobs[0], $blob, 'COM_MJ__BACKUP_RESTORE');
    }

    private function features_onMJRenderView($viewName, &$params)
    {
        if ($viewName !== 'global/form' || $params['controllerName'] !== 'default' || $params['viewName'] !== 'default') {
            return;
        }

        $this->injectBanner();

        $config_blobs =& $params['form'];

        $config_blobs[0]['COM_MJ__IMAGE'][] = array(
            'label' => MjHtml::prolabel('COM_MJ__HIRES_IMAGES'),
            'input' => '<div class="mjpro">' . MjHtml::proonoff() . '</div>'
        );
        $config_blobs[0]['COM_MJ__IMAGE'][] = array(
            'label' => MjHtml::prolabel('COM_MJ__HIRES_IMAGE_QUALITY'),
            'input' => '<div class="mjpro">' . MjHtml::proslider(80) . '</div>'
        );
    }

    private function injectBanner()
    {
        static $injected;
        if (!isset($injected)) {
            $injected = true;

            echo '<div id="mjprobanner">' . $this->banner . '</div>';

            $doc = JFactory::getDocument();
            $doc->addStyleDeclaration('
.mjpro {color: #ccc}
.mjpro .mjconfig_slider {background-color: #ccc}
.mjpro a,.mjpro a:hover {color: #ccc}
.mjpro label {float: none}
#mjprobanner {
	display: none;
	position: absolute;
	z-index: 3;
	padding: 10px 14px;
	font-weight: bold;
	color: #922;
	background-color: #ffc;
	border: 2px solid #f96;
	border-radius: 4px 10px 10px 10px;
}
#mjprobanner a {
	color: #22c;
	text-decoration: underline;
}');
            $doc->addScriptDeclaration('
window.addEvent("domready", function(){
	var status = 0, /* 0 - hidden, 1 - shown, 2 - pre-close state */
		banner = $("mjprobanner"),
		timerID = 0;
	function showBanner(target){
		if(timerID){
			clearTimeout(timerID);
			timerID = 0;
		}
		status = 1;
		if(target != null){
			var calc=target.getPosition();
			banner.setStyles({
				left: Math.ceil(calc.x),
				top: Math.ceil(calc.y+target.offsetHeight)
			});
		}
		banner.setStyle("display", "block");
	}
	function hideBanner(){
		if(timerID)
			return;
		status = 2;
		timerID = setTimeout(function(){
			timerID = 0;
			banner.setStyle("display", "none");
			status = 0;
		}, 300);
	}
	$$(".mjpro").addEvent("mouseenter", function(){showBanner(this);});
	$$(".mjpro").addEvent("mouseleave", function(){hideBanner();});
	$("mjprobanner").addEvent("mouseenter", function(){showBanner(null);});
	$("mjprobanner").addEvent("mouseleave", function(){hideBanner();});
	if(typeof Request == "function"){
		new Request.HTML({url: "http://ads.mobilejoomla.com/mjpro.html", method: "get", update: "mjprobanner"}).send();
	} else if(typeof Ajax == "function"){
		new Ajax("http://ads.mobilejoomla.com/mjpro.html", {method: "get", evalScripts: true, update: $("mjprobanner")}).request();
	}
});');
        }
    }
}