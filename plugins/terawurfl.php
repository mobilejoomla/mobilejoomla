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

jimport('joomla.plugin.plugin');

class plgMobileTerawurfl extends JPlugin
{
	function plgMobileTerawurfl(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onDeviceDetection(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		$mysql4 = $this->params->get('mysql4', 0);
		require_once(JPATH_SITE.DS.'plugins'.DS.'mobile'.DS.'terawurfl'.DS.'TeraWurfl.php');

		if(version_compare(phpversion(), '5.0.0', '<'))
		{
			$wurflObj = new TeraWurfl();
			if(!is_object($wurflObj))
				return;
			$matched = $wurflObj->getDeviceCapabilitiesFromAgent($_SERVER['HTTP_USER_AGENT']);
		}
		else
		{
			try
			{
				$wurflObj = new TeraWurfl();
				if(!is_object($wurflObj))
					return;
				$matched = $wurflObj->getDeviceCapabilitiesFromAgent($_SERVER['HTTP_USER_AGENT']);
			}
			catch(exception $e)
			{
				return;
			}
		}

		if(!$matched)
			return;

		if($wurflObj->getDeviceCapability('is_wireless_device'))
		{
			if($wurflObj->getDeviceCapability('device_os')=='iPhone OS')
				$MobileJoomla_Device['markup'] = 'iphone';
			else switch($wurflObj->getDeviceCapability('preferred_markup'))
			{
				case 'wml_1_1':
				case 'wml_1_2':
				case 'wml_1_3':
					$MobileJoomla_Device['markup'] = 'wml';
					break;
				case 'html_wi_imode_compact_generic':
				case 'html_wi_imode_html_1':
				case 'html_wi_imode_html_2':
				case 'html_wi_imode_html_3':
				case 'html_wi_imode_html_4':
				case 'html_wi_imode_html_5':
					$MobileJoomla_Device['markup'] = 'chtml';
					break;
				case 'html_wi_oma_xhtmlmp_1_0': //application/vnd.wap.xhtml+xml
				case 'html_wi_w3_xhtmlbasic': //application/xhtml+xml DTD XHTML Basic 1.0
					$MobileJoomla_Device['markup'] = 'xhtml';
					break;
				case 'html_web_3_2': //text/html DTD HTML 3.2 Final
				case 'html_web_4_0': //text/html DTD HTML 4.01 Transitional
					$MobileJoomla_Device['markup'] = '';
					break;
			}
		}

		$MobileJoomla_Device['screenwidth'] = $wurflObj->getDeviceCapability('max_image_width');
		$MobileJoomla_Device['screenheight'] = $wurflObj->getDeviceCapability('max_image_height');

		$MobileJoomla_Device['imageformats'] = array ();
		if($wurflObj->getDeviceCapability('png'))
			$MobileJoomla_Device['imageformats'][] = 'png';
		if($wurflObj->getDeviceCapability('jpg'))
			$MobileJoomla_Device['imageformats'][] = 'jpg';
		if($wurflObj->getDeviceCapability('gif'))
			$MobileJoomla_Device['imageformats'][] = 'gif';
		if($wurflObj->getDeviceCapability('wbmp'))
			$MobileJoomla_Device['imageformats'][] = 'wbmp';
	}
}
