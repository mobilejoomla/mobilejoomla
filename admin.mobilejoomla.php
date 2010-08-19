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

require_once(JPATH_COMPONENT.DS.'admin.mobilejoomla.html.php');

$task = JRequest::getCmd('task');
global $mainframe;

switch($task)
{
	case 'save':
		saveconfig($task);
		break;
	case 'cancel':
		$mainframe->redirect('index2.php');
		break;
	case 'about':
		showabout();
		break;
	case 'extensions':
		showextensions();
		break;
	default:
		showconfig();
		break;
}

function selectArray(&$arr, $tag_name, $tag_attribs, $key, $text, $selected = NULL)
{
	reset($arr);
	$html = "<select name=\"$tag_name\" $tag_attribs>";
	$count = count($arr);
	for($i = 0; $i < $count; $i++)
	{
		$k = $arr[$i][$key];
		$extra = ($k == $selected ? " selected=\"selected\"" : '');
		$html .= "<option value=\"".$k."\"$extra>".$arr[$i][$text]."</option>";
	}
	$html .= "</select>";
	return $html;
}

function showconfig()
{
	/** @var array $MobileJoomla_Settings */
	include(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php');

	/** @var JDatabase $db */
	$db =& JFactory::getDBO();
	$query = 'SELECT DISTINCT(position) FROM #__modules WHERE client_id = 0';
	$db->setQuery($query);
	$positions = $db->loadResultArray();
	$positions = (is_array($positions)) ? $positions : array ();

	$templateBaseDir = JPATH_SITE.DS.'templates'.DS;
	$templates = array ();
	$templates[] = array ('value' => '');

	jimport('joomla.filesystem.folder');
	$templateDirs = JFolder::folders($templateBaseDir);
	foreach($templateDirs as $templateDir)
	{
		$templateFile = $templateBaseDir.$templateDir.DS.'templateDetails.xml';
		if(!is_file($templateFile))
			continue;
		$xml = JApplicationHelper::parseXMLInstallFile($templateFile);
		if($xml['type'] != 'template')
			continue;
		$templates[] = array ('value' => $templateDir);

		$xml =& JFactory::getXMLParser('Simple');
		if($xml->loadFile($templateFile))
		{
			$p =& $xml->document->getElementByPath('positions');
			if(is_a($p, 'JSimpleXMLElement') && count($p->children()))
				foreach($p->children() as $child)
					$positions[] = $child->data();
		}
	}
	$positions[] = '';
	$positions = array_unique($positions);
	sort($positions);

	$modulepositions = array ();
	foreach($positions as $position)
		$modulepositions[] = array ('value' => $position);

	$lists = array ();

	//Plugin settings
	$caching = array (JHTML::_('select.option', 0, 'Disable'),
					  JHTML::_('select.option', 1, 'Global'));
	$lists['caching'] = JHTML::_('select.radiolist', $caching, 'mjconfig_caching', 'class="inputbox"', 'value', 'text', $MobileJoomla_Settings['caching']);

	$lists['domains'] = JHTML::_('select.booleanlist', 'mjconfig_domains', 'class="inputbox"', $MobileJoomla_Settings['domains']);

	//XHTML/WAP2.0 devices
	$lists['xhtmltemplate'] = selectArray($templates, 'mjconfig_xhtmltemplate', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['xhtmltemplate']);

	$gzip = array (JHTML::_('select.option', 0, 'No'),
	               JHTML::_('select.option', 1, 'Auto'));
	$lists['xhtmlgzip'] = JHTML::_('select.radiolist', $gzip, 'mjconfig_xhtmlgzip', 'class="inputbox"', 'value', 'text', $MobileJoomla_Settings['xhtmlgzip']);

	$lists['xhtmlredirect'] = JHTML::_('select.booleanlist', 'mjconfig_xhtmlredirect', 'class="inputbox"', $MobileJoomla_Settings['xhtmlredirect']);

	//WAP devices
	$lists['waptemplate'] = selectArray($templates, 'mjconfig_waptemplate', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['waptemplate']);

	$lists['wapgzip'] = JHTML::_('select.radiolist', $gzip, 'mjconfig_wapgzip', 'class="inputbox"', 'value', 'text', $MobileJoomla_Settings['wapgzip'], 'value', 'text');

	$lists['wapredirect'] = JHTML::_('select.booleanlist', 'mjconfig_wapredirect', 'class="inputbox"', $MobileJoomla_Settings['wapredirect']);

	//IMODE devices
	$lists['imodetemplate'] = selectArray($templates, 'mjconfig_imodetemplate', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['imodetemplate']);

	$lists['imodegzip'] = JHTML::_('select.radiolist', $gzip, 'mjconfig_imodegzip', 'class="inputbox"', 'value', 'text', $MobileJoomla_Settings['imodegzip']);

	$lists['imoderedirect'] = JHTML::_('select.booleanlist', 'mjconfig_imoderedirect', 'class="inputbox"', $MobileJoomla_Settings['imoderedirect']);

	//iPhone/iPod devices
	$lists['iphonetemplate'] = selectArray($templates, 'mjconfig_iphonetemplate', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['iphonetemplate']);

	$lists['iphonegzip'] = JHTML::_('select.radiolist', $gzip, 'mjconfig_iphonegzip', 'class="inputbox"', 'value', 'text', $MobileJoomla_Settings['iphonegzip']);

	$lists['iphoneredirect'] = JHTML::_('select.booleanlist', 'mjconfig_iphoneredirect', 'class="inputbox"', $MobileJoomla_Settings['iphoneredirect']);

	//mobile_pda template setting
	$lists['tmpl_xhtml_header1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_header1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_header1']);
	$lists['tmpl_xhtml_header2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_header2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_header2']);

	$lists['tmpl_xhtml_pathway'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_pathway', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_pathway']);

	$lists['tmpl_xhtml_pathwayhome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_pathwayhome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_pathwayhome']);

	$lists['tmpl_xhtml_middle1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_middle1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_middle1']);
	$lists['tmpl_xhtml_middle2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_middle2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_middle2']);

	$lists['tmpl_xhtml_componenthome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_componenthome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_componenthome']);

	$lists['tmpl_xhtml_footer1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_footer1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_footer1']);
	$lists['tmpl_xhtml_footer2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_footer2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_footer2']);

	$lists['tmpl_xhtml_jfooter'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_jfooter', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_jfooter']);

	$simplehead = array (JHTML::_('select.option', 0, JText::_('Standard Joomla')),
	                     JHTML::_('select.option', 1, JText::_('Simplified (title only)')));
	$lists['tmpl_xhtml_simplehead'] = JHTML::_('select.genericlist', $simplehead, 'mjconfig_tmpl_xhtml_simplehead', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_xhtml_simplehead']);

	$lists['tmpl_xhtml_allowextedit'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_allowextedit', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_allowextedit']);

	$lists['tmpl_xhtml_removetags'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_removetags', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_removetags']);

	$lists['tmpl_xhtml_removescripts'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_removescripts', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_removescripts']);

	$img = array (JHTML::_('select.option', 0, JText::_('Don\'t rescale')),
	              JHTML::_('select.option', 1, JText::_('Remove all')),
	              JHTML::_('select.option', 2, JText::_('Rescale if bigger')),
	              JHTML::_('select.option', 3, JText::_('Fixed rescale ratio')));
	$lists['tmpl_xhtml_img'] = JHTML::_('select.genericlist', $img, 'mjconfig_tmpl_xhtml_img', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_xhtml_img']);

	$lists['tmpl_xhtml_entitydecode'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_entitydecode', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_entitydecode']);

	$lists['tmpl_xhtml_embedcss'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_embedcss', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_embedcss']);

	$contenttype = array (JHTML::_('select.option', 0, 'auto'),
	                      JHTML::_('select.option', 1, 'application/vnd.wap.xhtml+xml'),
	                      JHTML::_('select.option', 2, 'application/xhtml+xml'),
	                      JHTML::_('select.option', 3, 'text/html'),
	                      JHTML::_('select.option', 4, 'text/xhtml'));
	$lists['tmpl_xhtml_contenttype'] = JHTML::_('select.genericlist', $contenttype, 'mjconfig_tmpl_xhtml_contenttype', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_xhtml_contenttype']);

	$lists['tmpl_xhtml_xmlhead'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_xmlhead', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_xmlhead']);

	$xhtmldoctype = array (JHTML::_('select.option', 0, JText::_('No')),
	                       JHTML::_('select.option', 1, 'WAPFORUM/WML2.0'),
	                       JHTML::_('select.option', 2, 'WAPFORUM/XHTML Mobile 1.0'),
	                       JHTML::_('select.option', 3, 'WAPFORUM/XHTML Mobile 1.1'),
	                       JHTML::_('select.option', 4, 'OMA/XHTML Mobile 1.2'),
	                       JHTML::_('select.option', 5, 'W3C/XHTML Basic 1.0'),
	                       JHTML::_('select.option', 6, 'W3C/XHTML Basic 1.1'),
	                       JHTML::_('select.option', 7, 'W3C/XHTML 1.0 Transitional'),
	                       JHTML::_('select.option', 8, 'W3C/XHTML 1.0 Strict'),
	                       JHTML::_('select.option', 9, 'W3C/HTML 4.0 Mobile'));
	$lists['tmpl_xhtml_doctype'] = JHTML::_('select.genericlist', $xhtmldoctype, 'mjconfig_tmpl_xhtml_doctype', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_xhtml_doctype']);

	$lists['tmpl_xhtml_xmlns'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_xmlns', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_xmlns']);

	//mobile_wap template setting
	$lists['tmpl_wap_header'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_wap_header', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_wap_header']);

	$lists['tmpl_wap_pathway'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_wap_pathway', 'class="inputbox"', $MobileJoomla_Settings['tmpl_wap_pathway']);

	$lists['tmpl_wap_pathwayhome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_wap_pathwayhome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_wap_pathwayhome']);

	$lists['tmpl_wap_middle'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_wap_middle', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_wap_middle']);

	$lists['tmpl_wap_componenthome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_wap_componenthome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_wap_componenthome']);

	$lists['tmpl_wap_footer'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_wap_footer', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_wap_footer']);

	$lists['tmpl_wap_jfooter'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_wap_jfooter', 'class="inputbox"', $MobileJoomla_Settings['tmpl_wap_jfooter']);

	$lists['tmpl_wap_cards'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_wap_cards', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_wap_cards']);

	$lists['tmpl_wap_removetags'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_wap_removetags', 'class="inputbox"', $MobileJoomla_Settings['tmpl_wap_removetags']);

	$lists['tmpl_wap_img'] = JHTML::_('select.genericlist', $img, 'mjconfig_tmpl_wap_img', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_wap_img']);

	$lists['tmpl_wap_entitydecode'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_wap_entitydecode', 'class="inputbox"', $MobileJoomla_Settings['tmpl_wap_entitydecode']);

	$wapdoctype = array (JHTML::_('select.option', 0, JText::_('No')),
	                     JHTML::_('select.option', 1, 'WAPFORUM/WML1.1'),
	                     JHTML::_('select.option', 2, 'WAPFORUM/WML1.2'));
	$lists['tmpl_wap_doctype'] = JHTML::_('select.genericlist', $wapdoctype, 'mjconfig_tmpl_wap_doctype', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_wap_doctype']);

	//mobile_imode template setting
	$lists['tmpl_imode_header1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_header1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_header1']);
	$lists['tmpl_imode_header2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_header2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_header2']);

	$lists['tmpl_imode_pathway'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_imode_pathway', 'class="inputbox"', $MobileJoomla_Settings['tmpl_imode_pathway']);

	$lists['tmpl_imode_pathwayhome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_imode_pathwayhome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_imode_pathwayhome']);

	$lists['tmpl_imode_middle1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_middle1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_middle1']);
	$lists['tmpl_imode_middle2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_middle2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_middle2']);

	$lists['tmpl_imode_componenthome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_imode_componenthome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_imode_componenthome']);

	$lists['tmpl_imode_footer1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_footer1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_footer1']);
	$lists['tmpl_imode_footer2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_footer2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_footer2']);

	$lists['tmpl_imode_jfooter'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_imode_jfooter', 'class="inputbox"', $MobileJoomla_Settings['tmpl_imode_jfooter']);

	$lists['tmpl_imode_removetags'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_imode_removetags', 'class="inputbox"', $MobileJoomla_Settings['tmpl_imode_removetags']);

	$lists['tmpl_imode_img'] = JHTML::_('select.genericlist', $img, 'mjconfig_tmpl_imode_img', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_imode_img']);

	$lists['tmpl_imode_entitydecode'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_imode_entitydecode', 'class="inputbox"', $MobileJoomla_Settings['tmpl_imode_entitydecode']);

	$lists['tmpl_imode_doctype'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_imode_doctype', 'class="inputbox"', $MobileJoomla_Settings['tmpl_imode_doctype']);

	//mobile_iphone template setting
	$lists['tmpl_iphone_header1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_header1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_header1']);
	$lists['tmpl_iphone_header2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_header2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_header2']);

	$lists['tmpl_iphone_pathway'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_iphone_pathway', 'class="inputbox"', $MobileJoomla_Settings['tmpl_iphone_pathway']);

	$lists['tmpl_iphone_pathwayhome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_iphone_pathwayhome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_iphone_pathwayhome']);

	$lists['tmpl_iphone_middle1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_middle1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_middle1']);
	$lists['tmpl_iphone_middle2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_middle2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_middle2']);

	$lists['tmpl_iphone_componenthome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_iphone_componenthome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_iphone_componenthome']);

	$lists['tmpl_iphone_footer1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_footer1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_footer1']);
	$lists['tmpl_iphone_footer2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_footer2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_footer2']);

	$lists['tmpl_iphone_jfooter'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_iphone_jfooter', 'class="inputbox"', $MobileJoomla_Settings['tmpl_iphone_jfooter']);

	$lists['tmpl_iphone_img'] = JHTML::_('select.genericlist', $img, 'mjconfig_tmpl_iphone_img', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_iphone_img']);

	$lists['tmpl_iphone_removetags'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_iphone_removetags', 'class="inputbox"', $MobileJoomla_Settings['tmpl_iphone_removetags']);

	$lists['tmpl_iphone_removetags'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_iphone_removetags', 'class="inputbox"', $MobileJoomla_Settings['tmpl_iphone_removetags']);

	function menuoptions()
	{
		$db =& JFactory::getDBO();
		$query = 'SELECT id, menutype, name, link, type, parent FROM #__menu WHERE published=1 ORDER BY menutype, parent, ordering';
		$db->setQuery($query);
		$mitems = $db->loadObjectList();
		$children = array();
		foreach($mitems as $v)
		{
			$pt = $v->parent;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}
		$list = array();
		$id = intval($mitems[0]->parent);
		if(@$children[$id])
			TreeRecurse($id, '', $list, $children);
		$mitems = array();
		$lastMenuType = null;
		foreach($list as $list_a)
		{
			if($list_a->menutype != $lastMenuType)
			{
				if($lastMenuType)
					$mitems[] = JHTML::_('select.option', '</OPTGROUP>' );
				$mitems[] = JHTML::_('select.option', '<OPTGROUP>', $list_a->menutype);
				$lastMenuType = $list_a->menutype;
			}
			if($list_a->type == 'component')
				$link = $list_a->link.'&Itemid='.$list_a->id;
			else
				$link = '-';
			$mitems[] = JHTML::_('select.option', $link, $list_a->treename, 'value', 'text', $link=='-');
		}
		if($lastMenuType !== null)
			$mitems[] = JHTML::_('select.option', '</OPTGROUP>');
		return $mitems;
	}
	function TreeRecurse($id, $indent, &$list, &$children, $level=0)
	{
		foreach($children[$id] as $v)
		{
			$id = $v->id;
			$list[$id] = $v;
			$list[$id]->treename = $indent.$v->name;
			if(@$children[$id] && $level<=99)
				TreeRecurse($id, $indent.'&nbsp;&nbsp;', $list, $children, $level+1);
		}
	}
	$lists['menuoptions'] = menuoptions();

	HTML_mobilejoomla::showconfig($lists, $MobileJoomla_Settings);
}

function saveExtensionsConfig()
{
	global $mainframe;

	$content = file_get_contents(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'extensions'.DS.'extensions.json');

	$json = json_decode($content);

	foreach($json->extensions as $extension)
	{
		$content = file_get_contents(JPATH_SITE.DS.$extension->configPath);
		$config = json_decode($content);

		$newconfig = array ();

		foreach($config as $key => $val)
		{
			$req = JRequest::getVar($extension->name.'_'.$key, NULL);

			if(is_array($req))
				$req = implode(',', $req);

			if(empty ($req))
				$req = '';

			$newconfig[$key] = $req;
		}

		file_put_contents(JPATH_SITE.DS.$extension->configPath, json_encode($newconfig));
	}

	$mainframe->redirect('index2.php?option=com_mobilejoomla&task=extensions',
	                     JText::_('The Configuration Details have been updated'));
}

function saveconfig($task)
{
	if(JRequest::getVar('ext', false))
	{
		saveExtensionsConfig();

		return;
	}

	$configfname = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php';
	include($configfname);

	$settings = array ('caching', 'domains', 'pcpage', 'templatewidth', 'jpegquality', 'desktop_url',
	                   'xhtmltemplate', 'xhtmlhomepage', 'xhtmlgzip', 'xhtmldomain', 'xhtmlredirect', 'xhtml_buffer_width',
	                   'waptemplate', 'waphomepage', 'wapgzip', 'wapdomain', 'wapredirect', 'wml_buffer_width',
	                   'imodetemplate', 'imodehomepage', 'imodegzip', 'imodedomain', 'imoderedirect', 'chtml_buffer_width',
	                   'iphonetemplate', 'iphonehomepage', 'iphonegzip', 'iphonedomain', 'iphoneredirect', 'iphone_buffer_width',
	                   'tmpl_xhtml_header1', 'tmpl_xhtml_header2', 'tmpl_xhtml_pathway', 'tmpl_xhtml_pathwayhome',
	                   'tmpl_xhtml_middle1', 'tmpl_xhtml_middle2', 'tmpl_xhtml_componenthome', 'tmpl_xhtml_footer1',
	                   'tmpl_xhtml_footer2', 'tmpl_xhtml_jfooter', 'tmpl_xhtml_simplehead', 'tmpl_xhtml_allowextedit',
	                   'tmpl_xhtml_removetags', 'tmpl_xhtml_removescripts', 'tmpl_xhtml_img', 'tmpl_xhtml_entitydecode',
	                   'tmpl_xhtml_embedcss', 'tmpl_xhtml_contenttype', 'tmpl_xhtml_xmlhead', 'tmpl_xhtml_doctype',
	                   'tmpl_xhtml_xmlns',
	                   'tmpl_wap_header', 'tmpl_wap_pathway', 'tmpl_wap_pathwayhome', 'tmpl_wap_middle',
	                   'tmpl_wap_componenthome', 'tmpl_wap_footer', 'tmpl_wap_cards', 'tmpl_wap_jfooter',
	                   'tmpl_wap_removetags', 'tmpl_wap_img', 'tmpl_wap_entitydecode', 'tmpl_wap_doctype',
	                   'tmpl_imode_header1', 'tmpl_imode_header2', 'tmpl_imode_pathway', 'tmpl_imode_pathwayhome',
	                   'tmpl_imode_middle1', 'tmpl_imode_middle2', 'tmpl_imode_componenthome', 'tmpl_imode_footer1',
	                   'tmpl_imode_footer2', 'tmpl_imode_jfooter', 'tmpl_imode_removetags', 'tmpl_imode_img',
	                   'tmpl_imode_entitydecode', 'tmpl_imode_doctype',
	                   'tmpl_iphone_header1', 'tmpl_iphone_header2', 'tmpl_iphone_pathway', 'tmpl_iphone_pathwayhome',
	                   'tmpl_iphone_middle1', 'tmpl_iphone_middle2', 'tmpl_iphone_componenthome', 'tmpl_iphone_footer1',
	                   'tmpl_iphone_footer2', 'tmpl_iphone_jfooter', 'tmpl_iphone_img', 'tmpl_iphone_removetags');
	$params = array ();
	foreach($settings as $param)
	{
		if(isset($_POST['mjconfig_'.$param]))
		{
			if(!get_magic_quotes_gpc())
				$_POST['mjconfig_'.$param] = addslashes($_POST['mjconfig_'.$param]);
			$MobileJoomla_Settings[$param] = $_POST['mjconfig_'.$param];
		}
		if(is_numeric($MobileJoomla_Settings[$param]))
			$params[] = "'$param'=>".$MobileJoomla_Settings[$param];
		else
			$params[] = "'$param'=>'".$MobileJoomla_Settings[$param]."'";
	}
	$config = "<?php\n"
			. "defined( '_JEXEC' ) or die( 'Restricted access' );\n"
			. "\n"
			. "\$MobileJoomla_Settings=array(\n"
			. "'version'=>'".HTML_mobilejoomla::getMJVersion()."',\n"
			. implode(",\n", $params)."\n"
			. ");\n"
			. "?>";

	jimport('joomla.filesystem.file');
	global $mainframe;
	if(JFile::write($configfname, $config))
	{
		$mainframe->redirect('index2.php?option=com_mobilejoomla',
		                     JText::_('The Configuration Details have been updated'));
	}
	else
	{
		$mainframe->redirect('index2.php?option=com_mobilejoomla',
		                     JText::_('An Error Has Occurred! Unable to open config file to write!'));
	}
}

function showabout()
{
	HTML_mobilejoomla::showabout();
}

function showextensions()
{
	if(!function_exists('json_decode'))
	{
		echo JText::_('ERROR: json library is not installed.');
		return;
	}
	$content = file_get_contents(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'extensions'.DS.'extensions.json');

	$json = json_decode($content);

	HTML_mobilejoomla::showextensions($json->extensions);
}
