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
class OperaUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array("opera","opera_7","opera_8","opera_9","opera_10");
	
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
	if(UserAgentUtils::checkIfContains($ua,"Opera/10")){
			return "opera_10";
		}elseif(UserAgentUtils::checkIfContains($ua,"Opera/9")){
			return "opera_9";
		}elseif(UserAgentUtils::checkIfContains($ua,"Opera/8")){
			return "opera_8";
		}elseif(UserAgentUtils::checkIfContains($ua,"Opera/7")){
			return "opera_7";
		}
		$tolerance = 5;
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: LD with threshold $tolerance",LOG_INFO);
		return $this->ldMatch($ua, $tolerance);
	}
	public function recoveryMatch($ua){
			return "opera";
	}
}
