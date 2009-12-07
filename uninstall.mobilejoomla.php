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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

global $MJ_version;
$MJ_version='';
// get version of current package
$manifest = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.xml';
if(is_file($manifest))
{
	jimport('domit.xml_domit_lite_include');
	$xmlDoc = new DOMIT_Lite_Document();
	$xmlDoc->resolveErrors(false);
	if($xmlDoc->loadXML($manifest, false, true))
	{
		$root = &$xmlDoc->documentElement;
		$element = &$root->getElementsByPath('version', 1);
		$MJ_version = $element ? $element->getText() : '';
	}
}


function InstallSystemMambot($sourcedir,$name,$fullname,$publish=1)
{
	global $MambotsSystem,$ERRORS;
	$upgrade=false;
	if(is_file($MambotsSystem.DS.$name.'.php'))
	{
		$upgrade=true;
		JFile::delete($MambotsSystem.DS.$name.'.php');
		JFile::delete($MambotsSystem.DS.$name.'.xml');
	}
	$status=TRUE;
	if(!JFile::copy($sourcedir.DS.$name.'.php',$MambotsSystem.DS.$name.'.php'))
	{
		$ERRORS[]=str_replace(array('%1','%2'),array($sourcedir.DS.$name.'.php',$MambotsSystem.DS.$name.'.php'),MJ_LANG_ERROR_CANNOTCOPY);
		$status=FALSE;
	}
	if(!JFile::copy($sourcedir.DS.$name.'.xm_',$MambotsSystem.DS.$name.'.xml'))
	{
		$ERRORS[]=str_replace(array('%1','%2'),array($sourcedir.DS.$name.'.xm_',$MambotsSystem.DS.$name.'.xml'),MJ_LANG_ERROR_CANNOTCOPY);
		$status=FALSE;
	}
	if(!$upgrade)
	{
		$database =& JFactory::getDBO();
		$database->setQuery("INSERT INTO `#__plugins` (`name`, `element`, `folder`, `published`) VALUES ('$fullname', '$name', 'system', $publish);");
		$database->query();
	}
	return $status;
}

function UninstallSystemMambot($name)
{
	global $MambotsSystem;
	$database =& JFactory::getDBO();
	$database->setQuery( 'DELETE FROM #__plugins WHERE `element` = '.$database->Quote($name));
	$database->query();
	$status = true;
	$status &= JFile::delete($MambotsSystem.DS.$name.'.php');
	$status &= JFile::delete($MambotsSystem.DS.$name.'.xml');
	return $status;
}

function InstallTemplate($sourcedir,$name)
{
	global $Templates,$ERRORS;
	if(!is_dir($sourcedir))
	{
		$ERRORS[]=MJ_LANG_ERROR_CANNOTFINDDIR." $sourcedir.";
		return FALSE;
	}
	if(is_dir($Templates.DS.$name))
	{
		JFolder::delete($Templates.DS.$name);
	}
	$status=JFolder::copy($sourcedir,$Templates.DS.$name,'',true);
	if(is_file($Templates.DS.$name.DS.'templateDetails.xm_')&&
		!JFile::move($Templates.DS.$name.DS.'templateDetails.xm_',$Templates.DS.$name.DS.'templateDetails.xml'))
	{
		$ERRORS[]=str_replace(array('%1','%2'),array($Templates.DS.$name.DS.'templateDetails.xm_',$Templates.DS.$name.DS.'templateDetails.xml'),MJ_LANG_ERROR_CANNOTRENAME);
		$status=FALSE;
	}
	return $status;
}

function UninstallTemplate($name)
{
	global $Templates,$ERRORS;
	$database =& JFactory::getDBO();
	$quotedname=$database->Quote($name);
	$database->setQuery("DELETE FROM #__templates_menu WHERE client_id = 0 AND template = $quotedname");
	$database->query();
	if(!JFolder::delete($Templates.DS.$name))
	{
		$ERRORS[]=MJ_LANG_ERROR_CANNOTREMOVEDIR.' '.$Templates.DS.$name;
		return FALSE;
	}
	return TRUE;
}

