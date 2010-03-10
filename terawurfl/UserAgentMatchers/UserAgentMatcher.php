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
 * $Id: UserAgentMatcher.php,v 1.2 2008/03/01 00:05:25 kamermans Exp $
 * $RCSfile: UserAgentMatcher.php,v $
 * 
 * Based On: Java WURFL Evolution by Luca Passani
 *
 */
abstract class UserAgentMatcher {
	
	protected $wurfl;
	/**
	 * Array of WURFL IDs => User Agents.  Typically used for matching user agents.
	 * @var array
	 */
	public $deviceList;
	
	public function __construct(TeraWurfl $wurfl) {
		$this->wurfl = $wurfl;
	}
	
    /**
     *
     * @param ua
     * @return
     */
    abstract public function applyConclusiveMatch($ua);
    
    /**
     *
     * @param request
     * @param userAgentLogger
     * @return
     */
    public function applyRecoveryMatch($ua) {
        return $this->recoveryMatch($ua);
    }
    /**
     * Overide this method in order to have an alternative match
     * @param ua
     * @return String
     */
    public function recoveryMatch($ua){
        return "generic";
    }
    private function updateDeviceList(){
    	if(is_array($this->deviceList) && count($this->deviceList)>0) return;
    	$this->deviceList = $this->wurfl->db->getFullDeviceList($this->wurfl->fullTableName());
    }
    public function risMatch($ua,$tolerance){
    	if($this->wurfl->db->db_implements_ris){
    		return $this->wurfl->db->getDeviceFromUA_RIS($ua,$tolerance,$this);
    	}
    	$this->updateDeviceList();
    	return UserAgentUtils::risMatch($ua,$tolerance,$this);
    }
    public function ldMatch($ua,$tolerance=NULL){
    	if($this->wurfl->db->db_implements_ld){
    		return $this->wurfl->db->getDeviceFromUA_LD($ua,$tolerance,$this);
    	}
    	$this->updateDeviceList();
    	return UserAgentUtils::ldMatch($ua,$tolerance,$this);
    }
    public function matcherName(){
    	return get_class($this);
    }
    public function tableSuffix(){
    	$cname = $this->matcherName();
    	return substr($cname,0,strpos($cname,"UserAgentMatcher"));
    }
    /**
     * Check if string contains another string
     * @param $ua Haystack
     * @param $find Needle (either string or array of strings)
     * @return boolean needle found in haystack
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
     * Check if string starts with another string
     * @param $ua Haystack
     * @param $find Needle (either string or array of strings)
     * @return boolean haystack begins with needle
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
     * Check if string contains another string using PCRE (Perl Compatible Reqular Expressions)
     * @param $ua Haystack
     * @param $find Needle (either string or array of strings)
     * @return boolean needle found in haystack
     */
    public static function regexContains($ua,$find){
	    if(is_array($find)){
    		foreach($find as $part){
    			if(preg_match($find,$ua)){
    				return true;
    			}
    		}
    		return false;
    	}else{
	    	return (preg_match($find,$ua) > 0);
    	}
    }
}
?>