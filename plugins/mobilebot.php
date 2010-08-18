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
		global $mainframe;
		if($mainframe->isAdmin()) // don't use MobileJoomla in backend
			return;

		//load MobileJoomla class
		require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.class.php');

		//load config
		$MobileJoomla_Settings =& MobileJoomla::getConfig();
		$MobileJoomla_Device =& MobileJoomla::getDevice();

		// check for markup chooser module
		plgSystemMobileBot::processMarkupChange($MobileJoomla_Settings);

		JPluginHelper::importPlugin('mobile');
		$mainframe->triggerEvent('onDeviceDetection', array (&$MobileJoomla_Settings, &$MobileJoomla_Device));

		$markup = $MobileJoomla_Device['markup'];

		//get user choice
		$user_markup = plgSystemMobileBot::getUserMarkup($MobileJoomla_Settings);
		if($user_markup!==false)
			switch($user_markup)
			{
				case 'mobile':
					if(empty($markup))
						$markup = 'xhtml';
					break;
				default:
					$markup = $user_markup;
			}

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

		if(($MobileJoomla_Device['screenwidth'] == 0) || ($MobileJoomla_Device['screenheight'] == 0))
		{
			switch($markup)
			{
				case 'wml':
					$MobileJoomla_Device['screenwidth'] = 96;
					$MobileJoomla_Device['screenheight'] = 64;
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

		$MobileJoomla_Device['markup'] = $markup;
		switch($markup)
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
			$MobileJoomla->setHeader();
			/** @var JRegistry $config */
			$config =& JFactory::getConfig();
			if($MobileJoomla_Settings['caching'])
			{
				$handler = $config->getValue('config.cache_handler', 'file');
				$class = 'JCacheStorage'.ucfirst($handler);
				$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'cachestorage'.DS.$handler.'.php';
				JLoader::register($class, $path);
			}
			else
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
		else
			$MobileJoomla_Device['markup'] = false;
	}

	function onAfterRoute()
	{
		/** @var JSite $mainframe */
		global $mainframe;
		if($mainframe->isAdmin()) // don't use MobileJoomla in backend
			return;

		// don't filter RSS and non-html
		/** @var JDocument $document */
		$document =& JFactory::getDocument();
		$format = $document->getType();
		$doctype = JRequest::getVar('type', false);
		if($doctype == 'rss' || $doctype == 'atom' || (($format!=='html') && ($format!=='raw')))
		{
			//reset mobile content-type header
			unset($GLOBALS['_JRESPONSE']->headers['Content-type']);
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

		if(function_exists('json_decode'))
		{
			$content = @file_get_contents(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'extensions'.DS.'extensions.json');
			$json = json_decode($content);
			if(isset($json->extensions)) foreach($json->extensions as $extension)
			{
				if($extension->name == 'com_mobilemosets')
				{
					global $mtconf;
					$db =& JFactory::getDBO();
					require (JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_mtree'.DS.'config.mtree.class.php');
					$mtconf = new mtConfig($db);
					$mtconf->mtconfig['template']->value = 'mobile';
					$mtconf->mtconfig['template']->default = 'mobile';
				}
			}
		}

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
			$mainframe->setTemplate($template);
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

		$current = $_GET;
		unset($current['Itemid']);
		unset($current['lang']);

		/** @var JMenuSite $menu */
		$menu =& JSite::getMenu();
		$default = $menu->getDefault();
		$home = $default->query;

		if($current == $home)
		{
			$MobileJoomla->setHome(true);
			if($homepage)
			{
				$parsed = parse_url(JURI::base());
				$path = isset($parsed['path']) ? $parsed['path'] : '';

				$_SERVER['REQUEST_URI'] = $path.$homepage;
				if(substr($homepage, 0, 10) == 'index.php?')
				{
					$_SERVER['QUERY_STRING'] = substr($homepage, 10);
					foreach($current as $key => $val) //clear old variables
					{
						unset($_REQUEST[$key]);
						unset($_GET[$key]);
					}
					parse_str($_SERVER['QUERY_STRING'], $request);
					JRequest::set($request, 'get');
				}
				else
				{
					$uri = new JURI('http://'.$_SERVER['REQUEST_URI']);
					$router =& $mainframe->getRouter();
					$result = $router->parse($uri);
					JRequest::set($result, 'get', false);
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
			case '':
			case 'mobile':
			case 'xhtml':
			case 'iphone':
			case 'wml':
			case 'imode':
				break;
			default:
				$markup = false;
		}
		return $markup;
	}

	function processMarkupChange(&$MobileJoomla_Settings)
	{
		/** @var JSite $mainframe */
		global $mainframe;
		if((@$_GET['option'] == 'com_mobilejoomla') && (@$_GET['task'] == 'setmarkup') &&
				isset($_GET['markup']) && isset($_GET['return']))
		{
			$mainframe->setUserState('mobilejoomla.forged_markup', 'yes');
			$markup = plgSystemMobileBot::CheckMarkup($_GET['markup']);
			if($markup!==false)
				setcookie('mj.markup', $markup, time()+365*24*60*60);
			else
				setcookie('mj.markup', '', time()-365*24*60*60);
			$mainframe->setUserState('mobilejoomla.markup', $markup);
			$return = base64_decode($_GET['return']);
			$mainframe->redirect($return);
		}
	}

	function getUserMarkup(&$MobileJoomla_Settings)
	{
		/** @var JSite $mainframe */
		global $mainframe;
		$markup = plgSystemMobileBot::CheckMarkup($mainframe->getUserState('mobilejoomla.markup', false));
		if($markup===false && isset($_COOKIE['mjmarkup']))
		{
			if(($markup = plgSystemMobileBot::CheckMarkup($_COOKIE['mjmarkup']))!==false)
				$mainframe->setUserState('mobilejoomla.markup', $markup);
		}
		return $markup;
	}

	function onAfterRender()
	{
		if(!defined('_MJ')) return;

		$text = JResponse::getBody();

		/** @var JSite $mainframe */
		global $mainframe;
		$mainframe->triggerEvent('onMobilePagePrepare', array (&$text));

		/** @var MobileJoomla $MobileJoomla */
		$MobileJoomla =& MobileJoomla::getInstance();
		$text = $MobileJoomla->processPage($text);

		JResponse::setBody($text);
	}
}
