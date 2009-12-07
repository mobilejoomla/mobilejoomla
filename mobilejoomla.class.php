<?php
/**
 * Kuneri Mobile Joomla! for Joomla!1.5
 * http://www.mobilejoomla.com/
 *
 * @version	0.9.0
 * @license	http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	Copyright (C) 2008-2009 Kuneri Ltd. All rights reserved.
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class MobileJoomla
{
	var $config = null;
	var $_ishomepage = false;
	function &getInstance($markup='',$config=null)
	{
		static $instance;
		if(!is_object($instance))
		{
			$class='MobileJoomla_'.strtoupper($markup);
			if(!class_exists($class) || !$config)
				return null;
			$instance = new $class;
			$instance->config = $config;
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
		JResponse::setHeader('Content-type',$this->getContentString(),true);
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
		$document =& JFactory::getDocument();
		return $document->getTitle();
	}
	function showHead($showstylesheet=true)
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
}

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
		$contenttype='text/html';
		if($this->config['tmpl_xhtml_contenttype']==0)
			$contenttype=$this->ContentType_Auto();
		else
		{
			$contenttypes=array(1=>'application/vnd.wap.xhtml+xml','application/xhtml+xml','text/html','text/xhtml');
			$contenttype=$contenttypes[$this->config['tmpl_xhtml_contenttype']];
		}
		$this->contenttype=$contenttype;
		return $contenttype;
	}
	function getContentString()
	{
		if($this->contenttype==null) $this->getContentType();
		return $this->contenttype.'; charset=utf-8';
	}
	function showXMLheader()
	{
		if( $this->config['tmpl_xhtml_xmlhead'] )
			echo '<?xml version="1.0" encoding="utf-8" ?>'."\n";
	}
	function showDocType()
	{
		$doctypes=array(
		1=>'<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD WML 2.0//EN" "http://www.wapforum.org/dtd/wml20.dtd">'
		  ,'<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">'
		  ,'<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.1//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile11.dtd">'
		  ,'<!DOCTYPE html PUBLIC "-//OMA//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">'
		  ,'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">'
		  ,'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">'
		  ,'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
		  ,'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
		  ,'<!DOCTYPE HTML SYSTEM "-//W3C//DTD HTML 4.0//EN" "html40-mobile.dtd">'
		);
		if($this->config['tmpl_xhtml_doctype'])
			echo $doctypes[$this->config['tmpl_xhtml_doctype']]."\n";
	}
	function getXmlnsString()
	{
		if($this->config['tmpl_xhtml_xmlns'])
			return ' xmlns="http://www.w3.org/1999/xhtml"';
		return '';
	}
	function showHead($showstylesheet=true)
	{
		if($this->config['tmpl_xhtml_simplehead'])
			echo '<title>'. $this->getPageTitle() .'</title>'."\n";
		else
			echo '<jdoc:include type="head" />';
		global $mainframe;
		$cur_template=$mainframe->getTemplate();
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
			$user =& JFactory::getUser();
			if(!$user->get('guest'))
			{
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
		case 'header': return $this->config['tmpl_xhtml_header1'];
		case 'header2': return $this->config['tmpl_xhtml_header2'];
		case 'middle': return $this->config['tmpl_xhtml_middle1'];
		case 'middle2': return $this->config['tmpl_xhtml_middle2'];
		case 'footer': return $this->config['tmpl_xhtml_footer1'];
		case 'footer2': return $this->config['tmpl_xhtml_footer2'];
		}
		return '';
	}
	function loadModules($position)
	{
		echo '<jdoc:include type="modules" name="'.$position.'" style="xhtml_m" />';
	}
	function showPathway()
	{
		if($this->config['tmpl_xhtml_pathway'] && ((!$this->_ishomepage && JRequest::getCmd( 'view' ) != 'frontpage') || $this->config['tmpl_xhtml_pathwayhome']))
			echo '<jdoc:include type="module" name="breadcrumbs" style="xhtml_m" />';
	}
	function showMainBody()
	{
		echo '<jdoc:include type="message" />';
		if( !$this->_ishomepage || $this->config['tmpl_xhtml_componenthome'] )
			echo '<jdoc:include type="component" />';
	}
	function showFooter()
	{
		global $mainframe;
		if($this->config['tmpl_xhtml_jfooter'])
		{
			$version = new JVersion();
?>
<p class="jfooter">
&copy; <?php echo JHTML::_('date', 'now', '%Y').' '.$mainframe->getCfg('sitename'); ?><br />
<?php echo $version->URL; ?><br />
Mobile version by <a href="http://www.mobilejoomla.com/">Kuneri Mobile Joomla!</a>
</p>
<?php
		}
	}
	function processPage($text)
	{
		if($this->config['tmpl_xhtml_img']==1 )
			$text = preg_replace( '#<img [^>]+>#is', '', $text );
		elseif($this->config['tmpl_xhtml_img']>=2 )
		{
			require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'wurfl'.DS.'imageadaptation.php');
			$scaletype=$this->config['tmpl_xhtml_img']-2;
			$text = RescaleImages($text,$scaletype);
		}
		if($this->config['tmpl_xhtml_removetags']) {
			$text = preg_replace( '#<iframe\s[^>]+? />#is',  '', $text );
			$text = preg_replace( '#<iframe.+?</iframe>#is', '', $text );
			$text = preg_replace( '#<object\s[^>]+? />#is',  '', $text );
			$text = preg_replace( '#<object\s.+?</object>#is','',$text );
			$text = preg_replace( '#<embed\s[^>]+? />#is',   '', $text );
			$text = preg_replace( '#<embed.+</embed>#is',   '', $text );
			$text = preg_replace( '#<applet\s[^>]+? />#is',  '', $text );
			$text = preg_replace( '#<applet\s.+?</applet>#is','',$text );
			$text = preg_replace( '#(<.+?)align="center"(.+?>)#is', '$1class="center"$2', $text ); // mosimage fix
			$text = str_replace(  '<br>',					'<br />', $text ); // xml-compatibility
		}
		if($this->config['tmpl_xhtml_removescripts']) {
            $text = preg_replace( '#<script\s[^>]+? />#is',  '', $text );
			$text = preg_replace( '#<script\s.+?</script>#is','',$text );
		}
		if($this->config['tmpl_xhtml_entitydecode'])
		{
			$text=strtr($text,array('&lt;'=>'&amp;lt;','&gt;'=>'&amp;gt;','&amp;'=>'&amp;amp;'));
			$text=html_entity_decode($text,ENT_NOQUOTES,'UTF-8');
		}
		//remove target="_blank" from links
		$text=preg_replace( '#(<a [^>]*?)target="_blank"([^>]*?>)#is', '\1\2', $text );
		//allow caching
		JResponse::allowCache(true);
		JResponse::setHeader('Cache-Control','no-transform');
		return $text;
	}
	function ContentType_Auto()
	{
		$accept = array(
			'xhtml' => 'application/xhtml+xml',
			'html'  => 'text/html',
			'wml'   => 'text/vnd.wap.wml',  
			'mhtml' => 'application/vnd.wap.xhtml+xml');
		if(isset($GLOBALS['mobilemime']))
			return $accept[$GLOBALS['mobilemime']];
		elseif(isset($_SERVER['HTTP_ACCEPT']))
		{
			$c=array();
			foreach($accept as $mime_lang=>$mime_type)
			{
				$c[$mime_lang]=1;
				if(stristr($_SERVER['HTTP_ACCEPT'],$mime_type))
				{
					$c[$mime_lang]++;
					if(preg_match('|'.str_replace(array('/','.','+'),array('\/','\.','\+'),$mime_type).'(, [^;]+?)?;q=0(\.[1-9]+)|i',$_SERVER['HTTP_ACCEPT'],$matches))
						$c[$mime_lang]-=(float)$matches[1];
				}
			}
			arsort($c,SORT_NUMERIC);
			if(array_sum($c)==count($c))
			{
				unset($c);
				$c['html']=1;
			}
			$max=max($c);
			foreach($c as $type=>$val)
				if($val!=$max) unset($c[$type]);
			if(strpos($_SERVER['HTTP_USER_AGENT'],'Profile/MIDP')) //for mobile devices fist check for xhtml mime
			{
				if(array_key_exists('xhtml',$c))	return $accept['xhtml'];
				if(array_key_exists('html',$c))		return $accept['html'];
			}
			else
			{
				if(array_key_exists('html',$c))		return $accept['html'];
				if(array_key_exists('xhtml',$c))	return $accept['xhtml'];
			}
			if(array_key_exists('wml',$c))		return $accept['wml'];
			if(array_key_exists('mhtml',$c))	return $accept['mhtml'];
		}
		return 'text/html';
	}
}



class MobileJoomla_WML extends MobileJoomla
{
	function getMarkup()
	{
		return 'wml';
	}
	function getCharset()
	{
		return 'utf-8';
	}
	function getContentType()
	{
		return 'text/vnd.wap.wml';
	}
	function getContentString()
	{
		return 'text/vnd.wap.wml; charset=utf-8';
	}
	function showXMLheader()
	{
	}
	function showDocType()
	{
	}
	function getXmlnsString()
	{
		return '';
	}
	function showHead($showstylesheet=true)
	{
	}
	function getPosition($pos)
	{
		if(!isset($this->config)) return '';
		switch($pos)
		{
		case 'header': return $this->config['tmpl_wap_header'];
		case 'middle': return $this->config['tmpl_wap_middle'];
		case 'footer': return $this->config['tmpl_wap_footer'];
		case 'cards': return $this->config['tmpl_wap_cards'];
		}
		return '';
	}
	function loadModules($position)
	{
		echo '<jdoc:include type="modules" name="'.$position.'" style="wml" />';
	}
	function loadModulesAsCards($position)
	{
		echo '<jdoc:include type="modules" name="'.$position.'" style="wmlcards" />';
	}
	function showPathway()
	{
		if($this->config['tmpl_wap_pathway'] &&
			( !$this->_ishomepage || $this->config['tmpl_wap_pathwayhome'] ) )
			echo '<jdoc:include type="module" name="breadcrumbs" style="wml" />';
	}
	function showMainBody()
	{
		echo '<jdoc:include type="message" />';
		if( !$this->_ishomepage || $this->config['tmpl_wap_componenthome'] )
			echo '<jdoc:include type="component" />';
	}
	function showFooter()
	{
		global $mainframe;
		if($this->config['tmpl_wap_jfooter'])
		{
			$version = new JVersion();
?>
<p><small>
&copy; <?php echo JHTML::_('date', 'now', '%Y').' '.$mainframe->getCfg('sitename'); ?><br/>
<?php echo $version->URL; ?><br/>
<?php echo JText::_('Mobile version by');?> <a href="http://www.mobilejoomla.com/">Kuneri Mobile Joomla!</a>
</small></p>
<?php
		}
	}
	function processPage($text)
	{
		$doctypes=array(
			1=>'<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">'
			  ,'<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.2//EN" "http://www.wapforum.org/DTD/wml_1.2.xml">'
		);
		$pretext = '<?xml version="1.0" encoding="utf-8" ?>'."\n"
				 . $doctypes[$this->config['tmpl_wap_doctype']]."\n";

		$text = preg_replace( '#<img src="[^"]*arrow(_rtl)?\.png" alt=""\s*/>#', '&gt;', $text ); //pathway fix
		$text = preg_replace( '#<iframe\s[^>]+? />#is',		'', $text );
		$text = preg_replace( '#<iframe.+</iframe>#is',		'', $text );
		$text = preg_replace( '#<object\s[^>]+? />#is',		'', $text );
		$text = preg_replace( '#<object\s.+?</object>#is',	'', $text );
		$text = preg_replace( '#<embed\s[^>]+? />#is',		'', $text );
		$text = preg_replace( '#<embed.+</embed>#is',		'', $text );
		$text = preg_replace( '#<applet\s[^>]+? />#is',		'', $text );
		$text = preg_replace( '#<applet\s.+?</applet>#is',	'', $text );
		$text = preg_replace( '#<script\s[^>]+? />#is',		'', $text );
		$text = preg_replace( '#<script\s.+?</script>#is',	'', $text );
		$text = preg_replace( '#<h(.*?)>#is',				'<big>', $text);
		$text = preg_replace( '#</h(.*?)>#is',				'</big><br/>', $text);
		$text = preg_replace( '#<(ol|ul|dl|div|table)(.*?)>#i','', $text);
		$text = preg_replace( '#</(ol|ul|dl|table)>#i',		'', $text);
		$text = preg_replace( '#</div>#i',					'<br/>', $text);
		$text = preg_replace( '#<(td|tr|dd|li|span)(.*?)>#is',	'', $text);
		$text = preg_replace( '#</(tr|dd|li)>#i',			'<br/>', $text);
		$text = str_ireplace( '</td>',						' | ', $text);
		$text = str_ireplace( '</span>',					'', $text);
		$text = str_replace(  ' | <br/>',					'<br/>', $text);
		$text = preg_replace( '#<dt(.*?)>#is',				'<strong>', $text);
		$text = str_ireplace( '</dt>',						'</strong><br/>', $text);
		$text = preg_replace( '# class="(.*?)"#is',			'', $text);
		$text = preg_replace( '# rel="(.*?)"#is',			'', $text);
		$text = preg_replace( '# id="(.*?)"#is',			'', $text);
		$text = preg_replace( '# style="(.*?)"#is',			'', $text);
		$text = preg_replace( '# title="(.*?)"#is',			'', $text);
		$text = trim($text);

		$title = $this->getPageTitle();
		$pos = strpos($text,'<card');
		if($pos===false) // there is no card tag
		{
			$text = '<card id="main" title="' . $title . '">' . "\n"
				  . $text
				  . "\n</card>\n";
		}
		else
		{
			$text = '<card id="main" title="' . $title . '">' . "\n"
				  . substr($text,0,$pos)
				  . "\n</card>\n"
				  . substr($text,$pos);
		}
		
		$text = "<wml>\n"
			. "<head>\n"
			. "<meta http-equiv=\"Cache-Control\" content=\"max-age=0\" forua=\"true\" />\n"
			. "</head>\n"
			. $text."\n"
			. "</wml>";

		if($this->config['tmpl_wap_img']==1 )
			$text = preg_replace( '#<img [^>]+>#is', '', $text );
		elseif($this->config['tmpl_wap_img']>=2 )
		{
			require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'wurfl'.DS.'imageadaptation.php');
			$scaletype=$this->config['tmpl_wap_img']-2;
			$text = RescaleImages($text,$scaletype);
		}

		$text = str_replace( '<br/>', '<br>', $text );
		$text = strip_tags($text,'<a><access><anchor><b><big><br><card><do><em><fieldset><go><head><i><img><input><meta><noop><onevent><optgroup><option><p><postfield><prev><refresh><select><setvar><small><strong><table><td><tr><template><timer><u><wml>');
		$text = str_replace( '<br />', '<br/>', $text );
		$text = str_replace( '<br>', '<br/>', $text );
		$text = preg_replace( '#\s\s+#', ' ', $text );
		$text = preg_replace( "#(\n|\r)+#", "\n", $text );

		if($this->config['tmpl_wap_entitydecode']==1)
		{
			$text=strtr($text,array('&lt;'=>'&amp;lt;','&gt;'=>'&amp;gt;','&amp;'=>'&amp;amp;'));
			$text=html_entity_decode($text,ENT_NOQUOTES,'UTF-8');
		}

		return $pretext.$text;
	}
}



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
	function showHead($showstylesheet=true)
	{
		echo '<title>'. $this->getPageTitle() ."</title>\n";
	}
	function showPathway()
	{
		if($this->config['tmpl_imode_pathway'] &&
			( !$this->_ishomepage || $this->config['tmpl_imode_pathwayhome'] ) )
			echo '<jdoc:include type="module" name="breadcrumbs" style="chtml" />';
	}
	function showMainBody()
	{
		echo '<jdoc:include type="message" />';
		if( !$this->_ishomepage || $this->config['tmpl_imode_componenthome'] )
			echo '<jdoc:include type="component" />';
	}
	function showFooter()
	{
		global $mainframe;
		if($this->config['tmpl_imode_jfooter'])
		{
			$version = new JVersion();
?>
<p class="jfooter">
&copy; <?php echo JHTML::_('date', 'now', '%Y').' '.$mainframe->getCfg('sitename'); ?><br>
<?php echo $version->URL; ?><br>
<?php echo JText::_('Mobile version by');?> <a href="http://www.mobilejoomla.com/">Kuneri Mobile Joomla!</a>
</p>
<?php
		}
	}
	function getPosition($pos)
	{
		if(!isset($this->config)) return '';
		switch($pos)
		{
		case 'header': return $this->config['tmpl_imode_header1'];
		case 'header2': return $this->config['tmpl_imode_header2'];
		case 'middle': return $this->config['tmpl_imode_middle1'];
		case 'middle2': return $this->config['tmpl_imode_middle2'];
		case 'footer': return $this->config['tmpl_imode_footer1'];
		case 'footer2': return $this->config['tmpl_imode_footer2'];
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
		$text = preg_replace( '#<([^>]) ?/>#s', '<\1>', $text );
// TODO: remove table
// TODO: remove colors
// TODO: remove stylesheet
		if($this->config['tmpl_imode_img']==1 )
			$text = preg_replace( '#<img [^>]+>#is', '', $text );
		elseif($this->config['tmpl_imode_img']>=2 )
		{
			require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'wurfl'.DS.'imageadaptation.php');
			// convert to gif only
			$scaletype=$this->config['tmpl_imode_img']-2;
			$text = RescaleImages($text,$scaletype);
		}
		// allowable tags: a base blockquote body br center dd dir div dl dt form head h... hr html img input(exept type=image&file) li menu meta(refresh only) ol option(selected, but not value) p plaintext pre select textarea title ul
		if($this->config['tmpl_imode_removetags']) {
			$text = preg_replace( '#<iframe\s[^>]+ ?/>#is',  '', $text );
			$text = preg_replace( '#<iframe.+</iframe>#is', '', $text );
			$text = preg_replace( '#<object\s[^>]+ ?/>#is',  '', $text );
			$text = preg_replace( '#<object\s.+</object>#is','',$text );
			$text = preg_replace( '#<embed\s[^>]+ ?/>#is',   '', $text );
			$text = preg_replace( '#<embed.+</embed>#is',   '', $text );
			$text = preg_replace( '#<applet\s[^>]+ ?/>#is',  '', $text );
			$text = preg_replace( '#<applet\s.+</applet>#is','',$text );
			$text = preg_replace( '#<script\s[^>]+ ?/>#is',  '', $text );
			$text = preg_replace( '#<script\s.+</script>#is','',$text );
		}
		if($this->config['tmpl_imode_entitydecode'])
		{
			$text=strtr($text,array('&lt;'=>'&amp;lt;','&gt;'=>'&amp;gt;','&amp;'=>'&amp;amp;'));
			$text=html_entity_decode($text,ENT_NOQUOTES,'UTF-8');
		}
		return $text;
	}
}

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
	function showHead($showstylesheet=true)
	{
		$document =& JFactory::getDocument ();
		$headerstuff = $document->getHeadData();
		unset($headerstuff['scripts'][JURI::base(true).'/media/system/js/mootools.js']);
		unset($headerstuff['scripts'][JURI::base(true).'/media/system/js/caption.js']);
		$document->setHeadData($headerstuff);
        echo '<jdoc:include type="head" />';
	}
	function showPathway()
	{
		if($this->config['tmpl_iphone_pathway'] &&
			( !$this->_ishomepage || $this->config['tmpl_iphone_pathwayhome'] ) )
			echo '<jdoc:include type="module" name="breadcrumbs" />';
	}
	function showMainBody()
	{
		echo '<jdoc:include type="message" />';
		if( !$this->_ishomepage || $this->config['tmpl_iphone_componenthome'] )
			echo '<jdoc:include type="component" />';
	}
	function showFooter()
	{
		global $mainframe;
		if($this->config['tmpl_iphone_jfooter'])
		{
			$version = new JVersion();
?>
<p class="jfooter">
&copy; <?php echo JHTML::_('date', 'now', '%Y').' '.$mainframe->getCfg('sitename'); ?><br>
<?php echo $version->URL; ?><br>
<?php echo JText::_('Mobile version by');?> <a href="http://www.mobilejoomla.com/">Kuneri Mobile Joomla!</a>
</p>
<?php
		}
	}
	function processPage($text)
	{
		if($this->config['tmpl_iphone_img']==1 )
			$text=preg_replace( '#<img [^>]+>#is', '', $text );
		elseif($this->config['tmpl_iphone_img']>=2 )
		{
			require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'wurfl'.DS.'imageadaptation.php');
			$scaletype=$this->config['tmpl_iphone_img']-2;
			$text=RescaleImages($text,$scaletype);
		}
		//TODO: parse css-files
		return $text;
	}
	function getPosition($pos)
	{
		if(!isset($this->config)) return '';
		switch($pos)
		{
		case 'header': return $this->config['tmpl_iphone_header1'];
		case 'header2': return $this->config['tmpl_iphone_header2'];
		case 'middle': return $this->config['tmpl_iphone_middle1'];
		case 'middle2': return $this->config['tmpl_iphone_middle2'];
		case 'footer': return $this->config['tmpl_iphone_footer1'];
		case 'footer2': return $this->config['tmpl_iphone_footer2'];
		}
		return '';
	}
}

?>