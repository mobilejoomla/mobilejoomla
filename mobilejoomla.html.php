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

include_once JPATH_ADMINISTRATOR.'/components/com_mobilejoomla/classes/jhtmlmjconfig.php';

class HTML_mobilejoomla
{
	static $selftest_blob;

	static function getMJVersion()
	{
		static $mjver;
		if(!isset($mjver))
		{
			$mjver = false;
			$manifest = JPATH_ADMINISTRATOR.'/components/com_mobilejoomla/mobilejoomla.xml';
			if(is_file($manifest))
			{
				$xml = simplexml_load_file($manifest);
				$version = isset($xml->version) ? (string)$xml->version : null;
				if($version)
					$mjver = $version;
			}

			if($mjver)
			{
				if(strpos($mjver, '.pro') !== false)
					$mjver = 'Pro ' . str_replace('.pro', '', $mjver);
				else
				{
					$file = JPATH_ADMINISTRATOR.'/components/com_mobilejoomla/packages/version.dat';
					$type = file_exists($file) ? preg_replace('/\W+/','',file_get_contents($file)) : 'Community';
					$mjver = $type . ' ' . $mjver;
				}
			}
		}
		return $mjver;
	}

	static function CheckForUpdate()
	{
		$version = HTML_mobilejoomla::getMJVersion();
		if($version)
		{
			$document = JFactory::getDocument();
			$document->addStyleSheet(JURI::base(true).'/components/com_mobilejoomla/css/mjbanner.css');

			jimport('joomla.plugins.helper');
			if(JPluginHelper::isEnabled('mobile', 'scientia'))
				$detector = 'wurfl';
			elseif(JPluginHelper::isEnabled('mobile', 'amdd'))
				$detector = 'amdd';
			else
				$detector = 'simple';
			$document->addStyleSheet('http://www.mobilejoomla.com/checker.php?v='.urlencode($version)
										.'&amp;j='.urlencode(JVERSION)
										.'&amp;d='.$detector);
		}
	}
	
	static function showNotification()
	{
		JHTML::_('behavior.modal', 'a.modal');

		HTML_mobilejoomla::CheckForUpdate();

		$app = JFactory::getApplication();
		$updatenotice = '<div id="mjmsgarea"></div>';
		if(version_compare(JVERSION, '3.0', '>='))
		{
			echo $updatenotice;
		}
		else
		{
			$app->enqueueMessage($updatenotice, 'mj');
			if(version_compare(JVERSION, '1.7', '<'))
			{
				$document = JFactory::getDocument();
				$document->addStyleDeclaration('#mjmsgarea{margin:-8px -10px 8px}');
			}
		}
	}

	static function showconfig(&$lists, $MobileJoomla_Settings)
	{
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.switcher');
		JHTML::_('behavior.modal', 'a.modal');

		JToolBarHelper::title(JText::_('COM_MJ__MOBILE_JOOMLA_SETTINGS'), 'config.php');
		JToolBarHelper::apply();
		JToolBarHelper::cancel('cancel');
		$version = substr(JVERSION,0,3);
		$user = JFactory::getUser();
		if($version != '1.5' && $user->authorise('core.admin', 'com_mobilejoomla'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_mobilejoomla');
		}

		HTML_mobilejoomla::showNotification();
		$document = JFactory::getDocument();
		$document->addScript(JURI::base(true).'/components/com_mobilejoomla/js/mj_ui.js');
		$document->addStyleSheet(JURI::base(true).'/components/com_mobilejoomla/css/mjsettings.css');

		self::$selftest_blob = array();
		self::checkGD2();
		self::checkRemoteConnection();
		self::checkAliasDuplicates();
		self::checkTemplateAssignments();
		self::checkForcedMarkup();
		self::checkDesktopURL($MobileJoomla_Settings);

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
							'input_blob' => '<span id="mjconfig_jpegquality_slider" class="mjconfig_slider"><span id="mjconfig_jpegquality_knob" class="mjconfig_knob"></span></span>'
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
					'COM_MJ__EXTMANAGER' => array(
						array(
							'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__EXTS_MANAGER', 'COM_MJ__EXTS_MANAGER_DESC'),
							'input_blob' =>  '<p>'
												.'<a class="modal button" href="index.php?option=com_mobilejoomla&extmanager=view_modules" rel="{handler:\'iframe\',size:{x:760,y:480}}">Modules</a>'
												.' <span style="padding:0 2em">&nbsp;</span> '
												.'<a class="modal button" href="index.php?option=com_mobilejoomla&extmanager=view_plugins" rel="{handler:\'iframe\',size:{x:760,y:480}}">Plugins</a>'
											.'</p>'
						)
					),
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

		if(count(self::$selftest_blob))
			array_unshift_assoc($config_blobs['COM_MJ__GENERAL_SETTINGS'][1],
				self::$selftest_blob,
				'COM_MJ__SELFTEST_WARNINGS');

		if(count($lists['dbsize']))
		{
			$text = '';
			foreach($lists['dbsize'] as $plugin)
			{
				$title = $plugin[0];
				if(is_int($plugin[1]) || ctype_digit($plugin[1]))
					$size = number_format($plugin[1]/(1024*1024), 2, '.', '') . ' Mb';
				else
					$size = $plugin[1];
				$date = isset($plugin[2]) ? '<i>'.$plugin[2].'</i>' : '';
				$text .= "<p>$title $date &nbsp; [$size]</p>";
			}
		}
		else
			$text = 'N/A';
			$config_blobs['COM_MJ__GENERAL_SETTINGS'][1]['COM_MJ__INFORMATION'][] = array(
				'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__DEVICE_DATABASE_SIZE'),
				'input_blob' => $text
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
		$dispatcher->trigger('onMJDisplayConfig', array(&$config_blobs, &$MobileJoomla_Settings, $lists));

		include(JPATH_COMPONENT.'/admin_tpl/config_tabs.php');
	}

