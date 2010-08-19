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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

global $MJ_version;
$MJ_version = '###VERSION###';

function InstallPlugin($group, $sourcedir, $name, $fullname, $publish = 1, $ordering = -99)
{
	global $ERRORS;
	$PluginDir = JPATH_PLUGINS.DS.$group;
	$upgrade = false;
	if(is_file($PluginDir.DS.$name.'.php'))
	{
		$upgrade = true;
		JFile::delete($PluginDir.DS.$name.'.php');
		JFile::delete($PluginDir.DS.$name.'.xml');
	}
	$status = true;
	if(!JFile::copy($sourcedir.DS.$name.'.php', $PluginDir.DS.$name.'.php'))
	{
		$ERRORS[] = str_replace(array ('%1', '%2'),
		                        array ($sourcedir.DS.$name.'.php', $PluginDir.DS.$name.'.php'),
		                        JText::_("Cannot copy '%1' into '%2'."));
		$status = false;
	}
	if(!JFile::copy($sourcedir.DS.$name.'.xm_', $PluginDir.DS.$name.'.xml'))
	{
		$ERRORS[] = str_replace(array ('%1', '%2'),
		                        array ($sourcedir.DS.$name.'.xm_', $PluginDir.DS.$name.'.xml'),
		                        JText::_("Cannot copy '%1' into '%2'."));
		$status = false;
	}
	if(!$upgrade)
	{
		/** @var JDatabase $db */
		$db =& JFactory::getDBO();
		$db->setQuery("INSERT INTO `#__plugins` (`name`, `element`, `folder`, `published`, `ordering`) VALUES ('$fullname', '$name', '$group', $publish, $ordering)");
		$db->query();
	}
	return $status;
}

function UninstallPlugin($group, $name)
{
	$PluginDir = JPATH_PLUGINS.DS.$group;
	/** @var JDatabase $db */
	$db =& JFactory::getDBO();
	$db->setQuery('DELETE FROM #__plugins WHERE `element` = '.$db->Quote($name));
	$db->query();
	$status = true;
	$status = JFile::delete($PluginDir.DS.$name.'.php') && $status;
	$status = JFile::delete($PluginDir.DS.$name.'.xml') && $status;
	return $status;
}

function InstallTemplate($sourcedir, $name)
{
	global $ERRORS;
	$TemplateDir = JPATH_ROOT.DS.'templates'.DS.$name;
	if(!is_dir($sourcedir))
	{
		$ERRORS[] = JText::_('Cannot find directory:')." $sourcedir.";
		return false;
	}
	if(is_dir($TemplateDir))
	{
		if(!JFolder::delete($TemplateDir))
			$ERRORS[] = JText::_('Cannot remove directory:').' '.$TemplateDir;
	}
	$status = JFolder::copy($sourcedir, $TemplateDir, '', true);
	if(is_file($TemplateDir.DS.'templateDetails.xm_') &&
			!JFile::move($TemplateDir.DS.'templateDetails.xm_', $TemplateDir.DS.'templateDetails.xml'))
	{
		$ERRORS[] = str_replace(array ('%1', '%2'),
		                        array ($TemplateDir.DS.'templateDetails.xm_', $TemplateDir.DS.'templateDetails.xml'),
		                        JText::_("Cannot rename '%1' into '%2'."));
		$status = false;
	}
	else
	{
		/** @var JDatabase $db */
		$db =& JFactory::getDBO();
		$query = 'SELECT COUNT(*) FROM #__templates_menu WHERE template = '.$db->Quote($name);
		$db->setQuery($query);
		if($db->loadResult()==0)
		{
			$query = 'INSERT INTO #__templates_menu (template, menuid) VALUES ('.$db->Quote($name).', -1)';
			$db->setQuery($query);
			$db->query();
		}
	}
	return $status;
}

