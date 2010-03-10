<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version		###VERSION###
 * @license		###LICENSE###
 * @copyright	###COPYRIGHT###
 * @date        ###DATE###
 */

/*
 Standalone version of get_browser() in PHP
  http://www.php.net/manual/function.get-browser.php
 Detection of the capacities of a Web browser client.
 Requires a compatible browscap.ini database,
  such as php_browscap.ini on
  http://browsers.garykeith.com/downloads.asp
Version 1.3.1, 2006-09-09, http://alexandre.alapetite.net/doc-alex/php-local-browscap/
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

function _sortBrowscap($a,$b)
{
	$sa=strlen($a);
	$sb=strlen($b);
	if ($sa>$sb) return -1;
	elseif ($sa<$sb) return 1;
	else return strcasecmp($a,$b);
}

function _lowerBrowscap($r){return array_change_key_case($r,CASE_LOWER);}

function get_browser_local($user_agent=null,$return_array=false,$db='./browscap.ini')
{//http://alexandre.alapetite.net/doc-alex/php-local-browscap/
	if(($user_agent==null)&&isset($_SERVER['HTTP_USER_AGENT']))
		$user_agent=$_SERVER['HTTP_USER_AGENT'];
	$browscapIni=parse_ini_file($db,true); //Get php_browscap.ini on http://browsers.garykeith.com/downloads.asp
	$browscapPath=$db;
	uksort($browscapIni,'_sortBrowscap');
	$browscapIni=array_map('_lowerBrowscap',$browscapIni);
	$cap=null;
	foreach($browscapIni as $key=>$value)
	{
		if(($key!='*')&&(!array_key_exists('parent',$value)))
			continue;
		$keyEreg='^'.str_replace(
			array('\\','.','?','*','^','$','[',']','|','(',')','+','{','}','%'),
			array('\\\\','\\.','.','.*','\\^','\\$','\\[','\\]','\\|','\\(','\\)','\\+','\\{','\\}','\\%'),
			$key).'$';
		if(preg_match('%'.$keyEreg.'%i',$user_agent))
		{
			$cap=array('browser_name_regex'=>strtolower($keyEreg),
					   'browser_name_pattern'=>$key) + $value;
			$maxDeep=8;
			while(array_key_exists('parent',$value)&&array_key_exists($parent=$value['parent'],$browscapIni)&&(--$maxDeep>0))
				$cap+=($value=$browscapIni[$parent]);
			break;
		}
	}
	return $return_array ? $cap : (object)$cap;
}

function CheckMobile()
{
	if(!isset($_SERVER['HTTP_USER_AGENT']))
		return '';
	$browser=get_browser_local($_SERVER['HTTP_USER_AGENT'],true,JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'methods'.DS.'php_browscap.ini');
	if(isset($browser['isMobileDevice'])&&($browser['isMobileDevice']==true))
		return 'xhtml';
	return '';
}