	private static function isJoomla15()
	{
		static $is_joomla15;
		if(!isset($is_joomla15))
			$is_joomla15 = (substr(JVERSION,0,3) == '1.5');
		return $is_joomla15;
	}

	private static function checkGD2()
	{
		if(!function_exists('imagecopyresized'))
		{
			self::$selftest_blob[] = array(
				'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__WARNING_GD2'),
				'input_blob' => '<p>'.JText::_('COM_MJ__WARNING_GD2_TEXT').'</p>'
			);
		}
	}

	private static function checkRemoteConnection()
	{
		if(!preg_match('#\.pro$#', self::getMJVersion()))
			return;
		if(!function_exists('fsockopen')
			&& !function_exists('curl_init')
			&& !ini_get('allow_url_fopen'))
		{
			self::$selftest_blob[] = array(
				'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__WARNING_REMOTE'),
				'input_blob' => '<p>'.JText::_('COM_MJ__WARNING_REMOTE_TEXT').'</p>'
			);
		}
	}

	private static function checkAliasDuplicates()
	{
		$db = JFactory::getDBO();

		if(!self::isJoomla15())
			return;

		$query = "SELECT m1.id, m1.menutype, m1.name AS title, m1.alias FROM #__menu AS m1 LEFT JOIN #__menu AS m2 ON m1.alias=m2.alias WHERE m1.id<>m2.id AND m1.type<>'menulink' AND m2.type<>'menulink' GROUP BY m1.id ORDER BY m1.alias";
		$db->setQuery($query);
		$duples = $db->loadObjectList();

		$url_prefix = 'index.php?option=com_menus&task=edit&cid[]=';

		if(count($duples))
		{
			$list = array();
			$alias = $duples[0]->alias;
			foreach($duples as $item)
			{
				if($alias != $item->alias)
				{
					$list[] = '';
					$alias = $item->alias;
				}
				$list[] = '<a href="'.$url_prefix.$item->id.'">'.$item->title.'</a> ['.$item->menutype.']';
			}
			self::$selftest_blob[] = array(
					'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__WARNING_ALIASES'),
					'input_blob' => '<p>' . implode('<br/>', $list) . '</p>'
			);
		}
	}

	private static function checkTemplateAssignments()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$db = JFactory::getDBO();

		//get mobile templates
		$jpath_themes = JPATH_ROOT.'/templates';
		$templates = JFolder::folders($jpath_themes);
		$mobile_templates = array();
		foreach($templates as $template)
			if(is_file($jpath_themes.'/'.$template.'/templateDetails.xml')
				&& is_file($jpath_themes.'/'.$template.'/index.php'))
			{
				$content = JFile::read($jpath_themes.'/'.$template.'/index.php');
				if(strpos($content, "defined('_MJ') or die(")!==false)
					$mobile_templates[] = $template;
			}

		// no mobile templates
		if(count($mobile_templates)==0)
		{
			self::$selftest_blob[] = array(
					'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__WARNING_NOTEMPLATES'),
					'input_blob' => '<p>'.JText::_('COM_MJ__WARNING_NOTEMPLATES_TEXT').'</p>'
			);
			return;
		}

		// get assigned mobile templates
		$list = array();
		foreach($mobile_templates as $template)
			$list[] = $db->Quote($template);
		$list = implode(', ', $list);

		$assigned_templates = array();
		if(self::isJoomla15())
		{
			$query = "SELECT tm.template, tm.menuid, m.name FROM #__templates_menu AS tm LEFT JOIN #__menu AS m ON m.id=tm.menuid WHERE template IN ($list) AND tm.menuid>=0 AND tm.client_id=0 ORDER BY tm.template, tm.menuid";
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			foreach($rows as $row)
				$assigned_templates[$row->template][] = array($row->menuid, $row->name);
		}
		else
		{
			$query = "SELECT template FROM #__template_styles WHERE template IN ($list) AND home=1 AND client_id=0 ORDER BY template";
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			foreach($rows as $row)
				$assigned_templates[$row->template][] = array(0, null);

			$query = "SELECT ts.template, m.id, m.title FROM #__menu AS m LEFT JOIN #__template_styles AS ts ON m.template_style_id=ts.id WHERE ts.template IN ($list) AND ts.client_id=0 ORDER BY ts.template, m.id";
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			foreach($rows as $row)
				$assigned_templates[$row->template][] = array($row->id, $row->title);
		}

		if(count($assigned_templates))
		{
			if(self::isJoomla15())
				$url_prefix = 'index.php?option=com_menus&task=edit&cid[]=';
			else
				$url_prefix = 'index.php?option=com_menus&task=item.edit&id=';

			$list = array();
			foreach($assigned_templates as $key=>$items)
			{
				foreach($items as $item)
				{
					$menuid = $item[0];
					$title  = $item[1];
					if($menuid)
						$list[] = $key.' &lt; <a href="'.$url_prefix.$menuid.'">'.htmlspecialchars($title).'</a>';
					else
						$list[] = '<a href="index.php?option=com_templates">'.$key.'</a>'
								 .' ('.JText::_('COM_MJ__WARNING_ASSIGNEDTEMPLATES_DEFAULT').')';
				}
			}
			self::$selftest_blob[] = array(
					'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__WARNING_ASSIGNEDTEMPLATES', 'COM_MJ__WARNING_ASSIGNEDTEMPLATES_DESC'),
					'input_blob' => '<p>'.implode('<br/>', $list).'</p>'
				);
		}
	}

	private static function checkForcedMarkup()
	{
		$markup = isset($_COOKIE['mjmarkup']) ? $_COOKIE['mjmarkup'] : '';
		if($markup=='desktop' || $markup=='')
			return;
		$resetUrl = JURI::root().'?device=desktop';
		self::$selftest_blob[] = array(
				'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__WARNING_FORCEDMARKUP'),
				'input_blob' => '<p>'
									.JText::_('COM_MJ__WARNING_FORCEDMARKUP_MARKUP_'.strtoupper($markup))
									.' [<a href="'.$resetUrl.'" target="_blank">'
										.JText::_('COM_MJ__WARNING_FORCEDMARKUP_RESET')
									.'</a>]'
								.'</p>'
			);
	}

	private static function checkDesktopURL($MobileJoomla_Settings)
	{
		$desktopURL = JURI::root();

		if($MobileJoomla_Settings['desktop_url'] == $desktopURL)
			return;

		self::$selftest_blob[] = array(
				'label_blob' => JHTML::_('mjconfig.label', 'COM_MJ__WARNING_DESKTOPURL'),
				'input_blob' => '<p>'.JText::_('COM_MJ__WARNING_DESKTOPURL_TEXT').'<br>'.$desktopURL.'</p>'
			);
	}
}

if(!function_exists('array_unshift_assoc'))
{
	function array_unshift_assoc(&$arr, $value, $key=null)
	{
		$arr = array_reverse($arr, true);
		if($key)
			$arr[$key] = $value;
		else
			$arr[] = $value;
		$arr = array_reverse($arr, true);
	}
}
