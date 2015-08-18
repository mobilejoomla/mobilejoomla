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

class MJJqmFramework extends JEvent
{
    /** @var JDocumentHTML */
    private $document;
    /** @var string */
    private $template = '';
    /** @var Joomla\Registry\Registry */
    private $params;
    /** @var string */
    private $base = '';
    /** @var string */
    private $path = '';

    /** @var string jQueryMobile version */
    public $jqm_ver = '1.4.5';
    /** @var string jQuery version */
    public $jq_ver = '1.9.1';
    /** @var bool Load jQuery Migration */
    public $jq_migration = false;
    /** @var string jQuery Migration version */
    public $jq_migration_ver = '1.2.1';

    /** @var string */
    private $jqm_css = '';
    /** @var string */
    private $jqm_jq = '';
    /** @var string */
    private $jqm_jqmigr = '';
    /** @var string */
    private $jqm_jqm = '';

    /** @var array */
    private $loaded_css = array();
    /** @var array */
    private $loaded_js = array();
    /** @var array */
    private $combinerSource = array();

    /** @var bool */
    private $compatJQueryEasy = false;
    /** @var bool */
    private $compatJQueryEasySafeNoConflict = false; // use noConflict( ) to don't match jQueryEasy regexp

    /**
     * @param $document JDocumentHTML
     */
    public function __construct($document)
    {
        if (version_compare(JVERSION, '3.0', '<')) {
            $dispatcher = JDispatcher::getInstance();
        } else {
            $dispatcher = JEventDispatcher::getInstance();
        }

        if (version_compare(JVERSION, '1.6', '<')) {
            $dispatcher->attach($this);
        } else {
            $dispatcher->attach(array('event' => 'onMobilePagePrepare', 'handler' => array($this, 'onMobilePagePrepare')));
            $dispatcher->attach(array('event' => 'onAfterRender', 'handler' => array($this, 'onAfterRender')));
        }

        $this->document = $document;
        $this->template = $document->template;
        $this->params = $document->params;
        $this->base = '/templates/' . $this->template . '/';
        $this->path = dirname(dirname(__FILE__));

        $this->initURLs();

        // Force Mootools loading
        if ($this->params->get('mootools'))
            $this->forceMootools();
    }

    /**
     * @param $text string
     */
    public function onMobilePagePrepare(&$text)
    {
        $app = JFactory::getApplication();
        $app->triggerEvent('onBeforeCompileHead');

        /** @var $doc JDocumentHTML */
        $doc = JFactory::getDocument();

        $headerstuff = $doc->getHeadData();

        // populate preload_styleSheets and preload_scripts
        $this->parseCustomPreload($headerstuff);

        $this->fixJQueryEasy($headerstuff);
        if ($this->params->get('fixbootstrap'))
            $this->fixBootstrap($headerstuff);
        if ($this->params->get('fixjqueryui'))
            $this->fixJQueryUI($headerstuff);
        if ($this->params->get('removejquery'))
            $this->removeJQuery($headerstuff);

        $this->generateHead($headerstuff, $text);
    }

    public function onAfterRender()
    {
    }

