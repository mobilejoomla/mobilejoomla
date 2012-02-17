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

function getMenuList($menuoptions, $name, $value)
{
	static $is_joomla15;
	if(!isset($is_joomla15))
		$is_joomla15 = (substr(JVERSION,0,3) == '1.5');

	if(!$is_joomla15)
		return JHTML::_('select.genericlist',
						$menuoptions,
						$name.'_tmp',
						array('list.attr' => 'size="7" onchange="document.getElementById(\''.$name.'\').value=this.value" ',
							  'list.select' => $value,
							  'option.text.toHtml' => false));
	else
		return JHTML::_('select.genericlist',
						$menuoptions,
						$name.'_tmp',
						'size="7" onchange="document.getElementById(\''.$name.'\').value=this.value" ',
						'value',
						'text',
						$value);
}

class HTML_mobilejoomla
{
	function getMJVersion()
	{
		$manifest = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.xml';
		if(is_file($manifest))
		{
			$xml =& JFactory::getXMLParser('Simple');
			if($xml->loadFile($manifest))
			{
				$element =& $xml->document->getElementByPath('version');
				$version = $element ? $element->data() : '';
				if($version)
					return $version;
			}
		}
		return false;
	}

	function CheckForUpdate()
	{
		$version = HTML_mobilejoomla::getMJVersion();
		if($version)
		{
?>
<style>#mjupdate {display: none}</style>
<link rel="stylesheet" type="text/css" href="http://www.mobilejoomla.com/checker.php?v=<?php echo urlencode($version); ?>&amp;j=<?php echo urlencode(JVERSION); ?>"/>
<?php
		}
	}
	
