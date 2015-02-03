<?php
/**
 * Advanced Mobile Device Detection
 *
 * @version		###VERSION###
 * @license		###LICENSE###
 * @copyright	###COPYRIGHT###
 * @date		###DATE###
 */

class AmddUA
{
	/**
	 * Get real User-Agent string from HTTP headers
	 * @static
	 * @param array $headers
	 * @return string
	 */
	public static function getUserAgentFromRequest($headers = null)
	{
		static $userAgentHeaders = array(
			'HTTP_DEVICE_STOCK_UA',       // Opera proposal https://github.com/operasoftware/Device-Stock-UA-RFC
			'HTTP_X_DEVICE_USER_AGENT',   // Content Transformation Proxies http://www.w3.org/TR/ct-guidelines/
			'HTTP_X_ORIGINAL_USER_AGENT', // Google Wireless Transcoder
			'HTTP_X_OPERAMINI_PHONE_UA',  // Opera Mini browser
			'HTTP_X_SKYFIRE_PHONE',       // Skyfire browser
			'HTTP_X_BOLT_PHONE_UA',       // Bolt browser
			'HTTP_X_MOBILE_UA',           // Mowser transcoder
			'HTTP_X_UCBROWSER_DEVICE_UA', // UC Browser
			'HTTP_USER_AGENT'
		);

		if($headers === null)
			$headers = $_SERVER;

		foreach($userAgentHeaders as $header)
			if(isset($headers[$header]))
				return $headers[$header];

		return '';
	}

