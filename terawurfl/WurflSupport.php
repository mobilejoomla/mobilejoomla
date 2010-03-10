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
 * $Id: WurflSupport.php,v 1.3 2008/03/01 00:05:25 kamermans Exp $
 * $RCSfile: WurflSupport.php,v $
 * 
 * Based On: Java WURFL Evolution by Luca Passani
 *
 */
class WurflSupport{
	
	// Properties
	public $errors;
	
	// Constructor
	public function __construct(){
		$this->errors = array();
	}
	
	// Public Methods
	public static function getUserAgent(){
		$userAgent = '';
		if(isset($_GET['UA'])){
			$userAgent = $_GET['UA'];
		}elseif(isset($_SERVER['HTTP_X_DEVICE_USER_AGENT'])){
			$userAgent = $_SERVER['HTTP_X_DEVICE_USER_AGENT'];
		}elseif(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])){
			$userAgent = $_SERVER['HTTP_X_OPERAMINI_PHONE_UA'];
		}else{
			$userAgent = $_SERVER['HTTP_USER_AGENT'];
		}
		return $userAgent;
	}
	
	public static function getAcceptHeader(){
		if(isset($_GET['ACCEPT'])){
			return $_GET['ACCEPT'];
		}else{
			return $_SERVER['HTTP_ACCEPT'];
		}
	}
	
	public static function getUAProfile(){
		return isset($_SERVER['X-WAP-PROFILE'])?$_SERVER['X-WAP-PROFILE']:'';
	}
	
	public static function isUplink(){
		$ua = self::getUserAgent();
		if(strpos($ua,"UP.Link")!==false){
			return true;
		}else{
			return false;
		}
	}
	
	public static function checkCapability($capability){
		// See if it exists in capa DB
	}
	
	public static function formatBytes($bytes){
	    $unim = array("B","KB","MB","GB","TB","PB");
	    $c = 0;
	    while ($bytes>=1024) {
	        $c++;
	        $bytes = $bytes/1024;
	    }
	    return number_format($bytes,($c ? 2 : 0),".",",")." ".$unim[$c];
	}
	public static function formatBitrate($bytes,$seconds){
		$unim = array("bps","Kbps","Mbps","Gbps","Tbps","Pbps");
		$bits = $bytes * 8;
		$bps = $bits / $seconds;
	    $c = 0;
		while ($bps>=1000) {
	        $c++;
	        $bps = $bps/1000;
	    }
	    return number_format($bps,($c ? 2 : 0),".",",")." ".$unim[$c];
	}
	public static function showBool($var){
		if($var === true)return("true");
		if($var === false)return("false");
		return($var);
	}
	public static function showLogLevel($num){
		$log_arr = array(1=>"LOG_CRIT",4=>"LOG_ERR",5=>"LOG_WARNING",6=>"LOG_NOTICE");
		return($log_arr[$num]);
	}	
	// Private Methods
	
	
}
?>