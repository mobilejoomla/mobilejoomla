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
class NintendoUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array("nintendo_wii_browser","nintendo_dsi_ver1","nintendo_ds_ver1");
	
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		$deviceId = '';
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: LD",LOG_INFO);
		$deviceId = $this->ldMatch($ua);
		return $deviceId;
	}
	public function recoveryMatch($ua){
		if(self::contains($ua,"Nintendo Wii")){
			return "nintendo_wii_browser";
		}
		if(self::contains($ua,"Nintendo DSi")){
			return "nintendo_dsi_ver1";
		}
		if((self::startsWith($ua,'Mozilla/') && self::contains($ua,"Nitro") && self::contains($ua,"Opera"))){
			return "nintendo_ds_ver1";
		}
		return "nintendo_wii_browser";
	}
}
