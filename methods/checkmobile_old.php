<?php
/**
 * Kuneri Mobile Joomla! for Joomla!1.5
 * http://www.mobilejoomla.com/
 *
 * @version		0.9.0
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	Copyright (C) 2008-2009 Kuneri Ltd. All rights reserved.
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

function CheckMobile()
{
	if( (isset($_SERVER['HTTP_USER_AGENT']))&&
		((substr($_SERVER['HTTP_USER_AGENT'],0,10)=='portalmmm/')||
		 (substr($_SERVER['HTTP_USER_AGENT'],0,7)=='DoCoMo/')))
			return 'chtml';
	$devices = array(
	'up.browser',	'windows ce',	'blackberry','midp',	'smartphone',
	'wap',			'handheld',		'mmp',		'mobile',	'palm',
	'acer',			'alcatel',		'audiovox',	'avantgo',	'blazer',
	'cdm',			'digital paths','elaine',	'epoc',		'ericsson',
	'go.web',		'handspring',	'kyocera',	'lg',		'motorola',
	'nec-',			'nokia',		'o2',		'openwave',	'opera mini',
	'operamini',	'opwv',			'panasonic','pda',		'phone',
	'playstation portable','pocket','psp',		'qci',		'rover',
	'sagem',		'sanyo',		'samsung',	'sec',		'sendo',
	'sharp',		'sonyericsson',	'symbian',	'telit',	'tsm',
	'up-browser',	'up.link',		'vodafone',	'xiino'
	);
	if(isset($_SERVER['HTTP_USER_AGENT']))
	{
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		foreach($devices as $browser)
			if(false!==strpos($agent,$browser))
				return 'xhtml';
	}
	return '';
}
?>