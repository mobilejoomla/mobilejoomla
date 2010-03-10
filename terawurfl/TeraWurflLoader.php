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
 * $Id: TeraWurflLoader.php,v 1.9 2008/03/01 00:05:25 kamermans Exp $
 * $RCSfile: TeraWurflLoader.php,v $
 * 
 * Based On: Java WURFL Evolution by Luca Passani
 *
 */
class TeraWurflLoader{
	
	public static $WURFL_LOCAL = "local";
	public static $WURFL_REMOTE = "remote";
	public static $WURFL_REMOTE_CVS = "remote_cvs";
	public static $WURFL_PATCH = "patch";
	
	// Properties
	public $errors;
	public $version;
	public $last_updated;
	public $groups;
	
	private $source;
	private $table;
	private $file;
	private $wurfl;
	
	private $devices;
	private $xml;
	private $tables;
	private $patchtables;
	
	public $mainDevices = 0;
	public $patchAddedDevices = 0;
	public $patchMergedDevices = 0;
	
	private $timestart;
	private $timevalidate;
	private $timesort;
	private $timepatch;
	private $timedatabase;
	private $timecache;
	private $timeend;
	
	private $PRESERVE_CACHE = true;
	
	// Constructor
	public function __construct(TeraWurfl &$wurfl,$source){
		$this->errors = array();
		$this->wurfl = $wurfl;
		$this->source = $source;
		$this->devices = array();
		$this->tables = array();
		$this->patchtables = array();
		$this->file = $this->getFile();
		$this->table = $this->getTable();
	}
	