function UninstallTemplate($name)
{
	global $ERRORS;
	$TemplateDir = JPATH_ROOT.DS.'templates'.DS.$name;
	/** @var JDatabase $db */
	$db =& JFactory::getDBO();
	$db->setQuery('DELETE FROM #__templates_menu WHERE client_id = 0 AND template = '.$db->Quote($name));
	$db->query();
	if(!JFolder::delete($TemplateDir))
	{
		$ERRORS[] = JText::_('Cannot remove directory:').' '.$TemplateDir;
		return false;
	}
	return true;
}

function InstallModule($sourcedir, $name, $title, $position, $published = 1, $showtitle = 1)
{
	global $ERRORS;
	$ModuleDir = JPATH_ROOT.DS.'modules'.DS.$name;
	if(!is_dir($sourcedir))
	{
		$ERRORS[] = JText::_('Cannot find directory:')." $sourcedir.";
		return false;
	}
	$upgrade = false;
	if(is_file($ModuleDir.DS.$name.'.php'))
	{
		$upgrade = true;
		if(!JFolder::delete($ModuleDir))
			JText::_('Cannot remove directory:').' '.$ModuleDir;
	}
	if(!$upgrade)
	{
		/** @var JDatabase $db */
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT id FROM #__modules WHERE module = '$name' AND client_id = 0");
		$ids = $db->loadResultArray();
		if(count($ids)>0)
		{
			$db->setQuery('DELETE FROM #__modules_menu WHERE moduleid IN ('.implode(', ',$ids).')');
			$db->query();
			$db->setQuery("DELETE FROM #__modules WHERE module = '$name' AND client_id = 0");
			$db->query();
		}
	}
	if(!JFolder::copy($sourcedir.DS.$name, $ModuleDir, '', true))
	{
		$ERRORS[] = str_replace(array ('%1', '%2'),
		                        array ($sourcedir.DS.$name, $ModuleDir.DS),
		                        JText::_("Cannot copy '%1' into '%2'."));
		return false;
	}
	if(is_file($ModuleDir.DS.$name.'.xm_') &&
			!JFile::move($ModuleDir.DS.$name.'.xm_', $ModuleDir.DS.$name.'.xml'))
	{
		$ERRORS[] = str_replace(array ('%1', '%2'),
		                        array ($ModuleDir.DS.$name.'.xm_', $ModuleDir.DS.$name.'.xml'),
		                        JText::_("Cannot rename '%1' into '%2'."));
		return false;
	}
	if(!$upgrade)
	{
		if(!is_array($position))
			$position = array ($position);
		foreach($position as $pos)
		{
			$db->setQuery("INSERT INTO `#__modules` (`title`, `content`, `ordering`, `position`, `published`, `module`, `showtitle`, `params`) VALUES ('$title', '', 1, '$pos', $published, '$name', '$showtitle', '')");
			$db->query();
			$id = (int) $db->insertid();
			$db->setQuery("INSERT INTO `#__modules_menu` VALUES ( $id, 0 )");
			$db->query();
		}
	}
	return true;
}

function UninstallModule($name)
{
	$ModuleDir = JPATH_ROOT.DS.'modules'.DS.$name;

	/** @var JDatabase $db */
	$db =& JFactory::getDBO();
	$db->setQuery("SELECT id FROM #__modules WHERE module = '$name' AND client_id = 0");
	$ids = $db->loadResultArray();
	if(count($ids)>0)
	{
		$db->setQuery('DELETE FROM #__modules_menu WHERE moduleid IN ('.implode(', ',$ids).')');
		$db->query();
		$db->setQuery("DELETE FROM #__modules WHERE module = '$name' AND client_id = 0");
		$db->query();
	}

	if(!JFolder::delete($ModuleDir))
	{
		$ERRORS[] = JText::_('Cannot remove directory:').' '.$ModuleDir;
		return false;
	}

	return true;
}

