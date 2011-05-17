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

jimport('joomla.installer.installer');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

function MJ_version()
{
	return '###VERSION###';
}

function isJoomla16()
{
	static $is_joomla16;
	if(isset($is_joomla16))
		return $is_joomla16;
	$version = new JVersion;
 	$is_joomla16 = (substr($version->getShortVersion(),0,3) == '1.6');
	return $is_joomla16;
}

function getExtensionId($type, $name, $group='')
{
	/** @var JDatabase $db */
	$db =& JFactory::getDBO();
 	if(isJoomla16())
	{
		if($type=='plugin')
			$db->setQuery("SELECT extension_id FROM #__extensions WHERE `type`='$type' AND `folder`='$group' AND `element`='$name'");
		else
			$db->setQuery("SELECT extension_id FROM #__extensions WHERE `type`='$type' AND `element`='$name'");
		return $db->loadResult();
	}
	//Joomla!1.5
	switch($type)
	{
	case 'plugin':
		$db->setQuery("SELECT id FROM #__plugins WHERE `folder`='$group' AND `element`='$name'");
		return $db->loadResult();
	case 'module':
		$db->setQuery("SELECT id FROM #__modules WHERE `module`='$name'");
		return $db->loadResult();
	case 'template':
		return $name;
	default:
		return false;
	}
}

function InstallPlugin($group, $sourcedir, $name, $fullname, $publish = 1, $ordering = -99)
{
	$upgrade = getExtensionId('plugin', $name, $group);
	$installer = new JInstaller();
	if(!$installer->install($sourcedir.DS.$name))
		return false;
	if(!$upgrade)
	{
		/** @var JDatabase $db */
		$db =& JFactory::getDBO();
		if(isJoomla16())
			$db->setQuery("UPDATE `#__extensions` SET `enabled`=$publish, `ordering`=$ordering WHERE `type`='plugin' AND `element`='$name' AND `folder`='$group'");
		else
			$db->setQuery("UPDATE `#__plugins` SET `published`=$publish, `ordering`=$ordering  WHERE `element`='$name' AND `folder`='$group'");
		$db->query();
	}
	return true;
}

function UninstallPlugin($group, $name)
{
	$id = getExtensionId('plugin', $name, $group);
	$installer = new JInstaller();
	if(!$installer->uninstall('plugin', $id))
		return false;
	return true;
}

function InstallTemplate($sourcedir, $name)
{
	$installer = new JInstaller();
	if(!$installer->install($sourcedir.DS.$name))
		return false;
	if(!isJoomla16())
	{
		/** @var JDatabase $db */
		$db =& JFactory::getDBO();
		$db->setQuery('SELECT COUNT(*) FROM #__templates_menu WHERE template = '.$db->Quote($name));
		if($db->loadResult()==0)
		{
			$db->setQuery('INSERT INTO #__templates_menu (template, menuid) VALUES ('.$db->Quote($name).', -1)');
			$db->query();
		}
	}
	return true;
}

function UninstallTemplate($name)
{
	$id = getExtensionId('template', $name);
	$installer = new JInstaller();
	if(!$installer->uninstall('template', $id))
		return false;
	if(!isJoomla16())
	{
		/** @var JDatabase $db */
		$db =& JFactory::getDBO();
		$db->setQuery('DELETE FROM #__templates_menu WHERE template = '.$db->Quote($name));
		$db->query();
	}
	return true;
}

