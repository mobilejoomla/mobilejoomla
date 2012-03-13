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

include_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'classes'.DS.'jhtmlmjconfig.php';

class HTML_mobilejoomla
{
	static function getMJVersion()
	{
		$manifest = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.xml';
		if(is_file($manifest))
		{
			$xml = JFactory::getXMLParser('Simple');
			if($xml->loadFile($manifest))
			{
				$element = $xml->document->getElementByPath('version');
				$version = $element ? $element->data() : '';
				if($version)
					return $version;
			}
		}
		return false;
	}

	static function CheckForUpdate()
	{
		$version = HTML_mobilejoomla::getMJVersion();
		if($version)
		{
			$document = JFactory::getDocument();
			$document->addStyleSheet(JURI::base(true).'/components/com_mobilejoomla/css/mjbanner.css');
			$document->addStyleSheet('http://www.mobilejoomla.com/checker.php?v='.urlencode($version).'&amp;j='.urlencode(JVERSION));
		}
	}
	
	static function showNotification()
	{
		HTML_mobilejoomla::CheckForUpdate();
		JHTML::_('behavior.modal', 'a.modal');

		$app = JFactory::getApplication();
		$updatenotice = '<div id="mjmsgarea"></div>';
		$app->enqueueMessage($updatenotice, 'banner');
		if(version_compare(JVERSION,'1.7.0','lt'))
		{
			$document = JFactory::getDocument();
			$document->addStyleDeclaration('#mjmsgarea{margin:-8px -10px 8px}');
		}
	}