function UpdateConfig()
{
	global $ERRORS, $WARNINGS;
	global $MJ_version;

	$configfile = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php';
	$defconfigfile = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'defconfig.php';

	if(is_file($configfile))
	{
		include($configfile);
	}
	elseif(is_file($defconfigfile))
	{
		include($defconfigfile);
	}
	else
	{
		$ERRORS[] = JText::_('Cannot find:')." $defconfigfile";
		return false;
	}

	unset($MobileJoomla_Settings['version']);
	if(isset($MobileJoomla_Settings['useragent']))
		unset($MobileJoomla_Settings['useragent']);
	if(!isset($MobileJoomla_Settings['caching']))
		$MobileJoomla_Settings['caching'] = 0;

	$MobileJoomla_Settings['desktop_url'] = JURI::root();

	$parsed = parse_url(JURI::root());
	$basehost = $parsed['host'];
	if(substr($basehost, 0, 4) == 'www.')
		$basehost = substr($basehost, 4);

	if(substr($MobileJoomla_Settings['xhtmldomain'], -1) == '.')
		$MobileJoomla_Settings['xhtmldomain'] .= $basehost;
	if(substr($MobileJoomla_Settings['wapdomain'], -1) == '.')
		$MobileJoomla_Settings['wapdomain'] .= $basehost;
	if(substr($MobileJoomla_Settings['imodedomain'], -1) == '.')
		$MobileJoomla_Settings['imodedomain'] .= $basehost;
	if(substr($MobileJoomla_Settings['iphonedomain'], -1) == '.')
		$MobileJoomla_Settings['iphonedomain'] .= $basehost;

	if(!function_exists('imagecopyresized'))
	{
		$WARNINGS[] = JText::_('GD2 library is not loaded.');
		if($MobileJoomla_Settings['tmpl_xhtml_img'] > 1)
			$MobileJoomla_Settings['tmpl_xhtml_img'] = 1;
		if($MobileJoomla_Settings['tmpl_wap_img'] > 1)
			$MobileJoomla_Settings['tmpl_wap_img'] = 1;
		if($MobileJoomla_Settings['tmpl_imode_img'] > 1)
			$MobileJoomla_Settings['tmpl_imode_img'] = 1;
		if($MobileJoomla_Settings['tmpl_iphone_img'] > 1)
			$MobileJoomla_Settings['tmpl_iphone_img'] = 1;
	}

	$params = array ();
	foreach($MobileJoomla_Settings as $param => $value)
	{
		if(is_numeric($value))
			$params[] = "'$param'=>$value";
		else
			$params[] = "'$param'=>'".addslashes($value)."'";
	}

	$config = "<?php\n"
			. "defined( '_JEXEC' ) or die( 'Restricted access' );\n"
			. "\n"
			. "\$MobileJoomla_Settings=array(\n"
			. "'version'=>'$MJ_version',\n"
			. implode(",\n", $params)."\n"
			. ");\n"
			. "?>";

	if(!JFile::write($configfile, $config))
	{
		$ERRORS[] = JText::_('Cannot update:')." $configfile";
		return false;
	}
	else
	{
		JFile::delete($defconfigfile);
	}

	return true;
}

function terawurfl_install_procedure()
{
	/** @var JDatabase $db */
	$db =& JFactory::getDBO();

	if(version_compare($db->getVersion(), '5.0.0', '<'))
		return false;

	$TeraWurfl_RIS = "CREATE PROCEDURE `TeraWurfl_RIS`(IN ua VARCHAR(255), IN tolerance INT, IN matcher VARCHAR(64))
BEGIN
DECLARE curlen INT;
DECLARE wurflid VARCHAR(64) DEFAULT NULL;
DECLARE curua VARCHAR(255);

SELECT CHAR_LENGTH(ua)  INTO curlen;
findua: WHILE ( curlen >= tolerance ) DO
	SELECT CONCAT(LEFT(ua, curlen ),'%') INTO curua;
	SELECT idx.DeviceID INTO wurflid
		FROM `#__TeraWurflIndex` idx INNER JOIN `#__TeraWurflMerge` mrg ON idx.DeviceID = mrg.DeviceID
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
	$db->setQuery($TeraWurfl_RIS);
	$isSuccessful = $db->query();

	$TeraWurfl_FallBackDevices = "CREATE PROCEDURE `TeraWurfl_FallBackDevices`(current_fall_back VARCHAR(64))
BEGIN
WHILE current_fall_back != 'root' DO
	SELECT capabilities FROM `#__TeraWurflMerge` WHERE deviceID = current_fall_back;
	SELECT fall_back FROM `#__TeraWurflMerge` WHERE deviceID = current_fall_back INTO current_fall_back;
END WHILE;
END";
	$db->setQuery($TeraWurfl_FallBackDevices);
	$isSuccessful = $db->query() && $isSuccessful;

	return $isSuccessful;
}

