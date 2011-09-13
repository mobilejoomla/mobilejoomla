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

defined('_MJ') or die('Incorrect usage of Mobile Joomla.');

$MobileJoomla =& MobileJoomla::getInstance();

$MobileJoomla->showXMLheader();
$MobileJoomla->showDocType();
$base = $this->baseurl."/templates/".$this->template;
?>
<html<?php echo $MobileJoomla->getXmlnsString(); ?>>
<head>
<?php $MobileJoomla->showHead(); ?>
	<meta http-equiv="Content-Type" content="<?php echo $MobileJoomla->getContentString(); ?>"/>
	<title>Mobile Joomla!</title>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/resources/styles/reset.css";</style>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/resources/styles/baseStyles.css";</style>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/mj_xhtml.css";</style>
<?php if(is_file(JPATH_SITE.DS.'templates'.DS.$this->template.DS.'css'.DS.'custom.css')): ?>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/css/custom.css";</style>
<?php endif; ?>
	<script type="text/javascript" src="<?php echo $base;?>/resources/scripts/templates.js"></script>
	<script src="<?php echo $base;?>/mj_xhtml.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
<?php $MobileJoomla->showMessage(); ?>
<?php $MobileJoomla->showComponent(); ?>
</body>
</html>