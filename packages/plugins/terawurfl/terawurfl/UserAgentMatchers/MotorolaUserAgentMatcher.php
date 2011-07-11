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
class MotorolaUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array("mot_mib22_generic");
	
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		$tolerance = 5;
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold $tolerance",LOG_INFO);
		if(self::startsWith($ua,"Mot-") || self::startsWith($ua,"MOT-") || self::startsWith($ua,"Motorola")) {
			$deviceId = $this->risMatch($ua, $tolerance);
			return $deviceId;
		}
		$deviceId = $this->ldMatch($ua,$tolerance);
		return $deviceId;
	}
	public function recoveryMatch($ua){
		$this->wurfl->toLog("Applying ".get_class($this)." Recovery Match",LOG_INFO);
		if(self::contains($ua,"MIB/2.2") || self::contains($ua,"MIB/BER2.2")){
			return "mot_mib22_generic";
		}
		return WurflConstants::$GENERIC;
	}
}
