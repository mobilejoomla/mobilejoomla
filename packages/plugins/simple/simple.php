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

class plgMobileSimple extends JPlugin
{
	function plgMobileSimple(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onDeviceDetection(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		$this->checkAccept($MobileJoomla_Settings, $MobileJoomla_Device);
		$this->checkUserAgent($MobileJoomla_Settings, $MobileJoomla_Device);
		if($MobileJoomla_Device['markup'])
			$this->checkScreenSize($MobileJoomla_Settings, $MobileJoomla_Device);
	}
	
	function checkAccept(&$MobileJoomla_Settings, &$MobileJoomla_Device)
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
		$max = max($c);
		foreach($c as $mime_markup=>$val)
			if($val!=$max)
				unset($c[$mime_markup]);
		$mime = 'html';
		if(array_key_exists('html', $c))
		{
			if(strpos(@$_SERVER['HTTP_USER_AGENT'], 'Profile/MIDP-2.0 Configuration/CLDC-1.1') && array_key_exists('xhtml', $c))
				$mime = 'xhtml';
			else
				$mime = 'html';
		}
		elseif(array_key_exists('xhtml', $c))
			$mime = 'xhtml';
		elseif(array_key_exists('mhtml', $c))
			$mime = 'mhtml';
		elseif(array_key_exists('wml', $c))
			$mime = 'wml';
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
	
	function checkUserAgent(&$MobileJoomla_Settings, &$MobileJoomla_Device)
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

		$iphone_list = array('Mozilla/5.0 (iPod;',
							 'Mozilla/5.0 (iPod touch;',
							 'Mozilla/5.0 (iPhone;',
							 'Apple iPhone ',
							 'Mozilla/5.0 (iPhone Simulator;',
							 'Mozilla/5.0 (Aspen Simulator;',
							 'Mozilla/5.0 (device; U; CPU iPhone OS');
		if($MobileJoomla_Settings['iphoneipad'])
			$iphone_list[] = 'Mozilla/5.0 (iPad;';
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

		$useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';

		$desktop_os_list = array('Windows NT', 'Macintosh', 'Mac OS X', 'Mac_PowerPC', 'MacPPC', 'X11',
								 'x86_64', 'ia64', 'i686', 'i586', 'i386', 'Windows+NT', 'Windows XP',
								 'Windows 2000', 'Win2000', 'Windows ME', 'Win 9x', 'Windows 98',
								 'Windows 95', 'Win16', 'Win95', 'Win98', 'WinNT', 'Linux ppc', '(OS/2',
								 '; OS/2', 'OpenBSD', 'FreeBSD', 'NetBSD', 'SunOS', 'BeOS', 'Solaris',
								 'Debian', 'HP-UX', 'HPUX', 'IRIX', 'Unix', 'UNIX', 'OpenVMS', 'RISC',
								 'Darwin', 'Konqueror', 'MSIE 7.0', 'MSIE 8.0', 'MSIE 9.0');
		$webbots_list = array('Bot', 'bot', 'BOT', 'Crawler', 'crawler', 'Spider', 'Googlebot',
							  'ia_archiver', 'Mediapartners-Google', 'msnbot', 'Yahoo! Slurp', 'YahooSeeker',
							  'Validator', 'W3C-checklink', 'CSSCheck', 'GSiteCrawler');

		$found_desktop = $this->CheckSubstrs($desktop_os_list, $useragent_commentsblock) ||
						 $this->CheckSubstrs($webbots_list, $useragent);
		if($found_desktop)
		{
			$MobileJoomla_Device['markup'] = '';
			return;
		}

		$wapbots_list = array('Wapsilon', 'WinWAP', 'WAP-Browser');
		$found_mobilebot = $this->CheckSubstrs($wapbots_list, $useragent);
		if($found_mobilebot)
		{
			$MobileJoomla_Device['markup'] = 'wml';
			return;
		}

		$mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian',
								'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo',
								'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ', 'webOS/');
		$mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160x160', '176x220',
								   '240x240', '240x320', '320x240', 'UP.Browser', 'UP.Link', 'SymbianOS',
								   'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry',
								   'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_',
								   'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo');
		$found_mobile = $this->CheckSubstrs($mobile_os_list, $useragent_commentsblock) ||
						$this->CheckSubstrs($mobile_token_list, $useragent);
		if($found_mobile)
		{
			$MobileJoomla_Device['markup'] = 'xhtml';
			return;
		}
	}
	
	function checkScreenSize(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		if(isset($_SERVER['HTTP_X_SCREEN_WIDTH']) && $_SERVER['HTTP_X_SCREEN_WIDTH']
				&& isset($_SERVER['HTTP_X_SCREEN_HEIGHT']) && $_SERVER['HTTP_X_SCREEN_HEIGHT'])
		{
			$MobileJoomla_Device['screenwidth']  = (int)$_SERVER['HTTP_X_SCREEN_WIDTH'];
			$MobileJoomla_Device['screenheight'] = (int)$_SERVER['HTTP_X_SCREEN_HEIGHT'];
			return;
		}
		if(isset($_SERVER['HTTP_X_BROWSER_WIDTH']) && $_SERVER['HTTP_X_BROWSER_WIDTH']
				&& isset($_SERVER['HTTP_X_BROWSER_HEIGHT']) && $_SERVER['HTTP_X_BROWSER_HEIGHT'])
		{
			$MobileJoomla_Device['screenwidth']  = (int)$_SERVER['HTTP_X_BROWSER_WIDTH'];
			$MobileJoomla_Device['screenheight'] = (int)$_SERVER['HTTP_X_BROWSER_HEIGHT'];
			return;
		}
		if(isset($_SERVER['HTTP_X_OS_PREFS'])
				&& preg_match('#fw:(\d+);\s*fh:(\d+);#i',$_SERVER['HTTP_X_OS_PREFS'],$matches))
		{
			$MobileJoomla_Device['screenwidth']  = (int)$matches[1];
			$MobileJoomla_Device['screenheight'] = (int)$matches[2];
			return;
		}

		$screen = '';
		if(empty($screen) && isset($_SERVER['HTTP_UA_PIXELS']))
			$screen = $_SERVER['HTTP_UA_PIXELS'];
		if(empty($screen) && isset($_SERVER['HTTP_X_UP_DEVCAP_SCREENPIXELS']))
			$screen = $_SERVER['HTTP_X_UP_DEVCAP_SCREENPIXELS'];
		if(empty($screen) && isset($_SERVER['HTTP_X_JPHONE_DISPLAY']))
			$screen = $_SERVER['HTTP_X_JPHONE_DISPLAY'];
		if(empty($screen) && isset($_SERVER['HTTP_X_AVANTGO_SCREENSIZE']))
			$screen = base64_decode($_SERVER['HTTP_X_AVANTGO_SCREENSIZE']);
		if(empty($screen) && isset($_SERVER['HTTP_USER_AGENT'])
				&& preg_match('#\b[\d]{3,4}x[\d]{3,4}\b#', $_SERVER['HTTP_USER_AGENT'], $matches))
		{
			$screen = $matches[0];
		}

		if(empty($screen))
			return;
		$screen = preg_split('#[x*,]#i', $screen);
		if(count($screen)==2)
		{
			$MobileJoomla_Device['screenwidth']  = (int)$screen[0];
			$MobileJoomla_Device['screenheight'] = (int)$screen[1];
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
