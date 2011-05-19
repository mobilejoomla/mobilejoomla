<?php
/**
 * Tera_WURFL - PHP MySQL driven WURFL
 * 
 * Tera-WURFL was written by Steve Kamerman, and is based on the
 * Java WURFL Evolution package by Luca Passani and WURFL PHP Tools by Andrea Trassati.
 * This version uses a database to store the entire WURFL file, multiple patch
 * files, and a persistent caching mechanism to provide extreme performance increases.
 * 
 * @package TeraWurfl
 * @author Steve Kamerman <stevekamerman AT gmail.com>
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
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