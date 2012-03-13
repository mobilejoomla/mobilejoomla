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

$MobileJoomla = MobileJoomla::getInstance();
$MobileJoomla_Device =& MobileJoomla::getDevice();

$MobileJoomla->showXMLheader();
$MobileJoomla->showDocType();
$base = $this->baseurl."/templates/".$this->template;
?>
<html<?php echo $MobileJoomla->getXmlnsString(); ?>>
<head>
<?php $MobileJoomla->showHead(); ?>
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />
	<meta name="HandheldFriendly" content="True" />
	<meta name="MobileOptimized" content="<?php echo $MobileJoomla_Device['screenwidth']; ?>" />
	<meta http-equiv="cleartype" content="on" />
	<meta name="format-detection" content="telephone=no" />
	<meta name="format-detection" content="address=no" />
	<style type="text/css" media="screen">@import "<?php echo $base;?>/resources/styles/reset.css";</style>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/resources/styles/baseStyles.css";</style>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/css/mj_xhtml.css";</style>
<?php
	if(@filesize(JPATH_SITE.DS.'templates'.DS.$this->template.DS.'css'.DS.'custom.css'))
	{
		if($MobileJoomla->getParam('embedcss', false))
		{
			echo "<style>\n";
			@readfile(JPATH_SITE.DS.'templates'.DS.$this->template.DS.'css'.DS.'custom.css');
			echo "</style>\n";
		}
		else
		{
?>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/css/custom.css";</style>
<?php
		}
	}
?>
	<script type="text/javascript" src="<?php echo $base;?>/resources/scripts/templates.js"></script>
	<script src="<?php echo $base;?>/mj_xhtml.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
<?php $MobileJoomla->showMessage(); ?>
<?php $MobileJoomla->showComponent(); ?>
</body>
</html>