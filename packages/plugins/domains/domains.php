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
	var $_domain_markup = null;

	function plgMobileDomains(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function setConfig($name, $value)
	{
		static $is_joomla15;
		if(!isset($is_joomla15))
			$is_joomla15 = (substr(JVERSION,0,3) == '1.5');

		/** @var JRegistry $config */
		$config = JFactory::getConfig();
		if($is_joomla15)
			return $config->setValue('config.'.$name, $value);
		else
			return $config->set($name, $value);
	}

	function onAfterDeviceDetection(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		$host = $_SERVER['HTTP_HOST'];
		if(empty($host))
			return;

		$this->getSchemePath($http, $base);

		// Check for current domain
		$markup = $MobileJoomla_Device['markup'];
		if(isset($MobileJoomla_Settings[$markup.'.domain'])
			&& $host == $MobileJoomla_Settings[$markup.'.domain'])
		{
			$this->setConfig('live_site', $http.'://'.$host.$base);
			$this->_domain_markup = $markup;
			return;
		}

		// Mobile domains
		$markups_list = array('xhtml', 'iphone', 'tablet', 'chtml', 'wml');
		foreach($markups_list as $markup)
		{
			if(isset($MobileJoomla_Settings[$markup.'.domain'])
				&& $host == $MobileJoomla_Settings[$markup.'.domain']
				&& plgSystemMobileBot::checkMarkup($markup) !== false)
			{
				$this->_domain_markup = $MobileJoomla_Device['markup'];
				$MobileJoomla_Device['markup'] = $markup;
				$this->setConfig('live_site', $http.'://'.$host.$base);
				return;
			}
		}
		
		// Desktop domain
		$app = JFactory::getApplication();
		// is it non-first visit? Then don't redirect
		if($app->getUserState('mobilejoomla.markup') !== null)
		{
			$markup = $MobileJoomla_Device['markup'];
			if(isset($MobileJoomla_Settings[$markup.'.domain'])
				&& empty($MobileJoomla_Settings[$markup.'.domain']))
			{
				return;
			}
			$MobileJoomla_Device['markup'] = '';
		}
	}

	function onBeforeMobileMarkupInit(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		$host = $_SERVER['HTTP_HOST'];
		if(empty($host))
			return;

		$markup = $MobileJoomla_Device['markup'];

		if($this->_domain_markup !== null
			&& (!isset($MobileJoomla_Settings[$markup.'.domain'])
				|| $host != $MobileJoomla_Settings[$markup.'.domain']))
		{
			$MobileJoomla_Device['markup'] = $this->_domain_markup;
		}

		if($markup == '' || @$_SERVER['REQUEST_METHOD']=='POST')
			return;

		$app = JFactory::getApplication();
		if(isset($MobileJoomla_Settings[$markup.'.domain']))
		{
			$domain_markup = $MobileJoomla_Settings[$markup.'.domain'];
			if(!empty($domain_markup) && $host != $domain_markup)
			{
				$uri      = JUri::getInstance();
				$protocol = $uri->toString(array('scheme'));
				$path     = $uri->toString(array('path','query'));

				$app->redirect($protocol . $domain_markup . $path);
			}
		}
	}

	function getSchemePath(&$http, &$base)
	{
		if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off'))
			$http = 'https';
		else
			$http = 'http';

		$app = JFactory::getApplication();
		$live_url = $app->getCfg('live_site');
		if($live_url)
		{
			$parsed = parse_url($live_url);
			if($parsed !== false)
			{
				$base = isset($parsed['path']) ? $parsed['path'] : '/';
				return;
			}
		}

		if(strpos(php_sapi_name(), 'cgi') !== false && !empty($_SERVER['REQUEST_URI']) &&
				(!ini_get('cgi.fix_pathinfo') || version_compare(PHP_VERSION, '5.2.4', '<')))
			$base =  rtrim(dirname(str_replace(array('"','<','>',"'"), '', $_SERVER['PHP_SELF'])), '/\\');
		else
			$base =  rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
	}
}
