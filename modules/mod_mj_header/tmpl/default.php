<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<?php if ($showTitle):?>
<h1><?php echo $siteTitle;?></h1>
<h2><?php echo $pageTitle;?></h2>
<?php else:?>
<img src="<?php echo $imgURL;?>" alt="<?php echo $siteTitle;?>" />
<?php endif;?>