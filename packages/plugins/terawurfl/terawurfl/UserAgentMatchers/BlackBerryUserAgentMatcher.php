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
class BlackBerryUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array(
		'2.' => 'blackberry_generic_ver2',
		'3.2' => 'blackberry_generic_ver3_sub2',
		'3.3' => 'blackberry_generic_ver3_sub30',
		'3.5' => 'blackberry_generic_ver3_sub50',
		'3.6' => 'blackberry_generic_ver3_sub60',
		'3.7' => 'blackberry_generic_ver3_sub70',
		'4.1' => 'blackberry_generic_ver4_sub10',
		'4.2' => 'blackberry_generic_ver4_sub20',
		'4.3' => 'blackberry_generic_ver4_sub30',
		'4.5' => 'blackberry_generic_ver4_sub50',
		'4.6' => 'blackberry_generic_ver4_sub60',
		'4.7' => 'blackberry_generic_ver4_sub70',
		'4.' => 'blackberry_generic_ver4',
		'5.' => 'blackberry_generic_ver5',
		'6.' => 'blackberry_generic_ver6',
	);
	
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua){
		if(self::startsWith($ua,"BlackBerry;")){
			$tolerance = UserAgentUtils::ordinalIndexOf($ua,';',3);
		}else{
			$tolerance = UserAgentUtils::firstSlash($ua);
		}
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold  $tolerance",LOG_INFO);
		return $this->risMatch($ua, $tolerance);
	}
	public function recoveryMatch($ua){
		// BlackBerry
		$this->wurfl->toLog("Applying ".get_class($this)." recovery match ($ua)",LOG_INFO);
		if(preg_match('#Black[Bb]erry[^/\s]+/(\d.\d)#',$ua,$matches)){
			$version = $matches[1];
			foreach(self::$constantIDs as $vercode => $deviceID){
				if(strpos($version,$vercode) !== false){
					return $deviceID;
				}
			}
		}
		return WurflConstants::$GENERIC;
	}
}