	// \x80-\xFF sequence
	const ASCII_UP = "\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8A\x8B\x8C\x8D\x8E\x8F\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9A\x9B\x9C\x9D\x9E\x9F\xA0\xA1\xA2\xA3\xA4\xA5\xA6\xA7\xA8\xA9\xAA\xAB\xAC\xAD\xAE\xAF\xB0\xB1\xB2\xB3\xB4\xB5\xB6\xB7\xB8\xB9\xBA\xBB\xBC\xBD\xBE\xBF\xC0\xC1\xC2\xC3\xC4\xC5\xC6\xC7\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1\xD2\xD3\xD4\xD5\xD6\xD7\xD8\xD9\xDA\xDB\xDC\xDD\xDE\xDF\xE0\xE1\xE2\xE3\xE4\xE5\xE6\xE7\xE8\xE9\xEA\xEB\xEC\xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF6\xF7\xF8\xF9\xFA\xFB\xFC\xFD\xFE\xFF";
	// 128 '?' characters
	const ASCII_QQ = '????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????';
	/**
	 * Remove redundant data from User-Agent string
	 * @static
	 * @param string $ua
	 * @return string
	 */
	public static function normalize($ua)
	{
		// Remove non-ascii characters
		//$ua = preg_replace('#[^ -~]+#', '', $ua);
		$ua = strtr($ua, self::ASCII_UP, self::ASCII_QQ);
		$ua = str_replace(array("\n", "\r", "\t"), ' ', $ua);

		// Fix possible proxy bugs
		$ua = ltrim($ua, ':= \'"');
		$ua = preg_replace('#^(?:User-Agent[:= ]*)+#i', '', $ua);
		if(strpos($ua, '+') !== false && strpos($ua, ' ') === false)
		{
			$chars = count_chars($ua, 1);
			if($chars[ord('+')] >= 4)
				$ua = str_replace('+', ' ', $ua);
		}
		$ua = str_replace(')AppleWebKit', ') AppleWebKit', $ua);
		$ua = trim($ua, " \'\"\\");

		// Remove serial numbers
		$ua = preg_replace('#(?:(?:[/;]SN| IMEI/)(?:\d{14,15}|X{14,15})|\[(?:NT|ST|TF)?(?:\d+|X+)\])#', '', $ua);
		$ua = preg_replace('# BMID/[0-9A-F]{10,}#', '', $ua);

		// Replace locale id by xx
		$ua = preg_replace('#(?<=[/;\[ ])(?:[A-Za-z][a-z]|haw)(?:[_-][A-Za-z]{2})?(?=[);\] ])#', 'xx', $ua);
		$ua = preg_replace('#(?<=; )[a-z]{2}-(?=;)#', 'xx', $ua); //buggy strings
		// Remove locale id
		$ua = preg_replace('#; *xx *(?=[);])#', '', $ua);
		$ua = str_replace(' [xx]', '', $ua);

		// Remove security level
		$ua = preg_replace('#; ?[UIN](?=[;)])#i', '', $ua);

		// Remove browser prefix
		$ua = preg_replace('#^(?:i|MQQ|One|Zing)Browser/\d\.?\d/(?=Mozilla/5\.0 \(Linux; Android )#', '', $ua);

		// Remove AppleWebKit and Safari version
		$ua = preg_replace('#( (?:AppleWebKit|Safari|Version|webOSBrowser|NintendoBrowser)/)[\w\./+]+#', '\1*', $ua);


		// Normalize Blackberry
		$ua = str_ireplace('blackberry', 'BlackBerry', $ua);
		if(strpos($ua, ' VendorID/') !== false)
			$ua = preg_replace('#(?<= VendorID/)(?:\d+|-1)#', '100', $ua);

		// Normalize Nokia
		$ua = str_ireplace('nokia', 'Nokia', $ua);
		// Remove Nokia build version
		if(strpos($ua, 'Nokia') !== false)
		{
			$ua = preg_replace('#(?<=^Nokia)([\w\./-]+ )\([\d\.a-z_]+\) #', '\1', $ua);

			// remove Browser versions
			$ua = preg_replace('#( BrowserNG| NokiaBrowser| Series\s?\d\d|OviBrowser|SymbianOS)/[\d\.]+(?:gpp-gba)?#', '\1/*', $ua);

			$ua = preg_replace('#((?:^|\s)Nokia[\w\./-]+)(?:/[\d\.a-z_]+)+(?= )#', '\1', $ua);
			$ua = preg_replace('#(?<=\bNokia)([\w\.-]+/\d+)\.\d{4,}(?= )#', '\1', $ua);
			$ua = preg_replace('#(?<=^Mozilla/[45]\.0 \()(.*?Nokia ?[\w\.-]+)/[\d\.a-z_]+(?=;)#', '\1', $ua);
		}

		// Remove Motorola version
		if(strpos($ua, 'Blur_Version') !== false)
			$ua = preg_replace('#(?<=/)Blur_Version\.[^ ]+(?= )#', '', $ua);
		if(substr($ua, 0, 4) === 'MOT-')
			$ua = preg_replace('#(?<=^MOT-)([\w-]+)/[\w\.]+(?= )#', '\1', $ua);

		// Remove Samsung build numbers
		if(stripos($ua, 'samsung') !== false || strpos($ua, 'GT') !== false)
			$ua = preg_replace('#((?:^|; )(?:SAMSUNG|Samsung|GT|SAMSUNG GT|SAMSUNG SM)-[\w-]+)/[\w\./-]+#', '\1', $ua);

		// Remove SonyEricsson build numbers
		if(strpos($ua, 'SonyEricsson') !== false)
			$ua = preg_replace('#(?<=SonyEricsson)([\w-]+)/[\w\./-]+#', '\1', $ua);

		// Remove Pantech build numbers
		if(substr($ua, 0, 7) === 'Pantech')
			$ua = preg_replace('#(?<=^Pantech)([\w-]+)/[\w\./-]+#', '\1', $ua);

		// Remove PlayStation Vita subversion
		if(stripos($ua, 'playstation') !== false)
		{
			$ua = preg_replace('#(?<=\(playstation \d )(\d+\.)\d+(?=\))#i', '\1*', $ua);
			$ua = preg_replace('#(?<=\(PlayStation Vita )(\d+\.)\d+(?=\))#', '\1*', $ua);
		}

		// Convert Dalvik to Mozilla header
		if(substr($ua, 0, 6) === 'Dalvik' && preg_match('#^Dalvik/[\d\.]+ (\(.*?\))$#', $ua, $match))
			$ua = "Mozilla/5.0 {$match[1]} AppleWebKit/* (KHTML, like Gecko) Version/4.0 Mobile Safari/*";

		if(strpos($ua, 'Android') !== false)
		{
			// Remove Android subversion
			$ua = preg_replace('#(?<=Android)( ?(?>\d+\.))[\w\.-]+#', '\1*', $ua);
			// Remove Cyanogen identificator
			$ua = str_replace(' (thor & digetx)', '', $ua);
			$ua = preg_replace('#; (?:CyanogenMod|CyanMobile)[\w \.-]+#', '', $ua);
			// Remove Android build version
			$ua = preg_replace('#(Android .*?) Build/[^;)]+#', '\1', $ua);
		}

		if(strpos($ua, 'Windows Phone') !== false)
		{
			// Remove WP's mimic
			$ua = str_replace('(Mobile; Windows Phone 8.1; Android 4.0; ', '(Windows Phone 8.1; ', $ua);
			$ua = str_replace('(Mobile; Windows Phone 8.1; Android 4.*; ', '(Windows Phone 8.1; ', $ua);
			if(($pos = strpos($ua, ' like iPhone OS ')) !== false)
				$ua = substr($ua, 0, $pos+12); // keep just "like iPhone"
		}

		// Remove iPhone revision version
		if(strpos($ua, ' like Mac OS X') !== false)
			$ua = preg_replace('#(?<= OS )(\d+_\d+)_\d+(?= like Mac OS X)#', '\1', $ua);
		// Remove iPhone build version
		if(strpos($ua, ' Mobile/') !== false)
			$ua = preg_replace('#( \(KHTML, like Gecko\).*? Mobile/\d{1,2})[A-Z]\d*\w\b#', '\1', $ua);


		// Remove Opera Mini/Mobile/Tablet version
		if(strpos($ua, 'Opera ') !== false)
			$ua = preg_replace('#(?<=Opera )(Mini|Mobi|Mobile|Tablet)/[^;)]+#', '\1', $ua);
		if(strpos($ua, 'OperaMini/') === 0)
			$ua = preg_replace('#(?<=OperaMini)/[\d\.]+#', '', $ua);

		// Remove Opera 15+ suffix
		if(strpos($ua, ' OPR/') !== false)
			$ua = preg_replace('#\s+OPR/\d+\.\d+$#', '', $ua);

		// Remove GSA suffix
		if(strpos($ua, ' GSA/') !== false)
			$ua = preg_replace('#\s+GSA/[\d\.]+#', '', $ua);

		// Remove Chrome for iPhone revision version
		if(strpos($ua, ' CriOS') !== false)
			$ua = preg_replace('#(?<= CriOS)/[\d\.]+#', '', $ua);
		// Remove Chrome for Android revision version
		if(strpos($ua, ' Chrome') !== false)
			$ua = preg_replace('#(?<= Chrome)/[\d\.]+#', '', $ua);

		// Remove Yandex Browser suffix
		if(strpos($ua, ' YaBrowser') !== false)
			$ua = preg_replace('# YaBrowser/[\d\.]+#', '', $ua);

		// Remove Silk version
		if(strpos($ua, ' Silk') !== false)
		{
			$ua = preg_replace('#(?<= Silk)/[\d\.]+#', '', $ua);
			$ua = preg_replace('# Silk-Accelerated=(?:true|false)#', '', $ua);
		}

		// Fennec browser
		if(strpos($ua, ' Firefox') !== false)
		{
			$ua = preg_replace('#(?<=^Mozilla/5\.0 \()([^;)]+); ([^;)]+); [^)]+\) Gecko/[\d\.]+ Firefox/[\d\.]+ Fennec/([\d\.]+).*?#', '\1; \2) Fennec/*', $ua);
			$ua = preg_replace('#(?<=^Mozilla/5\.0 \()([^;)]+); (Mobile|Tablet); [^)]+\) Gecko/[\d\.]+ Firefox/([\d\.]+).*?#', '\1; \2) Firefox/*', $ua);
			// temporary code to convert old entries
			$ua = preg_replace('#( (?:Fennec|Firefox)/)[\d\.]+$#', '\1*', $ua);
		}

		// Remove Maxthon fingerprint
		if(strpos($ua, 'Maxthon') !== false)
		{
			$ua = str_replace(')Maxthon ', ') ', $ua);
			$ua = preg_replace('# Maxthon(?:/[\d\.]+)?$#', '', $ua);
		}

		if(strpos($ua, 'Vodafone') !== false)
		{
			// Remove Vodafone/1.0/ prefix
			$ua = preg_replace('#^Vodafone/(\d+\.\d+/)?#', '', $ua);
			// Remove Vodafone suffix
			$ua = str_replace('-Vodafone ', ' ', $ua);
		}

		// Remove UCWEB/UCBrowser suffix
		if(strpos($ua, 'UC') !== false)
			$ua = preg_replace('#(?: \(|[ /]|\b)UC(?: ?Browser|WEB)/?\d.*$#', '', $ua);

		// Remove UP.Link version of Openwave WAP Gateway
		if(($pos = strpos($ua, 'UP.Link')) !== false)
			$ua = rtrim(substr($ua, 0, $pos));

		// Shrink Facebook App suffix
		if(($pos = strpos($ua, ' [FBAN')) !== false)
			$ua = substr($ua, 0, $pos);
		elseif(($pos = strpos($ua, ' [FB_IAB/')) !== false)
			$ua = substr($ua, 0, $pos);

		// Remove SVN suffix
		if(strpos($ua, ' SVN/') !== false)
			$ua = preg_replace('# SVN/\w+$#', '', $ua);

		// Remove common suffixes
		$ua = str_replace(array(
					 ' 3gpp-gba'
					,' MMS/LG-Android-MMS-V1.0'
					,' MMS/LG-Android-MMS-V1.0/V1.2)'
					,' MMS/LG-Android-MMS-V1.0/1.2)'
					,' MMS/LG-Android-MMS-V1.2'
					,' Mobitest'
					,' Twitter for iPhone'
					,' Twitter for iPad'
					,' [Pinterest/iOS]'
				), '', $ua);
		if(($pos = strpos($ua, ',gzip(gfe)')) !== false)
			$ua = substr($ua, 0, $pos);
		$ua = preg_replace('# (?:YJApp-ANDROID|Flipboard/|Mobicip/|baidubrowser/|baiduvoice/|baiduboxapp/|zhangbai/).*$#', '', $ua);

		$ua = str_ireplace(' UNTRUSTED/1.0', '', $ua);
		$ua = preg_replace('#(?:'
								.'(?: FirePHP| BingWeb|flameblur)/[\d\.]+'
								.'|; [\w\.-]+-user-\d+' // Garmin
							.')$#', '', $ua);
		if(strpos($ua, 'NAVER(') !== false)
			$ua = preg_replace('# NAVER\(inapp; [^)]+\)#', '', $ua);
		if(strpos($ua, 'CyanogenMod') !== false)
			$ua = preg_replace('# CyanogenMod/[\w\./]+$#', '', $ua);

		// Remove long numbers series
		$ua = preg_replace('#([^\d]\d+\.\d+)[_\.][\w\.-]+#', '\1', $ua);

		// Feed readers
		$ua = preg_replace('#\d+(?= (?:reader|subscriber)s?)#i', '1', $ua);
		if(strpos($ua, 'feedID: ') !== false)
			$ua = preg_replace('#(?<=feedID: )\d+#', '0', $ua);
		if(strpos($ua, ' feed-id=') !== false)
			$ua = preg_replace('#(?<= feed-id=)[a-z\d]+#', '0', $ua);

		// Beautify
		$ua = preg_replace('#(?<= ) +#', '', $ua);
		$ua = str_replace(' ;', ';', $ua);
		$ua = preg_replace('#(?<=;);+#', '', $ua);
		$ua = preg_replace('#[; ]+(?=\))#', '', $ua);

		if(preg_match('#^\W#', $ua))
			$ua = '';
		$ua = substr($ua, 0, 255);
		$ua = trim($ua);

		return $ua;
	}