	function showUpdateNotification()
	{
		HTML_mobilejoomla::CheckForUpdate();
		JHTML::_('behavior.modal', 'a.modal');
?>
<style type="text/css">
#mjupdate {
	background: url("components/com_mobilejoomla/images/warning.png") no-repeat scroll 10px 50% #FDFBB9;
	font-weight: bold;
	border: 3px solid #f00;
	-moz-border-radius: 8px;
	-webkit-border-radius: 8px;
	border-radius: 8px;
	line-height: 100%;
	padding: 0 10px 10px 48px;
	text-align: left;
}
</style>
		<div id="mjupdate">
			<h2><?php echo JText::_('COM_MJ__UPDATE_AVAILABLE');?></h2>
			<?php echo JText::sprintf('COM_MJ__UPDATE_NOTIFICATION', 'class="modal" href="index.php?tmpl=component&option=com_mobilejoomla&task=update" rel="{handler: \'iframe\', size: {x: 480, y: 320}}"'); ?>
		</div>
<?php
	}

	function showconfig(&$lists, $MobileJoomla_Settings)
	{
		jimport('joomla.filesystem.file');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.switcher');
		HTML_mobilejoomla::showUpdateNotification();
		?>
<style>
table.admintable td.key {
	margin-right: 10px;
}
fieldset.adminform label {
	width: auto;
	min-width: 20px;
	clear: none;
	margin: 1px 10px 0 5px;
}
</style>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table cellpadding="1" cellspacing="1" border="0" width="90%">
			<tr>
				<td width="300">
					<table class="adminheading">
						<tr>
							<th nowrap="nowrap"
							    class="config"><?php echo JText::_('COM_MJ__MOBILE_JOOMLA_SETTINGS'); ?></th>
						</tr>
					</table>
				</td>
				<td width="500">
					<span class="componentheading">/ administrator / components / com_mobilejoomla / config.php <?php echo JText::_('COM_MJ__IS') ?>
						: <b><?php echo
							!JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php') ? '<font color="red">'.JText::_('COM_MJ__MISSING').'</font>'
							: is_writable(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php') ? '<font color="green">'.JText::_('COM_MJ__WRITEABLE').'</font>'
							: '<font color="red">'.JText::_('COM_MJ__UNWRITEABLE').'</font>' ?></b></span>
				</td>
			</tr>
		</table>
		<?php
		jimport('joomla.html.pane');
		/** @var JPane $tabs */
		$tabs =& JPane::getInstance();
		echo $tabs->startPane('configPane');
		echo $tabs->startPanel('Global settings', 'bot-page');
		?>
		<br/>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MJ__GENERAL_SETTINGS'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__CACHING'); ?>::<?php echo JText::_('COM_MJ__CACHING_DESC'); ?>"><?php echo JText::_('COM_MJ__CACHING'); ?></span>
					</td>
					<td><?php echo $lists['caching']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__BROWSER_CACHING'); ?>::<?php echo JText::_('COM_MJ__BROWSER_CACHING_DESC'); ?>"><?php echo JText::_('COM_MJ__BROWSER_CACHING'); ?></span>
					</td>
					<td><?php echo $lists['httpcaching']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__DOMAIN_SUPPORT'); ?>::<?php echo JText::_('COM_MJ__DOMAIN_SUPPORT_DESC'); ?>"><?php echo JText::_('COM_MJ__DOMAIN_SUPPORT'); ?></span>
					</td>
					<td><?php echo $lists['domains']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__PAGE_FOR_PC'); ?>::<?php echo JText::_('COM_MJ__PAGE_FOR_PC_DESC'); ?>"><?php echo JText::_('COM_MJ__PAGE_FOR_PC'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="80" name="mjconfig_pcpage"
					           value="<?php echo $MobileJoomla_Settings['pcpage']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__PC_TEMPLATE_WIDTH'); ?>::<?php echo JText::_('COM_MJ__PC_TEMPLATE_WIDTH_DESC'); ?>"><?php echo JText::_('COM_MJ__PC_TEMPLATE_WIDTH'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="6" name="mjconfig_templatewidth"
					           value="<?php echo $MobileJoomla_Settings['templatewidth']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__IMAGE_QUALITY'); ?>::<?php echo JText::_('COM_MJ__IMAGE_QUALITY_DESC'); ?>"><?php echo JText::_('COM_MJ__IMAGE_QUALITY'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="3" name="mjconfig_jpegquality"
					           value="<?php echo $MobileJoomla_Settings['jpegquality']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__DESKTOP_URL'); ?>::<?php echo JText::_('COM_MJ__DESKTOP_URL_DESC'); ?>"><?php echo JText::_('COM_MJ__DESKTOP_URL'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="30" name="mjconfig_desktop_url"
					           value="<?php echo $MobileJoomla_Settings['desktop_url']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MOBILE_SITENAME'); ?>::<?php echo JText::_('COM_MJ__MOBILE_SITENAME_DESC'); ?>"><?php echo JText::_('COM_MJ__MOBILE_SITENAME'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="30" name="mjconfig_mobile_sitename"
					           value="<?php echo $MobileJoomla_Settings['mobile_sitename']; ?>"/></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MJ__XHTMLMP_DOMAIN'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__DOMAIN_NAME'); ?>::<?php echo JText::_('COM_MJ__DOMAIN_NAME_XHTMLMP_DESC'); ?>"><?php echo JText::_('COM_MJ__DOMAIN_NAME'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="30" name="mjconfig_xhtmldomain"
					           value="<?php echo $MobileJoomla_Settings['xhtmldomain']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__REDIRECT_TO_DOMAIN'); ?>::<?php echo JText::_('COM_MJ__REDIRECT_TO_DOMAIN_XHTMLMP_DESC'); ?>"><?php echo JText::_('COM_MJ__REDIRECT_TO_DOMAIN'); ?></span>
					</td>
					<td><?php echo $lists['xhtmlredirect']; ?></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MJ__IPHONE_DOMAIN'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__DOMAIN_NAME'); ?>::<?php echo JText::_('COM_MJ__DOMAIN_NAME_IPHONE_DESC'); ?>"><?php echo JText::_('COM_MJ__DOMAIN_NAME'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="30" name="mjconfig_iphonedomain"
					           value="<?php echo $MobileJoomla_Settings['iphonedomain']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__REDIRECT_TO_DOMAIN'); ?>::<?php echo JText::_('COM_MJ__REDIRECT_TO_DOMAIN_IPHONE_DESC'); ?>"><?php echo JText::_('COM_MJ__REDIRECT_TO_DOMAIN'); ?></span>
					</td>
					<td><?php echo $lists['iphoneredirect']; ?></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MJ__WML_DOMAIN'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__DOMAIN_NAME'); ?>::<?php echo JText::_('COM_MJ__DOMAIN_NAME_WML_DESC'); ?>"><?php echo JText::_('COM_MJ__DOMAIN_NAME'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="30" name="mjconfig_wapdomain"
					           value="<?php echo $MobileJoomla_Settings['wapdomain']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__REDIRECT_TO_DOMAIN'); ?>::<?php echo JText::_('COM_MJ__REDIRECT_TO_DOMAIN_WML_DESC'); ?>"><?php echo JText::_('COM_MJ__REDIRECT_TO_DOMAIN'); ?></span>
					</td>
					<td><?php echo $lists['wapredirect']; ?></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MJ__CHTML_DOMAIN'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__DOMAIN_NAME'); ?>::<?php echo JText::_('COM_MJ__DOMAIN_NAME_CHTML_DESC'); ?>"><?php echo JText::_('COM_MJ__DOMAIN_NAME'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="30" name="mjconfig_imodedomain"
					           value="<?php echo $MobileJoomla_Settings['imodedomain']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__REDIRECT_TO_DOMAIN'); ?>::<?php echo JText::_('COM_MJ__REDIRECT_TO_DOMAIN_CHTML_DESC'); ?>"><?php echo JText::_('COM_MJ__REDIRECT_TO_DOMAIN'); ?></span>
					</td>
					<td><?php echo $lists['imoderedirect']; ?></td>
				</tr>
				</tbody>
			</table>
		</fieldset>


		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel('Smartphone (XHTML)', 'pda-page');
		?>
		<br/>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MJ__XHTMLMP_SETTINGS'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__TEMPLATE_NAME'); ?>::<?php echo JText::_('COM_MJ__TEMPLATE_NAME_XHTMLMP_DESC'); ?>"><?php echo JText::_('COM_MJ__TEMPLATE_NAME'); ?></span>
					</td>
					<td><?php echo $lists['xhtmltemplate']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__HOMEPAGE'); ?>::<?php echo JText::_('COM_MJ__HOMEPAGE_DESC'); ?>"><?php echo JText::_('COM_MJ__HOMEPAGE'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="80" name="mjconfig_xhtmlhomepage" id="mjconfig_xhtmlhomepage"
					           value="<?php echo $MobileJoomla_Settings['xhtmlhomepage']; ?>"/></td>
				</tr>
				<tr><td></td><td><?php echo getMenuList($lists['menuoptions'], 'mjconfig_xhtmlhomepage', $MobileJoomla_Settings['xhtmlhomepage']); ?></td></tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__IMAGE_ADAPTATION_METHOD'); ?>::<?php echo JText::_('COM_MJ__IMAGE_ADAPTATION_METHOD_DESC'); ?>"><?php echo JText::_('COM_MJ__IMAGE_ADAPTATION_METHOD'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_img']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__STYLE_IMAGE_SIZE'); ?>::<?php echo JText::_('COM_MJ__STYLE_IMAGE_SIZE_DESC'); ?>"><?php echo JText::_('COM_MJ__STYLE_IMAGE_SIZE'); ?></span></td>
					<td><?php echo $lists['tmpl_xhtml_img_addstyles']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_MJ__GZIP_COMPRESSION'); ?></td>
					<td><?php echo $lists['xhtmlgzip']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_MJ__REMOVE_UNSUPPORTED_TAGS'); ?></td>
					<td><?php echo $lists['tmpl_xhtml_removetags']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_MJ__REMOVE_SCRIPT_TAGS'); ?></td>
					<td><?php echo $lists['tmpl_xhtml_removescripts']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__CONVERT_HTMLENTITIES'); ?>::<?php echo JText::_('COM_MJ__CONVERT_HTMLENTITIES_DESC'); ?>"><?php echo JText::_('COM_MJ__CONVERT_HTMLENTITIES'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_entitydecode']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__CONTENT_TYPE'); ?>::<?php echo JText::_('COM_MJ__CONTENT_TYPE_DESC'); ?>"><?php echo JText::_('COM_MJ__CONTENT_TYPE'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_contenttype']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__DECREASE_IMAGE_WIDTH'); ?>::<?php echo JText::_('COM_MJ__DECREASE_IMAGE_WIDTH_DESC'); ?>"><?php echo JText::_('COM_MJ__DECREASE_IMAGE_WIDTH'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="10" name="mjconfig_xhtml_buffer_width"
					           value="<?php echo $MobileJoomla_Settings['xhtml_buffer_width']; ?>"/></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MJ__XHTMLMP_TEMPLATE_SETTINGS'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_1'); ?>::<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_1_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_1'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_header1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_2'); ?>::<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_2_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_2'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_header2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_3'); ?>::<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_3_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_3'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_header3']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__SHOW_PATHWAY'); ?>::<?php echo JText::_('COM_MJ__SHOW_PATHWAY_DESC'); ?>"><?php echo JText::_('COM_MJ__SHOW_PATHWAY'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_pathway']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__PATHWAY_ON_HOMEPAGE'); ?>::<?php echo JText::_('COM_MJ__PATHWAY_ON_HOMEPAGE_DESC'); ?>"><?php echo JText::_('COM_MJ__PATHWAY_ON_HOMEPAGE'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_pathwayhome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_1'); ?>::<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_1_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_1'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_middle1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_2'); ?>::<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_2_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_2'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_middle2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_3'); ?>::<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_3_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_3'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_middle3']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__COMPONENT_ON_HOMEPAGE'); ?>::<?php echo JText::_('COM_MJ__COMPONENT_ON_HOMEPAGE_DESC'); ?>"><?php echo JText::_('COM_MJ__COMPONENT_ON_HOMEPAGE'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_componenthome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_1'); ?>::<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_1_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_1'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_footer1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_2'); ?>::<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_2_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_2'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_footer2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_3'); ?>::<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_3_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_3'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_footer3']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__SHOW_JOOMLA_FOOTER'); ?>::<?php echo JText::_('COM_MJ__SHOW_JOOMLA_FOOTER_DESC'); ?>"><?php echo JText::_('COM_MJ__SHOW_JOOMLA_FOOTER'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_jfooter']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__USE_HEAD'); ?>::<?php echo JText::_('COM_MJ__USE_HEAD'); ?>"><?php echo JText::_('COM_MJ__USE_HEAD'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_simplehead']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__EXTENDED_EDITORS'); ?>::<?php echo JText::_('COM_MJ__EXTENDED_EDITORS_DESC'); ?>"><?php echo JText::_('COM_MJ__EXTENDED_EDITORS'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_allowextedit']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__EMBED_CSS'); ?>::<?php echo JText::_('COM_MJ__EMBED_CSS_DESC'); ?>"><?php echo JText::_('COM_MJ__EMBED_CSS'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_embedcss']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__USE_XMLHEAD'); ?>::<?php echo JText::_('COM_MJ__USE_XMLHEAD_DESC'); ?>"><?php echo JText::_('COM_MJ__USE_XMLHEAD'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_xmlhead']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_MJ__DOCTYPE_HEAD'); ?></td>
					<td><?php echo $lists['tmpl_xhtml_doctype']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__XMLNS_IN_HEAD'); ?>::<?php echo JText::_('COM_MJ__XMLNS_IN_HEAD_DESC'); ?>"><?php echo JText::_('COM_MJ__XMLNS_IN_HEAD'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_xmlns']; ?></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel('iPhone', 'iphone-page');
		?>
		<br/>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MJ__IPHONE_SETTINGS'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__TEMPLATE_NAME'); ?>::<?php echo JText::_('COM_MJ__TEMPLATE_NAME_IPHONE_DESC'); ?>"><?php echo JText::_('COM_MJ__TEMPLATE_NAME'); ?></span>
					</td>
					<td><?php echo $lists['iphonetemplate']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__HOMEPAGE'); ?>::<?php echo JText::_('COM_MJ__HOMEPAGE_DESC'); ?>"><?php echo JText::_('COM_MJ__HOMEPAGE'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="80" name="mjconfig_iphonehomepage" id="mjconfig_iphonehomepage"
					           value="<?php echo $MobileJoomla_Settings['iphonehomepage']; ?>"/></td>
				</tr>
				<tr><td></td><td><?php echo getMenuList($lists['menuoptions'], 'mjconfig_iphonehomepage', $MobileJoomla_Settings['iphonehomepage']); ?></td></tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__IMAGE_ADAPTATION_METHOD'); ?>::<?php echo JText::_('COM_MJ__IMAGE_ADAPTATION_METHOD_DESC'); ?>"><?php echo JText::_('COM_MJ__IMAGE_ADAPTATION_METHOD'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_img']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__STYLE_IMAGE_SIZE'); ?>::<?php echo JText::_('COM_MJ__STYLE_IMAGE_SIZE_DESC'); ?>"><?php echo JText::_('COM_MJ__STYLE_IMAGE_SIZE'); ?></span></td>
					<td><?php echo $lists['tmpl_iphone_img_addstyles']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_MJ__GZIP_COMPRESSION'); ?></td>
					<td><?php echo $lists['iphonegzip']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_MJ__REMOVE_UNSUPPORTED_TAGS'); ?></td>
					<td><?php echo $lists['tmpl_iphone_removetags']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__DECREASE_IMAGE_WIDTH'); ?>::<?php echo JText::_('COM_MJ__DECREASE_IMAGE_WIDTH_DESC'); ?>"><?php echo JText::_('COM_MJ__DECREASE_IMAGE_WIDTH'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="10" name="mjconfig_iphone_buffer_width"
					           value="<?php echo $MobileJoomla_Settings['iphone_buffer_width']; ?>"/></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MJ__IPHONE_TEMPLATE_SETTINGS'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_1'); ?>::<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_1_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_1'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_header1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_2'); ?>::<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_2_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_2'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_header2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_3'); ?>::<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_3_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_3'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_header3']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__SHOW_PATHWAY'); ?>::<?php echo JText::_('COM_MJ__SHOW_PATHWAY_DESC'); ?>"><?php echo JText::_('COM_MJ__SHOW_PATHWAY'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_pathway']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__PATHWAY_ON_HOMEPAGE'); ?>::<?php echo JText::_('COM_MJ__PATHWAY_ON_HOMEPAGE_DESC'); ?>"><?php echo JText::_('COM_MJ__PATHWAY_ON_HOMEPAGE'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_pathwayhome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_1'); ?>::<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_1_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_1'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_middle1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_2'); ?>::<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_2_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_2'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_middle2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_3'); ?>::<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_3_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_3'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_middle3']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__COMPONENT_ON_HOMEPAGE'); ?>::<?php echo JText::_('COM_MJ__COMPONENT_ON_HOMEPAGE_DESC'); ?>"><?php echo JText::_('COM_MJ__COMPONENT_ON_HOMEPAGE'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_componenthome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_1'); ?>::<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_1_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_1'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_footer1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_2'); ?>::<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_2_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_2'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_footer2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_3'); ?>::<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_3_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_3'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_footer3']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__SHOW_JOOMLA_FOOTER'); ?>::<?php echo JText::_('COM_MJ__SHOW_JOOMLA_FOOTER_DESC'); ?>"><?php echo JText::_('COM_MJ__SHOW_JOOMLA_FOOTER'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_jfooter']; ?></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel('WAP', 'wap-page');
		?>
		<br/>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MJ__WML_SETTINGS'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__TEMPLATE_NAME'); ?>::<?php echo JText::_('COM_MJ__TEMPLATE_NAME_WML_DESC'); ?>"><?php echo JText::_('COM_MJ__TEMPLATE_NAME'); ?></span>
					</td>
					<td><?php echo $lists['waptemplate']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__HOMEPAGE'); ?>::<?php echo JText::_('COM_MJ__HOMEPAGE_DESC'); ?>"><?php echo JText::_('COM_MJ__HOMEPAGE'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="80" name="mjconfig_waphomepage" id="mjconfig_waphomepage"
					           value="<?php echo $MobileJoomla_Settings['waphomepage']; ?>"/></td>
				</tr>
				<tr><td></td><td><?php echo getMenuList($lists['menuoptions'], 'mjconfig_waphomepage', $MobileJoomla_Settings['waphomepage']); ?></td></tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__IMAGE_ADAPTATION_METHOD'); ?>::<?php echo JText::_('COM_MJ__IMAGE_ADAPTATION_METHOD_DESC'); ?>"><?php echo JText::_('COM_MJ__IMAGE_ADAPTATION_METHOD'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_img']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_MJ__GZIP_COMPRESSION'); ?></td>
					<td><?php echo $lists['wapgzip']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_MJ__REMOVE_UNSUPPORTED_TAGS'); ?></td>
					<td><?php echo $lists['tmpl_wml_removetags']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__CONVERT_HTMLENTITIES'); ?>::<?php echo JText::_('COM_MJ__CONVERT_HTMLENTITIES_DESC'); ?>"><?php echo JText::_('COM_MJ__CONVERT_HTMLENTITIES'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_entitydecode']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_MJ__DOCTYPE_HEAD'); ?></td>
					<td><?php echo $lists['tmpl_wml_doctype']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__DECREASE_IMAGE_WIDTH'); ?>::<?php echo JText::_('COM_MJ__DECREASE_IMAGE_WIDTH_DESC'); ?>"><?php echo JText::_('COM_MJ__DECREASE_IMAGE_WIDTH'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="10" name="mjconfig_wml_buffer_width"
					           value="<?php echo $MobileJoomla_Settings['wml_buffer_width']; ?>"/></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MJ__WML_TEMPLATE_SETTINGS'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_1'); ?>::<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_1_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_1'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_header1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_2'); ?>::<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_2_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_2'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_header2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_3'); ?>::<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_3_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_3'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_header3']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__SHOW_PATHWAY'); ?>::<?php echo JText::_('COM_MJ__SHOW_PATHWAY_DESC'); ?>"><?php echo JText::_('COM_MJ__SHOW_PATHWAY'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_pathway']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__PATHWAY_ON_HOMEPAGE'); ?>::<?php echo JText::_('COM_MJ__PATHWAY_ON_HOMEPAGE_DESC'); ?>"><?php echo JText::_('COM_MJ__PATHWAY_ON_HOMEPAGE'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_pathwayhome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_1'); ?>::<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_1_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_1'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_middle1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_2'); ?>::<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_2_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_2'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_middle2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_3'); ?>::<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_3_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_3'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_middle3']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__COMPONENT_ON_HOMEPAGE'); ?>::<?php echo JText::_('COM_MJ__COMPONENT_ON_HOMEPAGE_DESC'); ?>"><?php echo JText::_('COM_MJ__COMPONENT_ON_HOMEPAGE'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_componenthome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_1'); ?>::<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_1_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_1'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_footer1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_2'); ?>::<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_2_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_2'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_footer2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_3'); ?>::<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_3_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_3'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_footer3']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__SHOW_JOOMLA_FOOTER'); ?>::<?php echo JText::_('COM_MJ__SHOW_JOOMLA_FOOTER_DESC'); ?>"><?php echo JText::_('COM_MJ__SHOW_JOOMLA_FOOTER'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_jfooter']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_WMLCARDS'); ?>::<?php echo JText::_('COM_MJ__MODULE_WMLCARDS_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_WMLCARDS'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wml_cards']; ?></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel('iMode', 'imode-page');
		?>
		<br/>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MJ__CHTML_SETTINGS'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__TEMPLATE_NAME'); ?>::<?php echo JText::_('COM_MJ__TEMPLATE_NAME_CHTML_DESC'); ?>"><?php echo JText::_('COM_MJ__TEMPLATE_NAME'); ?></span>
					</td>
					<td><?php echo $lists['imodetemplate']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__HOMEPAGE'); ?>::<?php echo JText::_('COM_MJ__HOMEPAGE_DESC'); ?>"><?php echo JText::_('COM_MJ__HOMEPAGE'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="80" name="mjconfig_imodehomepage" id="mjconfig_imodehomepage"
					           value="<?php echo $MobileJoomla_Settings['imodehomepage']; ?>"/></td>
				</tr>
				<tr><td></td><td><?php echo getMenuList($lists['menuoptions'], 'mjconfig_imodehomepage', $MobileJoomla_Settings['imodehomepage']); ?></td></tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__IMAGE_ADAPTATION_METHOD'); ?>::<?php echo JText::_('COM_MJ__IMAGE_ADAPTATION_METHOD_DESC'); ?>"><?php echo JText::_('COM_MJ__IMAGE_ADAPTATION_METHOD'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_img']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_MJ__GZIP_COMPRESSION'); ?></td>
					<td><?php echo $lists['imodegzip']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_MJ__REMOVE_UNSUPPORTED_TAGS'); ?></td>
					<td><?php echo $lists['tmpl_chtml_removetags']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__CONVERT_HTMLENTITIES'); ?>::<?php echo JText::_('COM_MJ__CONVERT_HTMLENTITIES_DESC'); ?>"><?php echo JText::_('COM_MJ__CONVERT_HTMLENTITIES'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_entitydecode']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__DECREASE_IMAGE_WIDTH'); ?>::<?php echo JText::_('COM_MJ__DECREASE_IMAGE_WIDTH_DESC'); ?>"><?php echo JText::_('COM_MJ__DECREASE_IMAGE_WIDTH'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="10" name="mjconfig_chtml_buffer_width"
					           value="<?php echo $MobileJoomla_Settings['chtml_buffer_width']; ?>"/></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MJ__CHTML_TEMPLATE_SETTINGS'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_1'); ?>::<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_1_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_1'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_header1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_2'); ?>::<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_2_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_2'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_header2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_3'); ?>::<?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_3_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_ABOVE_PATHWAY_3'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_header3']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__SHOW_PATHWAY'); ?>::<?php echo JText::_('COM_MJ__SHOW_PATHWAY_DESC'); ?>"><?php echo JText::_('COM_MJ__SHOW_PATHWAY'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_pathway']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__PATHWAY_ON_HOMEPAGE'); ?>::<?php echo JText::_('COM_MJ__PATHWAY_ON_HOMEPAGE_DESC'); ?>"><?php echo JText::_('COM_MJ__PATHWAY_ON_HOMEPAGE'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_pathwayhome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_1'); ?>::<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_1_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_1'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_middle1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_2'); ?>::<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_2_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_2'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_middle2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_3'); ?>::<?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_3_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BETWEEN_PATHWAY_COMPONENT_3'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_middle3']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__COMPONENT_ON_HOMEPAGE'); ?>::<?php echo JText::_('COM_MJ__COMPONENT_ON_HOMEPAGE_DESC'); ?>"><?php echo JText::_('COM_MJ__COMPONENT_ON_HOMEPAGE'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_componenthome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_1'); ?>::<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_1_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_1'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_footer1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_2'); ?>::<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_2_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_2'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_footer2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_3'); ?>::<?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_3_DESC'); ?>"><?php echo JText::_('COM_MJ__MODULE_BELOW_COMPONENT_3'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_footer3']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('COM_MJ__SHOW_JOOMLA_FOOTER'); ?>::<?php echo JText::_('COM_MJ__SHOW_JOOMLA_FOOTER_DESC'); ?>"><?php echo JText::_('COM_MJ__SHOW_JOOMLA_FOOTER'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_chtml_jfooter']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_MJ__DOCTYPE_HEAD'); ?></td>
					<td><?php echo $lists['tmpl_chtml_doctype']; ?></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<?php
		echo $tabs->endPanel();
		echo $tabs->endPane();
		echo JHTML::_('form.token');
		?>
		<input type="hidden" name="option" value="<?php echo JRequest::getString('option'); ?>"/>
		<input type="hidden" name="task" value=""/>
		</form>
