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

class plgMobileSimple extends JPlugin
{
    public function plgMobileSimple(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }

    public function onMjGetDeviceList()
    {
        return array(
            'desktop' => 'Desktop',
            'mobile' => 'Mobile'
        );
    }

    public function onDeviceDetection($mj)
    {
        /** @var MjSettingsModel $mjSettings */
        $mjSettings = $mj->settings;
        /** @var MjDevice $mjDevice */
        $mjDevice = $mj->device;

        if ($mjDevice->markup !== false) {
            return;
        }
        $this->checkAccept($mjSettings, $mjDevice);
        $this->checkUserAgent($mjSettings, $mjDevice);
        if ($mjDevice->markup) {
            $this->checkScreenSize($mjSettings, $mjDevice);
        }
    }

    private function checkAccept(&$mjSettings, &$mjDevice)
    {
        if (!isset($_SERVER['HTTP_ACCEPT'])) {
            return;
        }
        $accept = array('xhtml' => 'application/xhtml+xml',
            'html' => 'text/html',
            'wml' => 'text/vnd.wap.wml',
            'mhtml' => 'application/vnd.wap.xhtml+xml');
        $c = array();
        foreach ($accept as $mime_markup => $mime_type) {
            $c[$mime_markup] = 0;
            if (stristr($_SERVER['HTTP_ACCEPT'], $mime_type)) {
                if (preg_match('|' . str_replace(array('/', '.', '+'), array('\/', '\.', '\+'), $mime_type) . ';q=(0\.\d+)|i', $_SERVER['HTTP_ACCEPT'], $matches)) {
                    $c[$mime_markup] += (float)$matches[1];
                } else {
                    $c[$mime_markup]++;
                }
            }
        }
        $max = max($c);
        foreach ($c as $mime_markup => $val) {
            if ($val !== $max) {
                unset($c[$mime_markup]);
            }
        }
        $mime = 'html';
        if (isset($c['html'])) {
            if (isset($c['xhtml']) && strpos(@$_SERVER['HTTP_USER_AGENT'], 'Profile/MIDP-2.0 Configuration/CLDC-1.1')) {
                $mime = 'xhtml';
            } else {
                $mime = 'html';
            }
        } elseif (isset($c['xhtml'])) {
            $mime = 'xhtml';
        } elseif (isset($c['mhtml'])) {
            $mime = 'mhtml';
        } elseif (isset($c['wml'])) {
            $mime = 'wml';
        }
        $mjDevice->mimetype = $accept[$mime];
        switch ($mime) {
            case 'wml':
                $mjDevice->markup = 'mobile';
                break;
            case 'mhtml':
            case 'xhtml':
                $mjDevice->markup = 'mobile';
                break;
            default:
                $mjDevice->markup = '';
        }
    }

