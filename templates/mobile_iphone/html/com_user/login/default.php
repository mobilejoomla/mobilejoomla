<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php if ($this->params->get( 'show_page_title', 1)) : ?>
<div class="h3 componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<?php echo $this->escape($this->params->get('page_title')); ?>
</div>
<?php endif; ?>
<?php echo $this->loadTemplate($this->type); ?>

