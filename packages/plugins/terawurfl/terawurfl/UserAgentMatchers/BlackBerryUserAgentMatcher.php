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
class BlackBerryUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array(
		"blackberry_generic_ver2",
		"blackberry_generic_ver3_sub2",
		"blackberry_generic_ver3_sub30",
		"blackberry_generic_ver3_sub50",
		"blackberry_generic_ver3_sub60",
		"blackberry_generic_ver3_sub70",
		"blackberry_generic_ver4",
	);
	
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua){
		$ua = preg_replace('/^BlackBerry (\d+.*)$/','BlackBerry$1',$ua);
		$tolerance = UserAgentUtils::firstSlash($ua);
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold  $tolerance",LOG_INFO);
		//TODO: evaluate RIS vs. LD for this matcher
		return $this->risMatch($ua, $tolerance);
	}
	public function recoveryMatch($ua){
        // BlackBerry
        $ua = preg_replace('/^BlackBerry (\d+.*)$/','BlackBerry$1',$ua);
        $this->wurfl->toLog("Applying ".get_class($this)." recovery match ($ua)",LOG_INFO);
        if(self::startsWith($ua,"BlackBerry")){
            $position = UserAgentUtils::firstSlash($ua);
            if($position !== false && ($position + 4) <= strlen($ua)){
                $version = substr($ua,$position+1,$position+4);
                $this->wurfl->toLog("BlackBerry version substring is $version",LOG_INFO);
                if (self::startsWith($version,"2.")) {
                    return "blackberry_generic_ver2";
                }
                if (self::startsWith($version,"3.2")) {
                    return "blackberry_generic_ver3_sub2";
                }
                if (self::startsWith($version,"3.3")) {
                    return "blackberry_generic_ver3_sub30";
                }
                if (self::startsWith($version,"3.5")) {
                    return "blackberry_generic_ver3_sub50";
                }
                if (self::startsWith($version,"3.6")) {
                    return "blackberry_generic_ver3_sub60";
                }
                if (self::startsWith($version,"3.7")) {
                    return "blackberry_generic_ver3_sub70";
                }
                if (self::startsWith($version,"4.")) {
                    return "blackberry_generic_ver4";
                }
                $this->wurfl->toLog("No version matched, User-Agent: $ua version: $version",LOG_INFO);
            }   
        }     
        return WurflConstants::$GENERIC;
    }
}

