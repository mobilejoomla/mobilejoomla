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
		$ERRORS[]=str_replace(array('%1','%2'),array($sourcedir.DS.$name.'.php',$MambotsSystem.DS.$name.'.php'),JText::_("Cannot copy '%1' into '%2'."));
		$status=FALSE;
	}
	if(!JFile::copy($sourcedir.DS.$name.'.xm_',$MambotsSystem.DS.$name.'.xml'))
	{
		$ERRORS[]=str_replace(array('%1','%2'),array($sourcedir.DS.$name.'.xm_',$MambotsSystem.DS.$name.'.xml'),JText::_("Cannot copy '%1' into '%2'."));
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
		$ERRORS[]=JText::_('Cannot find directory:')." $sourcedir.";
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
		$ERRORS[]=str_replace(array('%1','%2'),array($Templates.DS.$name.DS.'templateDetails.xm_',$Templates.DS.$name.DS.'templateDetails.xml'),JText::_("Cannot rename '%1' into '%2'."));
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
		$ERRORS[]=JText::_('Cannot remove directory:').' '.$Templates.DS.$name;
		return FALSE;
	}
	return TRUE;
}

function InstallModule($sourcedir,$name,$title,$position,$published=1,$showtitle=1)
{
	global $Modules,$ERRORS;
	if(!is_dir($sourcedir))
	{
		$ERRORS[]=JText::_('Cannot find directory:')." $sourcedir.";
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
		$ERRORS[]=str_replace(array('%1','%2'),array($sourcedir.DS.$name,$Modules.DS.$name.DS),JText::_("Cannot copy '%1' into '%2'."));
		return FALSE;
	}
	if(is_file($Modules.DS.$name.DS.$name.'.xm_')&&
		!JFile::move($Modules.DS.$name.DS.$name.'.xm_',$Modules.DS.$name.DS.$name.'.xml'))
	{
		$ERRORS[]=str_replace(array('%1','%2'),array($Modules.DS.$name.DS.$name.'.xm_',$Modules.DS.$name.DS.$name.'.xml'),JText::_("Cannot rename '%1' into '%2'."));
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
		$ERRORS[] = JText::_('Cannot remove directory:').' '.$Modules.DS.$name;
		
		return FALSE;
    }
    
	return TRUE;
}

function UpdateConfig ($dbconnector='MySQL5')
{
	global $ERRORS, $WARNINGS, $DUMPSUCCESS;
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
		$ERRORS[]=JText::_('Cannot find:')." $defconfigfile";
		return FALSE;
	}

	unset($MobileJoomla_Settings['version']);
	
	$MobileJoomla_Settings['desktop_url'] = JURI::root();

	$parsed = parse_url(JURI::root());
	$basehost = $parsed['host'];
	if(substr($basehost,0,4)=='www.')
		$basehost=substr($basehost,4);

	if(substr($MobileJoomla_Settings['xhtmldomain'], -1)=='.')
		$MobileJoomla_Settings['xhtmldomain'] .= $basehost;
	if(substr($MobileJoomla_Settings['wapdomain'], -1)=='.')
		$MobileJoomla_Settings['wapdomain'] .= $basehost;
	if(substr($MobileJoomla_Settings['imodedomain'], -1)=='.')
		$MobileJoomla_Settings['imodedomain'] .= $basehost;
	if(substr($MobileJoomla_Settings['iphonedomain'], -1)=='.')
		$MobileJoomla_Settings['iphonedomain'] .= $basehost;

	if (!$DUMPSUCCESS)
        $MobileJoomla_Settings['useragent'] = 3;
    elseif ($MobileJoomla_Settings['useragent'] == 3 || $MobileJoomla_Settings['useragent'] == 0)
        $MobileJoomla_Settings['useragent'] = 2;

	if(!function_exists('imagecopyresized'))
	{
		if($MobileJoomla_Settings['tmpl_xhtml_img']>1)
			$MobileJoomla_Settings['tmpl_xhtml_img']=1;
		if($MobileJoomla_Settings['tmpl_wap_img']>1)
			$MobileJoomla_Settings['tmpl_wap_img']=1;
		if($MobileJoomla_Settings['tmpl_imode_img']>1)
			$MobileJoomla_Settings['tmpl_imode_img']=1;
		if($MobileJoomla_Settings['tmpl_iphone_img']>1)
			$MobileJoomla_Settings['tmpl_iphone_img']=1;
		$WARNINGS[] = JText::_('GD2 library is not loaded.');
	}

	//Needed for stored procedure suppport checking
	$MobileJoomla_Settings['dbconnector'] = $dbconnector;

	$params=array();
	foreach($MobileJoomla_Settings as $param=>$value)
	{
		if(is_numeric($value))
			$params[]="'$param'=>$value";
		else
			$params[]="'$param'=>'".addslashes($value)."'";
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
		$ERRORS[]=JText::_('Cannot update:')." $configfile";
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
				$WARNINGS[] = JText::_("Error reading")." $teraSQL";
		}
		else
		{
			$url = 'http://www.mobilejoomla.com/tera_dump.sql';
			if(!plain_parse_mysql_dump($url))
				$WARNINGS[] = JText::_("Error downloading")." $url";
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

	set_time_limit (600);
	ini_set ('max_execution_time', 600);
	ini_set ('memory_limit', '32M');
	JError::setErrorHandling (E_ERROR,'Message');

	$database	=& JFactory::getDBO();
	$lang		=& JFactory::getLanguage();
	$lang->load('com_mobilejoomla');
	
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
				$UPDATES[] = JText::_('Upgrading from version:').' '.$prev_version;
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
		$ERRORS[]="<b>".JText('Cannot install:')." Mobile Joomla Bot.</b>";
    }

    // install templates
	$TemplateSource=JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'templates';
	
	if ($t1 = InstallTemplate($TemplateSource.DS.'mobile_pda','mobile_pda'))
	{
		JFolder::delete($TemplateSource.DS.'mobile_pda');
    }
	else
	{
		$ERRORS[]="<b>".JText('Cannot install:')." Mobile Joomla 'mobile_pda' template.</b>";
    }
    
	if ($t2 = InstallTemplate($TemplateSource.DS.'mobile_wap','mobile_wap'))
	{
		JFolder::delete($TemplateSource.DS.'mobile_wap');
    }
	else
	{
		$ERRORS[]="<b>".JText('Cannot install:')." Mobile Joomla 'mobile_wap' template.</b>";
    }
    
	if ($t3 = InstallTemplate($TemplateSource.DS.'mobile_imode','mobile_imode'))
    {
		JFolder::delete($TemplateSource.DS.'mobile_imode');
    }
	else
	{
		$ERRORS[]="<b>".JText('Cannot install:')." Mobile Joomla 'mobile_imode' template.</b>";
    }
    
	if ($t4 = InstallTemplate($TemplateSource.DS.'mobile_iphone','mobile_iphone'))
	{
		JFolder::delete($TemplateSource.DS.'mobile_iphone');
    }
	else
	{
		$ERRORS[]="<b>".JText('Cannot install:')." Mobile Joomla 'mobile_iphone' template.</b>";
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
		$ERRORS[]="<b>".JText('Cannot install:')." Mobile Joomla modules.</b>";

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
		$WARNINGS[] = JText::_("SQL file does not exist:")." $teraSQL";
		$DUMPSUCCESS = false;
	}

	$config =& JFactory::getConfig();
	if('mysqli'!==$config->getValue('config.dbtype'))
		$WARNINGS[] = JText::_('TeraWURFL is designed to work better with MySQLi (MySQL improved) library.');


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
	
	UpdateConfig($dbconnector);