function InstallModule($sourcedir, $name, $title, $position, $published = 1, $showtitle = 1, $admin = 0)
{
	if(!is_array($position))
		$position = array ($position);
	$upgrade = getExtensionId('module', $name);
	$installer = new JInstaller();
	if(!$installer->install($sourcedir.DS.$name))
		return false;
	if(!$upgrade)
	{
		$id = getExtensionId('module', $name);
		if($id)
		{
			/** @var JDatabase $db */
			$db =& JFactory::getDBO();

			if(isJoomla16())
				$db->setQuery("SELECT `params` FROM `#__extensions` WHERE extension_id=$id");
			else
				$db->setQuery("SELECT `params` FROM `#__modules` WHERE id=$id");
			$params = $db->Quote($db->loadResult());

			$db->setQuery("DELETE FROM `#__modules` WHERE `module`='$name'");
			$db->query();

			if(!isJoomla16())
			{
				$db->setQuery("DELETE FROM `#__modules_menu` WHERE moduleid=$id");
				$db->query();
			}

			$access = isJoomla16() ? 1 : 0;
			foreach($position as $pos)
			{
				$db->setQuery("SELECT MAX(ordering) FROM `#__modules` WHERE `position`='$pos'");
				$ordering = $db->loadResult();
				++$ordering;

				if(isJoomla16())
					$db->setQuery("INSERT INTO `#__modules` (`title`, `ordering`, `position`, `published`, `module`, `showtitle`, `params`, `access`, `client_id`, `language`) VALUES ('$title', $ordering, '$pos', $published, '$name', $showtitle, $params, $access, $admin, '*')");
				else
					$db->setQuery("INSERT INTO `#__modules` (`title`, `ordering`, `position`, `published`, `module`, `showtitle`, `params`, `access`, `client_id`) VALUES ('$title', $ordering, '$pos', $published, '$name', $showtitle, $params, $access, $admin)");
				$db->query();
				$id = (int) $db->insertid();

				$db->setQuery("INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES ($id, 0)");
				$db->query();
			}
		}
	}
	return true;
}

function UninstallModule($name)
{
	$id = getExtensionId('module', $name);
	$installer = new JInstaller();
	if(!$installer->uninstall('module', $id))
		return false;
	return true;
}

