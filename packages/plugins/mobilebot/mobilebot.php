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

class _CacheStub
{
	function setCaching($bool){}
	function get(){return false;}
	function store(){}
}

class plgSystemMobileBot extends JPlugin
{
	function plgSystemMobileBot(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onAfterInitialise()
	{
		/** @var JSite $mainframe */
		$mainframe =& JFactory::getApplication();
		if($mainframe->isAdmin()) // don't use MobileJoomla in backend
			return;

		$version = new JVersion;
		$is_joomla15 = (substr($version->getShortVersion(),0,3) == '1.5');

		//load MobileJoomla class
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.class.php');

		//load config
		$MobileJoomla_Settings =& MobileJoomla::getConfig();
		$MobileJoomla_Device =& MobileJoomla::getDevice();

		JPluginHelper::importPlugin('mobile');

		$cached_settings = $mainframe->getUserState('mobilejoomla.settings', false);
		$cached_device = $mainframe->getUserState('mobilejoomla.device', false);
		if(!is_array($cached_settings) || !is_array($cached_device))
		{
			$mainframe->triggerEvent('onDeviceDetection', array (&$MobileJoomla_Settings, &$MobileJoomla_Device));
			$mainframe->setUserState('mobilejoomla.settings', serialize($MobileJoomla_Settings));
			$mainframe->setUserState('mobilejoomla.device', serialize($MobileJoomla_Device));
		}
		else
		{
			if($is_joomla15)
				Jloader::register('TeraWurfl', JPATH_PLUGINS.DS.'mobile'.DS.'terawurfl'.DS.'TeraWurfl.php');
			else
				Jloader::register('TeraWurfl', JPATH_PLUGINS.DS.'mobile'.DS.'terawurfl'.DS.'terawurfl'.DS.'TeraWurfl.php');
			$MobileJoomla_Settings = unserialize($cached_settings);
			$MobileJoomla_Device = unserialize($cached_device);
		}

		$MobileJoomla_Device['real_markup'] = $MobileJoomla_Device['markup'];

		$mainframe->triggerEvent('onAfterDeviceDetection', array (&$MobileJoomla_Settings, &$MobileJoomla_Device));

		$markup = $MobileJoomla_Device['markup'];
		$MobileJoomla_Device['default_markup'] = $markup;

		//get user choice
		$user_markup = $this->getUserMarkup();
		if($user_markup!==false)
			$markup = $user_markup;

		// template preview
		if(isset($_GET['template']) && $_GET['template'] != '')
		{
			switch($_GET['template'])
			{
			case $MobileJoomla_Settings['xhtmltemplate']:
				$markup = 'xhtml';
				break;
			case $MobileJoomla_Settings['iphonetemplate']:
				$markup = 'iphone';
				break;
			case $MobileJoomla_Settings['waptemplate']:
				$markup = 'wml';
				break;
			case $MobileJoomla_Settings['imodetemplate']:
				$markup = 'chtml';
				break;
			}
		}

		$MobileJoomla_Device['markup'] = $markup;

		if(($MobileJoomla_Device['screenwidth'] == 0) || ($MobileJoomla_Device['screenheight'] == 0))
		{
			switch($markup)
			{
				case 'wml':
					$MobileJoomla_Device['screenwidth'] = 64;
					$MobileJoomla_Device['screenheight'] = 96;
					break;
				case 'chtml':
					$MobileJoomla_Device['screenwidth'] = 120;
					$MobileJoomla_Device['screenheight'] = 128;
					break;
				case 'xhtml':
					$MobileJoomla_Device['screenwidth'] = 240;
					$MobileJoomla_Device['screenheight'] = 320;
					break;
				case 'iphone':
					$MobileJoomla_Device['screenwidth'] = 320;
					$MobileJoomla_Device['screenheight'] = 480;
					break;
			}
		}

		if($MobileJoomla_Device['imageformats']===null)
		{
			switch($markup)
			{
				case 'wml':
					$MobileJoomla_Device['imageformats'] = array ('wbmp');
					break;
				case 'chtml':
					$MobileJoomla_Device['imageformats'] = array ('gif');
					break;
				case 'xhtml':
				case 'iphone':
					$MobileJoomla_Device['imageformats'] = array ('png', 'gif', 'jpg');
					break;
			}
		}
		if(count($MobileJoomla_Device['imageformats']) == 0)
		{
			$MobileJoomla_Settings['tmpl_wap_img'] = 1;
			$MobileJoomla_Settings['tmpl_imode_img'] = 1;
			$MobileJoomla_Settings['tmpl_xhtml_img'] = 1;
			$MobileJoomla_Settings['tmpl_iphone_img'] = 1;
		}

		$mainframe->triggerEvent('onBeforeMobileMarkupInit', array (&$MobileJoomla_Settings, &$MobileJoomla_Device));

		$this->updateUserMarkup();

		switch($MobileJoomla_Device['markup'])
		{
			case 'xhtml':
				$MobileJoomla =& MobileJoomla::getInstance('xhtmlmp');
				break;
			case 'wml':
				$MobileJoomla =& MobileJoomla::getInstance('wml');
				break;
			case 'chtml':
				$MobileJoomla =& MobileJoomla::getInstance('chtml');
				break;
			case 'iphone':
				$MobileJoomla =& MobileJoomla::getInstance('iphone');
				break;
		}

		if(isset($MobileJoomla))
		{
			// set headers here to be compatible with System-Cache
			$MobileJoomla->setHeader();

			/** @var JRegistry $config */
			$config =& JFactory::getConfig();
			if($MobileJoomla_Settings['mobile_sitename'])
				$config->setValue('sitename', $MobileJoomla_Settings['mobile_sitename']);

			if(!$is_joomla15) //Joomla!1.6+
			{
				if(!$MobileJoomla_Settings['caching'])
					$config->setValue('config.caching', false);

				$cachekey = $MobileJoomla_Device['markup'].'_'.
							$MobileJoomla_Device['screenwidth'].'_'.
							$MobileJoomla_Device['screenheight'].'_'.
							implode('', $MobileJoomla_Device['imageformats']);
				$this->setRequestVar('mjcachekey', $cachekey);
				$registeredurlparams = $mainframe->get('registeredurlparams');
				if(empty($registeredurlparams))
					$registeredurlparams = new stdClass();
				$registeredurlparams->mjcachekey = 'CMD';
				$mainframe->set('registeredurlparams', $registeredurlparams);
			}
			else //Joomla!1.5
			{
				if($MobileJoomla_Settings['caching'])
				{
					$handler = $config->getValue('config.cache_handler', 'file');
					$class = 'JCacheStorage'.ucfirst($handler);
					$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'cachestorage'.DS.$handler.'.php';
					jimport('joomla.cache.storage');
					JLoader::register($class, $path);
				}
				else //disable System-Cache plugin
				{
					$config->setValue('config.caching', false);
					$dispatcher =& JDispatcher::getInstance();
					foreach($dispatcher->_observers as $index => $object)
					{
						if(is_a($object, 'plgSystemCache'))
						{
							$object->_cache = new _CacheStub();
							unset($dispatcher->_observers[$index]);
							break;
						}
					}
				}
			}
			$router =& $mainframe->getRouter();
			$router->attachBuildRule(array($this, 'buildRule'));
		}
		else
			$MobileJoomla_Device['markup'] = false;
	}

	function buildRule(&$router, &$uri)
	{
		$MobileJoomla_Device =& MobileJoomla::getDevice();
		if($MobileJoomla_Device['markup'] != $MobileJoomla_Device['default_markup'])
			$uri->setVar('device', $MobileJoomla_Device['markup']);
	}

	function onAfterRoute()
	{
		/** @var JSite $mainframe */
		$mainframe =& JFactory::getApplication();
		if($mainframe->isAdmin()) // don't use MobileJoomla in backend
			return;

		$version = new JVersion;
		$is_joomla15 = (substr($version->getShortVersion(),0,3) == '1.5');

		// don't filter RSS and non-html
		/** @var JDocument $document */
		$document =& JFactory::getDocument();
		$format = $document->getType();
		$doctype = JRequest::getVar('type', false);
		if($doctype == 'rss' || $doctype == 'atom' || (($format!=='html') && ($format!=='raw')))
		{
			//reset mobile content-type header
			$headers = JResponse::getHeaders();
			JResponse::clearHeaders();
			foreach($headers as $header)
				if(strtolower($header['name']) != 'content-type')
					JResponse::setHeader($header['name'], $header['value']);
			return;
		}

		// Load config
		$MobileJoomla_Settings =& MobileJoomla::getConfig();
		$MobileJoomla_Device =& MobileJoomla::getDevice();

		if($MobileJoomla_Device['markup']===false) //desktop
		{
			$pcpage = $MobileJoomla_Settings['pcpage'];
			if($pcpage && ($pcpage!=='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))
				$mainframe->redirect($pcpage);
			return;
		}

		define('_MJ', 1);
		/** @var MobileJoomla $MobileJoomla */
		$MobileJoomla =& MobileJoomla::getInstance();

		JPluginHelper::importPlugin('mobile');
		$mainframe->triggerEvent('onMobile', array (&$MobileJoomla, &$MobileJoomla_Settings, &$MobileJoomla_Device));

		switch($MobileJoomla_Device['markup'])
		{
			case 'xhtml':
				$template = $MobileJoomla_Settings['xhtmltemplate'];
				$homepage = $MobileJoomla_Settings['xhtmlhomepage'];
				$gzip = $MobileJoomla_Settings['xhtmlgzip'];
				break;
			case 'wml':
				$template = $MobileJoomla_Settings['waptemplate'];
				$homepage = $MobileJoomla_Settings['waphomepage'];
				$gzip = $MobileJoomla_Settings['wapgzip'];
				break;
			case 'chtml':
				$template = $MobileJoomla_Settings['imodetemplate'];
				$homepage = $MobileJoomla_Settings['imodehomepage'];
				$gzip = $MobileJoomla_Settings['imodegzip'];
				break;
			case 'iphone':
				$template = $MobileJoomla_Settings['iphonetemplate'];
				$homepage = $MobileJoomla_Settings['iphonehomepage'];
				$gzip = $MobileJoomla_Settings['iphonegzip'];
				break;
		}

		//Set template
		if($template)
		{
			$mainframe->setUserState('setTemplate', $template);
			$mainframe->setTemplate($template);
		}
		//Set gzip
		/** @var JRegistry $config */
		$config =& JFactory::getConfig();
		$config->setValue('config.gzip', $gzip);

		//Set headers
		JResponse::clearHeaders();
		/** @var JDocument $document */
		$document =& JFactory::getDocument();
		$document->setMimeEncoding($MobileJoomla->getContentType());
		$MobileJoomla->setHeader();

		if(JRequest::getMethod()=='POST')
			return;

		$current = $_GET;
		unset($current['device']);
		unset($current['lang']);
		unset($current['format']);
		unset($current['no_html']);
		unset($current['language']);
		unset($current['tp']);
		unset($current['limit']); // fix for sh404sef
		if(isset($current[session_name()]))
			unset($current[session_name()]);

		/** @var JMenuSite $menu */
		$menu =& JSite::getMenu();
		$default = $menu->getDefault();
		$home = $default->query;
		$home['Itemid'] = $default->id;

		if(substr($homepage, 0, 10) == 'index.php?')
		{
			parse_str(substr($homepage, 10), $mj_home);
			if(isset($mj_home['Itemid']))
			{
				$mj_home_Itemid = (int)$mj_home['Itemid'];
				$menu->setDefault($mj_home_Itemid);
			}
			if($current == $mj_home)
				$MobileJoomla->setHome(true);
		}

		if($current == $home)
		{
			$MobileJoomla->setHome(true);
			if($homepage)
			{
				if(isset($mj_home_Itemid))
				{
					global $Itemid;
					$Itemid = $mj_home_Itemid;
					$menu->setActive($Itemid);
					$mainframe->authorize($Itemid);
				}

				$_SERVER['REQUEST_URI'] = JURI::base(true).'/'.$homepage;
				if(isset($mj_home))
				{
					$_SERVER['QUERY_STRING'] = substr($homepage, 10);
					foreach($current as $key => $val) //clear old variables
					{
						unset($_REQUEST[$key]);
						unset($_GET[$key]);
					}
					JRequest::set($mj_home, 'get');
				}
				else
				{
					$url = 'http';
					$url .= (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])
							 && (strtolower($_SERVER['HTTPS'])!='off'))
							? 's' : '';
					$url .= '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

					$uri = new JURI($url);
					$router =& $mainframe->getRouter();
					$result = $router->parse($uri);
					JRequest::set($result, 'get');
				}
			}
		}
	}

	// Validate markup
	function CheckMarkup($markup)
	{
		if(($markup===false)||($markup===null))
			return false;
		switch($markup)
		{
			case 'desktop':
				return '';
			case '':
			case 'xhtml':
			case 'iphone':
			case 'wml':
			case 'chtml':
				return $markup;
		}
		return false;
	}

	function getUserMarkup()
	{
		/** @var JSite $mainframe */
		$mainframe =& JFactory::getApplication();

		$markup = false;

		if(isset($_GET['device']))
			$markup = $this->CheckMarkup($_GET['device']);

		if($markup === false)
			$markup = $this->CheckMarkup($mainframe->getUserState('mobilejoomla.markup', false));

		if($markup === false && isset($_COOKIE['mjmarkup']))
			$markup = $this->CheckMarkup($_COOKIE['mjmarkup']);

		return $markup;
	}

	function updateUserMarkup()
	{
		$MobileJoomla_Device =& MobileJoomla::getDevice();
		$markup = $MobileJoomla_Device['markup'];

		/** @var JSite $mainframe */
		$mainframe =& JFactory::getApplication();
		$mainframe->setUserState('mobilejoomla.markup', $markup);

		if($markup != $MobileJoomla_Device['default_markup'])
			setcookie('mjmarkup', $markup ? $markup : 'desktop', time()+365*24*60*60);
		else
			setcookie('mjmarkup', '', time()-365*24*60*60);
	}

	function setRequestVar($name, $value = null)
	{
		$_REQUEST[$name] = $value;
		$GLOBALS['_JREQUEST'][$name] = array('SET.REQUEST'=>true);
	}

	function onAfterRender()
	{
		if(!defined('_MJ')) return;

		$text = JResponse::getBody();

		/** @var JSite $mainframe */
		$mainframe =& JFactory::getApplication();
		$mainframe->triggerEvent('onMobilePagePrepare', array (&$text));

		/** @var MobileJoomla $MobileJoomla */
		$MobileJoomla =& MobileJoomla::getInstance();
		$MobileJoomla_Settings =& MobileJoomla::getConfig();

		$text = $MobileJoomla->processPage($text);

		JResponse::setBody($text);

		if($MobileJoomla_Settings['httpcaching'])
		{
			JResponse::allowCache(true);
			JResponse::setHeader('Vary', 'Cookie');
		}
		JResponse::setHeader('Cache-Control', 'no-transform');
	}
}
