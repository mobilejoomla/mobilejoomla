<?php
/**
 * Kuneri Mobile Joomla! for Joomla!1.5
 * http://www.mobilejoomla.com/
 *
 * @version		0.9.0
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	Copyright (C) 2008-2009 Kuneri Ltd. All rights reserved.
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

defined( '_MJ' ) or die( 'Incorrect using of Kuneri Mobile Joomla.' );

$MobileJoomla =& MobileJoomla::getInstance();

$MobileJoomla->showDocType();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="<?php echo $MobileJoomla->getContentString(); ?>">
<?php $MobileJoomla->showHead(); ?>
</head>
<body>
<?php

$modulepos=$MobileJoomla->getPosition('header');
if( $modulepos && $this->countModules($modulepos)>0)
	$MobileJoomla->loadModules($modulepos);
$modulepos=$MobileJoomla->getPosition('header2');
if( $modulepos && $this->countModules($modulepos)>0)
	$MobileJoomla->loadModules($modulepos);

$MobileJoomla->showPathway();

$modulepos=$MobileJoomla->getPosition('middle');
if( $modulepos && $this->countModules($modulepos)>0)
	$MobileJoomla->loadModules($modulepos);
$modulepos=$MobileJoomla->getPosition('middle2');
if( $modulepos && $this->countModules($modulepos)>0)
	$MobileJoomla->loadModules($modulepos);

$MobileJoomla->showMainBody();

$modulepos=$MobileJoomla->getPosition('footer');
if( $modulepos && $this->countModules($modulepos)>0)
	$MobileJoomla->loadModules($modulepos);
$modulepos=$MobileJoomla->getPosition('footer2');
if( $modulepos && $this->countModules($modulepos)>0)
	$MobileJoomla->loadModules($modulepos);

$MobileJoomla->showFooter();
?>
</body></html>