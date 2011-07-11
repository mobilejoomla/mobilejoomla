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
class SamsungUserAgentMatcher extends UserAgentMatcher {
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		if(self::startsWith($ua,array("SAMSUNG-","SEC-","SCH"))){
			$tolerance = UserAgentUtils::firstSlash($ua);
			$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold (first slash) $tolerance",LOG_INFO);
		}elseif(self::startsWith($ua,"Samsung") || self::startsWith($ua,"SPH") || self::startsWith($ua,"SGH")){
			$tolerance = UserAgentUtils::firstSpace($ua);
			$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold (first space) $tolerance",LOG_INFO);
		}else{
			$tolerance = UserAgentUtils::secondSlash($ua);
			$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold (second slash) $tolerance",LOG_INFO);
		}
		return $this->risMatch($ua, $tolerance);
	}
	public function recoveryMatch($ua){
		if(self::startsWith($ua,"SAMSUNG")){
			$tolerance = 8;
			return $this->ldMatch($ua,$tolerance);
		}else{
			$tolerance = UserAgentUtils::indexOfOrLength($ua,'/',strpos($ua,'Samsung'));
			return $this->risMatch($ua, $tolerance);
		}
	}
}
