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
class AndroidUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array(
		'generic_android',
		'generic_android_ver1_5',
		'generic_android_ver1_6',
		'generic_android_ver2',
		'generic_android_ver2_1',
		'generic_android_ver2_2',
	);
	
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		$tolerance = UserAgentUtils::indexOfOrLength($ua,' Build/', 0);
		if($tolerance == strlen($ua))
			$tolerance = UserAgentUtils::indexOfOrLength($ua,')', 0);
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold $tolerance",LOG_INFO);
		return $this->risMatch($ua, $tolerance);
	}
	public function recoveryMatch($ua){
		if(UserAgentUtils::checkIfContains($ua, 'Froyo')){
			return 'generic_android_ver2_2';
		}
		if(preg_match('#Android[\s/](\d).(\d)#',$ua,$matches)){
			$version = 'generic_android_ver'.$matches[1].'_'.$matches[2];
			if($version == 'generic_android_ver2_0') $version = 'generic_android_ver2';
			if(in_array($version,self::$constantIDs)){
				return $version;
			}
		}
		return 'generic_android';
	}
}