    private function parseCustomPreload(&$headerstuff)
    {
        $css_custom_preload = $this->path . '/css/custom_preload.txt';
        $js_custom_preload = $this->path . '/js/custom_preload.txt';

        $headerstuff['preload_styleSheets'] = array();
        $headerstuff['preload_scripts'] = array();

        if (is_file($css_custom_preload)) {
            $custom_styles = @file($css_custom_preload);
            foreach ($custom_styles as $url) {
                $url = trim($url);
                if (strlen($url)) switch ($url[0]) {
                    case '#':
                        break;
                    case '-':
                        $url = ltrim(substr($url, 1));
                        $this->loaded_css[] = $url;
                        break;
                    case '*':
                        $url = ltrim(substr($url, 1));
                        $headerstuff['preload_styleSheets'][$url] = 1;
                        break;
                    default:
                        if (!isset($headerstuff['styleSheets'][$url]))
                            $headerstuff['styleSheets'][$url] = array('mime' => 'text/css', 'media' => null, 'attribs' => array());
                }
            }
        }

        if (is_file($js_custom_preload)) {
            $custom_scripts = @file($js_custom_preload);
            foreach ($custom_scripts as $url) {
                $url = trim($url);
                if (strlen($url)) switch ($url[0]) {
                    case '#':
                        break;
                    case '-':
                        $url = ltrim(substr($url, 1));
                        $this->loaded_js[$url] = 1;
                        break;
                    case '*':
                        $url = ltrim(substr($url, 1));
                        $headerstuff['preload_scripts'][$url] = 1;
                        break;
                    default:
                        if (!isset($headerstuff['scripts'][$url]))
                            $headerstuff['scripts'][$url] = array('mime' => 'text/javascript', 'defer' => false, 'async' => false);
                }
            }
        }
    }

    /**
     * @param $headerstuff Array
     */
    private function fixJQueryEasy(&$headerstuff)
    {
        $plugin = JPluginHelper::getPlugin('system', 'jqueryeasy');
        if (count($plugin) == 0)
            return;

        if (version_compare(JVERSION, '1.6', '>='))
            $params = new JRegistry($plugin->params);
        else
            $params = new JParameter($plugin->params);

        if (!$params->get('jqueryinfrontend', 0))
            return;

//		$this->params->set('load_external', 1); // for correct work of combiner
        $this->compatJQueryEasy = true;

        if ($params->get('removenoconflictfrontend', 1))
            $this->compatJQueryEasySafeNoConflict = true;

        if (isset($headerstuff['scripts']['JQLIB'])) {
            unset($headerstuff['scripts']['JQLIB']);
            $this->jqm_jq = 'JQLIB';
        }
        if (isset($headerstuff['scripts']['JQEASY_JQLIB'])) {
            unset($headerstuff['scripts']['JQEASY_JQLIB']);
            $this->jqm_jq = 'JQEASY_JQLIB';
        }
        if (isset($headerstuff['scripts']['JQNOCONFLICT']))
            unset($headerstuff['scripts']['JQNOCONFLICT']);
        if (isset($headerstuff['scripts']['JQEASY_JQNOCONFLICT']))
            unset($headerstuff['scripts']['JQEASY_JQNOCONFLICT']);
        $headerstuff['script'] = str_replace('JQEASY_JQNOCONFLICT', '', $headerstuff['script']);
        $headerstuff['script'] = str_replace('JQNOCONFLICT', '', $headerstuff['script']);
    }

    /**
     * @param $headerstuff Array
     */
    private function fixBootstrap(&$headerstuff)
    {
        $regexp = '/bootstrap(?:[\.-][\w\.-]*)\.css';
        foreach ($headerstuff['styleSheets'] as $url => $param)
            if (preg_match('#' . $regexp . '#', $url))
                $headerstuff['preload_styleSheets'][$url] = 1;
        if (count($headerstuff['custom'])) foreach ($headerstuff['custom'] as &$custom) {
            if (preg_match_all('#<link\s[^>]*href="([^">]*?' . $regexp . '[^">]*?)"[^>]*/?\>#', $custom, $matches)) {
                foreach ($matches[1] as $index => $url) {
                    $headerstuff['preload_styleSheets'][$url] = 1;
                    $custom = str_replace($matches[0][$index], '', $custom);
                }
            }
            $custom = trim($custom);
        }

        if (!isset($headerstuff['script']['text/javascript'])) {
            $headerstuff['script']['text/javascript'] = '';
        }
        $headerstuff['script']['text/javascript'] .= 'try{jQuery.fn.button.noConflict()}catch(e){};';
    }

