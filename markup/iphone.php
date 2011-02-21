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

	//	function showXMLheader()
	//	{
	//	}
	//	function showDocType()
	//	{
	//	}
	function showHead($showstylesheet = true)
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
	}

	function showBreadcrumbs()
	{
		if($this->config['tmpl_iphone_pathway'] && (!$this->_ishomepage || $this->config['tmpl_iphone_pathwayhome']))
			echo '<jdoc:include type="module" name="breadcrumbs" style="iphone" />';
	}

	function showComponent()
	{
		if(!$this->_ishomepage || $this->config['tmpl_iphone_componenthome'])
			echo '<jdoc:include type="component" />';
	}

	function showFooter()
	{
		$mainframe =& JFactory::getApplication();
		if($this->config['tmpl_iphone_jfooter'])
		{
			/** @var JLanguage $lang */
			$lang =& JFactory::getLanguage();
			$lang->load('com_mobilejoomla', JPATH_ADMINISTRATOR);
			$version = new JVersion();
			$fyear = (substr($version->getShortVersion(),0,3) == '1.6') ? 'Y' : '%Y';
?>
<p class="jfooter">&copy; <?php echo JHTML::_('date', 'now', $fyear).' '.$mainframe->getCfg('sitename'); ?><br><?php echo $version->URL; ?><br><?php echo JText::_('COM_MJ__MOBILE_VERSION_BY');?> <a href="http://www.mobilejoomla.com/">Mobile Joomla!</a></p>
<?php
		}
	}

	function processPage($text)
	{
		if($this->config['tmpl_iphone_img'] == 1)
			$text = preg_replace('#<img [^>]+>#is', '', $text);
		elseif($this->config['tmpl_iphone_img'] >= 2)
		{
			$scaletype = $this->config['tmpl_iphone_img']-2;
			$addstyles = $this->config['tmpl_iphone_img_addstyles'];
			$text = MobileJoomla::RescaleImages($text, $scaletype, $addstyles);
		}

		if($this->config['tmpl_iphone_removetags'])
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

	function loadModules($position)
	{
		echo '<jdoc:include type="modules" name="'.$position.'" style="iphone" />';
	}

	function getPosition($pos)
	{
		if(!isset($this->config)) return '';
		switch($pos)
		{
			case 'header':
				return $this->config['tmpl_iphone_header1'];
			case 'header2':
				return $this->config['tmpl_iphone_header2'];
			case 'header3':
				return $this->config['tmpl_iphone_header3'];
			case 'middle':
				return $this->config['tmpl_iphone_middle1'];
			case 'middle2':
				return $this->config['tmpl_iphone_middle2'];
			case 'middle3':
				return $this->config['tmpl_iphone_middle3'];
			case 'footer':
				return $this->config['tmpl_iphone_footer1'];
			case 'footer2':
				return $this->config['tmpl_iphone_footer2'];
			case 'footer3':
				return $this->config['tmpl_iphone_footer3'];
		}
		return '';
	}
}
