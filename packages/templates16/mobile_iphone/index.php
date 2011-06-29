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
$MobileJoomla_Device =& MobileJoomla::getDevice();

$version = new JVersion;
$is_joomla16 = (substr($version->getShortVersion(),0,3) != '1.5');

$base = JURI::base()."templates/".$this->template;
$homepage = JURI::base();
if(!empty ($MobileJoomla->config['tmpl_iphone_homepage']))
	$homepage = $MobileJoomla->config['tmpl_iphone_homepage'];

/** @var JMenuSite $menu */
$menu =& JSite::getMenu();
$activemenu =& $menu->getActive();
$hasSubmenus = (bool)$menu->getItems($is_joomla16 ? 'parent_id' : 'parent', $activemenu->id);
?>
<!doctype html>
<html <?php echo $MobileJoomla->getXmlnsString(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php echo $MobileJoomla->getContentString(); ?>"/>
<?php $MobileJoomla->showHead(); ?>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/jqtouch-src/jqtouch/jqtouch.min.css";</style>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/jqtouch-src/themes/apple/theme.min.css";</style>
	<style type="text/css" media="screen">@import "<?php echo $base;?>/css/mj_iphone.css";</style>
	<meta name="viewport" content="width = <?php echo (int)$MobileJoomla_Device['screenwidth']; ?>, initial-scale = 1.0, user-scalable = no, maximum-scale = 1.0">
</head>
<body>
<div<?php echo ($MobileJoomla->_ishomepage) ? ' id="home"' : '';?> class="current">
	<div class="toolbar">
		<h1><?php /** @var JSite $app */ $app =& JFactory::getApplication(); echo $app->getCfg('sitename'); ?></h1>
	<?php if(!$MobileJoomla->_ishomepage): ?>
		<a class="back" href="javascript:history.go(-1)"><?php echo JText::_('TPL_MOBILE_IPHONE__BACK'); ?></a>
		<a class="home" href="<?php echo $homepage;?>"><?php echo JText::_('TPL_MOBILE_IPHONE__HOME'); ?></a>
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

if($MobileJoomla->config['tmpl_iphone_pathway'] && (!$MobileJoomla->_ishomepage || $MobileJoomla->config['tmpl_iphone_pathwayhome'])): ?>
	<div class="content">
		<?php $MobileJoomla->showBreadcrumbs(); ?>
	</div>
<?php
endif;

$modulepos = $MobileJoomla->getPosition('middle');
if($modulepos && $this->countModules($modulepos) > 0 && ($MobileJoomla->_ishomepage || $hasSubmenus))
{
	?><div id="<?php echo $modulepos; ?>"><?php $MobileJoomla->loadModules($modulepos); ?></div><?php
}

if(!(!$MobileJoomla->config['tmpl_iphone_componenthome'] && $MobileJoomla->_ishomepage)): ?>
	<div class="content">
		<div class="container">
			<?php $MobileJoomla->showComponent(); ?>
		</div>
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
    	 (function(){
    		var loc = "cloud-ad.appspot.com/ad.js"
    		var uri = "?domain=" + encodeURIComponent(window.location.hostname);
    		var src = ('https:' == document.location.protocol ? 'https://' : 'http://') + loc + uri;
    		var strings = ['<script src="',  src, '" type="text/javascript">', '<', '/script>'].join('');
    		document.write(strings);
    	})();
    	/* ]]> */
    	//-->
    </script>
<?php
    }
?>
</div>
</body>
</html>