<?php if(substr(JVERSION,0,3) == '1.5') : ?>
		<script type="text/javascript" src="<?php echo JURI::root(true);?>/includes/js/overlib_mini.js"></script>
<?php endif; ?>
		<?php

	}

	function showabout()
	{
		$version = HTML_mobilejoomla::getMJVersion();
		?>
		<!--fieldset class="adminform"-->
		<!--legend><?php echo JText::_('COM_MJ__MOBILEJOOMLA'); ?></legend-->
		<table class="admintable" cellspacing="1">
			<tbody>
			<tr>
				<td>
					<h2>MobileJoomla <?php echo $version;?></h2>
					<?php echo JText::_('COM_MJ__MOBILEJOOMLA_DESCRIPTION'); ?>
					<br/>
					<br/>
					<a href="http://www.mobilejoomla.com/"><?php echo JText::_('COM_MJ__VISIT_MOBILEJOOMLA'); ?></a>
					<br/>
					<br/>
					<?php HTML_mobilejoomla::showUpdateNotification(); ?>
					<div id="mjnoupdate">
						<h2><?php echo JText::_('COM_MJ__NO_UPDATES');?></h2>
						<?php echo JText::_('COM_MJ__MOBILEJOOMLA_UPTODATE');?>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
		<!--/fieldset-->
		<?php
	}
}