function InstallModule($sourcedir,$name,$title,$position,$published=1,$showtitle=1)
{
	global $Modules,$ERRORS;
	if(!is_dir($sourcedir))
	{
		$ERRORS[]=MJ_LANG_ERROR_CANNOTFINDDIR." $sourcedir.";
		return FALSE;
	}
	$upgrade=false;
	if(is_file($Modules.DS.$name.DS.$name.'.php'))
	{
		$upgrade=true;
		JFolder::delete($Modules.DS.$name);
	}
	if(!$upgrade)
	{
		$database =& JFactory::getDBO();
		$database->setQuery( "SELECT id FROM #__modules WHERE module = '$name' AND client_id = 0" );
		$ids=$database->loadResultArray();
		foreach($ids as $id)
		{
			$database->setQuery( "DELETE FROM #__modules_menu WHERE moduleid = $id" );
			$database->query();
		}
		$database->setQuery( "DELETE FROM #__modules WHERE module = '$name' AND client_id = 0" );
		$database->query();
	}
	if(!JFolder::copy($sourcedir.DS.$name,$Modules.DS.$name,'',true))
	{
		$ERRORS[]=str_replace(array('%1','%2'),array($sourcedir.DS.$name,$Modules.DS.$name.DS),MJ_LANG_ERROR_CANNOTCOPY);
		return FALSE;
	}
	if(is_file($Modules.DS.$name.DS.$name.'.xm_')&&
		!JFile::move($Modules.DS.$name.DS.$name.'.xm_',$Modules.DS.$name.DS.$name.'.xml'))
	{
		$ERRORS[]=str_replace(array('%1','%2'),array($Modules.DS.$name.DS.$name.'.xm_',$Modules.DS.$name.DS.$name.'.xml'),MJ_LANG_ERROR_CANNOTRENAME);
		return FALSE;
	}
	if(!$upgrade)
	{
		if(!is_array($position)) $position=array($position);
		foreach($position as $pos)
		{
			$database->setQuery( "INSERT INTO `#__modules` (`title`, `content`, `ordering`, `position`, `published`, `module`, `showtitle`, `params`) VALUES ('$title', '', 1, '$pos', $published, '$name', '$showtitle', '')" );
			$database->query();
			$id=(int)$database->insertid();
			$database->setQuery( "INSERT INTO `#__modules_menu` VALUES ( $id, 0 )" );
			$database->query();
		}
	}
	return TRUE;
}

function UninstallModule($name)
{
	global $Modules;
	$database =& JFactory::getDBO();
	$database->setQuery( "SELECT id FROM #__modules WHERE module = '$name' AND client_id = 0" );
	$ids=$database->loadResultArray();
	foreach($ids as $id)
	{
		$database->setQuery( "DELETE FROM #__modules_menu WHERE moduleid = $id" );
		$database->query();
	}
	$database->setQuery( "DELETE FROM #__modules WHERE module = '$name' AND client_id = 0" );
	$database->query();
	if(!JFolder::delete($Modules.DS.$name))
	{
		$ERRORS[]=MJ_LANG_ERROR_CANNOTREMOVEDIR.' '.$Modules.DS.$name;
		return FALSE;
	}
	return TRUE;
}