    /**
     * @param $headerstuff Array
     */
    private function fixJQueryUI(&$headerstuff)
    {
        $regexp = '(?:/jquery-ui(?:[0-9\.-]|custom|min|pack)*?\.css|JQEASY_JQUICSS)';
        foreach ($headerstuff['styleSheets'] as $url => $param)
            if (preg_match('#' . $regexp . '#', $url))
                $headerstuff['preload_styleSheets'][$url] = 1;
        if (count($headerstuff['custom'])) foreach ($headerstuff['custom'] as &$custom) {
            if (preg_match_all('#<link\s[^>]*href="([^">]*?' . $regexp . '[^">]*?)"[^>]*/?\>#', $custom, $matches)) {
                foreach ($matches[1] as $index => $url) {
                    $headerstuff['preload_styleSheets'][$url] = 1;
                    $custom = str_replace($matches[0][$index], '', $custom);
                }
            }
            $custom = trim($custom);
        }
    }

    /**
     * @param $headerstuff Array
     */
    private function removeJQuery(&$headerstuff)
    {
        $regexp = '/jquery(?:[0-9\.-]|latest|min|pack)*?\.js';
        foreach ($headerstuff['scripts'] as $url => $param)
            if (preg_match('#' . $regexp . '#', $url))
                $this->loaded_js[$url] = 1;
        if (count($headerstuff['custom'])) foreach ($headerstuff['custom'] as &$custom) {
            if (preg_match_all('#<script\s[^>]*src="([^">]*' . $regexp . '[^">]*)"[^>]*></script>#', $custom, $matches)) {
                foreach ($matches[1] as $index => $url) {
                    $this->loaded_js[$url] = 1;
                    $custom = str_replace($matches[0][$index], '', $custom);
                }
            }
            $custom = trim($custom);
        }
    }

    /**
     * @param $headerstuff array
     * @param $text string
     */
    private function generateHead(&$headerstuff, &$text)
    {
        $embedCustom = $headerstuff['custom'];

        if (count($embedCustom)) foreach ($embedCustom as &$custom) {
            if (preg_match_all('#<link\s[^>]*href="([^">]*)"[^>]*/?>#', $custom, $matches)) {
                foreach ($matches[0] as $index => $tag)
                    if (strpos($tag, 'rel="stylesheet"') !== false && isset($this->loaded_css[$matches[1][$index]]))
                        $custom = str_replace($tag, '', $custom);
            }
            if (preg_match_all('#<script\s[^>]*src="([^">]*)"[^>]*></script>#', $custom, $matches)) {
                foreach ($matches[1] as $index => $url)
                    if (isset($this->loaded_js[$url]))
                        $custom = str_replace($matches[0][$index], '', $custom);
            }
        }

        $head = $this->docHeadRender($headerstuff);

        $text = str_replace('<mj:head/>', $head, $text);
    }

    /**
     * @param $headerstuff Array
     * @return string
     */
    private function docHeadRender($headerstuff)
    {
        $document = JFactory::getDocument();
        $buffer = '';

        include dirname(__FILE__) . '/head.php';

        return $buffer;
    }

