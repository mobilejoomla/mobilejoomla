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

class JHTMLMjconfig
{
	function label($label, $tooltip = '', $for_input = '')
	{
		$label = htmlspecialchars(JText::_($label), ENT_QUOTES, 'UTF-8');
		if($tooltip)
		{
			$tooltip = 'class="hasTip" title="'.addslashes($label).'::'.addslashes(htmlspecialchars(JText::_($tooltip), ENT_QUOTES, 'UTF-8')).'"';
		}
		if($for_input)
		{
			$for_input = 'for="'.$for_input.'"';
		}
		return "<label $for_input $tooltip>$label</label>";
	}

	function textinput($name, $value, $size=16, $attrs = NULL)
	{
		$value = addslashes(htmlspecialchars(JText::_($value), ENT_QUOTES, 'UTF-8'));
		if(!$attrs)
		{
			$attrs = array();
		}
		if(!$attrs['id'])
		{
			$attrs['id'] = $name;
		}
		$attrs['name'] = $name;
		$attrs['value'] = $value;
		$attrs['size'] = $size;
		foreach($attrs as $attr=>$val)
		{
			$attr_list[] = "$attr=\"$val\"";
		}
		$attr_str = join(' ',$attr_list);
		return "<input $attr_str>";
	}

	function menulist($menuoptions, $name, $value)
	{
		static $is_joomla15;
		if(!isset($is_joomla15))
			$is_joomla15 = (substr(JVERSION,0,3) == '1.5');

		if(!$is_joomla15)
			return JHTML::_('select.genericlist',
							$menuoptions,
							$name.'_tmp',
							array('list.attr' => 'size="7" onchange="document.getElementById(\''.$name.'\').value=this.value" ',
								  'list.select' => $value,
								  'option.text.toHtml' => false));
		else
			return JHTML::_('select.genericlist',
							$menuoptions,
							$name.'_tmp',
							'size="7" onchange="document.getElementById(\''.$name.'\').value=this.value" ',
							'value',
							'text',
							$value);
	}
}

class HTML_mobilejoomla
{
	function getMJVersion()
	{
		$manifest = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.xml';
		if(is_file($manifest))
		{
			$xml =& JFactory::getXMLParser('Simple');
			if($xml->loadFile($manifest))
			{
				$element =& $xml->document->getElementByPath('version');
				$version = $element ? $element->data() : '';
				if($version)
					return $version;
			}
		}
		return false;
	}

	function CheckForUpdate()
	{
		$version = HTML_mobilejoomla::getMJVersion();
		if($version)
		{
			$document =& JFactory::getDocument();
			$document->addStyleSheet(JURI::base(true).'/components/com_mobilejoomla/css/mjbanner.css');
			$document->addStyleSheet('http://www.mobilejoomla.com/checker.php?v='.urlencode($version).'&amp;j='.urlencode(JVERSION));
		}
	}
	
	function showUpdateNotification()
	{
		HTML_mobilejoomla::CheckForUpdate();
		JHTML::_('behavior.modal', 'a.modal');
		$app =& JFactory::getApplication();
		$updatenotice = '<div id="mjupdate"><h2>'.JText::_('COM_MJ__UPDATE_AVAILABLE').'</h2>'.
							JText::sprintf('COM_MJ__UPDATE_NOTIFICATION',
								'class="modal" href="index.php?tmpl=component&option=com_mobilejoomla&task=update" rel="{handler: \'iframe\', size: {x: 480, y: 320}}"').
						'</div>';
		$app->enqueueMessage($updatenotice, 'banner');
	}

