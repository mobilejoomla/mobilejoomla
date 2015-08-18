<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php if($this->error): ?>
<div class="error">
	<?php echo $this->escape($this->error); ?>
</div>
<?php endif; ?>