	// Public Methods
	/**
	 * Loads the WURFL and patch files into the database
	 */
	public function load(){
		$this->wurfl->toLog("Loading WURFL",LOG_INFO);
		if(!is_readable($this->file)){
			$this->wurfl->toLog("The main WURFL file could not be opened: ".$this->file,LOG_ERROR);
			$this->errors[]="The main WURFL file could not be opened: ".$this->file;
			return false;
		}
		$this->timestart = microtime(true);
		try{
			$this->xml = simplexml_load_file($this->file);
		}catch(Exception $ex){
			$this->wurfl->toLog("Could not parse WURFL file: not valid XML (".$this->file.")",LOG_ERROR);
			$this->errors[]="Could not parse WURFL file: not valid XML (".$this->file.")";
			return false;
		}
		if(isset($this->xml->version)){
			$this->version = (string) $this->xml->version->ver;
			$this->last_updated = (string) $this->xml->version->last_updated;
		}
		// Check for "devices" or "wurfl_patch"
		$this->wurfl->toLog("Validating ".($this->source),LOG_INFO);
		if(!$this->validate()) return false;
		$this->wurfl->toLog("Sorting ".($this->source),LOG_INFO);
		if(!$this->sort()) return false;
		$this->wurfl->toLog("Loading Patches",LOG_INFO);
		if(!$this->loadPatches()) return false;
		$this->wurfl->toLog("Loading data into DB",LOG_INFO);
		if(!$this->loadIntoDB()) return false;
		$this->timecache = microtime(true);
		if($this->PRESERVE_CACHE){
			$this->wurfl->toLog("Rebuilding cache",LOG_INFO);
			$this->wurfl->db->rebuildCacheTable();
		}else{
			$this->wurfl->db->createCacheTable();
		}
		$this->timeend = microtime(true);
		$this->wurfl->toLog("Finished loading WURFL {$this->version} ({$this->last_updated}) in ".round($this->totalLoadTime(),2)." seconds",LOG_WARNING);
		return true;
	}
	/**
	 * Validates the XML data from the WURFL file or Patch file and places the devices in $this->devices
	 */
	public function validate(){
		if($this->source != self::$WURFL_PATCH) $this->timevalidate = microtime(true);
		$before_errors = count($this->errors);
		foreach($this->xml->devices->device as $device){
			if(!(string)$device['id'] == "generic"){
				// Must have a valid wurfl ID
				if(strlen((string)$device['id'])==0){
					$this->wurfl->toLog("Skipping WURFL entry (invalid ID):\n".var_export($device->asXML(),true),LOG_WARNING);
					$this->errors[] = "Skipping WURFL entry (invalid ID):\n".var_export($device->asXML(),true);
					continue;
				}
				// Must have a valid User Agent unless it's "generic"
				if(strlen((string)$device['user_agent'])==0){
					$this->wurfl->toLog("Skipping WURFL entry (invalid User Agent):\n".var_export($device->asXML(),true),LOG_WARNING);
					$this->errors[] = "Skipping WURFL entry (invalid User Agent):\n".var_export($device->asXML(),true);
					continue;
				}
				// Must have a valid fall_back, we can't validate patch fall_backs unless we look through $this->tables
				if($this->source != self::$WURFL_PATCH && !$this->validID((string)$device['fall_back'])){
					$this->wurfl->toLog("Skipping WURFL entry (invalid Fallback '".$device['fall_back']."'):\n".var_export($device->asXML(),true),LOG_WARNING);
					$this->errors[] = "Skipping WURFL entry (invalid Fallback '".$device['fall_back']."'):\n".var_export($device->asXML(),true);
					continue;
				}
			}
			// Collect Groups and Capabilities from DEVICES table only
			if($this->table == TeraWurflConfig::$DEVICES){
				foreach($device->group as $group){
					$group_name = (string)$group['name'];
					if(!isset($this->groups[$group_name]))$this->groups[$group_name]=array();
					foreach($group->capability as $cap){
						$capname = (string)$cap['name'];
						$capvalue = (string)$cap['value'];
						if(!isset($this->groups[$group_name][$capname]))$this->groups[$group_name][$capname]=$capvalue;
					}
				}
			}
			// Put validated device in $this->devices array as an array
			$this->devices[] = self::deviceXMLToArray($device);
		}
		return ($before_errors == count($this->errors));
	}
	/**
	 * Sorts the validated data from $this->devices into their respective UserAgentMatcher tables ($this->tables)
	 * based on the UserAgentMatcher that matches the device's user agent
	 */
	public function sort(){
		if($this->source != self::$WURFL_PATCH) $this->timesort = microtime(true);
		// Destroy the loaded XML
		unset($this->xml);
		foreach($this->devices as $device){
			// This will return something like "Nokia", "Motorola", or "CatchAll"
			$matcher = UserAgentFactory::userAgentType($this->wurfl,$device['user_agent']);
			// TeraWurfl_Nokia
			$uatable = $this->table.'_'.$matcher;
			if($this->source == self::$WURFL_PATCH){
				if(!isset($this->patchtables[$uatable]))$this->patchtables[$uatable]=array();
				$this->patchtables[$uatable][$device['id']]=$device;
			}else{
				if(!isset($this->tables[$uatable]))$this->tables[$uatable]=array();
				$this->tables[$uatable][$device['id']]=$device;
				$this->mainDevices++;
			}
		}
		// Destroy the devices array
		$this->devices = array();
		return true;
	}
	/**
	 * Loads the completed WURFL into the database.
	 */
	public function loadIntoDB(){
		$this->timedatabase = microtime(true);
		if($this->wurfl->db->loadDevices($this->tables)){
			return true;
		}else{
			$this->errors = array_merge($this->errors,$this->wurfl->db->errors);
			return false;
		}
	}
	public function loadPatches(){
		if(!TeraWurflConfig::$PATCH_ENABLE) return true;
		$this->source = TeraWurflLoader::$WURFL_PATCH;
		$this->timepatch = microtime(true);
		// Explode the patchfile string into an array of patch files (normally just one file)
		$patches = explode(';',TeraWurflConfig::$PATCH_FILE);
		foreach($patches as $patch){
			$this->wurfl->toLog("Loading patch: ".$patch,LOG_WARNING);
			$this->loadPatchFile(TeraWurfl::absoluteDataDir().$patch);
		}
		return true;
	}
	public function loadPatchFile($patchFile){
		$this->patchtables = array();
		$this->devices = array();
		// Make sure we can read the file
		if(!is_readable($patchFile)){
			$this->wurfl->toLog("The patch file could not be opened: ".$patchFile,LOG_ERR);
			$this->errors[]="The patch file could not be opened: ".$patchFile;
			return false;
		}
		try{
			$this->xml = simplexml_load_file($patchFile);
		}catch(Exception $ex){
			$this->wurfl->toLog("Could not parse WURFL file: not valid XML (".$patchFile.")",LOG_ERR);
			$this->errors[]="Could not parse WURFL file: not valid XML (".$patchFile.")";
			return false;
		}
		/**
		 * At this point the main WURFL has been loaded and all of it's data is in $this->tables.
		 * Next we will reuse the validate and sort functions, but since $this->source is now
		 * self::WURFL_PATCH, they will load the devices into $this->patchtables
		 */
		if(!$this->validate()) return false;
		if(!$this->sort()) return false;
		// Merge the patch into the main array
		// TODO: this might be faster with: while(array_pop($this->patchtables))
		foreach($this->patchtables as $table => &$devices){
			// Check if main WURFL has this table already
			if(!isset($this->tables[$table])) $this->tables[$table]=array();
			$maintable =& $this->tables[$table];
			foreach($devices as $id => &$device){
				if(isset($maintable[$id])){
					// Merge this device on top of the existing device
					TeraWurfl::mergeCapabilities($maintable[$device['id']],$device);
					$this->patchMergedDevices++;
				}else{
					// Add this new device to the table
					$maintable[$id] = $device;
					$this->patchAddedDevices++;
				}
			}
			unset($maintable);
		}
		// Clear this array - we're finished with it.
		$this->patchtables = array();
	}
	public function totalLoadTime(){
		return ($this->timeend - $this->timestart);
	}
	public function parseTime(){
		return ($this->timevalidate - $this->timestart);
	}
	public function validateTime(){
		return ($this->timesort - $this->timevalidate);
	}
	public function sortTime(){
		return ($this->timepatch - $this->timesort);
	}
	public function patchTime(){
		return ($this->timedatabase - $this->timepatch);
	}
	public function databaseTime(){
		return ($this->timecache - $this->timedatabase);
	}
	public function cacheRebuildTime(){
		return ($this->timeend - $this->timecache);
	}
	
