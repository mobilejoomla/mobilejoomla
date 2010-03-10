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
 * $Id: NintendoUserAgentMatcher.php,v 1.2 2008/03/01 00:05:25 kamermans Exp $
 * $RCSfile: NintendoUserAgentMatcher.php,v $
 * 
 * Based On: Java WURFL Evolution by Luca Passani
 *
 */
class NintendoUserAgentMatcher extends UserAgentMatcher {
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
?>