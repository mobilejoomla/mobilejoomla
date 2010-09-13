<?php
/**
 * Tera_WURFL - PHP MySQL driven WURFL
 * 
 * Tera-WURFL was written by Steve Kamerman, and is based on the
 * Java WURFL Evolution package by Luca Passani and WURFL PHP Tools by Andrea Trassati.
 * This version uses a MySQL database to store the entire WURFL file, multiple patch
 * files, and a persistent caching mechanism to provide extreme performance increases.
 * 
 * @package TeraWurfl
 * @author Steve Kamerman <stevekamerman AT gmail.com>
 * @version Stable 2.1.3 $Date: 2010/07/29 20:36:29
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
    
    public static $ACCEPT_HEADER_VND_WAP_XHTML_XML = "application/vnd.wap.xhtml+xml";
    public static $ACCEPT_HEADER_XHTML_XML = "application/xhtml+xml";
    public static $ACCEPT_HEADER_TEXT_HTML = "application/text+html";

    public static $XHTML = "xhtml";
    
    public static $XHTML_ADVANCED = "xhtml_level_3_4";
    public static $XHTML_SIMPLE = "xhtml_level_1_2";
    public static $WML = "xhtml_level_-1_0";

    public static $XHTML_SUPPORT_LEVEL = "xhtml_support_level";
    public static $RESOLUTION_WIDTH = "resolution_width";
    
    /**
     * These mobile browser strings will be compared case-insensitively, so keep them all lowercase for faster searching
     * @var Array MOBILE_BROWSERS
     */
    public static $MOBILE_BROWSERS = array('cldc','symbian','midp','j2me','mobile','wireless','palm','phone','pocket pc','pocketpc',
    	'netfront','bolt','iris','brew','openwave','windows ce','wap2.','android','opera mini','opera mobi','maemo','fennec','blazer','vodafone');
    public static $DESKTOP_BROWSERS = array('slcc1','.net clr','trident/4','media center pc','funwebproducts','macintosh','wow64','aol 9.','america online browser','googletoolbar');
    public static $ROBOTS = array('bot','crawler','spider','novarra','transcoder','yahoo! searchmonkey','yahoo! slurp','feedfetcher-google','toolbar','mowser');
    
    public function __construct(){
    	
    }
}
