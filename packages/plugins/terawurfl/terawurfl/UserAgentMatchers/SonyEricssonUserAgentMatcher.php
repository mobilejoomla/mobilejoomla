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
class SonyEricssonUserAgentMatcher extends UserAgentMatcher {
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		// firstSlash() - 1 because some UAs have revisions that aren't getting detected properly:
		// SonyEricssonW995a/R1FA Browser/NetFront/3.4 Profile/MIDP-2.1 Configuration/CLDC-1.1 JavaPlatform/JP-8.4.3
		$tolerance = UserAgentUtils::firstSlash($ua) - 1;
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold $tolerance",LOG_INFO);
		if(self::startsWith($ua,"SonyEricsson")){
			return $this->risMatch($ua, $tolerance);
		}
		$tolerance = UserAgentUtils::secondSlash($ua);
		return $this->risMatch($ua, $tolerance);
	}
	public function recoveryMatch($ua){
		$tolerance = 14;
		return $this->risMatch($ua, $tolerance);
	}
}
