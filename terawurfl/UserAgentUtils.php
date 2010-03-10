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
 * $Id: UserAgentUtils.php,v 1.4 2008/03/01 00:05:25 kamermans Exp $
 * $RCSfile: UserAgentUtils.php,v $
 * 
 * Based On: Java WURFL Evolution by Luca Passani
 *
 */
class UserAgentUtils{
	
	public static $WORST_MATCH = 7;
	
	public function __construct(){
		
	}
	/**
	 * Find the matching Device ID for a given User Agent using RIS (Reduction in String) 
	 * @param string $ua User Agent
	 * @param int $tolerance How short the strings are allowed to get before a match is abandoned
	 * @param UserAgentMatcher $matcher The UserAgentMatcherInstance that is matching the User Agent
	 * @return string WURFL ID
	 */
	public static function risMatch($ua,$tolerance,UserAgentMatcher $matcher){
		// PHP RIS Function
		$devices =& $matcher->deviceList;
		// Exact match
		$key = array_search($ua,$devices);
		if($key !== false){
			return $key;
		}
		// Narrow results to those that match the tolerance level
		$curlen = strlen($ua);
		while($curlen >= $tolerance){
			foreach($devices as $testID => $testUA){
				// Comparing substrings may be faster, but you would need to use strcmp() on the subs anyway,
				// so this is probably the fastest - maybe preg /^$test/ would be faster???
				//echo "testUA: $testUA, ua: $ua\n<br/>";
				if(strpos($testUA,$ua) === 0){
					return $testID;
				}
			}
			$ua = substr($ua,0,strlen($ua)-1);
			$curlen = strlen($ua);
        }
        return WurflConstants::$GENERIC;
	}
	/**
	 * Find the matching Device ID for a given User Agent using LD (Leveshtein Distance)
	 * @param string $ua User Agent
	 * @param int $tolerance Tolerance that is still considered a match
	 * @param UserAgentMatcher $matcher The UserAgentMatcherInstance that is matching the User Agent
	 * @return string WURFL IDe
	 */
	public static function ldMatch($ua,$tolerance=null,$matcher){
		// PHP Leveshtein Distance Function
		if(is_null($tolerance)){
			$tolerance = self::$WORST_MATCH;
		}
		$devices =& $matcher->deviceList;
		$key = array_search($ua,$devices);
		if($key !== false){
			return $key;
		}
		$best = $tolerance;
		$current = 0;
		$match = WurflConstants::$GENERIC;
		foreach($devices as $testID => $testUA){
			$current = levenshtein($ua,$testUA);
			//echo "<hr/>$ua<br/>$testUA<br/>LD: $current<br/>";
			if($current <= $best){
				$best = $current;
				$match = $testID;
			}
		}
		return $match;
	}
	public static function directMatch($ua){
		// Call MySQL SELECT
	}
	public static function numSlashes($userAgent){
		return substr_count($userAgent,'/');
	}
	public static function firstSlash($userAgent){
		$position = strpos($userAgent,'/');
		return ($position!==false)? $position: strlen($userAgent);
	}
	public static function secondSlash($userAgent){
		$first = strpos($userAgent,'/');
		$first++;
		$position = strpos($userAgent,'/',$first);
		return ($position!==false)? $position: strlen($userAgent);
	}
	public static function firstSpace($userAgent){
		$position = strpos($userAgent,' ');
		return ($position!==false)? $position: strlen($userAgent);
	}
	public static function firstOpenParen($userAgent){
		$position = strpos($userAgent,'(');
		return ($position!==false)? $position: strlen($userAgent);
	}
	public static function removeUPLinkFromUA($ua){
		$index = strpos($ua,'UP.Link');
		if($index===false){
			return $ua;
		}else{
			// Return the UA up to the UP.Link/xxxxxx part
			return substr($ua,0,$index);
		}
	}
	public static function checkIfContains($ua,$find){
    	if(is_array($find)){
    		foreach($find as $part){
    			if(strpos($ua,$part)!==false){
    				return true;
    			}
    		}
    		return false;
    	}else{
	    	return (strpos($ua,$find)!==false);
    	}
    }
	public static function lastAttempts($ua){
		//before we give up and return generic, one last
		//attempt to catch well-behaved Nokia and Openwave browsers!
		if(self::checkIfContains($ua,'UP.Browser/7'))
			return 'opwv_v7_generic';
		if(self::checkIfContains($ua,'UP.Browser/6'))
			return 'opwv_v6_generic';
		if(self::checkIfContains($ua,'UP.Browser/5'))
			return 'upgui_generic';
		if(self::checkIfContains($ua,'UP.Browser/4'))
			return 'uptext_generic';
		if(self::checkIfContains($ua,'UP.Browser/3'))
			return 'uptext_generic';
		if(self::checkIfContains($ua,'Series60'))
			return 'nokia_generic_series60';
		if(self::checkIfContains($ua,'Mozilla/4.0'))
			return 'generic_web_browser';
		if(self::checkIfContains($ua,'Mozilla/5.0'))
			return 'generic_web_browser';
		if(self::checkIfContains($ua,'Mozilla/6.0'))
			return 'generic_web_browser';
		
		return self::$GENERIC;
	}
	public static function isMobileBrowser($ua){
		$lowerua = strtolower($ua);
		if(self::isDesktopBrowser($ua)){
			return false;
		}
		foreach(WurflConstants::$MOBILE_BROWSERS as $browser_signature){
			if(strpos($lowerua, $browser_signature) !== false){
				return true;
			}
		}
		if(UserAgentMatcher::regexContains($ua,'/[^\d]\d{3}x\d{3}/')){
			return true;
		}
		return false;
	}
	public static function isDesktopBrowser($ua){
		$lowerua = strtolower($ua);
		foreach(WurflConstants::$DESKTOP_BROWSERS as $browser_signature){
			if(strpos($lowerua, $browser_signature) !== false){
				return true;
			}
		}
	}
	public static function indexOfOrLength($ua, $target, $startingIndex) {
		$length = strlen($ua);
		if($startingIndex === false) {
			return $length;
		}
		$pos = strpos($ua, $target, $startingIndex);
		return ($pos === false)? $length : $pos;
	}
	public static function ordinalIndexOf($ua, $needle, $ordinal) {
		if (is_null($ua) || empty($ua) || !is_integer($ordinal)){
			return -1;
		}
		$found = 0;
		$index = -1;
		do{
			$index = strpos($ua, $needle, $index + 1);
			$index = is_int($index)? $index: -1;
			if ($index < 0) {
				return $index;
			}
			$found++;
		}while($found < $ordinal);
		return $index;
	
	}
	public static function isRobot($ua){
		$lowerua = strtolower($ua);
		foreach(WurflConstants::$ROBOTS as $browser_signature){
			if(strstr($lowerua, $browser_signature)){
				return true;
			}
		}
		return false;
	}
	static function LD($s,$t){
		return levenshtein($s,$t);
	}
}
?>