	/**
	 * Check that User-Agent string corresponds to one of popular desktop browsers
	 * @static
	 * @param string $ua
	 * @return bool
	 */
	public static function isDesktop($ua)
	{
		if(empty($ua))
			return true;

		// fast check for Mobile Safari
		if(strpos($ua, ' Mobile Safari/') !== false)
			return false;

		if(strpos($ua, '</') !== false || strpos($ua, '<?php') !== false)
			return true; // spam (tags in UA)

		$windows_platforms = '(?:Windows (?:NT|Vista|XP|2000|ME|98|95|3\.)|Win ?[39])';
		$linux_platforms   = '(?:Ubuntu; ?)?X11;(?: ?Ubuntu;)? ?(?:Linux|SunOS|FreeBSD|OpenBSD|NetBSD|Arch Linux|CrOS)[ ;)]';
		$desktop_platforms = "(?:Macintosh; |(?:Windows; ?)?$windows_platforms|$linux_platforms)";

//		// test Windows Phone in desktop mode
//		if(preg_match('#^Mozilla/5\.0 \(compatible; MSIE (9|10)\.0; Windows NT[^)]* Trident/[56]\.0.* ZuneWP7#', $ua))
//			return false;

//		// test IE 5+
//		if(preg_match('#^Mozilla/[45]\.0 \(compatible; MSIE \d+\.[\dab]+; '.$windows_platforms.'#', $ua))
//		{
//			if(preg_match('#(?:Google Wireless Transcoder|PalmSource|Windows Phone 6\.5)#i', $ua))
//				return false;
//			return true;
//		}

		// test IE-based browsers for windows
		if(preg_match('#^Mozilla/\d\.\d+ \((?:compatible|Windows); (?:.*; ?)?'.$windows_platforms.'#', $ua))
		{
			if(preg_match('#(?:Google Wireless Transcoder|PalmSource|Windows Phone 6\.5)#i', $ua))
				return false;
			return true;
		}

		// test IE 10
		if(preg_match('#^Mozilla/\d\.\d+ \((?:MS)?IE \d+\.\d+.*; ?'.$windows_platforms.'#', $ua))
		{
			return true;
		}

		// test Firefox/Chrome/Safari/IE 11+
		if(preg_match('#^Mozilla/\d\.\d \('.$desktop_platforms.'#i', $ua))
		{
			if(preg_match('#(?:Maemo Browser|Novarra-Vision|Tablet browser)#', $ua))
				return false;
			return true;
		}

		// test Opera
		if(preg_match('#^Opera/\d\.\d\d? \('.$desktop_platforms.'#i', $ua))
		{
			if(preg_match('#Opera ?(?:Mini|Mobi|Tablet)#i', $ua))
				return false;
			return true;
		}

		$regexp = '#^(?:Mozilla/5\.0 \(compatible; Konqueror/\d.*\)$' // test Konqueror
					. '|AppEngine-Google|Apple-PubSub/|check_http/|curl/|ELinks/|Feedfetcher-Google;|GoogleEarth/'
					. '|HTMLParser|ia_archiver|iTunes/|Java/|Liferea/|Links |Lynx/|Microsoft Office/|NSPlayer|Outlook-Express/'
					. '|PHP|php|PycURL/|python-requests/|Python[ -]|Reeder/|Wget|WordPress|WWW\-' // wget, php, java, etc
				.')#';
		if(preg_match($regexp, $ua))
			return true;

		$regexp = '#(?: (?:AOL|America Online Browser) ' // test AOL
					.'|CFNetwork/[\d\.]+ Darwin/\d' // test iOS download library
					.'|[Dd]etector|\.NET CLR|GTB\d|GoogleToolbar'
					.'|HttpClient|HTTPClient|HttpStream|Http_Client|HTTP_Request'
					.'|crontab|libwww-perl|[Mm]onitor|multi_get|/Nutch-|WinHttp|::'
				.')#';
		if(preg_match($regexp, $ua))
			return true;

		return false;
	}

