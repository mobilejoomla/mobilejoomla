<?php
/**
 * Advanced Mobile Device Detection
 *
 * @version		###VERSION###
 * @license		###LICENSE###
 * @copyright	###COPYRIGHT###
 * @date		###DATE###
 */

require_once dirname(__FILE__) . '/config.php';

class AmddDatabaseJoomla extends AmddDatabase
{
	private $table = null;
	private $tableCache = null;

	public function __construct()
	{
		$this->table = AmddDatabaseJoomlaConfig::$dbTableName;
		$this->tableCache = AmddDatabaseJoomlaConfig::$dbTableName.'_cache';
	}

	public function createTables()
	{
		$db = JFactory::getDBO();

		$query = "DROP TABLE IF EXISTS `{$this->table}`";
		$db->setQuery($query);
		$db->query();

		$query = "DROP TABLE IF EXISTS `{$this->tableCache}`";
		$db->setQuery($query);
		$db->query();

		$query = "CREATE TABLE `{$this->table}` ("
				."  `ua`    VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,"
				."  `group` VARCHAR(32)  CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,"
				."  `data`  VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,"
				."  UNIQUE(`ua`),"
				."  INDEX(`group`)"
				.") TYPE=MyISAM CHARACTER SET utf8 COLLATE utf8_bin";
		$db->setQuery($query);
		$db->query();

		$query = "CREATE TABLE `{$this->tableCache}` ("
				."  `ua`    VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,"
				."  `data`  VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,"
				."  `time`  INT UNSIGNED NOT NULL"
				.") TYPE=MyISAM CHARACTER SET utf8 COLLATE utf8_bin";
		$db->setQuery($query);
		$db->query();
	}

	public function getDevice($ua)
	{
		$db = JFactory::getDBO();

		$query = "SELECT `data` FROM `{$this->table}` WHERE `ua`=".$db->Quote($ua);
		$db->setQuery($query);
		return $db->loadResult();
	}

	public function getDevices($group)
	{
		$db = JFactory::getDBO();

		$query = "SELECT `ua`, `data` FROM `{$this->table}` WHERE `group`=".$db->Quote($group);
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getDeviceFromCache($ua)
	{
		$db = JFactory::getDBO();

		$query = "SELECT `data` FROM `{$this->tableCache}` WHERE `ua`=".$db->Quote($ua);
		$db->setQuery($query);
		$data = $db->loadResult();

		if($data !== null)
		{
			$query = "UPDATE `{$this->tableCache}` SET time=".time()." WHERE `ua`=".$db->Quote($ua);
			$db->setQuery($query);
			$db->query();
		}

		return $data;
	}

	public function putDeviceToCache($ua, $data, $limit = 0)
	{
		$db = JFactory::getDBO();

		if($limit >= 0)
		{
			$query = "SELECT COUNT(*) FROM `{$this->tableCache}`";
			$db->setQuery($query);
			$cacheSize = $db->loadResult();

			if($cacheSize>$limit)
			{
				$query = "DELETE FROM `{$this->tableCache}` WHERE time <="
						." (SELECT time FROM"
						."   (SELECT time FROM `{$this->tableCache}` ORDER BY time DESC LIMIT $limit, 1)"
						." foo)";
				$db->setQuery($query);
				$db->query();
			}
		}

		if($limit != 0)
		{
			$x_ua = $db->Quote($ua);
			$x_data = $db->Quote($data);
			$x_time = time();
			$query = "INSERT IGNORE INTO `{$this->tableCache}` (`ua`, `data`, `time`)"
					." VALUES ($x_ua, $x_data, $x_time)"
					." ON DUPLICATE KEY UPDATE `data`=$x_data, `time`=$x_time";
			$db->setQuery($query);
			$db->query();
		}
	}

	public function clearCache()
	{
		$db = JFactory::getDBO();

		$query = "DELETE FROM `{$this->tableCache}`";
		$db->setQuery($query);
		$db->query();
	}
}