	static function showconfig(&$lists, $MobileJoomla_Settings)
	{
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.switcher');
		JHTML::_('behavior.modal', 'a.modal');

		HTML_mobilejoomla::showNotification();
		$document = JFactory::getDocument();
		$document->addScript(JURI::base(true).'/components/com_mobilejoomla/js/mj_UI.js');
		$document->addStyleSheet(JURI::base(true).'/components/com_mobilejoomla/css/mjsettings.css');

		$config_blobs = array(
			'COM_MJ__GENERAL_SETTINGS' => array(
				//left
				array(
					'COM_MJ__MOBILE_SITE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__MOBILE_SITENAME', 'COM_MJ__MOBILE_SITENAME_DESC', 'mobile_sitename'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'mobile_sitename', $MobileJoomla_Settings['mobile_sitename'], 30)
						)
					),
					'COM_MJ__IMAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__IMAGE_QUALITY', 'COM_MJ__IMAGE_QUALITY_DESC', 'jpegquality'),
							'input_blob' => '<span id="mjconfig_jpegquality_slider"><span id="mjconfig_jpegquality_knob"></span></span>'
											.JHTML::_('mjconfig.textinput', 'jpegquality', $MobileJoomla_Settings['jpegquality'], 2, array('style'=>'text-align:right')).'%'
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__IMAGE_ADAPTATION_METHOD', 'COM_MJ__IMAGE_ADAPTATION_METHOD_DESC'),
							'input_blob' => $lists['global.img']
						)
					),
					'COM_MJ__HOMEPAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__FORCED_HOMEPAGE', 'COM_MJ__FORCED_HOMEPAGE_DESC', 'global.homepage'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'global.homepage', $MobileJoomla_Settings['global.homepage'])
						),
						array(
							'input_blob' => JHTML::_('mjconfig.menulist', $lists['menuoptions'], 'global.homepage', $MobileJoomla_Settings['global.homepage'])
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__COMPONENT_ON_HOMEPAGE', 'COM_MJ__COMPONENT_ON_HOMEPAGE_DESC'),
							'input_blob' => $lists['global.componenthome']
						)
					)
				),
				//right
				array(
					'COM_MJ__INFORMATION' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__CURRENT_VERSION'),
							'input_blob' => '<p>'.self::getMJVersion().'</p>'
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__LATEST_VERSION'),
							'input_blob' => '<p id="mjconfig_latestver"><span id="mjlatestver"></span> <a class="fltrt modal" id="mjlatestverurl" href="index.php?tmpl=component&option=com_mobilejoomla&task=update" rel="{handler: \'iframe\', size: {x: 480, y: 320}}">'.JText::_('COM_MJ__UPDATE').'</a></p>'
						)
					),
					'COM_MJ__SUPPORT'=>array(
						array(
							'label_blob' => '<p><a target="_blank" href="http://www.mobilejoomla.com/documentation.html?ref=info">'.JText::_('COM_MJ__DOCUMENTATION').'</a></p>'
						),
						array(
							'label_blob' => '<p><a target="_blank" href="http://www.mobilejoomla.com/forums.html?ref=info">'.JText::_('COM_MJ__FORUMS').'</a></p>'
						),
						array(
							'label_blob' => '<p><a target="_blank" href="http://www.mobilejoomla.com/blog.html?ref=info">'.JText::_('COM_MJ__LATEST_NEWS').'</a></p>'
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
							'input_blob' => $lists['xhtml.template']
						)
					),
					'COM_MJ__HOMEPAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__FORCED_HOMEPAGE', 'COM_MJ__FORCED_HOMEPAGE_DESC', 'xhtml.homepage'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'xhtml.homepage', $MobileJoomla_Settings['xhtml.homepage'])
						),
						array(
							'input_blob' => JHTML::_('mjconfig.menulist', $lists['menuoptions'], 'xhtml.homepage', $MobileJoomla_Settings['xhtml.homepage'])
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__COMPONENT_ON_HOMEPAGE', 'COM_MJ__COMPONENT_ON_HOMEPAGE_DESC'),
							'input_blob' => $lists['xhtml.componenthome']
						)
					),
					'COM_MJ__IMAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__IMAGE_ADAPTATION_METHOD', 'COM_MJ__IMAGE_ADAPTATION_METHOD_DESC'),
							'input_blob' => $lists['xhtml.img']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__DECREASE_IMAGE_WIDTH', 'COM_MJ__DECREASE_IMAGE_WIDTH_DESC', 'xhtml.buffer_width'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'xhtml.buffer_width', $MobileJoomla_Settings['xhtml.buffer_width'], 5, array('style'=>'text-align:right'))
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__STYLE_IMAGE_SIZE', 'COM_MJ__STYLE_IMAGE_SIZE_DESC'),
							'input_blob' => $lists['xhtml.img_addstyles']
						)
					),
					'COM_MJ__ADVANCED' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__GZIP_COMPRESSION'),
							'input_blob' => $lists['xhtml.gzip']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__REMOVE_UNSUPPORTED_TAGS', 'COM_MJ__REMOVE_UNSUPPORTED_TAGS_XHTMLMP_DESC'),
							'input_blob' => $lists['xhtml.removetags']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__REMOVE_SCRIPT_TAGS'),
							'input_blob' => $lists['xhtml.removescripts']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__CONVERT_HTMLENTITIES', 'COM_MJ__CONVERT_HTMLENTITIES_DESC'),
							'input_blob' => $lists['xhtml.entitydecode']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__CONTENT_TYPE', 'COM_MJ__CONTENT_TYPE_DESC'),
							'input_blob' => $lists['xhtml.contenttype']
						)
					)
				),
				//right
				array(
					'COM_MJ__TEMPLATE_MODULES' => array(),
					'COM_MJ__TEMPLATE_OPTIONS' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__USE_HEAD', 'COM_MJ__USE_HEAD_DESC'),
							'input_blob' => $lists['xhtml.simplehead']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__EXTENDED_EDITORS', 'COM_MJ__EXTENDED_EDITORS_DESC'),
							'input_blob' => $lists['xhtml.allowextedit']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__EMBED_CSS', 'COM_MJ__EMBED_CSS_DESC'),
							'input_blob' => $lists['xhtml.embedcss']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__USE_XMLHEAD', 'COM_MJ__USE_XMLHEAD_DESC'),
							'input_blob' => $lists['xhtml.xmlhead']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__DOCTYPE_HEAD'),
							'input_blob' => $lists['xhtml.doctype']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__XMLNS_IN_HEAD', 'COM_MJ__XMLNS_IN_HEAD_DESC'),
							'input_blob' => $lists['xhtml.xmlns']
						)
					)
				)
			),
			'COM_MJ__IPHONE_SETTINGS' => array(
				//left
				array(
					'COM_MJ__SETTINGS' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__TEMPLATE_NAME', 'COM_MJ__TEMPLATE_NAME_IPHONE_DESC'),
							'input_blob' => $lists['iphone.template']
						)
					),
					'COM_MJ__HOMEPAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__FORCED_HOMEPAGE', 'COM_MJ__FORCED_HOMEPAGE_DESC', 'iphone.homepage'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'iphone.homepage', $MobileJoomla_Settings['iphone.homepage']),
						),
						array(
							'input_blob' => JHTML::_('mjconfig.menulist', $lists['menuoptions'], 'iphone.homepage', $MobileJoomla_Settings['iphone.homepage'])
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__COMPONENT_ON_HOMEPAGE', 'COM_MJ__COMPONENT_ON_HOMEPAGE_DESC'),
							'input_blob' => $lists['iphone.componenthome']
						)
					),
					'COM_MJ__IMAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__IMAGE_ADAPTATION_METHOD', 'COM_MJ__IMAGE_ADAPTATION_METHOD_DESC'),
							'input_blob' => $lists['iphone.img']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__DECREASE_IMAGE_WIDTH', 'COM_MJ__DECREASE_IMAGE_WIDTH_DESC', 'iphone.buffer_width'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'iphone.buffer_width', $MobileJoomla_Settings['iphone.buffer_width'], 5, array('style'=>'text-align:right'))
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__STYLE_IMAGE_SIZE', 'COM_MJ__STYLE_IMAGE_SIZE_DESC'),
							'input_blob' => $lists['iphone.img_addstyles']
						)
					),
					'COM_MJ__ADVANCED' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__GZIP_COMPRESSION'),
							'input_blob' => $lists['iphone.gzip']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__REMOVE_UNSUPPORTED_TAGS'),
							'input_blob' => $lists['iphone.removetags']
						)
					)
				),
				//right
				array(
					'COM_MJ__TEMPLATE_MODULES' => array()
				)
			),
			'COM_MJ__WML_SETTINGS' => array(
				//left
				array(
					'COM_MJ__SETTINGS' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__TEMPLATE_NAME', 'COM_MJ__TEMPLATE_NAME_WML_DESC'),
							'input_blob' => $lists['wml.template']
						)
					),
					'COM_MJ__HOMEPAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__FORCED_HOMEPAGE', 'COM_MJ__FORCED_HOMEPAGE_DESC', 'wml.homepage'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'wml.homepage', $MobileJoomla_Settings['wml.homepage']),
						),
						array(
							'input_blob' => JHTML::_('mjconfig.menulist', $lists['menuoptions'], 'wml.homepage', $MobileJoomla_Settings['wml.homepage'])
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__COMPONENT_ON_HOMEPAGE', 'COM_MJ__COMPONENT_ON_HOMEPAGE_DESC'),
							'input_blob' => $lists['wml.componenthome']
						)
					),
					'COM_MJ__IMAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__IMAGE_ADAPTATION_METHOD', 'COM_MJ__IMAGE_ADAPTATION_METHOD_DESC'),
							'input_blob' => $lists['wml.img']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__DECREASE_IMAGE_WIDTH', 'COM_MJ__DECREASE_IMAGE_WIDTH_DESC', 'wml.buffer_width'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'wml.buffer_width', $MobileJoomla_Settings['wml.buffer_width'], 5, array('style'=>'text-align:right'))
						)
					),
					'COM_MJ__ADVANCED' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__GZIP_COMPRESSION'),
							'input_blob' => $lists['wml.gzip']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__REMOVE_UNSUPPORTED_TAGS'),
							'input_blob' => $lists['wml.removetags']
						)
					)
				),
				//right
				array(
					'COM_MJ__TEMPLATE_MODULES' => array(),
					'COM_MJ__TEMPLATE_OPTIONS' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__MODULE_WMLCARDS', 'COM_MJ__MODULE_WMLCARDS_DESC'),
							'input_blob' => $lists['wml.cards']
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
							'input_blob' => $lists['chtml.template']
						)
					),
					'COM_MJ__HOMEPAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__FORCED_HOMEPAGE', 'COM_MJ__FORCED_HOMEPAGE_DESC', 'chtml.homepage'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'chtml.homepage', $MobileJoomla_Settings['chtml.homepage'])
						),
						array(
							'input_blob' => JHTML::_('mjconfig.menulist', $lists['menuoptions'], 'chtml.homepage', $MobileJoomla_Settings['chtml.homepage'])
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__COMPONENT_ON_HOMEPAGE', 'COM_MJ__COMPONENT_ON_HOMEPAGE_DESC'),
							'input_blob' => $lists['chtml.componenthome']
						)
					),
					'COM_MJ__IMAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__IMAGE_ADAPTATION_METHOD', 'COM_MJ__IMAGE_ADAPTATION_METHOD_DESC'),
							'input_blob' => $lists['chtml.img']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__DECREASE_IMAGE_WIDTH', 'COM_MJ__DECREASE_IMAGE_WIDTH_DESC', 'chtml.buffer_width'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'chtml.buffer_width', $MobileJoomla_Settings['chtml.buffer_width'], 5, array('style'=>'text-align:right'))
						)
					),
					'COM_MJ__ADVANCED' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__GZIP_COMPRESSION'),
							'input_blob' => $lists['chtml.gzip']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__REMOVE_UNSUPPORTED_TAGS'),
							'input_blob' => $lists['chtml.removetags']
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__CONVERT_HTMLENTITIES', 'COM_MJ__CONVERT_HTMLENTITIES_DESC'),
							'input_blob' => $lists['chtml.entitydecode']
						)
					)
				),
				//right
				array(
					'COM_MJ__TEMPLATE_MODULES' => array(),
					'COM_MJ__TEMPLATE_OPTIONS' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__DOCTYPE_HEAD'),
							'input_blob' => $lists['chtml.doctype']
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
							'input_blob' => $lists['global.gzip']
						)
					),
					'COM_MJ__IMAGE' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__PC_TEMPLATE_WIDTH', 'COM_MJ__PC_TEMPLATE_WIDTH_DESC', 'templatewidth'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'templatewidth', $MobileJoomla_Settings['templatewidth'], 5, array('style'=>'text-align:right')),
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__STYLE_IMAGE_SIZE', 'COM_MJ__STYLE_IMAGE_SIZE_DESC'),
							'input_blob' => $lists['global.img_addstyles']
						)
					),
					'COM_MJ__COMPATIBILITY' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__REMOVE_UNSUPPORTED_TAGS'),
							'input_blob' => $lists['global.removetags']
						)
					)
				),
				//right
				array(
					'COM_MJ__DOMAIN_NAME' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__DESKTOP_URL', 'COM_MJ__DESKTOP_URL_DESC', 'desktop_url'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'desktop_url', $MobileJoomla_Settings['desktop_url'])
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__PAGE_FOR_PC', 'COM_MJ__PAGE_FOR_PC_DESC', 'pcpage'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'pcpage', $MobileJoomla_Settings['pcpage'])
						)
					),
					'COM_MJ__REDIRECT_TO_DOMAIN' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__XHTMLMP_DOMAIN', 'COM_MJ__DOMAIN_NAME_XHTMLMP_DESC', 'xhtml.domain'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'xhtml.domain', $MobileJoomla_Settings['xhtml.domain'])
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__IPHONE_DOMAIN', 'COM_MJ__DOMAIN_NAME_IPHONE_DESC', 'iphone.domain'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'iphone.domain', $MobileJoomla_Settings['iphone.domain'])
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__WML_DOMAIN', 'COM_MJ__DOMAIN_NAME_WML_DESC', 'wml.domain'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'wml.domain', $MobileJoomla_Settings['wml.domain'])
						),
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__CHTML_DOMAIN', 'COM_MJ__DOMAIN_NAME_CHTML_DESC', 'chtml.domain'),
							'input_blob' => JHTML::_('mjconfig.textinput', 'chtml.domain', $MobileJoomla_Settings['chtml.domain'])
						)
					)
				)
			)
		);
		if($lists['dbsize'])
		{
			$config_blobs['COM_MJ__GENERAL_SETTINGS'][1]['COM_MJ__INFORMATION'][] = array(
					'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__DEVICE_DATABASE_SIZE'),
					'input_blob' => '<p>'.$lists['dbsize'].' MB</p>'
				);
		}
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
				for($i=1; $i<=3; ++$i)
				{
					$config_blobs[$device][1]['COM_MJ__TEMPLATE_MODULES'][] = array(
						'label_blob' => JHTML::_('mjconfig.label', "{$section}_{$i}"),
						'input_blob' => $lists["{$deviceconfig}.{$sectionconfig}{$i}"]
					);
				}
			}
		}
		
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onMJDisplayConfig', array(&$config_blobs, &$MobileJoomla_Settings));

		include(JPATH_COMPONENT.DS.'admin_tpl'.DS.'config_tabs.php');
	}
}