function UpdateConfig()
{
	$configfile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php';
	$defconfigfile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'defconfig.php';

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
		JError::raiseError(0, JText::_('COM_MJ__CANNOT_FIND')." $defconfigfile");
		return false;
	}

	unset($MobileJoomla_Settings['version']);
	if(isset($MobileJoomla_Settings['useragent']))
		unset($MobileJoomla_Settings['useragent']);
	if(!isset($MobileJoomla_Settings['caching']))
		$MobileJoomla_Settings['caching'] = 0;
	if(!isset($MobileJoomla_Settings['httpcaching']))
		$MobileJoomla_Settings['httpcaching'] = 1;

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

	if(!isset($MobileJoomla_Settings['mobile_sitename']) || $MobileJoomla_Settings['mobile_sitename']=='')
	{
		$conf =& JFactory::getConfig();
		$MobileJoomla_Settings['mobile_sitename'] = $conf->getValue('sitename');
	}

	if(!isset($MobileJoomla_Settings['tmpl_xhtml_img_addstyles']))
		$MobileJoomla_Settings['tmpl_xhtml_img_addstyles'] = 0;
	if(!isset($MobileJoomla_Settings['tmpl_iphone_img_addstyles']))
		$MobileJoomla_Settings['tmpl_iphone_img_addstyles'] = 0;
	
	if(!isset($MobileJoomla_Settings['tmpl_xhtml_header3']))
		$MobileJoomla_Settings['tmpl_xhtml_header3'] = 'mj_all_header';
	if(!isset($MobileJoomla_Settings['tmpl_xhtml_middle3']))
		$MobileJoomla_Settings['tmpl_xhtml_middle3'] = 'mj_all_middle';
	if(!isset($MobileJoomla_Settings['tmpl_xhtml_footer3']))
		$MobileJoomla_Settings['tmpl_xhtml_footer3'] = 'mj_all_footer';

	if(!isset($MobileJoomla_Settings['tmpl_iphone_header3']))
		$MobileJoomla_Settings['tmpl_iphone_header3'] = 'mj_all_header';
	if(!isset($MobileJoomla_Settings['tmpl_iphone_middle3']))
		$MobileJoomla_Settings['tmpl_iphone_middle3'] = 'mj_all_middle';
	if(!isset($MobileJoomla_Settings['tmpl_iphone_footer3']))
		$MobileJoomla_Settings['tmpl_iphone_footer3'] = 'mj_all_footer';

	if(isset($MobileJoomla_Settings['tmpl_wap_header']))
	{
		$MobileJoomla_Settings['tmpl_wap_header1'] = $MobileJoomla_Settings['tmpl_wap_header'];
		unset($MobileJoomla_Settings['tmpl_wap_header']);
	}
	if(isset($MobileJoomla_Settings['tmpl_wap_middle']))
	{
		$MobileJoomla_Settings['tmpl_wap_middle1'] = $MobileJoomla_Settings['tmpl_wap_middle'];
		unset($MobileJoomla_Settings['tmpl_wap_middle']);
	}
	if(isset($MobileJoomla_Settings['tmpl_wap_footer']))
	{
		$MobileJoomla_Settings['tmpl_wap_footer1'] = $MobileJoomla_Settings['tmpl_wap_footer'];
		unset($MobileJoomla_Settings['tmpl_wap_footer']);
	}
	if(!isset($MobileJoomla_Settings['tmpl_wap_header2']))
		$MobileJoomla_Settings['tmpl_wap_header2'] = '';
	if(!isset($MobileJoomla_Settings['tmpl_wap_middle2']))
		$MobileJoomla_Settings['tmpl_wap_middle2'] = '';
	if(!isset($MobileJoomla_Settings['tmpl_wap_footer2']))
		$MobileJoomla_Settings['tmpl_wap_footer2'] = '';
	if(!isset($MobileJoomla_Settings['tmpl_wap_header3']))
		$MobileJoomla_Settings['tmpl_wap_header3'] = 'mj_all_header';
	if(!isset($MobileJoomla_Settings['tmpl_wap_middle3']))
		$MobileJoomla_Settings['tmpl_wap_middle3'] = 'mj_all_middle';
	if(!isset($MobileJoomla_Settings['tmpl_wap_footer3']))
		$MobileJoomla_Settings['tmpl_wap_footer3'] = 'mj_all_footer';

	if(!isset($MobileJoomla_Settings['tmpl_imode_header3']))
		$MobileJoomla_Settings['tmpl_imode_header3'] = 'mj_all_header';
	if(!isset($MobileJoomla_Settings['tmpl_imode_middle3']))
		$MobileJoomla_Settings['tmpl_imode_middle3'] = 'mj_all_middle';
	if(!isset($MobileJoomla_Settings['tmpl_imode_footer3']))
		$MobileJoomla_Settings['tmpl_imode_footer3'] = 'mj_all_footer';

	if(!isset($MobileJoomla_Settings['iphoneipad']))
		$MobileJoomla_Settings['iphoneipad'] = 1;

	if(!function_exists('imagecopyresized'))
	{
		JError::raiseWarning(0, JText::_('COM_MJ__GD2_LIBRARY_IS_NOT_LOADED'));
		if($MobileJoomla_Settings['tmpl_xhtml_img'] > 1)
			$MobileJoomla_Settings['tmpl_xhtml_img'] = 1;
		if($MobileJoomla_Settings['tmpl_wap_img'] > 1)
			$MobileJoomla_Settings['tmpl_wap_img'] = 1;
		if($MobileJoomla_Settings['tmpl_imode_img'] > 1)
			$MobileJoomla_Settings['tmpl_imode_img'] = 1;
		if($MobileJoomla_Settings['tmpl_iphone_img'] > 1)
			$MobileJoomla_Settings['tmpl_iphone_img'] = 1;
	}

	$MobileJoomla_Settings['httpcaching'] = 0;

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
			. "'version'=>'".MJ_version()."',\n"
			. implode(",\n", $params)."\n"
			. ");\n"
			. "?>";

	if(!JFile::write($configfile, $config))
	{
		JError::raiseError(0, JText::_('COM_MJ__CANNOT_UPDATE').' '.$configfile);
		return false;
	}

	return true;
}

