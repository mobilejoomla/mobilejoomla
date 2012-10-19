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

//load language file (to allow users to rename template)
$lang =& JFactory::getLanguage();
$lang->load('tpl_mobile_iphone');

$MobileJoomla =& MobileJoomla::getInstance();

$base = $this->baseurl.'/templates/'.$this->template;
$home = $this->baseurl.'/';

$MobileJoomla_Device =& MobileJoomla::getDevice();
if($MobileJoomla_Device['markup'] != $MobileJoomla_Device['default_markup'])
	$home .= '?device='.$MobileJoomla_Device['markup'];
?>
<!doctype html>
<html<?php echo $MobileJoomla->getXmlnsString(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php echo $MobileJoomla->getContentString(); ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no" />
	<meta name="format-detection" content="address=no" />
<?php $MobileJoomla->showHead(); ?>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/jqtouch-src/jqtouch/jqtouch.min.css";</style>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/jqtouch-src/themes/<?php echo $this->params->get('theme', 'apple'); ?>/theme.min.css";</style>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/css/mj_iphone.css";</style>
<?php if(@filesize(JPATH_SITE.DS.'templates'.DS.$this->template.DS.'css'.DS.'custom.css')): ?>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/css/custom.css";</style>
<?php endif; ?>
</head>
<body>
<div<?php echo ($MobileJoomla->isHome()) ? ' id="home"' : '';?> class="current">
	<div class="toolbar">
		<h1><?php
			$app =& JFactory::getApplication();
			echo htmlspecialchars_decode($app->getCfg('sitename'));
		?></h1>
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

$MobileJoomla->showMessage();

$modulepos = $MobileJoomla->getPosition('header3');
if($modulepos && $this->countModules($modulepos) > 0)
{
	$MobileJoomla->loadModules($modulepos);
}


$show_content = false;
$show_content |= $MobileJoomla->getParam('componenthome') || !$MobileJoomla->isHome();
$show_content |= $this->countModules($MobileJoomla->getPosition('middle'));
$show_content |= $this->countModules($MobileJoomla->getPosition('middle2'));

if($show_content): ?>
	<div class="content">
<?php
	$modulepos = $MobileJoomla->getPosition('middle');
	if($modulepos && $this->countModules($modulepos) > 0)
	{
		?><div id="<?php echo $modulepos; ?>"><?php $MobileJoomla->loadModules($modulepos); ?></div><?php
	}

	if($MobileJoomla->getParam('componenthome') || !$MobileJoomla->isHome()): ?>
		<div class="container">
			<?php $MobileJoomla->showComponent(); ?>
		</div>
<?php
	endif;

	$modulepos = $MobileJoomla->getPosition('middle2');
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

$modulepos = $MobileJoomla->getPosition('middle3');
if($modulepos && $this->countModules($modulepos) > 0)
{
	?><div id="<?php echo $modulepos; ?>">
		<?php $MobileJoomla->loadModules($modulepos); ?>
	</div><?php
}


$modulepos = $MobileJoomla->getPosition('footer');
if($modulepos && $this->countModules($modulepos) > 0)
{
	?><div id="<?php echo $modulepos; ?>" class="current">
		<?php $MobileJoomla->loadModules($modulepos); ?>
	</div><?php
}

$modulepos = $MobileJoomla->getPosition('footer2');
if($modulepos && $this->countModules($modulepos) > 0)
{
	?><div id="<?php echo $modulepos; ?>" class="current">
		<?php $MobileJoomla->loadModules($modulepos); ?>
	</div><?php
}

$MobileJoomla->showFooter();

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
<div class="mj-sponsor-placement">
<script type="text/javascript">
//<!--
/* <![CDATA[ */
document.write('<script src'+'="http'+('https:'==document.location.protocol?'s':'')+'://ads.mobilejoomla.com/ad.js?domain='+encodeURIComponent(window.location.hostname)+'" type="text/javascript"><'+'/script>');
/* ]]> */
//-->
</script>
</div>
<?php
}
?>
</div>
</body>
</html>