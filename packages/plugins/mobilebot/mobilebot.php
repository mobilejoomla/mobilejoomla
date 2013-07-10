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
jimport('joomla.environment.uri');

class _CacheStub
{
	function setCaching($bool){}
	function get(){return false;}
	function store(){}
}

function plgSystemMobileBot_onAfterRenderLast()
{
	return plgSystemMobileBot::onAfterRenderLast();
}

class plgSystemMobileBot extends JPlugin
{
	function plgSystemMobileBot(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function isJoomla15()
	{
		static $is_joomla15;
		if(!isset($is_joomla15))
			$is_joomla15 = (substr(JVERSION,0,3) == '1.5');
		return $is_joomla15;
	}
	function getConfig($name, $default=null)
	{
		/** @var JRegistry $config */
		$config = JFactory::getConfig();
		if($this->isJoomla15())
			return $config->getValue('config.'.$name, $default);
		else
			return $config->get($name, $default);
	}
	function setConfig($name, $value)
	{
		/** @var JRegistry $config */
		$config = JFactory::getConfig();
		if($this->isJoomla15())
			return $config->setValue('config.'.$name, $value);
		else
			return $config->set($name, $value);
	}

	function onAfterInitialise()
	{
		/** @var JSite $app */
		$app = JFactory::getApplication();
		if($app->isAdmin()) // don't use MobileJoomla in backend
			return;

		$is_joomla15 = $this->isJoomla15();

		//load MobileJoomla class
		require_once(JPATH_ADMINISTRATOR.'/components/com_mobilejoomla/classes/mobilejoomla.php');

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
 			$app->redirect($uri->toString());
		}

		JPluginHelper::importPlugin('mobile');

		$cached_data = $app->getUserState('mobilejoomla.cache');
		if($cached_data!==null)
		{
			$cached_data = @gzinflate(@base64_decode($cached_data));
			if($cached_data!==false)
				$cached_data = @unserialize($cached_data);
		}

		if(is_array($cached_data))
		{
			$MobileJoomla_Device = $cached_data['device'];
		}
		else
		{
			$app->triggerEvent('onDeviceDetection', array(&$MobileJoomla_Settings, &$MobileJoomla_Device));

			$gzlevel = 5;
			$cached_data = array('device'=>$MobileJoomla_Device);
			$cached_data = base64_encode(gzdeflate(serialize($cached_data), $gzlevel));
			$app->setUserState('mobilejoomla.cache', $cached_data);
		}
		$MobileJoomla_Device['markup'] = self::CheckMarkup($MobileJoomla_Device['markup']);

		$MobileJoomla_Device['real_markup'] = $MobileJoomla_Device['markup'];

		$app->triggerEvent('onAfterDeviceDetection', array (&$MobileJoomla_Settings, &$MobileJoomla_Device));
		$MobileJoomla_Device['markup'] = self::CheckMarkup($MobileJoomla_Device['markup']);

		$markup = $MobileJoomla_Device['markup'];
		$MobileJoomla_Device['default_markup'] = $markup;

		//get user choice
		$user_markup = $this->getUserMarkup();
		if($user_markup!==false)
			$markup = $user_markup;

		// template preview
		$getTemplate = isset($_GET['template']) ? $_GET['template'] : null;
		if(version_compare(JVERSION, '1.7', '>='))
		{
			if($getTemplate===null && isset($_GET['templateStyle']) && is_int($_GET['templateStyle']))
			{
				$db = JFactory::getDBO();
				$query = 'SELECT template FROM #__template_styles WHERE id = '.intval($_GET['templateStyle']).' AND client_id = 0';
				$db->setQuery($query);
				$getTemplate = $db->loadResult();
			}
		}
		elseif(version_compare(JVERSION, '1.6', '>='))
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
					$MobileJoomla_Device['screenwidth'] = 320;
					$MobileJoomla_Device['screenheight'] = 480;
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

		$app->triggerEvent('onBeforeMobileMarkupInit', array (&$MobileJoomla_Settings, &$MobileJoomla_Device));
		$MobileJoomla_Device['markup'] = self::CheckMarkup($MobileJoomla_Device['markup']);

		$this->updateUserMarkup();

		$app->triggerEvent('onMobileMarkupInit', array (&$MobileJoomla_Settings, &$MobileJoomla_Device));

		$markup = $MobileJoomla_Device['markup'];
		if(empty($markup))
		{
			$MobileJoomla_Device['markup'] = false;
			return;
		}

		$MobileJoomla = MobileJoomla::getInstance($markup);

		// set headers here to be compatible with System-Cache
		$MobileJoomla->setHeader();

		if($MobileJoomla_Settings['mobile_sitename'])
			$this->setConfig('sitename', $MobileJoomla_Settings['mobile_sitename']);

		if(!$is_joomla15) //Joomla!1.6+
		{
			if(!$MobileJoomla_Settings['caching'])
				$this->setConfig('caching', 0);

			$cachekey = MobileJoomla::getCacheKey();

			$registeredurlparams = isset($app->registeredurlparams) ? $app->registeredurlparams : null;
			if(empty($registeredurlparams))
				$registeredurlparams = new stdClass();
			$this->setRequestVar('mjcachekey', $cachekey);
			$registeredurlparams->mjcachekey = 'CMD';
			$this->setRequestVar('mjurlkey', JRequest::getURI());
			$registeredurlparams->mjurlkey = 'STRING';
			$app->registeredurlparams = $registeredurlparams;

			//fix System-Cache plugin in J!3.0
			if(JPluginHelper::isEnabled('system', 'cache') && version_compare(JVERSION, '3.0.0', '>='))
			{
				$dispatcher = JEventDispatcher::getInstance();
				$refObj = new ReflectionObject($dispatcher);
				$refProp = $refObj->getProperty('_observers');
				$refProp->setAccessible(true);
				$observers = $refProp->getValue($dispatcher);
				foreach($observers as $index => $object)
					if(is_a($object, 'plgSystemCache'))
						$object->_cache_key = '~'.$cachekey.'~'.$object->_cache_key;
			}
		}
		else //Joomla!1.5
		{
			if($MobileJoomla_Settings['caching'])
			{
				/** @var JRegistry $config */
				$config = JFactory::getConfig();
				$handler = $config->getValue('config.cache_handler', 'file');
				$class = 'JCacheStorage'.ucfirst($handler);
				$path = JPATH_ADMINISTRATOR.'/components/com_mobilejoomla/override/cachestorage/'.$handler.'.php';
				jimport('joomla.cache.storage');
				JLoader::register($class, $path);
			}
			else
			{
				$this->setConfig('caching', 0);
				//disable System-Cache plugin
				$dispatcher = JDispatcher::getInstance();
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

		if(@$_SERVER['REQUEST_METHOD'] != 'POST')
		{
			$router = $app->getRouter();
			$router->attachBuildRule(array($this, 'buildRule'));
		}

		if(!defined('SHMOBILE_MOBILE_TEMPLATE_SWITCHED'))
			define('SHMOBILE_MOBILE_TEMPLATE_SWITCHED', 1);
	}

	/**
	 * @param JRouter $router
	 * @param JURI $uri
	 */
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
				$app = JFactory::getApplication();
				$menu = $app->getMenu();
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
		/** @var JSite $app */
		$app = JFactory::getApplication();
		if($app->isAdmin()) // don't use MobileJoomla in backend
			return;

		$is_joomla15 = $this->isJoomla15();

		// don't filter RSS and non-html
		/** @var JDocument $document */
		$document = JFactory::getDocument();
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

		//be last registered onAfterRender event
		$app->registerEvent('onAfterRender', 'plgSystemMobileBot_onAfterRenderLast');

		// Load config
		$MobileJoomla_Settings =& MobileJoomla::getConfig();
		$MobileJoomla_Device =& MobileJoomla::getDevice();

		if(version_compare(JVERSION, '3.0', '<'))
		{
			jimport('joomla.environment.browser');
			$browser = JBrowser::getInstance();
			$browser->set('_mobile', $MobileJoomla_Device['markup']!==false);
		}

		JPluginHelper::importPlugin('mobile');
		$app->triggerEvent('onMobileAfterRoute', array (&$MobileJoomla_Settings, &$MobileJoomla_Device));

		$this->filterExtensions($MobileJoomla_Settings, $MobileJoomla_Device);

		// "Vary" header for proxy
		JResponse::setHeader('Vary', 'User-Agent');

		if($MobileJoomla_Device['markup']===false) //desktop
		{
			$pcpage = $MobileJoomla_Settings['pcpage'];
			if($pcpage && ($pcpage!=='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))
				$app->redirect($pcpage);
			return;
		}

		define('_MJ', 1);
		/** @var MobileJoomla $MobileJoomla */
		$MobileJoomla = MobileJoomla::getInstance();

		if(!$is_joomla15 && isset($app->registeredurlparams)) //Joomla!1.6+
		{
			$registeredurlparams = $app->registeredurlparams;
			$this->setRequestVar('mjurlkey', null);
			unset($registeredurlparams->mjurlkey);
			$app->registeredurlparams = $registeredurlparams;
		}

		$app->triggerEvent('onMobile', array (&$MobileJoomla, &$MobileJoomla_Settings, &$MobileJoomla_Device));

		$template = $MobileJoomla->getParam('template');
		$homepage = $MobileJoomla->getParam('homepage');
		$gzip     = $MobileJoomla->getParam('gzip');

		//Set template
		if(!empty($template))
		{
			if($is_joomla15)
			{
				$app->setUserState('setTemplate', $template);
				$app->setTemplate($template);
			}
			else
			{
				$db = JFactory::getDBO();
				$query = "SELECT params FROM #__template_styles WHERE client_id = 0 AND template = ".$db->Quote($template)." ORDER BY id LIMIT 1";
				$db->setQuery($query);
				$params_data = $db->loadResult();
				if(empty($params_data))
					$params_data = '{}';
				if(version_compare(JVERSION, '1.7', '>='))
				{
					$app->setTemplate($template, $params_data);
				}
				elseif(version_compare(JVERSION, '1.6', '>='))
				{
					$app->setTemplate($template);
					$template_obj = $app->getTemplate(true);
					$template_obj->params->loadJSON($params_data);
				}
			}
		}

		// JHTML overrides
		if(version_compare(JVERSION, '3.0', '<'))
		{
			jimport('joomla.html.html');
			JHTML::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_mobilejoomla/override/html');
			if(@is_dir($dir = JPATH_THEMES.'/'.$template.'/override/html'))
				JHTML::addIncludePath($dir);
		}
		else
		{
			// load email.php only (workaround for new J!3 class loader)
			if(@is_file($path = JPATH_THEMES.'/'.$template.'/override/html/email.php'))
				JLoader::register('JHtmlEmail', $path, true);
			else
				JLoader::register('JHtmlEmail', JPATH_ADMINISTRATOR.'/components/com_mobilejoomla/override/html/email.php', true);
		}

		$this->setConfig('gzip', $gzip);

		//Set headers
		JResponse::clearHeaders();
		$document = JFactory::getDocument();
		$document->setMimeEncoding($MobileJoomla->getContentType());
		$MobileJoomla->setHeader();

		// SEO
		$canonical = MobileJoomla::getCanonicalURI();
		if($canonical)
		{
			$document->addHeadLink($canonical, 'canonical');
			$document->setMetaData('robots', 'noindex, nofollow');
		}

		if(JRequest::getMethod()=='POST')
			return;

		/** @var JMenu $menu */
		$menu = $app->getMenu();
		$router = $app->getRouter();
		$Itemid = version_compare(JVERSION, '3.0', '>=') ? $app->input->getInt('Itemid') : JRequest::getInt('Itemid');
		$item = $menu->getItem($Itemid);
		$current = array_merge($item->query, $_GET, $router->getVars());
		unset($current['device']);
		unset($current['lang']);
		unset($current['format']);
		unset($current['no_html']);
		unset($current['language']);
		unset($current['tp']);
		unset($current['template']);
		unset($current['templateStyle']);
		unset($current['start']);
		unset($current['limitstart']);
		unset($current['limit']); // fix for sh404sef
		if(isset($current['limitstart']) && $current['limitstart']==0)
			unset($current['limitstart']);
		if(isset($current[session_name()]))
			unset($current[session_name()]);

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
				if($is_joomla15)
					$menu->setDefault($mj_home_Itemid);
				else
					$menu->setDefault($mj_home_Itemid, '*');
			}
			if($current == $mj_home)
				$MobileJoomla->setHome(true);
		}

		if(count($current)==0 || $current == $home)
		{
			$MobileJoomla->setHome(true);
			if($homepage)
			{
				if(isset($mj_home_Itemid))
				{
					global $Itemid;
					$Itemid = $mj_home_Itemid;
					$menu->setActive($Itemid);
					if($is_joomla15)
						$app->authorize($Itemid);
					else
						$app->authorise($Itemid);
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
					$router = $app->getRouter();
					$result = $router->parse($uri);
					JRequest::set($result, 'get');
				}
			}
		}
	}

	// Validate markup
	public static function CheckMarkup($markup)
	{
		if(($markup===false)||($markup===null))
			return false;
		static $markup_path;
		if(!isset($markup_path))
			$markup_path = JPATH_ADMINISTRATOR.'/components/com_mobilejoomla/markup/';
		switch($markup)
		{
			case 'desktop':
			case '':
				return '';
			case 'xhtml':
			case 'iphone':
			case 'wml':
			case 'chtml':
				return $markup;
			default:
				if(class_exists('MobileJoomla_'.$markup, false) || file_exists($markup_path.$markup.'.php'))
					return $markup;
		}
		return false;
	}

	function getUserMarkup()
	{
		$markup = false;

		if(isset($_GET['device']))
		{
			$markup = $_GET['device'];
			$uri = JURI::getInstance();
			$uri->delVar('device');

			if($markup == 'auto')
				return false;
			$markup = self::CheckMarkup($markup);
		}

		if($markup === false && isset($_COOKIE['mjmarkup']))
			$markup = self::CheckMarkup($_COOKIE['mjmarkup']);

		return $markup;
	}

	function updateUserMarkup()
	{
		if(isset($_GET['template']) || isset($_GET['templateStyle']))
			return;

		$MobileJoomla_Device =& MobileJoomla::getDevice();
		$markup = $MobileJoomla_Device['markup'];

		$app = JFactory::getApplication();
		$app->setUserState('mobilejoomla.markup', $markup);

		$MobileJoomla_Settings =& MobileJoomla::getConfig();
		$desktop_uri = parse_url($MobileJoomla_Settings['desktop_url']);
		$cookie_domain = $desktop_uri['host'];
		if(substr($cookie_domain, 0, 4)=='www.')
			$cookie_domain = substr($cookie_domain, 4);
		$cookie_domain = '.'.$cookie_domain;
//		$http_host = $_SERVER['HTTP_HOST'];
//		if(substr($http_host, -strlen($cookie_domain))==$cookie_domain)
//			$cookie_domain = $http_host;

		if($markup != $MobileJoomla_Device['default_markup'])
			setcookie('mjmarkup', $markup ? $markup : 'desktop', time()+365*24*60*60, '/', $cookie_domain);
		elseif(isset($_COOKIE['mjmarkup']))
			setcookie('mjmarkup', '', time()-365*24*60*60, '/', $cookie_domain);
	}

	function setRequestVar($name, $value = null)
	{
		if($value !== null)
		{
			$_REQUEST[$name] = $value;
			$GLOBALS['_JREQUEST'][$name] = array('SET.REQUEST'=>true);
		}
		else
		{
			unset($_REQUEST[$name]);
			unset($GLOBALS['_JREQUEST'][$name]);
		}
	}

	function onAfterRender()
	{
		if(!defined('_MJ')) return;

		if($this->isJoomla15())
		{
			/** @var JSite $app */
			$app = JFactory::getApplication();
			$app->setUserState('setTemplate', null);
		}

		$text = JResponse::getBody();

		$app = JFactory::getApplication();
		$app->triggerEvent('onMobilePagePrepare', array(&$text));

		$MobileJoomla = MobileJoomla::getInstance();
		$text = $MobileJoomla->processPage($text);

		if(!empty($text))
			JResponse::setBody($text);

		$app->triggerEvent('onMobileAfterPagePrepare');
	}

	static function onAfterRenderLast()
	{
		if(!defined('_MJ')) return;

		$MobileJoomla_Settings =& MobileJoomla::getConfig();

		if($MobileJoomla_Settings['httpcaching'])
		{
			JResponse::allowCache(true);
			JResponse::setHeader('Vary', 'Cookie');
		}
		JResponse::setHeader('Cache-Control', 'no-transform');

		//remove Content-Type duplicates
		$headers = JResponse::getHeaders();
		JResponse::clearHeaders();
		$found = false;
		foreach($headers as $header)
		{
			if(strtolower($header['name']) != 'content-type')
				JResponse::setHeader($header['name'], $header['value']);
			elseif(!$found)
			{
				JResponse::setHeader($header['name'], $header['value']);
				$found = true;
			}
		}
	}

	function filterExtensions(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		$markup = $MobileJoomla_Device['markup'];
		if(empty($markup))
			$markup = 'desktop';

		jimport('joomla.plugins.helper');
		jimport('joomla.application.module.helper');
		$db = JFactory::getDBO();


		if(substr(JVERSION,0,3) == '1.5')
			$query = "SELECT p.folder AS type, p.element AS name FROM #__mj_plugins AS mj LEFT JOIN #__plugins AS p ON p.id=mj.id WHERE mj.markup=".$db->Quote($markup);
		else
			$query = "SELECT p.folder AS type, p.element AS name FROM #__mj_plugins AS mj LEFT JOIN #__extensions AS p ON p.extension_id=mj.id WHERE mj.markup=".$db->Quote($markup);
		$db->setQuery($query);
		$mj_plugins = $db->loadObjectList();

		if(is_array($mj_plugins)) foreach($mj_plugins as $plugin)
		{
			$p = JPluginHelper::getPlugin($plugin->type, $plugin->name);
			if(is_object($p))
				$p->type = '_mj_dummy_';
		}


		$query = "SELECT m.id, m.position FROM #__mj_modules AS mj LEFT JOIN #__modules AS m ON m.id=mj.id WHERE mj.markup=".$db->Quote($markup);
		$db->setQuery($query);
		$mj_modules = $db->loadObjectList();

		$j_modules = array();
		if(is_array($mj_modules)) foreach($mj_modules as $module)
		{
			if(!isset($j_modules[$module->position]))
			{
				$j_modules[$module->position] = array();
				$list = JModuleHelper::getModules($module->position);
				foreach($list as $item)
					$j_modules[$module->position][$item->id] = $item;
			}
			if(isset($j_modules[$module->position][$module->id]))
			{
				$m = $j_modules[$module->position][$module->id];
				$m->position = $m->module = $m->name = '_mj_dummy_';
			}
		}
	}
}