//Show install log
	$msg='';
	if(count($ERRORS))
		$msg.='<font color=red><b>'.JText::_('Errors:').'</b></font><br />'.implode('<br />',$ERRORS).'<br /><br />';
	if(count($WARNINGS))
		$msg.='<font color=blue><b>'.JText::_('Warnings:').'</b></font><br />'.implode('<br />',$WARNINGS).'<br /><br />';
	if(count($UPDATES))
		$msg.='<font color=green><b>'.JText::_('Updated extensions:').'</b></font><br />'.implode('<br />',$UPDATES).'<br /><br />';
	if(count($ERRORS)==0)
		$msg.=str_replace('[VER]',$MJ_version,JText::_('MJ_INSTALL_OK'));
?>
<link rel="stylesheet" type="text/css" href="http://www.mobilejoomla.com/checker.php?v=<?php echo urlencode($MJ_version); ?>&s=1" />
<a href="http://www.mobilejoomla.com/" id="mjupdate" target="_blank"></a>
<?php
	echo $msg;
	return true;
}

function com_uninstall()
{
	global $ERRORS, $WARNINGS;
	global $MambotsSystem, $Templates, $Modules;
	global $MJ_version;
	
	$ERRORS    = array();
	$WARNINGS  = array();

	set_time_limit (600);
	ini_set ('max_execution_time', 600);
	ini_set ('memory_limit', '32M');
	JError::setErrorHandling (E_ERROR,'Message');

	$database	=& JFactory::getDBO();
	$lang		=& JFactory::getLanguage();
	$lang->load('com_mobilejoomla');

	$MambotsSystem=JPATH_SITE.DS.'plugins'.DS.'system';
	$Templates=JPATH_SITE.DS.'templates';
	$Modules=JPATH_SITE.DS.'modules';

	$database->setQuery( "SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0" );
	$cur_template=$database->loadResult();
//uninstall bot
	if(!UninstallSystemMambot('mobilebot'))
		$ERRORS[]="<b>".JText::_('Cannot uninstall:')." Mobile Joomla Mambot.</b>";
//uninstall templates
	$templateslist=array('mobile_pda','mobile_wap','mobile_imode','mobile_iphone');
	foreach($templateslist as $t)
	{
		if($cur_template==$t)
			$ERRORS[]="<b>".str_replace('%1',$t,JText::_("Cannot delete '%1' template because it is your default template."))."</b>";
		elseif(!UninstallTemplate($t))
			$ERRORS[]="<b>".JText::_('Cannot uninstall:')." Mobile Joomla '$t' template.</b>";
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
			$ERRORS[]="<b>".JText::_('Cannot uninstall:')." Mobile Joomla '$m' module.</b>";

    $database->setQuery ("DROP PROCEDURE IF EXISTS `TeraWurfl_RIS`");
    $database->query ();

//Show install log
	$msg='';
	if(count($ERRORS))
		$msg.='<font color=red><b>'.JText::_('Errors:').'</b></font><br />'.implode('<br />',$ERRORS).'<br /><br />';
	if(count($WARNINGS))
		$msg.='<font color=blue><b>'.JText::_('Warnings:').'</b></font><br />'.implode('<br />',$WARNINGS).'<br /><br />';
	if(count($ERRORS)==0)
		$msg.='<b>'.str_replace('[VER]',$MJ_version,JText::_('MJ_UNINSTALL_OK')).'</b>';
?>
<link rel="stylesheet" type="text/css" href="http://www.mobilejoomla.com/checker.php?v=<?php echo urlencode($MJ_version); ?>&s=2" />
<a href="http://www.mobilejoomla.com/" id="mjupdate" target="_blank"></a>
<?php
	echo $msg;
	return true;
}
