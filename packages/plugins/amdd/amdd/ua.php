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

	/**
	 * Remove redundant data from User-Agent string
	 * @static
	 * @param string $ua
	 * @return string
	 */
	public static function normalize($ua)
	{
		// Fix possible proxy bugs
		$ua = preg_replace('#^((User-Agent)?[:=]\s+)+#i', '', $ua);
		$ua = trim($ua, " \t\n\r\'\"\\");

		// Remove serial numbers
		$ua = preg_replace('#([/;]SN\d{15}|[/;]SNX{15}|\[(NT|ST|TF)?\d+\])#', '', $ua);

		// Replace locale id by xx
		$ua = preg_replace('#(?<=[/;\[ ])[A-Za-z][a-z]([_-][A-Za-z]{2})?(?=[);\] ])#', 'xx', $ua);
		$ua = preg_replace('#(?<=; )[a-z]{2}-(?=;)#', 'xx', $ua); //buggy strings
		// Remove locale id
		$ua = preg_replace('#; xx(?=[);])#', '', $ua);
		$ua = str_replace(' [xx]', '', $ua);

		// Remove security level
		$ua = preg_replace('#; ?[UIN](?=[;)])#', '', $ua);


		// Remove AppleWebKit and Safari subversion
		$ua = preg_replace('#(?<=\sAppleWebKit/|\sSafari/)(\d+)\.[\w\.]+#', '\1', $ua);


		// Normalize Blackberry
		$ua = str_ireplace('blackberry', 'BlackBerry', $ua);
		$ua = preg_replace('#(?<= VendorID/)(?:\d+|-1)#', '100', $ua);

		// Normalize Nokia
		$ua = str_ireplace('nokia', 'Nokia', $ua);
		// Remove Nokia build version
		$ua = preg_replace('#(?<=^Nokia)([\w\./-]+ )\([\d\.a-z_]+\) #', '\1', $ua);
		$ua = preg_replace('#(?<=^Nokia)([\w\./-]+)/[\d\.a-z_]+(/[\d\.a-z_]+)?(?= )#', '\1', $ua);
		$ua = preg_replace('#(?<=^Mozilla/[45]\.0 \()(.*?Nokia\s?[\w\.-]+)/[\d\.a-z_]+(?=;)#', '\1', $ua);

		// Remove Motorola version
		$ua = preg_replace('#(?<=/)Blur_Version\.[^\s]+(?= )#', '', $ua);
		$ua = preg_replace('#(?<=^MOT-)([\w-]+)/[\w\.]+(?= )#', '\1', $ua);

		// Remove Samsung build numbers
		$ua = preg_replace('#((?:^|; )(?:SAMSUNG|Samsung|GT|SAMSUNG GT)-[\w-]+)/[\w\./-]+#', '\1', $ua);

		// Remove SonyEricsson build numbers
		$ua = preg_replace('#(?<=SonyEricsson)([\w-]+)/[\w\./-]+#', '\1', $ua);

		// Remove Pantech build numbers
		$ua = preg_replace('#(?<=^Pantech)([\w-]+)/[\w\./-]+#', '\1', $ua);


		// Remove Android revision version
		$ua = preg_replace('#(?<=Android)(\s?(?>\d+\.\d+))[\w\.-]+#', '\1', $ua);
		// Remove Cyanogen identificator
		$ua = str_replace(' (thor & digetx)', '', $ua);
		$ua = preg_replace('#; (?:CyanogenMod|CyanMobile)[\w\s\.-]+#', '', $ua);
		// Remove Android build version
		$ua = preg_replace('#(Android .*?) Build/[^;)]+#', '\1', $ua);


		// Remove iPhone revision version
		$ua = preg_replace('#(?<= OS )(\d+_\d+)_\d+(?= like Mac OS X)#', '\1', $ua);
		// Remove iPhone build version
		$ua = preg_replace('#( \(KHTML, like Gecko\).*? Mobile/\d{1,2})[A-Z]\d*\w\b#', '\1', $ua);


		// Remove Opera Mini/Mobile/Tablet version
		$ua = preg_replace('#(?<=Opera )(Mini|Mobi|Mobile|Tablet)/[^;)]+#', '\1', $ua);


		// Remove Chrome for iPhone revision version
		$ua = preg_replace('#(?<= CriOS/)(\d+)\.[\d\.]+#', '\1', $ua);
		// Remove Chrome for Android revision version
		$ua = preg_replace('#(?<= Chrome/)(\d+)\.[\d\.]+#', '\1', $ua);


		// Fennec browser
		if(strpos($ua, ' Firefox'))
		{
			$ua = preg_replace('#(?<=^Mozilla/5\.0 \()([^;)]+); ([^;)]+); [^)]+\) Gecko/[\d\.]+ Firefox/[\d\.]+ Fennec/([\d\.]+).*?#', '\1; \2) Fennec/\3', $ua);
			$ua = preg_replace('#(?<=^Mozilla/5\.0 \()([^;)]+); (Mobile|Tablet); [^)]+\) Gecko/[\d\.]+ Firefox/([\d\.]+).*?#', '\1; \2) Firefox/\3', $ua);
		}

		// Remove Maxthon fingerprint
		$ua = str_replace(')Maxthon ', ') ', $ua);

		// Remove Vodafone/1.0/ prefix
		$ua = preg_replace('#^Vodafone/(\d+\.\d+/)?#', '', $ua, 1);
		// Remove Vodafone suffix
		$ua = str_replace('-Vodafone ', ' ', $ua);

		// Remove UCBrowser suffix
		$ua = preg_replace('#/UCBrowser/[\d\./]+$#', '', $ua);
		// Remove UCBrowser/UCWEB subversion numbers
		$ua = preg_replace('#(\b(UC\s?Browser|UCWEB)/?\d+)\..*$#', '\1', $ua);

		// Remove UP.Link version of Openwave WAP Gateway
		$ua = preg_replace('#\sUP\.Link.*$#', '', $ua);

		// Shrink Facebook App suffix
		$ua = preg_replace('#(?<= \[FBAN)/.*$#', ']', $ua);

		// Remove common suffixes
		$ua = str_replace(' 3gpp-gba', '', $ua);
		$ua = str_ireplace(' UNTRUSTED/1.0', '', $ua);
		$ua = str_replace(',gzip(gfe) (via translate.google.com)', '', $ua);
		$ua = preg_replace('# MMS/LG-Android-MMS-(V1\.0/V?1\.2|V1\.0|V1\.2)#', '', $ua);
		$ua = preg_replace('#( BingWeb|flameblur)/[\d\.]+$#', '', $ua);
		$ua = preg_replace('#; [\w\.-]+-user-\d+$#', '', $ua); // Garmin
		$ua = preg_replace('# Twitter for (iPhone|iPad)#', '', $ua);
		$ua = str_replace(' Mobitest', '', $ua);
		$ua = preg_replace('# FirePHP/[\d\.]+$#', '', $ua);

		// Remove long numbers series
		$ua = preg_replace('#(?<=[^\d])(\d+\.\d+)[_\.][\w\.-]+#', '\1', $ua);

		// Feed readers
		$ua = preg_replace('#\d+ (reader|subscriber)s?#', '1 \1', $ua);
		$ua = preg_replace('#(?<=feedID: )\d+#', '0', $ua);

		// Beautify
		$ua = preg_replace('#(?<=\s)\s+#', '', $ua);
		$ua = str_replace(' ;', ';', $ua);
		$ua = preg_replace('#(?<=;);+#', '', $ua);
		$ua = preg_replace('#[;\s]+(?=\))#', '', $ua);
		$ua = str_replace(')AppleWebKit', ') AppleWebKit', $ua);

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

		$windows_platforms = 'Windows (?:NT|XP|2000|ME|98|95|3\.)';
		$linux_platforms   = '(?:Ubuntu; )?X11;(?: Ubuntu;)? Linux[ ;]';
		$desktop_platforms = "(?:Macintosh; |(?:Windows; )?$windows_platforms|$linux_platforms)";

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
		if(preg_match('#^Mozilla/\d\.\d+ \(compatible; .*; ?'.$windows_platforms.'#', $ua))
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
		if(preg_match('#^Opera/\d\.\d\d? \('.$desktop_platforms.'#', $ua))
			return true;

		// test Konqueror
		if(preg_match('#^Mozilla/5\.0 \(compatible; Konqueror/\d.*\)$#', $ua))
			return true;

		// test AOL
		if(preg_match('# (?:AOL|America Online Browser) #', $ua))
			return true;

		// test iOS download library
		if(preg_match('#CFNetwork/[\d\.]+ Darwin/\d#', $ua))
			return true;

		// wget, php, java, etc
		if(preg_match('#^(?:AppEngine-Google|Apple-PubSub/|curl/|Feedfetcher-Google;|iTunes/|Java/|Liferea/|Lynx/|Microsoft Office/|Outlook-Express/|PHP|php|python-requests/|Python-urllib|Reeder/|Wget|WordPress)#', $ua)
				|| preg_match('#(?:HttpClient|HttpStream|libwww-perl)#', $ua))
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

		switch(strtoupper($ua{0}))
		{
		case 'A':
			if(strpos($ua, 'ACS-')===0)
				return 'nec_acs';

			if(stripos($ua, 'alcatel')===0)
				return 'alcatel';

			if(stripos($ua, 'amoi')===0)
				return 'amoi';

			if(strpos($ua, 'Apple')===0)
				return 'apple';

			if(strpos($ua, 'ASUS-')===0)
				return 'asus';

			if(stripos($ua, 'audiovox')===0)
				return 'audiovox';

			break;
		case 'B':
			if(stripos($ua, 'benq')===0)
				return 'benq';

			if(stripos($ua, 'bird')===0)
				return 'bird';

			if(stripos($ua, 'BlackBerry')===0)
				return 'blackberry';

			break;
		case 'C':
			if(stripos($ua, 'cdm')===0)
				return 'audiovox_cdm';

			if(strpos($ua, 'Compal')===0)
				return 'compal';

			break;
		case 'D':
			if(strpos($ua, 'DoCoMo/')===0)
				return 'imode_docomo';

			break;
		case 'E':
			if(strpos($ua, 'Ericsson')===0)
				return 'ericsson';

			break;
		case 'F':
			if(stripos($ua, 'fly')===0)
				return 'fly';

			break;
		case 'G':
			if(strpos($ua, 'GF-')===0)
				return 'pantech_gf';

			if(strpos($ua, 'Gradiente')===0)
				return 'gradiente';

			if(stripos($ua, 'grundig')===0)
				return 'grundig';

			break;
		case 'H':
			if(strpos($ua, 'Haier')===0)
				return 'haier';

			if(strpos($ua, 'HTC')===0)
				return 'htc';

			if(stripos($ua, 'huawei')===0)
				return 'huawei';

			break;
		case 'I':
			if(strpos($ua, 'i-mobile')===0)
				return 'i-mobile';

			break;
		case 'J':
			if(strpos($ua, 'J-PHONE')===0)
				return 'softbank_jphone';

			if(strpos($ua, 'jBrowser')===0)
				return 'jbrowser';

			break;
		case 'K':
			if(strpos($ua, 'KGT/')===0)
				return 'imode_nec_kgt';

			if(strpos($ua, 'KDDI')===0)
				return 'kddi';

			if(strpos($ua, 'KWC-')===0)
				return 'kyocera_kwc';
			if(stripos($ua, 'kyocera')===0)
				return 'kyocera';

			break;
		case 'L':
			if(stripos($ua, 'lenovo')===0)
				return 'lenovo';

			if(strpos($ua, 'LGE-')===0)
				return 'lg_lge';
			if(strpos($ua, 'LG')===0)
				return 'lg';

			break;
		case 'M':
			if(strpos($ua, 'MERIDIAN')===0)
				return 'fly_meridian';

			if(strpos($ua, 'Mitsu')===0)
				return 'mitsubishi';

			if(stripos($ua, 'moto')===0)
				return 'motorola';
			if(stripos($ua, 'mot-')===0)
				return 'motorola_mot';

			if(strpos($ua, 'Mozilla/5.0 (BlackBerry; ')===0)
				return 'blackberry_mozilla';

			if(strpos($ua, 'Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0; ')===0)
				return 'windowsphone';

			if(strpos($ua, 'Mozilla/4.0 (MobilePhone ')===0)
				return 'sanyo_mobilephone';

			if(strpos($ua, 'Mozilla/5.0 (webOS/')===0)
				return 'webos';

			break;
		case 'N':
			if(strpos($ua, 'NEC-')===0)
				return 'nec';

			break;
		case 'O':
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

			break;
		case 'P':
			if(strpos($ua, 'Panasonic')===0)
				return 'panasonic';

			if(stripos($ua, 'pantech')===0)
				return 'pantech';
			if(strpos($ua, 'PT-')===0)
				return 'pantech_pt';
			if(strpos($ua, 'PG-')===0)
				return 'pantech_pg';

			if(stripos($ua, 'philips')===0)
				return 'philips';

			if(strpos($ua, 'POLARIS')===0)
				return 'lg_polaris';

			if(strpos($ua, 'portalmmm/')===0)
				return 'imode_portalmmm';

			break;
		case 'Q':
			if(strpos($ua, 'QC-')===0)
				return 'kyocera_qc';

			if(strpos($ua, 'Qtek')===0)
				return 'qtek';

			break;
		case 'R':
			if(stripos($ua, 'rim')===0)
				return 'blackberry_rim';

			if(strpos($ua, 'Rover')===0)
				return 'rover';

			break;
		case 'S':
			if(stripos($ua, 'Sagem')===0)
				return 'sagem';

			if(stripos($ua, 'Sanyo')===0)
				return 'sanyo';

			if(stripos($ua, 'SAMSUNG-GT')===0)
				return 'samsung_s_gt';
			if(stripos($ua, 'SAMSUNG-SCH')===0)
				return 'samsung_s_sch';
			if(stripos($ua, 'SAMSUNG-SEC')===0)
				return 'samsung_s_sec';
			if(stripos($ua, 'SAMSUNG-SGH')===0)
				return 'samsung_s_sgh';
			if(stripos($ua, 'Samsung-SPH')===0)
				return 'samsung_s_sph';
			if(stripos($ua, 'SamsungSGH')===0)
				return 'samsung_ssgh';
			if(stripos($ua, 'SAMSUNG')===0)
				return 'samsung';
			if(stripos($ua, 'sam')===0)
				return 'samsung_sam';
			if(stripos($ua, 'SCH-')===0)
				return 'samsung_sch';
			if(strpos($ua, 'SGH-')===0)
				return 'samsung_sgh';
			if(strpos($ua, 'SPH-')===0)
				return 'samsung_sph';
			if(strpos($ua, 'SEC-')===0)
				return 'samsung_sec';

			if(strpos($ua, 'Sendo')===0)
				return 'sendo';

			if(stripos($ua, 'Sharp')===0)
				return 'sharp';

			if(strpos($ua, 'SIE-')===0)
				return 'siemens';

			if(stripos($ua, 'SonyEricsson')===0)
				return 'sonyericsson';

			if(strpos($ua, 'Spice')===0)
				return 'spice';

			break;
		case 'T':
			if(strpos($ua, 'Telit')===0)
				return 'telit';

			if(strpos($ua, 'TSM')===0)
				return 'vitelcom_tsm';

			if(strpos($ua, 'Toshiba')===0)
				return 'toshiba';

			break;
		case 'U':
			break;
		case 'V':
			if(strpos($ua, 'Vertu')===0)
				return 'vertu';

			if(strpos($ua, 'Vodafone')===0)
				return 'vodafone';

			if(stripos($ua, 'vx')===0)
				return 'lg_vx';

			break;
		case 'W':
			if(strpos($ua, 'WinWAP')===0)
				return 'winwap';

			break;
		case 'X':
			break;
		case 'Y':
			break;
		case 'Z':
			if(strpos($ua, 'ZTE')===0)
				return 'zte';

			break;
		}

		if(preg_match('#Mozilla/5\.0 \(Linux; Android [^;]+; xx; ([^)]+?) Build/#', $ua, $match))
		{
			$model = $match[1];
			if(preg_match('#^alcatel#i', $model))
				return 'android_alcatel';
			if(preg_match('#^(HTC|Desire)#', $model))
				return 'android_htc';
			if(preg_match('#^LG#', $model))
				return 'android_lg';
			if(preg_match('#^(MB|ME|Moto|Milestone)#', $model))
				return 'android_motorola';
			if(preg_match('#^(Xoom|XT)#', $model))
				return 'android_motorolax';
			if(preg_match('#^Nexus#', $model))//todo: test "Nexus One" and "Nexus S"
				return 'android_nexus';
			if(preg_match('#^(SCH-|SHW-|SPH-)#', $model))
				return 'android_samsung';
			if(preg_match('#^(Galaxy|GT-|SAMSUNG GT-)#', $model))
				return 'android_samsung_gt';
			if(preg_match('#^(SAMSUNG-SGH-|SGH-)#', $model))
				return 'android_samsung_sgh';
			if(preg_match('#^SonyEricsson#', $model))
				return 'android_sonyericsson';
			if(preg_match('#^T-Mobile#', $model))
				return 'android_tmobile';
			if(preg_match('#^droid#i', $model))
				return 'android_verizon';
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

		if(preg_match('#(?:bot\b|crawler|search|slurp|spider|yahoo|https?://|download)#i', $ua))
			return 'bot';

		return '';
	}
}