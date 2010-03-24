<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version		###VERSION###
 * @license		###LICENSE###
 * @copyright	###COPYRIGHT###
 * @date        ###DATE###
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

function CheckMobile()
{
    global $wurflObj;

    if (!isset ($wurflObj))
    {
        require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'terawurfl'.DS.'TeraWurfl.php');

		if(version_compare(phpversion(),'5.0.0','<'))
		{
			$wurflObj = new TeraWurfl();
			$matched = $wurflObj->getDeviceCapabilitiesFromAgent($_SERVER['HTTP_USER_AGENT']);
		}
		else
		{
			try
			{
				$wurflObj = new TeraWurfl();
				$matched = $wurflObj->getDeviceCapabilitiesFromAgent($_SERVER['HTTP_USER_AGENT']);
			}
			catch(exception $e)
			{
				$wurflObj = null;
				$matched = false;
			}
		}
    }

    // Get the capabilities of the current client. $matched will be true if Tera-WURFL
    // found a match for the device and false if not.

	if($matched && $wurflObj->getDeviceCapability("is_wireless_device"))
	{
		switch($wurflObj->getDeviceCapability("preferred_markup"))
		{
		case 'wml_1_1':
		case 'wml_1_2':
		case 'wml_1_3':
			return 'wml';//text/vnd.wap.wml encoding="ISO-8859-15"
		case 'html_wi_imode_compact_generic':
		case 'html_wi_imode_html_1':
		case 'html_wi_imode_html_2':
		case 'html_wi_imode_html_3':
		case 'html_wi_imode_html_4':
		case 'html_wi_imode_html_5':
			return 'chtml';//text/html
		case 'html_wi_oma_xhtmlmp_1_0'://application/vnd.wap.xhtml+xml
		case 'html_wi_w3_xhtmlbasic'://application/xhtml+xml DTD XHTML Basic 1.0
			return 'xhtml';
		case 'html_web_3_2'://text/html DTD HTML 3.2 Final
		case 'html_web_4_0'://text/html DTD HTML 4.01 Transitional
			return '';
		}
	}
	return '';
}