function terawurfl_test()
{
	global $WARNINGS;
	$test = true;

	if(version_compare(phpversion(), '5.0.0', '<'))
	{
		$WARNINGS[] = JText::_('TeraWURFL is designed to work with PHP5 only.');
		$test = false;
	}

	if(!class_exists('mysqli') || !function_exists('mysqli_connect'))
	{
		$WARNINGS[] = JText::_('TeraWURFL is designed to work with MySQLi (MySQL improved) library.');
		$test = false;
	}

	if(!$test)
		return false;
	
	/** @var JRegistry $conf */
	$config =& JFactory::getConfig();
	$host = $config->getValue('host');
	$port = NULL;
	$socket = NULL;
	if(strpos($host, ':')!==false)
	{
		list($host, $port) = explode(':', $host);
		if(!is_numeric($port))
		{
			$socket = $port;
			$port = NULL;
		}
	}
	if($host == '')
		$host = 'localhost';
	$user = $config->getValue('user');
	$pass = $config->getValue('password');
	$dbname = $config->getValue('db');

	$mysqli = new mysqli($host, $user, $pass, $dbname, $port, $socket);
	if(mysqli_connect_error())
	{
		$WARNINGS[] = JText::sprintf('Failed to connect to your MySQL database server using MySQLi library. MySQLi reports the following message (#%d): %s.', mysqli_connect_errno(), mysqli_connect_error());
		return false;
	}
	$mysqli->close();

	return true;
}

function parse_mysql_dump($file)
{
	global $WARNINGS;
	if(!extension_loaded('bz2'))
	{
		if(JPATH_ISWIN)
			@dl('php_bz2.dll');
		else
			@dl('bz2.so');
	}
	if(function_exists('bzopen') && JFile::exists($file))
	{
		bz2_parse_mysql_dump($file);
	}
	else
	{
		$teraPath = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'plugins'.DS.'terawurfl'.DS;
		$teraSQL = $teraPath.'tera_dump.sql';
		$teraSQL_root = JPATH_SITE.DS.'tera_dump.sql';

		if((ini_get('safe_mode')==0) && JFile::exists($file))
		{
			$pwd = getcwd();
			chdir($teraPath);
			exec('bunzip2 -k '.escapeshellarg($file).' 2>&1');
			chdir($pwd);
		}

		if(!JFile::exists($teraSQL) && JFile::exists($teraSQL_root))
			$teraSQL = $teraSQL_root;

		if(JFile::exists($teraSQL))
		{
			if(!plain_parse_mysql_dump($teraSQL))
				$WARNINGS[] = JText::_("Error reading")." $teraSQL";
			if($teraSQL != $teraSQL_root)
				JFile::delete($teraSQL);
		}
		else
		{
			$url = 'http://www.mobilejoomla.com/tera_dump_097.sql';
			if(!plain_parse_mysql_dump($url))
				$WARNINGS[] = JText::_("Error downloading")." $url";
		}
	}
}

function bz2_parse_mysql_dump($url)
{
	/** @var JRegistry $conf */
	$conf =& JFactory::getConfig();
	$debuglevel = $conf->getValue('config.debug');

	/** @var JDatabase $db */
	$db =& JFactory::getDBO();

	$db->debug(0);

	$handle = bzopen($url, 'r');
	$sql_line = '';
	$lastchar = '';
	$counter = 0;
	while(!feof($handle))
	{
		$buf = bzread($handle, 8192);
		if(trim($buf) != '')
		{
			$sql_line .= $buf;
			if(strpos($lastchar.$buf, ";\n") !== false)
			{
				$queries = explode(";\n", $sql_line);
				$sql_line = array_pop($queries);
				foreach($queries as $query) if(trim($query) != '')
				{
					$db->setQuery($query);
					$db->query();
					$counter++;
				}
			}
			$lastchar = $buf[strlen($buf)-1];
		}
	}
	bzclose($handle);
	$db->debug($debuglevel);
	if($debuglevel)
	{
		$db->setQuery("# Insert $counter terawurfl queries");
		$db->query();
	}
}

