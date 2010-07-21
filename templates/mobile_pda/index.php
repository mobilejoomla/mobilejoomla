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
$base = JURI::base()."templates/".$this->template;
?>
<html<?php echo $MobileJoomla->getXmlnsString(); ?>>
<head>
<?php $MobileJoomla->showHead(); ?>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/resources/styles/reset.css";</style>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/resources/styles/baseStyles.css";</style>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/css/mj_xhtml.css";</style>
	<script type="text/javascript" src="<?php echo $base;?>/resources/scripts/templates.js"></script>
	<script src="<?php echo $base;?>/mj_xhtml.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
<div id="wrap">
	<div id="header">
<?php
		$modulepos = $MobileJoomla->getPosition('header');
		if($modulepos && $this->countModules($modulepos) > 0):
			?><div id="<?php echo $modulepos; ?>"><?php $MobileJoomla->loadModules($modulepos); ?></div><?php
		endif;
?>
	</div>
<?php
	$modulepos = $MobileJoomla->getPosition('header2');
	if($modulepos && $this->countModules($modulepos) > 0):
		?><div id="<?php echo $modulepos; ?>"><?php $MobileJoomla->loadModules($modulepos); ?></div><?php
	endif;
?>
	<div id="content">
<?php
		$modulepos = $MobileJoomla->getPosition('middle');
		if($modulepos && $this->countModules($modulepos) > 0):
			?><div id="<?php echo $modulepos; ?>"><?php $MobileJoomla->loadModules($modulepos); ?></div><?php
		endif;

		$MobileJoomla->showPathway();
		$MobileJoomla->showMainBody();

		$modulepos = $MobileJoomla->getPosition('middle2');
		if($modulepos && $this->countModules($modulepos) > 0):
			?><div id="<?php echo $modulepos; ?>"><?php $MobileJoomla->loadModules($modulepos); ?></div><?php
		endif;
?>
		<div class="top">
			<a href="#header">Back to the top</a>
		</div>
<?php
		if(!$MobileJoomla->_ishomepage)
		{
?>
			<div class="home">
				<a href="<?php echo $this->baseurl;?>">Home</a>
			</div>
<?php
		}
?>
	</div>
	<div id="footer">
<?php
		$modulepos = $MobileJoomla->getPosition('footer');
		if($modulepos && $this->countModules($modulepos) > 0):
			?><div id="<?php echo $modulepos; ?>"><?php $MobileJoomla->loadModules($modulepos); ?></div><?php
		endif;

		$MobileJoomla->showFooter();

		$modulepos = $MobileJoomla->getPosition('footer2');
		if($modulepos && $this->countModules($modulepos) > 0):
			?><div id="<?php echo $modulepos; ?>"><?php $MobileJoomla->loadModules($modulepos); ?></div><?php
		endif;
?>
	</div>
</div>
</body>
</html>