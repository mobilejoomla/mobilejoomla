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

/** @var $this JDocumentHTML */

/** @var JApplicationSite $app */
$app = JFactory::getApplication();
/** @var MobileJoomla $mj */
$mj_list = $app->triggerEvent('onGetMobileJoomla');
$mj = array_pop($mj_list);

include_once dirname(__FILE__).'/includes/mjjqmframework.php';
$mjJqmHelper = new MJJqmFramework($this);

$enable_backbutton = true;
// don't show Back button on devices with hardware back button
if(preg_match('/Android|BlackBerry/', @$_SERVER['HTTP_USER_AGENT']))
	$enable_backbutton = false;

//params
$theme_page    = $this->params->get('theme_page');
$theme_header  = $this->params->get('theme_header');
$theme_footer  = $this->params->get('theme_footer');

$app = JFactory::getApplication();
$doc = JFactory::getDocument();

$enhance_content = true;
if($this->params->get('enhance'))
{
	$option = $app->input->getCmd('option');
	$woEnhancing = explode(',', $this->params->get('enhance_coms', ''));
	if(in_array($option, $woEnhancing))
		$enhance_content = false;
}

$base = $this->baseurl.'/templates/'.$this->template;
$home = $this->baseurl.'/';
$base_full = JUri::base().'templates/'.$this->template;

/** @var MjDevice $mjDevice */
$mjDevice = $mj->device;
$markup = $mjDevice->markup;
if($markup != $mjDevice->default_markup)
	$home .= '?device='.$markup;

$tmpl_componenthome = $mj->getParam('componenthome');

include_once dirname(__FILE__).'/includes/process.php';


$toolbar = $mj->getToolbar();

if($mj->isHome() && !$app->input->getInt('limitstart'))
{
	$logo = $this->params->get('logo');
	if($logo == '' || $logo == '-') $this->params->set('title', 1);

	switch($this->params->get('title'))
	{
		case 2:
			$page_header = '<img src="'.JUri::base(true).$logo.'" ress-nowrap ress-fullwidth alt="">';
			break;
		case 3:
			$page_header = '<img src="'.JUri::base(true).$logo.'" ress-nowrap ress-fullwidth alt=""> '.$app->getCfg('sitename');
			break;
		case 1:
		default:
			$page_header = $app->getCfg('sitename');
			break;
	}
}
else
{
	if($enable_backbutton && !$toolbar->isBackButtonHidden())
		$toolbar->prependButton('left', 'javascript:history.back()', 'left',
			JText::_('TPL_MOBILE_JQM__BACK'),
            array('attrib'=>'data-rel="back" data-iconpos="notext"', 'class'=>'back')
		);

	if(!$toolbar->isHomeButtonHidden())
		$toolbar->prependButton('right', $home, 'home',
			JText::_('TPL_MOBILE_JQM__HOME'),
            array('attrib'=>'data-direction="reverse" data-iconpos="notext"', 'class'=>'home')
		);

	$page_header = $toolbar->getTitle();
	if($page_header==null)
		$page_header = $doc->getTitle();
}

$hasPanel = ($this->countModules('mj_panel') > 0);
if($hasPanel)
{
	$panelId = 'panel'.mt_rand();
	$toolbar->appendButton('left', '#'.$panelId, 'bars',
        JText::_('TPL_MOBILE_JQM__MENUBTN'),
        array('attrib'=>'data-iconpos="notext"', 'class'=>'panel')
    );
}

?>
<!doctype html>
<html>
<head>
<?php include dirname(__FILE__).'/includes/common-headers.php'; ?>
<mj:head/>
</head>
<body>
<div data-role="page"<?php
	if($theme_page) echo " data-theme=\"$theme_page\"";
	$class = '';
	if($mj->isHome()) $class .= " home";
    if($hasPanel) $class .= " ui-responsive-panel";
	if(!empty($class))
		echo ' class="' . trim($class) . '"';
?>>
<?php if($hasPanel) : ?>
<div class="ui-panel-wrapper">
<?php endif; ?>
<?php
	$attrs = '';
	if($theme_header)
		$attrs .= " data-theme=\"$theme_header\"";
?>
	<div class="header" data-role="header"<?php echo $attrs; ?>>
		<h1><?php echo $page_header; ?></h1>
