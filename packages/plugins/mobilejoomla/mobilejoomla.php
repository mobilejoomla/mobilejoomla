<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version    ###VERSION###
 * @license    ###LICENSE###
 * @copyright  ###COPYRIGHT###
 * @date       ###DATE###
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.environment.uri');
jimport('joomla.filesystem.file');

class _CacheStub_J15
{
    public function setCaching($bool)
    {
    }

    public function get()
    {
        return false;
    }

    public function store()
    {
    }
}

function plgSystemMobileJoomla_onAfterRenderLast()
{
    /** @var JApplicationSite $app */
    $app = JFactory::getApplication();
    $app->triggerEvent('onAfterRenderLast');
}

class plgSystemMobileJoomla extends JPlugin
{
    /** @var MjJoomlaWrapper */
    private $joomlaWrapper;

    /** @var MobileJoomla */
    private $mj;

    /** @var bool */
    private $is_joomla15;

    /** @var array */
    private $devices;

    public function plgSystemMobileJoomla(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->is_joomla15 = (substr(JVERSION, 0, 3) === '1.5');
    }

    public function onGetMobileJoomla()
    {
        return $this->mj;
    }

    public function onAfterInitialise()
    {
        /** @var JApplicationSite $app */
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {// don't use MobileJoomla in backend
            return;
        }

        include_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/legacy/joomlawrapper.php';
        $this->joomlaWrapper = MjJoomlaWrapper::getInstance();

        //load MobileJoomla class
        require_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/classes/mobilejoomla.php';

        include_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/classes/mjhelper.php';
        $this->devices = MjHelper::getDeviceList();

        /** @var MobileJoomla $mj */
        $mj = new MobileJoomla($this->joomlaWrapper);
        /** @var MjSettingsModel $mjSettings */
        $mjSettings = $mj->settings;

        $this->mj = $mj;

        if ($mjSettings->get('autoupdate', false)) {
            register_shutdown_function(array($this, 'checkNewVersion'));
        }

        JPluginHelper::importPlugin('mobile');
        $app->triggerEvent('onMJInitialise', array($mj));

        if (isset($_COOKIE['mjmarkup'])) {
            $markup = $this->mj->checkMarkup($_COOKIE['mjmarkup']);
            if ($markup !== false) {
                $uri = JUri::getInstance();
                if ($uri->getVar('device', false) === false) {
                    $uri = clone($uri);
                    $uri->setVar('device', $markup);
                    $app->redirect($uri->toString());
                }
            }
        }

        $cached_data = $app->getUserState('mobilejoomla.cache');
        if ($cached_data !== null) {
            $cached_data = gzinflate(base64_decode($cached_data));
            if ($cached_data !== false) {
                $cached_data = @unserialize($cached_data);
            }
        }

        if (is_array($cached_data) && ($cached_data['ua'] === @$_SERVER['HTTP_USER_AGENT'])) {
            $mj->device = $cached_data['device'];
        } else {
            $app->triggerEvent('onDeviceDetection', array($mj));

            $gzlevel = 5;
            $cached_data = array('device' => $mj->device, 'ua' => @$_SERVER['HTTP_USER_AGENT']);
            $cached_data = base64_encode(gzdeflate(serialize($cached_data), $gzlevel));
            $app->setUserState('mobilejoomla.cache', $cached_data);
        }

        /** @var MjDevice $mjDevice */
        $mjDevice = $mj->device;

        $mjDevice->markup = $mj->checkMarkup($mjDevice->markup);

        $mjDevice->real_markup = $mjDevice->markup;

        $app->triggerEvent('onAfterDeviceDetection', array($mj));
        $mjDevice->markup = $mj->checkMarkup($mjDevice->markup);

        $markup = $mjDevice->markup;
        $mjDevice->default_markup = $markup;

        //get user choice
        $user_markup = $this->getUserMarkup();
        if ($user_markup !== false) {
            $markup = $user_markup;
        }

        // template preview
        if (isset($_GET['template'])) {
            $getTemplate = $_GET['template'];
        } elseif (version_compare(JVERSION, '1.7', '>=') && isset($_GET['templateStyle']) && is_int($_GET['templateStyle'])) {
            $getTemplate = (int)$_GET['templateStyle'];
        } else {
            $getTemplate = null;
        }
        if ($getTemplate) {
            foreach ($this->devices as $device => $title) {
                if ($getTemplate === $mjSettings->get($device . '.template')) {
                    $markup = $device;
                    break;
                }
            }
        }

        $mjDevice->markup = $markup;

        if (($mjDevice->screenwidth === 0) || ($mjDevice->screenheight === 0)) {
            // @todo iterate installed modes
            switch ($markup) {
                case 'mobile':
                    $mjDevice->screenwidth = 320;
                    $mjDevice->screenheight = 480;
                    break;
                case 'tablet':
                    $mjDevice->screenwidth = 800;
                    $mjDevice->screenheight = 1280;
                    break;
            }
        }

        if ($mjDevice->imageformats === null) {
            switch ($markup) {
                case 'wml':
                    $mjDevice->imageformats = array('wbmp');
                    break;
                case 'chtml':
                    $mjDevice->imageformats = array('gif');
                    break;
                case 'mobile':
                case 'tablet':
                case 'xhtml':
                case 'iphone':
                    $mjDevice->imageformats = array('png', 'gif', 'jpg');
                    break;
            }
        }
        if (count($mjDevice->imageformats) === 0) {
            foreach ($this->devices as $device => $title) {
                $mjSettings->set($device . '.img', 0);
            }
        }

        $app->triggerEvent('onBeforeMobileMarkupInit', array($mj));
        $mjDevice->markup = $mj->checkMarkup($mjDevice->markup);

        $this->updateUserMarkup();

        $app->triggerEvent('onMobileMarkupInit', array($mj));

        $markup = $mjDevice->markup;
        if (empty($markup)) {
            $mjDevice->markup = false;
            return;
        }

        $mj->setMarkup($markup);

        // set headers here to be compatible with System-Cache
        $mj->generator->setHeader();

        if ($mjSettings->get('mobile_sitename')) {
            $this->joomlaWrapper->setConfig('sitename', $mjSettings->get('mobile_sitename'));
        }

        if (!$this->is_joomla15) //Joomla!1.6+
        {
            if (!$mjSettings->get('caching')) {
                $this->joomlaWrapper->setConfig('caching', 0);
            }

            $cachekey = $mj->getCacheKey();

            $registeredurlparams = isset($app->registeredurlparams) ? $app->registeredurlparams : null;
            if (empty($registeredurlparams)) {
                $registeredurlparams = new stdClass();
            }
            $this->setRequestVar('mjcachekey', $cachekey);
            $registeredurlparams->mjcachekey = 'CMD';
            /* @todo: refactor getURI */
            $this->setRequestVar('mjurlkey', JRequest::getURI());
            $registeredurlparams->mjurlkey = 'STRING';
            $app->registeredurlparams = $registeredurlparams;

            //fix System-Cache plugin in J!3.0
            if (JPluginHelper::isEnabled('system', 'cache') && version_compare(JVERSION, '3.0.0', '>=')) {
                $dispatcher = JEventDispatcher::getInstance();
                $refObj = new ReflectionObject($dispatcher);
                $refProp = $refObj->getProperty('_observers');
                $refProp->setAccessible(true);
                /** @var array $observers */
                $observers = $refProp->getValue($dispatcher);
                foreach ($observers as $index => $object) {
                    if (is_a($object, 'plgSystemCache')) {
                        $object->_cache_key = '~' . $cachekey . '~' . $object->_cache_key;
                    }
                }
            }
        } else { //Joomla!1.5
            if ($mjSettings->get('caching')) {
                /** @var Joomla\Registry\Registry $config */
                $config = JFactory::getConfig();
                $handler = $config->getValue('config.cache_handler', 'file');
                $handler .= '_mj';
                $config->setValue('config.cache_handler', $handler);
                $class = 'JCacheStorage' . ucfirst($handler);
                $path = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/override/cachestorage_j15/' . $handler . '.php';
                jimport('joomla.cache.storage');
                JLoader::register($class, $path);
            } else {
                $this->joomlaWrapper->setConfig('caching', 0);
                //disable System-Cache plugin
                $dispatcher = JDispatcher::getInstance();
                foreach ($dispatcher->_observers as $index => $object) {
                    if (is_a($object, 'plgSystemCache')) {
                        $object->_cache = new _CacheStub_J15();
                        unset($dispatcher->_observers[$index]);
                        break;
                    }
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $router = $app->getRouter();
            $router->attachBuildRule(array($this, 'buildRule'));
        }

        if (!defined('SHMOBILE_MOBILE_TEMPLATE_SWITCHED')) {
            define('SHMOBILE_MOBILE_TEMPLATE_SWITCHED', 1);
        }
    }

    /**
     * @param JRouter $router
     * @param JUri $uri
     */
    public function buildRule(&$router, &$uri)
    {
        $mjDevice = $this->mj->device;
        if ($mjDevice->markup !== $mjDevice->default_markup) {
            switch ($uri->getVar('format')) {
                case 'feed':
                case 'json':
                case 'xml':
                    return;
            }
            switch ($uri->getVar('type')) {
                case 'rss':
                case 'atom':
                    return;
            }
            if ((is_a($router, 'shRouter') || class_exists('Sh404sefClassRouter')) &&
                $uri->getVar('Itemid') && count($uri->getQuery(true)) === 2
            ) // check for sh404sef
            {
                $itemid = (int)$uri->getVar('Itemid');
                $app = JFactory::getApplication();
                $menu = $app->getMenu();
                $item = $menu->getItem($itemid);
                $uri->setQuery($item->query);
                $uri->setVar('Itemid', $itemid);
                $uri->setVar('device', $mjDevice->markup);
            } else {
                $uri->setVar('device', $mjDevice->markup);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function onAfterRoute()
    {
        /** @var JApplicationSite $app */
        $app = JFactory::getApplication();
        if ($app->isAdmin()) { // don't use MobileJoomla in backend
            $this->checkUpdateURLs();
            return;
        }

        // don't filter RSS and non-html
        /** @var JDocument $document */
        $document = JFactory::getDocument();
        $format = $document->getType();
        /* @todo refactor JRequest usage */
        $doctype = JRequest::getVar('type', false);
        if ($doctype === 'rss' || $doctype === 'atom' || (($format !== 'html') && ($format !== 'raw'))) {
            //reset mobile content-type header
            $headers = JResponse::getHeaders();
            JResponse::clearHeaders();
            foreach ($headers as $header) {
                if (strtolower($header['name']) !== 'content-type') {
                    JResponse::setHeader($header['name'], $header['value']);
                }
            }
            return;
        }

        //be last registered onAfterRender event
        $app->registerEvent('onAfterRender', 'plgSystemMobileJoomla_onAfterRenderLast');

        // Load config
        $mjSettings = $this->mj->settings;
        $mjDevice = $this->mj->device;

        jimport('joomla.environment.browser');
        $browser = JBrowser::getInstance();
        if (version_compare(JVERSION, '3.0', '<')) {
            $browser->set('_mobile', $mjDevice->markup !== false);
        } else {
            $refObj = new ReflectionObject($browser);
            $refProp = $refObj->getProperty('mobile');
            $refProp->setAccessible(true);
            $refProp->setValue($browser, $mjDevice->markup !== false);
        }

        JPluginHelper::importPlugin('mobile');
        $app->triggerEvent('onMobileAfterRoute', array($this->mj));

        $this->filterExtensions();

        // "Vary" header for proxy
        JResponse::setHeader('Vary', 'User-Agent');

        if ($mjDevice->markup === false) //desktop
        {
            return;
        }

        define('_MJ', 1);

        /** @var MjMarkupGenerator $markupGenerator */
        $markupGenerator = $this->mj->generator;

        if (!$this->is_joomla15 && isset($app->registeredurlparams)) //Joomla!1.6+
        {
            $registeredurlparams = $app->registeredurlparams;
            $this->setRequestVar('mjurlkey', null);
            unset($registeredurlparams->mjurlkey);
            $app->registeredurlparams = $registeredurlparams;
        }

        $app->triggerEvent('onMobile', array($this->mj));

        $template = $this->mj->getParam('template');
        $homepage = $this->mj->getParam('homepage');
        $gzip = $this->mj->getParam('gzip');

        if (in_array((string)JRequest::getInt('Itemid'), (array)$this->mj->getParam('nomjitems'), true)) {
            $template = '';
        }

        //Set template
        if (!empty($template)) {
            if ($this->is_joomla15) {
                $app->setUserState('setTemplate', $template);
                $app->setTemplate($template);
            } else {
                $joomlaWrapper = MjJoomlaWrapper::getInstance();
                $db = $joomlaWrapper->getDbo();
                $query = new MjQueryBuilder($db);
                $template_style = $query
                    ->select('params, template')
                    ->from('#__template_styles')
                    ->where($query->qn('client_id') . '=0')
                    ->where($query->qn('id') . '=' . (int)$template)
                    ->setQuery()
                    ->loadObject();
                $params_data = $template_style->params;
                $template = $template_style->template;
                if (empty($params_data)) {
                    $params_data = '{}';
                }
                if (version_compare(JVERSION, '1.7', '>=')) {
                    $app->setTemplate($template, $params_data);
                } elseif (version_compare(JVERSION, '1.6', '>=')) {
                    $app->setTemplate($template);
                    /** @var stdClass $template_obj */
                    $template_obj = $app->getTemplate(true);
                    /** @var Joomla\Registry\Registry {$template_obj->params} */
                    $template_obj->params->loadJSON($params_data);
                }
            }
        }

        // JHtml overrides
        if (version_compare(JVERSION, '3.0', '<')) {
            jimport('joomla.html.html');
            JHtml::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/override/html');
            if (is_dir($dir = JPATH_THEMES . '/' . $template . '/override/html')) {
                JHtml::addIncludePath($dir);
            }
        } else {
            // load email.php only (workaround for new J!3 class loader)
            if (is_file($path = JPATH_THEMES . '/' . $template . '/override/html/email.php')) {
                JLoader::register('JHtmlEmail', $path, true);
            } else {
                JLoader::register('JHtmlEmail', JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/override/html/email.php', true);
            }
        }

        $this->joomlaWrapper->setConfig('gzip', $gzip);

        //Set headers
        JResponse::clearHeaders();
        $markupGenerator->setHeader();

        // SEO
        $canonical = $this->mj->getCanonicalURI();
        if ($canonical) {
            if ($format === 'html') {
                /** @var $document JDocumentHtml */
                $document->addHeadLink($canonical, 'canonical');
            }
            $document->setMetaData('robots', 'noindex, nofollow');
        }

        if (JRequest::getMethod() === 'POST') {
            return;
        }

        /** @var JMenu $menu */
        $menu = $app->getMenu();
        $router = $app->getRouter();
        $Itemid = version_compare(JVERSION, '3.0', '>=') ? $app->input->getInt('Itemid') : JRequest::getInt('Itemid');
        $item = $menu->getItem($Itemid);
        if (is_object($item)) {
            $current = array_merge($item->query, $_GET, $router->getVars());
        } else {
            $current = array_merge($_GET, $router->getVars());
        }
        if (!isset($current['Itemid'])) {
            $current['Itemid'] = (string)$Itemid;
        }
        unset(
            $current['device']
            , $current['lang']
            , $current['format']
            , $current['no_html']
            , $current['language']
            , $current['tp']
            , $current['template']
            , $current['templateStyle']
            , $current['start']
            , $current['limitstart']
            , $current['limit'] // fix for sh404sef
            , $current[session_name()]
        );
        if (isset($current['limitstart']) && $current['limitstart'] == 0) {
            unset($current['limitstart']);
        }

        if ($this->is_joomla15) {
            $default = $menu->getDefault();
        } else {
            $lang = JFactory::getLanguage();
            $default = $menu->getDefault($lang->getTag());
        }
        $home = $default->query;
        $home['Itemid'] = $default->id;

        if (substr($homepage, 0, 10) === 'index.php?') {
            parse_str(substr($homepage, 10), $mj_home);
            if (isset($mj_home['Itemid'])) {
                $mj_home_Itemid = (int)$mj_home['Itemid'];
                if ($this->is_joomla15) {
                    $menu->setDefault($mj_home_Itemid);
                } else {
                    $menu->setDefault($mj_home_Itemid, '*');
                }
            }
            if ($current == $mj_home) {
                $this->mj->setHome(true);
            }
        }

        if (count($current) === 0 || $current == $home) {
            $this->mj->setHome(true);
            if ($homepage) {
                if (isset($mj_home_Itemid)) {
                    global $Itemid;
                    $Itemid = $mj_home_Itemid;
                    $menu->setActive($Itemid);
                    if (version_compare(JVERSION, '3.2', '>=')) {
                        $menu->authorise($Itemid);
                    } elseif (!$this->is_joomla15) {
                        $app->authorise($Itemid);
                    } else {
                        $app->authorize($Itemid);
                    }
                }

                $_SERVER['REQUEST_URI'] = JUri::base(true) . '/' . $homepage;
                if (isset($mj_home)) {
                    $_SERVER['QUERY_STRING'] = substr($homepage, 10);
                    foreach ($current as $key => $val) { //clear old variables
                        unset($_REQUEST[$key], $_GET[$key]);
                    }
                    JRequest::set($mj_home, 'get');
                } else {
                    $url = 'http';
                    $url .= (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])
                        && (strtolower($_SERVER['HTTPS']) !== 'off'))
                        ? 's' : '';
                    $url .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                    $uri = new JUri($url);
                    $router = $app->getRouter();
                    $result = $router->parse($uri);
                    JRequest::set($result, 'get');
                }
            }
        }
    }

    private function getUserMarkup()
    {
        $markup = false;

        if (isset($_GET['device'])) {
            $markup = $_GET['device'];
            $uri = JUri::getInstance();
            $uri->delVar('device');

            if ($markup === 'auto') {
                return false;
            }
            $markup = $this->mj->checkMarkup($markup);
        }

//        if ($markup === false && isset($_COOKIE['mjmarkup'])) {
//            $markup = $this->mj->checkMarkup($_COOKIE['mjmarkup']);
//        }

        return $markup;
    }

    private function updateUserMarkup()
    {
        if (isset($_GET['template']) || isset($_GET['templateStyle'])) {
            return;
        }

        $mjDevice = $this->mj->device;
        $markup = $mjDevice->markup;

        $app = JFactory::getApplication();
        $app->setUserState('mobilejoomla.markup', $markup);

        $mjSettings = $this->mj->settings;
        $cookie_domain = $mjSettings->get('desktop_domain');
        if (substr($cookie_domain, 0, 4) === 'www.') {
            $cookie_domain = substr($cookie_domain, 4);
        }
        $cookie_domain = '.' . $cookie_domain;
//        $http_host = $_SERVER['HTTP_HOST'];
//        if(substr($http_host, -strlen($cookie_domain))==$cookie_domain)
//            $cookie_domain = $http_host;

        if ($markup != $mjDevice->default_markup) {
            setcookie('mjmarkup', $markup ? $markup : 'desktop', 0, '/', $cookie_domain);
        } elseif (isset($_COOKIE['mjmarkup'])) {
            setcookie('mjmarkup', '', 1, '/', $cookie_domain);
        }
    }

    private function setRequestVar($name, $value = null)
    {
        if ($value !== null) {
            $_REQUEST[$name] = $value;
            $GLOBALS['_JREQUEST'][$name] = array('SET.REQUEST' => true);
        } else {
            unset($_REQUEST[$name], $GLOBALS['_JREQUEST'][$name]);
        }
    }

    public function onAfterRender()
    {
    }

    public function onAfterRenderLast()
    {
        if (!defined('_MJ')) {
            return;
        }

        if ($this->is_joomla15) {
            /** @var JSite $app */
            $app = JFactory::getApplication();
            $app->setUserState('setTemplate', null);
        }

        $text = JResponse::getBody();

        $app = JFactory::getApplication();
        $app->triggerEvent('onMobilePagePrepare', array(&$text));

//        $text = $this->mj->generator->processPage($text);
        require_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/ress/ressio.php';
        if ($this->is_joomla15) {
            /** @var array $classes */
            $classes = include JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/ress/classmap.php';
            foreach ($classes as $classname => $filepath) {
                JLoader::register($classname, $filepath);
            }
        }

        $options = array(
            'autostart' => false,
            'webrootpath' => JPATH_BASE,
            'staticdir' => '/media/mj',
            'fileloader' => ($this->mj->getParam('distribmode') === 'php') ? 'php' : 'file',
            'fileloaderphppath' => JPATH_ROOT . '/media/mj/',
            'cachepath' => JPATH_CACHE . '/mj',
            'html' => array(
                'merge_space' => (bool)$this->mj->getParam('html_mergespace'),
                'remove_comments' => (bool)$this->mj->getParam('html_removecomments'),
                'uri_minify' => (bool)$this->mj->getParam('html_minifyurl'),
                'gzlevel' => 0
            ),
            'css' => array(
                'inline_limit' => (int)$this->mj->getParam('css_inlinelimit')
            ),
            'js' => array(
                'inline_limit' => (int)$this->mj->getParam('js_inlinelimit')
            ),
            'img' => array(
                'jpegquality' => (int)$this->mj->getParam('jpegquality'),
                'set_dimension' => true,
                'buffer_width' => (int)$this->mj->getParam('buffer_width'),
                'rescale' => (bool)$this->mj->getParam('img'),
                'scaletype' => $this->mj->getParam('img') ? 'fit' : '',
                'hiresimages' => (bool)$this->mj->getParam('hiresimages', false),
                'hiresjpegquality' => (int)$this->mj->getParam('hiresjpegquality', $this->mj->getParam('jpegquality')),
                'keeporig' => false,
                'wrapwideimg' => (bool)$this->mj->getParam('img_wrapwide', false),
                'wideimgclass' => 'mjwideimg',
                'lazyload' => (bool)$this->mj->getParam('img_lazyload', false)
            ),
            'amdd' => array(
                'handler' => 'joomla',
                'dbTableName' => '#__mj_amdd'
            ),
            'di' => array(
//                'deviceDetector' => 'MJ_Ressio_DeviceDetector',
                'jsMinify' => $this->mj->getParam('js_optimize') ? 'Ressio_JsMinify_JsMin' : 'Ressio_JsMinify_None',
                'cssMinify' => $this->mj->getParam('css_optimize') ? 'Ressio_CssMinify_Ress' : 'Ressio_CssMinify_None',
//                'filesystem' => 'MJ_Ressio_FileSystem_Joomla',
            )
        );
        $app->triggerEvent('onMJRessioConfig', array(&$options));
        $ressio = new Ressio($options);
        if ($this->mj->getParam('removetags')) {
            /** @var Ressio_Dispatcher $dispatcher */
            $dispatcher = $ressio->di->get('dispatcher');
            $dispatcher->addListener(
                array('HtmlIterateTagIFRAMEBefore', 'HtmlIterateTagOBJECTBefore',
                    'HtmlIterateTagEMBEDBefore', 'HtmlIterateTagAPPLETBefore'),
                array($this, 'RessioRemoveTag')
            );
        }
        $app->triggerEvent('onMJRessioPrepare', array($ressio));
        $text = $ressio->run($text);

        if (!empty($text)) {
            JResponse::setBody($text);
        }

        $app->triggerEvent('onMobileAfterPagePrepare');

        if ($this->mj->getParam('httpcaching')) {
            JResponse::allowCache(true);
            JResponse::setHeader('Vary', 'Cookie');
        }
        JResponse::setHeader('Cache-Control', 'no-transform');

        //remove Content-Type duplicates
        $headers = JResponse::getHeaders();
        JResponse::clearHeaders();
        $found = false;
        foreach ($headers as $header) {
            if (strtolower($header['name']) !== 'content-type') {
                JResponse::setHeader($header['name'], $header['value']);
            } elseif (!$found) {
                JResponse::setHeader($header['name'], $header['value']);
                $found = true;
            }
        }
    }

    /**
     * @param Ressio_Event $event
     * @param Ressio $ressio
     * @param HTML_Node $node
     */
    public function RessioRemoveTag($event, $ressio, $node)
    {
        $node->detach();
    }

    private function filterExtensions()
    {
        $mjDevice = $this->mj->device;

        $markup = $mjDevice->markup;
        if (empty($markup)) {
            $markup = 'desktop';
        }

        jimport('joomla.plugins.helper');
        jimport('joomla.application.module.helper');
        $joomlaWrapper = MjJoomlaWrapper::getInstance();
        $db = $joomlaWrapper->getDbo();

        $query = new MjQueryBuilder($db);
        $query
            ->select('p.folder AS ' . $query->qn('type') . ', p.element AS ' . $query->qn('name'))
            ->from($query->qn('#__mj_plugins') . ' AS mj');
        if ($this->is_joomla15) {
            $query->leftJoin($query->qn('#__plugins') . ' AS p ON p.id=mj.id');
        } else {
            $query->leftJoin($query->qn('#__extensions') . ' AS p ON p.extension_id=mj.id');
        }
        $mj_plugins = $query
            ->where('mj.device=' . $query->q($markup))
            ->setQuery()
            ->loadObjectList();

        if (is_array($mj_plugins)) {
            foreach ($mj_plugins as $plugin) {
                $p = JPluginHelper::getPlugin($plugin->type, $plugin->name);
                if (is_object($p)) {
                    $p->type = '_mj_dummy_';
                }
            }
        }


        $query = new MjQueryBuilder($db);
        $mj_modules = $query
            ->select('m.id, m.position')
            ->from($query->qn('#__mj_modules') . ' AS mj')
            ->leftJoin($query->qn('#__modules') . ' AS m ON m.id=mj.id')
            ->where('mj.device=' . $query->q($markup))
            ->setQuery()
            ->loadObjectList();

        $j_modules = array();
        if (is_array($mj_modules)) {
            foreach ($mj_modules as $module) {
                if (!isset($j_modules[$module->position])) {
                    $j_modules[$module->position] = array();
                    $list = JModuleHelper::getModules($module->position);
                    foreach ($list as $item) {
                        $j_modules[$module->position][$item->id] = $item;
                    }
                }
                if (isset($j_modules[$module->position][$module->id])) {
                    $m = $j_modules[$module->position][$module->id];
                    $m->position = $m->module = $m->name = '_mj_dummy_';
                }
            }
        }
    }

    private function checkUpdateURLs()
    {
        include_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/legacy/joomlawrapper.php';
        $joomlaWrapper = MjJoomlaWrapper::getInstance();

        $option = $joomlaWrapper->getRequestWord('option');

        if ($option === 'com_installer') {
            $task = $joomlaWrapper->getRequestVar('task');

            if (in_array($task, array('update.find', 'update.update', 'update.ajax'), true)) {
                include_once JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/models/settings.php';
                $mjSettings = new MjSettingsModel($joomlaWrapper);
                $domain = urlencode($mjSettings->get('desktop_domain'));

                $db = $joomlaWrapper->getDbo();

                $query = new MjQueryBuilder($db);
                $list = $query
                    ->select('update_site_id', 'location')
                    ->from('#__update_sites')
                    ->where($query->qn('location') . ' LIKE ' . $query->q('http://www.mobilejoomla.com/%'))
                    ->setQuery()
                    ->loadObjectList();
                foreach ($list as $item) {
                    $url = $item->location;
                    $url_new = explode('?', $url);
                    $url_new = $url_new[0] . '?domain=' . $domain .
                        '&joomla=' . JVERSION . '&mj=###VERSION###';
                    if ($url_new !== $url) {
                        $query = new MjQueryBuilder($db);
                        $query
                            ->update('#__update_sites')
                            ->set($query->qn('location') . '=' . $query->q($url_new))
                            ->where($query->qn('update_site_id') . '=' . $item->update_site_id)
                            ->setQuery()
                            ->query();
                    }
                }
            }

            if ($task === 'update.update') {
                $content = '';
                JFile::write(JPATH_ROOT . '/components/com_mobilejoomla/update.dat', $content);
            }
        }
    }

    public function onExtensionAfterUpdate()
    {
        JFile::delete(JPATH_ROOT . '/components/com_mobilejoomla/update.dat');
    }

    public function checkNewVersion()
    {
        if (time() < (int)$this->mj->settings->get('autoupdate_next_check')) {
            return;
        }

        // @todo Add a lock to don't have two (or more) updating processes
        $lockpath = JPATH_ROOT . '/cache/mj/update.lock';
        $lock = @mkdir($lockpath);
        if (!$lock) {
            // check timestamp -> remove dir if 2+ hours difference
            if (filemtime($lockpath) < time() - 2 * 3600) {
                @rmdir($lockpath);
            }
            return;
        }

        $this->mj->settings->set('autoupdate_next_check', time() + 24 * 60 * 60);
        $this->mj->settings->save();

        $this->installUpdate();

        @rmdir($lockpath);
    }

    private function installUpdate()
    {
        ignore_user_abort(true);
        set_time_limit(200);
        $current_version = '###VERSION###';

        $manifest = JPATH_ADMINISTRATOR . '/components/com_mobilejoomla/mobilejoomla.xml';
        $xmlManifest = simplexml_load_file($manifest);

        if ($xmlManifest === false) {
            return;
        }
        $updateServer = (string)$xmlManifest->updateservers->server[0];

        $xml = file_get_contents($updateServer);
        if ($xml === false) {
            return;
        }

        $xml = simplexml_load_string($xml);
        if ($xml === false) {
            return;
        }

        $new_version = (string)$xml->update->version;

        if (version_compare($current_version, $new_version, '>=')) {
            return;
        }

        jimport('joomla.installer.helper');
        jimport('joomla.installer.installer');

        // download
        $app = JFactory::getApplication();
        $url = 'http://www.mobilejoomla.com/latest2.php';
        $app->triggerEvent('onMJBeforeDownload', array(&$url));
        $packagefile = JInstallerHelper::downloadPackage($url);
        if ($packagefile === false) {
            return;
        }

        // unpack
        $config = JFactory::getConfig();
        if (substr(JVERSION, 0, 3) === '1.5') {
            $extractdir = $config->getValue('config.tmp_path');
        } else {
            $extractdir = $config->get('tmp_path');
        }
        $extractdir .= '/' . $packagefile;
        if (JInstallerHelper::unpack($extractdir) === false) {
            JInstallerHelper::cleanupInstall($packagefile, $extractdir);
            return;
        }

        // install
        $installer = new JInstaller();
        $installer->install($extractdir);
        JInstallerHelper::cleanupInstall($packagefile, $extractdir);
    }
}