function UpdateConfig($botparams=null)
{
	global $ERRORS;
	global $MJ_version;
	$configfile=JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php';
	$defconfigfile=JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'defconfig.php';
	if(is_file($configfile))
		include($configfile);
	elseif(is_file($defconfigfile))
		include($defconfigfile);
	else
	{
		$ERRORS[]=MJ_LANG_ERROR_CANNOTFIND." $defconfigfile";
		return FALSE;
	}

	$settings=array('useragent','domains','pcpage','templatewidth','jpegquality',
		'wurflcache','wurfluacache',
		'xhtmltemplate','xhtmlhomepage','xhtmlgzip','xhtmldomain','xhtmlredirect',
		'waptemplate','waphomepage','wapgzip','wapdomain','wapredirect',
		'imodetemplate','imodehomepage','imodegzip','imodedomain','imoderedirect',
		'iphonetemplate','iphonehomepage','iphonegzip','iphonedomain','iphoneredirect',
		'tmpl_xhtml_header1','tmpl_xhtml_header2','tmpl_xhtml_pathway','tmpl_xhtml_pathwayhome',
		'tmpl_xhtml_middle1','tmpl_xhtml_middle2','tmpl_xhtml_componenthome','tmpl_xhtml_footer1',
		'tmpl_xhtml_footer2','tmpl_xhtml_jfooter','tmpl_xhtml_simplehead','tmpl_xhtml_allowextedit',
		'tmpl_xhtml_removetags','tmpl_xhtml_removescripts','tmpl_xhtml_img','tmpl_xhtml_entitydecode','tmpl_xhtml_embedcss',
		'tmpl_xhtml_contenttype','tmpl_xhtml_xmlhead','tmpl_xhtml_doctype','tmpl_xhtml_xmlns',
		'tmpl_wap_header','tmpl_wap_pathway','tmpl_wap_pathwayhome','tmpl_wap_middle',
		'tmpl_wap_componenthome','tmpl_wap_footer','tmpl_wap_cards','tmpl_wap_jfooter',
		'tmpl_wap_removetags','tmpl_wap_img','tmpl_wap_entitydecode',
		'tmpl_wap_doctype',
		'tmpl_imode_header1','tmpl_imode_header2','tmpl_imode_pathway','tmpl_imode_pathwayhome',
		'tmpl_imode_middle1','tmpl_imode_middle2','tmpl_imode_componenthome','tmpl_imode_footer1',
		'tmpl_imode_footer2','tmpl_imode_jfooter','tmpl_imode_removetags','tmpl_imode_img',
		'tmpl_imode_entitydecode','tmpl_imode_doctype',
		'tmpl_iphone_header1','tmpl_iphone_header2','tmpl_iphone_pathway','tmpl_iphone_pathwayhome',
		'tmpl_iphone_middle1','tmpl_iphone_middle2','tmpl_iphone_componenthome','tmpl_iphone_footer1',
		'tmpl_iphone_footer2','tmpl_iphone_jfooter','tmpl_iphone_img'
	);
	$params=array();
	foreach($settings as $param)
	{
		if($botparams && isset($botparams->$param))
			$MobileJoomla_Settings[$param]=$botparams->$param;
		if(is_numeric($MobileJoomla_Settings[$param]))
			$params[]="'$param'=>".$MobileJoomla_Settings[$param];
		else
			$params[]="'$param'=>'".addslashes($MobileJoomla_Settings[$param])."'";
	}
	$config = "<?php\n"
			. "defined( '_JEXEC' ) or die( 'Restricted access' );\n"
			. "\n"
			. "\$MobileJoomla_Settings=array(\n"
			. "'version'=>'$MJ_version',\n"
			. implode(",\n",$params)."\n"
			. ");\n"
			. "?>";
	if(!JFile::write($configfile,$config))
	{
		$ERRORS[]=MJ_LANG_ERROR_CANNOTUPDATE." $configfile";
		return FALSE;
	}
	else
		JFile::delete($defconfigfile);
	return TRUE;
}

