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
/** @var $showTitle bool */
/** @var $siteTitle string */
/** @var $imgURL string */
?>

<?php if($showTitle): ?>

<?php if(!empty($siteTitle)): ?>
<h1><?php echo $siteTitle;?></h1>
<?php endif; ?>

<?php if(!empty($pageTitle)): ?>
<h2><?php echo $pageTitle;?></h2>
<?php endif; ?>

<?php else: ?>

<img src="<?php echo $imgURL;?>" alt="<?php echo $siteTitle;?>" class="mj-fullwidth"/>

<?php endif; ?>