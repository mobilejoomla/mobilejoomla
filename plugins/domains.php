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

class plgMobileDomains extends JPlugin
{
	function plgMobileDomains(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onDeviceDetection(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		// Check for special domains
		if($MobileJoomla_Settings['domains'] == '1')
		{
			$parsed = parse_url(JURI::base());
			$path = isset($parsed['path']) ? $parsed['path'] : '';
			$http = isset($parsed['scheme']) ? $parsed['scheme'] : 'http';
			$domain_xhtml = $MobileJoomla_Settings['xhtmldomain'];
			$domain_wap = $MobileJoomla_Settings['wapdomain'];
			$domain_imode = $MobileJoomla_Settings['imodedomain'];
			$domain_iphone = $MobileJoomla_Settings['iphonedomain'];
			/** @var JRegistry $config */
			if($domain_xhtml && $_SERVER['HTTP_HOST'] == $domain_xhtml)
			{ // Smartphone (xhtml-mp/wap2) domain
				$MobileJoomla_Device['markup'] = 'xhtml';
				$config =& JFactory::getConfig();
				$config->setValue('config.live_site', $http.'://'.$_SERVER['HTTP_HOST'].$path);
			}
			elseif($domain_wap && $_SERVER['HTTP_HOST'] == $domain_wap)
			{ // WAP (wml) domain
				$MobileJoomla_Device['markup'] = 'wml';
				$config =& JFactory::getConfig();
				$config->setValue('config.live_site', $http.'://'.$_SERVER['HTTP_HOST'].$path);
			}
			elseif($domain_imode && $_SERVER['HTTP_HOST'] == $domain_imode)
			{ // iMode (chtml) domain
				$MobileJoomla_Device['markup'] = 'chtml';
				$config =& JFactory::getConfig();
				$config->setValue('config.live_site', $http.'://'.$_SERVER['HTTP_HOST'].$path);
			}
			elseif($domain_iphone && $_SERVER['HTTP_HOST'] == $domain_iphone)
			{ // iPhone/iPod domain
				$MobileJoomla_Device['markup'] = 'iphone';
				$config =& JFactory::getConfig();
				$config->setValue('config.live_site', $http.'://'.$_SERVER['HTTP_HOST'].$path);
			}
			elseif($MobileJoomla_Device['markup']!=='') //Redirect to special domain
			{
				/** @var JSite $mainframe */
				global $mainframe;
				if($MobileJoomla_Settings['xhtmlredirect'] && ($MobileJoomla_Device['markup'] == 'xhtml') && $domain_xhtml)
					$mainframe->redirect($http.'://'.$domain_xhtml.$path);

				if($MobileJoomla_Settings['wapredirect'] && ($MobileJoomla_Device['markup'] == 'wml') && $domain_wap)
					$mainframe->redirect($http.'://'.$domain_wap.$path);

				if($MobileJoomla_Settings['imoderedirect'] && ($MobileJoomla_Device['markup'] == 'chtml') && $domain_imode)
					$mainframe->redirect($http.'://'.$domain_imode.$path);

				if($MobileJoomla_Settings['iphoneredirect'] && ($MobileJoomla_Device['markup'] == 'iphone') && $domain_iphone)
					$mainframe->redirect($http.'://'.$domain_iphone.$path);
			}
		}
	}
}
