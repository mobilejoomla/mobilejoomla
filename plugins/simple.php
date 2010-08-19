<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version		###VERSION###
 * @license		###LICENSE###
 * @copyright	###COPYRIGHT###
 * @date		###DATE###
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgMobileSimple extends JPlugin
{
	function plgMobileSimple(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onDeviceDetection(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		if(empty($useragent))
			return;

		$iphone_list = array ('Mozilla/5.0 (iPod;',
		                      'Mozilla/5.0 (iPod touch;',
		                      'Mozilla/5.0 (iPhone;',
		                      'Apple iPhone ',
		                      'Mozilla/5.0 (iPhone Simulator;',
		                      'Mozilla/5.0 (Aspen Simulator;',
		                      'Mozilla/5.0 (device; U; CPU iPhone OS');
		foreach($iphone_list as $iphone_ua)
			if(strpos($useragent, $iphone_ua)===0)
			{
				$MobileJoomla_Device['markup'] = 'iphone';
				return;
			}

		if(((substr($useragent, 0, 10) == 'portalmmm/') || (substr($useragent, 0, 7) == 'DoCoMo/')))
		{
			$MobileJoomla_Device['markup'] = 'chtml';
			return;
		}

		if(!isset($_SERVER['HTTP_ACCEPT']))
			$mime = '';
		else
		{
			$accept = array ('xhtml' => 'application/xhtml+xml', 'html' => 'text/html', 'wml' => 'text/vnd.wap.wml', 'mhtml' => 'application/vnd.wap.xhtml+xml');
			$c = array ();
			foreach($accept as $mime_lang => $mime_type)
			{
				$c[$mime_lang] = 1;
				if(stristr($_SERVER['HTTP_ACCEPT'], $mime_type))
				{
					$c[$mime_lang]++;
					if(preg_match('|'.str_replace(array ('/', '.', '+'), array ('\/', '\.', '\+'), $mime_type).';q=0(\.[1-9]+)|i', $_SERVER['HTTP_ACCEPT'], $matches))
						$c[$mime_lang] -= (float) $matches[1];
				}
			}
			arsort($c, SORT_NUMERIC);
			if(array_sum($c) == count($c))
				$mime = 'html';
			else
			{
				$max = max($c);
				foreach($c as $type => $val)
					if($val != $max) unset($c[$type]);
				if(array_key_exists('html', $c))
					$mime = 'html';
				elseif(array_key_exists('xhtml', $c))
					$mime = 'xhtml';
				elseif(array_key_exists('mhtml', $c))
					$mime = 'mhtml';
				elseif(array_key_exists('wml', $c))
					$mime = 'wml';
			}
			$MobileJoomla_Device['mimetype'] = $accept[$mime];
		}
		switch($mime)
		{
			case 'wml':
				$MobileJoomla_Device['markup'] = 'wml';
				break;
			case 'mhtml':
			case 'xhtml':
				$MobileJoomla_Device['markup'] = 'xhtml';
				break;
			default:
				$MobileJoomla_Device['markup'] = '';
		}
	}
}