    private function checkUserAgent(&$mjSettings, &$mjDevice)
    {
        $userAgentHeaders = array(
            'HTTP_X_DEVICE_USER_AGENT',
            'HTTP_X_ORIGINAL_USER_AGENT',
            'HTTP_X_OPERAMINI_PHONE_UA',
            'HTTP_X_SKYFIRE_PHONE',
            'HTTP_X_BOLT_PHONE_UA',
            'HTTP_USER_AGENT'
        );
        $useragent = '';
        foreach ($userAgentHeaders as $header) {
            if (isset($_SERVER[$header]) && $_SERVER[$header]) {
                $useragent = $_SERVER[$header];
                break;
            }
        }
        if (empty($useragent)) {
            return;
        }

        $iphone_list = array('Mozilla/5.0 (iPod;',
            'Mozilla/5.0 (iPod touch;',
            'Mozilla/5.0 (iPhone;',
            'Apple iPhone ',
            'Mozilla/5.0 (iPhone Simulator;',
            'Mozilla/5.0 (Aspen Simulator;',
            'Mozilla/5.0 (device; U; CPU iPhone OS');
        foreach ($iphone_list as $iphone_ua)
            if (strpos($useragent, $iphone_ua) === 0) {
                $mjDevice->markup = 'mobile';
                return;
            }

        if (((substr($useragent, 0, 10) === 'portalmmm/') ||
            (substr($useragent, 0, 7) === 'DoCoMo/'))
        ) {
            $mjDevice->markup = 'mobile';
            return;
        }

        $useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';

        $desktop_os_list = array('Windows NT', 'Macintosh', 'Mac OS X', 'Mac_PowerPC', 'MacPPC', 'X11',
            'x86_64', 'ia64', 'i686', 'i586', 'i386', 'Windows+NT', 'Windows XP',
            'Windows 2000', 'Win2000', 'Windows ME', 'Win 9x', 'Windows 98',
            'Windows 95', 'Win16', 'Win95', 'Win98', 'WinNT', 'Linux ppc', '(OS/2',
            '; OS/2', 'OpenBSD', 'FreeBSD', 'NetBSD', 'SunOS', 'BeOS', 'Solaris',
            'Debian', 'HP-UX', 'HPUX', 'IRIX', 'Unix', 'UNIX', 'OpenVMS', 'RISC',
            'Darwin', 'Konqueror', 'MSIE 7.0', 'MSIE 8.0', 'MSIE 9.0');
        $webbots_list = array('Bot', 'bot', 'BOT', 'Crawler', 'crawler', 'Spider', 'Googlebot',
            'ia_archiver', 'Mediapartners-Google', 'msnbot', 'Yahoo! Slurp', 'YahooSeeker',
            'Validator', 'W3C-checklink', 'CSSCheck', 'GSiteCrawler');

        $found_desktop = $this->CheckSubstrs($desktop_os_list, $useragent_commentsblock) ||
            $this->CheckSubstrs($webbots_list, $useragent);
        if ($found_desktop) {
            $mjDevice->markup = '';
            return;
        }

        $wapbots_list = array('Wapsilon', 'WinWAP', 'WAP-Browser');
        $found_mobilebot = $this->CheckSubstrs($wapbots_list, $useragent);
        if ($found_mobilebot) {
            $mjDevice->markup = 'mobile';
            return;
        }

        $mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian',
            'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo',
            'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ', 'webOS/');
        $mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160x160', '176x220',
            '240x240', '240x320', '320x240', 'UP.Browser', 'UP.Link', 'SymbianOS',
            'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry',
            'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_',
            'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'Mobile Safari');
        $found_mobile = $this->CheckSubstrs($mobile_os_list, $useragent_commentsblock) ||
            $this->CheckSubstrs($mobile_token_list, $useragent);
        if ($found_mobile) {
            $mjDevice->markup = 'mobile';
            return;
        }
    }

    private function checkScreenSize(&$mjSettings, &$mjDevice)
    {
        if (isset($_SERVER['HTTP_X_SCREEN_WIDTH']) && $_SERVER['HTTP_X_SCREEN_WIDTH']
            && isset($_SERVER['HTTP_X_SCREEN_HEIGHT']) && $_SERVER['HTTP_X_SCREEN_HEIGHT']
        ) {
            $mjDevice->screenwidth = (int)$_SERVER['HTTP_X_SCREEN_WIDTH'];
            $mjDevice->screenheight = (int)$_SERVER['HTTP_X_SCREEN_HEIGHT'];
            return;
        }
        if (isset($_SERVER['HTTP_X_BROWSER_WIDTH']) && $_SERVER['HTTP_X_BROWSER_WIDTH']
            && isset($_SERVER['HTTP_X_BROWSER_HEIGHT']) && $_SERVER['HTTP_X_BROWSER_HEIGHT']
        ) {
            $mjDevice->screenwidth = (int)$_SERVER['HTTP_X_BROWSER_WIDTH'];
            $mjDevice->screenheight = (int)$_SERVER['HTTP_X_BROWSER_HEIGHT'];
            return;
        }
        if (isset($_SERVER['HTTP_ACCEPT'])
            && preg_match('#,ss/(\d+)x(\d+),#i', $_SERVER['HTTP_ACCEPT'], $matches)
        ) {
            $mjDevice->screenwidth = (int)$matches[1];
            $mjDevice->screenheight = (int)$matches[2];
            return;
        }
        if (isset($_SERVER['HTTP_X_OS_PREFS'])
            && preg_match('#fw:(\d+);\s*fh:(\d+);#i', $_SERVER['HTTP_X_OS_PREFS'], $matches)
        ) {
            $mjDevice->screenwidth = (int)$matches[1];
            $mjDevice->screenheight = (int)$matches[2];
            return;
        }
        if (isset($_SERVER['HTTP_X_OPERA_INFO'])
            && preg_match('#sw=(\d+),\s*sh=(\d+)#i', $_SERVER['HTTP_X_OPERA_INFO'], $matches)
        ) {
            $mjDevice->screenwidth = (int)$matches[1];
            $mjDevice->screenheight = (int)$matches[2];
            return;
        }
        if (isset($_SERVER['HTTP_X_SKYFIRE_SCREEN'])
            && preg_match('#^(\d{3,4}),(\d{3,4})(,|$)#', $_SERVER['HTTP_X_SKYFIRE_SCREEN'], $matches)
        ) {
            $mjDevice->screenwidth = (int)$matches[1];
            $mjDevice->screenheight = (int)$matches[2];
            return;
        }

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = $_SERVER['HTTP_USER_AGENT'];
            if (strpos($ua, ' WQVGA ') !== false) {
                $mjDevice->screenwidth = 240;
                $mjDevice->screenheight = 400;
                return;
            }
            if (strpos($ua, ' HVGA ') !== false) {
                $mjDevice->screenwidth = 320;
                $mjDevice->screenheight = 480;
                return;
            }
            if (strpos($ua, ' WVGA ') !== false) {
                $mjDevice->screenwidth = 480;
                $mjDevice->screenheight = 800;
                return;
            }
            if (strpos($ua, ' resolution\\') !== false
                && preg_match('# resolution\\\\(\d{3})(\d{3})\b#', $ua, $matches)
            ) {
                $mjDevice->screenwidth = (int)$matches[1];
                $mjDevice->screenheight = (int)$matches[2];
                return;
            }
            if (strpos($ua, ';LCD/') !== false
                && preg_match('#;LCD/(\d{3})(\d{3});#', $ua, $matches)
            ) {
                $mjDevice->screenwidth = (int)$matches[1];
                $mjDevice->screenheight = (int)$matches[2];
                return;
            }
        }

        $screen = '';
        if (empty($screen) && isset($_SERVER['HTTP_UA_PIXELS'])) {
            $screen = $_SERVER['HTTP_UA_PIXELS'];
        }
        if (empty($screen) && isset($_SERVER['HTTP_X_UP_DEVCAP_SCREENPIXELS'])) {
            $screen = $_SERVER['HTTP_X_UP_DEVCAP_SCREENPIXELS'];
        }
        if (empty($screen) && isset($_SERVER['HTTP_X_JPHONE_DISPLAY'])) {
            $screen = $_SERVER['HTTP_X_JPHONE_DISPLAY'];
        }
        if (empty($screen) && isset($_SERVER['HTTP_X_AVANTGO_SCREENSIZE'])) {
            $screen = base64_decode($_SERVER['HTTP_X_AVANTGO_SCREENSIZE']);
        }
        if (empty($screen) && isset($_SERVER['HTTP_USER_AGENT'])
            && preg_match('#(\b\d{3,4}x\d{3,4}\b|(?<=scr=)\d{3,4}_\d{3,4}\b)#', $_SERVER['HTTP_USER_AGENT'], $matches)
        ) {
            $screen = $matches[0];
        }

        if (empty($screen)) {
            return;
        }
        $screen = preg_split('#[x*,_]#i', $screen);
        if (count($screen) === 2) {
            $mjDevice->screenwidth = (int)$screen[0];
            $mjDevice->screenheight = (int)$screen[1];
        }
    }

    /**
     * @param array $substrs
     * @param string $text
     * @return bool
     */
    private function CheckSubstrs($substrs, $text)
    {
        foreach ($substrs as $substr) {
            if (false !== strpos($text, $substr)) {
                return true;
            }
        }
        return false;
    }
}
