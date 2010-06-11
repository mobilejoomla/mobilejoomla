<?php
/**
 * Tera_WURFL - PHP MySQL driven WURFL
 * 
 * Tera-WURFL was written by Steve Kamerman, and is based on the
 * Java WURFL Evolution package by Luca Passani and WURFL PHP Tools by Andrea Trassati.
 * This version uses a MySQL database to store the entire WURFL file, multiple patch
 * files, and a persistent caching mechanism to provide extreme performance increases.
 * 
 * @package TeraWurflUserAgentMatchers
 * @author Steve Kamerman <stevekamerman AT gmail.com>
 * @version Stable 2.1.2 $Date: 2010/05/14 15:53:02
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 */
/**
 * Provides a specific user agent matching technique
 * @package TeraWurflUserAgentMatchers
 */
class FirefoxUserAgentMatcher extends UserAgentMatcher {
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		$matches = array();
		if(preg_match('/Firefox\/(\d)\.(\d)/',$ua,$matches)){
			if(TeraWurflConfig::$SIMPLE_DESKTOP_ENGINE_ENABLE){
				return WurflConstants::$GENERIC_WEB_BROWSER;
			}
			switch($matches[1]){
				// cases are intentionally out of sequnce for performance
				case 3:
					return ($matches[2]==5)? 'firefox_3_5': 'firefox_3';
					break;
				case 2:
					return 'firefox_2';
					break;
				case 1:
					return ($matches[2]==5)? 'firefox_1_5': 'firefox_1';
					break;
				default:
					//return 'firefox';
					break;
			}
		}
		$tolerance = 5;
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: LD with threshold $tolerance",LOG_INFO);
		return $this->ldMatch($ua, $tolerance);
	}
}
