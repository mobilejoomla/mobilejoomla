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

class MobileJoomla_IPHONE extends MobileJoomla
{
	function getMarkup()
	{
		return 'iphone';
	}

	function setHeader()
	{
	}

	function showHead()
	{
		/*$document =& JFactory::getDocument ();
$headerstuff = $document->getHeadData();
unset($headerstuff['scripts'][JURI::base(true).'/media/system/js/caption.js']);
unset($headerstuff['scripts'][JURI::base(true).'/media/system/js/mootools.js']);
$document->setHeadData($headerstuff);*/
		echo '<jdoc:include type="head" />';
		$mainframe =& JFactory::getApplication();
		$template = $mainframe->getTemplate();
		if(file_exists(JPATH_THEMES.DS.$template.DS.'apple-touch-icon.png'))
			echo '<link rel="apple-touch-icon" href="'.JURI::base(true).'/templates/'.$template.'/apple-touch-icon.png" />';
		$canonical = MobileJoomla::getCanonicalURI();
		if($canonical)
			echo '<link rel="canonical" href="'.$canonical.'">';
	}

	function showBreadcrumbs($style='iphone')
	{
		if($this->config['iphone.pathway'] && (!$this->_ishomepage || $this->config['iphone.pathwayhome']))
			echo '<jdoc:include type="module" name="breadcrumbs" style="'.$style.'" />';
	}

	function showComponent()
	{
		if(!$this->_ishomepage || $this->config['iphone.componenthome'])
			echo '<jdoc:include type="component" />';
	}

	function showFooter()
	{
		$mainframe =& JFactory::getApplication();
		if($this->config['iphone.jfooter'])
		{
			/** @var JLanguage $lang */
			$lang =& JFactory::getLanguage();
			$lang->load('com_mobilejoomla', JPATH_ADMINISTRATOR);
			$fyear = (substr(JVERSION,0,3) != '1.5') ? 'Y' : '%Y';
			$version = new JVersion();
?>
<p class="jfooter">&copy; <?php echo JHTML::_('date', 'now', $fyear).' '.$mainframe->getCfg('sitename'); ?><br><?php echo $version->URL; ?><br><?php echo JText::_('COM_MJ__MOBILE_VERSION_BY');?> <a href="http://www.mobilejoomla.com/">Mobile Joomla!</a></p>
<?php
		}
	}

	function processPage($text)
	{
		if($this->config['iphone.img'] == 1)
			$text = preg_replace('#<img [^>]+>#is', '', $text);
		elseif($this->config['iphone.img'] >= 2)
		{
			$scaletype = $this->config['iphone.img']-2;
			$addstyles = $this->config['iphone.img_addstyles'];
			$text = MobileJoomla::RescaleImages($text, $scaletype, $addstyles);
		}

		if($this->config['iphone.removetags'])
		{
			$text = preg_replace('#<object\s[^>]+?/>#is', '', $text);
			$text = preg_replace('#<object\s.+?</object>#is', '', $text);
			$text = preg_replace('#<embed\s[^>]+?/>#is', '', $text);
			$text = preg_replace('#<embed.+?</embed>#is', '', $text);
			$text = preg_replace('#<applet\s[^>]+?/>#is', '', $text);
			$text = preg_replace('#<applet\s.+?</applet>#is', '', $text);
		}

		//TODO: parse css-files
		return $text;
	}

	function loadModules($position, $style='iphone')
	{
		echo '<jdoc:include type="modules" name="'.$position.'" style="'.$style.'" />';
	}

	function getPosition($pos)
	{
		if(!isset($this->config)) return '';
		switch($pos)
		{
			case 'header':
				return $this->config['iphone.header1'];
			case 'header2':
				return $this->config['iphone.header2'];
			case 'header3':
				return $this->config['iphone.header3'];
			case 'middle':
				return $this->config['iphone.middle1'];
			case 'middle2':
				return $this->config['iphone.middle2'];
			case 'middle3':
				return $this->config['iphone.middle3'];
			case 'footer':
				return $this->config['iphone.footer1'];
			case 'footer2':
				return $this->config['iphone.footer2'];
			case 'footer3':
				return $this->config['iphone.footer3'];
		}
		return '';
	}

	function getAccessKey()
	{
		return false;
	}
}
