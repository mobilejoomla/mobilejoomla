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
/**#@+
 * Include required files
 */
require_once realpath(dirname(__FILE__).'/TeraWurflConfig.php');
require_once realpath(dirname(__FILE__).'/DatabaseConnectors/TeraWurflDatabase.php');
require_once realpath(dirname(__FILE__).'/TeraWurflLoader.php');
require_once realpath(dirname(__FILE__).'/UserAgentFactory.php');
require_once realpath(dirname(__FILE__).'/UserAgentUtils.php');
require_once realpath(dirname(__FILE__).'/WurflConstants.php');
require_once realpath(dirname(__FILE__).'/WurflSupport.php');
require_once realpath(dirname(__FILE__).'/UserAgentMatchers/UserAgentMatcher.php');
/**#@-*/
/**
 * The main Tera-WURFL Class, provides all end-user methods and properties for interacting
 * with Tera-WURFL
 * 
 * @package TeraWurfl
 */
class TeraWurfl{
	
	public static $SETTING_WURFL_VERSION = 'wurfl_version';
	public static $SETTING_WURFL_DATE = 'wurfl_date';
	public static $SETTING_LOADED_DATE = 'loaded_date';
	public static $SETTING_PATCHES_LOADED = 'patches_loaded';
	/**
	 * Array of errors that were encountered while processing the request
	 * @var Array
	 */
	public $errors;
	/**
	 * Array of WURFL capabilities of the requested device
	 * @var Array
	 */
	public $capabilities;
	/**
	 * Database connector to be used, must extend TeraWurflDatabase.  All database functions are performed
	 * in the database connector through its methods and properties.
	 * @see TeraWurflDatabase
	 * @see TeraWurflDatabase_MySQL5
	 * @var TeraWurflDatabase
	 */
	public $db = false;
	/**
	 * The directory that TeraWurfl.php is in
	 * @var String
	 */
	public $rootdir;
	/**
	 * The user agent that is being evaluated
	 * @var String
	 */
	public $userAgent; 
	/**
	 * The HTTP Accept header that is being evaluated
	 * @var String
	 */
	public $httpAccept;
	/**
	 * The UserAgentMatcher that is currently in use
	 * @var UserAgentMatcher
	 */
	public $userAgentMatcher;
	/**
	 * Was the evaluated device found in the cache
	 * @var Bool
	 */
	public $foundInCache;
	
	/**
	 * The installed branch of Tera-WURFL
	 * @var String
	 */
	public $release_branch = "Stable";
	/**
	 * The installed version of Tera-WURFL
	 * @var String
	 */
	public $release_version = "2.1.3";
	/**
	 * The required version of PHP for this release
	 * @var String
	 */
	public static $required_php_version = "5.0.0";
	
	/**
	 * Lookup start time
	 * @var int
	 */
	protected $lookup_start;
	/**
	 * Lookup end time
	 * @var int
	 */
	protected $lookup_end;
	/**
	 * The array key that is returned as a WURFL capability group in the capabilities
	 * array that stored Tera-WURFL specific information about the request
	 * @var String
	 */
	protected $matchDataKey = "tera_wurfl";
	/**
	 * The Tera-WURFL specific data that is added to the capabilities array
	 * @var array
	 */
	protected $matchData;
	/**
	 * Array of UserAgentMatchers and match attempt types that the API used to find a matching device
	 * @var Array
	 */
	protected $matcherHistory;
	/*
	 * This keeps the device fallback lookup from running away.
	 * The deepest device I've seen is sonyericsson_z520a_subr3c at 15
	 */
	protected $maxDeviceDepth = 40;
	
	// Constructor
	public function __construct(){
		$this->errors = array();
		$this->capabilities = array();
		$this->matcherHistory = array();
		$this->rootdir = dirname(__FILE__).'/';
		$dbconnector = 'TeraWurflDatabase_'.TeraWurflConfig::$DB_CONNECTOR;
		if($this->db === false) $this->db = new $dbconnector;
		if(!$this->db->connect()){
			//throw new Exception("Cannot connect to database: ".$this->db->getLastError());
			return false;
		}
	}
	
