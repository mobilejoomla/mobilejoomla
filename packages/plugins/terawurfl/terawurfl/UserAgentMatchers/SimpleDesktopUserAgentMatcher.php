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
 * Matches desktop browsers.  This UserAgentMatcher is unlike the rest in that it is does not use any database functions to find a matching device.  If a device is not matched with this UserAgentMatcher, another one is assigned to match it using the database.
 * @package TeraWurflUserAgentMatchers
 */
class SimpleDesktopUserAgentMatcher extends UserAgentMatcher {
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		return WurflConstants::$GENERIC_WEB_BROWSER;
	}
	/**
	 * Is the given user agent very likely to be a desktop browser
	 * @param String User agent
	 * @return Bool
	 */
	public static function isDesktopBrowser($ua){
		if(UserAgentUtils::isMobileBrowser($ua)) return false;
		if(self::contains($ua,array(
			'HTC', // HTC; horrible user agents, especially with Opera
			'PPC', // PowerPC; not always mobile, but we'll kick it out of SimpleDesktop and match it in the WURFL DB
			'Nintendo', // too hard to distinguish from Opera
		))) return false;
		// Firefox
		if(self::contains($ua,"Firefox") && !self::contains($ua,'Tablet')) return true;
		if(UserAgentUtils::isDesktopBrowser($ua)) return true;
		if(self::startsWith($ua,'Opera/')) return true;
		if(self::regexContains($ua,array(
			// Internet Explorer 9
			'/^Mozilla\/5\.0 \(compatible; MSIE 9\.0; Windows NT \d\.\d/',
			// Internet Explorer <9
			'/^Mozilla\/4\.0 \(compatible; MSIE \d\.\d; Windows NT \d\.\d/',
		))) return true;
		if(self::contains($ua,array(
			"Chrome",
			"yahoo.com",
			"google.com",
			"Comcast",
		))){
			return true;
		}
		return false;
	}
}
