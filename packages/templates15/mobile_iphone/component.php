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

$base = $this->baseurl.'/templates/'.$this->template;

?>
<!doctype html>
<html <?php echo $MobileJoomla->getXmlnsString(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php echo $MobileJoomla->getContentString(); ?>"/>
<?php $MobileJoomla->showHead(); ?>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/jqtouch-src/jqtouch/jqtouch.min.css";</style>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/jqtouch-src/themes/<?php echo $this->params->get('theme', 'apple'); ?>/theme.min.css";</style>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/css/mj_iphone.css";</style>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0"/>
</head>
<body>
<div<?php echo ($MobileJoomla->_ishomepage) ? ' id="home"' : '';?> class="current">
<?php $MobileJoomla->showMessage(); ?>
<?php $MobileJoomla->showComponent(); ?>
</div>
</body>
</html>