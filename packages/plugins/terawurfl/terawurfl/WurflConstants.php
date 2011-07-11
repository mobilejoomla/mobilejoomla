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
 * @package    WURFL
 * @copyright  ScientiaMobile, Inc.
 * @author     Steve Kamerman <steve AT scientiamobile.com>
 * @license    GNU Affero General Public License
 * @version    $id$
 */
/**
 * Provides global access to Tera-WURFL Constants
 * @package TeraWurfl
 *
 */
class WurflConstants{
	
	public static $GENERIC = "generic";
	public static $GENERIC_XHTML = "generic_xhtml";
	public static $GENERIC_WEB_BROWSER = "generic_web_browser";
	public static $SIMPLE_DESKTOP_UA = "TeraWurflSimpleDesktopMatcher/";
	
	/**
	 * These mobile browser strings will be compared case-insensitively, so keep them all lowercase for faster searching
	 * @var Array MOBILE_BROWSERS
	 */
	public static $MOBILE_BROWSERS = array(
		'cldc',
		'symbian',
		'midp',
		'j2me',
		'mobile',
		'wireless',
		'palm',
		'phone',
		'pocket pc',
		'pocketpc',
		'netfront',
		'bolt',
		'iris',
		'brew',
		'openwave',
		'windows ce',
		'wap2.',
		'android',
		'opera mini',
		'opera mobi',
		'maemo',
		'fennec',
		'blazer',
		'vodafone',
		'wp7',
		'armv',
	);
	public static $DESKTOP_BROWSERS = array(
		'slcc1',
		'.net clr',
		'wow64',
		'media center pc',
		'funwebproducts',
		'macintosh',
		'aol 9.',
		'america online browser',
		'googletoolbar',
	);
	public static $ROBOTS = array(
		'bot',
		'crawler',
		'spider',
		'novarra',
		'transcoder',
		'yahoo! searchmonkey',
		'yahoo! slurp',
		'feedfetcher-google',
		'toolbar',
		'mowser'
	);
}