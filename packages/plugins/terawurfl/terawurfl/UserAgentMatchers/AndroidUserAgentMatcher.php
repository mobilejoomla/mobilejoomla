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
class AndroidUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array(
		'generic_android',
		'generic_android_ver1_5',
		'generic_android_ver1_6',
		'generic_android_ver2',
		'generic_android_ver2_1',
		'generic_android_ver2_2',
		'generic_android_ver2_3',
		'generic_android_ver3_0',
	);
	
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		$tolerance = UserAgentUtils::indexOfOrLength($ua,' Build/', 0);
		if($tolerance == strlen($ua))
			$tolerance = UserAgentUtils::indexOfOrLength($ua,')', 0);
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold $tolerance",LOG_INFO);
		return $this->risMatch($ua, $tolerance);
	}
	public function recoveryMatch($ua){
		if(UserAgentUtils::checkIfContains($ua, 'Froyo')){
			return 'generic_android_ver2_2';
		}
		if(preg_match('#Android[\s/](\d)\.(\d)#',$ua,$matches)){
			$version = 'generic_android_ver'.$matches[1].'_'.$matches[2];
			if($version == 'generic_android_ver2_0') $version = 'generic_android_ver2';
			if(in_array($version,self::$constantIDs)){
				return $version;
			}
		}
		return 'generic_android';
	}
}
