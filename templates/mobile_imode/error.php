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
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

defined('_MJ') or die('Incorrect using of Kuneri Mobile Joomla.');

$MobileJoomla =& MobileJoomla::getInstance();

$MobileJoomla->showDocType();
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="<?php echo $MobileJoomla->getContentString(); ?>">
<?php $MobileJoomla->showHead(); ?>
</head>
<body>
<div><b><?php echo $this->error->code ?> - <?php echo $this->error->message ?></b></div>
<?php if($this->debug) echo '<div>'.$this->renderBacktrace().'</div>'; ?>
</body>
</html>