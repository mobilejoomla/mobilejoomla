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

//
// INSTALLATION
//
define( 'MJ_LANG_INSTALL_OK',   '</strong><h1>Kuneri Mobile Joomla [VER]</h1>Kuneri Mobile Joomla! — это самый простой способ начать работать в мобильном интернете!<br /><br />(C) Copyright 2008-2009 <a href="http://www.kuneri.net/">Kuneri Ltd.</a><br /><br />Kuneri Mobile Joomla! [VER] успешно установлен.<br /><br /><b>Вы находитесь в одном шаге от окончания установки. Пожалуйста <a href="index2.php?option=com_mobilejoomla&task=wurfl">нажмите здесь</a> и кликните "Download WURFL", а после загрузки кликните "Update WURFL cache".</b><br /><br /><a href="http://www.mobilejoomla.com/">Узнайте больше об Kuneri Mobile Joomla!</a>' );
define( 'MJ_LANG_UNINSTALL_OK', 'Компонент Mobile Joomla [VER] успешно удален.' );
//Errors
define( 'MJ_LANG_ERRORS', 'Ошибки:' );
define( 'MJ_LANG_ERROR_CANNOTFINDDIR',     'Не удается найти каталог:' );
define( 'MJ_LANG_ERROR_CANNOTGETPERM',     'Не удается получить права для:' );
define( 'MJ_LANG_ERROR_CANNOTCOPY',        "Не удается скопировать '%1' в '%2'." );
define( 'MJ_LANG_ERROR_CANNOTRENAME',      "Не удается переместить '%1' в '%2'." );
define( 'MJ_LANG_ERROR_CANNOTREMOVEDIR',   'Не удается удалить каталог:' );
define( 'MJ_LANG_ERROR_CANNOTFIND',        'Не удается найти:' );
define( 'MJ_LANG_ERROR_CANNOTUPDATE',      'Не удается обновить:' );
define( 'MJ_LANG_ERROR_CANNOTINSTALL',     'Не удается установить:' );
define( 'MJ_LANG_ERROR_CANNOTUNINSTALL',   'Не удается удалить:' );
define( 'MJ_LANG_ERROR_CANNOTDELTEMPLATE', "Не удается удалить шаблон '%1', т.к. это шаблон по-умолчанию." );
define( 'MJ_LANG_ERROR_FILEDOESNTEXIST',   "Файл '%1' не существует." );
define( 'MJ_LANG_ERROR_CANNOTREADFILE',    "Не удается прочитать файл '%1'." );
define( 'MJ_LANG_ERROR_CANNOTMAKEBACKUPDIR',"Не удается сделать резервную копию файла '%1', т.к. директория '%2' недоступна для записиe." );
define( 'MJ_LANG_ERROR_CANNOTMAKEBACKUP',  "Не удается сделать резервную копию файла '%1'." );
define( 'MJ_LANG_ERROR_FILEISNTWRITABLE',  "Файл '%1' недоступен для записи." );
//Updates
define( 'MJ_LANG_UPGRADE', 'Обновление с версии:' );
define( 'MJ_LANG_UPDATES', 'Обновлены расширения:' );
define( 'MJ_LANG_UPDATE_CANNOTUPDATE',  'Не удается обновить:' );
define( 'MJ_LANG_UPDATE_UNINSTALL',     'Удалено:' );
//Warnings:
define( 'MJ_LANG_WARNINGS', 'Предупреждения:' );
define( 'MJ_LANG_WARN_CANNOTDELETE', "Не удается удалить каталог %1. Не забудьте удалить его." );
// WURFL download
define('WURFL_DOWNLOAD__DOWNLOADING','Загрузка');
define('WURFL_DOWNLOAD__ERROR','Ошибка:');
define('WURFL_DOWNLOAD__OK','OK.');
define('WURFL_DOWNLOAD__CANNOT_DOWNLOAD_FILE','Не удается загрузить файл.');
define('WURFL_DOWNLOAD__CANNOT_OPEN_REMOTE_FILE','Не удается начать скачивание файла.');
define('WURFL_DOWNLOAD__CANNOT_CREATE_LOCAL_FILE','Не удается создать локальный файл.');
define('WURFL_DOWNLOAD__WURFLZIP_IS_CORRUPTED','wurfl.zip поврежден.');
// WURFL update cache
define('WURFL_UPDATECACHE__PLEASE_WAIT','Ждите...');
define('WURFL_UPDATECACHE__FORCED_CACHE_UPDATE_STARTED','Запущена процедура обновления кэша');
define('WURFL_UPDATECACHE__UPDATING_MULTICACHE_DIR','Обновление директории multicache');
define('WURFL_UPDATECACHE__DONE_UPDATING_CACHE','Обновление кэша завершено');
define('WURFL_UPDATECACHE__WHY_UPDATE_CACHE','Зачем обновлять кэш, если WURFL_USE_CACHE не установлена в true?');
define('WURFL_UPDATECACHE__PARSER_LOAD_TIME','Время загрузки парсера:');
define('WURFL_UPDATECACHE__PARSING_TIME','Время парсинга:');
define('WURFL_UPDATECACHE__TOTAL','Полное вемя:');
?>