<?php
/**
 * Tera_WURFL - PHP MySQL driven WURFL
 * 
 * Tera-WURFL was written by Steve Kamerman, and is based on the
 * Java WURFL Evolution package by Luca Passani and WURFL PHP Tools by Andrea Trassati.
 * This version uses a MySQL database to store the entire WURFL file, multiple patch
 * files, and a persistent caching mechanism to provide extreme performance increases.
 * 
 * @package TeraWurfl
 * @author Steve Kamerman <stevekamerman AT gmail.com>
 * @version Stable 2.1.3 $Date: 2010/07/29 20:36:29
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 */
/**
 * Provides static functions for working with User Agents
 * @package TeraWurfl
 *
 */
class UserAgentUtils{
	
	public static $WORST_MATCH = 7;
	
	public function __construct(){
		
	}
	/**
	 * Find the matching Device ID for a given User Agent using RIS (Reduction in String) 
	 * @param string User Agent
	 * @param int How short the strings are allowed to get before a match is abandoned
	 * @param UserAgentMatcher The UserAgentMatcher instance that is matching the User Agent
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
	 * @param string User Agent
	 * @param int Tolerance that is still considered a match
	 * @param UserAgentMatcher The UserAgentMatcher instance that is matching the User Agent
	 * @return string WURFL ID
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
	/**
	 * Number of slashes ('/') found in the given user agent
	 * @param String User Agent
	 * @return int Count
	 */
	public static function numSlashes($userAgent){
		return substr_count($userAgent,'/');
	}
	/**
	 * The character position of the first slash.  If there are no slashes, returns string length
	 * @param String User Agent
	 * @return int Character position
	 */
	public static function firstSlash($userAgent){
		$position = strpos($userAgent,'/');
		return ($position!==false)? $position: strlen($userAgent);
	}
	/**
	 * The character position of the second slash.  If there is no second slash, returns string length
	 * @param String User Agent
	 * @return int Character position
	 */
	public static function secondSlash($userAgent){
		$first = strpos($userAgent,'/');
		$first++;
		$position = strpos($userAgent,'/',$first);
		return ($position!==false)? $position: strlen($userAgent);
	}
	/**
	 * The character position of the first space.  If there are no spaces, returns string length
	 * @param String User Agent
	 * @return int Character position
	 */
	public static function firstSpace($userAgent){
		$position = strpos($userAgent,' ');
		return ($position!==false)? $position: strlen($userAgent);
	}
	/**
	 * The character position of the first open parenthisis.  If there are no open parenthisis, returns string length
	 * @param String User Agent
	 * @return int Character position
	 */
	
	public static function firstOpenParen($userAgent){
		$position = strpos($userAgent,'(');
		return ($position!==false)? $position: strlen($userAgent);
	}
	/**
	 * Removes garbage from user agent string
	 * @param String User agent
	 * @return String User agent
	 */
	public static function cleanUserAgent($ua){
		$ua = self::removeUPLinkFromUA($ua);
		// Remove serial number
		$ua = preg_replace('/\/SN\d{15}/','/SNXXXXXXXXXXXXXXX',$ua);
		// Remove locale identifier
		$ua = preg_replace('/([ ;])[a-zA-Z]{2}-[a-zA-Z]{2}([ ;\)])/','$1xx-xx$2',$ua);
		$ua = self::normalizeBlackberry($ua);
		$ua = rtrim($ua);
		return $ua;
	}
	/**
	 * Normalizes BlackBerry user agent strings
	 * @param String User agent
	 * @return String User agent
	 */
	public static function normalizeBlackberry($ua){
		$pos = strpos($ua,'BlackBerry');
		if($pos !== false && $pos > 0) $ua = substr($ua,$pos);
		return $ua;
	}
	/**
	 * Removes UP.Link traces from user agent strings
	 * @param String User agent
	 * @return String User agent
	 */
	public static function removeUPLinkFromUA($ua){
		// Remove the gateway signatures from UA (UP.Link/x.x.x)
		$index = strpos($ua,'UP.Link');
		if($index===false){
			return $ua;
		}else{
			// Return the UA up to the UP.Link/xxxxxx part
			return substr($ua,0,$index);
		}
	}
	/**
	 * Removes Vodafone garbage from user agent string
	 * @param String User agent
	 * @return String User agent
	 */
	public static function removeVodafonePrefix($ua){
		return preg_replace('/^Vodafone\/(\d\.\d\/)?/','',$ua,1);
	}
	/**
	 * Check if user agent contains string or array of strings
	 * @param String User agent
	 * @param Mixed String or Array of strings
	 * @return Bool
	 */
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
    /**
     * Returns the character position (index) of the target string in the given user agent, starting from a given index.  If target is not in user agent, returns length of user agent.
     * @param String User agent
     * @param String Target string to search for
     * @param int Character postition in the user agent at which to start looking for the target
     * @return int Character position (index) or user agent length
     */
	public static function indexOfOrLength($ua, $target, $startingIndex) {
		$length = strlen($ua);
		if($startingIndex === false) {
			return $length;
		}
		$pos = strpos($ua, $target, $startingIndex);
		return ($pos === false)? $length : $pos;
	}
	/**
	 * The character postition of the Nth occurance of a target string in a user agent
	 * @param String User agent
	 * @param String Target string to search for in user agent
	 * @param int The Nth occurence to find
	 * @return int Character position
	 */
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
    /**
     * Checks for traces of mobile device signatures and returns an appropriate generic WURFL Device ID
     * @param String User agent
     * @return String WURFL ID
     */
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
		
		return WurflConstants::$GENERIC;
	}
	/**
	 * The given user agent is definitely from a mobile device
	 * @param String User agent
	 * @return Bool
	 */
	public static function isMobileBrowser($ua){
		$lowerua = strtolower($ua);
		if(self::isDesktopBrowser($ua)){
			return false;
		}
		if(UserAgentMatcher::contains($lowerua,WurflConstants::$MOBILE_BROWSERS)) return true;
		if(UserAgentMatcher::regexContains($ua,array(
				// ARM Processor
				'/armv[5-9][l0-9]/',
				// Screen resolution in UA
				'/[^\d]\d{3}x\d{3}/'
			)
		)){
			return true;
		}
		return false;
	}
	/**
	 * The given user agent is definitely from a desktop browser
	 * @param String User agent
	 * @return Bool
	 */
	public static function isDesktopBrowser($ua){
		$lowerua = strtolower($ua);
		foreach(WurflConstants::$DESKTOP_BROWSERS as $browser_signature){
			if(strpos($lowerua, $browser_signature) !== false){
				return true;
			}
		}
	}
	/**
	 * The given user agent is definitely from a bot/crawler
	 * @param String User agent
	 * @return Bool
	 */
	public static function isRobot($ua){
		$lowerua = strtolower($ua);
		foreach(WurflConstants::$ROBOTS as $browser_signature){
			if(strstr($lowerua, $browser_signature)){
				return true;
			}
		}
		return false;
	}
	public static function LD($s,$t){
		// PHP's levenshtein() function requires arguments to be <= 255 chars
		if(strlen($s) > 255 || strlen($t) > 255){
			return levenshtein(substr($s,0,255),substr($t,0,255));
		}
		return levenshtein($s,$t);
	}
}
