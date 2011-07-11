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
class DoCoMoUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array('docomo_generic_jap_ver2','docomo_generic_jap_ver1');
	
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		$deviceId = '';
		if(UserAgentUtils::numSlashes($ua) >= 2){
			$tolerance = UserAgentUtils::secondSlash($ua);
		}else{
			//  DoCoMo/2.0 F01A(c100;TB;W24H17)
			$tolerance = UserAgentUtils::firstOpenParen($ua);
		}
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold $tolerance",LOG_INFO);
		$deviceId = $this->risMatch($ua, $tolerance);
		return $deviceId;
	}
	public function recoveryMatch($ua){
		$versionIndex = 7;
		$version = $ua[$versionIndex];
		return ($version == '2')? 'docomo_generic_jap_ver2': 'docomo_generic_jap_ver1';
	}
}

