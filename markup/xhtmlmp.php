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

class MobileJoomla_XHTMLMP extends MobileJoomla
{
	var $contenttype = null;

	function getMarkup()
	{
		return 'xhtml';
	}

	function getCharset()
	{
		return 'utf-8';
	}

	function getContentType()
	{
		$contenttype = 'text/html';
		if($this->config['tmpl_xhtml_contenttype'] == 0)
			$contenttype = $this->ContentType_Auto();
		else
		{
			$contenttypes = array (1 => 'application/vnd.wap.xhtml+xml', 'application/xhtml+xml', 'text/html', 'text/xhtml');
			$contenttype = $contenttypes[$this->config['tmpl_xhtml_contenttype']];
		}
		$this->contenttype = $contenttype;
		return $contenttype;
	}

	function getContentString()
	{
		if($this->contenttype == null) $this->getContentType();
		return $this->contenttype.'; charset=utf-8';
	}

	function showXMLheader()
	{
		if($this->config['tmpl_xhtml_xmlhead'])
			echo '<?xml version="1.0" encoding="utf-8" ?>'."\n";
	}

	function showDocType()
	{
		$doctypes = array (1 => '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD WML 2.0//EN" "http://www.wapforum.org/dtd/wml20.dtd">', '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">', '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.1//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile11.dtd">', '<!DOCTYPE html PUBLIC "-//OMA//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">', '<!DOCTYPE HTML SYSTEM "-//W3C//DTD HTML 4.0//EN" "html40-mobile.dtd">');
		if($this->config['tmpl_xhtml_doctype'])
			echo $doctypes[$this->config['tmpl_xhtml_doctype']]."\n";
	}

	function getXmlnsString()
	{
		if($this->config['tmpl_xhtml_xmlns'])
			return ' xmlns="http://www.w3.org/1999/xhtml"';
		return '';
	}

	function showHead($showstylesheet = true)
	{
		if($this->config['tmpl_xhtml_simplehead'])
			echo '<title>'.$this->getPageTitle().'</title>'."\n";
		else
			echo '<jdoc:include type="head" />';
		global $mainframe;
		$cur_template = $mainframe->getTemplate();
		if($showstylesheet && is_file(JPATH_SITE.DS.'templates'.DS.$cur_template.DS.'css'.DS.'template_css.css'))
		{
			if($this->config['tmpl_xhtml_embedcss'])
			{
				echo "<style>\n";
				@readfile(JPATH_SITE.DS.'templates'.DS.$cur_template.DS.'css'.DS.'template_css.css');
				echo "</style>\n";
			}
			else
				echo '<link href="'.JURI::base().'templates/'.$cur_template.'/css/template_css.css" rel="stylesheet" type="text/css" />'."\n";
		}
		if($this->config['tmpl_xhtml_allowextedit'])
		{
			/** @var JUser $user */
			$user =& JFactory::getUser();
			if(!$user->get('guest'))
			{
				/** @var JEditor $editor */
				$editor =& JFactory::getEditor();
				echo $editor->initialise();
			}
		}
	}

	function getPosition($pos)
	{
		if(!isset($this->config)) return '';
		switch($pos)
		{
			case 'header':
				return $this->config['tmpl_xhtml_header1'];
			case 'header2':
				return $this->config['tmpl_xhtml_header2'];
			case 'middle':
				return $this->config['tmpl_xhtml_middle1'];
			case 'middle2':
				return $this->config['tmpl_xhtml_middle2'];
			case 'footer':
				return $this->config['tmpl_xhtml_footer1'];
			case 'footer2':
				return $this->config['tmpl_xhtml_footer2'];
		}
		return '';
	}

	function loadModules($position)
	{
		echo '<jdoc:include type="modules" name="'.$position.'" style="xhtml_m" />';
	}

	function showPathway()
	{
		if($this->config['tmpl_xhtml_pathway'] && ((!$this->_ishomepage && JRequest::getCmd('view') != 'frontpage') || $this->config['tmpl_xhtml_pathwayhome']))
			echo '<jdoc:include type="module" name="breadcrumbs" style="xhtml_m" />';
	}

	function showMainBody()
	{
		echo '<jdoc:include type="message" />';
		if(!$this->_ishomepage || $this->config['tmpl_xhtml_componenthome'])
			echo '<jdoc:include type="component" />';
	}

