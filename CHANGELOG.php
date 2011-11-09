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
?>

CHANGELOG

1.0
RC5 release
- Add device-specific meta-tags to improve page look
- Reduce distributive size
- Fix redirect to subdomain issue
- Fix iOS5 display
- Fix incorrect images in menu module (Joomla!1.5)
- Disable wrap text around wide images (50% to 100% of screen width)
- Fix issue with undefined params object

1.0
RC4 release
- Support for user-defined css/custom.css that is not overwritten on updates
- Fix issue with incorrect regular expression matching in image rescaling procedure
- Support for anchor_css menu item parameter (Joomla!1.6/1.7)
- Fix issue with live_url on redirects
- Fix issue with incorrect language strings in settings of mobile_iphone template
- Fix conflict with Mootools in mobile_pda template
- Fix incorrect homepage detecting with language switcher

1.0
RC3 release
- Fix sh404sef issue in Joomla!1.6/1.7
- Fix registration form layout in mobile_pda template on Joomla!1.5
- Fix possible warning in mod_mj_menu module

1.0
RC2 release
- Fix session corruption in Joomla!1.6/1.7
- Fix conflict with sh404sef extension
- Fix redirect-after-login issue in mobile_iphone template and Joomla!1.5
- Fix call-time pass-by-reference warning in mod_mj_menu.php
- Fix loading of template parameters in Joomla!1.6/1.7
- Fix subdomain detection issue

1.0
RC release
- Support of Joomla!1.6/1.7
- Mobile templates for Joomla!1.6/1.7
- Update to TeraWURFL 2.1.5
- Update WURFL database (13.07.2011)
- New stable method for switching between mobile markups
- New mobile menu module with support of both horizontal and vertical layouts
- Speedup by caching device parameters
- Fix issue with incorrect "Standard version" link on subdomain
- Fix issue with possible warning in TeraWURFL plugin
- Fix to keep path at redirect to subdomain
- Fix issue with uninstalling of MJ on Joomla!1.5
- Fix issue with incorrect IE9 detecting in Mobile-Simple plugin
- Fix redirect to subdomain issue in Joomla!1.6
- Support of absolute URLs starting with // at image rescaling
- Support of single-subdomain mode
- Improve mobile_pda and mobile_iphone templates
- Keep float:left/right style for rescaled images
- Many small changes

0.9.12
Beta release
- Backend Cpanel icon module
- One-click updater
- Update to TeraWURFL 2.1.4
- Update WURFL database
- Improve default Mobile-Simple detector
- Fix issue with IE9 browser
- Partial support of Joomla!1.6

0.9.11
Beta release
- Fix notice in Mobile-Simple plugin
- Fix issue with mobile_iphone and mobile_pda templates
- Update WURFL database
- Option in TeraWURFL plugin to enable/disable caching
- Fix issue with mobile homepage
- Option to hide "Select Markup" module for PC browsers

0.9.10
Beta release
- Add "Browser Caching" option
- Add common mobile module positions: mj_all_header, mj_all_middle, and mj_all_footer
- Setting to use iPhone template for iPads too.
- By default Ad block will be shown in mobile_pda and mobile_iphone templates

0.9.9
Beta release
- Fix MarkupChooser module
- Add "Mobile Sitename" setting
- Add "Use style to force image size" setting
- Update rescaled images after changing of source image
- Fix PHP4 issues
- Update WURFL database
- Many small changes

0.9.8
Beta release
- Fix of double slash in urls of resized images
- Update to TeraWURFL 2.1.3
- Update WURFL database
- Improve default device detection
- Fix of some installation issues
- Fix of homepage detection issue
- Improve install procedure

0.9.7
Beta release
- Fix of mobile homepage support
- Fix of mysqli work with nonstandard port/socket
- Feature to select menuitem instead of typing its url in homepage parameters
- Support of apple-touch icon (57x57 px) in iPhone templates
- Check for mysqli connection during install
- Improvement of gif and wbmp rescaling

0.9.6
Beta release
- Fix of image rescaling procedure
- Fix of installing procedure
- Fix of markupchooser module to work with joomla installed in subdirectory
- Fix of determining iPhones

0.9.5
Beta release
- RSS issue fixed
- Templates preview issue fixed
- Support of caching to increase site performance
- Support of mobile plugins
- New image adaptation script
- TeraWURFL updated to v.2.1.2

0.9.4
Beta release
- Support of iPod Touch 1G firmware 3.0
- TeraWURFL updated to v.2.1.1
- Cache issue fixed

0.9.3
Beta release
- MobileJoomla installer bugs fixed
- Wrong image extension at image rescaling fix
- Problems (with incorrect links and working in SEF mode) in markupchooser module are fixed
- Problems with redirect to subdomains are fixed
- System-Cache plugin is disabled in mobile mode
- Support for PHP4 is added

0.9.2
Beta release
- Installation made more compatible

0.9.1
Beta release
- Installation package made smaller
- Some various fixes

0.9.0
Beta release
- Fixes to TeraWurfl
- Fixes to modules
- Numerous other small fixes

0.8.2
Third iteration on private alpha
- Changed default content-type, eliminates xml parsing errors
- Splitted TeraWURFL sql dumps
- Added fallback for MySQL4
- Improved checks for stored procedures
- Small CSS fixes
- Changed imageadaption to use Joomla's libraries, eliminates permission errors
- Image scaling fixed for iPhone

0.8.1
Second iteration on private alpha.
- Switched to TeraWURFL as main device detection
- Installation shortened, now it is one click
- Some fixes regarding PHP4 compatibility (although actually we do not support it)
- Memory issues should be obselete (all was related to the old WURFL)

0.8.0
Initial release. Alpha & privately distributed.
