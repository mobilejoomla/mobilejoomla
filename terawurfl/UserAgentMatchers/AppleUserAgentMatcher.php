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
 * @version Stable Stable 2.1.1 $Date: 2010/03/01 15:40:10
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 */
/**
 * Provides a specific user agent matching technique
 * @package TeraWurflUserAgentMatchers
 */
class AppleUserAgentMatcher extends UserAgentMatcher {
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		$deviceId = '';
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: LD",LOG_INFO);
		$deviceId = $this->ldMatch($ua);
		return $deviceId;
	}
	/**
	 * if the UA contains "iPhone" return "apple_iphone_ver1" if the UA contains
	 * "iPod" return "apple_ipod_touch_ver1"
	 */
	public function recoveryMatch($ua){
		if(self::contains($ua,"iPhone OS 1") || self::contains($ua,"iPhone 1")){
			return "apple_iphone_ver1";
		}
		if(self::contains($ua,"iPhone OS 2") || self::contains($ua,"iPhone 2")){
			return "apple_iphone_ver2";
		}
		if(self::contains($ua,"iPhone OS 3") || self::contains($ua,"iPhone 3")){
			return "apple_iphone_ver3";
		}
		if(self::contains($ua,"iPhone")){
			return "apple_iphone_ver1";
		}
		return "apple_ipod_touch_ver1";
	}
}