function terawurfl_install_procedure()
{
	/** @var JDatabase $db */
	$db =& JFactory::getDBO();

	if(version_compare($db->getVersion(), '5.0.0', '<'))
		return false;

	$TeraWurfl_RIS = "CREATE PROCEDURE `#__TeraWurfl_RIS`(IN ua VARCHAR(255), IN tolerance INT, IN matcher VARCHAR(64))
BEGIN
DECLARE curlen INT;
DECLARE wurflid VARCHAR(64) DEFAULT NULL;
DECLARE curua VARCHAR(255);

SELECT CHAR_LENGTH(ua)  INTO curlen;
findua: WHILE ( curlen >= tolerance ) DO
	SELECT CONCAT(LEFT(ua, curlen ),'%') INTO curua;
	SELECT idx.DeviceID INTO wurflid
		FROM #__TeraWurflIndex idx INNER JOIN #__TeraWurflMerge mrg ON idx.DeviceID = mrg.DeviceID
		WHERE mrg.match = 1 AND idx.matcher = matcher
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

	$TeraWurfl_FallBackDevices = "CREATE PROCEDURE `#__TeraWurfl_FallBackDevices`(current_fall_back VARCHAR(64))
BEGIN
WHILE current_fall_back != 'root' DO
	SELECT capabilities FROM #__TeraWurflMerge WHERE deviceID = current_fall_back;
	SELECT fall_back FROM #__TeraWurflMerge WHERE deviceID = current_fall_back INTO current_fall_back;
END WHILE;
END";
	$db->setQuery($TeraWurfl_FallBackDevices);
	$isSuccessful = $db->query() && $isSuccessful;

	return $isSuccessful;
}

function terawurfl_test()
{
	$test = true;

	if(version_compare(phpversion(), '5.0.0', '<'))
	{
		JError::raiseWarning(0, JText::_('COM_MJ__TERAWURFL_PHP5_ONLY'));
		$test = false;
	}

	if(!class_exists('mysqli') || !function_exists('mysqli_connect'))
	{
		JError::raiseWarning(0, JText::_('COM_MJ__TERAWURFL_MYSQLI_LIBRARY'));
		$test = false;
	}

	if(!$test)
		return false;
	
	/** @var JRegistry $config */
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
		JError::raiseWarning(0, JText::sprintf('COM_MJ__FAILED_TO_CONNECT_MYSQLI', mysqli_connect_errno(), mysqli_connect_error()));
		return false;
	}
	$mysqli->close();

	return true;
}

function safe_dl($name)
{
	if(extension_loaded($name))
		return true;
	if(!(bool)ini_get('enable_dl')
		|| (bool)ini_get('safe_mode')
		|| !function_exists('dl'))
		return false;
	$sapi_name = php_sapi_name();
	if($sapi_name!='cli' && (substr($sapi_name,0,3)!='cgi' || isset($_SERVER['GATEWAY_INTERFACE'])))
		return false;
	if(substr(PHP_OS, 0, 3) == 'WIN')
		@dl('php_'.$name.'.dll');
	else
		@dl($name.'.'.PHP_SHLIB_SUFFIX);
	return extension_loaded($name);
}

function parse_mysql_dump($handler, $uri)
{
	static $methods = array(
		'file' => array('open'=>'fopen',  'read'=>'fread',  'close'=>'fclose',  'eof'=>'feof'),
		'gz'   => array('open'=>'gzopen', 'read'=>'gzread', 'close'=>'gzclose', 'eof'=>'gzeof'),
		'bz2'  => array('open'=>'bzopen', 'read'=>'bzread', 'close'=>'bzclose', 'eof'=>'feof')
	);
	foreach($methods[$handler] as $func)
		if(!function_exists($func))
			return false;
	$open  = $methods[$handler]['open'];
	$read  = $methods[$handler]['read'];
	$close = $methods[$handler]['close'];
	$eof   = $methods[$handler]['eof'];
	$mode  = $handler=='bz2' ? 'r' : 'rb';

	$f = @$open($uri, $mode);
	if(!$f)
		return false;

	/** @var JRegistry $conf */
	$conf =& JFactory::getConfig();
	$debuglevel = $conf->getValue('config.debug');

	/** @var JDatabase $db */
	$db =& JFactory::getDBO();
	$db->debug(0);

	$sql_line = '';
	$counter = 0;
	while(!$eof($f))
	{
		$buf = $read($f, 32768);
		if(trim($buf))
		{
			$sql_line .= $buf;
			$queries = explode(";\n", $sql_line);
			$sql_line = array_pop($queries);
			foreach($queries as $query)
			{
				$db->setQuery($query);
				if($db->query()===false)
				{
					JError::raiseError(0, 'Database error: '.$db->getErrorMsg());
					break 2;
				}
				$counter++;
			}
		}
	}
	$close($f);

	$db->debug($debuglevel);
	if($debuglevel)
	{
		$db->setQuery("# Insert $counter terawurfl queries");
		$db->query();
	}
	return true;
}

