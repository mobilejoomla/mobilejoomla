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

class plgMobileWebbots extends JPlugin
{
	function plgMobileWebbots(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onDeviceDetection(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';
		function CheckSubstrs($substrs, $text)
		{
			foreach($substrs as $substr)
				if(false!==strpos($text, $substr))
					return true;
			return false;
		}

		$desktop_os_list = array ('Windows NT', 'Macintosh', 'Mac OS X', 'Mac_PowerPC', 'MacPPC', 'X11', 'x86_64', 'ia64', 'i686', 'i586', 'i386', 'Windows+NT', 'Windows XP', 'Windows 2000', 'Win2000', 'Windows ME', 'Win 9x', 'Windows 98', 'Windows 95', 'Win16', 'Win95', 'Win98', 'WinNT', 'Linux ppc', '(OS/2', '; OS/2', 'OpenBSD', 'FreeBSD', 'NetBSD', 'SunOS', 'BeOS', 'Solaris', 'Debian', 'HP-UX', 'HPUX', 'IRIX', 'Unix', 'UNIX', 'OpenVMS', 'RISC', 'Darwin', 'Konqueror', 'MSIE 7.0', 'MSIE 8.0');
		$webbots_list = array ('Bot', 'bot', 'BOT', 'Crawler', 'crawler', 'Spider', 'Googlebot', 'ia_archiver', 'Mediapartners-Google', 'msnbot', 'Yahoo! Slurp', 'YahooSeeker', 'Validator', 'W3C-checklink', 'CSSCheck', 'GSiteCrawler');
		$wapbots_list = array ('Wapsilon', 'WinWAP', 'WAP-Browser');
		$found_desktop = CheckSubstrs($desktop_os_list, $useragent_commentsblock) || CheckSubstrs($webbots_list, $useragent);
		$found_mobile = CheckSubstrs($wapbots_list, $useragent);
		if($found_mobile && !$found_desktop)
		{ // WAP bot for sure
			$MobileJoomla_Device['markup'] = 'wml';
		}
		elseif($found_desktop && !$found_mobile && $MobileJoomla_Device['markup']===false)
		{
			$mobile_os_list = array ('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
			$mobile_token_list = array ('Profile/MIDP', 'Configuration/CLDC-', '160x160', '176x220', '240x240', '240x320', '320x240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo');
			$found_mobile = CheckSubstrs($mobile_os_list, $useragent_commentsblock) || CheckSubstrs($mobile_token_list, $useragent);
			if(!$found_mobile)
				$MobileJoomla_Device['markup'] = ''; //Desktop for sure
		}
	}
}
