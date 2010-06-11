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
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

jimport('joomla.plugin.plugin');

class plgMobileAlways extends JPlugin
{
	function plgMobileAlways(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onDeviceDetection(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		$markup = $this->params->get('markup', '');
		if($markup)
			$MobileJoomla_Device['markup'] = $markup;
	}
}
