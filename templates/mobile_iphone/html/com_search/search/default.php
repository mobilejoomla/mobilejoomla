<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php /*
<?php if ( $this->params->get( 'show_page_title', 0 ) ) : ?>
<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<?php echo $this->params->get( 'page_title' ); ?>
</div>
<?php endif; ?>
*/ ?>

<?php /* echo $this->loadTemplate('form'); */ ?>

<?php if ($this->total === 0) : ?>
	<div class="info">
		<?php echo $this->result; ?>
	</div>
<?php endif; ?>

<?php if(!$this->error && count($this->results) > 0) :
	echo $this->loadTemplate('results');
else :
	echo $this->loadTemplate('error');
endif; ?>
