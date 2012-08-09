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
require_once dirname(__FILE__) . '/ua.php';
require_once dirname(__FILE__) . '/database/database.php';

class Amdd
{
    /**
	 * Status of last device detecting:
	 * 0 no detection
	 * 1 isDesktop
	 * 2 exact match
	 * 3 from cache
	 * 4 prefix match
	 * 5 levenshtein match
	 * 6 unknown device
	 * @var int
	 */
	public static $matchType = 0;

	/**
	 * Get capabilities for given UA
	 * @static
	 * @param string $ua User-Agent (will be auto-detected if it's null)
	 * @param bool $exact
	 * @return stdClass
	 */
	public static function getCapabilities($ua = null, $exact = false)
	{
		self::$matchType = 0;

		if($ua === null)
			$ua = AmddUA::getUserAgentFromRequest();

		$ua = AmddUA::normalize($ua);

		self::$matchType = 1;
		if(AmddUA::isDesktop($ua))
			return self::makeDesktop();

		$ua = substr($ua, 0, 255);
		$data = self::getDevice($ua, $exact);

		if($data !== null)
			$data = @json_decode($data);

		if($data === null)
		{
			self::$matchType = 6;
			$data = self::makeDesktop();
		}

		return $data;
	}

	private static function makeDesktop()
	{
		$data = new stdClass;
		$data->type = 'desktop';
		$data->markup = '';
		//$data->screenWidth = 0;
		//$data->screenHeight = 0;
		//$data->imageFormats = array('gif', 'jpg', 'png');
		return $data;
	}

	private static function getDevice($ua, $exact = false)
	{
		$db = AmddDatabase::getInstance(AmddConfig::$dbHandlerName);

		self::$matchType = 2;
		// load matched device
		$data = $db->getDevice($ua);
		if($data !== null)
			return $data;

		if($exact) return null;

		// load device from cache
		if(AmddConfig::$cacheSize != 0)
		{
			self::$matchType = 3;
			$data = $db->getDeviceFromCache($ua);
			if($data !== null)
				return empty($data) ? null : $data;
		}

		// find closest device
		$group = AmddUA::getGroup($ua);
		$devices = $db->getDevices($group);

		self::$matchType = 4;
		$data = self::findByPrefix($ua, $devices);
		if($data !== null)
		{
			self::$matchType = 5;
			$data = self::findByLevenshtein($ua, $devices);
		}

		// save to cache
		$db->putDeviceToCache($ua, empty($data)?'':$data, AmddConfig::$cacheSize);

		return $data;
	}

	private static function findByPrefix($ua, $devices)
	{
		$ua_size = strlen($ua);

		$data = null;

		if(!preg_match('#^(Mozilla|Opera|NetFront)/#', $ua))
		{
			preg_match('#^(DoCoMo|portalmmm)/.*?(\(|$)#', $ua, $match)
			or preg_match('#^.{5,}?[ /]#', $ua, $match);

			$best = isset($match[0]) ? strlen($match[0]) : $ua_size;

			foreach($devices as $device)
			{
				$dev_ua = $device->ua;
				if(substr($ua, 0, $best) != substr($dev_ua, 0, $best))
					continue;

				$min_size = min($ua_size, strlen($dev_ua));
				for($i = $best; $i < $min_size; $i++)
				{
					if($ua{$i} == $dev_ua{$i})
						$best++;
				}
				$data = $device->data;
			}
		}

		return $data;
	}

	private static function findByLevenshtein($ua, $devices)
	{
		$ua_size = strlen($ua);

		$data = null;

		$best = min(12, intval($ua_size/2)); // maximum number of changes in UA string
		foreach($devices as $device)
		{
			if(abs(strlen($device->ua) - $ua_size) > $best)
				continue;

			$current = levenshtein($ua, $device->ua);
			if($current <= $best)
			{
				$best = $current;
				$data = $device->data;
			}
		}

		return $data;
	}
}