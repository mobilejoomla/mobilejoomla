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

class MobileJoomla
{
	var $config = null;
	var $device = null;
	var $_ishomepage = false;

	function &getConfig()
	{
		static $instance;
		if(!is_array($instance))
		{
			$config = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php';
			$MobileJoomla_Settings = array ();
			include($config);
			$instance = &$MobileJoomla_Settings;
		}
		return $instance;
	}

	function &getDevice()
	{
		static $instance;
		if(!is_array($instance))
		{
			$instance = array ('markup' => false, 'screenwidth' => 0, 'screenheight' => 0, 'imageformats' => null, 'mimetype' => null, 'param' => array ());
		}
		return $instance;
	}

	function &getInstance($markup = '')
	{
		static $instance;
		if(!is_object($instance))
		{
			if($markup == '')
				return null;
			$class = 'MobileJoomla_'.strtoupper($markup);
			if(!class_exists($class))
			{
				$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'markup'.DS.$markup.'.php';
				require_once($path);
				if(!class_exists($class))
					JError::raiseError(500, 'Class not found: '.$class);
			}
			$instance = new $class;
			$instance->config =& MobileJoomla::getConfig();
			$instance->device =& MobileJoomla::getDevice();
		}
		return $instance;
	}

	function getMarkup()
	{
		return '';
	}

	function isHome()
	{
		return $this->_ishomepage;
	}

	function setHome($ishome)
	{
		$this->_ishomepage = $ishome;
	}

	function getCharset()
	{
		return 'utf-8';
	}

	function getContentType()
	{
		return 'text/html';
	}

	function getContentString()
	{
		return $this->getContentType().'; charset=utf-8';
	}

	function setHeader()
	{
		JResponse::setHeader('Content-type', $this->getContentString(), true);
	}

	function showXMLheader()
	{
		echo '<?xml version="1.0" encoding="utf-8" ?>'."\n";
	}

	function showDocType()
	{
	}

	function getXmlnsString()
	{
		return '';
	}

	function getPageTitle()
	{
		/** @var JDocument $document */
		$document =& JFactory::getDocument();
		return $document->getTitle();
	}

	function showHead($showstylesheet = true)
	{
		echo '<jdoc:include type="head" />';
	}

	function showPathway()
	{
		echo '<jdoc:include type="module" name="breadcrumbs" style="-1" />';
	}

	function showMainBody()
	{
		echo '<jdoc:include type="component" />';
	}

	function showFooter()
	{
	}

	function processPage($text)
	{
		return $text;
	}

	function getPosition($pos)
	{
		return '';
	}

	function loadModules($position)
	{
		echo '<jdoc:include type="modules" name="'.$position.'" />';
	}

	function loadModulesAsCards($position)
	{
	}

	function RescaleImages($text, $scaletype)
	{
		require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'imagerescaler.class.php');
		return ImageRescaler::RescaleImages($text, $scaletype);
	}
}
