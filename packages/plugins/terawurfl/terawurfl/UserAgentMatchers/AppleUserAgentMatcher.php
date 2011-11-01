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
class AppleUserAgentMatcher extends UserAgentMatcher {

	public static $constantIDs = array(
		'apple_ipod_touch_ver1',
		'apple_ipad_ver1',
		'apple_iphone_ver1',
	);

	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		$deviceId = '';

		if(self::startsWith($ua, 'Apple')){
			if(($tolerance = UserAgentUtils::ordinalIndexOf($ua,' ',3)) == -1){
				$tolerance = strlen($ua);
			}
		}else{
			$tolerance = UserAgentUtils::indexOfOrLength($ua,';',0);
		}
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold  $tolerance",LOG_INFO);
		$deviceId = $this->risMatch($ua,$tolerance);
		return $deviceId;
	}
	public function recoveryMatch($ua){
		$this->wurfl->toLog("Applying ".get_class($this)." recovery match ($ua)",LOG_INFO);
		if(self::contains($ua,'iPod')) return "apple_ipod_touch_ver1";
		if(self::contains($ua,'iPad')) return "apple_ipad_ver1";
		if(self::contains($ua,"iPhone")) return "apple_iphone_ver1";
		return WurflConstants::$GENERIC;
	}
}
