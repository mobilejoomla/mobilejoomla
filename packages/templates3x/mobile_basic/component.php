<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version    ###VERSION###
 * @license    ###LICENSE###
 * @copyright  ###COPYRIGHT###
 * @date       ###DATE###
 */
defined('_JEXEC') or die('Restricted access');

//defined('_MJ') or die('Incorrect usage of Mobile Joomla!');

/** @var JApplicationSite $app */
$app = JFactory::getApplication();
/** @var MobileJoomla $mj */
$mj_list = $app->triggerEvent('onGetMobileJoomla');
$mj = array_pop($mj_list);

//params
$theme_page    = $this->params->get('theme_page');
$theme_header  = $this->params->get('theme_header');

$doc = JFactory::getDocument();
$mjDevice = $mj->device;

$base = $this->baseurl.'/templates/'.$this->template;

include dirname(__FILE__).'/includes/process.php';

$jqm_ver = '1.4.5';

if($this->params->get('load_external'))
{
	if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS'])!='off'))
	{
		$jqm_css = 'https://ajax.aspnetcdn.com/ajax/jquery.mobile/'.$jqm_ver.'/jquery.mobile-'.$jqm_ver.'.min.css';
		$jqm_jq  = 'https://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.9.1.min.js';
		$jqm_jqm = 'https://ajax.aspnetcdn.com/ajax/jquery.mobile/'.$jqm_ver.'/jquery.mobile-'.$jqm_ver.'.min.js';
	}
	else
	{
		$jqm_css = 'http://code.jquery.com/mobile/'.$jqm_ver.'/jquery.mobile-'.$jqm_ver.'.min.css';
		$jqm_jq  = 'http://code.jquery.com/jquery-1.9.1.min.js';
		$jqm_jqm = 'http://code.jquery.com/mobile/'.$jqm_ver.'/jquery.mobile-'.$jqm_ver.'.min.js';
	}
}
else
{
	$jqm_css = $base.'/vendor/jqm/jquery.mobile-'.$jqm_ver.'.min.css';
	$jqm_jq  = $base.'/vendor/jqm/jquery-1.9.1.min.js';
	$jqm_jqm = $base.'/vendor/jqm/jquery.mobile-'.$jqm_ver.'.min.js';
}

$css_dir = dirname(__FILE__).'/css/';
$js_dir = dirname(__FILE__).'/js/';

$css_preload = array();
$js_preload  = array();

if(is_file($css_dir.'custom_preload.txt'))
	$custom_styles = @file($css_dir.'custom_preload.txt');
else
	$custom_styles = array();
$custom_styles[] = '*'.$jqm_css;
$custom_styles[] = '*'.$base.'/css/mj.css';
$custom_styles[] = '*'.$base.'/css/structure.css';
foreach($custom_styles as $url)
{
	$url = trim($url);
	if(strlen($url) && $url[0]=='*')
	{
		$url = ltrim(substr($url, 1));
		if(!isset($css_preload[$url]))
			$css_preload[$url] = 1;
	}
}

if(is_file($js_dir.'custom_preload.txt'))
	$custom_scripts = @file($js_dir.'custom_preload.txt');
else
	$custom_scripts = array();
$custom_scripts[] = '*'.$jqm_jq;
foreach($custom_scripts as $url)
{
	$url = trim($url);
	if(strlen($url) && $url[0]=='*')
	{
		$url = ltrim(substr($url, 1));
		if(!isset($js_preload[$url]))
			$js_preload[$url] = 1;
	}
}
?>
<!doctype html>
<html>
<head>
<?php include dirname(__FILE__).'/includes/common-headers.php'; ?>
<?php
	foreach($css_preload as $url=>$dummy)
		echo "<link rel=\"stylesheet\" href=\"$url\">\n";

	foreach($js_preload as $url=>$dummy)
		echo "<script type=\"text/javascript\" src=\"$url\"></script>\n";

	$mobileinit = array();
//	$mobileinit[] = 'jQuery.mobile.loadingMessage="'.addslashes(JText::_('TPL_MOBILE_JQM__LOADING')).'";';
	$mobileinit[] = 'jQuery.mobile.loadingMessage=false;';
	$mobileinit[] = 'jQuery.mobile.pageLoadErrorMessage="'.addslashes(JText::_('TPL_MOBILE_JQM__PAGELOADERROR')).'";';
//		$mobileinit[] = 'jQuery.mobile.ajaxEnabled=false;jQuery.mobile.pushStateEnabled=false;jQuery.mobile.hashListeningEnabled=false;';
		$mobileinit[] = 'jQuery.mobile.ajaxEnabled=false;jQuery.mobile.pushStateEnabled=false;';
?>
	<script type="text/javascript">jQuery(document).on('mobileinit',function(){<?php echo implode('', $mobileinit); ?>});</script>
	<script type="text/javascript" src="<?php echo $jqm_jqm;?>"></script>
	<script type="text/javascript">
		jqm = jQuery.noConflict();
<?php if($theme_header): ?>
		jqm.mobile.page.prototype.options.headerTheme='<?php echo $theme_header; ?>';
<?php endif ?>
	</script>
	<script type="text/javascript" src="<?php echo $base; ?>/js/pageinit.js"></script>
	<script type="text/javascript">
<?php
		if(is_file($js_dir.'custom.js'))
			include($js_dir.'custom.js');
?>
	</script>
	<mj:head/>
<?php if(@filesize($css_dir.'custom.css')): ?>
	<link rel="stylesheet" href="<?php echo $base;?>/css/custom.css">
<?php endif; ?>
</head>
<body>
<div data-role="page"<?php if($theme_page) echo " data-theme=\"$theme_page\""; ?>>
	<div class="content ui-content" data-role="main">
        <jdoc:include type="message" />
		<div class="jcomponent"<?php if(!$this->params->get('enhance')) echo ' data-enhance="false"'; ?>>
			<?php if (!$mj->isHome() || $mj->getParam('componenthome')) echo '<jdoc:include type="component" />'; ?>
		</div>
	</div>
</div>
</body>
</html>