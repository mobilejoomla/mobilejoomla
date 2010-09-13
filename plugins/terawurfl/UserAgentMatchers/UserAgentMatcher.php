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
 * @version Stable 2.1.3 $Date: 2010/07/29 20:36:29
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 */
/**
 * An abstract class that all UserAgentMatchers must extend.
 * @package TeraWurflUserAgentMatchers
 */
abstract class UserAgentMatcher {
	
	/**
	 * @var TeraWurfl Running instance of Tera-WURFL
	 */
	protected $wurfl;
	/**
	 * WURFL IDs that are hardcoded in this connector.  Used for compatibility testing against new WURFLs
	 * @var array
	 */
	public static $constantIDs = array();
	/**
	 * @var Array List of WURFL IDs => User Agents.  Typically used for matching user agents.
	 */
	public $deviceList;
	
	public function __construct(TeraWurfl $wurfl) {
		$this->wurfl = $wurfl;
	}
	
    /**
     * Attempts to find a conclusively matching WURFL ID from a given user agent
     * @param String User agent
     * @return String Matching WURFL ID
     */
    abstract public function applyConclusiveMatch($ua);
    
    /**
     * Attempts to find a loosely matching WURFL ID from a given user agent
     * @param String User agent
     * @return String Matching WURFL ID
     */
    public function applyRecoveryMatch($ua) {
        return $this->recoveryMatch($ua);
    }
    /**
     * Overide this method in order to have an alternative matching algorithm
     * @param String User agent
     * @return String Matching WURFL ID
     */
    public function recoveryMatch($ua){
        return "generic";
    }
    /**
     * Updates the deviceList Array to contain all the WURFL IDs that are related to the current UserAgentMatcher
     * @return void
     */
    protected function updateDeviceList(){
    	if(is_array($this->deviceList) && count($this->deviceList)>0) return;
    	$this->deviceList = $this->wurfl->db->getFullDeviceList($this->wurfl->fullTableName());
    }
    /**
     * Attempts to match given user agent string to a device from the database by comparing less and less of the strings until a match is found (RIS, Reduction in String)
     * @param String User agent
     * @param int Tolerance, how many characters must match from left to right
     * @return String WURFL ID
     */
    public function risMatch($ua,$tolerance){
    	if($this->wurfl->db->db_implements_ris){
    		return $this->wurfl->db->getDeviceFromUA_RIS($ua,$tolerance,$this);
    	}
    	$this->updateDeviceList();
    	return UserAgentUtils::risMatch($ua,$tolerance,$this);
    }
    /**
     * Attempts to match given user agent string to a device from the database by calculating their Levenshtein Distance (LD)
     * @param String User agent
     * @param int Tolerance, how much difference is allowed
     * @return String WURFL ID
     */
    public function ldMatch($ua,$tolerance=NULL){
    	if($this->wurfl->db->db_implements_ld){
    		return $this->wurfl->db->getDeviceFromUA_LD($ua,$tolerance,$this);
    	}
    	$this->updateDeviceList();
    	return UserAgentUtils::ldMatch($ua,$tolerance,$this);
    }
    /**
     * Returns the name of the UserAgentMatcher in use
     * @return String UserAgentMatcher name
     */
    public function matcherName(){
    	return get_class($this);
    }
    /**
     * Returns the database table suffix for the current UserAgentMatcher
     * @return String Table suffix
     */
    public function tableSuffix(){
    	$cname = $this->matcherName();
    	return substr($cname,0,strpos($cname,"UserAgentMatcher"));
    }
    /**
     * Check if user agent contains target string
     * @param String User agent
     * @param String Target string or array of strings
     * @return Bool
     */
    public static function contains($ua,$find){
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
     * Check if user agent starts with target string
     * @param String User agent
     * @param String Target string or array of strings
     * @return Bool
     */
    public static function startsWith($ua,$find){
    	if(is_array($find)){
    		foreach($find as $part){
    			if(strpos($ua,$part)===0){
    				return true;
    			}
    		}
    		return false;
    	}else{
	    	return (strpos($ua,$find)===0);
    	}
    }
    /**
     * Check if user agent contains another string using PCRE (Perl Compatible Reqular Expressions)
     * @param String User agent
     * @param $find Target regex string or array of regex strings
     * @return Bool
     */
    public static function regexContains($ua,$find){
	    if(is_array($find)){
    		foreach($find as $part){
    			if(preg_match($part,$ua)){
    				return true;
    			}
    		}
    		return false;
    	}else{
	    	return (preg_match($find,$ua));
    	}
    }
}