    private function initURLs()
    {
        // set jQuery version
        switch ((string)$this->params->get('jqueryversion')) {
            case '17': // 1.7.2
                $this->jq_ver = '1.7.2';
                break;
            case '18': // 1.8.3
                $this->jq_ver = '1.8.3';
                break;
            case '19m': // 1.9.1/migrate
                $this->jq_migration = true;
            case '19': // 1.9.1
                $this->jq_ver = '1.9.1';
                break;
            case '110m': // 1.10.2/migrate
                $this->jq_migration = true;
            case '110': // 1.10.2
                $this->jq_ver = '1.10.2';
                break;
            case '111': // 1.11.1
                $this->jq_ver = '1.11.1';
                break;
            case '20m': // 2.0.3/migrate
                $this->jq_migration = true;
            case '20': // 2.0.3
                $this->jq_ver = '2.0.3';
                break;
            case '21': // 2.1.1
                $this->jq_ver = '2.1.1';
                break;
            default: // 1.9.1
                $this->jq_ver = '1.9.1';
        }

        if ($this->params->get('jqmigrate'))
            $this->jq_migration = true;

        // fix default module content theme
        if (!$this->params->get('theme_modulecontent')) {
            $content_theme = $this->params->get('theme_page');
            if (!$content_theme) $content_theme = 'a';
            $this->params->set('theme_modulecontent', $content_theme);
        }

        $jqm_ver = $this->jqm_ver;
        $jq_ver = $this->jq_ver;
        $jqmigr_ver = $this->jq_migration_ver;

        if ($this->params->get('load_external')) {
            include dirname(__FILE__) . '/cdn.php';

            $cdn = $this->params->get('load_external');
            if (!isset($cdnList[$cdn]))
                $cdn = '1';

            $protocol = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https:' : 'http:';

            if ($cdn == '1') // auto (value '1' is for backward compatibility)
                $cdn = 'jquery.com';
//				$cdn = ($protocol==='https:') ? 'aspnetcdn.com' : 'jquery.com';

            $jqm_css_file = 'jquery.mobile.css';

            $this->jqm_jq = $protocol . str_replace('*', $jq_ver, $cdnList[$cdn]['jquery.js']);
            $this->jqm_css = $protocol . str_replace('*', $jqm_ver, $cdnList[$cdn][$jqm_css_file]);
            $this->jqm_jqm = $protocol . str_replace('*', $jqm_ver, $cdnList[$cdn]['jquery.mobile.js']);

            if ($this->jq_migration) {
                $this->jqm_jqmigr = $protocol . str_replace('*', $jqmigr_ver, $cdnList[$cdn]['jquery.migrate.js']);
            }
        } else {
            $jqm_url = $this->base . 'vendor/jqm/';

            $this->jqm_jq = $jqm_url . 'jquery-' . $jq_ver;
            $this->jqm_css = $jqm_url . 'jquery.mobile-' . $jqm_ver;
            $this->jqm_jqm = $jqm_url . 'jquery.mobile-' . $jqm_ver;

            if ($this->jq_migration) {
                $this->jqm_jqmigr = $jqm_url . 'jquery-migrate-' . $jqmigr_ver;
            }
        }

        // Force Mootools loading
        if ($this->params->get('mootools')) {
            $this->forceMootools();
        }
    }

    private function forceMootools()
    {
        $mootoolsFound = false;

        $headerstuff = $this->document->getHeadData();

        $scripts = $headerstuff['scripts'];
        if (!is_array($scripts))
            $scripts = array();

        foreach ($scripts as $url => $meta)
            if (preg_match('#mootools-(?:core|more)(?:-uncompressed)?\.js#', $url)) {
                $mootoolsFound = true;
                break;
            }

        if (!$mootoolsFound) {
            $mootools_js = JHtml::_('script', 'system/mootools-core.js', false, true, true, false);
            // trick for array_unshift with key preserving
            $scripts = array_reverse($scripts, true);
            $scripts[$mootools_js] = array('mime' => 'text/javascript', 'defer' => false, 'async' => false);
            $scripts = array_reverse($scripts, true);
            $headerstuff['scripts'] = $scripts;
            $this->document->setHeadData($headerstuff);
        }
    }

    private function combinerInit()
    {
        $this->combinerSource = array();
    }

    private function combinerAdd($type, $text)
    {
        switch ($type) {
            case 'cssfile':
                if (isset($this->loaded_css[$text]))
                    return;
                break;
            case 'jsfile':
                if (isset($this->loaded_js[$text]))
                    return;
                break;
        }
        $this->combinerSource[] = array('type' => $type, 'text' => $text);
    }