	function showFooter()
	{
		global $mainframe;
		if($this->config['tmpl_xhtml_jfooter'])
		{
			/** @var JLanguage $lang */
			$lang =& JFactory::getLanguage();
			$lang->load('com_mobilejoomla');
			$version = new JVersion();
?>
<p class="jfooter">&copy; <?php echo JHTML::_('date', 'now', '%Y').' '.$mainframe->getCfg('sitename'); ?><br/><?php echo $version->URL; ?><br/><?php echo JText::_('Mobile version by');?> <a href="http://www.mobilejoomla.com/">Mobile Joomla!</a></p>
<?php
		}
	}

	function processPage($text)
	{
		if($this->config['tmpl_xhtml_img'] == 1)
			$text = preg_replace('#<img [^>]+>#is', '', $text);
		elseif($this->config['tmpl_xhtml_img'] >= 2)
		{
			$scaletype = $this->config['tmpl_xhtml_img']-2;
			$text = MobileJoomla::RescaleImages($text, $scaletype);
		}
		if($this->config['tmpl_xhtml_removetags'])
		{
			$text = preg_replace('#<iframe\s[^>]+? />#is', '', $text);
			$text = preg_replace('#<iframe.+?</iframe>#is', '', $text);
			$text = preg_replace('#<object\s[^>]+? />#is', '', $text);
			$text = preg_replace('#<object\s.+?</object>#is', '', $text);
			$text = preg_replace('#<embed\s[^>]+? />#is', '', $text);
			$text = preg_replace('#<embed.+</embed>#is', '', $text);
			$text = preg_replace('#<applet\s[^>]+? />#is', '', $text);
			$text = preg_replace('#<applet\s.+?</applet>#is', '', $text);
/*			$text = preg_replace('#(<.+?)align="center"(.+?>)#is', '\1class="center"\2', $text); // mosimage fix */
			$text = str_replace('<br>', '<br />', $text); // xml-compatibility
		}
		if($this->config['tmpl_xhtml_removescripts'])
		{
			$text = preg_replace('#<script\s[^>]+? />#is', '', $text);
			$text = preg_replace('#<script\s.+?</script>#is', '', $text);
		}
		if($this->config['tmpl_xhtml_entitydecode'])
		{
			$text = strtr($text, array ('&lt;' => '&amp;lt;', '&gt;' => '&amp;gt;', '&amp;' => '&amp;amp;'));
			$text = html_entity_decode($text, ENT_NOQUOTES, 'UTF-8');
		}

		//remove target="_blank" from links
		$text = preg_replace('#(<a [^>]*?)target="_blank"([^>]*?>)#is', '\1\2', $text);

		return $text;
	}

	function ContentType_Auto()
	{
		if($this->device['mimetype'])
			return $this->device['mimetype'];

		if(isset($_SERVER['HTTP_ACCEPT']))
		{
			$accept = array ('xhtml' => 'application/xhtml+xml', 'html' => 'text/html', 'wml' => 'text/vnd.wap.wml', 'mhtml' => 'application/vnd.wap.xhtml+xml');
			$c = array ();
			foreach($accept as $mime_lang => $mime_type)
			{
				$c[$mime_lang] = 1;
				if(stristr($_SERVER['HTTP_ACCEPT'], $mime_type))
				{
					$c[$mime_lang]++;
					if(preg_match('|'.str_replace(array ('/', '.', '+'), array ('\/', '\.', '\+'), $mime_type).';q=0(\.[1-9]+)|i', $_SERVER['HTTP_ACCEPT'], $matches))
						$c[$mime_lang] -= (float) $matches[1];
				}
			}
			arsort($c, SORT_NUMERIC);
			if(array_sum($c) == count($c))
			{
				unset($c);
				$c['html'] = 1;
			}
			$max = max($c);
			foreach($c as $type => $val)
				if($val != $max) unset($c[$type]);
			if(array_key_exists('xhtml', $c))
			{
				unset($c);
				$c['xhtml'] = 1;
			}
			elseif(array_key_exists('html', $c))
			{
				unset($c);
				$c['html'] = 1;
			}
			elseif(array_key_exists('wml', $c))
			{
				unset($c);
				$c['wml'] = 1;
			}
			elseif(array_key_exists('mhtml', $c))
			{
				unset($c);
				$c['mhtml'] = 1;
			}
			return $accept[key($c)];
		}
		return 'text/html';
	}
}
