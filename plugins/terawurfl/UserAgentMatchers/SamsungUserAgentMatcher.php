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
 * Provides a specific user agent matching technique
 * @package TeraWurflUserAgentMatchers
 */
class SamsungUserAgentMatcher extends UserAgentMatcher {
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		if(self::startsWith($ua,array("SAMSUNG-","SEC-","SCH"))){
			$tolerance = UserAgentUtils::firstSlash($ua);
			$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold (first slash) $tolerance",LOG_INFO);
		}elseif(self::startsWith($ua,"Samsung") || self::startsWith($ua,"SPH") || self::startsWith($ua,"SGH")){
			$tolerance = UserAgentUtils::firstSpace($ua);
			$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold (first space) $tolerance",LOG_INFO);
		}else{
			$tolerance = UserAgentUtils::secondSlash($ua);
			$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold (second slash) $tolerance",LOG_INFO);
		}
		return $this->risMatch($ua, $tolerance);
	}
	public function recoveryMatch($ua){
		if(self::startsWith($ua,"SAMSUNG")){
			$tolerance = 8;
			return $this->ldMatch($ua,$tolerance);
		}else{
			$tolerance = UserAgentUtils::indexOfOrLength($ua,'/',strpos($ua,'Samsung'));
			return $this->risMatch($ua, $tolerance);
		}
	}
}
