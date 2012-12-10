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
	const ASCII_UP = '€‚ƒ„…†‡ˆ‰Š‹ŒŽ‘’“”•–—˜™š›œžŸ ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ';
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
		// Remove not ascii characters
		//$ua = preg_replace('#[^ -~]+#', '', $ua);
		$ua = strtr($ua, self::ASCII_UP, self::ASCII_QQ);
		$ua = str_replace(array("\n", "\r", "\t"), ' ', $ua);

		// Fix possible proxy bugs
		$ua = ltrim($ua, ":= ");
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

		// Replace locale id by xx
		$ua = preg_replace('#(?<=[/;\[ ])[A-Za-z][a-z](?:[_-][A-Za-z]{2})?(?=[);\] ])#', 'xx', $ua);
		$ua = preg_replace('#(?<=; )[a-z]{2}-(?=;)#', 'xx', $ua); //buggy strings
		// Remove locale id
		$ua = preg_replace('#; *xx *(?=[);])#', '', $ua);
		$ua = str_replace(' [xx]', '', $ua);

		// Remove security level
		$ua = preg_replace('#; ?[UIN](?=[;)])#i', '', $ua);

		// Remove browser prefix
		$ua = preg_replace('#^(?:i|MQQ|One|Zing)Browser/\d\.\d/(?=Mozilla/5\.0 \(Linux; Android )#', '', $ua);

		// Remove AppleWebKit and Safari subversion
		$ua = preg_replace('#( AppleWebKit/\d+| Safari/\d+)\.[\w\.]+#', '\1', $ua);


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
			$ua = preg_replace('#(?<=^Nokia)([\w\./-]+)/[\d\.a-z_]+(/[\d\.a-z_]+)?(?= )#', '\1', $ua);
			$ua = preg_replace('#(?<=^Mozilla/[45]\.0 \()(.*?Nokia ?[\w\.-]+)/[\d\.a-z_]+(?=;)#', '\1', $ua);
		}

		// Remove Motorola version
		if(strpos($ua, 'Blur_Version') !== false)
			$ua = preg_replace('#(?<=/)Blur_Version\.[^ ]+(?= )#', '', $ua);
		if(substr($ua, 0, 4) === 'MOT-')
			$ua = preg_replace('#(?<=^MOT-)([\w-]+)/[\w\.]+(?= )#', '\1', $ua);

		// Remove Samsung build numbers
		if(stripos($ua, 'samsung') !== false || strpos($ua, 'GT') !== false)
			$ua = preg_replace('#((?:^|; )(?:SAMSUNG|Samsung|GT|SAMSUNG GT)-[\w-]+)/[\w\./-]+#', '\1', $ua);

		// Remove SonyEricsson build numbers
		if(strpos($ua, 'SonyEricsson') !== false)
			$ua = preg_replace('#(?<=SonyEricsson)([\w-]+)/[\w\./-]+#', '\1', $ua);

		// Remove Pantech build numbers
		if(substr($ua, 0, 7) === 'Pantech')
			$ua = preg_replace('#(?<=^Pantech)([\w-]+)/[\w\./-]+#', '\1', $ua);


		// Convert Dalvik to Mozilla header
		if(substr($ua, 0, 6) === 'Dalvik' && preg_match('#^Dalvik/[\d\.]+ (\(.*?\))$#', $ua, $match))
		{
			preg_match('#Android (\d\.\d+)#', $match[1], $ver);
			$ver = isset($ver[1]) ? $ver[1] : '';
			if($ver>='3.0')     $ver = '534';
			elseif($ver>='2.2') $ver = '533';
			elseif($ver>='2.0') $ver = '530';
			elseif($ver>='1.5') $ver = '525';
			else                $ver = '523';
			$ua = "Mozilla/5.0 {$match[1]} AppleWebKit/$ver (KHTML, like Gecko) Version/4.0 Mobile Safari/$ver Dalvik";
		}

		if(strpos($ua, 'Android') !== false)
		{
			// Remove Android revision version
			$ua = preg_replace('#(?<=Android)( ?(?>\d+\.\d+))[\w\.-]+#', '\1', $ua);
			// Remove Cyanogen identificator
			$ua = str_replace(' (thor & digetx)', '', $ua);
			$ua = preg_replace('#; (?:CyanogenMod|CyanMobile)[\w \.-]+#', '', $ua);
			// Remove Android build version
			$ua = preg_replace('#(Android .*?) Build/[^;)]+#', '\1', $ua);
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


		// Remove Chrome for iPhone revision version
		if(strpos($ua, ' CriOS') !== false)
			$ua = preg_replace('#(?<= CriOS/)(\d+)\.[\d\.]+#', '\1', $ua);
		// Remove Chrome for Android revision version
		if(strpos($ua, ' Chrome') !== false)
			$ua = preg_replace('#(?<= Chrome/)(\d+)\.[\d\.]+#', '\1', $ua);


		// Fennec browser
		if(strpos($ua, ' Firefox'))
		{
			$ua = preg_replace('#(?<=^Mozilla/5\.0 \()([^;)]+); ([^;)]+); [^)]+\) Gecko/[\d\.]+ Firefox/[\d\.]+ Fennec/([\d\.]+).*?#', '\1; \2) Fennec/\3', $ua);
			$ua = preg_replace('#(?<=^Mozilla/5\.0 \()([^;)]+); (Mobile|Tablet); [^)]+\) Gecko/[\d\.]+ Firefox/([\d\.]+).*?#', '\1; \2) Firefox/\3', $ua);
		}

		// Remove Maxthon fingerprint
		$ua = str_replace(')Maxthon ', ') ', $ua);

		if(strpos($ua, 'Vodafone') !== false)
		{
			// Remove Vodafone/1.0/ prefix
			$ua = preg_replace('#^Vodafone/(\d+\.\d+/)?#', '', $ua);
			// Remove Vodafone suffix
			$ua = str_replace('-Vodafone ', ' ', $ua);
		}

		if(strpos($ua, 'UC') !== false)
		{
			// Remove UCBrowser suffix
			$ua = preg_replace('#/UCBrowser/[\d\./]+$#', '', $ua);
			// Remove UCBrowser/UCWEB subversion numbers
			$ua = preg_replace('#(\b(UC ?Browser|UCWEB)/?\d+)\..*$#', '\1', $ua);
		}

		// Remove UP.Link version of Openwave WAP Gateway
		if(($pos = strpos($ua, 'UP.Link')) !== false)
			$ua = rtrim(substr($ua, 0, $pos));

		// Shrink Facebook App suffix
		if(($pos = strpos($ua, ' [FBAN/')) !== false)
			$ua = substr($ua, 0, $pos) . ' [FBAN]';

		// Remove SVN suffix
		if(strpos($ua, ' SVN/') !== false)
			$ua = preg_replace('# SVN/\w+$#', '', $ua);

		// Remove common suffixes
		$ua = str_replace(array(
					 ' 3gpp-gba'
					,',gzip(gfe) (via translate.google.com)'
					,' MMS/LG-Android-MMS-V1.0'
					,' MMS/LG-Android-MMS-V1.0/V1.2)'
					,' MMS/LG-Android-MMS-V1.0/1.2)'
					,' MMS/LG-Android-MMS-V1.2'
					,' Mobitest'
					,' Twitter for iPhone'
					,' Twitter for iPad'
				), '', $ua);
		$ua = str_ireplace(' UNTRUSTED/1.0', '', $ua);
		$ua = preg_replace('#(?:'
								.'(?: FirePHP| BingWeb|flameblur)/[\d\.]+'
								.'|; [\w\.-]+-user-\d+' // Garmin
							.')$#', '', $ua);
		if(strpos($ua, 'NAVER(') !== false)
			$ua = preg_replace('# NAVER\(inapp; [^)]+\)#', '', $ua);

		// Remove long numbers series
		$ua = preg_replace('#([^\d]\d+\.\d+)[_\.][\w\.-]+#', '\1', $ua);

		// Feed readers
		$ua = preg_replace('#\d+ (reader|subscriber)s?#i', '1 \1', $ua);
		if(strpos($ua, 'feedID: ') !== false)
			$ua = preg_replace('#(?<=feedID: )\d+#', '0', $ua);

		// Beautify
		$ua = preg_replace('#(?<= ) +#', '', $ua);
		$ua = str_replace(' ;', ';', $ua);
		$ua = preg_replace('#(?<=;);+#', '', $ua);
		$ua = preg_replace('#[; ]+(?=\))#', '', $ua);

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

		if(strpos($ua, '</') !== false)
			return true; // spam (tags in UA)

		$windows_platforms = '(?:Windows (?:NT|Vista|XP|2000|ME|98|95|3\.)|Win ?[39])';
		$linux_platforms   = '(?:Ubuntu; ?)?X11;(?: ?Ubuntu;)? ?(?:Linux|SunOS|FreeBSD|OpenBSD|Arch Linux|CrOS)[ ;)]';
		$desktop_platforms = "(?:Macintosh; |(?:Windows; ?)?$windows_platforms|$linux_platforms)";

		// test Windows Phone in desktop mode
		//if(preg_match('#^Mozilla/5\.0 \(compatible; MSIE (9|10)\.0; Windows NT[^)]* Trident/[56]\.0.* ZuneWP7#', $ua))
		//	return false;

//		// test IE 5+
//		if(preg_match('#^Mozilla/[45]\.0 \(compatible; MSIE \d+\.[\dab]+; '.$windows_platforms.'#', $ua))
//		{
//			if(preg_match('#(?:Google Wireless Transcoder|PalmSource|Windows Phone 6\.5)#i', $ua))
//				return false;
//			return true;
//		}

		// test IE-based browsers for windows
		if(preg_match('#^Mozilla/\d\.\d+ \((?:compatible|Windows); .*; ?'.$windows_platforms.'#', $ua))
		{
			if(preg_match('#(?:Google Wireless Transcoder|PalmSource|Windows Phone 6\.5)#i', $ua))
				return false;
			return true;
		}

		// test Firefox/Chrome/Safari
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
					. '|AppEngine-Google|Apple-PubSub/|check_http/|curl/|Feedfetcher-Google;'
					. '|HTMLParser|ia_archiver|iTunes/|Java/|Liferea/|Lynx/|Microsoft Office/|NSPlayer|Outlook-Express/'
					. '|PHP|php|PycURL/|python-requests/|Python-urllib|Reeder/|Wget|WordPress|WWW\-' // wget, php, java, etc
				.')#';
		if(preg_match($regexp, $ua))
			return true;

		$regexp = '#(?: (?:AOL|America Online Browser) ' // test AOL
					.'|CFNetwork/[\d\.]+ Darwin/\d' // test iOS download library
					.'|\.NET CLR|GTB\d|GoogleToolbar'
					.'|HttpClient|HttpMonitor|HttpStream|Http_Client|HTTP_Request'
					.'|libwww-perl|/Nutch-|WinHttp|::'
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
			if(strpos($ua_lc, 'lenovo')===0)
				return 'lenovo';

			if(strpos($ua, 'LGE-')===0)
				return 'lg_lge';
			if(strpos($ua, 'LG')===0)
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

			if(strpos($ua, 'Mozilla/5.0 (BlackBerry; ')===0)
				return 'blackberry_mozilla';

			if(strpos($ua, 'Mozilla/5.0 (LG-')===0)
				return 'lg_mozilla';

			if(strpos($ua, 'Mozilla/5.0 (PlayBook; ')===0)
				return 'playbook';

			if(    strpos($ua, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows Phone 8.0; Trident/6.0; IEMobile/10.0; ') ===0
				|| strpos($ua, 'Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0; ')===0
				|| strpos($ua, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows Phone OS 7.0; Trident/3.1; IEMobile/7.0; ')===0
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

			if(strpos($ua_lc, 'samsung-gt')===0)
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

			if(strpos($ua, 'Vodafone')===0)
				return 'vodafone';

			if(strpos($ua_lc, 'vx')===0)
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
			if(strpos($ua, 'ZTE')===0)
				return 'zte';

			break;
		}

		if(strpos($ua, 'Mozilla/5.0 (Linux; Android ') === 0
			&& preg_match('#Mozilla/5\.0 \(Linux; Android [^;]+; ?([^)]+)#', $ua, $match))
		{
			$model = trim($match[1]);
			$model_lc = strtolower($model);

			if(strpos($model, 'ADR')===0 || strpos($model, 'pcdadr')===0)
				return 'android_htc_adr';
			if(strpos($model, 'ASUS')===0 || strpos($model, 'Transformer')===0)
				return 'android_asus';
			if(strpos($model_lc, 'alcatel')===0)
				return 'android_alcatel';
			if(strpos($model, 'Fly')===0)
				return 'android_fly';
			if(strpos($model, 'HTC')===0 || strpos($model, 'Desire')===0 || strpos($model, 'Sensation')===0)
				return 'android_htc';
			if(strpos($model_lc, 'huawei')===0 || strpos($model, 'HW-HUAWEI')===0)
				return 'android_huawei';
			if(strpos($model, 'Vodafone')===0)
				return 'android_vodafone';
			if(strpos($model, 'LG')===0)
				return 'android_lg';
			if(strpos($model, 'Lenovo')===0)
				return 'android_lenovo';
			if($model{0}==='M')
			{
				if(strpos($model, 'Micromax')===0)
					return 'android_micromax';
				if(strpos($model, 'Mobiistar')===0)
					return 'android_mobiistar';
				if(strpos($model, 'MB')===0 || strpos($model, 'ME')===0 || strpos($model, 'MOT-ME')===0 || strpos($model, 'Moto')===0 || strpos($model, 'Milestone')===0)
					return 'android_motorola';
				if(strpos($model, 'MTC')===0)
					return 'android_mtc';
			}
			if(strpos($model_lc, 'xoom')===0 || strpos($model, 'XT')===0 || strpos($model, 'MOT-XT')===0)
				return 'android_motorolax';
			if(strpos($model, 'Nexus')===0)
				return 'android_nexus';
			if(strpos($model, 'NOOK')===0 || strpos($model, 'BNTV')===0)
				return 'android_nook';
			if(strpos($model, 'Galaxy')===0 || strpos($model, 'GT-')===0 || preg_match('#^[iI]9\d{3}\b#', $model))
				return 'android_samsung_gt';
			if($model{0}==='S')
			{
				if(strpos($model, 'SCH-')===0 || strpos($model, 'SHW-')===0 || strpos($model, 'SPH-')===0 || strpos($model, 'SHV-')===0)
					return 'android_samsung';
				if(strpos($model, 'SAMSUNG GT-')===0 || strpos($model, 'SAMSUNG-GT-')===0)
					return 'android_samsung_gt';
				if(strpos($model, 'SAMSUNG-SGH-')===0 || strpos($model, 'SGH-')===0)
					return 'android_samsung_sgh';
				if(strpos($model, 'Sony')===0)
					return 'android_sony';
				if(strpos($model, 'Sprint')===0)
					return 'android_sprint';
			}
			if(preg_match('#^(?:[LMSW][KT]|E|U|X|R8)\d\d[ai]#', $model))
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

		if(preg_match('#(?:agent\b|archive|\bapi\b|bot\b|\bcatalog\b|capture|check|crawl|dddd|download|mail|extractor|\bfeed|feed\b|https?://|link|\bping|proxy|\brss|rss\b|search|\bseo|server|service|slurp|spider|subscriber|\burl|url\b|validat|\bw3c|website|yahoo|yandex)#', $ua_lc)
			|| preg_match('#^(?:[a-z0-9][a-z0-9-]{0,61}[a-z0-9]\.)+[a-z]{2,9}$#', $ua_lc))
			return 'bot';

		return '';
	}
}