function plain_parse_mysql_dump($url)
{
	$handle = fopen($url, 'r');
	if($handle===false)
		return false;

	/** @var JRegistry $conf */
	$conf =& JFactory::getConfig();
	$debuglevel = $conf->getValue('config.debug');

	/** @var JDatabase $db */
	$db =& JFactory::getDBO();

	$db->debug(0);

	$sql_line = '';
	$lastchar = '';
	$counter = 0;
	while(!feof($handle))
	{
		$buf = fread($handle, 32768);
		if(trim($buf) != '')
		{
			$sql_line .= $buf;
			if(strpos($lastchar.$buf, ";\n") !== false)
			{
				$queries = explode(";\n", $sql_line);
				$sql_line = array_pop($queries);
				foreach($queries as $query) if(trim($query) != '')
				{
					$db->setQuery($query);
					$db->query();
					$counter++;
				}
			}
			$lastchar = $buf[strlen($buf)-1];
		}
	}
	fclose($handle);
	$db->debug($debuglevel);
	if($debuglevel)
	{
		$db->setQuery("# Insert $counter terawurfl queries");
		$db->query();
	}
	return true;
}

function clear_terawurfl_db()
{
	/** @var JDatabase $db */
	$db =& JFactory::getDBO();
	$tables = array ('#__TeraWurflCache', '#__TeraWurflCache_TEMP', '#__TeraWurflIndex', '#__TeraWurflMerge',
	                 '#__TeraWurfl_AOL', '#__TeraWurfl_Alcatel', '#__TeraWurfl_Android', '#__TeraWurfl_Apple',
	                 '#__TeraWurfl_BenQ', '#__TeraWurfl_BlackBerry', '#__TeraWurfl_Bot', '#__TeraWurfl_CatchAll',
	                 '#__TeraWurfl_Chrome', '#__TeraWurfl_DoCoMo', '#__TeraWurfl_Firefox', '#__TeraWurfl_Grundig',
	                 '#__TeraWurfl_HTC', '#__TeraWurfl_Kddi', '#__TeraWurfl_Konqueror', '#__TeraWurfl_Kyocera',
	                 '#__TeraWurfl_LG', '#__TeraWurfl_MSIE', '#__TeraWurfl_Mitsubishi', '#__TeraWurfl_Motorola',
	                 '#__TeraWurfl_Nec', '#__TeraWurfl_Nintendo', '#__TeraWurfl_Nokia', '#__TeraWurfl_Opera',
	                 '#__TeraWurfl_OperaMini', '#__TeraWurfl_Panasonic', '#__TeraWurfl_Pantech', '#__TeraWurfl_Philips',
	                 '#__TeraWurfl_Portalmmm', '#__TeraWurfl_Qtek', '#__TeraWurfl_SPV', '#__TeraWurfl_Safari',
	                 '#__TeraWurfl_Sagem', '#__TeraWurfl_Samsung', '#__TeraWurfl_Sanyo', '#__TeraWurfl_Sharp',
	                 '#__TeraWurfl_Siemens', '#__TeraWurfl_SonyEricsson', '#__TeraWurfl_Toshiba', '#__TeraWurfl_Vodafone',
	                 '#__TeraWurfl_WindowsCE');
	$query = 'DROP TABLE IF EXISTS `'.implode('`, `',$tables).'`';
	$db->setQuery($query);
	$db->query();
	if(version_compare($db->getVersion(), '5.0.0', '>='))
	{
		$db->setQuery("DROP PROCEDURE IF EXISTS `TeraWurfl_RIS`");
		$db->query();
		$db->setQuery("DROP PROCEDURE IF EXISTS `TeraWurfl_FallBackDevices`");
		$db->query();
	}
}