function load_mysql_dump($bz2_file)
{
	safe_dl('bz2');
	if(parse_mysql_dump('bz2', $bz2_file))
		return true;

	$teraPath = dirname($bz2_file).DS;
	$teraSQL = $teraPath.'tera_dump.sql';
	$disable_functions = ini_get('disable_functions');
	if( !(bool)ini_get('safe_mode') && JFile::exists($bz2_file) &&
		(preg_match('#\bescapeshellarg\b#', $disable_functions)==0) &&
		(preg_match('#\bexec\b#', $disable_functions)==0) )
	{
		$pwd = getcwd();
		chdir($teraPath);
		@exec('bunzip2 -k '.escapeshellarg($bz2_file).' 2>&1');
		chdir($pwd);
	}

	$teraSQL_root = JPATH_SITE.DS.'tera_dump.sql';
	if(!JFile::exists($teraSQL) && JFile::exists($teraSQL_root))
		$teraSQL = $teraSQL_root;

	if(JFile::exists($teraSQL))
	{
		$dump_ok = parse_mysql_dump('file', $teraSQL);
		if(!$dump_ok)
			JError::raiseWarning(0, JText::_('COM_MJ__ERROR_READING').' '.$teraSQL);
		if($teraSQL != $teraSQL_root)
			JFile::delete($teraSQL);
		return $dump_ok;
	}
	elseif(ini_get('allow_url_fopen'))
	{
		safe_dl('zlib');
		if(parse_mysql_dump('gz', 'http://www.mobilejoomla.com/tera_dump.sql.gz'))
			return true;
		$url = 'http://www.mobilejoomla.com/tera_dump.sql';
		if(parse_mysql_dump('file', $url))
			return true;
		JError::raiseWarning(0, JText::_('COM_MJ__ERROR_DOWNLOADING').' '.$url);
		return false;
	}
	else
	{
		JError::raiseWarning(0, JText::_('COM_MJ__CANNOT_DOWNLOAD_TERAWURFL'));
		return false;
	}
}

function clear_terawurfl_db()
{
	/** @var JDatabase $db */
	$db =& JFactory::getDBO();
	$tables = array ('#__TeraWurflCache', '#__TeraWurflCache_TEMP', '#__TeraWurflIndex', '#__TeraWurflMerge',
					 '#__TeraWurflSettings',
	                 '#__TeraWurfl_Alcatel', '#__TeraWurfl_Android', '#__TeraWurfl_AOL', '#__TeraWurfl_Apple',
	                 '#__TeraWurfl_BenQ', '#__TeraWurfl_BlackBerry', '#__TeraWurfl_Bot', '#__TeraWurfl_CatchAll',
	                 '#__TeraWurfl_Chrome', '#__TeraWurfl_DoCoMo', '#__TeraWurfl_Firefox', '#__TeraWurfl_Grundig',
	                 '#__TeraWurfl_HTC', '#__TeraWurfl_Kddi', '#__TeraWurfl_Konqueror', '#__TeraWurfl_Kyocera',
	                 '#__TeraWurfl_LG', '#__TeraWurfl_Mitsubishi', '#__TeraWurfl_Motorola', '#__TeraWurfl_MSIE',
	                 '#__TeraWurfl_Nec', '#__TeraWurfl_Nintendo', '#__TeraWurfl_Nokia', '#__TeraWurfl_Opera',
	                 '#__TeraWurfl_OperaMini', '#__TeraWurfl_Panasonic', '#__TeraWurfl_Pantech', '#__TeraWurfl_Philips',
	                 '#__TeraWurfl_Portalmmm', '#__TeraWurfl_Qtek', '#__TeraWurfl_Safari', '#__TeraWurfl_Sagem',
					 '#__TeraWurfl_Samsung', '#__TeraWurfl_Sanyo', '#__TeraWurfl_Sharp', '#__TeraWurfl_Siemens',
					 '#__TeraWurfl_SonyEricsson', '#__TeraWurfl_SPV', '#__TeraWurfl_Toshiba', '#__TeraWurfl_Vodafone',
	                 '#__TeraWurfl_WindowsCE');
	$query = 'DROP TABLE IF EXISTS `'.implode('`, `',$tables).'`';
	$db->setQuery($query);
	$db->query();
	if(version_compare($db->getVersion(), '5.0.0', '>='))
	{
		$db->setQuery("DROP PROCEDURE IF EXISTS `#__TeraWurfl_RIS`");
		$db->query();
		$db->setQuery("DROP PROCEDURE IF EXISTS `#__TeraWurfl_FallBackDevices`");
		$db->query();
	}
}

