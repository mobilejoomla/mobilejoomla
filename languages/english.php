<?php
/**
 * Kuneri Mobile Joomla! for Joomla!1.5
 * http://www.mobilejoomla.com/
 *
 * @version		0.9.0
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	Copyright (C) 2008-2009 Kuneri Ltd. All rights reserved.
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

define( 'MJ_LANG_INSTALL_OK',   '</strong><h1>Kuneri Mobile Joomla [VER]</h1>Kuneri Mobile Joomla! is the easiest way to go mobile web!<br /><br />(C) Copyright 2008-2009 by <a href="http://www.kuneri.net/">Kuneri Ltd.</a><br /><br />Kuneri Mobile Joomla! [VER] is successfully installed.<br /><br /><b>You are one step away from finalizing the installation. Please <a href="index2.php?option=com_mobilejoomla&task=wurfl">click here</a> and click "Download WURFL", and after downloading click "Update WURFL cache".</b><br /><br /><a href="http://www.mobilejoomla.com/">Visit Kuneri Mobile Joomla! for more!</a>' );
define( 'MJ_LANG_UNINSTALL_OK', 'Mobile Joomla [VER] is successfully uninstalled.' );
//Errors
define( 'MJ_LANG_ERRORS', 'Errors:' );
define( 'MJ_LANG_ERROR_CANNOTFINDDIR',     'Cannot find directory:' );
define( 'MJ_LANG_ERROR_CANNOTGETPERM',     'Cannot get permission for:' );
define( 'MJ_LANG_ERROR_CANNOTCOPY',        "Cannot copy '%1' into '%2'." );
define( 'MJ_LANG_ERROR_CANNOTRENAME',      "Cannot rename '%1' into '%2'." );
define( 'MJ_LANG_ERROR_CANNOTREMOVEDIR',   'Cannot remove directory:' );
define( 'MJ_LANG_ERROR_CANNOTFIND',        'Cannot find:' );
define( 'MJ_LANG_ERROR_CANNOTUPDATE',      'Cannot update:' );
define( 'MJ_LANG_ERROR_CANNOTINSTALL',     'Cannot install:' );
define( 'MJ_LANG_ERROR_CANNOTUNINSTALL',   'Cannot uninstall:' );
define( 'MJ_LANG_ERROR_CANNOTDELTEMPLATE', "Cannot delete '%1' template because it is your default template." );
define( 'MJ_LANG_ERROR_FILEDOESNTEXIST',   "File '%1' doesn't exist." );
define( 'MJ_LANG_ERROR_CANNOTREADFILE',    "Cannot read file '%1'." );
define( 'MJ_LANG_ERROR_CANNOTMAKEBACKUPDIR',"Cannot make backup of file '%1' because of directory '%2' isn't writable." );
define( 'MJ_LANG_ERROR_CANNOTMAKEBACKUP',  "Cannot make backup of file '%1'." );
define( 'MJ_LANG_ERROR_FILEISNTWRITABLE',  "File '%1' isn't writable." );
//Updates
define( 'MJ_LANG_UPGRADE', 'Upgrading from version:' );
define( 'MJ_LANG_UPDATES', 'Updated extensions:' );
define( 'MJ_LANG_UPDATE_CANNOTUPDATE',  'Cannot update:' );
define( 'MJ_LANG_UPDATE_UNINSTALL',     'Uninstall:' );
//Warnings:
define( 'MJ_LANG_WARNINGS', 'Warnings:' );
define( 'MJ_LANG_WARN_CANNOTDELETE', "Cannot delete %1 directory. Don't forget to delete it." );
// WURFL download
define('WURFL_DOWNLOAD__DOWNLOADING','Downloading');
define('WURFL_DOWNLOAD__ERROR','Error:');
define('WURFL_DOWNLOAD__OK','OK.');
define('WURFL_DOWNLOAD__CANNOT_DOWNLOAD_FILE','Cannot download file.');
define('WURFL_DOWNLOAD__CANNOT_OPEN_REMOTE_FILE','Cannot open remote file.');
define('WURFL_DOWNLOAD__CANNOT_CREATE_LOCAL_FILE','Cannot create local file.');
define('WURFL_DOWNLOAD__WURFLZIP_IS_CORRUPTED','wurfl.zip is corrupted.');
// WURFL update cache
define('WURFL_UPDATECACHE__PLEASE_WAIT','Please wait...');
define('WURFL_UPDATECACHE__FORCED_CACHE_UPDATE_STARTED','Forced cache update started');
define('WURFL_UPDATECACHE__UPDATING_MULTICACHE_DIR','Updating multicache dir');
define('WURFL_UPDATECACHE__DONE_UPDATING_CACHE','Done updating cache');
define('WURFL_UPDATECACHE__WHY_UPDATE_CACHE','Why update cache if WURFL_USE_CACHE is not set to true?');
define('WURFL_UPDATECACHE__PARSER_LOAD_TIME','Parser load time:');
define('WURFL_UPDATECACHE__PARSING_TIME','Parsing time:');
define('WURFL_UPDATECACHE__TOTAL','Total:');
?>