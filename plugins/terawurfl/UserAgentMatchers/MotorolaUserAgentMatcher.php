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
 * @version Stable 2.1.2 $Date: 2010/05/14 15:53:02
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 */
/**
 * Provides a specific user agent matching technique
 * @package TeraWurflUserAgentMatchers
 */
class MotorolaUserAgentMatcher extends UserAgentMatcher {
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		$tolerance = 5;
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold $tolerance",LOG_INFO);
		if(self::startsWith($ua,"Mot-") || self::startsWith($ua,"MOT-") || self::startsWith($ua,"Motorola")) {
			$deviceId = $this->risMatch($ua, $tolerance);
			return $deviceId;
		}
		$deviceId = $this->ldMatch($ua,$tolerance);
		return $deviceId;
	}
	public function recoveryMatch($ua){
		$this->wurfl->toLog("Applying ".get_class($this)." Recovery Match",LOG_INFO);
		if(self::contains($ua,"MIB/2.2") || self::contains($ua,"MIB/BER2.2")){
			return "mot_mib22_generic";
		}
		return WurflConstants::$GENERIC;
	}
}
