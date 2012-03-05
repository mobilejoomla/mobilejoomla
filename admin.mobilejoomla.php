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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.event.dispatcher');

require_once(JPATH_COMPONENT.DS.'admin.mobilejoomla.html.php');

$task = JRequest::getCmd('task');
$app =& JFactory::getApplication();

JPluginHelper::importPlugin('mobile');

$dispatcher =& JDispatcher::getInstance();
$dispatcher->trigger('onMJBeforeDispatch', array($task));

// TODO: transform into JController-based controller
switch($task)
{
	case 'apply':
		saveconfig($task);
		break;
	case 'cancel':
		$app->redirect('index.php');
		break;
	case 'update':
		update();
		break;
	default:
		showconfig();
		break;
}

function showconfig()
{
	/** @var array $MobileJoomla_Settings */
	include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php';

	include_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'classes'.DS.'jhtmlmjconfig.php';

	$app =& JFactory::getApplication();
	if(!JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php'))
		$app->enqueueMessage(JText::_('COM_MJ__CONFIG_MISSING'), 'error');
	elseif(!is_writable(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php'))
		$app->enqueueMessage(JText::_('COM_MJ__CONFIG_UNWRITEABLE'), 'warning');

	/** @var JDatabase $db */
	$db =& JFactory::getDBO();
	$query = 'SELECT DISTINCT(position) FROM #__modules WHERE client_id = 0';
	$db->setQuery($query);
	$positions = $db->loadResultArray();
	$positions = (is_array($positions)) ? $positions : array ();

	$templateBaseDir = JPATH_SITE.DS.'templates'.DS;
	$templates = array ();
	$templates[] = array ('value' => '');

	$templateDirs = JFolder::folders($templateBaseDir);
	foreach($templateDirs as $templateDir)
	{
		$templateFile = $templateBaseDir.$templateDir.DS.'templateDetails.xml';
		if(!JFile::exists($templateFile))
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

	$db->setQuery('SHOW TABLE STATUS FROM `'.$app->getCfg('db').'` LIKE '.$db->Quote($app->getCfg('dbprefix').'TeraWurfl%'));
	$result = $db->loadObjectList();
	$size = 0;
	foreach($result as $row)
		$size += $row->Data_length;
	$size /= 1024*1024;
	$lists['dbsize'] = $size ? number_format($size, 2, '.', '') : '';

	$img = array (JHTML::_('select.option', 0, JText::_('COM_MJ__IMG_DONT_RESCALE')),
	              JHTML::_('select.option', 2, JText::_('COM_MJ__IMG_RESCALE')),
	              JHTML::_('select.option', 3, JText::_('COM_MJ__IMG_FIXED_RESCALE_RATIO')),
	              JHTML::_('select.option', 1, JText::_('COM_MJ__IMG_REMOVE_ALL')));
	$caching = array (JHTML::_('select.option', 0, JText::_('COM_MJ__DISABLE')),
					  JHTML::_('select.option', 1, JText::_('COM_MJ__JOOMLA')));
	$gzip = array (JHTML::_('select.option', 1, JText::_('COM_MJ__AUTO')),
				   JHTML::_('select.option', 0, JText::_('COM_MJ__OFF')));
	$simplehead = array (JHTML::_('select.option', 0, JText::_('COM_MJ__HEAD_STANDARD')),
	                     JHTML::_('select.option', 1, JText::_('COM_MJ__HEAD_SIMPLIFIED')));
	$contenttype = array (JHTML::_('select.option', 0, JText::_('COM_MJ__AUTO')),
	                      JHTML::_('select.option', 1, 'application/vnd.wap.xhtml+xml'),
	                      JHTML::_('select.option', 2, 'application/xhtml+xml'),
	                      JHTML::_('select.option', 3, 'text/html'),
	                      JHTML::_('select.option', 4, 'text/xhtml'));
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
	$wmldoctype = array (JHTML::_('select.option', 0, JText::_('COM_MJ__NONE')),
	                     JHTML::_('select.option', 1, 'WAPFORUM/WML1.1'),
	                     JHTML::_('select.option', 2, 'WAPFORUM/WML1.2'));

	//Global settings
	$lists['global.removetags'] = JHTML::_('mjconfig.booleanparam', 'global.removetags', $MobileJoomla_Settings);
	$lists['global.img'] = JHTML::_('mjconfig.listparam', 'global.img', $img, $MobileJoomla_Settings);
	$lists['global.img_addstyles'] = JHTML::_('mjconfig.booleanparam', 'global.img_addstyles', $MobileJoomla_Settings);
	$lists['global.componenthome'] = JHTML::_('mjconfig.booleanparam', 'global.componenthome', $MobileJoomla_Settings);
	$lists['global.gzip'] = JHTML::_('mjconfig.radioparam', 'global.gzip', $gzip, $MobileJoomla_Settings);
	
	//Plugin settings
	$lists['caching'] = JHTML::_('mjconfig.radioparam', 'caching', $caching, $MobileJoomla_Settings);
	$lists['httpcaching'] = JHTML::_('mjconfig.booleanparam', 'httpcaching', $MobileJoomla_Settings);

	//XHTML/WAP2.0 devices
	$lists['xhtml.template'] = JHTML::_('mjconfig.templateparam', 'xhtml.template', $templates, $MobileJoomla_Settings);
	$lists['xhtml.gzip'] = JHTML::_('mjconfig.g_radioparam', 'xhtml.gzip', $gzip, $MobileJoomla_Settings);

	//WAP devices
	$lists['wml.template'] = JHTML::_('mjconfig.templateparam', 'wml.template', $templates, $MobileJoomla_Settings);
	$lists['wml.gzip'] = JHTML::_('mjconfig.g_radioparam', 'wml.gzip', $gzip, $MobileJoomla_Settings);

	//IMODE devices
	$lists['chtml.template'] = JHTML::_('mjconfig.templateparam', 'chtml.template', $templates, $MobileJoomla_Settings);
	$lists['chtml.gzip'] = JHTML::_('mjconfig.g_radioparam', 'chtml.gzip', $gzip, $MobileJoomla_Settings);

	//iPhone/iPod devices
	$lists['iphone.template'] = JHTML::_('mjconfig.templateparam', 'iphone.template', $templates, $MobileJoomla_Settings);
	$lists['iphone.gzip'] = JHTML::_('mjconfig.g_radioparam', 'iphone.gzip', $gzip, $MobileJoomla_Settings);

	//mobile_smartphone template setting
	$lists['xhtml.header1'] = JHTML::_('mjconfig.positionparam', 'xhtml.header1', $modulepositions, $MobileJoomla_Settings);
	$lists['xhtml.header2'] = JHTML::_('mjconfig.positionparam', 'xhtml.header2', $modulepositions, $MobileJoomla_Settings);
	$lists['xhtml.header3'] = JHTML::_('mjconfig.positionparam', 'xhtml.header3', $modulepositions, $MobileJoomla_Settings);
	$lists['xhtml.middle1'] = JHTML::_('mjconfig.positionparam', 'xhtml.middle1', $modulepositions, $MobileJoomla_Settings);
	$lists['xhtml.middle2'] = JHTML::_('mjconfig.positionparam', 'xhtml.middle2', $modulepositions, $MobileJoomla_Settings);
	$lists['xhtml.middle3'] = JHTML::_('mjconfig.positionparam', 'xhtml.middle3', $modulepositions, $MobileJoomla_Settings);
	$lists['xhtml.componenthome'] = JHTML::_('mjconfig.g_booleanparam', 'xhtml.componenthome', $MobileJoomla_Settings);
	$lists['xhtml.footer1'] = JHTML::_('mjconfig.positionparam', 'xhtml.footer1', $modulepositions, $MobileJoomla_Settings);
	$lists['xhtml.footer2'] = JHTML::_('mjconfig.positionparam', 'xhtml.footer2', $modulepositions, $MobileJoomla_Settings);
	$lists['xhtml.footer3'] = JHTML::_('mjconfig.positionparam', 'xhtml.footer3', $modulepositions, $MobileJoomla_Settings);
	$lists['xhtml.jfooter'] = JHTML::_('mjconfig.booleanparam', 'xhtml.jfooter', $MobileJoomla_Settings);
	$lists['xhtml.simplehead'] = JHTML::_('mjconfig.listparam', 'xhtml.simplehead', $simplehead, $MobileJoomla_Settings);
	$lists['xhtml.allowextedit'] = JHTML::_('mjconfig.booleanparam', 'xhtml.allowextedit', $MobileJoomla_Settings);
	$lists['xhtml.removetags'] = JHTML::_('mjconfig.g_booleanparam', 'xhtml.removetags', $MobileJoomla_Settings);
	$lists['xhtml.removescripts'] = JHTML::_('mjconfig.booleanparam', 'xhtml.removescripts', $MobileJoomla_Settings);
	$lists['xhtml.img'] = JHTML::_('mjconfig.g_listparam', 'xhtml.img', $img, $MobileJoomla_Settings);
	$lists['xhtml.img_addstyles'] = JHTML::_('mjconfig.g_booleanparam', 'xhtml.img_addstyles', $MobileJoomla_Settings);
	$lists['xhtml.entitydecode'] = JHTML::_('mjconfig.booleanparam', 'xhtml.entitydecode', $MobileJoomla_Settings);
	$lists['xhtml.embedcss'] = JHTML::_('mjconfig.booleanparam', 'xhtml.embedcss', $MobileJoomla_Settings);
	$lists['xhtml.contenttype'] = JHTML::_('mjconfig.listparam', 'xhtml.contenttype', $contenttype, $MobileJoomla_Settings);
	$lists['xhtml.xmlhead'] = JHTML::_('mjconfig.booleanparam', 'xhtml.xmlhead', $MobileJoomla_Settings);
	$lists['xhtml.doctype'] = JHTML::_('mjconfig.listparam', 'xhtml.doctype', $xhtmldoctype, $MobileJoomla_Settings);
	$lists['xhtml.xmlns'] = JHTML::_('mjconfig.booleanparam', 'xhtml.xmlns', $MobileJoomla_Settings);

	//mobile_wap template setting
	$lists['wml.header1'] = JHTML::_('mjconfig.positionparam', 'wml.header1', $modulepositions, $MobileJoomla_Settings);
	$lists['wml.header2'] = JHTML::_('mjconfig.positionparam', 'wml.header2', $modulepositions, $MobileJoomla_Settings);
	$lists['wml.header3'] = JHTML::_('mjconfig.positionparam', 'wml.header3', $modulepositions, $MobileJoomla_Settings);
	$lists['wml.middle1'] = JHTML::_('mjconfig.positionparam', 'wml.middle1', $modulepositions, $MobileJoomla_Settings);
	$lists['wml.middle2'] = JHTML::_('mjconfig.positionparam', 'wml.middle2', $modulepositions, $MobileJoomla_Settings);
	$lists['wml.middle3'] = JHTML::_('mjconfig.positionparam', 'wml.middle3', $modulepositions, $MobileJoomla_Settings);
	$lists['wml.componenthome'] = JHTML::_('mjconfig.g_booleanparam', 'wml.componenthome', $MobileJoomla_Settings);
	$lists['wml.footer1'] = JHTML::_('mjconfig.positionparam', 'wml.footer1', $modulepositions, $MobileJoomla_Settings);
	$lists['wml.footer2'] = JHTML::_('mjconfig.positionparam', 'wml.footer2', $modulepositions, $MobileJoomla_Settings);
	$lists['wml.footer3'] = JHTML::_('mjconfig.positionparam', 'wml.footer3', $modulepositions, $MobileJoomla_Settings);
	$lists['wml.jfooter'] = JHTML::_('mjconfig.booleanparam', 'wml.jfooter', $MobileJoomla_Settings);
	$lists['wml.cards'] = JHTML::_('mjconfig.positionparam', 'wml.cards', $modulepositions, $MobileJoomla_Settings);
	$lists['wml.removetags'] = JHTML::_('mjconfig.g_booleanparam', 'wml.removetags', $MobileJoomla_Settings);
	$lists['wml.img'] = JHTML::_('mjconfig.g_listparam', 'wml.img', $img, $MobileJoomla_Settings);
	$lists['wml.entitydecode'] = JHTML::_('mjconfig.booleanparam', 'wml.entitydecode', $MobileJoomla_Settings);
	$lists['wml.doctype'] = JHTML::_('mjconfig.listparam', 'wml.doctype', $wmldoctype, $MobileJoomla_Settings);

	//mobile_imode template setting
	$lists['chtml.header1'] = JHTML::_('mjconfig.positionparam', 'chtml.header1', $modulepositions, $MobileJoomla_Settings);
	$lists['chtml.header2'] = JHTML::_('mjconfig.positionparam', 'chtml.header2', $modulepositions, $MobileJoomla_Settings);
	$lists['chtml.header3'] = JHTML::_('mjconfig.positionparam', 'chtml.header3', $modulepositions, $MobileJoomla_Settings);
	$lists['chtml.middle1'] = JHTML::_('mjconfig.positionparam', 'chtml.middle1', $modulepositions, $MobileJoomla_Settings);
	$lists['chtml.middle2'] = JHTML::_('mjconfig.positionparam', 'chtml.middle2', $modulepositions, $MobileJoomla_Settings);
	$lists['chtml.middle3'] = JHTML::_('mjconfig.positionparam', 'chtml.middle3', $modulepositions, $MobileJoomla_Settings);
	$lists['chtml.componenthome'] = JHTML::_('mjconfig.g_booleanparam', 'chtml.componenthome', $MobileJoomla_Settings);
	$lists['chtml.footer1'] = JHTML::_('mjconfig.positionparam', 'chtml.footer1', $modulepositions, $MobileJoomla_Settings);
	$lists['chtml.footer2'] = JHTML::_('mjconfig.positionparam', 'chtml.footer2', $modulepositions, $MobileJoomla_Settings);
	$lists['chtml.footer3'] = JHTML::_('mjconfig.positionparam', 'chtml.footer3', $modulepositions, $MobileJoomla_Settings);
	$lists['chtml.jfooter'] = JHTML::_('mjconfig.booleanparam', 'chtml.jfooter', $MobileJoomla_Settings);
	$lists['chtml.removetags'] = JHTML::_('mjconfig.g_booleanparam', 'chtml.removetags', $MobileJoomla_Settings);
	$lists['chtml.img'] = JHTML::_('mjconfig.g_listparam', 'chtml.img', $img, $MobileJoomla_Settings);
	$lists['chtml.entitydecode'] = JHTML::_('mjconfig.booleanparam', 'chtml.entitydecode', $MobileJoomla_Settings);
	$lists['chtml.doctype'] = JHTML::_('mjconfig.booleanparam', 'chtml.doctype', $MobileJoomla_Settings);

	//mobile_iphone template setting
	$lists['iphone.header1'] = JHTML::_('mjconfig.positionparam', 'iphone.header1', $modulepositions, $MobileJoomla_Settings);
	$lists['iphone.header2'] = JHTML::_('mjconfig.positionparam', 'iphone.header2', $modulepositions, $MobileJoomla_Settings);
	$lists['iphone.header3'] = JHTML::_('mjconfig.positionparam', 'iphone.header3', $modulepositions, $MobileJoomla_Settings);
	$lists['iphone.middle1'] = JHTML::_('mjconfig.positionparam', 'iphone.middle1', $modulepositions, $MobileJoomla_Settings);
	$lists['iphone.middle2'] = JHTML::_('mjconfig.positionparam', 'iphone.middle2', $modulepositions, $MobileJoomla_Settings);
	$lists['iphone.middle3'] = JHTML::_('mjconfig.positionparam', 'iphone.middle3', $modulepositions, $MobileJoomla_Settings);
	$lists['iphone.componenthome'] = JHTML::_('mjconfig.g_booleanparam', 'iphone.componenthome', $MobileJoomla_Settings);
	$lists['iphone.footer1'] = JHTML::_('mjconfig.positionparam', 'iphone.footer1', $modulepositions, $MobileJoomla_Settings);
	$lists['iphone.footer2'] = JHTML::_('mjconfig.positionparam', 'iphone.footer2', $modulepositions, $MobileJoomla_Settings);
	$lists['iphone.footer3'] = JHTML::_('mjconfig.positionparam', 'iphone.footer3', $modulepositions, $MobileJoomla_Settings);
	$lists['iphone.jfooter'] = JHTML::_('mjconfig.booleanparam', 'iphone.jfooter', $MobileJoomla_Settings);
	$lists['iphone.img'] = JHTML::_('mjconfig.g_listparam', 'iphone.img', $img, $MobileJoomla_Settings);
	$lists['iphone.img_addstyles'] = JHTML::_('mjconfig.g_booleanparam', 'iphone.img_addstyles', $MobileJoomla_Settings);
	$lists['iphone.removetags'] = JHTML::_('mjconfig.g_booleanparam', 'iphone.removetags', $MobileJoomla_Settings);

	function menuoptions()
	{
		/** @var JDatabase $db */
		$db =& JFactory::getDBO();
		$isJoomla15 = (substr(JVERSION,0,3) == '1.5');
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

function saveconfig()
{
	$configfname = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php';
	include($configfname);

	$settings = array ('caching', 'httpcaching', 'domains', 'pcpage', 'templatewidth', 'jpegquality',
					   'desktop_url', 'mobile_sitename',
					   'xhtml.template', 'xhtml.homepage', 'xhtml.gzip', 'xhtml.domain', 'xhtml.redirect', 'xhtml.buffer_width',
					   'wml.template', 'wml.homepage', 'wml.gzip', 'wml.domain', 'wml.redirect', 'wml.buffer_width',
					   'chtml.template', 'chtml.homepage', 'chtml.gzip', 'chtml.domain', 'chtml.redirect', 'chtml.buffer_width',
					   'iphone.template', 'iphone.homepage', 'iphone.gzip', 'iphone.domain', 'iphone.redirect', 'iphone.buffer_width',
					   'global.gzip', 'global.removetags', 'global.img', 'global.img_addstyles', 'global.homepage', 'global.componenthome',
					   'xhtml.header1', 'xhtml.header2', 'xhtml.header3',
					   'xhtml.middle1', 'xhtml.middle2', 'xhtml.middle3',
					   'xhtml.componenthome',
					   'xhtml.footer1', 'xhtml.footer2', 'xhtml.footer3',
					   'xhtml.jfooter', 'xhtml.simplehead', 'xhtml.allowextedit',
					   'xhtml.removetags', 'xhtml.removescripts', 'xhtml.img', 'xhtml.img_addstyles',
					   'xhtml.entitydecode', 'xhtml.embedcss', 'xhtml.contenttype', 'xhtml.xmlhead',
					   'xhtml.doctype', 'xhtml.xmlns',
					   'wml.header1', 'wml.header2', 'wml.header3',
					   'wml.middle1', 'wml.middle2', 'wml.middle3',
					   'wml.componenthome',
					   'wml.footer1', 'wml.footer2', 'wml.footer3',
					   'wml.cards', 'wml.jfooter',
					   'wml.removetags', 'wml.img', 'wml.entitydecode', 'wml.doctype',
					   'chtml.header1', 'chtml.header2', 'chtml.header3',
					   'chtml.middle1', 'chtml.middle2', 'chtml.middle3',
					   'chtml.componenthome',
					   'chtml.footer1', 'chtml.footer2', 'chtml.footer3',
					   'chtml.jfooter', 'chtml.removetags', 'chtml.img',
					   'chtml.entitydecode', 'chtml.doctype',
					   'iphone.header1', 'iphone.header2', 'iphone.header3',
					   'iphone.middle1', 'iphone.middle2', 'iphone.middle3',
					   'iphone.componenthome',
					   'iphone.footer1', 'iphone.footer2', 'iphone.footer3',
					   'iphone.jfooter', 'iphone.img', 'iphone.img_addstyles',
					   'iphone.removetags');

	$dispatcher =& JDispatcher::getInstance();
	$dispatcher->trigger('onMJBeforeSave', array(&$settings, &$MobileJoomla_Settings));

	$params = array ();
	foreach($settings as $param)
	{
		$post_name = 'mjconfig_'.str_replace('.', '-', $param);
		if(isset($_POST[$post_name]))
		{
			if(!get_magic_quotes_gpc())
				$_POST[$post_name] = addslashes($_POST[$post_name]);
			$MobileJoomla_Settings[$param] = $_POST[$post_name];
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

	$app =& JFactory::getApplication();
	if(JFile::write($configfname, $config))
	{
		$app->redirect('index.php?option=com_mobilejoomla',
		               JText::_('COM_MJ__CONFIG_UPDATED'));
	}
	else
	{
		$app->redirect('index.php?option=com_mobilejoomla',
		               JText::_('COM_MJ__UNABLE_OPEN_CONFIG'));
	}
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
	jimport('joomla.installer.helper');
	jimport('joomla.installer.installer');
	$app =& JFactory::getApplication();
	$option = JRequest::getString('option');

	$state = JRequest::getWord('state');
	switch($state)
	{
	case 'download':
		_initStatus();
		$url = 'http://www.mobilejoomla.com/latest.php';
		$filename = JInstallerHelper::downloadPackage($url);
		if($filename)
			$app->setUserState( "$option.updatefilename", $filename );
		_sendStatus();
		break;
	case 'unpack':
		_initStatus();
		$filename = $app->getUserState( "$option.updatefilename", false );
		$config =& JFactory::getConfig();
		$path = $config->getValue('config.tmp_path').DS.$filename;
		if($path)
		{
			$result = JInstallerHelper::unpack($path);
			$app->setUserState( "$option.updatefilename", false );
			if($result!==false)
			{
				$app->setUserState( "$option.updatedir", $result['dir'] );
				JFile::delete($path);
			}
		}
		else
			JError::raiseWarning(1, JText::_('COM_MJ__UPDATE_UNKNOWN_PATH'));
		_sendStatus();
		break;
	case 'install':
		_initStatus();
		$dir = $app->getUserState( "$option.updatedir", false );
		if($dir)
		{
			$installer = new JInstaller();
			$installer->install($dir);
			$app->setUserState( "$option.updatedir", false );
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