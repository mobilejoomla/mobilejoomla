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
			$config = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php';
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
			$instance = array ('markup' => false, 'real_markup' => false, 'screenwidth' => 0, 'screenheight' => 0, 'imageformats' => null, 'mimetype' => null, 'param' => array ());
		}
		return $instance;
	}

	function &getInstance($markup = '')
	{
		static $instance;
		if(!is_object($instance))
		{
			if($markup == '')
			{
				$nullvar = null;
				return $nullvar;
			}
			$class = 'MobileJoomla_'.strtoupper($markup);
			if(!class_exists($class))
			{
				$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'markup'.DS.$markup.'.php';
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
		JResponse::setHeader('Content-Type', $this->getContentString(), true);
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

	function showHead()
	{
		echo '<jdoc:include type="head" />';
	}

	/** deprecated **/
	function showPathway()
	{
		$this->showBreadcrumbs();
	}
	function showBreadcrumbs($style='-1')
	{
		echo '<jdoc:include type="module" name="breadcrumbs" style="'.$style.'" />';
	}

	/** deprecated **/
	function showMainBody()
	{
		$this->showComponent();
	}
	function showComponent()
	{
		echo '<jdoc:include type="component" />';
	}

	function showMessage()
	{
		echo '<jdoc:include type="message" />';
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

	function RescaleImages($text, $scaletype, $addstyles = false)
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'imagerescaler.class.php');
		return ImageRescaler::RescaleImages($text, $scaletype, $addstyles);
	}

	function isCurrentMarkup($markup)
	{
		$MobileJoomla_Device =& MobileJoomla::getDevice();
		if($markup=='auto')
			$markup = $MobileJoomla_Device['real_markup'];
		elseif($markup=='desktop')
			$markup = '';
		elseif($markup=='mobile')
			$markup = $MobileJoomla_Device['real_markup']=='' ? 'xhtml' : $MobileJoomla_Device['real_markup'];
		return $markup === $MobileJoomla_Device['markup'];
	}

	function getDeviceViewURI($device)
	{
		$MobileJoomla_Settings =& MobileJoomla::getConfig();
		$MobileJoomla_Device =& MobileJoomla::getDevice();

		$uri = clone(JFactory::getURI());
		if($uri->getVar('format')=='html')
			$uri->delVar('format');
		$uri->delVar('device');

		$desktop_uri = new JURI($MobileJoomla_Settings['desktop_url']);
		$uri->setHost($desktop_uri->getHost());

		if($device=='mobile')
			$device = $MobileJoomla_Device['real_markup']=='' ? 'xhtml' : $MobileJoomla_Device['real_markup'];

		if($device=='auto')
			$device = $MobileJoomla_Device['real_markup']=='' ? 'desktop' : $MobileJoomla_Device['real_markup'];

		if($MobileJoomla_Settings['domains'] == '1')
		{
			switch($device)
			{
			case 'xhtml':
				if($MobileJoomla_Settings['xhtmldomain'] && $MobileJoomla_Settings['xhtmlredirect'])
					$uri->setHost($MobileJoomla_Settings['xhtmldomain']);
				break;
			case 'wml':
				if($MobileJoomla_Settings['wapdomain'] && $MobileJoomla_Settings['wapredirect'])
					$uri->setHost($MobileJoomla_Settings['wapdomain']);
				break;
			case 'chtml':
				if($MobileJoomla_Settings['imodedomain'] && $MobileJoomla_Settings['imoderedirect'])
					$uri->setHost($MobileJoomla_Settings['imodedomain']);
				break;
			case 'iphone':
				if($MobileJoomla_Settings['iphonedomain'] && $MobileJoomla_Settings['iphoneredirect'])
					$uri->setHost($MobileJoomla_Settings['iphonedomain']);
				break;
			case 'desktop':
				break;
			default:
				$device = false;
			}
		}
		else
		{
			switch($device)
			{
			case 'xhtml':
			case 'wml':
			case 'chtml':
			case 'iphone':
			case 'desktop':
				break;
			default:
				$device = false;
			}
		}

		if($device !== false)
			$uri->setVar('device', $device);

		return $uri->toString();
	}

	function getCanonicalURI()
	{
		$MobileJoomla_Device =& MobileJoomla::getDevice();
		if($MobileJoomla_Device['markup'] == $MobileJoomla_Device['default_markup'])
			return false;

		$MobeJoomla_Settings =& MobileJoomla::getConfig();
		$desktop_uri = new JURI($MobileJoomla_Settings['desktop_url']);

		$uri = clone(JFactory::getURI());
		$uri->delVar('device');
		$uri->delVar('format');
		$uri->setHost($desktop_uri->getHost());

		return $uri->toString();
	}

	function getAccessKey()
	{
		static $last_keynum = 0;
		if($last_keynum>=10)
			return false;
		$last_keynum++;
		return $last_keynum==10 ? '0' : $last_keynum;
	}
}
