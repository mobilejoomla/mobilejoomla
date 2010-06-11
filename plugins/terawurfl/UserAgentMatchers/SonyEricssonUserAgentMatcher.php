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
class SonyEricssonUserAgentMatcher extends UserAgentMatcher {
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		// firstSlash() - 1 because some UAs have revisions that aren't getting detected properly:
		// SonyEricssonW995a/R1FA Browser/NetFront/3.4 Profile/MIDP-2.1 Configuration/CLDC-1.1 JavaPlatform/JP-8.4.3
		$tolerance = UserAgentUtils::firstSlash($ua) - 1;
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold $tolerance",LOG_INFO);
		if(self::startsWith($ua,"SonyEricsson")){
			return $this->risMatch($ua, $tolerance);
		}
		$tolerance = UserAgentUtils::secondSlash($ua);
		return $this->risMatch($ua, $tolerance);
	}
	public function recoveryMatch($ua){
		$tolerance = 14;
		return $this->risMatch($ua, $tolerance);
	}
}
