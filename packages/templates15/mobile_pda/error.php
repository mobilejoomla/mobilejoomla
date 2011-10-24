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
?>
<html<?php echo $MobileJoomla->getXmlnsString(); ?>>
<head>
<title><?php echo $this->title; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
</head>
<body>
	<p><b><?php echo $this->error->get('code'); ?> - <?php echo $this->error->get('message'); ?></b></p>
	<?php if($this->debug) echo '<p>'.$this->renderBacktrace().'</p>'; ?>
</body>
</html>