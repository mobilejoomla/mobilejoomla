<?php
/**
 * Kuneri Mobile Joomla! for Joomla!1.5
 * http://www.mobilejoomla.com/
 *
 * @version		0.9.0
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	Copyright (C) 2008-2009 Kuneri Ltd. All rights reserved.
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport( 'joomla.plugin.plugin' );

class plgSystemMobileBot extends JPlugin
{
	function plgSystemMobileBot(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onAfterInitialise()
	{
		global $mainframe;
		if($mainframe->isAdmin()) // don't use MobileJoomla in backend
			return;

		$document =& JFactory::getDocument();
		$format = $document->getType();
		if(($format!=='html')&&($format!=='raw')) // don't use MobileJoomla for non-html data
			return;

		// Load config
		include(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php');

		// check for markup chooser module
		plgSystemMobileBot::processMarkupChange($MobileJoomla_Settings);

		$markup=''; //layout markup
		$ismobile=false; //true if mobile device detected

		$parsed=parse_url(JURI::base());
		$path=isset($parsed['path'])?$parsed['path']:'';
		$http=isset($parsed['scheme'])?$parsed['scheme']:'http';

		//Check user choice
		$user_markup=plgSystemMobileBot::getUserMarkup($MobileJoomla_Settings);

		// Check for special domains
		$domains=($MobileJoomla_Settings['domains']=='1');
		if($domains)
		{
			$domain_xhtml =$MobileJoomla_Settings['xhtmldomain'];
			$domain_wap   =$MobileJoomla_Settings['wapdomain'];
			$domain_imode =$MobileJoomla_Settings['imodedomain'];
			$domain_iphone=$MobileJoomla_Settings['iphonedomain'];
			$basehost=$parsed['host'];
			if(substr($basehost,0,4)=='www.') $basehost=substr($basehost,4);
			if(substr($domain_xhtml, -1)=='.') $domain_xhtml .=$basehost;
			if(substr($domain_wap,   -1)=='.') $domain_wap   .=$basehost;
			if(substr($domain_imode, -1)=='.') $domain_imode .=$basehost;
			if(substr($domain_iphone,-1)=='.') $domain_iphone.=$basehost;
			if( $domain_xhtml && $_SERVER['HTTP_HOST']==$domain_xhtml )
			{// Smartphone (xhtml-mp/wap2) domain
				$markup='xhtml';
				$config =& JFactory::getConfig();
				$config->setValue('config.live_site',$http.'://'.$_SERVER['HTTP_HOST'].$path);
				$ismobile=true;
				$domains=false;
			}
			elseif( $domain_wap && $_SERVER['HTTP_HOST']==$domain_wap )
			{// WAP (wml) domain
				$markup='wml';
				$config =& JFactory::getConfig();
				$config->setValue('config.live_site',$http.'://'.$_SERVER['HTTP_HOST'].$path);
				$ismobile=true;
				$domains=false;
			}
			elseif( $domain_imode && $_SERVER['HTTP_HOST']==$domain_imode )
			{// iMode (chtml) domain
				$markup='chtml';
				$config =& JFactory::getConfig();
				$config->setValue('config.live_site',$http.'://'.$_SERVER['HTTP_HOST'].$path);
				$ismobile=true;
				$domains=false;
			}
			elseif( $domain_iphone && $_SERVER['HTTP_HOST']==$domain_iphone )
			{// iPhone/iPod domain
				$markup='iphone';
				$config =& JFactory::getConfig();
				$config->setValue('config.live_site',$http.'://'.$_SERVER['HTTP_HOST'].$path);
				$ismobile=true;
				$domains=false;
			}
		}


		// Check for mobile device
		if(!$ismobile)
		{
			$found=false;
			if($MobileJoomla_Settings['useragent'] && ($MobileJoomla_Settings['useragent']<8)) // all methods except always_<...>
			{
				$useragent=isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
				$useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';
				//precheck for iPhone/iPod
				$iphone_list=array('Mozilla/5.0 (iPod;','Mozilla/5.0 (iPod touch;','Mozilla/5.0 (iPhone;','Apple iPhone ','Mozilla/5.0 (iPhone Simulator;','Mozilla/5.0 (Aspen Simulator;');
				foreach($iphone_list as $iphone_ua)
					if(strpos($useragent,$iphone_ua)===0)
					{// iPhone for sure
						$found=true;
						$ismobile=true;
						$markup='iphone';
						break;
					}
				if(!$found)
				{//precheck for desktops and bots
					function CheckSubstrs($substrs,$text)
					{
						foreach($substrs as $substr)
							if(false!==strpos($text,$substr))
								return true;
						return false;
					}
					$desktop_os_list=array('Windows NT','Macintosh','Mac OS X','Mac_PowerPC','MacPPC','X11','x86_64','ia64','i686','i586','i386','Windows+NT','Windows XP','Windows 2000','Win2000','Windows ME','Win 9x','Windows 98','Windows 95','Win16','Win95','Win98','WinNT','Linux ppc','(OS/2','; OS/2','OpenBSD','FreeBSD','NetBSD','SunOS','BeOS','Solaris','Debian','HP-UX','HPUX','IRIX','Unix','UNIX','OpenVMS','RISC','Darwin','Konqueror','MSIE 7.0','MSIE 8.0');
					$webbots_list=array('Bot','bot','BOT','Crawler','crawler','Spider','Googlebot','ia_archiver','Mediapartners-Google','msnbot','Yahoo! Slurp','YahooSeeker','Validator','W3C-checklink','CSSCheck','GSiteCrawler');
					$wapbots_list=array('Wapsilon','WinWAP','WAP-Browser');

					$found_desktop=CheckSubstrs($desktop_os_list,$useragent_commentsblock) ||
									CheckSubstrs($webbots_list,$useragent);
					$found_mobile=CheckSubstrs($wapbots_list,$useragent);
					if($found_mobile && !$found_desktop)
					{// WAP bot for sure
						$found=true;
						$ismobile=true;
						$markup='wml';
					}
					elseif($found_desktop && !$found_mobile)
					{
					$mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
					$mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160x160','176x220','240x240','240x320','320x240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo');
						$found_mobile=CheckSubstrs($mobile_os_list,$useragent_commentsblock) ||
										CheckSubstrs($mobile_token_list,$useragent);
						if(!$found_mobile)
							$found=true; //Desktop for sure
					}
				}
			}
			if(!$found)
			{//Check for mobile device using selected method
				$methods=array(1=>'accept','wurfl','compactwurfl','devmobi','old','andymoore','browscap','always_pda','always_wap','always_imode','always_iphone');
				$m=$MobileJoomla_Settings['useragent'];
				if($m==2) // check for wurfl installed
				{
					$wurflpath=JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'wurfl'.DS.'data'.DS;
					$wurfl = is_file($wurflpath.'wurfl.xml')
							&& is_file($wurflpath.'web_browsers_patch.xml')
							&& ($MobileJoomla_Settings['wurflcache']==0 || is_file($wurflpath.'cache.php'))
							&& ($MobileJoomla_Settings['wurflcache']!=2 || is_dir($wurflpath.'multicache'));
					if(!$wurfl)
					{//Use CompactWURFL if WURFL isn't loaded
						$m=3;
						$MobileJoomla_Settings['useragent']=3;
						if($MobileJoomla_Settings['tmpl_xhtml_img']>1) $MobileJoomla_Settings['tmpl_xhtml_img']=1;
						if($MobileJoomla_Settings['tmpl_wap_img']>1) $MobileJoomla_Settings['tmpl_wap_img']=1;
						if($MobileJoomla_Settings['tmpl_imode_img']>1) $MobileJoomla_Settings['tmpl_imode_img']=1;
						if($MobileJoomla_Settings['tmpl_iphone_img']>1) $MobileJoomla_Settings['tmpl_iphone_img']=1;
					}
				}
				if(isset($methods[$m]))
				{//Load mobile checking method
					$checkpath=JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'methods'.DS.'checkmobile_'.$methods[$m].'.php';
					if(file_exists($checkpath))
					{
						require $checkpath;
						$markup=CheckMobile();
						if($markup!=='') $ismobile=true;
					}
				}
			}
		}

		if(($ismobile && $user_markup !== '') || $user_markup == 'mobile') //It's mobile device
		{
			if($domains) //Redirect to special domain
			{
				if($MobileJoomla_Settings['xhtmlredirect']&&$markup=='xhtml'&&$domain_xhtml)
				{
					$mainframe->redirect($http.'://'.$domain_xhtml.$path.'/');
					//die();
				}
				if($MobileJoomla_Settings['wapredirect']&&$markup=='wml'&&$domain_wap)
				{
					$mainframe->redirect($http.'://'.$domain_wap.$path.'/');
					//die();
				}
				if($MobileJoomla_Settings['imoderedirect']&&$markup=='chtml'&&$domain_imode)
				{
					$mainframe->redirect($http.'://'.$domain_imode.$path.'/');
					//die();
				}
				if($MobileJoomla_Settings['iphoneredirect']&&$markup=='iphone'&&$domain_iphone)
				{
					$mainframe->redirect($http.'://'.$domain_iphone.$path.'/');
					//die();
				}
			}
			require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.class.php');
			if ($user_markup && $user_markup != 'mobile')
                $markup = $user_markup;

            if (empty ($markup))
                $markup = 'xhtml';

			switch($markup)
			{
			case 'xhtml':
				$template=$MobileJoomla_Settings['xhtmltemplate'];
				$homepage=$MobileJoomla_Settings['xhtmlhomepage'];
				$gzip=$MobileJoomla_Settings['xhtmlgzip'];
				$MobileJoomla =& MobileJoomla::getInstance('xhtmlmp',$MobileJoomla_Settings);
				break;
			case 'wml':
				$template=$MobileJoomla_Settings['waptemplate'];
				$homepage=$MobileJoomla_Settings['waphomepage'];
				$gzip=$MobileJoomla_Settings['wapgzip'];
				$MobileJoomla =& MobileJoomla::getInstance('wml',$MobileJoomla_Settings);
				break;
			case 'chtml':
				$template=$MobileJoomla_Settings['imodetemplate'];
				$homepage=$MobileJoomla_Settings['imodehomepage'];
				$gzip=$MobileJoomla_Settings['imodegzip'];
				$MobileJoomla =& MobileJoomla::getInstance('chtml',$MobileJoomla_Settings);
				break;
			case 'iphone':
				$template=$MobileJoomla_Settings['iphonetemplate'];
				$homepage=$MobileJoomla_Settings['iphonehomepage'];
				$gzip=$MobileJoomla_Settings['iphonegzip'];
				$MobileJoomla =& MobileJoomla::getInstance('iphone',$MobileJoomla_Settings);
				break;
			}
			if(!isset($MobileJoomla))
				return;
			define('_MJ',1);
			//Load some patched Joomla classes
			include_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'joomla.application.component.view.php');
			include_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'joomla.application.module.helper.php');
			//Set template
			if($template)
				$mainframe->setTemplate($template);
			$config =& JFactory::getConfig();
			//Set gzip
			$config->setValue('config.gzip',$gzip);
			// disable cache for PDAs
			$config->setValue('config.cache',false);
			JResponse::clearHeaders();
			$document =& JFactory::getDocument();
			$document->setMimeEncoding($MobileJoomla->getContentType());
			$MobileJoomla->setHeader();
			//Load special homepage
			if( $_SERVER['REQUEST_URI']==$path || $_SERVER['REQUEST_URI']==$path.'index.php' )
			{
				$MobileJoomla->setHome(true);
				if($homepage)
				{
					$_SERVER['REQUEST_URI']=$path.$homepage;
					if(substr($homepage,0,10)=='index.php?')
					{
						$_SERVER['QUERY_STRING']=substr($homepage,10);
						parse_str($_SERVER['QUERY_STRING'],$_REQUEST);
						$_GET=$_REQUEST;
						$_REQUEST+=$_COOKIE;
						$GLOBALS['_JREQUEST']=array(); // reset JRequest cache
					}
				}
			}
		}
		else //It's desktop
		{
			$pcpage=$MobileJoomla_Settings['pcpage'];
			if(($pcpage)&&($pcpage!=='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))
			{
				if(substr($pcpage,0,7)=='http://')
				{
					$mainframe->redirect($pcpage);
					//die();
				}
				$_SERVER['REQUEST_URI']=$path.$pcpage;
				if(substr($pcpage,0,10)=='index.php?')
				{
					$_SERVER['QUERY_STRING']=substr($pcpage,10);
					parse_str($_SERVER['QUERY_STRING'],$_REQUEST);
				}
			}
		}
	}

	// Validate markup
	function CheckMarkup($markup)
	{
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
			$markup=false;
		}
		return $markup;
	}

	function processMarkupChange(&$MobileJoomla_Settings)
	{
		global $mainframe;
		if((@$_GET['option']=='com_mobilejoomla') && (@$_GET['task']=='setmarkup') && isset($_GET['markup']) && isset($_GET['return']))
		{
			$markup=plgSystemMobileBot::CheckMarkup($_GET['markup']);
			if($markup!==false)
				setcookie('mj.markup',$markup,time()+365*24*60*60);
			else
				setcookie('mj.markup','',time()-365*24*60*60);
			$mainframe->setUserState('mobilejoomla.markup',$markup);
			$return=base64_decode($_GET['return']);
			$mainframe->redirect($return);
		}
	}

	function getUserMarkup(&$MobileJoomla_Settings)
	{
		global $mainframe;
		$markup=plgSystemMobileBot::CheckMarkup($mainframe->getUserState('mobilejoomla.markup',false));
		if($markup===false && isset($_COOKIE['mjmarkup']))
		{
			if(($markup=plgSystemMobileBot::CheckMarkup($_COOKIE['mjmarkup']))!==false)
				$mainframe->setUserState('mobilejoomla.markup',$markup);
		}
		return $markup;
	}

	function onAfterRender()
	{
		if(!defined('_MJ')) return;

		$MobileJoomla =& MobileJoomla::getInstance();
		$text=JResponse::getBody();
		$text=$MobileJoomla->processPage($text);
		JResponse::setBody($text);
	}
}
?>