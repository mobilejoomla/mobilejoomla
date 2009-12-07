<?php
/**
 * Kuneri Mobile Joomla! for Joomla!1.5
 * http://www.mobilejoomla.com/
 *
 * @version		0.9.0
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	Copyright (C) 2008-2009 Kuneri Ltd. All rights reserved.
 */
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );

header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: ".date("r"));

define( 'JPATH_BASE', implode(DS,array_slice(explode(DS,dirname(__FILE__)),0,-3)) );
require_once( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once( JPATH_BASE .DS.'includes'.DS.'helper.php' );
require_once( JPATH_BASE .DS.'includes'.DS.'toolbar.php' );
$mainframe =& JFactory::getApplication('administrator');
$user =& JFactory::getUser();
if ($user->get('guest')) die();

$lang =& JFactory::getLanguage();
$mosConfig_lang = $lang->getBackwardLang();
$languagepath=JPATH_BASE.DS.'components'.DS.'com_mobilejoomla'.DS.'languages'.DS;
if(is_file($languagepath.$mosConfig_lang.'.php'))
	include($languagepath.$mosConfig_lang.'.php');
elseif(is_file($languagepath.'english.php'))
	include($languagepath.'english.php');
else
	$error_msg="<b>Error:</b> language file '${languagepath}english.php' is not found.";

echo "<html>\n<body>\n";
if(isset($error_msg)) echo $error_msg.'<br><br><br>';
echo str_repeat(' ',256),"\n";flush();
echo WURFL_UPDATECACHE__PLEASE_WAIT."<br />\n";flush();

include(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php');

$cache=$MobileJoomla_Settings['wurflcache'];
$uacache=$MobileJoomla_Settings['wurfluacache'];
define("WURFL_USE_CACHE", $cache>0);
define("WURFL_USE_MULTICACHE", $cache>1);
define("MAX_UA_CACHE", $uacache);

// clear imageadaptation cache
$database =& JFactory::getDBO();
$database->setQuery('DELETE #__capability FROM #__capability');
$database->query();

// check memory limit
$memory_limit = trim(ini_get('memory_limit'));
if($memory_limit=='')
	$memory_limit='8M';
if($memory_limit>=0)
{
	switch(strtolower($memory_limit{strlen($memory_limit)-1}))
	{
		case 'g': $memory_limit *= 1024;
		case 'm': $memory_limit *= 1024;
		case 'k': $memory_limit *= 1024;
	}
	if($memory_limit<48*1024*1024)
		ini_set('memory_limit','48M');
}

function RemoveDir($path)
{
	if(!$path) return false;
	if(!is_dir($path)) return false;
	if($dh=opendir($path.DIRECTORY_SEPARATOR))
	{
		while(($file=readdir($dh))!==false)
		{
			if(($file=='.')||($file=='..')) continue;
			$newpath=$path.DIRECTORY_SEPARATOR.$file;
			if(is_dir($newpath))
				RemoveDir($newpath);
			else
				unlink($newpath);
        }
        closedir($dh);
    }
	return rmdir($path);
}

/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is WURFL PHP Libraries.
 *
 * The Initial Developer of the Original Code is
 * Andrea Trasatti.
 * Portions created by the Initial Developer are Copyright (C) 2004-2005
 * the Initial Developer. All Rights Reserved.
 *
 * ***** END LICENSE BLOCK ***** */

/*
 * $Id: update_cache.php,v 1.5 2006/09/13 12:52:12 atrasatti Exp $
 * $RCSfile: update_cache.php,v $ v2.1 beta2 (Apr, 16 2005)
 *
 * Author: Andrea Trasatti ( atrasatti AT users DOT sourceforge DOT net )
 * Multicache implementation: Herouth Maoz ( herouth AT spamcop DOT net )
 *
 */

/*
 *
 * This script should be called manually (CLI is suggested) to update the
 * multicache files when a new XML is available, e.g.:
 * andrea@wurfl$ php update_cache.php
 *
 * This script should be used when you have configured WURFL_CACHE_AUTOUPDATE
 * to false.
 *
 * KNOWN BUG: cache.php will be updated automatically, a race condition might
 * happen while generating the new files in the temporary directory and before
 * it's moved to the default path. Using a temporary cache file would fix this
 * issue. Your contributions/fixes are welcome ;-)
 *
 * More info can be found here in the PHP section:
 * http://wurfl.sourceforge.net/php/
 *
 * Questions or comments can be sent to
 * "Andrea Trasatti" <atrasatti AT users DOT sourceforge DOT net>
 *
 * Please, support this software, send any suggestion and improvement to me
 * or the mailing list and we will try to keep it updated and make it better
 * every day.
 *
 * If you like it and use it, please let me know or contact the wmlprogramming
 * mailing list: wmlprogramming@yahoogroups.com
 *
 */

set_time_limit(600);

list($usec, $sec) = explode(" ", microtime());
$start = ((float)$usec + (float)$sec); 

require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'wurfl'.DS.'wurfl_config.php');
define('FORCED_UPDATE', true);
require_once(WURFL_PARSER_FILE);

list($usec, $sec) = explode(" ", microtime());
$load_parser = ((float)$usec + (float)$sec); 

wurfl_log('update_cache', "Forced cache update started");
echo WURFL_UPDATECACHE__FORCED_CACHE_UPDATE_STARTED.'<br />';flush();
if (WURFL_USE_CACHE === true) {
	parse();
	if ( WURFL_USE_MULTICACHE === true ) {
		wurfl_log('update_cache', "Updating multicache dir");
		echo WURFL_UPDATECACHE__UPDATING_MULTICACHE_DIR.'<br />';flush();
		touch(MULTICACHE_TOUCH);
		if ( is_dir(MULTICACHE_DIR) )
			RemoveDir(substr(MULTICACHE_DIR, 0, -1));
		if(!rename(substr(MULTICACHE_TMP_DIR, 0, -1), substr(MULTICACHE_DIR, 0, -1)))
			if(copy(substr(MULTICACHE_TMP_DIR, 0, -1), substr(MULTICACHE_DIR, 0, -1)))
				unlink(substr(MULTICACHE_TMP_DIR, 0, -1));
		unlink(MULTICACHE_TOUCH);
	}
	wurfl_log('update_cache', "Done updating cache");
	echo WURFL_UPDATECACHE__DONE_UPDATING_CACHE.'<br />';flush();
} else {
	wurfl_log('update_cache', "Why update cache if WURFL_USE_CACHE is not set to true?");
	echo WURFL_UPDATECACHE__WHY_UPDATE_CACHE.'<br />';flush();
}

list($usec, $sec) = explode(" ", microtime());
$parse = ((float)$usec + (float)$sec); 

echo "<br />\n";
echo WURFL_UPDATECACHE__PARSER_LOAD_TIME.' '.($load_parser-$start)."<br />\n";
echo WURFL_UPDATECACHE__PARSING_TIME.' '.($parse-$load_parser)."<br />\n";
echo WURFL_UPDATECACHE__TOTAL.' '.($parse-$start)."<br />\n";

if(strcmp(PHP_VERSION,'PHP 5.2.0')<0 || error_get_last()===NULL)
	echo '<script>opener.location=opener.location;window.close();</script>';flush();

echo "</body>\n</html>\n";
?>
