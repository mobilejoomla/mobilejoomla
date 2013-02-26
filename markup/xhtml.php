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

class MobileJoomla_XHTML extends MobileJoomla
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
		if($this->config['xhtml.contenttype'] == 0)
			$contenttype = $this->ContentType_Auto();
		else
		{
			$contenttypes = array (1 => 'application/vnd.wap.xhtml+xml',
										'application/xhtml+xml',
										'text/html',
										'text/xhtml');
			$contenttype = $contenttypes[$this->config['xhtml.contenttype']];
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
		if($this->config['xhtml.xmlhead'])
			echo '<?xml version="1.0" encoding="utf-8" ?>'."\n";
	}

	function showDocType()
	{
		$doctypes = array (1 => '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD WML 2.0//EN" "http://www.wapforum.org/dtd/wml20.dtd">',
								'<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">',
								'<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.1//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile11.dtd">',
								'<!DOCTYPE html PUBLIC "-//OMA//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">',
								'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">',
								'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">',
								'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
								'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
								'<!DOCTYPE HTML SYSTEM "-//W3C//DTD HTML 4.0//EN" "html40-mobile.dtd">');
		if($this->config['xhtml.doctype'])
			echo $doctypes[$this->config['xhtml.doctype']]."\n";
	}

	function getXmlnsString()
	{
		$str = '';
		if($this->config['xhtml.xmlns'])
			$str .= ' xmlns="http://www.w3.org/1999/xhtml"';
		$str .= parent::getXmlnsString();
		return $str;
	}

	function showHead()
	{
		if($this->config['xhtml.simplehead'])
			echo '<title>'.$this->getPageTitle().'</title>'."\n";
		else
			echo '<jdoc:include type="head" />';
		if($this->config['xhtml.allowextedit'])
		{
			$user = JFactory::getUser();
			if(!$user->get('guest') && version_compare(JVERSION, '3.0', '<'))
			{
				$editor = JFactory::getEditor();
				echo $editor->initialise();
			}
		}
		$canonical = MobileJoomla::getCanonicalURI();
		if($canonical)
			echo '<link rel="canonical" href="'.$canonical.'">';
	}

	function loadModules($position, $style='xhtml_m')
	{
		echo '<jdoc:include type="modules" name="'.$position.'" style="'.$style.'" />';
	}

	function showFooter()
	{
		if($this->getParam('jfooter'))
		{
			$app = JFactory::getApplication();
			$lang = JFactory::getLanguage();
			$lang->load('com_mobilejoomla', JPATH_ADMINISTRATOR);
			$fyear = (substr(JVERSION,0,3) != '1.5') ? 'Y' : '%Y';
			$version = new JVersion();
?>
<p class="jfooter">&copy; <?php echo JHTML::_('date', 'now', $fyear).' '.$app->getCfg('sitename'); ?><br/><?php echo $version->URL; ?><br/><?php echo JText::_('COM_MJ__MOBILE_VERSION_BY');?> <a href="http://www.mobilejoomla.com/">Mobile Joomla!</a></p>
<?php
		}
	}

	function processPage($text)
	{
		if($this->getParam('img') == 1)
			$text = preg_replace('#<img [^>]+>#is', '', $text);
		elseif($this->getParam('img') >= 2)
		{
			$scaletype = $this->getParam('img')-2;
			$addstyles = (bool)$this->getParam('img_addstyles');
			$text = MobileJoomla::RescaleImages($text, $scaletype, $addstyles);
		}
		if($this->getParam('removetags'))
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
		if($this->config['xhtml.removescripts'])
		{
			$text = preg_replace('#<script\s[^>]+? />#is', '', $text);
			//$text = preg_replace('#<script\s.+?</script>#is', '', $text);
			$text = preg_replace('#<script([^\'"/>]|"[^"]*?"|\'[^\']*?\')*?>([^\'"/]|"([^"\\\\]|\\\\.)*?"|\'([^\'\\\\]|\\\\.)*?\'|/[^/*]|/\*.*?\*/|//.*?$)*?</script>#ism', '', $text);
		}
		if($this->config['xhtml.entitydecode'])
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

		if(!isset($_SERVER['HTTP_ACCEPT']))
			return 'text/html';

		$accept = array('xhtml' => 'application/xhtml+xml',
						'html' => 'text/html',
						'wml' => 'text/vnd.wap.wml',
						'mhtml' => 'application/vnd.wap.xhtml+xml');
		$c = array ();
		foreach($accept as $mime_markup => $mime_type)
		{
			if(stristr($_SERVER['HTTP_ACCEPT'], $mime_type))
			{
				if(preg_match('|'.str_replace(array('/','.','+'), array('\/','\.','\+'), $mime_type).';q=(0\.\d+)|i', $_SERVER['HTTP_ACCEPT'], $matches))
					$c[$mime_markup] = (float) $matches[1];
				else
					$c[$mime_markup] = 1;
			}
			else
				$c[$mime_markup] = 0;
		}
		$max = max($c);
		foreach($c as $mime_markup=>$val)
			if($val!=$max)
				unset($c[$mime_markup]);
		$mime = 'html';
		if(array_key_exists('html', $c))
		{
			if(strpos(@$_SERVER['HTTP_USER_AGENT'], 'Profile/MIDP-2.0 Configuration/CLDC-1.1') && array_key_exists('xhtml', $c))
				$mime = 'xhtml';
			else
				$mime = 'html';
		}
		elseif(array_key_exists('xhtml', $c))
			$mime = 'xhtml';
		elseif(array_key_exists('mhtml', $c))
			$mime = 'mhtml';
		elseif(array_key_exists('wml', $c))
			$mime = 'wml';
		return $accept[$mime];
	}
}
