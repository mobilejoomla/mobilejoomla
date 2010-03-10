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

defined( '_MJ' ) or die( 'Incorrect using of Kuneri Mobile Joomla.' );

$MobileJoomla =& MobileJoomla::getInstance();

$MobileJoomla->showXMLheader();
$MobileJoomla->showDocType();

$base = $this->baseurl . "/templates/" . $this->template;

?>


<!doctype html>
<html <?php echo $MobileJoomla->getXmlnsString(); ?>>
    <head>
        <meta http-equiv="Content-Type" content="<?php echo $MobileJoomla->getContentString(); ?>" />
        <title>Mobile Joomla!</title>
        <style type="text/css" media="screen">@import "<?=$base?>/resources/styles/reset.css";</style>
        <style type="text/css" media="screen">@import "<?=$base?>/resources/styles/baseStyles.css";</style>
        <style type="text/css" media="screen">@import "<?=$base?>/mj_xhtml.css";</style>
        <script type="text/javascript" src="<?=$base?>/resources/scripts/templates.js"></script>
        <script src="<?=$base?>/mj_xhtml.js" type="text/javascript" charset="utf-8"></script>         
    </head>
    <body>
    <?php $MobileJoomla->showMainBody(); ?>
    </body>
</html>