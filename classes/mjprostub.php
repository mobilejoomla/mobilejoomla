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
defined('_JEXEC') or die('Restricted access');

class plgMobileMJProStub extends JPlugin
{
	private $banner;
	function plgMobileMJProSub(&$subject, $config = null)
	{
		parent::__construct($subject, $config);
	}

	function onMJDisplayConfig(&$config_blobs, &$MobileJoomla_Settings, $lists)
	{
		$this->banner = 'This feature is available in <a href="http://www.mobilejoomla.com/mjpro">MobileJoomla Pro</a>';

		?><div id="mjprobanner"><?php echo $this->banner; ?></div><?php

		$doc = JFactory::getDocument();
		$doc->addStyleDeclaration('
.mjpro {color: #ccc}
.mjpro .mjconfig_slider {background-color: #ccc}
.mjpro a,.mjpro a:hover {color: #ccc}
.mjpro label {float: none}
#mjprobanner {
	display: none;
	position: absolute;
	z-index: 1;
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
}
');
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
		if(target != null)
			banner.position({relativeTo:target,position:"bottomleft"});
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
});
		');
		$this->backup_onMJDisplayConfig(   $config_blobs, $MobileJoomla_Settings, $lists);
		$this->features_onMJDisplayConfig( $config_blobs, $MobileJoomla_Settings, $lists);
		$this->tablet_onMJDisplayConfig(   $config_blobs, $MobileJoomla_Settings, $lists);
		$this->simulator_onMJDisplayConfig($config_blobs, $MobileJoomla_Settings, $lists);
	}

	function tablet_onMJDisplayConfig(&$config_blobs, &$MobileJoomla_Settings, $lists)
	{
		$advanced = end($config_blobs);
		$advanced_key = key($config_blobs);
		unset($config_blobs[$advanced_key]);

		/* @todo: add some text */
		$config_blobs['COM_MJ__TABLET_SETTINGS'] = array(
			array('COM_MJ__INFORMATION' => array(
				array(
					'label_blob' => '<label>'.$this->banner.'</label>'
				)
			)),
			array()
		);

		$config_blobs[$advanced_key] = $advanced;
		$config_blobs['COM_MJ__ADVANCED_SETTINGS'][1]['COM_MJ__REDIRECT_TO_DOMAIN'][] = array(
				'label_blob' => '<div class="mjpro">'.JHTML::_('mjconfig.prolabel', 'COM_MJ__TABLET_DOMAIN').'</div>',
				'input_blob' => '<div class="mjpro">'.JHTML::_('mjconfig.protextinput', '').'</div>'
			);
	}

	function backup_onMJDisplayConfig(&$config_blobs, &$MobileJoomla_Settings, $lists)
	{
		$blob = array(
				array(
					'label_blob' => '<div class="mjpro">'.JHTML::_('mjconfig.prolabel', 'COM_MJ__BACKUP_SETTINGS').'</div>',
					'input_blob' => '<div class="mjpro"><p id="backup-settings"><a href="index.php?option=com_mobilejoomla&task=backup">'.JText::_('COM_MJ__BACKUP').'</a></p></div>'
				),
				array(
					'label_blob' => '<div class="mjpro">'.JHTML::_('mjconfig.prolabel', 'COM_MJ__RESTORE').'</div>',
					'input_blob' => '<div class="mjpro"><p id="restore_file"><input type="file" disabled /><input type="button" disabled class="backup-restore" value="'.JText::_('COM_MJ__RESTORE_BTN').'" /></p></div>'
				)
			);
		array_unshift_assoc($config_blobs['COM_MJ__ADVANCED_SETTINGS'][0], $blob, 'COM_MJ__BACKUP_RESTORE');
	}

	function simulator_onMJDisplayConfig(&$config_blobs, &$MobileJoomla_Settings, $lists)
	{
		$devices=array(
			'COM_MJ__XHTMLMP_SETTINGS' => array(
				'Samsung Galaxy Nexus' => "size:{x:299,y:470}",
			),
			'COM_MJ__IPHONE_SETTINGS' => array(
				'iPhone 4S' => "size:{x:320,y:416}",
			),
			'COM_MJ__WML_SETTINGS' => array(
				'Nokia 3510i' => "size:{x:121,y:66}",// ~4 lines
			),
			'COM_MJ__CHTML_SETTINGS' => array(
				'NEC N341i' => "size:{x:162,y:179}",
			),
		);

		foreach($devices as $section=>$deviceinfo)
		{
			$skiplabel = false;
			$rows=array();
			foreach($deviceinfo as $model=>$modalopts)
			{
				$row = array();
				if(!$skiplabel)
					$row['label_blob'] = '<div class="mjpro">'.JHTML::_('mjconfig.prolabel', 'COM_MJ__DEVICE').'</div>';
				$row['input_blob'] = "<div class=\"mjpro\"><a href=\"javascript:return false;\">$model</a></div>";
				$rows[]=$row;
				$skiplabel = true;
			}
			array_unshift_assoc($config_blobs[$section][1], $rows, 'COM_MJ__PREVIEW');
		}
	}

	function features_onMJDisplayConfig(&$config_blobs, &$MobileJoomla_Settings, $lists)
	{
		$config_blobs['COM_MJ__GENERAL_SETTINGS'][0]['COM_MJ__IMAGE'][] = array(
				'label_blob' => '<div class="mjpro">'.JHTML::_('mjconfig.prolabel', 'COM_MJ__HIRES_IMAGES').'</div>',
				'input_blob' => '<div class="mjpro">'.JHTML::_('mjconfig.probooleanparam', 0).'</div>'
			);
		$config_blobs['COM_MJ__GENERAL_SETTINGS'][0]['COM_MJ__IMAGE'][] = array(
				'label_blob' => '<div class="mjpro">'.JHTML::_('mjconfig.prolabel', 'COM_MJ__HIRES_IMAGE_QUALITY').'</div>',
				'input_blob' => '<div class="mjpro"><span id="mjconfig_hijpegquality_slider" class="mjconfig_slider"><span id="mjconfig_hijpegquality_knob" class="mjconfig_knob"></span></span>'
								.JHTML::_('mjconfig.protextinput', 80, 2, array('style'=>'text-align:right;width:2em')).'%</div>'
			);
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration(
			"window.addEvent('domready',function(){"
			."try{"
				."var slider=new Slider('mjconfig_hijpegquality_slider','mjconfig_hijpegquality_knob',{steps:100}),"
					."quality=\$('mjconfig_hijpegquality');"
				."slider.set(+quality.value);"
			."}catch(e){};"
			."})");
	}
}