	public static function deviceXMLToArray(&$device){
		$data = array();
		$data['id'] = (string)$device['id'];
		if(isset($device['fall_back'])) $data['fall_back'] = (string)$device['fall_back'];
		if(isset($device['user_agent'])) $data['user_agent'] = (string)$device['user_agent'];
		if(isset($device['actual_device_root'])){
			$data['actual_device_root'] = (string)$device['actual_device_root'];
			$data['actual_device_root'] = ($data['actual_device_root'])?1:0;
		}
		foreach($device->group as $group){
			$groupname = (string)$group['id'];
			if(!isset($data[$groupname])) $data[$groupname]=array();
			foreach($group->capability as $cap){
				$capname = (string)$cap['name'];
				$value = (string)$cap['value'];
				// Clean Boolean values
				if($value === 'true')$value=true;
				if($value === 'false')$value=false;
				if(!is_bool($value)){
					// Clean Numeric values by loosely comparing the (float) to the (string)
					$numval = (float)$value;
					if(strcmp($value,$numval)==0)$value=$numval;
				}
				$data[$groupname][$capname]=$value;
			}
		}
		return $data;
	}
	
	// Private Methods
	private function validID($id){
		if(strlen($id)==0) return false;
		foreach($this->xml->devices->device as $device){
			if($device['id']==$id)return true;
		}
		return false;
	}
	private function getFile(){
			switch($this->source){
			case self::$WURFL_PATCH:
				return TeraWurfl::absoluteDataDir().TeraWurflConfig::$WURFL_PATCH_FILE;
				break;
			case self::$WURFL_LOCAL:
				return TeraWurfl::absoluteDataDir().TeraWurflConfig::$WURFL_FILE;
				break;
		}
	}
	private function getTable(){
		switch($this->source){
			case self::$WURFL_PATCH:
				return TeraWurflConfig::$PATCH;
				break;
			case self::$WURFL_LOCAL:
			case self::$WURFL_REMOTE:
			case self::$WURFL_REMOTE_CVS:
			default:
				return TeraWurflConfig::$DEVICES;
				break;
		}
	}
	
}
?>