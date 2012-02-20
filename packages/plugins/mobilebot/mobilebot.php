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

		$is_joomla15 = (substr(JVERSION,0,3) == '1.5');

		//load MobileJoomla class
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'classes'.DS.'mobilejoomla.php');

		//load config
		$MobileJoomla_Settings =& MobileJoomla::getConfig();
		$MobileJoomla_Device =& MobileJoomla::getDevice();

		// check for legacy redirect
		if((@$_GET['option'] == 'com_mobilejoomla') && (@$_GET['task'] == 'setmarkup') &&
				isset($_GET['markup']) && isset($_GET['return']))
		{
			$desktop_uri = new JURI($MobileJoomla_Settings['desktop_url']);
			$uri = new JURI(base64_decode($_GET['return']));
			if(!$uri->getScheme())
				$uri->setScheme('http');
			$uri->setHost($desktop_uri->getHost());
			$uri->setPort($desktop_uri->getPort());
 			$mainframe->redirect($uri->toString());
		}

		JPluginHelper::importPlugin('mobile');

		$cached_data = $mainframe->getUserState('mobilejoomla.cache');
		if($cached_data!==null)
		{
			$cached_data = @gzinflate(@base64_decode($cached_data));
			if($cached_data!==false)
			{
				if($is_joomla15)
					Jloader::register('TeraWurfl', JPATH_PLUGINS.DS.'mobile'.DS.'terawurfl'.DS.'TeraWurfl.php');
				else
					Jloader::register('TeraWurfl', JPATH_PLUGINS.DS.'mobile'.DS.'terawurfl'.DS.'terawurfl'.DS.'TeraWurfl.php');
				$cached_data = unserialize($cached_data);
			}
		}

		if(is_array($cached_data))
		{
			$MobileJoomla_Settings = $cached_data['settings'];
			$MobileJoomla_Device   = $cached_data['device'];
		}
		else
		{
			$mainframe->triggerEvent('onDeviceDetection', array(&$MobileJoomla_Settings, &$MobileJoomla_Device));

			$gzlevel = 5;
			$cached_data = array('settings'=>$MobileJoomla_Settings, 'device'=>$MobileJoomla_Device);
			$cached_data = base64_encode(gzdeflate(serialize($cached_data), $gzlevel));
			$mainframe->setUserState('mobilejoomla.cache', $cached_data);
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
		$getTemplate = isset($_GET['template']) ? $_GET['template'] : null;
		if(version_compare(JVERSION,'1.7','ge'))
		{
			if($getTemplate===null && isset($_GET['templateStyle']) && is_int($_GET['templateStyle']))
			{
				$db = JFactory::getDBO();
				$query = 'SELECT template FROM #__template_styles WHERE id = '.intval($_GET['templateStyle']).' AND client_id = 0';
				$db->setQuery($query);
				$getTemplate = $db->loadResult();
			}
		}
		elseif(version_compare(JVERSION,'1.6','ge'))
		{
			if(is_int($getTemplate))
			{
				$db = JFactory::getDBO();
				$query = 'SELECT template FROM #__template_styles WHERE id = '.intval($getTemplate).' AND client_id = 0';
				$db->setQuery($query);
				$getTemplate = $db->loadResult();
			}
		}
		if($getTemplate) switch($getTemplate)
		{
		case $MobileJoomla_Settings['xhtml.template']:
			$markup = 'xhtml';
			break;
		case $MobileJoomla_Settings['iphone.template']:
			$markup = 'iphone';
			break;
		case $MobileJoomla_Settings['wml.template']:
			$markup = 'wml';
			break;
		case $MobileJoomla_Settings['chtml.template']:
			$markup = 'chtml';
			break;
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
			$MobileJoomla_Settings['wml.img'] = 1;
			$MobileJoomla_Settings['chtml.img'] = 1;
			$MobileJoomla_Settings['xhtml.img'] = 1;
			$MobileJoomla_Settings['iphone.img'] = 1;
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
			default:
				$MobileJoomla_Device['markup'] = false;
				return;
		}

		// set headers here to be compatible with System-Cache
		$MobileJoomla->setHeader();

		/** @var JRegistry $config */
		$config =& JFactory::getConfig();
		if($MobileJoomla_Settings['mobile_sitename'])
			$config->setValue($is_joomla15?'config.sitename':'sitename', $MobileJoomla_Settings['mobile_sitename']);

		if(!$is_joomla15) //Joomla!1.6+
		{
			if(!$MobileJoomla_Settings['caching'])
				$config->setValue('caching', 0);

			$cachekey = $MobileJoomla_Device['markup'].'_'.
						$MobileJoomla_Device['screenwidth'].'_'.
						$MobileJoomla_Device['screenheight'].'_'.
						(is_array($MobileJoomla_Device['imageformats'])
							? implode('', $MobileJoomla_Device['imageformats'])
							: '');
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
				$config->setValue('config.caching', 0);
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

		if(!defined('SHMOBILE_MOBILE_TEMPLATE_SWITCHED'))
			define('SHMOBILE_MOBILE_TEMPLATE_SWITCHED', 1);
	}

	function buildRule(&$router, &$uri)
	{
		$MobileJoomla_Device =& MobileJoomla::getDevice();
		if($MobileJoomla_Device['markup'] != $MobileJoomla_Device['default_markup'])
		{
			switch($uri->getVar('format'))
			{
				case 'feed':
				case 'json':
				case 'xml':
					return;
			}
			switch($uri->getVar('type'))
			{
				case 'rss':
				case 'atom':
					return;
			}
			if((is_a($router, 'shRouter') || class_exists('Sh404sefClassRouter')) &&
					$uri->getVar('Itemid') && count($uri->getQuery(true))==2) // check for sh404sef
			{
				$itemid = $uri->getVar('Itemid');
                $app =& JFactory::getApplication();
                $menu =& $app->getMenu();
				$item = $menu->getItem($itemid);
				$uri->setQuery($item->query);
				$uri->setVar('Itemid', $itemid);
				$uri->setVar('device', $MobileJoomla_Device['markup']);
			}
			else
				$uri->setVar('device', $MobileJoomla_Device['markup']);
		}
	}

	function onAfterRoute()
	{
		/** @var JSite $mainframe */
		$mainframe =& JFactory::getApplication();
		if($mainframe->isAdmin()) // don't use MobileJoomla in backend
			return;

		$is_joomla15 = (substr(JVERSION,0,3) == '1.5');

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

		jimport('joomla.environment.browser');
		$browser =& JBrowser::getInstance();
		$browser->set('_mobile', $MobileJoomla_Device['markup']!==false);

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
			case 'wml':
			case 'chtml':
			case 'iphone':
				$template = $MobileJoomla->getParam('template');
				$homepage = $MobileJoomla->getParam('homepage');
				$gzip     = $MobileJoomla->getParam('gzip');
				break;
		}

		//Set template
		if($template)
		{
			if(!$is_joomla15)
			{
				$db =& JFactory::getDBO();
				$query = "SELECT params FROM #__template_styles WHERE client_id = 0 AND template = ".$db->Quote($template)." ORDER BY id LIMIT 1";
				$db->setQuery($query);
				$params_data = $db->loadResult();
				if(empty($params_data))
					$params_data = '{}';
			}
			if(version_compare(JVERSION,'1.7','ge'))
			{
				$mainframe->setTemplate($template, $params_data);
			}
			elseif(version_compare(JVERSION,'1.6','ge'))
			{
				$mainframe->setTemplate($template);
				$template_obj = $mainframe->getTemplate(true);
				$template_obj->params->loadJSON($params_data);
			}
			else
			{
				$mainframe->setUserState('setTemplate', $template);
				$mainframe->setTemplate($template);
			}
		}

		//Set gzip
		/** @var JRegistry $config */
		$config =& JFactory::getConfig();
		if($is_joomla15)
			$config->setValue('config.gzip', $gzip);
		else
			$config->setValue('gzip', $gzip);

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
		unset($current['template']);
		unset($current['templateStyle']);
		unset($current['limit']); // fix for sh404sef
		if(isset($current['limitstart']) && $current['limitstart']==0)
			unset($current['limitstart']);
		if(isset($current[session_name()]))
			unset($current[session_name()]);

        $menu =& $mainframe->getMenu();
		/** @var JMenuSite $menu */
		$menu =& JSite::getMenu();
		if($is_joomla15)
			$default = $menu->getDefault();
		else
		{
			$lang = JFactory::getLanguage();
			$default = $menu->getDefault($lang->getTag());
		}
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
						if($key != 'lang')
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
		{
			$markup = $this->CheckMarkup($_GET['device']);
			jimport('joomla.environment.uri');
			$uri =& JURI::getInstance();
			$uri->delVar('device');
		}

		if($markup === false)
			$markup = $this->CheckMarkup($mainframe->getUserState('mobilejoomla.markup'));

		if($markup === false && isset($_COOKIE['mjmarkup']))
			$markup = $this->CheckMarkup($_COOKIE['mjmarkup']);

		return $markup;
	}

	function updateUserMarkup()
	{
		if(isset($_GET['template']) || isset($_GET['templateStyle']))
			return;

		$MobileJoomla_Device =& MobileJoomla::getDevice();
		$markup = $MobileJoomla_Device['markup'];

		/** @var JSite $mainframe */
		$mainframe =& JFactory::getApplication();
		$mainframe->setUserState('mobilejoomla.markup', $markup);

		if($markup != $MobileJoomla_Device['default_markup'])
			setcookie('mjmarkup', $markup ? $markup : 'desktop', time()+365*24*60*60, '/');
		elseif(isset($_COOKIE['mjmarkup']))
			setcookie('mjmarkup', '', time()-365*24*60*60, '/');
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

		if(!empty($text))
			JResponse::setBody($text);

		if($MobileJoomla_Settings['httpcaching'])
		{
			JResponse::allowCache(true);
			JResponse::setHeader('Vary', 'Cookie');
		}
		JResponse::setHeader('Cache-Control', 'no-transform');
	}
}
