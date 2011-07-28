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

//ACL check
if(version_compare(JVERSION,'1.6.0','ge') &&
		!(JFactory::getUser()->authorise('core.manage', 'com_mobilejoomla')))
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));

require_once(JPATH_COMPONENT.DS.'admin.mobilejoomla.html.php');

$task = JRequest::getCmd('task');
$mainframe =& JFactory::getApplication();

// TODO: transform into JController-based controller
switch($task)
{
	case 'apply':
		saveconfig($task);
		break;
	case 'cancel':
		$mainframe->redirect('index.php');
		break;
	case 'about':
		showabout();
		break;
	case 'extensions':
		showextensions();
		break;
	case 'update':
		update();
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
	include(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php');

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
	$caching = array (JHTML::_('select.option', 0, JText::_('COM_MJ__DISABLE')),
					  JHTML::_('select.option', 1, JText::_('COM_MJ__GLOBAL')));
	$lists['caching'] = JHTML::_('select.radiolist', $caching, 'mjconfig_caching', 'class="inputbox"', 'value', 'text', $MobileJoomla_Settings['caching']);

	$httpcaching = array (JHTML::_('select.option', 0, JText::_('COM_MJ__DISABLE')),
						  JHTML::_('select.option', 1, JText::_('COM_MJ__ENABLE')));
	$lists['httpcaching'] = JHTML::_('select.radiolist', $httpcaching, 'mjconfig_httpcaching', 'class="inputbox"', 'value', 'text', $MobileJoomla_Settings['httpcaching']);

	$lists['domains'] = JHTML::_('select.booleanlist', 'mjconfig_domains', 'class="inputbox"', $MobileJoomla_Settings['domains']);

	//XHTML/WAP2.0 devices
	$lists['xhtmltemplate'] = selectArray($templates, 'mjconfig_xhtmltemplate', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['xhtmltemplate']);

	$gzip = array (JHTML::_('select.option', 0, JText::_('COM_MJ__NO')),
	               JHTML::_('select.option', 1, JText::_('COM_MJ__AUTO')));
	$lists['xhtmlgzip'] = JHTML::_('select.radiolist', $gzip, 'mjconfig_xhtmlgzip', 'class="inputbox"', 'value', 'text', $MobileJoomla_Settings['xhtmlgzip']);

	$lists['xhtmlredirect'] = JHTML::_('select.booleanlist', 'mjconfig_xhtmlredirect', 'class="inputbox"', $MobileJoomla_Settings['xhtmlredirect']);

	//WAP devices
	$lists['waptemplate'] = selectArray($templates, 'mjconfig_waptemplate', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['waptemplate']);

	$lists['wapgzip'] = JHTML::_('select.radiolist', $gzip, 'mjconfig_wapgzip', 'class="inputbox"', 'value', 'text', $MobileJoomla_Settings['wapgzip']);

	$lists['wapredirect'] = JHTML::_('select.booleanlist', 'mjconfig_wapredirect', 'class="inputbox"', $MobileJoomla_Settings['wapredirect']);

	//IMODE devices
	$lists['imodetemplate'] = selectArray($templates, 'mjconfig_imodetemplate', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['imodetemplate']);

	$lists['imodegzip'] = JHTML::_('select.radiolist', $gzip, 'mjconfig_imodegzip', 'class="inputbox"', 'value', 'text', $MobileJoomla_Settings['imodegzip']);

	$lists['imoderedirect'] = JHTML::_('select.booleanlist', 'mjconfig_imoderedirect', 'class="inputbox"', $MobileJoomla_Settings['imoderedirect']);

	//iPhone/iPod devices
	$lists['iphonetemplate'] = selectArray($templates, 'mjconfig_iphonetemplate', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['iphonetemplate']);

	$lists['iphoneipad'] = JHTML::_('select.booleanlist', 'mjconfig_iphoneipad', 'class="inputbox"', $MobileJoomla_Settings['iphoneipad']);

	$lists['iphonegzip'] = JHTML::_('select.radiolist', $gzip, 'mjconfig_iphonegzip', 'class="inputbox"', 'value', 'text', $MobileJoomla_Settings['iphonegzip']);

	$lists['iphoneredirect'] = JHTML::_('select.booleanlist', 'mjconfig_iphoneredirect', 'class="inputbox"', $MobileJoomla_Settings['iphoneredirect']);

	//mobile_pda template setting
	$lists['tmpl_xhtml_header1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_header1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_header1']);
	$lists['tmpl_xhtml_header2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_header2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_header2']);
	$lists['tmpl_xhtml_header3'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_header3', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_header3']);

	$lists['tmpl_xhtml_pathway'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_pathway', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_pathway']);

	$lists['tmpl_xhtml_pathwayhome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_pathwayhome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_pathwayhome']);

	$lists['tmpl_xhtml_middle1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_middle1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_middle1']);
	$lists['tmpl_xhtml_middle2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_middle2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_middle2']);
	$lists['tmpl_xhtml_middle3'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_middle3', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_middle3']);

	$lists['tmpl_xhtml_componenthome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_componenthome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_componenthome']);

	$lists['tmpl_xhtml_footer1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_footer1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_footer1']);
	$lists['tmpl_xhtml_footer2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_footer2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_footer2']);
	$lists['tmpl_xhtml_footer3'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_xhtml_footer3', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_xhtml_footer3']);

	$lists['tmpl_xhtml_jfooter'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_jfooter', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_jfooter']);

	$simplehead = array (JHTML::_('select.option', 0, JText::_('COM_MJ__HEAD_STANDARD')),
	                     JHTML::_('select.option', 1, JText::_('COM_MJ__HEAD_SIMPLIFIED')));
	$lists['tmpl_xhtml_simplehead'] = JHTML::_('select.genericlist', $simplehead, 'mjconfig_tmpl_xhtml_simplehead', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_xhtml_simplehead']);

	$lists['tmpl_xhtml_allowextedit'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_allowextedit', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_allowextedit']);

	$lists['tmpl_xhtml_removetags'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_removetags', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_removetags']);

	$lists['tmpl_xhtml_removescripts'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_removescripts', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_removescripts']);

	$img = array (JHTML::_('select.option', 0, JText::_('COM_MJ__IMG_DONT_RESCALE')),
	              JHTML::_('select.option', 1, JText::_('COM_MJ__IMG_REMOVE_ALL')),
	              JHTML::_('select.option', 2, JText::_('COM_MJ__IMG_RESCALE')),
	              JHTML::_('select.option', 3, JText::_('COM_MJ__IMG_FIXED_RESCALE_RATIO')));
	$lists['tmpl_xhtml_img'] = JHTML::_('select.genericlist', $img, 'mjconfig_tmpl_xhtml_img', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_xhtml_img']);

	$lists['tmpl_xhtml_img_addstyles'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_img_addstyles', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_img_addstyles']);

	$lists['tmpl_xhtml_entitydecode'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_entitydecode', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_entitydecode']);

	$lists['tmpl_xhtml_embedcss'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_embedcss', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_embedcss']);

	$contenttype = array (JHTML::_('select.option', 0, JText::_('COM_MJ__AUTO')),
	                      JHTML::_('select.option', 1, 'application/vnd.wap.xhtml+xml'),
	                      JHTML::_('select.option', 2, 'application/xhtml+xml'),
	                      JHTML::_('select.option', 3, 'text/html'),
	                      JHTML::_('select.option', 4, 'text/xhtml'));
	$lists['tmpl_xhtml_contenttype'] = JHTML::_('select.genericlist', $contenttype, 'mjconfig_tmpl_xhtml_contenttype', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_xhtml_contenttype']);

	$lists['tmpl_xhtml_xmlhead'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_xhtml_xmlhead', 'class="inputbox"', $MobileJoomla_Settings['tmpl_xhtml_xmlhead']);

	$xhtmldoctype = array (JHTML::_('select.option', 0, JText::_('COM_MJ__NONE')),
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
	$lists['tmpl_wap_header1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_wap_header1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_wap_header1']);
	$lists['tmpl_wap_header2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_wap_header2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_wap_header2']);
	$lists['tmpl_wap_header3'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_wap_header3', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_wap_header3']);

	$lists['tmpl_wap_pathway'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_wap_pathway', 'class="inputbox"', $MobileJoomla_Settings['tmpl_wap_pathway']);

	$lists['tmpl_wap_pathwayhome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_wap_pathwayhome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_wap_pathwayhome']);

	$lists['tmpl_wap_middle1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_wap_middle1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_wap_middle1']);
	$lists['tmpl_wap_middle2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_wap_middle2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_wap_middle2']);
	$lists['tmpl_wap_middle3'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_wap_middle3', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_wap_middle3']);

	$lists['tmpl_wap_componenthome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_wap_componenthome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_wap_componenthome']);

	$lists['tmpl_wap_footer1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_wap_footer1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_wap_footer1']);
	$lists['tmpl_wap_footer2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_wap_footer2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_wap_footer2']);
	$lists['tmpl_wap_footer3'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_wap_footer3', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_wap_footer3']);

	$lists['tmpl_wap_jfooter'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_wap_jfooter', 'class="inputbox"', $MobileJoomla_Settings['tmpl_wap_jfooter']);

	$lists['tmpl_wap_cards'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_wap_cards', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_wap_cards']);

	$lists['tmpl_wap_removetags'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_wap_removetags', 'class="inputbox"', $MobileJoomla_Settings['tmpl_wap_removetags']);

	$lists['tmpl_wap_img'] = JHTML::_('select.genericlist', $img, 'mjconfig_tmpl_wap_img', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_wap_img']);

	$lists['tmpl_wap_entitydecode'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_wap_entitydecode', 'class="inputbox"', $MobileJoomla_Settings['tmpl_wap_entitydecode']);

	$wapdoctype = array (JHTML::_('select.option', 0, JText::_('COM_MJ__NONE')),
	                     JHTML::_('select.option', 1, 'WAPFORUM/WML1.1'),
	                     JHTML::_('select.option', 2, 'WAPFORUM/WML1.2'));
	$lists['tmpl_wap_doctype'] = JHTML::_('select.genericlist', $wapdoctype, 'mjconfig_tmpl_wap_doctype', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_wap_doctype']);

	//mobile_imode template setting
	$lists['tmpl_imode_header1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_header1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_header1']);
	$lists['tmpl_imode_header2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_header2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_header2']);
	$lists['tmpl_imode_header3'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_header3', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_header3']);

	$lists['tmpl_imode_pathway'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_imode_pathway', 'class="inputbox"', $MobileJoomla_Settings['tmpl_imode_pathway']);

	$lists['tmpl_imode_pathwayhome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_imode_pathwayhome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_imode_pathwayhome']);

	$lists['tmpl_imode_middle1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_middle1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_middle1']);
	$lists['tmpl_imode_middle2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_middle2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_middle2']);
	$lists['tmpl_imode_middle3'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_middle3', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_middle3']);

	$lists['tmpl_imode_componenthome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_imode_componenthome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_imode_componenthome']);

	$lists['tmpl_imode_footer1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_footer1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_footer1']);
	$lists['tmpl_imode_footer2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_footer2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_footer2']);
	$lists['tmpl_imode_footer3'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_imode_footer3', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_imode_footer3']);

	$lists['tmpl_imode_jfooter'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_imode_jfooter', 'class="inputbox"', $MobileJoomla_Settings['tmpl_imode_jfooter']);

	$lists['tmpl_imode_removetags'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_imode_removetags', 'class="inputbox"', $MobileJoomla_Settings['tmpl_imode_removetags']);

	$lists['tmpl_imode_img'] = JHTML::_('select.genericlist', $img, 'mjconfig_tmpl_imode_img', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_imode_img']);

	$lists['tmpl_imode_entitydecode'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_imode_entitydecode', 'class="inputbox"', $MobileJoomla_Settings['tmpl_imode_entitydecode']);

	$lists['tmpl_imode_doctype'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_imode_doctype', 'class="inputbox"', $MobileJoomla_Settings['tmpl_imode_doctype']);

	//mobile_iphone template setting
	$lists['tmpl_iphone_header1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_header1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_header1']);
	$lists['tmpl_iphone_header2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_header2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_header2']);
	$lists['tmpl_iphone_header3'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_header3', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_header3']);

	$lists['tmpl_iphone_pathway'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_iphone_pathway', 'class="inputbox"', $MobileJoomla_Settings['tmpl_iphone_pathway']);

	$lists['tmpl_iphone_pathwayhome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_iphone_pathwayhome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_iphone_pathwayhome']);

	$lists['tmpl_iphone_middle1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_middle1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_middle1']);
	$lists['tmpl_iphone_middle2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_middle2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_middle2']);
	$lists['tmpl_iphone_middle3'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_middle3', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_middle3']);

	$lists['tmpl_iphone_componenthome'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_iphone_componenthome', 'class="inputbox"', $MobileJoomla_Settings['tmpl_iphone_componenthome']);

	$lists['tmpl_iphone_footer1'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_footer1', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_footer1']);
	$lists['tmpl_iphone_footer2'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_footer2', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_footer2']);
	$lists['tmpl_iphone_footer3'] = JHTML::_('select.genericlist', $modulepositions, 'mjconfig_tmpl_iphone_footer3', 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings['tmpl_iphone_footer3']);

	$lists['tmpl_iphone_jfooter'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_iphone_jfooter', 'class="inputbox"', $MobileJoomla_Settings['tmpl_iphone_jfooter']);

	$lists['tmpl_iphone_img'] = JHTML::_('select.genericlist', $img, 'mjconfig_tmpl_iphone_img', 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings['tmpl_iphone_img']);

	$lists['tmpl_iphone_img_addstyles'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_iphone_img_addstyles', 'class="inputbox"', $MobileJoomla_Settings['tmpl_iphone_img_addstyles']);

	$lists['tmpl_iphone_removetags'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_iphone_removetags', 'class="inputbox"', $MobileJoomla_Settings['tmpl_iphone_removetags']);

	$lists['tmpl_iphone_removetags'] = JHTML::_('select.booleanlist', 'mjconfig_tmpl_iphone_removetags', 'class="inputbox"', $MobileJoomla_Settings['tmpl_iphone_removetags']);

	function menuoptions()
	{
		/** @var JDatabase $db */
		$db =& JFactory::getDBO();
		$version = new JVersion;
		$isJoomla15 = (substr($version->getShortVersion(),0,3) == '1.5');
		if(!$isJoomla15)
			$query = 'SELECT id, menutype, title, link, type, parent_id FROM #__menu WHERE published=1 ORDER BY menutype, parent_id, ordering';
		else
			$query = 'SELECT id, menutype, name AS title, link, type, parent AS parent_id FROM #__menu WHERE published=1 ORDER BY menutype, parent, ordering';
		$db->setQuery($query);
		$mitems = $db->loadObjectList();
		$children = array();
		foreach($mitems as $v)
		{
			$pt = $v->parent_id;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}
		$list = array();
		if(!$isJoomla15)
			$id = intval($mitems[0]->id);
		else
			$id = intval($mitems[0]->parent_id);
		if(@$children[$id])
			TreeRecurse($id, '', $list, $children);
		$mitems = array();
		$mitems[] = JHTML::_('select.option', '', '&nbsp;');
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
			$mitems[] = JHTML::_('select.option', '</OPTGROUP>' );
		return $mitems;
	}
	function TreeRecurse($id, $indent, &$list, &$children, $level=0)
	{
		foreach($children[$id] as $v)
		{
			$id = $v->id;
			$list[$id] = $v;
			$list[$id]->treename = $indent.$v->title;
			if(@$children[$id] && $level<=99)
				TreeRecurse($id, $indent.'&nbsp;&nbsp;', $list, $children, $level+1);
		}
	}
	$lists['menuoptions'] = menuoptions();

	HTML_mobilejoomla::showconfig($lists, $MobileJoomla_Settings);
}

function saveExtensionsConfig()
{
	$mainframe =& JFactory::getApplication();

	$content = file_get_contents(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'extensions'.DS.'extensions.json');

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

	$mainframe->redirect('index.php?option=com_mobilejoomla&task=extensions',
	                     JText::_('COM_MJ__CONFIG_UPDATED'));
}

function saveconfig()
{
	if(JRequest::getVar('ext', false))
	{
		saveExtensionsConfig();

		return;
	}

	$configfname = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php';
	include($configfname);

	$settings = array ('caching', 'httpcaching', 'domains', 'pcpage', 'templatewidth', 'jpegquality',
					   'desktop_url', 'mobile_sitename',
					   'xhtmltemplate', 'xhtmlhomepage', 'xhtmlgzip', 'xhtmldomain', 'xhtmlredirect', 'xhtml_buffer_width',
					   'waptemplate', 'waphomepage', 'wapgzip', 'wapdomain', 'wapredirect', 'wml_buffer_width',
					   'imodetemplate', 'imodehomepage', 'imodegzip', 'imodedomain', 'imoderedirect', 'chtml_buffer_width',
					   'iphonetemplate', 'iphoneipad', 'iphonehomepage', 'iphonegzip', 'iphonedomain',
					   'iphoneredirect', 'iphone_buffer_width',
					   'tmpl_xhtml_header1', 'tmpl_xhtml_header2', 'tmpl_xhtml_header3',
					   'tmpl_xhtml_pathway', 'tmpl_xhtml_pathwayhome',
					   'tmpl_xhtml_middle1', 'tmpl_xhtml_middle2', 'tmpl_xhtml_middle3',
					   'tmpl_xhtml_componenthome',
					   'tmpl_xhtml_footer1', 'tmpl_xhtml_footer2', 'tmpl_xhtml_footer3',
					   'tmpl_xhtml_jfooter', 'tmpl_xhtml_simplehead', 'tmpl_xhtml_allowextedit',
					   'tmpl_xhtml_removetags', 'tmpl_xhtml_removescripts', 'tmpl_xhtml_img', 'tmpl_xhtml_img_addstyles',
					   'tmpl_xhtml_entitydecode', 'tmpl_xhtml_embedcss', 'tmpl_xhtml_contenttype', 'tmpl_xhtml_xmlhead',
					   'tmpl_xhtml_doctype', 'tmpl_xhtml_xmlns',
					   'tmpl_wap_header1', 'tmpl_wap_header2', 'tmpl_wap_header3',
					   'tmpl_wap_pathway', 'tmpl_wap_pathwayhome',
					   'tmpl_wap_middle1', 'tmpl_wap_middle2', 'tmpl_wap_middle3',
					   'tmpl_wap_componenthome',
					   'tmpl_wap_footer1', 'tmpl_wap_footer2', 'tmpl_wap_footer3',
					   'tmpl_wap_cards', 'tmpl_wap_jfooter',
					   'tmpl_wap_removetags', 'tmpl_wap_img', 'tmpl_wap_entitydecode', 'tmpl_wap_doctype',
					   'tmpl_imode_header1', 'tmpl_imode_header2', 'tmpl_imode_header3',
					   'tmpl_imode_pathway', 'tmpl_imode_pathwayhome',
					   'tmpl_imode_middle1', 'tmpl_imode_middle2', 'tmpl_imode_middle3',
					   'tmpl_imode_componenthome',
					   'tmpl_imode_footer1', 'tmpl_imode_footer2', 'tmpl_imode_footer3',
					   'tmpl_imode_jfooter', 'tmpl_imode_removetags', 'tmpl_imode_img',
					   'tmpl_imode_entitydecode', 'tmpl_imode_doctype',
					   'tmpl_iphone_header1', 'tmpl_iphone_header2', 'tmpl_iphone_header3',
					   'tmpl_iphone_pathway', 'tmpl_iphone_pathwayhome',
					   'tmpl_iphone_middle1', 'tmpl_iphone_middle2', 'tmpl_iphone_middle3',
					   'tmpl_iphone_componenthome',
					   'tmpl_iphone_footer1', 'tmpl_iphone_footer2', 'tmpl_iphone_footer3',
					   'tmpl_iphone_jfooter', 'tmpl_iphone_img', 'tmpl_iphone_img_addstyles',
					   'tmpl_iphone_removetags');
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
	$mainframe =& JFactory::getApplication();
	if(JFile::write($configfname, $config))
	{
		$mainframe->redirect('index.php?option=com_mobilejoomla',
		                     JText::_('COM_MJ__CONFIG_UPDATED'));
	}
	else
	{
		$mainframe->redirect('index.php?option=com_mobilejoomla',
		                     JText::_('COM_MJ__UNABLE_OPEN_CONFIG'));
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
		echo JText::_('COM_MJ__ERROR_JSON_LIBRARY_ISNOT_INSTALLED');
		return;
	}
	$content = file_get_contents(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'extensions'.DS.'extensions.json');

	$json = json_decode($content);

	HTML_mobilejoomla::showextensions($json->extensions);
}

function _initStatus()
{
	JError::setErrorHandling(E_ERROR, 'Message');
	@set_time_limit(1200);
	@ini_set('max_execution_time', 1200);
}
function _sendStatus()
{
	$msg = array();
	foreach(JError::getErrors() as $error)
		if($error->get('level'))
			$msg[] = $error->get('message');
	if(count($msg))
		$msg = '<p>'.implode('</p><p>', $msg).'</p>';
	else
		$msg = 'ok';
	echo $msg;
	jexit();
}
function update()
{
	jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.folder');
	jimport('joomla.installer.helper');
	jimport('joomla.installer.installer');
	$mainframe =& JFactory::getApplication();
	$option = JRequest::getString('option');

	$state = JRequest::getWord('state');
	switch($state)
	{
	case 'download':
		_initStatus();
		$url = 'http://www.mobilejoomla.com/latest.php';
		$filename = JInstallerHelper::downloadPackage($url);
		if($filename)
			$mainframe->setUserState( "$option.updatefilename", $filename );
		_sendStatus();
		break;
	case 'unpack':
		_initStatus();
		$filename = $mainframe->getUserState( "$option.updatefilename", false );
		$config =& JFactory::getConfig();
		$path = $config->getValue('config.tmp_path').DS.$filename;
		if($path)
		{
			$result = JInstallerHelper::unpack($path);
			$mainframe->setUserState( "$option.updatefilename", false );
			if($result!==false)
			{
				$mainframe->setUserState( "$option.updatedir", $result['dir'] );
				JFile::delete($path);
			}
		}
		else
			JError::raiseWarning(1, JText::_('COM_MJ__UPDATE_UNKNOWN_PATH'));
		_sendStatus();
		break;
	case 'install':
		_initStatus();
		$dir = $mainframe->getUserState( "$option.updatedir", false );
		if($dir)
		{
			$installer = new JInstaller();
			$installer->install($dir);
			$mainframe->setUserState( "$option.updatedir", false );
			JFolder::delete($dir);
		}
		else
			JError::raiseWarning(1, JText::_('COM_MJ__UPDATE_UNKNOWN_PATH'));
		_sendStatus();
		break;
	default: // TODO: move into view
?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript">
//<![CDATA[
jQuery.noConflict();
function mjOnError()
{
	jQuery("#mjlink").css('display','block');
}
function mjTestStatus(textStatus)
{
	switch(textStatus){
		case "success": break;
		case "notmodified": break;
		case "error": jQuery("#mjstatus").html("<?php echo JText::_('COM_MJ__UPDATE_AJAX_ERROR'); ?>"); mjOnError(); break;
		case "timeout": jQuery("#mjstatus").html("<?php echo JText::_('COM_MJ__UPDATE_AJAX_TIMEOUT'); ?>"); mjOnError(); break;
		case "abort": jQuery("#mjstatus").html("<?php echo JText::_('COM_MJ__UPDATE_AJAX_ABORT'); ?>"); mjOnError(); break;
		case "parsererror": jQuery("#mjstatus").html("<?php echo JText::_('COM_MJ__UPDATE_AJAX_PARSEERROR'); ?>"); mjOnError(); break;
	}
}
function mjAjaxDownload()
{
	jQuery("#mjdownload").addClass("highlight").addClass("ajaxload");
	jQuery.ajax({
		type: "GET",
		url: "index.php?option=com_mobilejoomla&task=update&state=download&tmpl=none",
		success: function(data){
			if(data!="ok") {
				jQuery("#mjdownload").addClass("error");
				jQuery("#mjstatus").html(data);
				mjOnError();
			} else {
				jQuery("#mjdownload").addClass("pass");
				mjAjaxUnpack(); 
			}
		},
		error: function(){
			jQuery("#mjdownload").addClass("error");
			mjOnError();
		},
		complete: function(jqXHR, textStatus){
			jQuery("#mjdownload").removeClass("ajaxload");
			mjTestStatus(textStatus);
		}
	});
}
function mjAjaxUnpack()
{
	jQuery("#mjunpack").addClass("highlight").addClass("ajaxload");
	jQuery.ajax({
		type: "GET",
		url: "index.php?option=com_mobilejoomla&task=update&state=unpack&tmpl=none",
		success: function(data){
			if(data!="ok") {
				jQuery("#mjunpack").addClass("error");
				jQuery("#mjstatus").html(data);
				mjOnError();
			} else {
				jQuery("#mjunpack").addClass("pass");
				mjAjaxInstall(); 
			}
		},
		error: function(){
			jQuery("#mjunpack").addClass("error");
			mjOnError();
		},
		complete: function(jqXHR, textStatus){
			jQuery("#mjunpack").removeClass("ajaxload");
			mjTestStatus(textStatus);
		}
	});
}
function mjAjaxInstall()
{
	jQuery("#mjinstall").addClass("highlight").addClass("ajaxload");
	jQuery.ajax({
		type: "GET",
		url: "index.php?option=com_mobilejoomla&task=update&state=install&tmpl=none",
		success: function(data){
			if(data!="ok") {
				jQuery("#mjinstall").addClass("error");
				jQuery("#mjstatus").html(data);
				mjOnError();
			} else {
				jQuery("#mjinstall").addClass("pass");
				window.parent.location.reload();
			}
		},
		error: function(){
			jQuery("#mjinstall").addClass("error");
			mjOnError();
		},
		complete: function(jqXHR, textStatus){
			jQuery("#mjinstall").removeClass("ajaxload");
			mjTestStatus(textStatus);
		}
	});
}
jQuery(document).ready(mjAjaxDownload);
//]]>
</script>
<style type="text/css">
.mjheader {
	font-size: 20px;
	font-weight: bold;
	line-height: 48px;
	margin-left: 5px;
	padding-left: 5px;
}
#mjstages {
	list-style-type: none;
	margin: 0;
	padding: 0;
}
#mjstages li {
	height: 22px;
	padding: 10px 0 0 32px;
	margin: 0;
}
#mjlink {
	display: none;
	background: url("components/com_mobilejoomla/images/warning.png") no-repeat scroll 10px 50% #FDFBB9;
	font-weight: bold;
	-moz-border-radius: 8px;
	-webkit-border-radius: 8px;
	border-radius: 8px;
	border: 3px solid #f00;
	line-height: 135%;
	margin-top: 15px;
	padding: 10px 10px 10px 48px;
	text-align: left;
}
#mjstatus {
	font-size: 80%;
	padding: 16px 8px 0;
}
.highlight {
	font-weight: bold;
}
.ajaxload {
	background: url("components/com_mobilejoomla/images/ajax-loader.gif") no-repeat scroll 0 50% #FFF;
	line-height: 100%;
	text-align: left;
}
.pass {
	background: url("components/com_mobilejoomla/images/tick.png") no-repeat scroll 8px 50% #FFF;
	line-height: 100%;
	text-align: left;
}
.error {
	background: url("components/com_mobilejoomla/images/error.png") no-repeat scroll 8px 50% #FDFBB9;
	color: #f00;
	line-height: 100%;
	text-align: left;
}
</style>
<div class="mjheader"><?php echo JText::_('COM_MJ__UPDATE_HEADER'); ?></div>
<ul id="mjstages">
	<li id="mjdownload"><?php echo JText::_('COM_MJ__UPDATE_DOWNLOAD'); ?></li>
	<li id="mjunpack"><?php echo JText::_('COM_MJ__UPDATE_UNPACK'); ?></li>
	<li id="mjinstall"><?php echo JText::_('COM_MJ__UPDATE_INSTALL'); ?></li>
</ul>
<div id="mjlink"><?php echo JText::_('COM_MJ__UPDATE_DOWNLOAD_LINK'); ?></div>
<div id="mjstatus"></div>
<?php
	}
}