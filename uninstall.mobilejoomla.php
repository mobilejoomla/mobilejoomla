<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version		###VERSION###
 * @license		###LICENSE###
 * @copyright	###COPYRIGHT###
 * @date        ###DATE###
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport ('joomla.filesystem.file');
jimport ('joomla.filesystem.folder');

global $MJ_version;
$MJ_version = '###VERSION###';

function InstallSystemMambot($sourcedir,$name,$fullname,$publish=1,$ordering=-1000)
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
		$database->setQuery("INSERT INTO `#__plugins` (`name`, `element`, `folder`, `published`, `ordering`) VALUES ('$fullname', '$name', 'system', $publish, $ordering);");
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
	$ids = $database->loadResultArray();
	
	foreach ($ids as $id)
	{
		$database->setQuery( "DELETE FROM #__modules_menu WHERE moduleid = $id" );
		$database->query();
	}
	
	$database->setQuery( "DELETE FROM #__modules WHERE module = '$name' AND client_id = 0" );
	$database->query();
	
	if (!JFolder::delete($Modules.DS.$name))
	{
		$ERRORS[] = MJ_LANG_ERROR_CANNOTREMOVEDIR.' '.$Modules.DS.$name;
		
		return FALSE;
    }
    
	return TRUE;
}

function UpdateConfig ($botparams = null, $dbconnector='MySQL5')
{
	global $ERRORS, $DUMPSUCCESS;
	global $MJ_version;
	
	$configfile    = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php';
	$defconfigfile = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'defconfig.php';
	
	if (is_file($configfile))
	{
		include($configfile);
    }
	elseif (is_file($defconfigfile))
	{
		include($defconfigfile);
    }
	else
	{
		$ERRORS[]=MJ_LANG_ERROR_CANNOTFIND." $defconfigfile";
		return FALSE;
	}

	$settings = array(
        'useragent',
        'domains',
        'pcpage',
        'templatewidth',
        'jpegquality',
		'xhtmltemplate',
        'xhtmlhomepage',
        'xhtmlgzip',
        'xhtmldomain',
        'xhtmlredirect',
		'waptemplate',
        'waphomepage',
        'wapgzip',
        'wapdomain',
        'wapredirect',
		'imodetemplate',
        'imodehomepage',
        'imodegzip',
        'imodedomain',
        'imoderedirect',
		'iphonetemplate',
        'iphonehomepage',
        'iphonegzip',
        'iphonedomain',
        'iphoneredirect',
		'tmpl_xhtml_header1',
        'tmpl_xhtml_header2',
        'tmpl_xhtml_pathway',
        'tmpl_xhtml_pathwayhome',
		'tmpl_xhtml_middle1',
        'tmpl_xhtml_middle2',
        'tmpl_xhtml_componenthome',
        'tmpl_xhtml_footer1',
		'tmpl_xhtml_footer2',
        'tmpl_xhtml_jfooter',
        'tmpl_xhtml_simplehead',
        'tmpl_xhtml_allowextedit',
		'tmpl_xhtml_removetags',
        'tmpl_xhtml_removescripts',
        'tmpl_xhtml_img',
        'tmpl_xhtml_entitydecode',
        'tmpl_xhtml_embedcss',
		'tmpl_xhtml_contenttype',
        'tmpl_xhtml_xmlhead',
        'tmpl_xhtml_doctype',
        'tmpl_xhtml_xmlns',
		'tmpl_wap_header',
        'tmpl_wap_pathway',
        'tmpl_wap_pathwayhome',
        'tmpl_wap_middle',
		'tmpl_wap_componenthome',
        'tmpl_wap_footer',
        'tmpl_wap_cards',
        'tmpl_wap_jfooter',
		'tmpl_wap_removetags',
        'tmpl_wap_img',
        'tmpl_wap_entitydecode',
		'tmpl_wap_doctype',
		'tmpl_imode_header1',
        'tmpl_imode_header2',
        'tmpl_imode_pathway',
        'tmpl_imode_pathwayhome',
		'tmpl_imode_middle1',
        'tmpl_imode_middle2',
        'tmpl_imode_componenthome',
        'tmpl_imode_footer1',
		'tmpl_imode_footer2',
        'tmpl_imode_jfooter',
        'tmpl_imode_removetags',
        'tmpl_imode_img',
		'tmpl_imode_entitydecode',
        'tmpl_imode_doctype',
		'tmpl_iphone_header1',
        'tmpl_iphone_header2',
        'tmpl_iphone_pathway',
        'tmpl_iphone_pathwayhome',
		'tmpl_iphone_middle1',
        'tmpl_iphone_middle2',
        'tmpl_iphone_componenthome',
        'tmpl_iphone_footer1',
		'tmpl_iphone_footer2',
        'tmpl_iphone_jfooter',
        'tmpl_iphone_img',
		'xhtml_buffer_width',
        'wml_buffer_width',
        'iphone_buffer_width',
        'chtml_buffer_width',
        'tmpl_iphone_removetags',
		'dbconnector',
		'desktop_url'
	);
	
	$params=array();
	
	$MobileJoomla_Settings['desktop_url'] = JURI::root ();
	
	if (!$DUMPSUCCESS)
        $MobileJoomla_Settings['useragent'] = 3;
    elseif ($MobileJoomla_Settings['useragent'] == 3 || $MobileJoomla_Settings['useragent'] == 0)
        $MobileJoomla_Settings['useragent'] = 2;

	//Needed for stored procedure suppport checking
	$MobileJoomla_Settings['dbconnector'] = $dbconnector;

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
	{
		JFile::delete($defconfigfile);
    }
    
	return TRUE;
}

