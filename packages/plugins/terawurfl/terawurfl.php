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
		if(version_compare(phpversion(), '5.0.0', '<'))
		{
			/** @var JDatabase $db */
			$db =& JFactory::getDBO();
			$query = "UPDATE #__plugins SET published = 0 WHERE element = 'terawurfl' AND folder = 'mobile'";
			$db->setQuery($query);
			$db->query();
			return;
		}

		if(!isset($_SERVER['HTTP_ACCEPT']) && !isset($_SERVER['HTTP_USER_AGENT']))
			return;

		//temporary patch for Google's Instant Preview
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'Google Web Preview')!==false)
		{
			$MobileJoomla_Device['markup'] = '';
			return;
		}

		require_once(dirname(__FILE__).DS.'terawurfl'.DS.'TeraWurflConfig.php');

		/** @var JRegistry $config */
		$config =& JFactory::getConfig();
		$host = $config->getValue('host');
		if($host=='' || $host[0]==':')
			$host = 'localhost'.$host;
		TeraWurflConfig::$TABLE_PREFIX = $config->getValue('dbprefix').'TeraWurfl';
		TeraWurflConfig::$DB_HOST      = $host;
		TeraWurflConfig::$DB_USER      = $config->getValue('user');
		TeraWurflConfig::$DB_PASS      = $config->getValue('password');
		TeraWurflConfig::$DB_SCHEMA    = $config->getValue('db');
		TeraWurflConfig::$LOG_LEVEL    = 0;

		$mysql4 = $this->params->get('mysql4', 0);
		if($mysql4)
			TeraWurflConfig::$DB_CONNECTOR = 'MySQL4';
		else
			TeraWurflConfig::$DB_CONNECTOR = 'MySQL5';

		$cache  = (bool)$this->params->get('cache', 1);
		TeraWurflConfig::$CACHE_ENABLE = $cache;

		require_once(dirname(__FILE__).DS.'terawurfl'.DS.'TeraWurfl.php');

		try
		{
			$wurflObj = new TeraWurfl();
			if(!is_object($wurflObj) || !$wurflObj->db->connected)
				return;
			$wurflObj->getDeviceCapabilitiesFromAgent();
		}
		catch(exception $e)
		{
			error_log("Caught exception 'Exception' with message '".$e->getMessage()."' in ".$e->getFile().':'.$e->getLine());
			return;
		}

		$MobileJoomla_Device['wurfl'] =& $wurflObj;

		if($wurflObj->getDeviceCapability('is_wireless_device'))
		{
			if($wurflObj->getDeviceCapability('device_os')=='iPhone OS')
			{
				if($MobileJoomla_Settings['iphoneipad'] || $wurflObj->getDeviceCapability('model_name')!='iPad')
					$MobileJoomla_Device['markup'] = 'iphone';
			}
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
				case 'docomo_imode_html_3':
				case 'html_wi_imode_html_4':
				case 'html_wi_imode_html_5':
				case 'html_wi_imode_htmlx_1':
				case 'html_wi_imode_htmlx_1_1':
				case 'html_wi_imode_htmlx_2':
				case 'html_wi_imode_htmlx_2_1':
				case 'html_wi_imode_htmlx_2_2':
				case 'html_wi_imode_htmlx_2_3':
					$MobileJoomla_Device['markup'] = 'chtml';
					break;
				case 'html_wi_oma_xhtmlmp_1_0': //application/vnd.wap.xhtml+xml
				case 'html_wi_w3_xhtmlbasic':   //application/xhtml+xml DTD XHTML Basic 1.0
				case 'html_wi_mml_html':
					$MobileJoomla_Device['markup'] = 'xhtml';
					break;
				case 'html_web_3_2': //text/html DTD HTML 3.2 Final
				case 'html_web_4_0': //text/html DTD HTML 4.01 Transitional
					$MobileJoomla_Device['markup'] = 'xhtml';
					break;
			}
			$MobileJoomla_Device['screenwidth']  = $wurflObj->getDeviceCapability('max_image_width');
			$MobileJoomla_Device['screenheight'] = $wurflObj->getDeviceCapability('max_image_height');

			$MobileJoomla_Device['imageformats'] = array ();
			if($wurflObj->getDeviceCapability('png'))
				$MobileJoomla_Device['imageformats'][] = 'png';
			if($wurflObj->getDeviceCapability('gif'))
				$MobileJoomla_Device['imageformats'][] = 'gif';
			if($wurflObj->getDeviceCapability('jpg'))
				$MobileJoomla_Device['imageformats'][] = 'jpg';
			if($wurflObj->getDeviceCapability('wbmp'))
				$MobileJoomla_Device['imageformats'][] = 'wbmp';
		}
		else
			$MobileJoomla_Device['markup'] = '';
	}
}
