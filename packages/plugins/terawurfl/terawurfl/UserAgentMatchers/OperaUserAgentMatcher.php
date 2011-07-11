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
class OperaUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array("opera","opera_7","opera_8","opera_9","opera_10");
	
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
	if(UserAgentUtils::checkIfContains($ua,"Opera/10")){
			return "opera_10";
		}elseif(UserAgentUtils::checkIfContains($ua,"Opera/9")){
			return "opera_9";
		}elseif(UserAgentUtils::checkIfContains($ua,"Opera/8")){
			return "opera_8";
		}elseif(UserAgentUtils::checkIfContains($ua,"Opera/7")){
			return "opera_7";
		}
		$tolerance = 5;
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: LD with threshold $tolerance",LOG_INFO);
		return $this->ldMatch($ua, $tolerance);
	}
	public function recoveryMatch($ua){
			return "opera";
	}
}
