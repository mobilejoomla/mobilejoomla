<?php
/**
 * Mobile Joomla!
 * http://www.mobilejoomla.com
 *
 * @version		0.9.8
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	(C) 2008-2010 MobileJoomla!
 * @date		September 2010
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgMobileSimple extends JPlugin
{
	function plgMobileSimple(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onDeviceDetection(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		$this->checkAccept($MobileJoomla_Device);
		$this->checkUserAgent($MobileJoomla_Device);
	}
	
	function checkAccept(&$MobileJoomla_Device)
	{
		if(!isset($_SERVER['HTTP_ACCEPT']))
			return;
		$accept = array('xhtml' => 'application/xhtml+xml',
						'html' => 'text/html',
						'wml' => 'text/vnd.wap.wml',
						'mhtml' => 'application/vnd.wap.xhtml+xml');
		$c = array ();
		foreach($accept as $mime_markup => $mime_type)
		{
			$c[$mime_markup] = 0;
			if(stristr($_SERVER['HTTP_ACCEPT'], $mime_type))
			{
				if(preg_match('|'.str_replace(array('/','.','+'), array('\/','\.','\+'), $mime_type).';q=(0\.\d+)|i', $_SERVER['HTTP_ACCEPT'], $matches))
					$c[$mime_markup] += (float) $matches[1];
				else
					$c[$mime_markup]++;
			}
		}
		arsort($c, SORT_NUMERIC);
		if(array_sum($c) == count($c))
			$mime = 'html';
		else
		{
			$max = max($c);
			foreach($c as $mime_markup=>$val)
				if($val!=$max)
					unset($c[$mime_markup]);
			if(array_key_exists('html', $c))
				$mime = 'html';
			elseif(array_key_exists('xhtml', $c))
				$mime = 'xhtml';
			elseif(array_key_exists('mhtml', $c))
				$mime = 'mhtml';
			elseif(array_key_exists('wml', $c))
				$mime = 'wml';
		}
		$MobileJoomla_Device['mimetype'] = $accept[$mime];
		switch($mime)
		{
			case 'wml':
				$MobileJoomla_Device['markup'] = 'wml';
				break;
			case 'mhtml':
			case 'xhtml':
				$MobileJoomla_Device['markup'] = 'xhtml';
				break;
			default:
				$MobileJoomla_Device['markup'] = '';
		}
	}
	
	function checkUserAgent(&$MobileJoomla_Device)
	{
		$userAgentHeaders = array(
			'HTTP_X_DEVICE_USER_AGENT',
			'HTTP_X_ORIGINAL_USER_AGENT',
			'HTTP_X_OPERAMINI_PHONE_UA',
			'HTTP_X_SKYFIRE_PHONE',
			'HTTP_X_BOLT_PHONE_UA',
			'HTTP_USER_AGENT'
		);
		$useragent = '';
		foreach($userAgentHeaders as $header)
			if(isset($_SERVER[$header]) && $_SERVER[$header])
			{
				$useragent = $_SERVER[$header];
				break;
			}
		if(empty($useragent))
			return;

		$useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';

		$iphone_list = array('Mozilla/5.0 (iPod;',
		                     'Mozilla/5.0 (iPod touch;',
		                     'Mozilla/5.0 (iPhone;',
		                     'Apple iPhone ',
		                     'Mozilla/5.0 (iPhone Simulator;',
		                     'Mozilla/5.0 (Aspen Simulator;',
		                     'Mozilla/5.0 (device; U; CPU iPhone OS');
		foreach($iphone_list as $iphone_ua)
			if(strpos($useragent, $iphone_ua)===0)
			{
				$MobileJoomla_Device['markup'] = 'iphone';
				return;
			}

		if(((substr($useragent, 0, 10) == 'portalmmm/') ||
			(substr($useragent, 0, 7)  == 'DoCoMo/')))
		{
			$MobileJoomla_Device['markup'] = 'chtml';
			return;
		}

		$desktop_os_list = array('Windows NT', 'Macintosh', 'Mac OS X', 'Mac_PowerPC', 'MacPPC', 'X11',
								 'x86_64', 'ia64', 'i686', 'i586', 'i386', 'Windows+NT', 'Windows XP',
								 'Windows 2000', 'Win2000', 'Windows ME', 'Win 9x', 'Windows 98',
								 'Windows 95', 'Win16', 'Win95', 'Win98', 'WinNT', 'Linux ppc', '(OS/2',
								 '; OS/2', 'OpenBSD', 'FreeBSD', 'NetBSD', 'SunOS', 'BeOS', 'Solaris',
								 'Debian', 'HP-UX', 'HPUX', 'IRIX', 'Unix', 'UNIX', 'OpenVMS', 'RISC',
								 'Darwin', 'Konqueror', 'MSIE 7.0', 'MSIE 8.0');
		$webbots_list = array('Bot', 'bot', 'BOT', 'Crawler', 'crawler', 'Spider', 'Googlebot',
							  'ia_archiver', 'Mediapartners-Google', 'msnbot', 'Yahoo! Slurp', 'YahooSeeker',
							  'Validator', 'W3C-checklink', 'CSSCheck', 'GSiteCrawler');
		$wapbots_list = array('Wapsilon', 'WinWAP', 'WAP-Browser');
		$found_desktop = self::CheckSubstrs($desktop_os_list, $useragent_commentsblock) ||
						 self::CheckSubstrs($webbots_list, $useragent);
		$found_mobilebot = self::CheckSubstrs($wapbots_list, $useragent);
		if($found_mobilebot && !$found_desktop)
		{ // WAP bot for sure
			$MobileJoomla_Device['markup'] = 'wml';
			return;
		}

		if($found_desktop && !$found_mobilebot)
		{
			$mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian',
									'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo',
									'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
			$mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160x160', '176x220',
									   '240x240', '240x320', '320x240', 'UP.Browser', 'UP.Link', 'SymbianOS',
									   'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry',
									   'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_',
									   'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo');
			$found_mobile = self::CheckSubstrs($mobile_os_list, $useragent_commentsblock) ||
							self::CheckSubstrs($mobile_token_list, $useragent);
			if(!$found_mobile)
				$MobileJoomla_Device['markup'] = ''; //Desktop for sure
		}
	}

	function CheckSubstrs($substrs, $text)
	{
		foreach($substrs as $substr)
			if(false!==strpos($text, $substr))
				return true;
		return false;
	}
}