	/**
	 * Returns the matching WURFL ID for a given User Agent
	 * @return String WURFL ID
	 */
	protected function getDeviceIDFromUALoose(){
		$this->matcherHistory = array();
		// Return generic UA if userAgent is empty
		if(strlen($this->userAgent)==0){
			$this->matchData['matcher'] = "none"; 
			$this->matchData['match_type'] = "none";
			$this->matchData['match'] = false;
			$this->setMatcherHistory();
			return WurflConstants::$GENERIC;
		}
		
		// Check for exact match
		if(TeraWurflConfig::$SIMPLE_DESKTOP_ENGINE_ENABLE && $this->userAgent == WurflConstants::$SIMPLE_DESKTOP_UA){
			// SimpleDesktop UA Matching avoids querying the database here
			$deviceID = WurflConstants::$GENERIC_WEB_BROWSER;
		}else{
			$deviceID = $this->db->getDeviceFromUA($this->userAgent);
		}
		$this->matcherHistory[] = $this->userAgentMatcher->matcherName() . "(exact)";
		if($deviceID !== false){
			$this->matchData['matcher'] = $this->userAgentMatcher->matcherName();
			$this->matchData['match_type'] = "exact";
			$this->matchData['match'] = true;
			$this->setMatcherHistory();
			return $deviceID;
		}
		// Check for a conclusive match
		$deviceID = $this->userAgentMatcher->applyConclusiveMatch($this->userAgent);
		$this->matcherHistory[] = $this->userAgentMatcher->matcherName() . "(conclusive)";
		if($deviceID != WurflConstants::$GENERIC){
			$this->matchData['matcher'] = $this->userAgentMatcher->matcherName();
			$this->matchData['match_type'] = "conclusive";
			$this->matchData['match'] = true;
			$this->setMatcherHistory();
			return $deviceID;
		}
		// Check for Vodafone magic
		if($this->userAgentMatcher->matcherName()!="VodafoneUserAgentMatcher" && UserAgentMatcher::contains($this->userAgent,"Vodafone")){
			@require_once realpath(dirname(__FILE__).'/UserAgentMatchers/VodafoneUserAgentMatcher.php');
			$vodafoneUserAgentMatcher = new VodafoneUserAgentMatcher($this);
			$this->matcherHistory[] = $vodafoneUserAgentMatcher->matcherName() . "(conclusive)";
			$deviceID = $vodafoneUserAgentMatcher->applyConclusiveMatch($this->userAgent);
			if($deviceID != WurflConstants::$GENERIC){
				$this->matchData['matcher'] = $vodafoneUserAgentMatcher->matcherName();
				$this->matchData['match_type'] = "conclusive";
				$this->matchData['match'] = true;
				$this->setMatcherHistory();
				return $deviceID;
			}
		}
		// Check for recovery match
		$deviceID = $this->userAgentMatcher->applyRecoveryMatch($this->userAgent);
		$this->matcherHistory[] = $this->userAgentMatcher->matcherName() . "(recovery)";
		if($deviceID != WurflConstants::$GENERIC){
			$this->matchData['matcher'] = $this->userAgentMatcher->matcherName();
			$this->matchData['match_type'] = "recovery";
			$this->matchData['match'] = false;
			$this->setMatcherHistory();
			return $deviceID;
		}
		// Check CatchAll if it's not already in use
		if($this->userAgentMatcher->matcherName()!="CatchAllUserAgentMatcher"){
			$catchAllUserAgentMatcher = new CatchAllUserAgentMatcher($this);
			$this->matcherHistory[] = $catchAllUserAgentMatcher->matcherName() . "(recovery)";
			$deviceID = $catchAllUserAgentMatcher->applyRecoveryMatch($this->userAgent);
			if($deviceID != WurflConstants::$GENERIC){
				// The CatchAll matcher is intelligent enough to determine the match properties
				$this->matchData['matcher'] = $catchAllUserAgentMatcher->matcher;
				$this->matchData['match_type'] = $catchAllUserAgentMatcher->match_type;
				$this->matchData['match'] = $catchAllUserAgentMatcher->match;
				$this->setMatcherHistory();
				return $deviceID;
			}
		}
		
		// A matching device still hasn't been found - check HTTP ACCEPT headers
		if(strlen($this->httpAccept) > 0){
			$this->matcherHistory[] = "http_accept";
			if(UserAgentMatcher::contains($this->httpAccept,array(
				WurflConstants::$ACCEPT_HEADER_VND_WAP_XHTML_XML,
				WurflConstants::$ACCEPT_HEADER_XHTML_XML,
				WurflConstants::$ACCEPT_HEADER_TEXT_HTML
			  ))){
				$this->matchData['matcher'] = "http_accept";
				$this->matchData['match_type'] = "recovery";
				// This isn't really a match, it's a suggestion
				$this->matchData['match'] = false;
				$this->setMatcherHistory();
				return WurflConstants::$GENERIC_XHTML;
			}
		}
		$this->matchData['matcher'] = "none";
		$this->matchData['match_type'] = "none";
		$this->matchData['match'] = false;
		$this->setMatcherHistory();
		
		if(UserAgentUtils::isMobileBrowser($this->userAgent)) return WurflConstants::$GENERIC_XHTML;
		return WurflConstants::$GENERIC_WEB_BROWSER;
	}
	/**
	 * Detects the capabilities from a given request object ($_SERVER)
	 * @param Array Request object ($_SERVER contains this data)
	 * @return Bool Match
	 */
	public function getDeviceCapabilitiesFromRequest($server){
		if(!isset($server))$server = $_SERVER;
		return $this->getDeviceCapabilitiesFromAgent(WurflSupport::getUserAgent($server),WurflSupport::getAcceptHeader($server));
	}
	/**
	 * Detects the capabilities of a device from a given user agent and optionally, the HTTP Accept Headers
	 * @param String HTTP User Agent
	 * @param String HTTP Accept Header
	 * @return Bool matching device was found
	 */
	public function getDeviceCapabilitiesFromAgent($userAgent=null,$httpAccept=null){
		$this->db->numQueries = 0;
		$this->matchData = array(
			"num_queries" => 0,
			"actual_root_device" => '',
			"match_type" => '',
			"matcher" => '',
			"match"	=> false,
			"lookup_time" => 0,
			"fall_back_tree" => ''
		);
		$this->lookup_start = microtime(true);
		$this->foundInCache = false;
		$this->capabilities = array();
		// Define User Agent
		$this->userAgent = (is_null($userAgent))? WurflSupport::getUserAgent(): $userAgent;
		if(strlen($this->userAgent) > 255) $this->userAgent = substr($this->userAgent,0,255);
		// Use the ultra high performance SimpleDesktopMatcher if enabled
		if(TeraWurflConfig::$SIMPLE_DESKTOP_ENGINE_ENABLE){
			require_once realpath(dirname(__FILE__).'/UserAgentMatchers/SimpleDesktopUserAgentMatcher.php');
			if(SimpleDesktopUserAgentMatcher::isDesktopBrowser($userAgent)) $this->userAgent = WurflConstants::$SIMPLE_DESKTOP_UA;
		}
		// Define HTTP ACCEPT header.  Default: DO NOT use HTTP_ACCEPT headers
		//$this->httpAccept= (is_null($httpAccept))? WurflSupport::getAcceptHeader(): $httpAccept;
		$this->userAgent = UserAgentUtils::cleanUserAgent($this->userAgent);
		// Check cache for device
		if(TeraWurflConfig::$CACHE_ENABLE){
			$cacheData = $this->db->getDeviceFromCache($this->userAgent);
			// Found in cache
			if($cacheData !== false){
				$this->capabilities = $cacheData;
				$this->foundInCache = true;
				$deviceID = $cacheData['id'];
			}
		}
		if(!$this->foundInCache){
			require_once realpath(dirname(__FILE__).'/UserAgentMatchers/SimpleDesktopUserAgentMatcher.php');
			// Find appropriate user agent matcher
			$this->userAgentMatcher = UserAgentFactory::createUserAgentMatcher($this,$this->userAgent);
			// Find the best matching WURFL ID
			$deviceID = $this->getDeviceIDFromUALoose($userAgent);
			// Get the capabilities of this device and all its ancestors
			$this->getFullCapabilities($deviceID);
			// Now add in the Tera-WURFL results array
			$this->lookup_end = microtime(true);
			$this->matchData['num_queries'] = $this->db->numQueries;
			$this->matchData['lookup_time'] = $this->lookup_end - $this->lookup_start;
			// Add the match data to the capabilities array so it gets cached
			$this->addCapabilities(array($this->matchDataKey => $this->matchData));
		}
		if(TeraWurflConfig::$CACHE_ENABLE==true && !$this->foundInCache){
			// Since this device was not cached, cache it now.
			$this->db->saveDeviceInCache($this->userAgent,$this->capabilities);
		}
		return $this->capabilities[$this->matchDataKey]['match'];
	}
	/**
	 * Builds the full capabilities array from the WURFL ID
	 * @param String WURFL ID
	 * @return void
	 */
	public function getFullCapabilities($deviceID){
		if(is_null($deviceID)){
			throw new Exception("Invalid Device ID: ".var_export($deviceID,true)."\nMatcher: {$this->userAgentMatcher->matcherName()}\nUser Agent: ".$this->userAgent);
			exit(1);
		}
		// Now get all the devices in the fallback tree
		$fallbackIDs = array();
		if($deviceID != WurflConstants::$GENERIC && $this->db->db_implements_fallback){
			$fallbackTree = $this->db->getDeviceFallBackTree($deviceID);
			$this->addTopLevelSettings($fallbackTree[0]);
			$fallbackTree = array_reverse($fallbackTree);
			foreach($fallbackTree as $dev){
				$fallbackIDs[] = $dev['id'];
				if(isset($dev['actual_device_root']) && $dev['actual_device_root'])$this->matchData['actual_root_device'] = $dev['id'];
				$this->addCapabilities($dev);
			}
			$this->matchData['fall_back_tree'] = implode(',',array_reverse($fallbackIDs));
		}else{
			$fallbackTree = array();
			$childDevice = $this->db->getDeviceFromID($deviceID);
			$fallbackTree[] = $childDevice;
			$fallbackIDs[] = $childDevice['id'];
			$currentDevice = $childDevice;
			$i=0;
			/**
			 * This loop starts with the best-matched device, and follows its fall_back until it reaches the GENERIC device
			 * Lets use "tmobile_shadow_ver1" for an example:
			 * 
			 * 'id' => 'tmobile_shadow_ver1', 'fall_back' => 'ms_mobile_browser_ver1'
			 * 'id' => 'ms_mobile_browser_ver1', 'fall_back' => 'generic_xhtml'
			 * 'id' => 'generic_xhtml', 'fall_back' => 'generic'
			 * 'id' => 'generic', 'fall_back' => 'root'
			 * 
			 * This fallback_tree in this example contains 4 elements in the order shown above.
			 * 
			 */
			while($currentDevice['fall_back'] != "root"){
				$currentDevice = $this->db->getDeviceFromID($currentDevice['fall_back']);
				if(in_array($currentDevice['id'],$fallbackIDs)){
					// The device we just looked up is already in the list, which means that
					// we are going to enter an infinate loop if we don't break from it.
					$this->toLog("The device we just looked up is already in the list, which means that we are going to enter an infinate loop if we don't break from it. DeviceID: $deviceID, FallbackIDs: [".implode(',',$fallbackIDs)."]",LOG_ERR);
					throw new Exception("Killed script to prevent infinate loop.  See log for details.");
					break;
				}
				if(!isset($currentDevice['fall_back']) || $currentDevice['fall_back'] == ''){
					$this->toLog("Empty fall_back detected. DeviceID: $deviceID, FallbackIDs: [".implode(',',$fallbackIDs)."]",LOG_ERR);
					throw new Exception("Empty fall_back detected.  See log for details.");
				}
				$fallbackTree[] = $currentDevice;
				$fallbackIDs[] = $currentDevice['id'];
				$i++;
				if($i > $this->maxDeviceDepth){
					$this->toLog("Exceeded maxDeviceDepth while trying to build capabilities for device. DeviceID: $deviceID, FallbackIDs: [".implode(',',$fallbackIDs)."]",LOG_ERR);
					throw new Exception("Killed script to prevent infinate loop.  See log for details.");
					break;
				}
			}
			$this->matchData['fall_back_tree'] = implode(',',$fallbackIDs);
			if($fallbackTree[count($fallbackTree)-1]['id'] != WurflConstants::$GENERIC){
				// The device we are looking up cannot be traced back to the GENERIC device
				// and will likely not contain the correct capabilities
				$this->toLog("The device we are looking up cannot be traced back to the GENERIC device and will likely not contain the correct capabilities. DeviceID: $deviceID, FallbackIDs: [".implode(',',$fallbackIDs)."]",LOG_ERR);
			}
			/**
			 * Merge the device capabilities from the parent (GENERIC) to the child (DeviceID)
			 * We merge in this order because the GENERIC device contains all the properties that can be set
			 * Then the next child modifies them, then the next child, and the next child, etc... 
			 */
			while(count($fallbackTree)>0){
				$dev = array_pop($fallbackTree);
				// actual_root_device is the most accurate device in the fallback tree that is a "real" device, not a sub version or generic
				if(isset($dev['actual_device_root']) && $dev['actual_device_root'])$this->matchData['actual_root_device'] = $dev['id'];
				$this->addCapabilities($dev);
			}
			$this->addTopLevelSettings($childDevice);
		}
	}
	/**
	 * Returns the value of the requested capability for the detected device
	 * @param String Capability name (e.g. "is_wireless_device")
	 * @return Mixed Capability value
	 */
	public function getDeviceCapability($capability) {
		// TODO: Optimize function, one method is to flatten the capabilities array, or create a group=>cap index
		$this->toLog('Searching for '.$capability.' as a capability', LOG_INFO);
		foreach ( $this->capabilities as $group ) {
			if ( !is_array($group) ) {
				continue;
			}
			while ( list($key, $value)=each($group) ) {
				if ($key==$capability) {
					$this->toLog('I found it, value is '.$value, LOG_INFO);
					return $value;
				}
			}
		}
		$this->toLog('I could not find the requested capability ('.$capability.'), returning NULL', LOG_WARNING);
		// since 1.5.2, I can't return "false" because that is a valid value.  Now I return NULL, use is_null() to check
		return null;
	}
	/**
	 * Returns the value of the given setting name
	 * @param String Setting value
	 */
	public function getSetting($key){
		return $this->db->getSetting($key);
	}
	public function fullTableName(){
		return TeraWurflConfig::$TABLE_PREFIX.'_'.$this->userAgentMatcher->tableSuffix();
	}
	/**
	 * Log an error in the Tera-WURFL log file
	 * @see TeraWurflConfig
	 * @param String The error message text
	 * @param Int The log level / severity of the error
	 * @param String The function or code that was being run when the error occured
	 * @return void
	 */
	public function toLog($text, $requestedLogLevel=LOG_NOTICE, $func="Tera-WURFL"){
		if($requestedLogLevel == LOG_ERR) $this->errors[] = $text;
		if (TeraWurflConfig::$LOG_LEVEL == 0 || ($requestedLogLevel-1) >= TeraWurflConfig::$LOG_LEVEL ) {
			return;
		}
		if ( $requestedLogLevel == LOG_ERR ) {
			$warn_banner = 'ERROR: ';
		} else if ( $requestedLogLevel == LOG_WARNING ) {
			$warn_banner = 'WARNING: ';
		} else {
			$warn_banner = '';
		}
		$_textToLog = date('r')." [".php_uname('n')." ".getmypid()."]"."[$func] ".$warn_banner . $text;
		$logfile = $this->rootdir.TeraWurflConfig::$DATADIR.TeraWurflConfig::$LOG_FILE;
		if(!is_writeable($logfile)){
			throw new Exception("Tera-WURFL Error: cannot write to log file ($logfile)");
		}
		$_logFP = fopen($logfile, "a+");
		fputs($_logFP, $_textToLog."\n");
		fclose($_logFP);
	}
	/**
	 * Adds the top level properties to the capabilities array, like id and user_agent
	 * @param Array New properties to be added
	 * @return void
	 */
	public function addTopLevelSettings(Array $newCapabilities){
		foreach($newCapabilities as $key => $val){
			if(is_array($val))continue;
			$this->capabilities[$key] = $val;
		}
	}
	/**
	 * Add new capabilities to the capabilities array
	 * @param Array Capabilities that are to be added
	 * @return void
	 */
	public function addCapabilities(Array $newCapabilities){
		self::mergeCapabilities($this->capabilities,$newCapabilities);
	}
	/**
	 * Combines the MatcherHistory array into a string and stores it in the matchData
	 * @return void
	 */
	protected function setMatcherHistory(){
		$this->matchData['matcher_history'] = implode(',',$this->matcherHistory);
	}
	/**
	 * Merges given $addedDevice array onto $baseDevice array
	 * @param Array Main capabilities array
	 * @param Array New capabilities array
	 * @return void
	 */
	public static function mergeCapabilities(Array &$baseDevice, Array $addedDevice){
		if(count($baseDevice) == 0){
			// Base device is empty
			$baseDevice = $addedDevice;
			return;
		}
		foreach($addedDevice as $levOneKey => $levOneVal){
			// Check if the base device has defined this value yet
			if(!is_array($levOneVal)){
				// This is top level setting, not a capability
				continue;
			}else{
				if(!array_key_exists($levOneKey,$baseDevice))$baseDevice[$levOneKey]=array();
				// This is an array value, merge the contents
				foreach($levOneVal as $levTwoKey => $levTwoVal){
					// This is just a scalar value, apply it
					$baseDevice[$levOneKey][$levTwoKey] = $levTwoVal;
					continue;
				}
			}
		}
	}
	/**
	 * Get the absolute path to the data directory on the filesystem
	 * @return String Absolute path to data directory
	 */
	public static function absoluteDataDir(){
		return dirname(__FILE__).'/'.TeraWurflConfig::$DATADIR;
	}
}