<?php
	include_once dirname(__FILE__).'/includes/toolbar.php';

	$iconClass = false ? ' ui-icon-alt' : ''; /* inverse icons in header */

	$leftButtons = $toolbar->getButtons('left');
	if(count($leftButtons)):
		?><div class="ui-btn-left<?php echo $iconClass; ?>"><?php echo renderToolbar($leftButtons); ?></div><?php
	endif;

	$rightButtons = $toolbar->getButtons('right');
	if(count($rightButtons)):
		?><div class="ui-btn-right<?php echo $iconClass; ?>"><?php echo renderToolbar(array_reverse($rightButtons)); ?></div><?php
	endif;
?>
	</div><?php /* div[data-role=header] */ ?>
	<div class="ui-content" data-role="main">
<?php

	if($this->countModules('mj_top') > 0):
		?><div class="posmj_top"><jdoc:include type="modules" name="mj_top" style="mobile" /></div><?php
	endif;
?>
	<div class="content">
<?php

	if($this->countModules('mj_top2') > 0):
		?><div class="posmj_top2"><jdoc:include type="modules" name="mj_top2" style="mobile" /></div><?php
	endif;

	?><jdoc:include type="message" /><?php

	if($this->countModules('mj_top3') > 0):
		?><div class="posmj_top3"><jdoc:include type="modules" name="mj_top3" style="mobile" /></div><?php
	endif;

	if($tmpl_componenthome || !$mj->isHome()):
		?><div class="jcomponent"<?php if(!$enhance_content) echo ' data-enhance="false"'; ?>><jdoc:include type="component" /></div><?php
	endif;

	if($this->countModules('mj_middle') > 0):
		?><div class="posmj_middle"><jdoc:include type="modules" name="mj_middle" style="mobile" /></div><?php
	endif;

?>
	</div><?php /* div.content */ ?>
<?php

	if($this->countModules('mj_middle2') > 0):
		?><div class="posmj_middle2"><jdoc:include type="modules" name="mj_middle2" style="mobile" /></div><?php
	endif;
?>
	</div><?php /* div[data-role=content] */ ?>
<?php
$hasFooter = $mj->getParam('jfooter') ||
			 $this->countModules('mj_footer') ||
			 $this->countModules('mj_footer2');
$footermode = $this->params->get('footermode');
if($footermode !== 'hide' && $hasFooter) :
	$attrs = '';
	if($theme_footer)
		$attrs .= " data-theme=\"$theme_footer\"";
	switch($footermode)
	{
		case 'fixed':
			$attrs .= ' data-position="fixed"';
			break;
		case 'fullscreen':
			$attrs .= ' data-position="fixed" data-fullscreen="true"';
			break;
	}
?>
	<div class="footer" data-role="footer"<?php echo $attrs; ?>><?php

		$dispatcher = JDispatcher::getInstance();
		$results = $dispatcher->trigger('onMobileJoomlaAdCheck', array());
		if(!in_array('f3da4a6dd8f15c9170572d18838c841e', array_map('md5', $results)))
		{
			?><div class="mj-sponsor-placement"><script type="text/javascript" ress-noasync ress-nomerge src="//ads.mobilejoomla.com/ad.js?domain=<?php echo $_SERVER['HTTP_HOST']; ?>"></script></div><?php
		}

		if($this->countModules('mj_footer') > 0):
			?><div class="posmj_footer"><jdoc:include type="modules" name="mj_footer" style="mobile" /></div><?php
		endif;

		$mj->generator->showFooter();

		if($this->countModules('mj_footer2') > 0):
			?><div class="posmj_footer2"><jdoc:include type="modules" name="mj_footer2" style="mobile" /></div><?php
		endif;

	?></div><?php /* div[data-role=footer] */ ?>
<?php endif; ?>
<?php if($hasPanel):
        ?></div><?php /* div.ui-panel-wrapper */
		?><div data-role="panel" data-display="push" id="<?php echo $panelId; ?>">
			<div class="posmj_panel"><jdoc:include type="modules" name="mj_panel" style="mobile" /></div>
		</div><?php /* div[data-role=panel] */
	endif;
?>
</div><?php /* div[data-role=page] */ ?>
</body>
</html>