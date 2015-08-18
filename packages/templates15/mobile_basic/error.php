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
defined( '_JEXEC' ) or die( 'Restricted access' );

//defined('_MJ') or die('Incorrect usage of Mobile Joomla!');

if(!is_object($this->params))
	$this->params = new JParameter('');

/** @var JApplicationSite $app */
$app = JFactory::getApplication();
/** @var MobileJoomla $mj */
$mj_list = $app->triggerEvent('onGetMobileJoomla');
$mj = array_pop($mj_list);

$mjDevice = $mj->device;

$base = $this->baseurl.'/templates/'.$this->template;

$jqmVer = '1.4.5';

$jqm_css = $base.'/vendor/jqm/jquery.mobile-'.$jqmVer.'.min.css';
$jqm_jq  = $base.'/vendor/jqm/jquery-1.9.1.min.js';
$jqm_jqm = $base.'/vendor/jqm/jquery.mobile-'.$jqmVer.'.min.js';

?>
<!doctype html>
<html>
<head>
<?php include dirname(__FILE__).'/includes/common-headers.php'; ?>
	<title><?php echo $this->error->getCode(); ?> - <?php echo $this->title; ?></title>
	<link rel="stylesheet" href="<?php echo $jqm_css;?>">
	<link rel="stylesheet" href="<?php echo $base;?>/css/mj.css">
	<link rel="stylesheet" href="<?php echo $base;?>/css/structure.css">
<?php if(@filesize(JPATH_SITE.'/templates/'.$this->template.'/css/custom.css')): ?>
	<link rel="stylesheet" href="<?php echo $base;?>/css/custom.css">
<?php endif; ?>
	<script type="text/javascript" src="<?php echo $jqm_jq;?>"></script>
	<script type="text/javascript" src="<?php echo $jqm_jqm;?>"></script>
</head>
<body>
<div data-role="page" class="ui-dialog">
	<div data-role="header" data-position="inline" class="header ui-corner-top ui-overlay-shadow">
		<h1><?php echo $this->error->getMessage(); ?></h1>
	</div>
	<div data-role="main" class="content ui-content ui-corner-bottom ui-overlay-shadow">
		<h1><?php echo $this->error->getCode(); ?> - <?php echo $this->error->getMessage(); ?></h1>
		<p><strong><?php echo JText::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></strong></p>
			<ol>
				<li><?php echo JText::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
				<li><?php echo JText::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
				<li><?php echo JText::_('JERROR_LAYOUT_MIS_TYPED_ADDRESS'); ?></li>
				<li><?php echo JText::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
				<li><?php echo JText::_('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND'); ?></li>
				<li><?php echo JText::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></li>
			</ol>
		<p><strong><?php echo JText::_('JERROR_LAYOUT_PLEASE_TRY_ONE_OF_THE_FOLLOWING_PAGES'); ?></strong></p>
			<ul data-role="listview" data-inset="true">
				<li><a href="<?php echo $this->baseurl; ?>/index.php" title="<?php echo JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?>" data-ajax="false"><?php echo JText::_('JERROR_LAYOUT_HOME_PAGE'); ?></a></li>
			</ul>
		<p><?php echo JText::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?></p>
		<?php if($this->debug) : ?>
			<div>
				<p><strong><?php echo $this->error->getMessage(); ?></strong></p>
				<p><?php echo $this->renderBacktrace(); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>
</body>
</html>