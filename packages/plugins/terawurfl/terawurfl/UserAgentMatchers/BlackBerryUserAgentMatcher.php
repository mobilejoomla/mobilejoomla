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
class BlackBerryUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array(
		'2.' => 'blackberry_generic_ver2',
		'3.2' => 'blackberry_generic_ver3_sub2',
		'3.3' => 'blackberry_generic_ver3_sub30',
		'3.5' => 'blackberry_generic_ver3_sub50',
		'3.6' => 'blackberry_generic_ver3_sub60',
		'3.7' => 'blackberry_generic_ver3_sub70',
		'4.1' => 'blackberry_generic_ver4_sub10',
		'4.2' => 'blackberry_generic_ver4_sub20',
		'4.3' => 'blackberry_generic_ver4_sub30',
		'4.5' => 'blackberry_generic_ver4_sub50',
		'4.6' => 'blackberry_generic_ver4_sub60',
		'4.7' => 'blackberry_generic_ver4_sub70',
		'4.' => 'blackberry_generic_ver4',
		'5.' => 'blackberry_generic_ver5',
		'6.' => 'blackberry_generic_ver6',
	);
	
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua){
		if(self::startsWith($ua,"BlackBerry;")){
			$tolerance = UserAgentUtils::ordinalIndexOf($ua,';',3);
		}else{
			$tolerance = UserAgentUtils::firstSlash($ua);
		}
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold  $tolerance",LOG_INFO);
		return $this->risMatch($ua, $tolerance);
	}
	public function recoveryMatch($ua){
		// BlackBerry
		$this->wurfl->toLog("Applying ".get_class($this)." recovery match ($ua)",LOG_INFO);
		if(preg_match('#Black[Bb]erry[^/\s]+/(\d.\d)#',$ua,$matches)){
			$version = $matches[1];
			foreach(self::$constantIDs as $vercode => $deviceID){
				if(strpos($version,$vercode) !== false){
					return $deviceID;
				}
			}
		}
		return WurflConstants::$GENERIC;
	}
}
