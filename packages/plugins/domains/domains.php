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
		$this->getSchemePath($http, $base);

		$markup = $MobileJoomla_Device['markup'];
		$host = $_SERVER['HTTP_HOST'];
		$domain_xhtml = $MobileJoomla_Settings['xhtml.domain'];
		$domain_wml = $MobileJoomla_Settings['wml.domain'];
		$domain_chtml = $MobileJoomla_Settings['chtml.domain'];
		$domain_iphone = $MobileJoomla_Settings['iphone.domain'];

		// Check for current domain
		if(($markup=='xhtml' && $domain_xhtml && $host==$domain_xhtml) ||
		   ($markup=='wml' && $domain_wml && $host==$domain_wml) ||
		   ($markup=='chtml' && $domain_chtml && $host==$domain_chtml) ||
		   ($markup=='iphone' && $domain_iphone && $host==$domain_iphone) )
		{
			$this->setConfig('live_site', $http.'://'.$host.$base);
			$this->_domain_markup = $markup;
			return;
		}

		if($domain_xhtml && $host == $domain_xhtml)
		{ // Smartphone (xhtml-mp/wap2) domain
			$MobileJoomla_Device['markup'] = 'xhtml';
			$this->setConfig('live_site', $http.'://'.$host.$base);
			$this->_domain_markup = $markup;
		}
		elseif($domain_iphone && $host == $domain_iphone)
		{ // iPhone/iPod domain
			$MobileJoomla_Device['markup'] = 'iphone';
			$this->setConfig('live_site', $http.'://'.$host.$base);
			$this->_domain_markup = $markup;
		}
		elseif($domain_chtml && $host == $domain_chtml)
		{ // iMode (chtml) domain
			$MobileJoomla_Device['markup'] = 'chtml';
			$this->setConfig('live_site', $http.'://'.$host.$base);
			$this->_domain_markup = $markup;
		}
		elseif($domain_wml && $host == $domain_wml)
		{ // WAP (wml) domain
			$MobileJoomla_Device['markup'] = 'wml';
			$this->setConfig('live_site', $http.'://'.$host.$base);
			$this->_domain_markup = $markup;
		}
		else
		{ // Desktop domain
			$app = JFactory::getApplication();
			// is it non-first visit? Then don't redirect
			if($app->getUserState('mobilejoomla.markup') !== null)
			{
				if( ($MobileJoomla_Device['markup']=='xhtml'  && $domain_xhtml =='') ||
					($MobileJoomla_Device['markup']=='iphone' && $domain_iphone=='') ||
					($MobileJoomla_Device['markup']=='chtml'  && $domain_chtml =='') ||
					($MobileJoomla_Device['markup']=='wml'    && $domain_wml   =='') )
						return;
				$MobileJoomla_Device['markup'] = '';
			}
		}
	}

	function onBeforeMobileMarkupInit(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		$markup = $MobileJoomla_Device['markup'];
		$host = $_SERVER['HTTP_HOST'];
		$domain_xhtml = $MobileJoomla_Settings['xhtml.domain'];
		$domain_wml = $MobileJoomla_Settings['wml.domain'];
		$domain_chtml = $MobileJoomla_Settings['chtml.domain'];
		$domain_iphone = $MobileJoomla_Settings['iphone.domain'];

		if($this->_domain_markup !== null)
		{
			if(!(($markup=='xhtml' && $domain_xhtml && $host==$domain_xhtml) ||
				 ($markup=='wml' && $domain_wml && $host==$domain_wml) ||
				 ($markup=='chtml' && $domain_chtml && $host==$domain_chtml) ||
				 ($markup=='iphone' && $domain_iphone && $host==$domain_iphone)))
				$MobileJoomla_Device['markup'] = $this->_domain_markup;
		}

		if($markup == '' || @$_SERVER['REQUEST_METHOD']=='POST')
			return;

		$http = 'http';
		if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off'))
			$http .= 's';

		$uri = JURI::getInstance();
		$parsed = parse_url($uri->toString());
		$path = isset($parsed['path']) ? $parsed['path'] : '/';

		$app = JFactory::getApplication();
		switch($markup)
		{
		case 'xhtml':
			if($domain_xhtml && $host != $domain_xhtml)
				$app->redirect($http.'://'.$domain_xhtml.$path);
			break;
		case 'wml':
			if($domain_wml && $host != $domain_wml)
				$app->redirect($http.'://'.$domain_wml.$path);
			break;
		case 'chtml':
			if($domain_chtml && $host != $domain_chtml)
				$app->redirect($http.'://'.$domain_chtml.$path);
			break;
		case 'iphone':
			if($domain_iphone && $host != $domain_iphone)
				$app->redirect($http.'://'.$domain_iphone.$path);
			break;
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
