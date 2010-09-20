<?php
/**
 * Tera_WURFL - PHP MySQL driven WURFL
 * 
 * Tera-WURFL was written by Steve Kamerman, and is based on the
 * Java WURFL Evolution package by Luca Passani and WURFL PHP Tools by Andrea Trassati.
 * This version uses a MySQL database to store the entire WURFL file, multiple patch
 * files, and a persistent caching mechanism to provide extreme performance increases.
 * 
 * @package TeraWurflUserAgentMatchers
 * @author Steve Kamerman <stevekamerman AT gmail.com>
 * @version Stable 2.1.3 $Date: 2010/09/18 15:43:21
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 */
/**
 * Provides a generic user agent matching technique
 * @package TeraWurflUserAgentMatchers
 */
class CatchAllUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array(
		"opwv_v72_generic",
		"opwv_v7_generic",
		"opwv_v62_generic",
		"opwv_v6_generic",
		"upgui_generic",
		"uptext_generic",
		"nokia_generic_series60",
		"generic_netfront_ver3",
		"generic_netfront_ver3_1",
		"generic_netfront_ver3_2",
		"generic_netfront_ver3_3",
		"generic_netfront_ver3_4",
		"generic_netfront_ver3_5",
		"docomo_generic_jap_ver1",
	);
	public $matcher;
	public $match_type;
	public $match = false;
	
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
		$this->matcher = $this->matcherName();
	}
	public function applyConclusiveMatch($ua) {
		$this->match_type = "conclusive";
		$tolerance = UserAgentUtils::firstSlash($ua);
		if(self::startsWith($ua,"Mozilla")){
			$tolerance = 5;
			$this->wurfl->toLog("Applying CatchAll Conclusive Match: LD $tolerance, UA:\n$ua",LOG_INFO);
			$deviceID = $this->ldMatch($ua,$tolerance);
			if($deviceID != WurflConstants::$GENERIC) $this->match = true;
			return $deviceID;
		}
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold $tolerance",LOG_INFO);
		$deviceID = $this->risMatch($ua, $tolerance);
		if($deviceID != WurflConstants::$GENERIC) $this->match = true;
		return $deviceID;
	}
	public function recoveryMatch($ua){
		// At this point, a recovery match is really no match at all.
		$this->match_type = "none";
		$this->wurfl->toLog("Applying CatchAll Recovery Match",LOG_INFO);
		$this->match = false;
		if(SimpleDesktopUserAgentMatcher::isDesktopBrowser($ua)) return WurflConstants::$GENERIC_WEB_BROWSER;
		//Openwave
		if (self::contains($ua,"UP.Browser/7.2")){
			return "opwv_v72_generic";
		}
		if (self::contains($ua,"UP.Browser/7")){
			return "opwv_v7_generic";
		}
		if (self::contains($ua,"UP.Browser/6.2")){
			return "opwv_v62_generic";
		}
		if (self::contains($ua,"UP.Browser/6")){
			return "opwv_v6_generic";
		}
		if (self::contains($ua,"UP.Browser/5")){
			return "upgui_generic";
		}
		if (self::contains($ua,"UP.Browser/4")){
			return "uptext_generic";
		}
		if (self::contains($ua,"UP.Browser/3")){
			return "uptext_generic";
		}
		
		//Series 60
		if (self::contains($ua,"Series60")){
			return "nokia_generic_series60";
		}
		
		// Access/Net Front
		if(self::contains($ua,"NetFront/3.0")|| self::contains($ua,"ACS-NF/3.0")){
			return "generic_netfront_ver3";
		}
		if(self::contains($ua,"NetFront/3.1")|| self::contains($ua,"ACS-NF/3.1")){
			return "generic_netfront_ver3_1";
		}
		if(self::contains($ua,"NetFront/3.2") || self::contains($ua,"ACS-NF/3.2")){
			return "generic_netfront_ver3_2";
		}
		if(self::contains($ua,"NetFront/3.3") || self::contains($ua,"ACS-NF/3.3")){
			return "generic_netfront_ver3_3";
		}
		if(self::contains($ua,"NetFront/3.4")){
			return "generic_netfront_ver3_4";
		}
		if(self::contains($ua,"NetFront/3.5")){
			return "generic_netfront_ver3_5";
		}
		
		// Contains Mozilla/, but not at the beginning of the UA
		// ie: MOTORAZR V8/R601_G_80.41.17R Mozilla/4.0 (compatible; MSIE 6.0 Linux; MOTORAZR V88.50) Profile/MIDP-2.0 Configuration/CLDC-1.1 Opera 8.50[zh]
		if(!self::startsWith($ua,"Mozilla/") && self::contains($ua,"Mozilla/")){
			return WurflConstants::$GENERIC_XHTML;
		}
		
		
		if(self::contains($ua,array("ObigoInternetBrowser/Q03C","AU-MIC/2","AU-MIC-","AU-OBIGO/","Obigo/Q03","Obigo/Q04","ObigoInternetBrowser/2","Teleca Q03B1"))){
			return WurflConstants::$GENERIC_XHTML;
		}
		
		
		// DoCoMo
		if(self::startsWith($ua,"DoCoMo") || self::startsWith($ua,"KDDI")){
			return "docomo_generic_jap_ver1";
		}
		return WurflConstants::$GENERIC;
	}
	
}
