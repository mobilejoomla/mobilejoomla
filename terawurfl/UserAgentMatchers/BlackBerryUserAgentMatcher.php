<?php
/*
 * Tera_WURFL - PHP MySQL driven WURFL
 * 
 * Tera-WURFL was written by Steve Kamerman, and is based on the
 * Java WURFL Evolution package by Luca Passani and WURFL PHP Tools by Andrea Trassati.
 * This version uses a MySQL database to store the entire WURFL file, multiple patch
 * files, and a persistent caching mechanism to provide extreme performance increases.
 * 
 * @package TeraWurfl
 * @author Steve Kamerman, stevekamerman AT gmail.com
 * @version Stable 2.0.0 $Date: 2009/11/13 23:59:59
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 * $Id: BlackBerryUserAgentMatcher.php,v 1.2 2008/03/01 00:05:25 kamermans Exp $
 * $RCSfile: BlackBerryUserAgentMatcher.php,v $
 * 
 * Based On: Java WURFL Evolution by Luca Passani
 *
 */
class BlackBerryUserAgentMatcher extends UserAgentMatcher {
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua){
		$ua = preg_replace('/^BlackBerry (\d+.*)$/','BlackBerry$1',$ua);
		$tolerance = UserAgentUtils::firstSlash($ua);
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold  $tolerance",LOG_INFO);
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
?>