    private function combinerGetHash()
    {
        $hash = '';
        foreach ($this->combinerSource as $src)
            switch ($src['type']) {
                case 'cssfile':
                    $file = $this->htmlGetFullPath('css', $src['text']);
                    $hash .= $src['text'] . '|' . @filemtime(JPATH_ROOT . $file) . '|';
                    break;
                case 'jsfile':
                    $file = $this->htmlGetFullPath('js', $src['text']);
                    $hash .= $src['text'] . '|' . @filemtime(JPATH_ROOT . $file) . '|';
                    break;
                case 'css':
                case 'js':
                    $hash .= $src['text'] . '|';
                    break;
            }
        $hash = substr(sha1($hash), 0, 8);
        return $hash;
    }

    private function htmlGetFullPath($type, $base)
    {
        if (substr($base, -4) == '.css' || substr($base, -3) == '.js' || strpos($base, '/') === false)
            return $base;

        if (!JDEBUG) {
            $full_min = $base . '.min.' . $type;
            if (substr($base, 0, 4) == 'http' || file_exists(JPATH_ROOT . $full_min))
                return $full_min;
        }

        return $base . '.' . $type;
    }

    private function htmlCSS($base)
    {
        $url = $this->htmlGetFullPath('css', $base);
        if (isset($this->loaded_css[$url]))
            return '';
        $this->loaded_css[$url] = 1;
        if (strpos($url, '//') === false)
            $url = $this->document->baseurl . $url;
        return '<link rel="stylesheet" href="' . $url . '">';
    }

    private function htmlJS($base)
    {
        $url = $this->htmlGetFullPath('js', $base);
        if (isset($this->loaded_js[$url]))
            return '';
        $this->loaded_js[$url] = 1;
        if (strpos($url, '//') === false)
            $url = $this->document->baseurl . $url;
        return '<script src="' . $url . '"></script>';
    }

    private function combinerGetDebug()
    {
        $buffer = '';
        foreach ($this->combinerSource as $src)
            switch ($src['type']) {
                case 'cssfile':
                    $buffer .= $this->htmlCSS($src['text']);
                    break;
                case 'jsfile':
                    $buffer .= $this->htmlJS($src['text']);
                    break;
                case 'css':
                    $buffer .= '<style>' . trim($src['text']) . '</style>';
                    break;
                case 'js':
                    $buffer .= "<script>\n" . trim($src['text']) . "\n</script>";
                    break;
            }
        return $buffer;
    }

    private function combinerFixCSSURL($content, $url)
    {
        $base = dirname($url) . '/';
        return preg_replace('#\burl\(\s*?(?:["\'](?!/|\w+?:)|(?![/"\']|\w+?:))#s', '\\0' . $this->document->baseurl . $base, $content);
    }

    private function combinerGetRelease()
    {
        $buffer = '';
        foreach ($this->combinerSource as $src) {
            $content = '';
            switch ($src['type']) {
                case 'cssfile':
                    $url = $this->htmlGetFullPath('css', $src['text']);
                    if (!isset($this->loaded_css[$url])) {
                        $file = JPATH_ROOT . $url;
                        $content = file_get_contents($file);
                        $content = $this->combinerFixCSSURL($content, $url);
                        $this->loaded_css[$url] = 1;
                    }
                    break;
                case 'jsfile':
                    $url = $this->htmlGetFullPath('js', $src['text']);
                    if (!isset($this->loaded_js[$url])) {
                        $file = JPATH_ROOT . $url;
                        $content = trim(file_get_contents($file));
                        if (strlen($content) && !in_array(substr($content, -1), array('}', ';')))
                            $content = $content . ';';
                        $this->loaded_js[$url] = 1;
                    }
                    break;
                case 'css':
                    $content = $src['text'];
                    break;
                case 'js':
                    $content = $src['text'];
                    break;
            }
            $buffer .= $content . "\n";
        }
        return $buffer;
    }

