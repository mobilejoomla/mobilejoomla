<?php
/**
 * Copyright (c) 2011 ScientiaMobile, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the COPYING file distributed with this package.
 *
 * @package    WURFL_UserAgentMatcher
 * @copyright  ScientiaMobile, Inc.
 * @author     Steve Kamerman <steve AT scientiamobile.com>
 * @license    GNU Affero General Public License
 * @version    $id$
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
