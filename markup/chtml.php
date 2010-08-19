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

	function showHead($showstylesheet = true)
	{
		echo '<title>'.$this->getPageTitle()."</title>\n";
	}

	function showPathway()
	{
		if($this->config['tmpl_imode_pathway'] && (!$this->_ishomepage || $this->config['tmpl_imode_pathwayhome']))
			echo '<jdoc:include type="module" name="breadcrumbs" style="chtml" />';
	}

	function showMainBody()
	{
		echo '<jdoc:include type="message" />';
		if(!$this->_ishomepage || $this->config['tmpl_imode_componenthome'])
			echo '<jdoc:include type="component" />';
	}

	function showFooter()
	{
		global $mainframe;
		if($this->config['tmpl_imode_jfooter'])
		{
			/** @var JLanguage $lang */
			$lang =& JFactory::getLanguage();
			$lang->load('com_mobilejoomla');
			$version = new JVersion();
?>
<p class="jfooter">&copy; <?php echo JHTML::_('date', 'now', '%Y').' '.$mainframe->getCfg('sitename'); ?><br><?php echo $version->URL; ?><br><?php echo JText::_('Mobile version by');?> <a href="http://www.mobilejoomla.com/">Mobile Joomla!</a></p>
<?php
		}
	}

	function getPosition($pos)
	{
		if(!isset($this->config)) return '';
		switch($pos)
		{
			case 'header':
				return $this->config['tmpl_imode_header1'];
			case 'header2':
				return $this->config['tmpl_imode_header2'];
			case 'middle':
				return $this->config['tmpl_imode_middle1'];
			case 'middle2':
				return $this->config['tmpl_imode_middle2'];
			case 'footer':
				return $this->config['tmpl_imode_footer1'];
			case 'footer2':
				return $this->config['tmpl_imode_footer2'];
		}
		return '';
	}

	function loadModules($position)
	{
		echo '<jdoc:include type="modules" name="'.$position.'" style="chtml" />';
	}

	function processPage($text)
	{
		//replace '<.../>' on '<...>'
		$text = preg_replace('#<([^>]) ?/>#s', '<\1>', $text);
		// TODO: remove table
		// TODO: remove colors
		// TODO: remove stylesheet
		if($this->config['tmpl_imode_img'] == 1)
			$text = preg_replace('#<img [^>]+>#is', '', $text);
		elseif($this->config['tmpl_imode_img'] >= 2)
		{
			$scaletype = $this->config['tmpl_imode_img']-2;
			$text = MobileJoomla::RescaleImages($text, $scaletype);
		}
		// allowable tags: a base blockquote body br center dd dir div dl dt form head h... hr html img input(exept type=image&file) li menu meta(refresh only) ol option(selected, but not value) p plaintext pre select textarea title ul
		if($this->config['tmpl_imode_removetags'])
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
			$text = preg_replace('#<script\s.+</script>#is', '', $text);
		}
		if($this->config['tmpl_imode_entitydecode'])
		{
			$text = strtr($text, array ('&lt;' => '&amp;lt;', '&gt;' => '&amp;gt;', '&amp;' => '&amp;amp;'));
			$text = html_entity_decode($text, ENT_NOQUOTES, 'UTF-8');
		}

		return $text;
	}
}
