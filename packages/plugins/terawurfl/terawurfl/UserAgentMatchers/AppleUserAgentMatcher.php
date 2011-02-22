<?php
/**
 * Tera_WURFL - PHP MySQL driven WURFL
 * 
 * Tera-WURFL was written by Steve Kamerman, and is based on the
 * Java WURFL Evolution package by Luca Passani and WURFL PHP Tools by Andrea Trassati.
 * This version uses a database to store the entire WURFL file, multiple patch
 * files, and a persistent caching mechanism to provide extreme performance increases.
 * 
 * @package TeraWurflUserAgentMatchers
 * @author Steve Kamerman <stevekamerman AT gmail.com>
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 */
/**
 * Provides a specific user agent matching technique
 * @package TeraWurflUserAgentMatchers
 */
class AppleUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array(
		'apple_ipod_touch_ver1',
		'apple_ipad_ver1',
		'apple_iphone_ver1',
	);
	
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		$deviceId = '';
		
		if(self::startsWith($ua, 'Apple')){
			if(($tolerance = UserAgentUtils::ordinalIndexOf($ua,' ',3)) == -1){
				$tolerance = strlen($ua);
			}
		}else{
			$tolerance = UserAgentUtils::indexOfOrLength($ua,';',0);
		}
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold  $tolerance",LOG_INFO);
		$deviceId = $this->risMatch($ua,$tolerance);
		return $deviceId;
	}
	public function recoveryMatch($ua){
		$this->wurfl->toLog("Applying ".get_class($this)." recovery match ($ua)",LOG_INFO);
		if(self::contains($ua,'iPod')) return "apple_ipod_touch_ver1";
		if(self::contains($ua,'iPad')) return "apple_ipad_ver1";
		if(self::contains($ua,"iPhone")) return "apple_iphone_ver1";
		return WurflConstants::$GENERIC;
	}
}