    private function combinerGet($type)
    {
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        /*if(!JDEBUG && $this->params->get('combine'))
        {
            $hash = $this->combinerGetHash();
            $cache_dir = JPATH_THEMES.'/'.$this->template.'/cache';
            $cached_file = $cache_dir.'/'.$hash.'.'.$type;
            $cached_url  = $this->document->baseurl . '/' . ltrim(str_replace(JPATH_ROOT, '', $cached_file), '/\\');

            $success = true;
            if(!JFile::exists($cached_file))
            {
                $content = $this->combinerGetRelease();

                if(!JFile::write($cached_file, $content))
                {
                    JFile::delete($cached_file);
                    $success = false;
                }

                $cached_file_gz = $cached_file . '.gz';
                $content = gzencode($content, 9);
                if(!JFile::write($cached_file_gz, $content))
                    JFile::delete($cached_file_gz);

                $files = JFolder::files($cache_dir, '\.'.$type.'(?:\.gz)?$');
                $exclude = array($cached_file, $cached_file_gz);
                foreach($files as $file)
                {
                    $full = $cache_dir.'/'.$file;
                    if(!in_array($full, $exclude))
                        JFile::delete($full);
                }
            }

            if($success)
            {
                switch($type)
                {
                    case 'css':
                        return '<link rel="stylesheet" href="' . $cached_url . '">';
                    case 'js':
                        return '<script src="' . $cached_url . '"></script>';
                }
            }
        }*/

        return $this->combinerGetDebug();
    }

    /**
     * @return string
     * @info Called from head.php
     */
    private function loadIcons()
    {
        // todo: check UA to print device-specific icons only (be aware of caching proxies!)
        // (touch-icon-57x57.png is used by some androids as well)
        $icons = array(
            'touch-icon-152x152.png' => 'rel="apple-touch-icon" sizes="152x152"', // iPad 3+, iOS 7
            'touch-icon-76x76.png' => 'rel="apple-touch-icon" sizes="76x76"', // iPad 1-2, iOS 7
            'touch-icon-precomposed-144x144.png' => 'rel="apple-touch-icon-precomposed" sizes="144x144"', // iPad 3+
            'touch-icon-precomposed-72x72.png' => 'rel="apple-touch-icon-precomposed" sizes="72x72"', // iPad 1-2
            'touch-icon-144x144.png' => 'rel="apple-touch-icon" sizes="144x144"', // iPad 3+
            'touch-icon-72x72.png' => 'rel="apple-touch-icon" sizes="72x72"', // iPad 1-2

            'touch-icon-120x120.png' => 'rel="apple-touch-icon" sizes="120x120"', // iPhone 4+, iOS 7
            'touch-icon-precomposed-114x114.png' => 'rel="apple-touch-icon-precomposed" sizes="114x114"', // iPhone 4+
            'touch-icon-precomposed-57x57.png' => 'rel="apple-touch-icon-precomposed"', // iPhone 1-3
            'touch-icon-114x114.png' => 'rel="apple-touch-icon" sizes="114x114"', // iPhone 4+
            'touch-icon-57x57.png' => 'rel="apple-touch-icon"', // iPhone 1-3
        );

        $startups = array(
            'touch-startup-image-320x460.png' => 'rel="apple-touch-startup-image"', // iPhone 1-3
            'touch-startup-image-640x920.png' => 'rel="apple-touch-startup-image" media="(device-height: 480px) and (-webkit-device-pixel-ratio: 2)"', // iPhone 4
            'touch-startup-image-640x1096.png' => 'rel="apple-touch-startup-image" media="(device-height: 568px) and (-webkit-device-pixel-ratio: 2)"', // iPhone 5

            'touch-startup-image-768x1004.png' => 'rel="apple-touch-startup-image" sizes="768x1004" media="(min-device-width: 481px) and (max-device-width: 1024px) and (orientation: portrait)"', // iPad 1-2
            'touch-startup-image-1024x748.png' => 'rel="apple-touch-startup-image" sizes="1024x748" media="(min-device-width: 481px) and (max-device-width: 1024px) and (orientation: landscape)"', // iPad 1-2
            'touch-startup-image-1536x2008.png' => 'rel="apple-touch-startup-image" sizes="1536x2008" media="(min-device-width: 481px) and (max-device-width: 1024px) and (orientation: portrait) and (-webkit-min-device-pixel-ratio: 2)"', // iPad 3+
            'touch-startup-image-2048x1496.png' => 'rel="apple-touch-startup-image" sizes="2048x1496" media="(min-device-width: 481px) and (max-device-width: 1024px) and (orientation: landscape) and (-webkit-min-device-pixel-ratio: 2)"', // iPad 3+
        );

        $html = '';

        $base_full = JUri::base() . 'templates/' . $this->template;

        foreach ($icons as $image => $rel)
            if (file_exists(JPATH_THEMES . '/' . $this->template . '/' . $image))
                $html .= "<link $rel href=\"$base_full/$image\">";

        return $html;
    }