	/**
	 * Check that User-Agent string is iPhone or iPod
	 * @param string $ua
	 * @return bool
	 */
	public static function isIphone($ua)
	{
		static $iphone_list = array(
			'Mozilla/5.0 (iPod;',
			'Mozilla/5.0 (iPod touch;',
			'Mozilla/5.0 (iPhone;',
			'Apple iPhone ',
			'Mozilla/5.0 (iPhone Simulator;',
			'Mozilla/5.0 (Aspen Simulator;',
			'Mozilla/5.0 (device; CPU iPhone OS'
		);
		foreach($iphone_list as $part)
			if(strpos($ua, $part)===0)
				return true;
		return false;
	}

	public function isCompactHTML($ua)
	{
		//TODO: use imode_spec.pdf
	}

	/**
	 * Get group name for User-Agent string
	 * @static
	 * @param string $ua
	 * @return string
	 */
	public static function getGroup($ua)
	{
		static $cache = array();
		if(!isset($cache[$ua]))
			$cache[$ua] = self::_getGroup($ua);
		return $cache[$ua];
	}

	private static function _getGroup($ua)
	{
		if(empty($ua))
			return '';

		$ua_lc = strtolower($ua);

		if(strpos($ua, 'Nintendo')!==false)
			return 'nintendo';

		switch($ua_lc{0})
		{
		case '0':
			if(strpos($ua, '0Vodafone')===0)
				return 'vodafone';

			break;
		case 'a':
			if(strpos($ua, 'ACS-')===0)
				return 'nec_acs';

			if(strpos($ua_lc, 'alcatel')===0)
				return 'alcatel';

			if(strpos($ua_lc, 'amoi')===0)
				return 'amoi';

			if(strpos($ua, 'Apple')===0)
				return 'apple';

			if(strpos($ua, 'ASTRO')===0)
				return 'astro';

			if(strpos($ua, 'ASUS-')===0)
				return 'asus';

			if(strpos($ua_lc, 'audiovox')===0)
				return 'audiovox';

			break;
		case 'b':
			if(strpos($ua_lc, 'benq')===0)
				return 'benq';

			if(strpos($ua_lc, 'bird')===0)
				return 'bird';

			if(strpos($ua_lc, 'blackberry')===0)
				return 'blackberry';

			break;
		case 'c':
			if(strpos($ua, 'Casio')===0)
				return 'casio';

			if(strpos($ua_lc, 'cdm')===0)
				return 'audiovox_cdm';

			if(strpos($ua, 'Compal')===0)
				return 'compal';

			break;
		case 'd':
			if(strpos($ua, 'DoCoMo/')===0)
				return 'imode_docomo';

			break;
		case 'e':
			if(strpos($ua, 'Ericsson')===0)
				return 'ericsson';

			break;
		case 'f':
			if(strpos($ua_lc, 'fly')===0)
				return 'fly';

			break;
		case 'g':
			if(strpos($ua, 'GF-')===0)
				return 'pantech_gf';

			if(strpos($ua, 'GT-')===0)
				return 'samsung_gt';

			if(strpos($ua, 'Gradiente')===0)
				return 'gradiente';

			if(strpos($ua_lc, 'grundig')===0)
				return 'grundig';

			break;
		case 'h':
			if(strpos($ua, 'Haier')===0)
				return 'haier';

			if(strpos($ua, 'HTC')===0)
				return 'htc';

			if(strpos($ua_lc, 'huawei')===0)
				return 'huawei';

			break;
		case 'i':
			if(strpos($ua, 'i-mobile')===0)
				return 'i-mobile';

			break;
		case 'j':
			if(strpos($ua, 'J-PHONE')===0)
				return 'softbank_jphone';

			if(strpos($ua, 'jBrowser')===0)
				return 'jbrowser';

			if(strpos($ua, 'JUC')===0)
				return 'juc';

			break;
		case 'k':
			if(strpos($ua, 'Karbonn')===0)
				return 'karbonn';

			if(strpos($ua, 'KGT/')===0)
				return 'imode_nec_kgt';

			if(strpos($ua, 'KDDI')===0)
				return 'kddi';

			if(strpos($ua, 'KWC-')===0)
				return 'kyocera_kwc';
			if(strpos($ua_lc, 'kyocera')===0)
				return 'kyocera';

			break;
		case 'l':
			if(strpos($ua_lc, 'lava')===0)
				return 'lava';

			if(strpos($ua_lc, 'lenovo')===0)
				return 'lenovo';

			if(strpos($ua, 'LGE-')===0)
				return 'lg_lge';
			if(strpos($ua, 'LG')===0)
				return 'lg';
			if(strpos($ua_lc, 'lg-')===0)
				return 'lg';

			break;
		case 'm':
			if(strpos($ua, 'MERIDIAN')===0)
				return 'fly_meridian';

			if(strpos($ua_lc, 'micromax')===0)
				return 'micromax';

			if(strpos($ua, 'Mitsu')===0)
				return 'mitsubishi';

			if(strpos($ua_lc, 'moto')===0)
				return 'motorola';
			if(strpos($ua_lc, 'mot-')===0)
				return 'motorola_mot';

			if(strpos($ua, 'Mozilla/5.0 (BlackBerry; ')===0 || strpos($ua, 'Mozilla/5.0 (BB10; ')===0)
				return 'blackberry_mozilla';

			if(strpos($ua, 'Mozilla/5.0 (LG-')===0)
				return 'lg_mozilla';

			if(strpos($ua, 'Mozilla/4.0 (compatible; MSIE 6.0; KDDI-')===0)
				return 'kddi_mozilla';

			if(strpos($ua, 'Mozilla/5.0 (PlayBook; ')===0)
				return 'playbook';

			if(    strpos($ua, 'Mozilla/5.0 (Windows Phone 8.1; ARM; Trident/7.0; Touch; rv:11.0; IEMobile/11.0; ')===0
				|| strpos($ua, 'Mozilla/5.0 (Mobile; Windows Phone 8.1; Android 4.')===0
				|| strpos($ua, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows Phone 8.0; Trident/6.0; IEMobile/10.0; ')===0
				|| strpos($ua, 'Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0; ')===0
				|| strpos($ua, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows Phone OS 7.0; Trident/3.1; IEMobile/7.0; ')===0
				|| (strpos($ua, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; ')===0 && strpos($ua, ' Windows Phone 6.5') !== false)
				)
				return 'windowsphone';

			if(strpos($ua, 'Mozilla/4.0 (MobilePhone ')===0)
				return 'sanyo_mobilephone';

			if(    strpos($ua_lc, 'mozilla/5.0 (playstation ')===0
				|| strpos($ua_lc, 'mozilla/4.0 (ps2; ')===0
				|| strpos($ua_lc, 'mozilla/4.0 (psp ')===0
				)
				return 'playstation';

			if(strpos($ua, 'Mozilla/5.0 (webOS/')===0)
				return 'webos';

			if(strpos($ua, 'Mozilla/5.0 (SAMSUNG; ')===0)
				return 'samsung_mozilla';

			break;
		case 'n':
			if(strpos($ua, 'NativeOperaMini')===0)
				return 'opera_native';

			if(strpos($ua, 'NEC-')===0)
				return 'nec';

			if(strpos($ua, 'Nexian')===0)
				return 'nexian';

			break;
		case 'o':
			if(strpos($ua, 'o2imode/')===0)
				return 'imode_o2';

			if(strpos($ua, 'Opera/')===0)
			{
				if(strpos($ua, 'Opera Mobile/')!==false)
					return 'opera_mobile';
				if(strpos($ua, 'Opera Mini/')!==false)
					return 'opera_mini';
				return 'opera';
			}

			if(strpos($ua, 'OperaMini')===0)
				return 'opera_mini';

			break;
		case 'p':
			if(strpos($ua, 'Panasonic')===0)
				return 'panasonic';

			if(strpos($ua_lc, 'pantech')===0)
				return 'pantech';
			if(strpos($ua, 'PT-')===0)
				return 'pantech_pt';
			if(strpos($ua, 'PG-')===0)
				return 'pantech_pg';

			if(strpos($ua_lc, 'philips')===0)
				return 'philips';

			if(strpos($ua, 'POLARIS')===0)
				return 'lg_polaris';

			if(strpos($ua, 'portalmmm/')===0)
				return 'imode_portalmmm';

			break;
		case 'q':
			if(strpos($ua, 'QC-')===0)
				return 'kyocera_qc';

			if(strpos($ua, 'Qtek')===0)
				return 'qtek';

			break;
		case 'r':
			if(strpos($ua, 'Reksio')===0)
				return 'reksio';

			if(strpos($ua_lc, 'rim')===0)
				return 'blackberry_rim';

			if(strpos($ua, 'Rover')===0)
				return 'rover';

			break;
		case 's':
			if(strpos($ua_lc, 'sagem')===0)
				return 'sagem';

			if(strpos($ua_lc, 'sanyo')===0)
				return 'sanyo';

			if(strpos($ua_lc, 'samsung-gt')===0 || strpos($ua_lc, 'samsung gt')===0)
				return 'samsung_s_gt';
			if(strpos($ua_lc, 'samsung-sch')===0)
				return 'samsung_s_sch';
			if(strpos($ua_lc, 'samsung-sec')===0)
				return 'samsung_s_sec';
			if(strpos($ua_lc, 'samsung-sgh')===0)
				return 'samsung_s_sgh';
			if(strpos($ua_lc, 'samsung-sph')===0)
				return 'samsung_s_sph';
			if(strpos($ua_lc, 'samsungsgh')===0)
				return 'samsung_ssgh';
			if(strpos($ua_lc, 'samsung')===0)
				return 'samsung';
			if(strpos($ua_lc, 'sam')===0)
				return 'samsung_sam';
			if(strpos($ua_lc, 'sch-')===0)
				return 'samsung_sch';
			if(strpos($ua, 'SGH-')===0)
				return 'samsung_sgh';
			if(strpos($ua, 'SPH-')===0)
				return 'samsung_sph';
			if(strpos($ua, 'SEC-')===0)
				return 'samsung_sec';

			if(strpos($ua, 'Sendo')===0)
				return 'sendo';

			if(strpos($ua_lc, 'sharp')===0)
				return 'sharp';

			if(strpos($ua, 'SIE-')===0)
				return 'siemens';

			if(strpos($ua, 'SkyBee')===0)
				return 'skybee';

			if(strpos($ua_lc, 'sonyericsson')===0)
				return 'sonyericsson';

			if(strpos($ua, 'Spice')===0)
				return 'spice';

			break;
		case 't':
			if(strpos($ua, 'Telit')===0)
				return 'telit';

			if(strpos($ua, 'TSM')===0)
				return 'vitelcom_tsm';

			if(strpos($ua, 'Toshiba')===0)
				return 'toshiba';

			break;
		case 'u':
			break;
		case 'v':
			if(strpos($ua, 'Vertu')===0)
				return 'vertu';

			if(strpos($ua, 'Videocon')===0)
				return 'videocon';

			if(strpos($ua, 'Vodafone')===0)
				return 'vodafone';

			if(strpos($ua, 'VX')===0)
				return 'lg_vx';

			break;
		case 'w':
			if(strpos($ua, 'WinWAP')===0)
				return 'winwap';

			break;
		case 'x':
			break;
		case 'y':
			break;
		case 'z':
			if(strpos($ua_lc, 'zmax')===0)
				return 'zonda';

			if(strpos($ua, 'ZTE')===0)
				return 'zte';
			if(strpos($ua_lc, 'zte-')===0)
				return 'zte';

			break;
		}

		if(strpos($ua, 'Mozilla/5.0 (Linux; Android ') === 0
			&& preg_match('#Mozilla/5\.0 \(Linux; Android [^;]+; ?([^)]+)#', $ua, $match))
		{
			$model = trim($match[1]);
			$model_lc = strtolower($model);

			if(strpos($model, 'ADR')===0 || strpos($model_lc, 'pcdadr')===0)
				return 'android_htc_adr';
			if(strpos($model_lc, 'alcatel')===0)
				return 'android_alcatel';
			if(strpos($model, 'Andromax')===0 || strpos($model, 'New Andromax')===0 || strpos($model, 'Smartfren')===0)
				return 'android_smartfren';
			if(strpos($model, 'ASUS')===0 || strpos($model, 'Transformer')===0)
				return 'android_asus';

			if(strpos($model_lc, 'fly')===0 && (strlen($model)===3 || $model{3}==' ' || $model{3}=='_'))
				return 'android_fly';

			if(strpos($model, 'GFIVE')===0)
				return 'android_gfive';

			if(strpos($model, 'HTC')===0 || strpos($model, 'Desire')===0 || strpos($model, 'Sensation')===0)
				return 'android_htc';
			if(strpos($model_lc, 'huawei')===0 || strpos($model, 'HW-HUAWEI')===0)
				return 'android_huawei';

			if(strpos($model, 'iBall')===0)
				return 'android_iball';
			if(strpos($model, 'IdeaTab')===0)
				return 'android_lenovo_ideatab';
			if(strpos($model, 'i-mobile')===0)
				return 'android_imobile';

			if(strpos($model, 'Karbonn')===0)
				return 'android_karbonn';

			if(strpos($model, 'Lenovo')===0)
				return 'android_lenovo';
			if(strpos($model, 'LG')===0)
				return 'android_lg';
			if(strpos($model, 'LIFETAB')===0)
				return 'android_medion_lifetab';

			if($model{0}==='M')
			{
				if(strpos($model, 'Micromax')===0)
					return 'android_micromax';
				if(strpos($model, 'MITO')===0)
					return 'android_mito';
				if(strpos($model, 'Mobiistar')===0)
					return 'android_mobiistar';
				if(strpos($model, 'MB')===0
					|| (strpos($model, 'ME')===0 && !preg_match('#^ME\d{3}[A-Z]+$#', $model))
					|| strpos($model, 'MOT-ME')===0
					|| strpos($model, 'Moto')===0 || strpos($model, 'Milestone')===0)
					return 'android_motorola';
				if(strpos($model, 'MTC')===0)
					return 'android_mtc';
			}
			if(strpos($model_lc, 'xoom')===0 || (strpos($model, 'XT')===0 && substr($model,2,1)!=='A') || strpos($model, 'MOT-XT')===0)
				return 'android_motorolax';

			if(strpos($model, 'Nexus')===0)
				return 'android_nexus';
			if(strpos($model, 'NOOK')===0 || strpos($model, 'BNTV')===0)
				return 'android_nook';

			if(strpos($model, 'PTAB')===0)
				return 'android_polaroid';

			if($model{0}==='S' || $model{0}==='G')
			{
				if(preg_match('#^(?:SAMSUNG[ -])?(?:SCH|SHW|SPH|SHV)-#', $model))
					return 'android_samsung';
				if(preg_match('#^(?:SAMSUNG[ -])?(GT|SGH|SM)-#', $model, $s_match))
					return 'android_samsung_'.strtolower($s_match[1]);
				if(strpos($model_lc, 'sony')===0)
					return 'android_sony';
				if(strpos($model, 'Sprint')===0)
					return 'android_sprint';
			}
			if(strpos($model, 'Galaxy')===0 || preg_match('#^[iI]9\d{3}\b#', $model))
				return 'android_samsung_gt';
			if(preg_match('#^(?:[LMSW][KT]|E|U|X|R8)\d\d[aiphw]#', $model))
				return 'android_sony';

			if(strpos($model, 'T-Mobile')===0)
				return 'android_tmobile';

			if(strpos($model_lc, 'droid')===0)
				return 'android_verizon';
			if(strpos($model, 'Vodafone')===0)
				return 'android_vodafone';

			if(strpos($model, 'ZTE')===0)
				return 'android_zte';
		}
		if(strpos($ua, 'Android 3.')!==false || strpos($ua, 'Android/3.')!==false)
			return 'android3';
		if(strpos($ua, 'Android')!==false)
			return 'android';

		if(strpos($ua, 'Mozilla/5')===0)
		{
			if(strpos($ua, '(iPhone')!==false || strpos($ua, '(Aspen Simulator')!==false)
				return 'apple_iphone';
			if(strpos($ua, '(iPad')!==false)
				return 'apple_ipad';
			if(strpos($ua, '(iPod')!==false)
				return 'apple_ipod';
		}

		if(strpos($ua, 'ZuneWP7')!==false)
			return 'windowsphone_desktop';
		if(strpos($ua, 'Windows Phone OS')!==false)
			return 'windowsphone';
		if(preg_match('#\(compatible; MSIE \d\.\d+; Windows CE; #', $ua))
			return 'windowsce';

		if(strpos($ua, 'Maemo')!==false)
			return 'maemo';
		if(strpos($ua, 'Nokia')!==false)
		{
			if(strpos($ua, 'Symbian/3'))
				return 'nokias3';
			if(strpos($ua, 'Series90'))
				return 'nokia90';
			if(strpos($ua, 'Series80'))
				return 'nokia80';
			if(strpos($ua, 'Series60'))
				return 'nokia60';
			if(strpos($ua, 'Series40'))
				return 'nokia40';
			if(strpos($ua, 'Mozilla/')===0)
				return 'nokia_mozilla';
			return 'nokia';
		}

		if(strpos($ua, 'BlackBerry')!==false)
			return 'blackberry_general';

		if(strpos($ua, 'PalmOS')!==false || strpos($ua, 'Blazer')!==false)
			return 'palm';

		if(strpos($ua, 'Danger hiptop ')!==false)
			return 'hiphop';

		if(strpos($ua, 'FOMA;')!==false)
			return 'imode_foma';

		if(strpos($ua, 'SoftBank')!==false)
			return 'softbank';

		if(strpos($ua, 'UP.Browser')!==false)
			return 'upbrowser';

		if(preg_match('#(?:\baddon\b|agent\b|\bajax|archive|\bapi\b|^blitz\.io;|\bblog|bot\b|\bcatalog\b|capture|check|crawl|dddd|download|extractor|\bfeed|feed\b|fetch|index\b|livecategory|mail|manager|multi_get|news\b|\bnews|parser\b|phantomjs|\bping|ping\b|plugin\b|proxy|\brss|rss\b|ruby\b|scanner\b|search|\bseo|server|service|sitemap|slurp|spider|subscriber|test|tracker|upload|\burl|url\b|validat|\bw3c|website|xml-?rpc|www\.|yahoo|yandex)#', $ua_lc)
			|| preg_match('#^(?:[a-z0-9][a-z0-9-]{0,61}[a-z0-9]\.)+[a-z]{2,9}(?:/[\d\.]+)?$#', $ua_lc)
			|| preg_match('#\b[\w\.]+@(?:[a-z0-9][a-z0-9-]{0,61}[a-z0-9]\.)+[a-z]{2,9}\b#', $ua_lc)
			|| (strpos($ua_lc, 'http') !== false && strpos($ua_lc, 'mre') === false)
			|| (preg_match('#^(?:[A-Za-z\s\?\.-]+|[A-Za-z\?_]+|[A-Za-z\s-]+/?\d+\.[\d\.]*[A-Za-z]?)$#', $ua)
			    && !preg_match('#(?:android|brew|browser|j2me|maui|meego|mobile|nook|openwave|opera|phone|symb|tablet|trusted|uc browser|ucweb|wap)#', $ua_lc))
		)
			return 'bot';

		return '';
	}
}