function install_procedure ()
{
        $db =& JFactory::getDBO ();

		$db->setQuery ("DROP PROCEDURE IF EXISTS `TeraWurfl_RIS`");
        $db->query ();
        
		$TeraWurfl_RIS = "CREATE PROCEDURE `TeraWurfl_RIS`(IN ua VARCHAR(255), IN tolerance INT, IN matcher VARCHAR(128))
BEGIN
DECLARE curlen INT;
DECLARE wurflid VARCHAR(128) DEFAULT NULL;
DECLARE curua VARCHAR(255);

SELECT CHAR_LENGTH(ua)  INTO curlen;
findua: WHILE ( curlen >= tolerance ) DO
	SELECT CONCAT(LEFT(ua, curlen ),'%') INTO curua;
	SELECT idx.DeviceID INTO wurflid
		FROM #__terawurflindex idx INNER JOIN #__terawurflmerge mrg ON idx.DeviceID = mrg.DeviceID
		WHERE idx.matcher = matcher
		AND mrg.user_agent LIKE curua
		LIMIT 1;
	IF wurflid IS NOT NULL THEN
		LEAVE findua;
	END IF;
	SELECT curlen - 1 INTO curlen;
END WHILE;

SELECT wurflid as DeviceID;
END";
        
        $db->setQuery ($TeraWurfl_RIS);
        $isSuccessful = $db->query ();
		return $isSuccessful;
	}

function plain_parse_mysql_dump ($url)
{
    $db =& JFactory::getDBO ();
    
    $handle = fopen ($url, 'r');
	if($handle===false)
		return FALSE;

    $query = '';

    while (!feof ($handle))
    {
        $sql_line = fgets ($handle);

        if (trim ($sql_line) != '' && strpos ($sql_line, '--') === false)
        {
            $query .= $sql_line;

            if (preg_match ("/;[\040]*\$/", $sql_line))
            {
                $db->setQuery ($query);
                $db->query ();

                $query = '';
            }
        }
    }
	return TRUE;
}

function parse_mysql_dump ($file)
{
	global $WARNINGS;
	if(!extension_loaded('bz2'))
	{
		if(JPATH_ISWIN)
			@dl('php_bz2.dll');
		else
			@dl('bz2.so');
	}
	if (function_exists ('bzopen') && JFile::exists ($file))
	{
		bz2_parse_mysql_dump ($file);
	}
	else
	{
		$teraPath = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'terawurfl'.DS; 
		$teraSQL = $teraPath . 'tera_dump.sql';
		
		if (JFile::exists ($file))
		{
			$pwd = getcwd ();
			chdir ($teraPath);
			exec('bunzip2 '.escapeshellarg($file).' 2>&1');
			chdir ($pwd);
		}
		
		if (JFile::exists ($teraSQL))
		{
			if(!plain_parse_mysql_dump($teraSQL))
				$WARNINGS[] = "Error reading $teraSQL";
		}
		else
		{
			$url = 'http://www.mobilejoomla.com/tera_dump.sql';
			if(!plain_parse_mysql_dump($url))
				$WARNINGS[] = "Error downloading $url";
		}
	}
}

function bz2_parse_mysql_dump ($url)
{
    $db =& JFactory::getDBO ();

    $handle = bzopen ($url, 'r');
    $sql_line = '';
    $i = 0;
    while (!feof ($handle))
    {
        $buf = bzread ($handle);
        
		if (trim ($buf) != '')
		{
			$sql_line .= $buf;
		
			if (($pos = strrpos ($sql_line, ";\n")) !== FALSE)
			{
				$queries = substr ($sql_line, 0, $pos + 1);
				$queries = explode (";\n", $queries);
			
				foreach ($queries as $query)
				{
					if (trim ($query) != '')
					{
						$db->setQuery ($query);
						$db->query ();
					}
				}

				$sql_line = substr ($sql_line, $pos + 2);
			}
		}
    }
}

