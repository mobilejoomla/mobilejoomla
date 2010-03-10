<?php
/*
 * Tera_WURFL - PHP MySQL driven WURFL
 * 
 * Tera-WURFL was written by Steve Kamerman, and is based on the
 * Java WURFL Evolution package by Luca Passani and WURFL PHP Tools by Andrea Trassati.
 * This version uses a MySQL database to store the entire WURFL file, multiple patch
 * files, and a persistent caching mechanism to provide extreme performance increases.
 * 
 * @package TeraWurfl
 * @author Steve Kamerman, stevekamerman AT gmail.com
 * @version Stable 2.0.0 $Date: 2009/11/13 23:59:59
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 * $Id: OperaUserAgentMatcher.php,v 1.2 2008/03/01 00:05:25 kamermans Exp $
 * $RCSfile: OperaUserAgentMatcher.php,v $
 * 
 * Based On: Java WURFL Evolution by Luca Passani
 *
 */
class OperaUserAgentMatcher extends UserAgentMatcher {
	public function __construct(TeraWurfl $wurfl){
		parent::__construct($wurfl);
	}
	public function applyConclusiveMatch($ua) {
		$tolerance = 5;
		$this->wurfl->toLog("Applying ".get_class($this)." Conclusive Match: LD with threshold $tolerance",LOG_INFO);
		return $this->ldMatch($ua, $tolerance);
	}
	public function recoveryMatch($ua){
		$this->wurfl->toLog("Applying ".get_class($this)." Recovery Match",LOG_INFO);
		if(UserAgentUtils::checkIfContains($ua,"Opera/10")){
			return "opera_10";
		}elseif(UserAgentUtils::checkIfContains($ua,"Opera/9")){
			return "opera_9";
		}elseif(UserAgentUtils::checkIfContains($ua,"Opera/8")){
			return "opera_8";
		}elseif(UserAgentUtils::checkIfContains($ua,"Opera/7")){
			return "opera_7";
		}else{
			return "opera";
		}
	}
}
?>