function com_install()
{
//check joomla version
	if(!defined('_JEXEC'))
	{
		 echo '<font color=red><b><u>This component is released for Joomla!1.5.x</u></b></font><br />Installation stopped.';
		 return true;
	}

	global $ERRORS,$WARNINGS,$UPDATES;
	global $MambotsSystem, $Templates, $Modules, $upgrade;
	global $MJ_version;
	$ERRORS=array();
	$WARNINGS=array();
	$UPDATES=array();
	$upgrade=false;

	$database =& JFactory::getDBO();
	$lang =& JFactory::getLanguage();
	$mosConfig_lang = $lang->getBackwardLang();
	set_time_limit(600);
	JError::setErrorHandling(E_ERROR,'Message');
  
  //check if wurfl scripts are in correct permission mode (0755) , change if not
  $wurflCacheFile = dirname(__FILE__).'/wurfl/upload_cache.php';
  $wurflDownloadFile = dirname(__FILE__).'/wurfl/wurfl_download.php';
  
  if (is_file($wurflCacheFile) && substr(decoct( fileperms($wurflCacheFile) ), 1) != '0755')
  {
    $chmodDone = chmod($wurflCacheFile, '0755');
    if( ! $chmodDone)
    {
      $WARNINGS[]="<b>File Permissions Warning:</b> Make sure ".$wurflCacheFile." has correct file permissions.";
    }
  }
  if (is_file($wurflDownloadFile) && substr(decoct( fileperms($wurflDownloadFile ) ), 1) != '0755')
  {
     $chmodDone = chmod($wurflDownloadFile, '0755');
    if( ! $chmodDone)
    {
      $WARNINGS[]="<b>File Permissions Warning:</b> Make sure ".$wurflDownloadFile." has correct file permissions.";
    }
  }

	$languagepath=JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'languages'.DS;
	if(is_file($languagepath.$mosConfig_lang.'.php'))
		include($languagepath.$mosConfig_lang.'.php');
	elseif(is_file($languagepath.'english.php'))
		include($languagepath.'english.php');
	else
		$ERRORS[]="<b>Installation error:</b> language file '${languagepath}english.php' is not found.";

	// check for upgrade
	$prev_version = '';
	$manifest = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.xml';
	if(is_file($manifest))
	{
		jimport('domit.xml_domit_lite_include');
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors(false);
		if($xmlDoc->loadXML($manifest, false, true))
		{
			$root = &$xmlDoc->documentElement;
			$element = &$root->getElementsByPath('version', 1);
			$prev_version = $element ? $element->getText() : '';
			if($prev_version)
			{
				$upgrade = true;
				$UPDATES[] = MJ_LANG_UPGRADE.' '.$prev_version;
			}
		}
	}

	// set icons for menu
	$database->setQuery( "UPDATE #__components"
					. "\n SET admin_menu_img='../administrator/components/com_mobilejoomla/images/mj16x16.gif' "
					. "\n WHERE admin_menu_link='option=com_mobilejoomla'" );
	$database->query();
	$database->setQuery( "UPDATE #__components"
					. "\n SET admin_menu_img='js/ThemeOffice/config.png'"
					. "\n WHERE admin_menu_link='option=com_mobilejoomla&task=settings'" );
	$database->query();
	$database->setQuery( "UPDATE #__components"
					. "\n SET admin_menu_img='../administrator/components/com_mobilejoomla/images/wurfl16x16.gif' "
					. "\n WHERE admin_menu_link='option=com_mobilejoomla&task=wurfl'" );
	$database->query();
	$database->setQuery( "UPDATE #__components"
					. "\n SET admin_menu_img='js/ThemeOffice/info.png'"
					. "\n WHERE admin_menu_link='option=com_mobilejoomla&task=about'" );
	$database->query();

	$MambotsSystem=JPATH_SITE.DS.'plugins'.DS.'system';
	$Templates=JPATH_SITE.DS.'templates';
	$Modules=JPATH_SITE.DS.'modules';

	$database->setQuery( "SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0" );
	$cur_template=$database->loadResult();

	if(is_file($MambotsSystem.DS.'pdabot.php'))
	{//upgrade from PDA-mambot 1.0 for J!1.5
		$database->setQuery( "SELECT params FROM #__plugins WHERE element = 'pdabot' AND folder = 'system'" );
		$params = $database->loadResult();
		$templateparams = JFile::read(JPATH_SITE.DS.'templates'.DS.'pda'.'params.ini');
		$botParams = new JParameter( $params."\r\n".$templateparams );
		$botParams->set('domains',$botParams->get('subdomain',1));
		$botParams->set('xhtmldomain',$botParams->get('subdomainname','pda').'.');
		$botParams->set('tmpl_xhtml_header1',$botParams->get('header1','header'));
		$botParams->set('tmpl_xhtml_header2',$botParams->get('header2',''));
		$botParams->set('tmpl_xhtml_pathway',$botParams->get('pathway',''));
		$botParams->set('tmpl_xhtml_pathwayhome',$botParams->get('pathwayhome',''));
		$botParams->set('tmpl_xhtml_middle1',$botParams->get('middle1',''));
		$botParams->set('tmpl_xhtml_middle2',$botParams->get('middle2',''));
		$botParams->set('tmpl_xhtml_componenthome',$botParams->get('componentonhome',1));
		$botParams->set('tmpl_xhtml_footer1',$botParams->get('footer1','footer'));
		$botParams->set('tmpl_xhtml_footer2',$botParams->get('footer2',''));
		$botParams->set('tmpl_xhtml_jfooter',$botParams->get('jfooter',1));
		$botParams->set('xhtmlhomepage',$botParams->get('homepage',1));
		$botParams->set('tmpl_xhtml_simplehead',$botParams->get('head',0));
		$botParams->set('tmpl_xhtml_allowextedit',$botParams->get('allowextedit',0));
		$botParams->set('tmpl_xhtml_img',$botParams->get('removeimg',0));
		$totalremove=$botParams->get('removeiframe',0)
					+$botParams->get('removeobject',0)
					+$botParams->get('removeapplet',0)
					+$botParams->get('removeembed',0)
					+$botParams->get('removescript',0);
		$botParams->set('tmpl_xhtml_removetags',$totalremove>2?1:0);
		$botParams->set('tmpl_xhtml_utf8',$botParams->get('utf',0));
		$botParams->set('tmpl_xhtml_embedcss',$botParams->get('embedcss',0));
		$contentconvert=array(-1=>0,0=>3,1=>1,2=>2,3=>4);
		$botParams->set('tmpl_xhtml_contenttype',$contentconvert[$botParams->get('content',-1)]);
		$botParams->set('tmpl_xhtml_xmlhead',$botParams->get('xmlhead',1));
		$botParams->set('tmpl_xhtml_xmlns',$botParams->get('xmlhtml',1));
		$doctypeconvert=array(0=>0,1=>2,2=>7,3=>8);
		$botParams->set('tmpl_xhtml_doctype',$doctypeconvert[$botParams->get('doctype',1)]);
		$botParams->set('xhtmlgzip',$botParams->get('gzip',0));
		$res=UpdateConfig( $botParams );
		if(UninstallSystemMambot('pdabot'))
			$UPDATES[]=MJ_LANG_UPDATE_UNINSTALL." PDA-mambot";
		else
			$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTUNINSTALL." PDA-mambot.</b>";
		if($cur_template=='pda')
			$ERRORS[]="<b>Cannot delete 'pda' template because it is your default template.</b>";
		else
		{
			if(UninstallTemplate('pda'))
				$UPDATES[]=MJ_LANG_UPDATE_UNINSTALL." PDA-template";
			else
				$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTUNINSTALL." PDA-template.</b>";
		}
	}
	else // Upgrade/Install MobileJoomla config
		UpdateConfig();

//install bot
	$MambotSource=JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'plugin';
	if(InstallSystemMambot($MambotSource,'mobilebot','Mobile Joomla Bot'))
		JFolder::delete($MambotSource);
	else
		$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTINSTALL." Mobile Joomla Bot.</b>";

// install templates
	$TemplateSource=JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'templates';
	if($t1=InstallTemplate($TemplateSource.DS.'mobile_pda','mobile_pda'))
		JFolder::delete($TemplateSource.DS.'mobile_pda');
	else
		$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTINSTALL." Mobile Joomla 'mobile_pda' template.</b>";
	if($t2=InstallTemplate($TemplateSource.DS.'mobile_wap','mobile_wap'))
		JFolder::delete($TemplateSource.DS.'mobile_wap');
	else
		$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTINSTALL." Mobile Joomla 'mobile_wap' template.</b>";
	if($t3=InstallTemplate($TemplateSource.DS.'mobile_imode','mobile_imode'))
		JFolder::delete($TemplateSource.DS.'mobile_imode');
	else
		$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTINSTALL." Mobile Joomla 'mobile_imode' template.</b>";
	if($t4=InstallTemplate($TemplateSource.DS.'mobile_iphone','mobile_iphone'))
		JFolder::delete($TemplateSource.DS.'mobile_iphone');
	else
		$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTINSTALL." Mobile Joomla 'mobile_iphone' template.</b>";
	if($t1&&$t2&&$t3&&$t4)
		JFolder::delete($TemplateSource);

//install modules (over existing)
	$ModuleSource=JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'modules';
	$status=TRUE;
	$status &= InstallModule($ModuleSource,'mod_mj_header',       'Header Module','mj_pda_header', 1, 0);
	//$status &= InstallModule($ModuleSource,'mod_mj_pda_search',   'Search Module','mj_pda_header', 0);
	$status &= InstallModule($ModuleSource,'mod_mj_pda_menu',     'Main Menu',    'mj_pda_header2', 1, 0);
	//$status &= InstallModule($ModuleSource,'mod_mj_pda_login',    'Login Form',   'mj_pda_footer', 0);
	$status &= InstallModule($ModuleSource,'mod_mj_wap_search',   'Search Module','mj_wap_cards');
	$status &= InstallModule($ModuleSource,'mod_mj_wap_menu',     'Main Menu',    'mj_wap_footer');
	//$status &= InstallModule($ModuleSource,'mod_mj_wap_login',    'Login Form',   'mj_wap_cards',0);
	$status &= InstallModule($ModuleSource,'mod_mj_imode_search', 'Search Module','mj_imode_header');
	//$status &= InstallModule($ModuleSource,'mod_mj_imode_login',  'Login Form',   'mj_imode_footer', 0);
	$status &= InstallModule($ModuleSource,'mod_mj_imode_menu',   'Main Menu',    'mj_imode_footer');
	//$status &= InstallModule($ModuleSource,'mod_mj_iphone_login', 'Login Form',   'mj_imode_footer',0);
	//$status &= InstallModule($ModuleSource,'mod_mj_iphone_search','Search Module','mj_iphone_header', 0);
	$status &= InstallModule($ModuleSource,'mod_mj_iphone_menu',  'Main Menu',    'mj_iphone_middle');
	//$status &= InstallModule($ModuleSource,'mod_mj_iphone_login', 'Login Form',   'mj_iphone_footer',0);
	$status &= InstallModule($ModuleSource,'mod_mj_markupchooser','Select Markup', array('footer','mj_pda_footer2','mj_wap_footer','mj_imode_footer','mj_iphone_footer2'),1,0);
	if($status)
		JFolder::delete($ModuleSource);
	else
		$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTINSTALL." Mobile Joomla modules.</b>";

	$query = "CREATE TABLE IF NOT EXISTS `#__capability` ("
			." `ua` varchar(250) NOT NULL default '',"
			." `format` varchar(4) NOT NULL default '',"
			." `devwidth` int(11) NOT NULL default '0',"
			." `devheight` int(11) NOT NULL default '0'"
			." ) TYPE=MyISAM;";
	$database->setQuery($query);
	$database->query();

//Show install log
	$msg='';
	if(count($ERRORS))
		$msg.='<font color=red><b>'.MJ_LANG_ERRORS.'</b></font><br />'.implode('<br />',$ERRORS).'<br /><br />';
	if(count($WARNINGS))
		$msg.='<font color=blue><b>'.MJ_LANG_WARNINGS.'</b></font><br />'.implode('<br />',$WARNINGS).'<br /><br />';
	if(count($UPDATES))
		$msg.='<font color=green><b>'.MJ_LANG_UPDATES.'</b></font><br />'.implode('<br />',$UPDATES).'<br /><br />';
	if(count($ERRORS)==0)
		$msg.=str_replace('[VER]',$MJ_version,MJ_LANG_INSTALL_OK);
?>
<link rel="stylesheet" type="text/css" href="http://www.mobilejoomla.com/checker.php?v=<?php echo urlencode($MJ_version); ?>&s=1" />
<a href="http://www.mobilejoomla.com/" id="mjupdate" target="_blank"></a>
<?php
	echo $msg;
	return true;
}

