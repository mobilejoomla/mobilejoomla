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
 * Provides static supporting functions for Tera-WURFL
 * @package TeraWurfl
 *
 */
class WurflSupport{
	
	public $errors;
	/**
	 * The HTTP Headers that Tera-WURFL will look through to find the best User Agent, if one is not specified
	 * @var Array
	 */
	public static $userAgentHeaders = array(
		'HTTP_X_DEVICE_USER_AGENT',
		'HTTP_X_ORIGINAL_USER_AGENT',
		'HTTP_X_OPERAMINI_PHONE_UA',
		'HTTP_X_SKYFIRE_PHONE',
		'HTTP_X_BOLT_PHONE_UA',
		'HTTP_USER_AGENT'
	);
	
	// Constructor
	public function __construct(){
		$this->errors = array();
	}
	
	// Public Methods
	public static function getUserAgent($source=null){
		if(is_null($source) || !is_array($source))$source = $_SERVER;
		$userAgent = '';
		if(isset($_GET['UA'])){
			$userAgent = $_GET['UA'];
		}else{
			foreach(self::$userAgentHeaders as $header){
				if(array_key_exists($header,$source) && $source[$header]){
					$userAgent = $source[$header];
					break;
				}
			}
		}
		return $userAgent;
	}
	
	public static function getAcceptHeader($source=null){
		if(is_null($source) || !is_array($source))$source = $_SERVER;
		if(isset($_GET['ACCEPT'])){
			return $_GET['ACCEPT'];
		}else{
			return $source['HTTP_ACCEPT'];
		}
	}
	
	public static function getUAProfile(){
		return isset($_SERVER['X-WAP-PROFILE'])?$_SERVER['X-WAP-PROFILE']:'';
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
}
