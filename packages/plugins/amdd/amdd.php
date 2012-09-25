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

jimport('joomla.plugin.plugin');

class plgMobileAmdd extends JPlugin
{
	function plgMobileAmdd(& $subject, $config)
	{
		parent::__construct($subject, $config);
		if(!isset($this->params))
			$this->params = new JParameter(null);
	}

	function onDeviceDetection(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		require_once(dirname(__FILE__).'/amdd/config.php');

		$cache     = (bool)$this->params->get('cache', 1);
		$cachesize = (int) $this->params->get('cachesize', 1000);
		AmddConfig::$cacheSize = $cache ? $cachesize : 0;

		require_once(dirname(__FILE__).'/amdd/amdd.php');

		try
		{
			$amddObj = Amdd::getCapabilities();
			if(!is_object($amddObj))
				return;
		}
		catch(AmddDatabaseException $e)
		{
			error_log("Caught exception 'Exception' with message '".$e->getMessage()."' in ".$e->getFile().':'.$e->getLine());
			return;
		}

		$MobileJoomla_Device['amdd'] = $amddObj;
		$MobileJoomla_Device['markup'] = $amddObj->markup;
		if(isset($amddObj->screenWidth))
			$MobileJoomla_Device['screenwidth'] = $amddObj->screenWidth;
		if(isset($amddObj->screenHeight))
			$MobileJoomla_Device['screenheight'] = $amddObj->screenHeight;
		if(isset($amddObj->imageFormats))
			$MobileJoomla_Device['imageformats'] = $amddObj->imageFormats;
	}

	function onGetDatabaseSize()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$db->setQuery('SHOW TABLE STATUS FROM `'.$app->getCfg('db').'` LIKE '.$db->Quote($app->getCfg('dbprefix').'mj_amdd%'));
		$result = $db->loadObjectList();

		$size = 0;
		foreach($result as $row)
			$size += $row->Data_length;

		$date = null;
		$xml = simplexml_load_file(dirname(__FILE__).'/amdd.xml');
		if(isset($xml->creationdate))
			$date = (string)$xml->creationdate;

		return $size ? array('Mobile - AMDD', $size, $date) : null;
	}
}