function com_uninstall()
{
	set_time_limit(600); // Because of a lot of files in the wurfl cache
	global $ERRORS,$WARNINGS,$UPDATES;
	global $MambotsSystem, $Templates, $Modules;
	global $MJ_version;
	$ERRORS=array();
	$WARNINGS=array();
	$UPDATES=array();

	$database =& JFactory::getDBO();
	$lang =& JFactory::getLanguage();
	$mosConfig_lang = $lang->getBackwardLang();

	$languagepath=JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'languages'.DS;
	if(is_file($languagepath.$mosConfig_lang.'.php'))
		include($languagepath.$mosConfig_lang.'.php');
	elseif(is_file($languagepath.'english.php'))
		include($languagepath.'english.php');
	else
		$ERRORS[]="<b>Uninstallation error:</b> language file '${languagepath}english.php' is not found.";

	$MambotsSystem=JPATH_SITE.DS.'plugins'.DS.'system';
	$Templates=JPATH_SITE.DS.'templates';
	$Modules=JPATH_SITE.DS.'modules';

	$database->setQuery( "SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0" );
	$cur_template=$database->loadResult();
//uninstall bot
	if(!UninstallSystemMambot('mobilebot'))
		$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTUNINSTALL." Mobile Joomla 3.0.alpha Mambot.</b>";
//uninstall templates
	$templateslist=array('mobile_pda','mobile_wap','mobile_imode','mobile_iphone');
	foreach($templateslist as $t)
	{
		if($cur_template==$t)
			$ERRORS[]="<b>".str_replace('%1',$t,MJ_LANG_ERROR_CANNOTDELTEMPLATE)."</b>";
		elseif(!UninstallTemplate($t))
			$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTUNINSTALL." Mobile Joomla '$t' template.</b>";
	}

	$query = "DROP TABLE `#__capability`;";
	$database->setQuery($query);
	$database->query();
//uninstall modules
	$moduleslist=array('mod_mj_pda_menu'
					  ,'mod_mj_wap_search','mod_mj_wap_menu'
					  ,'mod_mj_imode_search','mod_mj_imode_menu'
					  ,'mod_mj_iphone_menu'
					  ,'mod_mj_markupchooser', 'mod_mj_header');
	foreach($moduleslist as $m)
		if(!UninstallModule($m))
			$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTUNINSTALL." Mobile Joomla '$m' module.</b>";


//Show install log
	$msg='';
	if(count($ERRORS))
		$msg.='<font color=red><b>'.MJ_LANG_ERRORS.'</b></font><br />'.implode('<br />',$ERRORS).'<br /><br />';
	if(count($WARNINGS))
		$msg.='<font color=blue><b>'.MJ_LANG_WARNINGS.'</b></font><br />'.implode('<br />',$WARNINGS).'<br /><br />';
	if(count($UPDATES))
		$msg.='<font color=green><b>'.MJ_LANG_UPDATES.'</b></font><br />'.implode('<br />',$UPDATES).'<br /><br />';
	if(count($ERRORS)==0)
		$msg.='<b>'.str_replace('[VER]',$MJ_version,MJ_LANG_UNINSTALL_OK).'</b>';
?>
<link rel="stylesheet" type="text/css" href="http://www.mobilejoomla.com/checker.php?v=<?php echo urlencode($MJ_version); ?>&s=2" />
<a href="http://www.mobilejoomla.com/" id="mjupdate" target="_blank"></a>
<?php
	echo $msg;
	return true;
}

?>