	function showconfig(&$lists, $MobileJoomla_Settings)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.event.dispatcher');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.switcher');
		HTML_mobilejoomla::showUpdateNotification();
		$document =& JFactory::getDocument();
		$document->addScript(JURI::base(true).'/components/com_mobilejoomla/js/mj_UI.js');
		$document->addStyleSheet(JURI::base(true).'/components/com_mobilejoomla/css/mjsettings.css');

		$config_blobs = array(
			'COM_MJ__GENERAL_SETTINGS' => array(
				//left
				array(
					'COM_MJ__MOBILE_SITE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__MOBILE_SITENAME', 'COM_MJ__MOBILE_SITENAME_DESC', 'mjconfig_mobile_sitename'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'mjconfig_mobile_sitename', $MobileJoomla_Settings['mobile_sitename'], 30)
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__REMOVE_UNSUPPORTED_TAGS'),
							'input_blob' => $lists['tmpl_alldevices_removetags']
						),
					),
					'COM_MJ__IMAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__IMAGE_QUALITY', 'COM_MJ__IMAGE_QUALITY_DESC', 'mjconfig_jpegquality'),
							'input_blob' => '<span id="mjconfig_jpegquality_slider"><span id="mjconfig_jpegquality_knob"></span></span>'
											.JHTML::_('mjconfig.textinput', 'mjconfig_jpegquality', $MobileJoomla_Settings['jpegquality'], 2, array('style'=>'text-align:right'))
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__IMAGE_ADAPTATION_METHOD', 'COM_MJ__IMAGE_ADAPTATION_METHOD_DESC'),
							'input_blob' => $lists['tmpl_alldevices_img']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__STYLE_IMAGE_SIZE', 'COM_MJ__STYLE_IMAGE_SIZE_DESC'),
							'input_blob' => $lists['tmpl_alldevices_img_addstyles']
						),
					),
					'COM_MJ__HOMEPAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__FORCED_HOMEPAGE', 'COM_MJ__FORCED_HOMEPAGE_DESC', 'mjconfig_alldevices_homepage'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'mjconfig_alldevices_homepage', $MobileJoomla_Settings['alldeviceshomepage'], 50)
						),
						array(
							'input_blob' => JHTML::_('mjconfig.menulist', $lists['menuoptions'], 'mjconfig_alldevices_homepage', $MobileJoomla_Settings['alldeviceshomepage'])
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__COMPONENT_ON_HOMEPAGE', 'COM_MJ__COMPONENT_ON_HOMEPAGE_DESC'),
							'input_blob' => $lists['tmpl_alldevices_componenthome']
						)
					)
				),
				//right
				array(
					'COM_MJ__INFORMATION' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__CURRENT_VERSION'),
							'input_blob' => '<p>'.self::getMJVersion().' <a class="fltrt" href="#">'.JText::_('COM_MJ__GO_PRO').'</a></p>'
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__LATEST_VERSION'),
							'input_blob' => '<p>'.self::getMJVersion().' <a class="fltrt" href="#">'.JText::_('COM_MJ__UPDATE').'</a></p>'
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__DEV_DB_SIZE'),
							'input_blob' => '<p>100 MB <a class="fltrt" href="#">'.JText::_('COM_MJ__REDUCE_DEV_DB_SIZE').'</a></p>'
						)
					),
					'COM_MJ__SUPPORT'=>array(
						array(
							'label_blob' => '<p><a target="_blank" href="http://www.mobilejoomla.com/documentation.html">'.JText::_('COM_MJ__DOCUMENTATION').'</a></p>'
						),
						array(
							'label_blob' => '<p><a target="_blank" href="http://www.mobilejoomla.com/forums.html">'.JText::_('COM_MJ__FORUMS').'</a></p>'
						),
						array(
							'label_blob' => '<p><a target="_blank" href="http://www.mobilejoomla.com/blog.html">'.JText::_('COM_MJ__LATEST_NEWS').'</a></p>'
						)
					)
				)
			),
			'COM_MJ__XHTMLMP_SETTINGS' => array(
				//left
				array(
					'COM_MJ__SETTINGS' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__TEMPLATE_NAME', 'COM_MJ__TEMPLATE_NAME_XHTMLMP_DESC'),
							'input_blob' => $lists['xhtmltemplate']
						)
					),
					'COM_MJ__HOMEPAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__FORCED_HOMEPAGE', 'COM_MJ__FORCED_HOMEPAGE_DESC', 'mjconfig_xhtmlhomepage'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'mjconfig_xhtmlhomepage', $MobileJoomla_Settings['xhtmlhomepage'], 50)
						),
						array(
							'input_blob' => JHTML::_('mjconfig.menulist', $lists['menuoptions'], 'mjconfig_xhtmlhomepage', $MobileJoomla_Settings['xhtmlhomepage'])
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__COMPONENT_ON_HOMEPAGE', 'COM_MJ__COMPONENT_ON_HOMEPAGE_DESC'),
							'input_blob' => $lists['tmpl_xhtml_componenthome']
						)
					),
					'COM_MJ__IMAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__IMAGE_ADAPTATION_METHOD', 'COM_MJ__IMAGE_ADAPTATION_METHOD_DESC'),
							'input_blob' => $lists['tmpl_xhtml_img']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__DECREASE_IMAGE_WIDTH', 'COM_MJ__DECREASE_IMAGE_WIDTH_DESC', 'mjconfig_xhtml_buffer_width'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'mjconfig_xhtml_buffer_width', $MobileJoomla_Settings['xhtml_buffer_width'], 5, array('style'=>'text-align:right'))
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__STYLE_IMAGE_SIZE', 'COM_MJ__STYLE_IMAGE_SIZE_DESC'),
							'input_blob' => $lists['tmpl_xhtml_img_addstyles']
						)
					),
					'COM_MJ__ADVANCED' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__GZIP_COMPRESSION'),
							'input_blob' => $lists['xhtmlgzip']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__REMOVE_UNSUPPORTED_TAGS'),
							'input_blob' => $lists['tmpl_xhtml_removetags']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__REMOVE_SCRIPT_TAGS'),
							'input_blob' => $lists['tmpl_xhtml_removescripts']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__CONVERT_HTMLENTITIES', 'COM_MJ__CONVERT_HTMLENTITIES_DESC'),
							'input_blob' => $lists['tmpl_xhtml_entitydecode']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__CONTENT_TYPE', 'COM_MJ__CONTENT_TYPE_DESC'),
							'input_blob' => $lists['tmpl_xhtml_contenttype']
						)
					)
				),
				//right
				array(
					'COM_MJ__TEMPLATE_MODULES'=>array(),
					'COM_MJ__TEMPLATE_OPTIONS'=>array(
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__USE_HEAD','COM_MJ__USE_HEAD_DESC'),
							'input_blob'=>$lists['tmpl_xhtml_simplehead']
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__EXTENDED_EDITORS','COM_MJ__EXTENDED_EDITORS_DESC'),
							'input_blob'=>$lists['tmpl_xhtml_allowextedit']
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__EMBED_CSS','COM_MJ__EMBED_CSS_DESC'),
							'input_blob'=>$lists['tmpl_xhtml_embedcss']
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__USE_XMLHEAD','COM_MJ__USE_XMLHEAD_DESC'),
							'input_blob'=>$lists['tmpl_xhtml_xmlhead']
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__DOCTYPE_HEAD'),
							'input_blob'=>$lists['tmpl_xhtml_doctype']
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__XMLNS_IN_HEAD','COM_MJ__XMLNS_IN_HEAD_DESC'),
							'input_blob'=>$lists['tmpl_xhtml_xmlns']
						)
					)
				)
			),
			'COM_MJ__IPHONE_SETTINGS'=>array(
				//left
				array(
					'COM_MJ__SETTINGS' => array(
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__TEMPLATE_NAME','COM_MJ__TEMPLATE_NAME_IPHONE_DESC'),
							'input_blob'=>$lists['iphonetemplate']
						)
					),
					'COM_MJ__HOMEPAGE'=>array(
						array(
							'label_blob' =>JHTML::_('mjconfig.label','COM_MJ__FORCED_HOMEPAGE','COM_MJ__FORCED_HOMEPAGE_DESC','mjconfig_iphonehomepage'),
							'input_blob' =>JHTML::_('mjconfig.textinput','mjconfig_iphonehomepage',$MobileJoomla_Settings['iphonehomepage'],50),
						),
						array(
							'input_blob'=>JHTML::_('mjconfig.menulist',$lists['menuoptions'],'mjconfig_iphonehomepage',$MobileJoomla_Settings['iphonehomepage'])
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__COMPONENT_ON_HOMEPAGE','COM_MJ__COMPONENT_ON_HOMEPAGE_DESC'),
							'input_blob'=>$lists['tmpl_iphone_componenthome']
						)
					),
					'COM_MJ__IMAGE'=>array(
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__IMAGE_ADAPTATION_METHOD','COM_MJ__IMAGE_ADAPTATION_METHOD_DESC'),
							'input_blob'=>$lists['tmpl_iphone_img']
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__DECREASE_IMAGE_WIDTH','COM_MJ__DECREASE_IMAGE_WIDTH_DESC','mjconfig_iphone_buffer_width'),
							'input_blob'=>JHTML::_('mjconfig.textinput','mjconfig_iphone_buffer_width',$MobileJoomla_Settings['iphone_buffer_width'],5,array('style'=>'text-align:right'))
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__STYLE_IMAGE_SIZE','COM_MJ__STYLE_IMAGE_SIZE_DESC'),
							'input_blob'=>$lists['tmpl_iphone_img_addstyles']
						)
					),
					'COM_MJ__ADVANCED'=>array(
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__GZIP_COMPRESSION'),
							'input_blob'=>$lists['iphonegzip']
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__REMOVE_UNSUPPORTED_TAGS'),
							'input_blob'=>$lists['tmpl_iphone_removetags']
						),
					)
				),
				//right
				array(
					'COM_MJ__TEMPLATE_MODULES'=>array()
				)
			),
			'COM_MJ__WML_SETTINGS'=>array(
				//left
				array(
					'COM_MJ__SETTINGS' => array(
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__TEMPLATE_NAME','COM_MJ__TEMPLATE_NAME_WML_DESC'),
							'input_blob'=>$lists['waptemplate']
						)
					),
					'COM_MJ__HOMEPAGE'=>array(
						array(
							'label_blob' =>JHTML::_('mjconfig.label','COM_MJ__FORCED_HOMEPAGE','COM_MJ__FORCED_HOMEPAGE_DESC','mjconfig_waphomepage'),
							'input_blob' =>JHTML::_('mjconfig.textinput','mjconfig_waphomepage',$MobileJoomla_Settings['waphomepage'],50),
						),
						array(
							'input_blob'=>JHTML::_('mjconfig.menulist',$lists['menuoptions'],'mjconfig_waphomepage',$MobileJoomla_Settings['waphomepage'])
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__COMPONENT_ON_HOMEPAGE','COM_MJ__COMPONENT_ON_HOMEPAGE_DESC'),
							'input_blob'=>$lists['tmpl_wap_componenthome']
						)
					),
					'COM_MJ__IMAGE'=>array(
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__IMAGE_ADAPTATION_METHOD','COM_MJ__IMAGE_ADAPTATION_METHOD_DESC'),
							'input_blob'=>$lists['tmpl_wap_img']
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__DECREASE_IMAGE_WIDTH','COM_MJ__DECREASE_IMAGE_WIDTH_DESC','mjconfig_wml_buffer_width'),
							'input_blob'=>JHTML::_('mjconfig.textinput','mjconfig_wml_buffer_width',$MobileJoomla_Settings['wml_buffer_width'],5,array('style'=>'text-align:right'))
						)
					),
					'COM_MJ__ADVANCED'=>array(
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__GZIP_COMPRESSION'),
							'input_blob'=>$lists['wapgzip']
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__REMOVE_UNSUPPORTED_TAGS'),
							'input_blob'=>$lists['tmpl_wap_removetags']
						),
					)
				),
				//right
				array(
					'COM_MJ__TEMPLATE_MODULES' => array(),
					'COM_MJ__TEMPLATE_OPTIONS' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__MODULE_WMLCARDS', 'COM_MJ__MODULE_WMLCARDS_DESC'),
							'input_blob' => $lists['tmpl_wap_cards']
						)
					)
				)
			),
			'COM_MJ__CHTML_SETTINGS' => array(
				//left
				array(
					'COM_MJ__SETTINGS' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__TEMPLATE_NAME', 'COM_MJ__TEMPLATE_NAME_CHTML_DESC'),
							'input_blob' => $lists['imodetemplate']
						)
					),
					'COM_MJ__HOMEPAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__FORCED_HOMEPAGE', 'COM_MJ__FORCED_HOMEPAGE_DESC', 'mjconfig_imodehomepage'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'mjconfig_imodehomepage', $MobileJoomla_Settings['imodehomepage'], 50)
						),
						array(
							'input_blob' => JHTML::_('mjconfig.menulist', $lists['menuoptions'], 'mjconfig_imodehomepage', $MobileJoomla_Settings['imodehomepage'])
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__COMPONENT_ON_HOMEPAGE', 'COM_MJ__COMPONENT_ON_HOMEPAGE_DESC'),
							'input_blob' => $lists['tmpl_imode_componenthome']
						)
					),
					'COM_MJ__IMAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__IMAGE_ADAPTATION_METHOD', 'COM_MJ__IMAGE_ADAPTATION_METHOD_DESC'),
							'input_blob' => $lists['tmpl_imode_img']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__DECREASE_IMAGE_WIDTH', 'COM_MJ__DECREASE_IMAGE_WIDTH_DESC', 'mjconfig_chtml_buffer_width'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'mjconfig_chtml_buffer_width', $MobileJoomla_Settings['chtml_buffer_width'], 5, array('style'=>'text-align:right'))
						)
					),
					'COM_MJ__ADVANCED' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__GZIP_COMPRESSION'),
							'input_blob' => $lists['imodegzip']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__REMOVE_UNSUPPORTED_TAGS'),
							'input_blob' => $lists['tmpl_imode_removetags']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__CONVERT_HTMLENTITIES', 'COM_MJ__CONVERT_HTMLENTITIES_DESC'),
							'input_blob' => $lists['tmpl_imode_entitydecode']
						)
					)
				),
				//right
				array(
					'COM_MJ__TEMPLATE_MODULES' => array(),
					'COM_MJ__TEMPLATE_OPTIONS' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__DOCTYPE_HEAD'),
							'input_blob' => $lists['tmpl_imode_doctype']
						)
					)
				)
			),
			'COM_MJ__ADVANCED_SETTINGS' => array(
				//left
				array(
					'COM_MJ__PERFORMANCE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__CACHING', 'COM_MJ__CACHING_DESC'),
							'input_blob' => $lists['caching']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__BROWSER_CACHING', 'COM_MJ__BROWSER_CACHING_DESC'),
							'input_blob' => $lists['httpcaching']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__GZIP_COMPRESSION'),
							'input_blob' => $lists['alldevicesgzip']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__EMBED_CSS', 'COM_MJ__EMBED_CSS_DESC'),
							'input_blob' => $lists['tmpl_alldevices_embedcss']
						)
					),
					'COM_MJ__DOMAIN_NAME' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__DESKTOP_URL', 'COM_MJ__DESKTOP_URL_DESC', 'mjconfig_desktop_url'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'mjconfig_desktop_url', $MobileJoomla_Settings['desktop_url'], 40)
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__PAGE_FOR_PC', 'COM_MJ__PAGE_FOR_PC_DESC', 'mjconfig_pcpage'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'mjconfig_pcpage', $MobileJoomla_Settings['pcpage'], 40)
						)
					),
					'COM_MJ__IMAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__PC_TEMPLATE_WIDTH', 'COM_MJ__PC_TEMPLATE_WIDTH_DESC', 'mjconfig_templatewidth'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'mjconfig_templatewidth', $MobileJoomla_Settings['templatewidth'], 5, array('style'=>'text-align:right'))
						)
					)
				),
				//right
				array(
					'COM_MJ__REDIRECT_TO_DOMAIN'=>array(
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__XHTMLMP_DOMAIN','COM_MJ__DOMAIN_NAME_XHTMLMP_DESC','mjconfig_xhtmldomain'),
							'input_blob'=>JHTML::_('mjconfig.textinput','mjconfig_xhtmldomain',$MobileJoomla_Settings['xhtmldomain'],40)
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__IPHONE_DOMAIN','COM_MJ__DOMAIN_NAME_IPHONE_DESC','mjconfig_iphonedomain'),
							'input_blob'=> JHTML::_('mjconfig.textinput','mjconfig_iphonedomain',$MobileJoomla_Settings['iphonedomain'],40)
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__WML_DOMAIN','COM_MJ__DOMAIN_NAME_WML_DESC','mjconfig_wapdomain'),
							'input_blob'=> JHTML::_('mjconfig.textinput','mjconfig_wapdomain',$MobileJoomla_Settings['wapdomain'],40)
						),
						array(
							'label_blob'=>JHTML::_('mjconfig.label','COM_MJ__CHTML_DOMAIN','COM_MJ__DOMAIN_NAME_CHTML_DESC','mjconfig_imodedomain'),
							'input_blob'=> JHTML::_('mjconfig.textinput','mjconfig_imodedomain',$MobileJoomla_Settings['imodedomain'],40)
						)
					)
				)
			)
		);
		$tplmod_devices = array(
			'COM_MJ__XHTMLMP_SETTINGS' => 'xhtml',
			'COM_MJ__IPHONE_SETTINGS' => 'iphone',
			'COM_MJ__WML_SETTINGS' => 'wml',
			'COM_MJ__CHTML_SETTINGS' => 'chtml'
		);
		$tplmod_sections = array(
			'COM_MJ__MODULE_ABOVE_PATHWAY' => 'header',
			'COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT' => 'middle',
			'COM_MJ__MODULE_BELOW_COMPONENT' => 'footer'
		);
		
		//template modules
		foreach($tplmod_devices as $device=>$deviceconfig)
		{
			foreach($tplmod_sections as $section=>$sectionconfig)
			{
				for($i=1; $i<4; ++$i)
				{
					$config_blobs[$device][1]['COM_MJ__TEMPLATE_MODULES'][] = array(
						'label_blob' => JHTML::_('mjconfig.label', "{$section}_{$i}"),
						'input_blob' => $lists["tmpl_{$deviceconfig}_{$sectionconfig}{$i}"]
					);
				}
				$config_blobs[$device][1]['COM_MJ__TEMPLATE_MODULES'][] = array();
			}
			array_pop($config_blobs[$device][1]['COM_MJ__TEMPLATE_MODULES']);
		}
		
		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger('onMJDisplayConfig', array(&$config_blobs));
		include(JPATH_COMPONENT.DS.'admin_tpl'.DS.'config_tabs.php');
	}
}
