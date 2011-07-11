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
class VodafoneUserAgentMatcher extends UserAgentMatcher {
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		$clean_ua = $ua;
		if(self::contains($ua,"/SN") && !self::contains($ua,"XXXXXXXXXXXX")){
			//not using RegEx for the time being
			$start_idx = strpos($ua,"/SN")+3;
			$sub_str = substr($ua,$start_idx);
			$end_idx = strpos($sub_str," ");
			if($end_idx !== false && $sub_str != "" && strlen($sub_str) > $end_idx){
				$num_digits = strlen($sub_str) - $end_idx;
				$new_ua = substr($ua,0,$start_idx);
				for($i=0;$i<$end_idx;$i++){
					$new_ua .= "X";
				}
				$new_ua .= substr($ua,$end_idx);
				$clean_ua = $new_ua;
			}
		}
		
		$tolerance = UserAgentUtils::firstSlash($clean_ua);
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: RIS with threshold $tolerance",LOG_INFO);
		$match = $this->risMatch($clean_ua, $tolerance);
		if($match == WurflConstants::$GENERIC){
			$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: LD",LOG_INFO);
			return $this->ldMatch($ua);
		}
		return $match;
	}
}