function str2int($str)
{
	$unit = strtoupper(substr($str, -1));
	$num = intval(substr($str, 0, -1));
	switch($unit)
	{
	case 'G': $num *= 1024;
	case 'M': $num *= 1024;
	case 'K': $num *= 1024;
			  break;
	default:  $num = intval($str);
	}
	return $num;
}

function com_install()
{
	JError::setErrorHandling(E_ERROR, 'Message');

	@set_time_limit(1200);
	@ini_set('max_execution_time', 1200);

	$mj_memory_limit = '32M';
	$memory_limit = @ini_get('memory_limit');
	if($memory_limit && str2int($memory_limit) < str2int($mj_memory_limit))
		@ini_set('memory_limit', $mj_memory_limit);

	/** @var JDatabase $db */
	$db =& JFactory::getDBO();
	/** @var JLanguage $lang */
	$lang =& JFactory::getLanguage();
	$lang->load('com_mobilejoomla');

	// check for upgrade
	$upgrade = false;
	$prev_version = '';
	$manifest = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.xml';
	if(is_file($manifest))
	{
		$xml =& JFactory::getXMLParser('Simple');
		if($xml->loadFile($manifest))
		{
			$element =& $xml->document->getElementByPath('version');
			$prev_version = $element ? $element->data() : '';
			if($prev_version)
				$upgrade = true;
		}
	}

	$xm_files = JFolder::files(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'packages', '\.xm_$', 2, true);
	if(!empty($xm_files)) foreach($xm_files as $file)
	{
		$newfile = str_replace('.xm_', '.xml', $file);
		JFile::move($file, $newfile);
	}

	if($upgrade && !isJoomla16())
	{
		$query = "DROP TABLE IF EXISTS `#__capability`";
		$db->setQuery($query);
		$db->query();
		$admin = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS;
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
		if(JFile::exists(JPATH_PLUGINS.DS.'mobile'.DS.'webbots.php'))
			UninstallPlugin('mobile', 'webbots');
	}

	$extFile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'extensions'.DS.'extensions.json';
	$extDistFile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'extensions'.DS.'extensions.json.dist';

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
	$TemplateSource = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'packages'.DS.'templates';
	$TemplateSource .= isJoomla16() ? '16' : '15';
	$templates = array ('mobile_pda','mobile_wap','mobile_imode','mobile_iphone');
	$status = true;
	foreach($templates as $template)
	{
		if(!InstallTemplate($TemplateSource, $template))
		{
			$status = false;
			JError::raiseError(0, '<b>'.JText::_('COM_MJ__CANNOT_INSTALL')." Mobile Joomla '$template' template.</b>");
		}
	}

	$apple_touch_icon = JPATH_SITE.DS.'templates'.DS.'mobile_iphone'.DS.'apple-touch-icon.png';
	if(!JFile::exists($apple_touch_icon))
		JFile::move($TemplateSource.DS.'mobile_iphone'.DS.'apple-touch-icon.png', $apple_touch_icon);

	if($status)
		JFolder::delete($TemplateSource);

	//install modules
	$ModuleSource = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'packages'.DS.'modules';
	$status = true;
	$status = InstallModule($ModuleSource, 'mod_mj_header', 'Header Module', 'mj_pda_header', 1, 0) && $status;
	$status = InstallModule($ModuleSource, 'mod_mj_pda_menu', 'Main Menu', 'mj_pda_header2', 1, 0) && $status;
	$status = InstallModule($ModuleSource, 'mod_mj_wap_menu', 'Main Menu', 'mj_wap_footer') && $status;
	$status = InstallModule($ModuleSource, 'mod_mj_imode_menu', 'Main Menu', 'mj_imode_footer') && $status;
	$status = InstallModule($ModuleSource, 'mod_mj_iphone_menu', 'Main Menu', 'mj_iphone_middle', 1, 0) && $status;
	$status = InstallModule($ModuleSource, 'mod_mj_markupchooser', 'Select Markup',
	                        array ('footer', 'mj_all_footer'), 1, 0) && $status;
	$status = InstallModule($ModuleSource, 'mod_mj_adminicon', 'MobileJoomla CPanel Icons', 'icon', 1, 0, 1) && $status;
	if($status)
		JFolder::delete($ModuleSource);
	else
		JError::raiseError(0, '<b>'.JText::_('COM_MJ__CANNOT_INSTALL').' Mobile Joomla modules.</b>');

	//install plugins
	$PluginSource = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'packages'.DS.'plugins';
	$status = true;
	if(!InstallPlugin('system', $PluginSource, 'mobilebot', 'Mobile Joomla Plugin'))
	{
		$status = false;
		JError::raiseError(0, '<b>'.JText::_('COM_MJ__CANNOT_INSTALL').' Mobile Joomla Plugin.</b>');
	}
	if(!JFolder::create(JPATH_PLUGINS.DS.'mobile'))
	{
		$status = false;
		JError::raiseError(0, '<b>'.JText::_('COM_MJ__CANNOT_CREATE_DIRECTORY').' '.JPATH_PLUGINS.DS.'mobile</b>');
	}
	$checkers = array ('simple' => -2, 'always' => 8, 'domains' => 9);
	foreach($checkers as $plugin => $order)
		if(!InstallPlugin('mobile', $PluginSource, $plugin, 'Mobile - '.ucfirst($plugin), 1, $order))
		{
			$status = false;
			JError::raiseError(0, '<b>'.JText::_('COM_MJ__CANNOT_INSTALL').' Mobile - '.ucfirst($plugin).'.</b>');
		}

	// install terawurfl plugin
	clear_terawurfl_db();
	$teraSQL = $PluginSource.DS.'terawurfl'.DS.'tera_dump.sql.bz2';
	if(file_exists($teraSQL))
	{
		if(!InstallPlugin('mobile', $PluginSource, 'terawurfl', 'Mobile - TeraWURFL', 1, 0))
		{
			$status = false;
			JError::raiseError(0, '<b>'.JText::_('COM_MJ__CANNOT_INSTALL').' Mobile - TeraWURFL</b>');
		}
		else
		{
			$dump_ok = load_mysql_dump($teraSQL);
			JFile::delete($teraSQL);
			if($dump_ok && !terawurfl_install_procedure())
			{
				if(isJoomla16())
					$query = "UPDATE #__extensions SET params = 'mysql4=1' WHERE element = 'terawurfl' AND folder = 'mobile'";
				else
					$query = "UPDATE #__plugins SET params = 'mysql4=1' WHERE element = 'terawurfl' AND folder = 'mobile'";
				$db->setQuery($query);
				$db->query();
			}
			if(!$dump_ok || !terawurfl_test()) // disable terawurfl
			{
				JError::raiseWarning(0, JText::_('COM_MJ__TERAWURFL_WILL_BE_DISABLED'));
				if(isJoomla16())
					$query = "UPDATE #__extensions SET enabled = 0 WHERE element = 'terawurfl' AND folder = 'mobile'";
				else
					$query = "UPDATE #__plugins SET published = 0 WHERE element = 'terawurfl' AND folder = 'mobile'";
				$db->setQuery($query);
				$db->query();
				clear_terawurfl_db();
			}
			else
			{
				if(isJoomla16())
					$db->setQuery("SELECT enabled FROM `#__extensions` WHERE element = 'terawurfl' AND folder = 'mobile'");
				else
					$db->setQuery("SELECT published FROM `#__plugins` WHERE element = 'terawurfl' AND folder = 'mobile'");
				$published = $db->loadResult();
				if(!$published)
					JError::raiseWarning(0, JText::_('COM_MJ__TERAWURFL_MAY_BE_ENABLED'));
			}
		}
	}
	if($status)
		JFolder::delete($PluginSource);

	//Show install status
	$msg = '';
	if($upgrade)
		$msg .= '<font color=green><b>'.JText::_('COM_MJ__UPDATED_EXTENSIONS')."</b></font><br />$prev_version<br /><br />";
	$count = 0;
	foreach(JError::getErrors() as $error)
		if($error->get('level'))
			$count++;
	if($count == 0)
		$msg .= str_replace('[VER]', MJ_version(), JText::_('COM_MJ__INSTALL_OK'));
?>
	<link rel="stylesheet" type="text/css"
	      href="http://www.mobilejoomla.com/checker.php?v=<?php echo urlencode(MJ_version()); ?>&s=1"/>
	<a href="http://www.mobilejoomla.com/" id="mjupdate" target="_blank"></a>
	<?php echo $msg; ?>
<?php
	return true;
}