function com_install()
{
//check joomla version
	if(!defined('_JEXEC'))
	{
		 echo '<font color=red><b><u>This component is released for Joomla!1.5.x</u></b></font><br />Installation stopped.';
		 return true;
	}

	global $ERRORS, $WARNINGS, $UPDATES, $DUMPSUCCESS;
	global $MambotsSystem, $Templates, $Modules, $upgrade;
	global $MJ_version;
	
	$ERRORS    = array();
	$WARNINGS  = array();
	$UPDATES   = array();
	$upgrade   = false;

	$database          =& JFactory::getDBO();
	$lang              =& JFactory::getLanguage();
	$mosConfig_lang    = $lang->getBackwardLang();
	
	set_time_limit (600);
	ini_set ('max_execution_time', 600);
	ini_set ('memory_limit', '32M');
	JError::setErrorHandling (E_ERROR,'Message');


	$languagepath = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'languages'.DS;
	
	if (is_file($languagepath.$mosConfig_lang.'.php'))
	{
		include($languagepath.$mosConfig_lang.'.php');
    }
	elseif (is_file($languagepath.'english.php'))
	{
		include($languagepath.'english.php');
    }
	else
	{
        $ERRORS[] = "<b>Installation error:</b> language file '${languagepath}english.php' is not found.";
    }

	// check for upgrade
	$prev_version = '';
	$manifest = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.xml';
	if(is_file($manifest))
	{
		if (!class_exists ('DOMIT_Lite_Document'))
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
	
	$extFile = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'extensions'.DS.'extensions.json';
	$extDistFile = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'extensions'.DS.'extensions.json.dist';
	
	if (!JFile::exists ($extFile))
	{
        JFile::move($extDistFile, $extFile);
    }
    else
    {
        JFile::delete($extDistFile);
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
//	$database->setQuery( "UPDATE #__components"
//					. "\n SET admin_menu_img='../administrator/components/com_mobilejoomla/images/wurfl16x16.gif' "
//					. "\n WHERE admin_menu_link='option=com_mobilejoomla&task=wurfl'" );
//	$database->query();
	$database->setQuery( "UPDATE #__components"
					. "\n SET admin_menu_img='js/ThemeOffice/info.png'"
					. "\n WHERE admin_menu_link='option=com_mobilejoomla&task=about'" );
	$database->query();

	$MambotsSystem=JPATH_SITE.DS.'plugins'.DS.'system';
	$Templates=JPATH_SITE.DS.'templates';
	$Modules=JPATH_SITE.DS.'modules';

	$database->setQuery( "SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0" );
	$cur_template=$database->loadResult();

    //install bot
	$MambotSource=JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'plugin';
	
	if(InstallSystemMambot($MambotSource,'mobilebot','Mobile Joomla Bot'))
	{
		JFolder::delete($MambotSource);
    }
	else
	{
		$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTINSTALL." Mobile Joomla Bot.</b>";
    }

    // install templates
	$TemplateSource=JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'templates';
	
	if ($t1 = InstallTemplate($TemplateSource.DS.'mobile_pda','mobile_pda'))
	{
		JFolder::delete($TemplateSource.DS.'mobile_pda');
    }
	else
	{
		$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTINSTALL." Mobile Joomla 'mobile_pda' template.</b>";
    }
    
	if ($t2 = InstallTemplate($TemplateSource.DS.'mobile_wap','mobile_wap'))
	{
		JFolder::delete($TemplateSource.DS.'mobile_wap');
    }
	else
	{
		$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTINSTALL." Mobile Joomla 'mobile_wap' template.</b>";
    }
    
	if ($t3 = InstallTemplate($TemplateSource.DS.'mobile_imode','mobile_imode'))
    {
		JFolder::delete($TemplateSource.DS.'mobile_imode');
    }
	else
	{
		$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTINSTALL." Mobile Joomla 'mobile_imode' template.</b>";
    }
    
	if ($t4 = InstallTemplate($TemplateSource.DS.'mobile_iphone','mobile_iphone'))
	{
		JFolder::delete($TemplateSource.DS.'mobile_iphone');
    }
	else
	{
		$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTINSTALL." Mobile Joomla 'mobile_iphone' template.</b>";
    }
    
	if ($t1 && $t2 && $t3 && $t4)
		JFolder::delete($TemplateSource);

    //install modules (over existing)
	$ModuleSource  = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'modules';
	$status        = TRUE;
	
	$status &= InstallModule($ModuleSource,'mod_mj_header',       'Header Module','mj_pda_header', 1, 0);
	$status &= InstallModule($ModuleSource,'mod_mj_pda_menu',     'Main Menu',    'mj_pda_header2', 1, 0);
	$status &= InstallModule($ModuleSource,'mod_mj_wap_menu',     'Main Menu',    'mj_wap_footer');
	$status &= InstallModule($ModuleSource,'mod_mj_imode_menu',   'Main Menu',    'mj_imode_footer');
	$status &= InstallModule($ModuleSource,'mod_mj_iphone_menu',  'Main Menu',    'mj_iphone_middle', 1, 0);
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
	
	$teraSQL = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'terawurfl'.DS.'tera_dump.sql.bz2';
    
    $DUMPSUCCESS = true;
    
	if (JFile::exists ($teraSQL))
	{
		parse_mysql_dump ($teraSQL);
	}
	else
	{
		$WARNINGS[] = "SQL file {$teraSQL} does not exist";
		$DUMPSUCCESS = false;
	}
    
    if ( ! version_compare($database->getVersion(), '5.0.0', '<'))
	{	
		$dbconnector = 'MySQL5'; //this might be overriden below

        $procedureInstalled = install_procedure();
		//it might still fail (5.1.2)
		if ($procedureInstalled === FALSE)
		{
			$dbconnector = 'MySQL4';
		}
	}
	else
	{
		$dbconnector = 'MySQL4';
	}
	
	UpdateConfig($botParams = NULL, $dbconnector);

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
	global $ERRORS, $WARNINGS, $UPDATES;
	global $MambotsSystem, $Templates, $Modules;
	global $MJ_version;
	
	$ERRORS    = array();
	$WARNINGS  = array();
	$UPDATES   = array();

	$database          =& JFactory::getDBO();
	$lang              =& JFactory::getLanguage();
	$mosConfig_lang    = $lang->getBackwardLang();

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
		$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTUNINSTALL." Mobile Joomla Mambot.</b>";
//uninstall templates
	$templateslist=array('mobile_pda','mobile_wap','mobile_imode','mobile_iphone');
	foreach($templateslist as $t)
	{
		if($cur_template==$t)
			$ERRORS[]="<b>".str_replace('%1',$t,MJ_LANG_ERROR_CANNOTDELTEMPLATE)."</b>";
		elseif(!UninstallTemplate($t))
			$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTUNINSTALL." Mobile Joomla '$t' template.</b>";
	}

	$query = "DROP TABLE IF EXISTS `#__capability`;";
	$database->setQuery($query);
	$database->query();
	
	$tables = array (
        '#__terawurflcache',
		'#__terawurflcache_temp',
        '#__terawurflindex',
        '#__terawurflmerge',
        '#__terawurfl_alcatel',
        '#__terawurfl_android',
        '#__terawurfl_aol',
        '#__terawurfl_apple',
        '#__terawurfl_benq',
        '#__terawurfl_blackberry',
        '#__terawurfl_bot',
        '#__terawurfl_catchall',
        '#__terawurfl_chrome',
        '#__terawurfl_docomo',
        '#__terawurfl_firefox',
        '#__terawurfl_grundig',
        '#__terawurfl_htc',
        '#__terawurfl_kddi',
        '#__terawurfl_konqueror',
        '#__terawurfl_kyocera',
        '#__terawurfl_lg',
        '#__terawurfl_mitsubishi',
        '#__terawurfl_motorola',
        '#__terawurfl_msie',
        '#__terawurfl_nec',
        '#__terawurfl_nintendo',
        '#__terawurfl_nokia',
        '#__terawurfl_opera',
        '#__terawurfl_operamini',
        '#__terawurfl_panasonic',
        '#__terawurfl_pantech',
        '#__terawurfl_philips',
        '#__terawurfl_portalmmm',
        '#__terawurfl_qtek',
        '#__terawurfl_safari',
        '#__terawurfl_sagem',
        '#__terawurfl_samsung',
        '#__terawurfl_sanyo',
        '#__terawurfl_sharp',
        '#__terawurfl_siemens',
        '#__terawurfl_sonyericsson',
        '#__terawurfl_spv',
        '#__terawurfl_toshiba',
        '#__terawurfl_vodafone',
        '#__terawurfl_windowsce'
    );
    
    foreach ($tables as $table)
    {
        $query = "DROP TABLE IF EXISTS `{$table}`;";
        $database->setQuery($query);
        $database->query();
    }
    
	
//uninstall modules
	$moduleslist = array (
        'mod_mj_pda_menu',
		'mod_mj_wap_menu',
		'mod_mj_imode_menu',
		'mod_mj_iphone_menu',
		'mod_mj_markupchooser',
        'mod_mj_header'
    );
    
	foreach($moduleslist as $m)
		if(!UninstallModule($m))
			$ERRORS[]="<b>".MJ_LANG_ERROR_CANNOTUNINSTALL." Mobile Joomla '$m' module.</b>";

    $database->setQuery ("DROP PROCEDURE IF EXISTS `TeraWurfl_RIS`");
    $database->query ();

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
