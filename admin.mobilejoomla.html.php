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

class HTML_mobilejoomla
{
	function getMJVersion()
	{
		$manifest = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'mobilejoomla.xml';
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
			<style>#mjupdate {
				display: none
			}</style>
			<link rel="stylesheet" type="text/css"
			      href="http://www.mobilejoomla.com/checker.php?v=<?php echo urlencode($version); ?>"/>
			<?php

		}
	}

	function showconfig(&$lists, $MobileJoomla_Settings)
	{
		global $option;
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.switcher');
		HTML_mobilejoomla::CheckForUpdate();
		?>
		<form action="index.php" method="post" name="adminForm">
		<table cellpadding="1" cellspacing="1" border="0" width="90%">
			<tr>
				<td width="300">
					<table class="adminheading">
						<tr>
							<th nowrap="nowrap"
							    class="config"><?php echo JText::_('Mobile Joomla! Settings'); ?></th>
						</tr>
					</table>
				</td>
				<td width="500">
					<span class="componentheading">/ administrator / components / com_mobilejoomla / config.php <?php echo JText::_('is') ?>
						: <b><?php echo !is_file('components/com_mobilejoomla/config.php') ? '<font color="red">'.JText::_('Missing').'</font>' : is_writable('components/com_mobilejoomla/config.php') ? '<font color="green">'.JText::_('Writeable').'</font>' : '<font color="red">'.JText::_('Unwriteable').'</font>' ?></b></span>
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
			<legend><?php echo JText::_('General Settings'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Caching'); ?>::<?php echo JText::_('Allow caching for mobile pages (requires additional disk space).'); ?>"><?php echo JText::_('Caching'); ?></span>
					</td>
					<td><?php echo $lists['caching']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Domain (multisite) support'); ?>::<?php echo JText::_('Show mobile versions on aliases (like pda.site.name, wap.site.name).'); ?>"><?php echo JText::_('Domain (multisite) support'); ?></span>
					</td>
					<td><?php echo $lists['domains']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Show this page for PC'); ?>::<?php echo JText::_('Use this setting to allow mobile devices only. For PC browser either specified page will be displayed or browser will be redirected to specified external site. Keep this parameter empty to allow visit your site from PC.'); ?>"><?php echo JText::_('Show this page for PC'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="80" name="mjconfig_pcpage"
					           value="<?php echo $MobileJoomla_Settings['pcpage']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('PC template width'); ?>::<?php echo JText::_('Approximate width of the PC template (used in image \'proportional\' processing).'); ?>"><?php echo JText::_('PC template width'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="6" name="mjconfig_templatewidth"
					           value="<?php echo $MobileJoomla_Settings['templatewidth']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Rescaled image quality (0-100)'); ?>::<?php echo JText::_('Quality of rescaled jpeg images.'); ?>"><?php echo JText::_('Rescaled image quality (0-100)'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="3" name="mjconfig_jpegquality"
					           value="<?php echo $MobileJoomla_Settings['jpegquality']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Desktop URL'); ?>::<?php echo JText::_('Should be changed after moving the site to another URI only.'); ?>"><?php echo JText::_('Desktop URL'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="30" name="mjconfig_desktop_url"
					           value="<?php echo $MobileJoomla_Settings['desktop_url']; ?>"/></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('Smartphone (XHTML-MP/WAP2.0) Domain'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Domain name'); ?>::<?php echo JText::_('Domain name, e.g. pda.yoursite.com.'); ?>"><?php echo JText::_('Domain name'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="30" name="mjconfig_xhtmldomain"
					           value="<?php echo $MobileJoomla_Settings['xhtmldomain']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Redirect to domain'); ?>::<?php echo JText::_('Redirect xhtmlmp/wap2.0 devices to this domain.'); ?>"><?php echo JText::_('Redirect to domain'); ?></span>
					</td>
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
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Domain name'); ?>::<?php echo JText::_('Domain name, e.g. iphone.yoursite.com.'); ?>"><?php echo JText::_('Domain name'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="30" name="mjconfig_iphonedomain"
					           value="<?php echo $MobileJoomla_Settings['iphonedomain']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Redirect to domain'); ?>::<?php echo JText::_('Redirect iPhone/iPod devices to this domain.'); ?>"><?php echo JText::_('Redirect to domain'); ?></span>
					</td>
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
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Domain name'); ?>::<?php echo JText::_('Domain name, e.g. wap.yoursite.com.'); ?>"><?php echo JText::_('Domain name'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="30" name="mjconfig_wapdomain"
					           value="<?php echo $MobileJoomla_Settings['wapdomain']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Redirect to domain'); ?>::<?php echo JText::_('Redirect wap/wml devices to this domain.'); ?>"><?php echo JText::_('Redirect to domain'); ?></span>
					</td>
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
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Domain name'); ?>::<?php echo JText::_('Domain name, e.g. imode.yoursite.com.'); ?>"><?php echo JText::_('Domain name'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="30" name="mjconfig_imodedomain"
					           value="<?php echo $MobileJoomla_Settings['imodedomain']; ?>"/></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Redirect to domain'); ?>::<?php echo JText::_('Redirect imode devices to this domain.'); ?>"><?php echo JText::_('Redirect to domain'); ?></span>
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
			<legend><?php echo JText::_('XHTML-MP/WAP2.0 Settings'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Template name'); ?>::<?php echo JText::_('XHTMLMP/WAP2.0 template name.'); ?>"><?php echo JText::_('Template name'); ?></span>
					</td>
					<td><?php echo $lists['xhtmltemplate']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Homepage'); ?>::<?php echo JText::_('Set this page as a homepage.'); ?>"><?php echo JText::_('Homepage'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="80" name="mjconfig_xhtmlhomepage" id="mjconfig_xhtmlhomepage"
					           value="<?php echo $MobileJoomla_Settings['xhtmlhomepage']; ?>"/></td>
				</tr>
				<tr><td></td><td><?php echo JHTML::_('select.genericlist', $lists['menuoptions'], 'xhtml_tmp', 'size="7" onchange="document.getElementById(\'mjconfig_xhtmlhomepage\').value=this.value" ', 'value', 'text', $MobileJoomla_Settings['xhtmlhomepage']); ?></td></tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Image adaptation method'); ?>::<?php echo JText::_('Remove or resize images.'); ?>"><?php echo JText::_('Image adaptation method'); ?></span>
					</td>
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
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Convert html-entities to symbols'); ?>::<?php echo JText::_('Convert html-entities to symbols using html_entity_decode function.'); ?>"><?php echo JText::_('Convert html-entities to symbols'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_entitydecode']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Content-type'); ?>::<?php echo JText::_('Output Content-type header.'); ?>"><?php echo JText::_('Content-type'); ?></span>
					</td>
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
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('1st module position above pathway'); ?>::<?php echo JText::_('Name of the 1st module position above pathway.'); ?>"><?php echo JText::_('1st module position above pathway'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_header1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('2nd module position above pathway'); ?>::<?php echo JText::_('Name of the 2nd module position above pathway.'); ?>"><?php echo JText::_('2nd module position above pathway'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_header2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Show pathway'); ?>::<?php echo JText::_('Show pathway on the pages.'); ?>"><?php echo JText::_('Show pathway'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_pathway']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Pathway on home page'); ?>::<?php echo JText::_('Show pathway on home (main) page.'); ?>"><?php echo JText::_('Pathway on home page'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_pathwayhome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('1st module position between pathway and component'); ?>::<?php echo JText::_('Name of the 1st module position between pathway and component.'); ?>"><?php echo JText::_('1st module position between pathway and component'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_middle1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('2nd module position between pathway and component'); ?>::<?php echo JText::_('Name of the 2nd module position between pathway and component.'); ?>"><?php echo JText::_('2nd module position between pathway and component'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_middle2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Component on home page'); ?>::<?php echo JText::_('Show component on home (main) page.'); ?>"><?php echo JText::_('Component on home page'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_componenthome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('1st module position below component'); ?>::<?php echo JText::_('Name of the 1st module position below component.'); ?>"><?php echo JText::_('1st module position below component'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_footer1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('2nd module position below component'); ?>::<?php echo JText::_('Name of the 2nd module position below component.'); ?>"><?php echo JText::_('2nd module position below component'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_footer2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Show Joomla! footer'); ?>::<?php echo JText::_('Show site title and Joomla! licence in footer.'); ?>"><?php echo JText::_('Show Joomla! footer'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_jfooter']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Use head'); ?>::<?php echo JText::_('Use standard or simplified &amp;lt;head&amp;gt; block.'); ?>"><?php echo JText::_('Use head'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_simplehead']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Allow extended editors'); ?>::<?php echo JText::_('Allow to load extended editors (TinyMCE, etc.).'); ?>"><?php echo JText::_('Allow extended editors'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_allowextedit']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Embed CSS'); ?>::<?php echo JText::_('Embed css-style into page.'); ?>"><?php echo JText::_('Embed CSS'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_embedcss']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Use xml-head'); ?>::<?php echo JText::_('Start html with xml.'); ?>"><?php echo JText::_('Use xml-head'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_xmlhead']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('DOCTYPE-head'); ?></td>
					<td><?php echo $lists['tmpl_xhtml_doctype']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('xmlns in html-head'); ?>::<?php echo JText::_('Use html xmlns instead of html.'); ?>"><?php echo JText::_('xmlns in html-head'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_xhtml_xmlns']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Decrease Image Width'); ?>::<?php echo JText::_('Pixels to further decrease width of your already rescaled image, preserving aspect ratio. Write an integer.'); ?>"><?php echo JText::_('Decrease Image Width'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="10" name="mjconfig_xhtml_buffer_width"
					           value="<?php echo $MobileJoomla_Settings['xhtml_buffer_width']; ?>"/></td>
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
			<legend><?php echo JText::_('iPhone/iPod Settings'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Template name'); ?>::<?php echo JText::_('iPhone template name.'); ?>"><?php echo JText::_('Template name'); ?></span>
					</td>
					<td><?php echo $lists['iphonetemplate']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Homepage'); ?>::<?php echo JText::_('Set this page as a homepage.'); ?>"><?php echo JText::_('Homepage'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="80" name="mjconfig_iphonehomepage" id="mjconfig_iphonehomepage"
					           value="<?php echo $MobileJoomla_Settings['iphonehomepage']; ?>"/></td>
				</tr>
				<tr><td></td><td><?php echo JHTML::_('select.genericlist', $lists['menuoptions'], 'iphone_tmp', 'size="7" onchange="document.getElementById(\'mjconfig_iphonehomepage\').value=this.value" ', 'value', 'text', $MobileJoomla_Settings['iphonehomepage']); ?></td></tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Image adaptation method'); ?>::<?php echo JText::_('Remove or resize images.'); ?>"><?php echo JText::_('Image adaptation method'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_img']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('Gzip compression'); ?></td>
					<td><?php echo $lists['iphonegzip']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('Remove unsupported tags'); ?></td>
					<td><?php echo $lists['tmpl_iphone_removetags']; ?></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('iPhone/iPod Mobile Joomla Template API Settings'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('1st module position above pathway'); ?>::<?php echo JText::_('Name of the 1st module position above pathway.'); ?>"><?php echo JText::_('1st module position above pathway'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_header1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('2nd module position above pathway'); ?>::<?php echo JText::_('Name of the 2nd module position above pathway.'); ?>"><?php echo JText::_('2nd module position above pathway'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_header2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Show pathway'); ?>::<?php echo JText::_('Show pathway on the pages.'); ?>"><?php echo JText::_('Show pathway'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_pathway']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Pathway on home page'); ?>::<?php echo JText::_('Show pathway on home (main) page.'); ?>"><?php echo JText::_('Pathway on home page'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_pathwayhome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('1st module position between pathway and component'); ?>::<?php echo JText::_('Name of the 1st module position between pathway and component.'); ?>"><?php echo JText::_('1st module position between pathway and component'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_middle1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('2nd module position between pathway and component'); ?>::<?php echo JText::_('Name of the 2nd module position between pathway and component.'); ?>"><?php echo JText::_('2nd module position between pathway and component'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_middle2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Component on home page'); ?>::<?php echo JText::_('Show component on home (main) page.'); ?>"><?php echo JText::_('Component on home page'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_componenthome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('1st module position below component'); ?>::<?php echo JText::_('Name of the 1st module position below component.'); ?>"><?php echo JText::_('1st module position below component'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_footer1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('2nd module position below component'); ?>::<?php echo JText::_('Name of the 2nd module position below component.'); ?>"><?php echo JText::_('2nd module position below component'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_footer2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Show Joomla! footer'); ?>::<?php echo JText::_('Show site title and Joomla! licence in footer.'); ?>"><?php echo JText::_('Show Joomla! footer'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_iphone_jfooter']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Decrease Image Width'); ?>::<?php echo JText::_('Pixels to further decrease width of your already rescaled image, preserving aspect ratio. Write an integer.'); ?>"><?php echo JText::_('Decrease Image Width'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="10" name="mjconfig_iphone_buffer_width"
					           value="<?php echo $MobileJoomla_Settings['iphone_buffer_width']; ?>"/></td>
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
			<legend><?php echo JText::_('WAP (WML) Settings'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Template name'); ?>::<?php echo JText::_('WAP/WML template name.'); ?>"><?php echo JText::_('Template name'); ?></span>
					</td>
					<td><?php echo $lists['waptemplate']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Homepage'); ?>::<?php echo JText::_('Set this page as a homepage.'); ?>"><?php echo JText::_('Homepage'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="80" name="mjconfig_waphomepage" id="mjconfig_waphomepage"
					           value="<?php echo $MobileJoomla_Settings['waphomepage']; ?>"/></td>
				</tr>
				<tr><td></td><td><?php echo JHTML::_('select.genericlist', $lists['menuoptions'], 'wap_tmp', 'size="7" onchange="document.getElementById(\'mjconfig_waphomepage\').value=this.value" ', 'value', 'text', $MobileJoomla_Settings['waphomepage']); ?></td></tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Image adaptation method'); ?>::<?php echo JText::_('Remove or resize images.'); ?>"><?php echo JText::_('Image adaptation method'); ?></span>
					</td>
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
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Convert html-entities to symbols'); ?>::<?php echo JText::_('Convert html-entities to symbols using html_entity_decode function.'); ?>"><?php echo JText::_('Convert html-entities to symbols'); ?></span>
					</td>
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
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Module position above pathway'); ?>::<?php echo JText::_('Name of the module position above pathway.'); ?>"><?php echo JText::_('Module position above pathway'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wap_header']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Show pathway'); ?>::<?php echo JText::_('Show pathway on the pages.'); ?>"><?php echo JText::_('Show pathway'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wap_pathway']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Pathway on home page'); ?>::<?php echo JText::_('Show pathway on home (main) page.'); ?>"><?php echo JText::_('Pathway on home page'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wap_pathwayhome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Module position between pathway and component'); ?>::<?php echo JText::_('Name of the module position between pathway and component.'); ?>"><?php echo JText::_('Module position between pathway and component'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wap_middle']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Component on home page'); ?>::<?php echo JText::_('Show component on home (main) page.'); ?>"><?php echo JText::_('Component on home page'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wap_componenthome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Module position below component'); ?>::<?php echo JText::_('Name of the module position below component.'); ?>"><?php echo JText::_('Module position below component'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wap_footer']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Show Joomla! footer'); ?>::<?php echo JText::_('Show site title and Joomla! licence in footer.'); ?>"><?php echo JText::_('Show Joomla! footer'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wap_jfooter']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Module position for show modules as cards'); ?>::<?php echo JText::_('Name of the module position for show modules as cards.'); ?>"><?php echo JText::_('Module position for show modules as cards'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_wap_cards']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Decrease Image Width'); ?>::<?php echo JText::_('Pixels to further decrease width of your already rescaled image, preserving aspect ratio. Write an integer.'); ?>"><?php echo JText::_('Decrease Image Width'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="10" name="mjconfig_wml_buffer_width"
					           value="<?php echo $MobileJoomla_Settings['wml_buffer_width']; ?>"/></td>
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
			<legend><?php echo JText::_('iMode (CHTML) Settings'); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Template name'); ?>::<?php echo JText::_('IMODE template name.'); ?>"><?php echo JText::_('Template name'); ?></span>
					</td>
					<td><?php echo $lists['imodetemplate']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Homepage'); ?>::<?php echo JText::_('Set this page as a homepage.'); ?>"><?php echo JText::_('Homepage'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="80" name="mjconfig_imodehomepage" id="mjconfig_imodehomepage"
					           value="<?php echo $MobileJoomla_Settings['imodehomepage']; ?>"/></td>
				</tr>
				<tr><td></td><td><?php echo JHTML::_('select.genericlist', $lists['menuoptions'], 'imode_tmp', 'size="7" onchange="document.getElementById(\'mjconfig_imodehomepage\').value=this.value" ', 'value', 'text', $MobileJoomla_Settings['imodehomepage']); ?></td></tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Image adaptation method'); ?>::<?php echo JText::_('Remove or resize images.'); ?>"><?php echo JText::_('Image adaptation method'); ?></span>
					</td>
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
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Convert html-entities to symbols'); ?>::<?php echo JText::_('Convert html-entities to symbols using html_entity_decode function.'); ?>"><?php echo JText::_('Convert html-entities to symbols'); ?></span>
					</td>
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
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('1st module position above pathway'); ?>::<?php echo JText::_('Name of the 1st module position above pathway.'); ?>"><?php echo JText::_('1st module position above pathway'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_imode_header1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('2nd module position above pathway'); ?>::<?php echo JText::_('Name of the 2nd module position above pathway.'); ?>"><?php echo JText::_('2nd module position above pathway'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_imode_header2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Show pathway'); ?>::<?php echo JText::_('Show pathway on the pages.'); ?>"><?php echo JText::_('Show pathway'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_imode_pathway']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Pathway on home page'); ?>::<?php echo JText::_('Show pathway on home (main) page.'); ?>"><?php echo JText::_('Pathway on home page'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_imode_pathwayhome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('1st module position between pathway and component'); ?>::<?php echo JText::_('Name of the 1st module position between pathway and component.'); ?>"><?php echo JText::_('1st module position between pathway and component'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_imode_middle1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('2nd module position between pathway and component'); ?>::<?php echo JText::_('Name of the 2nd module position between pathway and component.'); ?>"><?php echo JText::_('2nd module position between pathway and component'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_imode_middle2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Component on home page'); ?>::<?php echo JText::_('Show component on home (main) page.'); ?>"><?php echo JText::_('Component on home page'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_imode_componenthome']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('1st module position below component'); ?>::<?php echo JText::_('Name of the 1st module position below component.'); ?>"><?php echo JText::_('1st module position below component'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_imode_footer1']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('2nd module position below component'); ?>::<?php echo JText::_('Name of the 2nd module position below component.'); ?>"><?php echo JText::_('2nd module position below component'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_imode_footer2']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Show Joomla! footer'); ?>::<?php echo JText::_('Show site title and Joomla! licence in footer.'); ?>"><?php echo JText::_('Show Joomla! footer'); ?></span>
					</td>
					<td><?php echo $lists['tmpl_imode_jfooter']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('DOCTYPE-head'); ?></td>
					<td><?php echo $lists['tmpl_imode_doctype']; ?></td>
				</tr>
				<tr>
					<td class="key"><span class="editlinktip hasTip"
					                      title="<?php echo JText::_('Decrease Image Width'); ?>::<?php echo JText::_('Pixels to further decrease width of your already rescaled image, preserving aspect ratio. Write an integer.'); ?>"><?php echo JText::_('Decrease Image Width'); ?></span>
					</td>
					<td><input class="text_area" type="text" size="10" name="mjconfig_chtml_buffer_width"
					           value="<?php echo $MobileJoomla_Settings['chtml_buffer_width']; ?>"/></td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<?php
		echo $tabs->endPanel();
		echo $tabs->endPane();
		echo JHTML::_('form.token');
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
		<!--legend><?php echo JText::_('Mobile Joomla!'); ?></legend-->
		<table class="admintable" cellspacing="1">
			<tbody>
			<tr>
				<td>
					<h2>MobileJoomla <?php echo $version;?></h2>
				<?php echo JText::_('Mobile Joomla! is the most advanced tool to turn your Joomla! web site into a mobile web site, compatible with all phones in the world, including iPhone, Smartphone, iMode and WAP phones'); ?>
					<br/>
					<br/>
					<a href="http://www.mobilejoomla.com/"><?php echo JText::_('Visit Mobile Joomla! for more!'); ?></a>
					<br/>
					<br/>

					<div id="mjupdate">
						<h2><?php echo JText::_('Update available');?></h2>
						New MobileJoomla version is available for <a href="http://www.mobilejoomla.com/download.html"
						                                             alt="<?php echo JText::_('Learn more on www.mobilejoomla.com'); ?>"
						                                             target="_blank">updating</a>.
					</div>
					<div id="mjnoupdate">
						<h2><?php echo JText::_('No updates available');?></h2>
					<?php echo JText::_('MobileJoomla is up-to-date.');?>
					</div>

				</td>
			</tr>
			</tbody>
		</table>
		<!--/fieldset-->
		<?php

	}

	function showextensions($tabs)
	{
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.switcher');
		/** @var JPane $tabsPane */
		$tabsPane =& JPane::getInstance();

		if(count($tabs) < 1)
		{
			?>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td>
						<h2>Mobile Joomla Extensions</h2>
					<?php echo JText::_('No extensions installed.'); ?><br/><br/>
					<?php echo JText::_('You can find more extensions from'); ?> <a
							href="http://www.mobilejoomla.com/"><?php echo JText::_('MobileJoomla homepage'); ?></a>.
					</td>
				</tr>
				</tbody>
			</table>
			<?php
            return;
		}

		function displayConfigTmpl($tmpl, &$config, $prefix)
		{
			$templates = array ();
			$jcompath = JPATH_SITE.DS.'components'.DS.'com_jcomments'.DS.'tpl';

			if(JFolder::exists($jcompath))
			{
				$dir = scandir($jcompath);
				foreach($dir as $entry)
				{
					if($entry == '.' || $entry == '..' ||
							!is_dir(JPATH_SITE.DS.'components'.DS.'com_jcomments'.DS.'tpl'.DS.$entry))
						continue;
					$templates[] = JHTML::_('select.option', $entry, $entry);
				}
				$userLevels = array (JHTML::_('select.option', 'Unregistered', 'Unregistered'),
				                     JHTML::_('select.option', 'Registered', 'Registered'),
				                     JHTML::_('select.option', 'Author', 'Author'),
				                     JHTML::_('select.option', 'Editor', 'Editor'),
				                     JHTML::_('select.option', 'Publisher', 'Publisher'),
				                     JHTML::_('select.option', 'Manager', 'Manager'),
				                     JHTML::_('select.option', 'Administrator', 'Administrator'),
				                     JHTML::_('select.option', 'Super Administrator', 'Super Administrator'));
				unset ($dir);
			}
			include (JPATH_SITE.DS.$tmpl);
		}

		?>
		<form action="index.php" method="post" name="adminForm">
		<?php

		echo $tabsPane->startPane('extensionsPane');

		foreach($tabs as $key => $tab)
		{
			echo $tabsPane->startPanel($tab->title, $tab->name);

			$content = file_get_contents(JPATH_SITE.DS.$tab->configPath);
			$config = json_decode($content);

			displayConfigTmpl($tab->adminTmplPath, $config, $tab->name);

			echo $tabsPane->endPanel();
		}

		echo $tabsPane->endPane();

		echo JHTML::_('form.token');?>
			<input type="hidden" name="option" value="com_mobilejoomla"/>
			<input type="hidden" name="task" value="save_ext"/>
			<input type="hidden" name="ext" value="1"/>

		</form>
		<?php

	}
}
