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

class MobileJoomla_CHTML extends MobileJoomla
{
	function getMarkup()
	{
		return 'chtml';
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
		return 'text/html; charset=utf-8';
	}

	function showXMLheader()
	{
	}

	function showDocType()
	{
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD Compact HTML 1.0 Draft//EN">';
	}

	function getXmlnsString()
	{
		return '';
	}

	function showHead()
	{
		echo '<title>'.$this->getPageTitle()."</title>\n";
		$canonical = MobileJoomla::getCanonicalURI();
		if($canonical)
			echo '<link rel="canonical" href="'.$canonical.'">';
	}

	function showFooter()
	{
		if($this->getParam('jfooter'))
		{
			$app = JFactory::getApplication();
			/** @var JLanguage $lang */
			$lang = JFactory::getLanguage();
			$lang->load('com_mobilejoomla', JPATH_ADMINISTRATOR);
			$fyear = (substr(JVERSION,0,3) != '1.5') ? 'Y' : '%Y';
			$version = new JVersion();
?>
<p class="jfooter">&copy; <?php echo JHTML::_('date', 'now', $fyear).' '.$app->getCfg('sitename'); ?><br><?php echo $version->URL; ?><br><?php echo JText::_('COM_MJ__MOBILE_VERSION_BY');?> <a href="http://www.mobilejoomla.com/">Mobile Joomla!</a></p>
<?php
		}
	}

	function loadModules($position, $style='chtml')
	{
		echo '<jdoc:include type="modules" name="'.$position.'" style="'.$style.'" />';
	}

	function processPage($text)
	{
		//replace '<.../>' on '<...>'
		$text = preg_replace('#<([^>]) ?/>#s', '<\1>', $text);
		// TODO: remove table
		// TODO: remove colors
		// TODO: remove stylesheet
		if($this->getParam('img') == 1)
			$text = preg_replace('#<img [^>]+>#is', '', $text);
		elseif($this->getParam('img') >= 2)
		{
			$scaletype = $this->getParam('img')-2;
			$text = MobileJoomla::RescaleImages($text, $scaletype);
		}
		// allowable tags: a base blockquote body br center dd dir div dl dt form head h... hr html img input(exept type=image&file) li menu meta(refresh only) ol option(selected, but not value) p plaintext pre select textarea title ul
		if($this->getParam('removetags'))
		{
			$text = preg_replace('#<iframe\s[^>]+ ?/>#is', '', $text);
			$text = preg_replace('#<iframe.+</iframe>#is', '', $text);
			$text = preg_replace('#<object\s[^>]+ ?/>#is', '', $text);
			$text = preg_replace('#<object\s.+</object>#is', '', $text);
			$text = preg_replace('#<embed\s[^>]+ ?/>#is', '', $text);
			$text = preg_replace('#<embed.+</embed>#is', '', $text);
			$text = preg_replace('#<applet\s[^>]+ ?/>#is', '', $text);
			$text = preg_replace('#<applet\s.+</applet>#is', '', $text);
			$text = preg_replace('#<script\s[^>]+ ?/>#is', '', $text);
			//$text = preg_replace('#<script\s.+</script>#is', '', $text);
			$text = preg_replace('#<script([^\'"/>]|"[^"]*?"|\'[^\']*?\')*?>([^\'"/]|"([^"\\\\]|\\\\.)*?"|\'([^\'\\\\]|\\\\.)*?\'|/[^/*]|/\*.*?\*/|//.*?$)*?</script>#ism', '', $text);
		}
		if($this->config['chtml.entitydecode'])
		{
			$text = strtr($text, array ('&lt;' => '&amp;lt;', '&gt;' => '&amp;gt;', '&amp;' => '&amp;amp;'));
			$text = html_entity_decode($text, ENT_NOQUOTES, 'UTF-8');
		}

		return $text;
	}
}