    /**
     * @return string
     * @info Called from head.php
     */
    private function loadCSS()
    {
        $html = '';
        $this->combinerInit();

        if ($this->params->get('load_external') && $this->jqm_css) {
            $html .= $this->htmlCSS($this->jqm_css);
        } else {
            if ($this->jqm_css)
                $this->combinerAdd('cssfile', $this->jqm_css);
        }
        $this->combinerAdd('cssfile', $this->base . 'css/structure');
        $this->combinerAdd('cssfile', $this->base . 'css/mj');

        $html .= $this->combinerGet('css');

        return $html;
    }

    /**
     * @return string
     * @info Called from head.php
     */
    private function loadJS()
    {
        $load_external = $this->params->get('load_external');
        $theme_header = $this->params->get('theme_header');
        $enhance = $this->params->get('enhance');

        $html = '';
        $this->combinerInit();

        if ($load_external || $this->compatJQueryEasy) {
            $html .= $this->htmlJS($this->jqm_jq);
            if ($this->jq_migration)
                $html .= $this->htmlJS($this->jqm_jqmigr);
        } else {
            $this->combinerAdd('jsfile', $this->jqm_jq);
            if ($this->jq_migration)
                $this->combinerAdd('jsfile', $this->jqm_jqmigr);
        }

        $mobileinit = array();
//		$mobileinit[] = 'jQuery.mobile.loadingMessage=false;';
        $mobileinit[] = 'jQuery.mobile.pageLoadErrorMessage="' . addslashes(JText::_('TPL_MOBILE_JQM__PAGELOADERROR')) . '";';
        $mobileinit[] = 'jQuery.mobile.ajaxEnabled=false;jQuery.mobile.pushStateEnabled=false;';//jQuery.mobile.hashListeningEnabled=false;
        if ($enhance)
            $mobileinit[] = 'jQuery.mobile.ignoreContentEnabled=true;';

        $mobileinit = "jQuery(document).on('mobileinit',function(){" . implode('', $mobileinit) . "});";
        if ($load_external) {
            $html .= '<script>' . $mobileinit . '</script>';
            $html .= $this->htmlJS($this->jqm_jqm);
        } else {
            $this->combinerAdd('js', $mobileinit);
            $this->combinerAdd('jsfile', $this->jqm_jqm);
        }

        // trick to bypass jQueryEasy regexp
        $postinit = "jqm=jQuery.noConflict(" . ($this->compatJQueryEasySafeNoConflict ? ' ' : '') . ");";
        if ($theme_header) $postinit .= "jqm.mobile.page.prototype.options.headerTheme='$theme_header';";
        $this->combinerAdd('js', $postinit);

        $this->combinerAdd('jsfile', $this->base . 'js/pageinit');

        $js_dir = $this->path . '/js/';
        if (is_file($js_dir . 'custom.js'))
            $this->combinerAdd('jsfile', $this->base . 'js/custom.js');

        $html .= $this->combinerGet('js');

        return $html;
    }
}