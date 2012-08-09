<?php
/**
 * Advanced Mobile Device Detection
 *
 * @version		###VERSION###
 * @license		###LICENSE###
 * @copyright	###COPYRIGHT###
 * @date		###DATE###
 */

class AmddDatabaseException extends Exception { }

abstract class AmddDatabase
{
	/**
	 * get instance of database object
	 * @static
	 * @param string $handlerName
	 * @return AmddDatabase
	 * @throws AmddDatabaseException
	 */
	public static function getInstance($handlerName)
	{
		static $handlers = array();

		$handlerName = strtolower($handlerName);

		if(!isset($handlers[$handlerName]))
		{
			$className = 'AmddDatabase'.$handlerName;
			if(!class_exists($className, false))
			{
				$path = dirname(__FILE__)."/{$handlerName}/db.{$handlerName}.php";
				if(!is_file($path))
					throw new AmddDatabaseException('File not found: '.$path, 1);
				require_once $path;
				if(!class_exists($className))
					throw new AmddDatabaseException('Class not found: '.$className, 1);
			}
			$handlers[$handlerName] = new $className;
		}

		return $handlers[$handlerName];
	}

	/**
	 * (re)create database tables
	 */
	public function createTables(){}

	/**
	 * Get device data from main table
	 * @param string $ua device User-Agent header
	 * @return string json-encoded device capabilities (null if not found)
	 */
	public function getDevice($ua){return null;}

	/**
	 * Get list of devices for group from main table
	 * @param string $group device group
	 * @return array list of objects with ua and data fields
	 */
	public function getDevices($group){return null;}

	/**
	 * Put device to main table
	 * @param string $group device group
	 * @param string $ua device User-Agent header
	 * @param string $data json-encoded device capabilities
	 */
	public function putDevice($group, $ua, $data){}

	/**
	 * Get device from cache table
	 * @param string $ua device User-Agent header
	 * @return string json-encoded device capabilities (null if not found)
	 */
	public function getDeviceFromCache($ua){return null;}

	/**
	 * Put device to cache table
	 * @param string $ua device User-Agent header
	 * @param string $data json-encoded device capabilities
	 * @param integer $limit cache size (0 = disabled, -1 = infinite)
	 */
	public function putDeviceToCache($ua, $data, $limit = 0){}

	/**
	 * Clear cache table
	 */
	public function clearCache(){}
}