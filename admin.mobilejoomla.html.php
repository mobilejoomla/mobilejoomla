<?php
/**
 * Kuneri Mobile Joomla! for Joomla!1.5
 * http://www.mobilejoomla.com/
 *
 * @version		0.9.0
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	Copyright (C) 2008-2009 Kuneri Ltd. All rights reserved.
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class HTML_mobilejoomla
{
	function getMJVersion()
	{
		$manifest = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.xml';
		if(is_file($manifest))
		{
			jimport('domit.xml_domit_lite_include');
			$xmlDoc = new DOMIT_Lite_Document();
			$xmlDoc->resolveErrors(false);
			if($xmlDoc->loadXML($manifest, false, true))
			{
				$root = &$xmlDoc->documentElement;
				$element = &$root->getElementsByPath('version', 1);
				$version = $element ? $element->getText() : '';
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
<style>#mjupdate{display:none}</style>
<link rel="stylesheet" type="text/css" href="http://www.mobilejoomla.com/checker.php?v=<?php echo urlencode($version); ?>" />
<?php
		}
	}
	function showconfig( &$lists, $MobileJoomla_Settings )
	{
		global $option;
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.switcher');
		HTML_mobilejoomla::CheckForUpdate();
?>
<form action="index.php" method="post" name="adminForm">
<table cellpadding="1" cellspacing="1" border="0" width="90%">
<tr>
	<td width="300"><table class="adminheading"><tr><th nowrap="nowrap" class="config"><?php echo JText::_('Kuneri Mobile Joomla! Settings'); ?></th></tr></table></td>
	<td width="500">
		<span class="componentheading">/ administrator / components / com_mobilejoomla / config.php <?php echo JText::_('is') ?> : <b><?php echo is_writable( 'components/com_mobilejoomla/config.php' ) ? '<font color="green">'.JText::_('Writeable').'</font>' : '<font color="red">'.JText::_('Unwriteable').'</font>' ?></b></span>
	</td>
</tr>
</table>
<?php
		jimport('joomla.html.pane');
		$tabs =& JPane::getInstance();
		echo $tabs->startPane("configPane");
		echo $tabs->startPanel("Global settings","bot-page");
?>
<br/>
<fieldset class="adminform">
<legend><?php echo JText::_('General Settings'); ?></legend>
<table class="admintable" cellspacing="1">
<tbody>
<tr>
	<td class="key"><?php echo JText::_('User agent checking'); ?></td>
	<td><?php echo $lists['useragent']; ?> <?php echo JText::_('(note that image rescaling works with WURFL only)'); ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Domain (multisite) support'); ?>::<?php echo JText::_('Show mobile versions on aliases (like pda.site.name, wap.site.name).'); ?>"><?php echo JText::_('Domain (multisite) support'); ?></span></td>
	<td><?php echo $lists['domains']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Show this page for PC'); ?>::<?php echo JText::_('Use this setting to allow mobile devices only. For PC browser either specified page will be displayed or browser will be redirected to specified external site. Keep this parameter empty to allow visit your site from PC.'); ?>"><?php echo JText::_('Show this page for PC'); ?></span></td>
	<td><input class="text_area" type="text" size="80" name="mjconfig_pcpage" value="<?php echo $MobileJoomla_Settings['pcpage']; ?>" /></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('PC template width'); ?>::<?php echo JText::_('Approximate width of the PC template (used in image \'proportional\' processing).'); ?>"><?php echo JText::_('PC template width'); ?></span></td>
	<td><input class="text_area" type="text" size="6" name="mjconfig_templatewidth" value="<?php echo $MobileJoomla_Settings['templatewidth']; ?>" /></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Rescaled image quality (0-100)'); ?>::<?php echo JText::_('Quality of rescaled jpeg images.'); ?>"><?php echo JText::_('Rescaled image quality (0-100)'); ?></span></td>
	<td><input class="text_area" type="text" size="3" name="mjconfig_jpegquality" value="<?php echo $MobileJoomla_Settings['jpegquality']; ?>" /></td>
</tr>
</tbody>
</table>
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_('Smartphone (XHTML-MP/WAP2.0) Domain'); ?></legend>
<table class="admintable" cellspacing="1">
<tbody>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Domain name'); ?>::<?php echo JText::_('Domain name, e.g. pda.yoursite.com.'); ?>"><?php echo JText::_('Domain name'); ?></span></td>
	<td><input class="text_area" type="text" size="30" name="mjconfig_xhtmldomain" value="<?php echo $MobileJoomla_Settings['xhtmldomain']; ?>" /></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Redirect to domain'); ?>::<?php echo JText::_('Redirect xhtmlmp/wap2.0 devices to this domain.'); ?>"><?php echo JText::_('Redirect to domain'); ?></span></td>
	<td><?php echo $lists['xhtmlredirect']; ?></td>
</tr>
</tbody>
</table>
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_('iPhone/iPod Domain'); ?></legend>
<table class="admintable" cellspacing="1">
<tbody>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Domain name'); ?>::<?php echo JText::_('Domain name, e.g. iphone.yoursite.com.'); ?>"><?php echo JText::_('Domain name'); ?></span></td>
	<td><input class="text_area" type="text" size="30" name="mjconfig_iphonedomain" value="<?php echo $MobileJoomla_Settings['iphonedomain']; ?>" /></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Redirect to domain'); ?>::<?php echo JText::_('Redirect iPhone/iPod devices to this domain.'); ?>"><?php echo JText::_('Redirect to domain'); ?></span></td>
	<td><?php echo $lists['iphoneredirect']; ?></td>
</tr>
</tbody>
</table>
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_('WAP (WML) Domain'); ?></legend>
<table class="admintable" cellspacing="1">
<tbody>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Domain name'); ?>::<?php echo JText::_('Domain name, e.g. wap.yoursite.com.'); ?>"><?php echo JText::_('Domain name'); ?></span></td>
	<td><input class="text_area" type="text" size="30" name="mjconfig_wapdomain" value="<?php echo $MobileJoomla_Settings['wapdomain']; ?>" /></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Redirect to domain'); ?>::<?php echo JText::_('Redirect wap/wml devices to this domain.'); ?>"><?php echo JText::_('Redirect to domain'); ?></span></td>
	<td><?php echo $lists['wapredirect']; ?></td>
</tr>
</tbody>
</table>
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_('iMode (CHTML) Domain'); ?></legend>
<table class="admintable" cellspacing="1">
<tbody>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Domain name'); ?>::<?php echo JText::_('Domain name, e.g. imode.yoursite.com.'); ?>"><?php echo JText::_('Domain name'); ?></span></td>
	<td><input class="text_area" type="text" size="30" name="mjconfig_imodedomain" value="<?php echo $MobileJoomla_Settings['imodedomain']; ?>" /></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Redirect to domain'); ?>::<?php echo JText::_('Redirect imode devices to this domain.'); ?>"><?php echo JText::_('Redirect to domain'); ?></span></td>
	<td><?php echo $lists['imoderedirect']; ?></td>
</tr>
</tbody>
</table>
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_('WURFL Settings'); ?></legend>
<table class="admintable" cellspacing="1">
<tbody>
<tr>
	<td class="key"><?php echo JText::_('WURFL cache'); ?></td>
	<td><?php echo $lists['wurflcache']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('User-agents to cache'); ?>::<?php echo JText::_('Maximum number of useragents for fast cache.'); ?>"><?php echo JText::_('User-agents to cache'); ?></span></td>
	<td><input class="text_area" type="text" size="6" name="mjconfig_wurfluacache" value="<?php echo $MobileJoomla_Settings['wurfluacache']; ?>" /></td>
</tr>
</tbody>
</table>
</fieldset>

<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel("Smartphone (XHTML)","pda-page");
?>
<br/>
<fieldset class="adminform">
<legend><?php echo JText::_('XHTML-MP/WAP2.0 Settings'); ?></legend>
<table class="admintable" cellspacing="1">
<tbody>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Template name'); ?>::<?php echo JText::_('XHTMLMP/WAP2.0 template name.'); ?>"><?php echo JText::_('Template name'); ?></span></td>
	<td><?php echo $lists['xhtmltemplate']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Homepage'); ?>::<?php echo JText::_('Set this page as a homepage.'); ?>"><?php echo JText::_('Homepage'); ?></span></td>
	<td><input class="text_area" type="text" size="50" name="mjconfig_xhtmlhomepage" value="<?php echo $MobileJoomla_Settings['xhtmlhomepage']; ?>" /></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Image adaptation method'); ?>::<?php echo JText::_('Remove or resize images.'); ?>"><?php echo JText::_('Image adaptation method'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_img']; ?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('Gzip compression'); ?></td>
	<td><?php echo $lists['xhtmlgzip']; ?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('Remove unsupported tags'); ?></td>
	<td><?php echo $lists['tmpl_xhtml_removetags']; ?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('Remove script tags'); ?></td>
	<td><?php echo $lists['tmpl_xhtml_removescripts']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Convert html-entities to symbols'); ?>::<?php echo JText::_('Convert html-entities to symbols using html_entity_decode function.'); ?>"><?php echo JText::_('Convert html-entities to symbols'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_entitydecode']; ?></td>
</tr>
<tr style="display:none">
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Content-type'); ?>::<?php echo JText::_('Output Content-type header.'); ?>"><?php echo JText::_('Content-type'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_contenttype']; ?></td>
</tr>
</tbody>
</table>
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_('XHTML-MP/WAP2.0 Mobile Joomla Template API Settings'); ?></legend>
<table class="admintable" cellspacing="1">
<tbody>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('1st module position above pathway'); ?>::<?php echo JText::_('Name of the 1st module position above pathway.'); ?>"><?php echo JText::_('1st module position above pathway'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_header1']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('2nd module position above pathway'); ?>::<?php echo JText::_('Name of the 2nd module position above pathway.'); ?>"><?php echo JText::_('2nd module position above pathway'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_header2']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Show pathway'); ?>::<?php echo JText::_('Show pathway on the pages.'); ?>"><?php echo JText::_('Show pathway'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_pathway']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Pathway on home page'); ?>::<?php echo JText::_('Show pathway on home (main) page.'); ?>"><?php echo JText::_('Pathway on home page'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_pathwayhome']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('1st module position between pathway and component'); ?>::<?php echo JText::_('Name of the 1st module position between pathway and component.'); ?>"><?php echo JText::_('1st module position between pathway and component'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_middle1']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('2nd module position between pathway and component'); ?>::<?php echo JText::_('Name of the 2nd module position between pathway and component.'); ?>"><?php echo JText::_('2nd module position between pathway and component'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_middle2']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Component on home page'); ?>::<?php echo JText::_('Show component on home (main) page.'); ?>"><?php echo JText::_('Component on home page'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_componenthome']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('1st module position below component'); ?>::<?php echo JText::_('Name of the 1st module position below component.'); ?>"><?php echo JText::_('1st module position below component'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_footer1']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('2nd module position below component'); ?>::<?php echo JText::_('Name of the 2nd module position below component.'); ?>"><?php echo JText::_('2nd module position below component'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_footer2']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Show Joomla! footer'); ?>::<?php echo JText::_('Show site title and Joomla! licence in footer.'); ?>"><?php echo JText::_('Show Joomla! footer'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_jfooter']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Use head'); ?>::<?php echo JText::_('Use standard or simplified &amp;lt;head&amp;gt; block.'); ?>"><?php echo JText::_('Use head'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_simplehead']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Allow extended editors'); ?>::<?php echo JText::_('Allow to load extended editors (TinyMCE, etc.).'); ?>"><?php echo JText::_('Allow extended editors'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_allowextedit']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Embed CSS'); ?>::<?php echo JText::_('Embed css-style into page.'); ?>"><?php echo JText::_('Embed CSS'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_embedcss']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Use xml-head'); ?>::<?php echo JText::_('Start html with xml.'); ?>"><?php echo JText::_('Use xml-head'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_xmlhead']; ?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('DOCTYPE-head'); ?></td>
	<td><?php echo $lists['tmpl_xhtml_doctype']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('xmlns in html-head'); ?>::<?php echo JText::_('Use html xmlns instead of html.'); ?>"><?php echo JText::_('xmlns in html-head'); ?></span></td>
	<td><?php echo $lists['tmpl_xhtml_xmlns']; ?></td>
</tr>
</tbody>
</table>
</fieldset>

<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel("iPhone","iphone-page");
?>
<br/>
<fieldset class="adminform">
<legend><?php echo JText::_('iPhone/iPod Settings'); ?></legend>
<table class="admintable" cellspacing="1">
<tbody>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Template name'); ?>::<?php echo JText::_('iPhone template name.'); ?>"><?php echo JText::_('Template name'); ?></span></td>
	<td><?php echo $lists['iphonetemplate']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Homepage'); ?>::<?php echo JText::_('Set this page as a homepage.'); ?>"><?php echo JText::_('Homepage'); ?></span></td>
	<td><input class="text_area" type="text" size="50" name="mjconfig_iphonehomepage" value="<?php echo $MobileJoomla_Settings['iphonehomepage']; ?>" /></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Image adaptation method'); ?>::<?php echo JText::_('Remove or resize images.'); ?>"><?php echo JText::_('Image adaptation method'); ?></span></td>
	<td><?php echo $lists['tmpl_iphone_img']; ?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('Gzip compression'); ?></td>
	<td><?php echo $lists['iphonegzip']; ?></td>
</tr>
</tbody>
</table>
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_('iPhone/iPod Mobile Joomla Template API Settings'); ?></legend>
<table class="admintable" cellspacing="1">
<tbody>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('1st module position above pathway'); ?>::<?php echo JText::_('Name of the 1st module position above pathway.'); ?>"><?php echo JText::_('1st module position above pathway'); ?></span></td>
	<td><?php echo $lists['tmpl_iphone_header1']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('2nd module position above pathway'); ?>::<?php echo JText::_('Name of the 2nd module position above pathway.'); ?>"><?php echo JText::_('2nd module position above pathway'); ?></span></td>
	<td><?php echo $lists['tmpl_iphone_header2']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Show pathway'); ?>::<?php echo JText::_('Show pathway on the pages.'); ?>"><?php echo JText::_('Show pathway'); ?></span></td>
	<td><?php echo $lists['tmpl_iphone_pathway']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Pathway on home page'); ?>::<?php echo JText::_('Show pathway on home (main) page.'); ?>"><?php echo JText::_('Pathway on home page'); ?></span></td>
	<td><?php echo $lists['tmpl_iphone_pathwayhome']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('1st module position between pathway and component'); ?>::<?php echo JText::_('Name of the 1st module position between pathway and component.'); ?>"><?php echo JText::_('1st module position between pathway and component'); ?></span></td>
	<td><?php echo $lists['tmpl_iphone_middle1']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('2nd module position between pathway and component'); ?>::<?php echo JText::_('Name of the 2nd module position between pathway and component.'); ?>"><?php echo JText::_('2nd module position between pathway and component'); ?></span></td>
	<td><?php echo $lists['tmpl_iphone_middle2']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Component on home page'); ?>::<?php echo JText::_('Show component on home (main) page.'); ?>"><?php echo JText::_('Component on home page'); ?></span></td>
	<td><?php echo $lists['tmpl_iphone_componenthome']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('1st module position below component'); ?>::<?php echo JText::_('Name of the 1st module position below component.'); ?>"><?php echo JText::_('1st module position below component'); ?></span></td>
	<td><?php echo $lists['tmpl_iphone_footer1']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('2nd module position below component'); ?>::<?php echo JText::_('Name of the 2nd module position below component.'); ?>"><?php echo JText::_('2nd module position below component'); ?></span></td>
	<td><?php echo $lists['tmpl_iphone_footer2']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Show Joomla! footer'); ?>::<?php echo JText::_('Show site title and Joomla! licence in footer.'); ?>"><?php echo JText::_('Show Joomla! footer'); ?></span></td>
	<td><?php echo $lists['tmpl_iphone_jfooter']; ?></td>
</tr>
</tbody>
</table>
</fieldset>

<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel("WAP","wap-page");
?>
<br/>
<fieldset class="adminform">
<legend><?php echo JText::_('WAP (WML) Settings'); ?></legend>
<table class="admintable" cellspacing="1">
<tbody>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Template name'); ?>::<?php echo JText::_('WAP/WML template name.'); ?>"><?php echo JText::_('Template name'); ?></span></td>
	<td><?php echo $lists['waptemplate']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Homepage'); ?>::<?php echo JText::_('Set this page as a homepage.'); ?>"><?php echo JText::_('Homepage'); ?></span></td>
	<td><input class="text_area" type="text" size="50" name="mjconfig_waphomepage" value="<?php echo $MobileJoomla_Settings['waphomepage']; ?>" /></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Image adaptation method'); ?>::<?php echo JText::_('Remove or resize images.'); ?>"><?php echo JText::_('Image adaptation method'); ?></span></td>
	<td><?php echo $lists['tmpl_wap_img']; ?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('Gzip compression'); ?></td>
	<td><?php echo $lists['wapgzip']; ?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('Remove unsupported tags'); ?></td>
	<td><?php echo $lists['tmpl_wap_removetags']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Convert html-entities to symbols'); ?>::<?php echo JText::_('Convert html-entities to symbols using html_entity_decode function.'); ?>"><?php echo JText::_('Convert html-entities to symbols'); ?></span></td>
	<td><?php echo $lists['tmpl_wap_entitydecode']; ?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('DOCTYPE-head'); ?></td>
	<td><?php echo $lists['tmpl_wap_doctype']; ?></td>
</tr>
</tbody>
</table>
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_('WAP (WML) Mobile Joomla Template API Settings'); ?></legend>
<table class="admintable" cellspacing="1">
<tbody>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Module position above pathway'); ?>::<?php echo JText::_('Name of the module position above pathway.'); ?>"><?php echo JText::_('Module position above pathway'); ?></span></td>
	<td><?php echo $lists['tmpl_wap_header']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Show pathway'); ?>::<?php echo JText::_('Show pathway on the pages.'); ?>"><?php echo JText::_('Show pathway'); ?></span></td>
	<td><?php echo $lists['tmpl_wap_pathway']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Pathway on home page'); ?>::<?php echo JText::_('Show pathway on home (main) page.'); ?>"><?php echo JText::_('Pathway on home page'); ?></span></td>
	<td><?php echo $lists['tmpl_wap_pathwayhome']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Module position between pathway and component'); ?>::<?php echo JText::_('Name of the module position between pathway and component.'); ?>"><?php echo JText::_('Module position between pathway and component'); ?></span></td>
	<td><?php echo $lists['tmpl_wap_middle']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Component on home page'); ?>::<?php echo JText::_('Show component on home (main) page.'); ?>"><?php echo JText::_('Component on home page'); ?></span></td>
	<td><?php echo $lists['tmpl_wap_componenthome']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Module position below component'); ?>::<?php echo JText::_('Name of the module position below component.'); ?>"><?php echo JText::_('Module position below component'); ?></span></td>
	<td><?php echo $lists['tmpl_wap_footer']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Show Joomla! footer'); ?>::<?php echo JText::_('Show site title and Joomla! licence in footer.'); ?>"><?php echo JText::_('Show Joomla! footer'); ?></span></td>
	<td><?php echo $lists['tmpl_wap_jfooter']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Module position for show modules as cards'); ?>::<?php echo JText::_('Name of the module position for show modules as cards.'); ?>"><?php echo JText::_('Module position for show modules as cards'); ?></span></td>
	<td><?php echo $lists['tmpl_wap_cards']; ?></td>
</tr>
</tbody>
</table>
</fieldset>

<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel("iMode","imode-page");
?>
<br/>
<fieldset class="adminform">
<legend><?php echo JText::_('iMode (CHTML) Settings'); ?></legend>
<table class="admintable" cellspacing="1">
<tbody>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Template name'); ?>::<?php echo JText::_('IMODE template name.'); ?>"><?php echo JText::_('Template name'); ?></span></td>
	<td><?php echo $lists['imodetemplate']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Homepage'); ?>::<?php echo JText::_('Set this page as a homepage.'); ?>"><?php echo JText::_('Homepage'); ?></span></td>
	<td><input class="text_area" type="text" size="50" name="mjconfig_imodehomepage" value="<?php echo $MobileJoomla_Settings['imodehomepage']; ?>" /></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Image adaptation method'); ?>::<?php echo JText::_('Remove or resize images.'); ?>"><?php echo JText::_('Image adaptation method'); ?></span></td>
	<td><?php echo $lists['tmpl_imode_img']; ?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('Gzip compression'); ?></td>
	<td><?php echo $lists['imodegzip']; ?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('Remove unsupported tags'); ?></td>
	<td><?php echo $lists['tmpl_imode_removetags']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Convert html-entities to symbols'); ?>::<?php echo JText::_('Convert html-entities to symbols using html_entity_decode function.'); ?>"><?php echo JText::_('Convert html-entities to symbols'); ?></span></td>
	<td><?php echo $lists['tmpl_imode_entitydecode']; ?></td>
</tr>
</tbody>
</table>
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_('iMode (CHTML) Mobile Joomla Template API Settings'); ?></legend>
<table class="admintable" cellspacing="1">
<tbody>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('1st module position above pathway'); ?>::<?php echo JText::_('Name of the 1st module position above pathway.'); ?>"><?php echo JText::_('1st module position above pathway'); ?></span></td>
	<td><?php echo $lists['tmpl_imode_header1']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('2nd module position above pathway'); ?>::<?php echo JText::_('Name of the 2nd module position above pathway.'); ?>"><?php echo JText::_('2nd module position above pathway'); ?></span></td>
	<td><?php echo $lists['tmpl_imode_header2']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Show pathway'); ?>::<?php echo JText::_('Show pathway on the pages.'); ?>"><?php echo JText::_('Show pathway'); ?></span></td>
	<td><?php echo $lists['tmpl_imode_pathway']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Pathway on home page'); ?>::<?php echo JText::_('Show pathway on home (main) page.'); ?>"><?php echo JText::_('Pathway on home page'); ?></span></td>
	<td><?php echo $lists['tmpl_imode_pathwayhome']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('1st module position between pathway and component'); ?>::<?php echo JText::_('Name of the 1st module position between pathway and component.'); ?>"><?php echo JText::_('1st module position between pathway and component'); ?></span></td>
	<td><?php echo $lists['tmpl_imode_middle1']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('2nd module position between pathway and component'); ?>::<?php echo JText::_('Name of the 2nd module position between pathway and component.'); ?>"><?php echo JText::_('2nd module position between pathway and component'); ?></span></td>
	<td><?php echo $lists['tmpl_imode_middle2']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Component on home page'); ?>::<?php echo JText::_('Show component on home (main) page.'); ?>"><?php echo JText::_('Component on home page'); ?></span></td>
	<td><?php echo $lists['tmpl_imode_componenthome']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('1st module position below component'); ?>::<?php echo JText::_('Name of the 1st module position below component.'); ?>"><?php echo JText::_('1st module position below component'); ?></span></td>
	<td><?php echo $lists['tmpl_imode_footer1']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('2nd module position below component'); ?>::<?php echo JText::_('Name of the 2nd module position below component.'); ?>"><?php echo JText::_('2nd module position below component'); ?></span></td>
	<td><?php echo $lists['tmpl_imode_footer2']; ?></td>
</tr>
<tr>
	<td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('Show Joomla! footer'); ?>::<?php echo JText::_('Show site title and Joomla! licence in footer.'); ?>"><?php echo JText::_('Show Joomla! footer'); ?></span></td>
	<td><?php echo $lists['tmpl_imode_jfooter']; ?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('DOCTYPE-head'); ?></td>
	<td><?php echo $lists['tmpl_imode_doctype']; ?></td>
</tr>
</tbody>
</table>
</fieldset>

<?php
		echo $tabs->endPanel();
		echo $tabs->endPane();
		echo JHTML::_( 'form.token' );
?>
<input type="hidden" name="option" value="<?php echo $option; ?>"/>
<input type="hidden" name="task" value=""/>
</form>
<script type="text/javascript" src="<?php echo JURI::base();?>/includes/js/overlib_mini.js"></script>
<?php
	}

	function showabout()
	{
        HTML_mobilejoomla::CheckForUpdate();
		$version = HTML_mobilejoomla::getMJVersion();
?>
<!--fieldset class="adminform"-->
<!--legend><?php echo JText::_('Kuneri Mobile Joomla!'); ?></legend-->
<table class="admintable" cellspacing="1">
<tbody>
<tr><td>
<h2>Kuneri MobileJoomla <?php echo $version;?></h2>
<?php echo JText::_('Kuneri Mobile Joomla! is the most advanced tool to turn your Joomla! web site into a mobile web site, compatible with all phones in the world, including iPhone, Smartphone, iMode and WAP phones'); ?>
<br />
<br />
<a href="http://www.mobilejoomla.com/"><?php echo JText::_('Visit Kuneri Mobile Joomla! for more!'); ?></a>
<br />
<br />
<div id="mjupdate">
    <h2><?php echo JText::_('Update available');?></h2>
    New MobileJoomla version is available for <a href="http://www.mobilejoomla.com/download.html" alt="<?php echo JText::_('Learn more on www.mobilejoomla.com'); ?>" target="_blank">update</a>.
</div>
<div id="mjnoupdate">
    <h2><?php echo JText::_('No updates available');?></h2>
    <?php echo JText::_('MobileJoomla is up-to-date.');?>
</div>

</td></tr>
</tbody>
</table>
<!--/fieldset-->
<?php
	}

	function showwurfl()
	{
		HTML_mobilejoomla::CheckForUpdate();
		function writable( $folder )
		{
			echo '<tr><td>'.$folder.DS.'</td><td align="left">';
			$fullpath=JPATH_SITE.DS.$folder;
			if(!is_dir($fullpath)) echo '<b><font color="red">'.JText::_('Not found').'</font></b>';
			elseif(!is_writable($fullpath)) echo '<b><font color="red">'.JText::_('Unwriteable').'</font></b>';
			else echo '<b><font color="green">'.JText::_('Writeable').'</font></b>';
			echo '</td></tr>';
		}
		function exists( $file )
		{
			echo '<tr><td>'.$file.'</td><td align="left">';
			echo is_file( JPATH_SITE.DS.$file ) ? '<b><font color="green">'.JText::_('OK').'</font></b>' : '<b><font color="red">'.JText::_('Not found').'</font></b>';
			echo '</td></tr>';
		}
		function exists_and_date( $file, $date=null )
		{
			$filedate=0x7fffffff;
			echo '<tr><td>'.$file.'</td><td align="left">';
			if(!is_file( JPATH_SITE.DS.$file ))
				echo '<b><font color="red">'.JText::_('Not found').'</font></b>';
			else
			{
				$filedate=filemtime( JPATH_SITE.DS.$file );
				if(($date===null)||($filedate>$date))
					echo '<b><font color="green">'.JText::_('OK').'</font></b> ['.date('d.m.Y h:i:s',$filedate).']';
				else
					echo '<b><font color="red">'.JText::_('Outdated').'</font></b> ['.date('d.m.Y h:i:s',$filedate).']';
			}
			echo '</td></tr>';
			return $filedate;
		}
		function existsdir_and_date( $file, $date=null )
		{
			echo '<tr><td>'.$file.DS.'</td><td align="left">';
			if(!is_dir( JPATH_SITE.DS.$file ))
				echo '<b><font color="red">'.JText::_('Not found').'</font></b>';
			else
			{
				$filedate=filemtime( JPATH_SITE.DS.$file );
				if(($date===null)||($filedate>$date))
					echo '<b><font color="green">'.JText::_('OK').'</font></b> ['.date('d.m.Y h:i:s',$filedate).']';
				else
					echo '<b><font color="red">'.JText::_('Outdated').'</font></b> ['.date('d.m.Y h:i:s',$filedate).']';
			}
			echo '</td></tr>';
		}
?>
<form action="index2.php" method="post" name="adminForm">
<table cellpadding="1" cellspacing="1" border="0" width="100%">
<tr>
	<td><table class="adminheading"><tr><th nowrap="nowrap" class="config">WURFL</th></tr></table></td>
</tr>
</table>
<table>
<tr><td colspan="2" align="center"><b><?php echo JText::_('Kuneri Mobile Joomla! WURFL checklist'); ?></b></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<?php
		include(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php');

		$wurflpath='administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'wurfl'.DS;
		writable($wurflpath.'data');
		$date=exists_and_date($wurflpath.'data'.DS.'wurfl.xml');
		$date2=exists_and_date($wurflpath.'data'.DS.'web_browsers_patch.xml');
		if($date2>$date) $date=$date2;
		if($MobileJoomla_Settings['wurflcache']>0)
			exists_and_date($wurflpath.'data'.DS.'cache.php',$date);
		if($MobileJoomla_Settings['wurflcache']==2)
			existsdir_and_date($wurflpath.'data'.DS.'multicache',$date);
		echo '<tr><td colspan="2">&nbsp;</td></tr>';
		exists($wurflpath.'wurfl_class.php');
		exists($wurflpath.'wurfl_config.php');
		exists($wurflpath.'wurfl_parser.php');
		exists($wurflpath.'wurfl_download.php');
		exists($wurflpath.'update_cache.php');
		exists($wurflpath.'imageadaptation.php');
?>
</table>
<p>&nbsp;</p>
<a href="components/com_mobilejoomla/wurfl/wurfl_download.php" onclick="window.open('components/com_mobilejoomla/wurfl/wurfl_download.php','popupwindow',config='toolbar=0,menubar=0,personalbar=0,width=450,height=250,scrollbars=1,resizable=1,modal=1,dependable=1');return false;"><?php echo JText::_('Download WURFL'); ?></a> <?php echo JText::_('(wurfl.xml and web_browsers_patch.xml)'); ?><br /><br />
<a href="components/com_mobilejoomla/wurfl/update_cache.php" onclick="window.open('components/com_mobilejoomla/wurfl/update_cache.php','popupwindow',config='toolbar=0,menubar=0,personalbar=0,width=300,height=200,scrollbars=1,resizable=1,modal=1,dependable=1');return false;"><?php echo JText::_('Update WURFL cache'); ?></a>
</form>
<?php
	}
	function showupdate($status,$current_ver,$latest_ver)
	{
?>
<table cellpadding="1" cellspacing="1" border="0" width="100%">
<tr>
	<td><table class="adminheading"><tr><th nowrap="nowrap" class="install"><?php echo JText::_('Check for update'); ?></th></tr></table></td>
</tr>
</table>
<?php
		$msg='';
		switch($status)
		{
		case -2: // error - unknown installed version (no access to xml manifest file)
			$msg='Unknown installed version (no access to xml manifest file).';
			break;
		case -1: // error - no access to mobilejoomla.com
			$msg='No access to mobilejoomla.com.';
			break;
		case 0: //latest version is installed
			$msg='Latest version of Mobile Joomla is installed.';
			break;
		}
		if($msg)
		{
?>
<table class="admintable" cellspacing="1">
<tbody><tr><td><?php echo JText::_($msg); ?></td></tr></tbody>
</table>
<?php
		}
		else
		{
?>
<table class="admintable" cellspacing="1">
<tbody><tr><td><?php echo JText::_('New version of Mobile Joomla is available:').' '.$latest_ver; ?></td></tr></tbody>
</table>
<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm">
<input type="hidden" name="install_url" value="http://www.mobilejoomla.com/MobileJoomla_J15_latest.zip" />
<input type="hidden" name="type" value="" />
<input type="hidden" name="installtype" value="url" />
<input type="hidden" name="task" value="doInstall" />
<input type="hidden" name="option" value="com_installer" />
<?php echo JHTML::_( 'form.token' ); ?>
<input type="submit" class="button" value="<?php echo JText::_('Download and install new version'); ?>" />
</form>
<?php
		}
	}
}
