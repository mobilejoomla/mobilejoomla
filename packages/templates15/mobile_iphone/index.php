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
$home = $this->baseurl.'/';

$MobileJoomla_Device =& MobileJoomla::getDevice();
if($MobileJoomla_Device['markup'] != $MobileJoomla_Device['default_markup'])
	$home .= '?device='.$MobileJoomla_Device['markup'];
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
<div<?php echo ($MobileJoomla->isHome()) ? ' id="home"' : '';?> class="current">
	<div class="toolbar">
		<h1><?php /** @var JSite $app */ $app =& JFactory::getApplication(); echo $app->getCfg('sitename'); ?></h1>
	<?php if(!$MobileJoomla->isHome()): ?>
		<a class="back" href="javascript:history.go(-1)"><?php echo JText::_('TPL_MOBILE_IPHONE__BACK'); ?></a>
		<a class="home" href="<?php echo $home; ?>"><?php echo JText::_('TPL_MOBILE_IPHONE__HOME'); ?></a>
	<?php endif;?>
	</div>
<?php

$modulepos = $MobileJoomla->getPosition('header');
if($modulepos && $this->countModules($modulepos) > 0)
{
	$MobileJoomla->loadModules($modulepos);
}
$modulepos = $MobileJoomla->getPosition('header2');
if($modulepos && $this->countModules($modulepos) > 0)
{
	$MobileJoomla->loadModules($modulepos);
}
$modulepos = $MobileJoomla->getPosition('header3');
if($modulepos && $this->countModules($modulepos) > 0)
{
	$MobileJoomla->loadModules($modulepos);
}

$MobileJoomla->showMessage();

if($MobileJoomla->config['tmpl_iphone_pathway'] && (!$MobileJoomla->isHome() || $MobileJoomla->config['tmpl_iphone_pathwayhome'])): ?>
	<div class="content">
		<?php $MobileJoomla->showBreadcrumbs(); ?>
	</div>
<?php
endif;

$modulepos = $MobileJoomla->getPosition('middle');
if($modulepos && $this->countModules($modulepos) > 0)
{
	?><div id="<?php echo $modulepos; ?>"><?php $MobileJoomla->loadModules($modulepos); ?></div><?php
}

$show_content = false;
$show_content |= $MobileJoomla->config['tmpl_iphone_componenthome'] || !$MobileJoomla->isHome();
$show_content |= $this->countModules($MobileJoomla->getPosition('middle2'));
$show_content |= $this->countModules($MobileJoomla->getPosition('middle3'));

if($show_content): ?>
	<div class="content">
<?php if($MobileJoomla->config['tmpl_iphone_componenthome'] || !$MobileJoomla->isHome()): ?>
		<div class="container">
			<?php $MobileJoomla->showComponent(); ?>
		</div>
<?php endif; ?>
<?php
	$modulepos = $MobileJoomla->getPosition('middle2');
	if($modulepos && $this->countModules($modulepos) > 0)
	{
		?><div id="<?php echo $modulepos; ?>">
			<?php $MobileJoomla->loadModules($modulepos); ?>
		</div><?php
	}

	$modulepos = $MobileJoomla->getPosition('middle3');
	if($modulepos && $this->countModules($modulepos) > 0)
	{
		?><div id="<?php echo $modulepos; ?>">
			<?php $MobileJoomla->loadModules($modulepos); ?>
		</div><?php
	}
?>
	</div>
<?php
endif;

$modulepos = $MobileJoomla->getPosition('footer');
if($modulepos && $this->countModules($modulepos) > 0)
{
	?><div id="<?php echo $modulepos; ?>" class="current">
		<?php $MobileJoomla->loadModules($modulepos); ?>
	</div><?php
}

$MobileJoomla->showFooter();

$modulepos = $MobileJoomla->getPosition('footer2');
if($modulepos && $this->countModules($modulepos) > 0)
{
	?><div id="<?php echo $modulepos; ?>" class="current">
		<?php $MobileJoomla->loadModules($modulepos); ?>
	</div><?php
}
$modulepos = $MobileJoomla->getPosition('footer3');
if($modulepos && $this->countModules($modulepos) > 0)
{
	?><div id="<?php echo $modulepos; ?>" class="current">
		<?php $MobileJoomla->loadModules($modulepos); ?>
	</div><?php
}

$dispatcher =& JDispatcher::getInstance(); 
$results = $dispatcher->trigger( 'onMobileJoomlaAdCheck', array() );
if(in_array('f3da4a6dd8f15c9170572d18838c841e', array_map('md5', $results)))
{
	// The user has installed plug-in to remove ads
}
else
{
?>
	<script type="text/javascript">
	//<!--
	/* <![CDATA[ */
	window.googleAfmcRequest = {
	  client: 'ca-mb-pub-5710199815985059',
	  format: '320x50_mb',
	  output: 'html',
	  slotname: '1896811186',
	};
	/* ]]> */
	//-->
	</script>
	<script type="text/javascript"src="http://pagead2.googlesyndication.com/pagead/show_afmc_ads.js"></script>
<?php
}
?>
</div>
</body>
</html>