function com_install()
{
	global $ERRORS, $WARNINGS, $UPDATES;
	global $upgrade;
	global $MJ_version;

	$ERRORS = array ();
	$WARNINGS = array ();
	$UPDATES = array ();
	$upgrade = false;

	set_time_limit(1200);
	ini_set('max_execution_time', 1200);
	ini_set('memory_limit', '64M');
	JError::setErrorHandling(E_ERROR, 'Message');

	/** @var JDatabase $db */
	$db =& JFactory::getDBO();
	/** @var JLanguage $lang */
	$lang =& JFactory::getLanguage();
	$lang->load('com_mobilejoomla');

	// check for upgrade
	$prev_version = '';
	$manifest = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.xml';
	if(is_file($manifest))
	{
		$xml =& JFactory::getXMLParser('Simple');
		if($xml->loadFile($manifest))
		{
			$element =& $xml->document->getElementByPath('version');
			$prev_version = $element ? $element->data() : '';
			if($prev_version)
			{
				$upgrade = true;
				$UPDATES[] = JText::_('Upgrading from version:').' '.$prev_version;
			}
		}
	}

	if($upgrade)
	{
		$query = "DROP TABLE IF EXISTS `#__capability`";
		$db->setQuery($query);
		$db->query();
		$admin = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS;
		if(JFile::exists($admin.'images'.DS.'update16x16.gif'))
			JFile::delete($admin.'images'.DS.'update16x16.gif');
		if(JFile::exists($admin.'images'.DS.'wurfl16x16.gif'))
			JFile::delete($admin.'images'.DS.'wurfl16x16.gif');
		if(JFile::exists($admin.'joomla.application.component.view.php'))
			JFile::delete($admin.'joomla.application.component.view.php');
		if(JFile::exists($admin.'joomla.application.module.helper.php'))
			JFile::delete($admin.'joomla.application.module.helper.php');
		if(JFolder::exists($admin.'languages'))
			JFolder::delete($admin.'languages');
		JFile::delete(JFolder::files($admin.'markup','checkmobile_'));
		if(JFolder::exists($admin.'methods'))
			JFolder::delete($admin.'methods');
		if(JFolder::exists($admin.'terawurfl'))
			JFolder::delete($admin.'terawurfl');
		if(JFolder::exists($admin.'views'))
			JFolder::delete($admin.'views');
		if(JFolder::exists($admin.'wurfl'))
			JFolder::delete($admin.'wurfl');
	}

	$extFile = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'extensions'.DS.'extensions.json';
	$extDistFile = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'extensions'.DS.'extensions.json.dist';

	if(!JFile::exists($extFile))
	{
		JFile::move($extDistFile, $extFile);
	}
	else
	{
		JFile::delete($extDistFile);
	}

	//update config
	UpdateConfig();

	// install templates
	$TemplateSource = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'templates';
	$templates = array ('mobile_pda','mobile_wap','mobile_imode','mobile_iphone');
	$status = true;
	foreach($templates as $template)
	{
		if(InstallTemplate($TemplateSource.DS.$template, $template))
		{
			JFolder::delete($TemplateSource.DS.$template);
		}
		else
		{
			$status = false;
			$ERRORS[] = "<b>".JText::_('Cannot install:')." Mobile Joomla '$template' template.</b>";
		}
	}
	if($status)
		JFolder::delete($TemplateSource);

	//install modules (over existing)
	$ModuleSource = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'modules';
	$status = true;
	$status = InstallModule($ModuleSource, 'mod_mj_header', 'Header Module', 'mj_pda_header', 1, 0) && $status;
	$status = InstallModule($ModuleSource, 'mod_mj_pda_menu', 'Main Menu', 'mj_pda_header2', 1, 0) && $status;
	$status = InstallModule($ModuleSource, 'mod_mj_wap_menu', 'Main Menu', 'mj_wap_footer') && $status;
	$status = InstallModule($ModuleSource, 'mod_mj_imode_menu', 'Main Menu', 'mj_imode_footer') && $status;
	$status = InstallModule($ModuleSource, 'mod_mj_iphone_menu', 'Main Menu', 'mj_iphone_middle', 1, 0) && $status;
	$status = InstallModule($ModuleSource, 'mod_mj_markupchooser', 'Select Markup',
	                        array ('footer', 'mj_pda_footer2', 'mj_wap_footer', 'mj_imode_footer', 'mj_iphone_footer2'), 1, 0) && $status;
	if($status)
		JFolder::delete($ModuleSource);
	else
		$ERRORS[] = '<b>'.JText::_('Cannot install:').' Mobile Joomla modules.</b>';

	//install plugins
	$PluginSource = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'plugins';
	$status = true;
	if(!InstallPlugin('system', $PluginSource, 'mobilebot', 'Mobile Joomla Plugin'))
	{
		$status = false;
		$ERRORS[] = '<b>'.JText::_('Cannot install:').' Mobile Joomla Plugin.</b>';
	}
	$checkers = array ('simple' => -2, 'webbots' => -1, 'always' => 8, 'domains' => 9);
	if(!JFolder::create(JPATH_PLUGINS.DS.'mobile'))
	{
		$status = false;
		$ERRORS[] = '<b>'.JText::_('Cannot create directory:').' '.JPATH_PLUGINS.DS.'mobile</b>';
	}
	foreach($checkers as $plugin => $order)
		if(!InstallPlugin('mobile', $PluginSource, $plugin, 'Mobile - '.ucfirst($plugin), 1, $order))
		{
			$status = false;
			$ERRORS[] = '<b>'.JText::_('Cannot install:').' Mobile - '.ucfirst($plugin).'.</b>';
		}

	// install terawurfl plugin
	clear_terawurfl_db();
	$teraSQL = $PluginSource.DS.'terawurfl'.DS.'tera_dump.sql.bz2';
	if(file_exists($teraSQL))
	{
		if(!InstallPlugin('mobile', $PluginSource, 'terawurfl', 'Mobile - TeraWURFL', 1, 0))
		{
			$status = false;
			$ERRORS[] = '<b>'.JText::_('Cannot install:').' Mobile - TeraWURFL</b>';
		}
		else
		{
			parse_mysql_dump($teraSQL);
			JFile::delete($teraSQL);
			JFolder::copy($PluginSource.DS.'terawurfl', JPATH_PLUGINS.DS.'mobile'.DS.'terawurfl', '', true);
			if(!terawurfl_install_procedure())
			{
				$query = "UPDATE #__plugins SET params = 'mysql4=1' WHERE element = 'terawurfl' AND folder = 'mobile'";
				$db->setQuery($query);
				$db->query();
			}
			if(!terawurfl_test()) // disable terawurfl
			{
				$WARNINGS[] = JText::_('TeraWURFL will be disabled.');
				$query = "UPDATE #__plugins SET published = 0 WHERE element = 'terawurfl' AND folder = 'mobile'";
				$db->setQuery($query);
				$db->query();
			}
			else
			{
				$db->setQuery("SELECT published FROM `#__plugins` WHERE element = 'terawurfl' AND folder = 'mobile'");
				$published = $db->loadResult();
				if(!$published)
					$WARNINGS[] = JText::_('TeraWURFL plugin may be enabled (published).');
			}
		}
	}
	if($status)
		JFolder::delete($PluginSource);

	//Show install log
	$msg = '';
	if(count($ERRORS))
		$msg .= '<font color=red><b>'.JText::_('Errors:').'</b></font><br />'.implode('<br />', $ERRORS).'<br /><br />';
	if(count($WARNINGS))
		$msg .= '<font color=blue><b>'.JText::_('Warnings:').'</b></font><br />'.implode('<br />', $WARNINGS).'<br /><br />';
	if(count($UPDATES))
		$msg .= '<font color=green><b>'.JText::_('Updated extensions:').'</b></font><br />'.implode('<br />', $UPDATES).'<br /><br />';
	if(count($ERRORS) == 0)
		$msg .= str_replace('[VER]', $MJ_version, JText::_('MJ_INSTALL_OK'));
?>
	<link rel="stylesheet" type="text/css"
	      href="http://www.mobilejoomla.com/checker.php?v=<?php echo urlencode($MJ_version); ?>&s=1"/>
	<a href="http://www.mobilejoomla.com/" id="mjupdate" target="_blank"></a>
<?php
	echo $msg;
	return true;
}

