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
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

function CheckMobile()
{
	$xhtml=strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml');
	if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'text/vnd.wap.wml')>0) || ($xhtml==false))
		return 'wml';
	if(($xhtml>0) || isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE']))
		return 'xhtml';
	if(preg_match(
			'/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|vodafone|o2|pocket|mobile|pda|psp|treo)/i',
			strtolower($_SERVER['HTTP_USER_AGENT'])))
		return 'xhtml';
	$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
	if($mobile_ua=='oper')
		return '';
	$imode_agents = array('doco','port');
	if(in_array($mobile_ua,$imode_agents))
		return 'chtml';
	$mobile_agents = array(
		'acs-','alav','alca','amoi','audi','aste','avan','benq','bird','blac',
		'blaz','brew','cell','cldc','cmd-','dang','eric','hipt','inno','ipaq',
		'java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-','maui',
		'maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-','newt',
		'noki','opwv','palm','pana','pant','pdxg','phil','play','pluc','prox',
		'qtek','qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-',
		'shar','sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli',
		'tim-','tosh','treo','tsm-','upg1','upsi','vk-v','voda','wap-','wapa',
		'wapi','wapp','wapr','webc','winw','winw','xda','xda-');
	if(in_array($mobile_ua,$mobile_agents))
		return 'xhtml';
	return '';
}