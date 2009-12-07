<?php
/**
 * Kuneri Mobile Joomla! for Joomla!1.5
 * http://www.mobilejoomla.com/
 *
 * @version		0.9.0
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	Copyright (C) 2008-2009 Kuneri Ltd. All rights reserved.
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

function CheckMobile()
{
	global $MobileJoomla_Settings;
	$cache=$MobileJoomla_Settings['wurflcache'];
	$uacache=$MobileJoomla_Settings['wurfluacache'];
	define('WURFL_USE_CACHE', $cache>0);
	define('WURFL_USE_MULTICACHE', $cache>1);
	define('MAX_UA_CACHE', $uacache);
	require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'wurfl'.DS.'wurfl_config.php');
	require_once(WURFL_CLASS_FILE);
	$myDevice = new wurfl_class($wurfl, $wurfl_agents);
	$myDevice->GetDeviceCapabilitiesFromAgent($_SERVER['HTTP_USER_AGENT']);
	if($myDevice->getDeviceCapability('is_wireless_device'))
	{
		switch($myDevice->getDeviceCapability('preferred_markup'))
		{
		case 'wml_1_1':
		case 'wml_1_2':
		case 'wml_1_3':
			return 'wml';//text/vnd.wap.wml encoding="ISO-8859-15"
		case 'html_wi_imode_compact_generic':
		case 'html_wi_imode_html_1':
		case 'html_wi_imode_html_2':
		case 'html_wi_imode_html_3':
		case 'html_wi_imode_html_4':
		case 'html_wi_imode_html_5':
			return 'chtml';//text/html
		case 'html_wi_oma_xhtmlmp_1_0'://application/vnd.wap.xhtml+xml
		case 'html_wi_w3_xhtmlbasic'://application/xhtml+xml DTD XHTML Basic 1.0
			return 'xhtml';
		case 'html_web_3_2'://text/html DTD HTML 3.2 Final
		case 'html_web_4_0'://text/html DTD HTML 4.01 Transitional
			return '';
		}
	}
	return '';
}
?>