function com_uninstall()
{
	JError::setErrorHandling(E_ERROR, 'Message');

	/** @var JDatabase $db */
	$db =& JFactory::getDBO();
	/** @var JLanguage $lang */
	$lang =& JFactory::getLanguage();
	$lang->load('com_mobilejoomla');

	if(isJoomla16())
		$db->setQuery("SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1 LIMIT 1");
	else
		$db->setQuery("SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0");
	$cur_template = $db->loadResult();

	//uninstall plugins
	if(!UninstallPlugin('system', 'mobilebot'))
		JError::raiseError(0, '<b>'.JText::_('COM_MJ__CANNOT_UNINSTALL').' Mobile Joomla Plugin.</b>');
	$checkers = array ('simple', 'always', 'domains');
	foreach($checkers as $plugin)
		if(!UninstallPlugin('mobile', $plugin))
			JError::raiseError(0, '<b>'.JText::_('COM_MJ__CANNOT_UNINSTALL').' Mobile - '.ucfirst($plugin).'.</b>');
	//uninstall terawurfl
	if(!UninstallPlugin('mobile', 'terawurfl'))
		JError::raiseError(0, '<b>'.JText::_('COM_MJ__CANNOT_UNINSTALL').' Mobile - TeraWURFL.</b>');
	clear_terawurfl_db();

	//uninstall templates
	$templateslist = array ('mobile_pda', 'mobile_wap', 'mobile_imode', 'mobile_iphone');
	foreach($templateslist as $t)
	{
		if($cur_template == $t)
			JError::raiseError(0, '<b>'.str_replace('%1', $t, JText::_('COM_MJ__CANNOT_DELETE_DEFAULT_TEMPLATE')).'</b>');
		elseif(!UninstallTemplate($t))
			JError::raiseError(0, '<b>'.JText::_('COM_MJ__CANNOT_UNINSTALL')." Mobile Joomla '$t' template.</b>");
	}

	//uninstall modules
	$moduleslist = array ('mod_mj_pda_menu', 'mod_mj_wap_menu', 'mod_mj_imode_menu', 'mod_mj_iphone_menu', 'mod_mj_markupchooser', 'mod_mj_header', 'mod_mj_adminicon');
	foreach($moduleslist as $m)
		if(!UninstallModule($m))
			JError::raiseError(0, '<b>'.JText::_('COM_MJ__CANNOT_UNINSTALL')." Mobile Joomla '$m' module.</b>");

	//Show uninstall status
	$msg = '';
	$count = 0;
	foreach(JError::getErrors() as $error)
		if($error->get('level'))
			$count++;
	if($count == 0)
		$msg .= '<b>'.str_replace('[VER]', MJ_version(), JText::_('COM_MJ__UNINSTALL_OK')).'</b>';
?>
	<link rel="stylesheet" type="text/css"
	      href="http://www.mobilejoomla.com/checker.php?v=<?php echo urlencode(MJ_version()); ?>&s=2"/>
	<a href="http://www.mobilejoomla.com/" id="mjupdate" target="_blank"></a>
	<?php echo $msg; ?>
<?php
	return true;
}
