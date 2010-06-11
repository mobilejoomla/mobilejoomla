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
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<title><?php echo $this->error->code ?> - <?php echo $this->title; ?></title>
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/error.css" type="text/css" />
	<?php if($this->direction == 'rtl') : ?>
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/error_rtl.css" type="text/css" />
	<?php endif; ?>
</head>
<body>
	<div align="center">
		<div id="outline">
		<div id="errorboxoutline">
			<div id="errorboxheader"><?php echo $this->error->code ?> - <?php echo $this->error->message ?></div>
			<div id="errorboxbody">
			<p><strong><?php echo JText::_('You may not be able to visit this page because of:'); ?></strong></p>
				<ol>
					<li><?php echo JText::_('An out-of-date bookmark/favourite'); ?></li>
					<li><?php echo JText::_('A search engine that has an out-of-date listing for this site'); ?></li>
					<li><?php echo JText::_('A mis-typed address'); ?></li>
					<li><?php echo JText::_('You have no access to this page'); ?></li>
					<li><?php echo JText::_('The requested resource was not found'); ?></li>
					<li><?php echo JText::_('An error has occurred while processing your request.'); ?></li>
				</ol>
			<p><strong><?php echo JText::_('Please try one of the following pages:'); ?></strong></p>
			<p>
				<ul>
					<li><a href="<?php echo $this->baseurl; ?>/index.php" title="<?php echo JText::_('Go to the home page'); ?>"><?php echo JText::_('Home Page'); ?></a></li>
				</ul>
			</p>
			<p><?php echo JText::_('If difficulties persist, please contact the system administrator of this site.'); ?></p>
			<div id="techinfo">
			<p><?php echo $this->error->message; ?></p>
			<p>
				<?php if($this->debug) :
					echo $this->renderBacktrace();
				endif; ?>
			</p>
			</div>
			</div>
		</div>
		</div>
	</div>
</body>
</html>