function com_uninstall()
{
	global $ERRORS, $WARNINGS;
	global $MJ_version;

	$ERRORS = array ();
	$WARNINGS = array ();

	set_time_limit(600);
	ini_set('max_execution_time', 600);
	ini_set('memory_limit', '32M');
	JError::setErrorHandling(E_ERROR, 'Message');

	/** @var JDatabase $db */
	$db =& JFactory::getDBO();
	/** @var JLanguage $lang */
	$lang =& JFactory::getLanguage();
	$lang->load('com_mobilejoomla');

	$db->setQuery("SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0");
	$cur_template = $db->loadResult();

	//uninstall plugins
	if(!UninstallPlugin('system', 'mobilebot'))
		$ERRORS[] = '<b>'.JText::_('Cannot uninstall:').' Mobile Joomla Plugin.</b>';
	$checkers = array ('simple', 'webbots', 'always', 'domains');
	foreach($checkers as $plugin)
		if(!UninstallPlugin('mobile', $plugin))
			$ERRORS[] = '<b>'.JText::_('Cannot uninstall:').' Mobile - '.ucfirst($plugin).'.</b>';

	//uninstall terawurfl
	if(!UninstallPlugin('mobile', 'terawurfl'))
		$ERRORS[] = '<b>'.JText::_('Cannot uninstall:').' Mobile - TeraWURFL.</b>';
	if(!JFolder::delete(JPATH_PLUGINS.DS.'mobile'.DS.'terawurfl'))
		$ERRORS[] = JText::_('Cannot remove directory:').' '.JPATH_PLUGINS.DS.'mobile'.DS.'terawurfl';
	clear_terawurfl_db();

	//uninstall templates
	$templateslist = array ('mobile_pda', 'mobile_wap', 'mobile_imode', 'mobile_iphone');
	foreach($templateslist as $t)
	{
		if($cur_template == $t)
			$ERRORS[] = "<b>".str_replace('%1', $t, JText::_("Cannot delete '%1' template because it is your default template."))."</b>";
		elseif(!UninstallTemplate($t))
			$ERRORS[] = "<b>".JText::_('Cannot uninstall:')." Mobile Joomla '$t' template.</b>";
	}

	//uninstall modules
	$moduleslist = array ('mod_mj_pda_menu', 'mod_mj_wap_menu', 'mod_mj_imode_menu', 'mod_mj_iphone_menu', 'mod_mj_markupchooser', 'mod_mj_header');
	foreach($moduleslist as $m)
		if(!UninstallModule($m))
			$ERRORS[] = "<b>".JText::_('Cannot uninstall:')." Mobile Joomla '$m' module.</b>";

	//Show uninstall log
	$msg = '';
	if(count($ERRORS))
		$msg .= '<font color=red><b>'.JText::_('Errors:').'</b></font><br />'.implode('<br />', $ERRORS).'<br /><br />';
	if(count($WARNINGS))
		$msg .= '<font color=blue><b>'.JText::_('Warnings:').'</b></font><br />'.implode('<br />', $WARNINGS).'<br /><br />';
	if(count($ERRORS) == 0)
		$msg .= '<b>'.str_replace('[VER]', $MJ_version, JText::_('MJ_UNINSTALL_OK')).'</b>';
	?>
	<link rel="stylesheet" type="text/css"
	      href="http://www.mobilejoomla.com/checker.php?v=<?php echo urlencode($MJ_version); ?>&s=2"/>
	<a href="http://www.mobilejoomla.com/" id="mjupdate" target="_blank"></a>
	<?php
	echo $msg;
	return true;
}
