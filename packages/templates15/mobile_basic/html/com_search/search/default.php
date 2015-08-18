<?php defined('_JEXEC') or die('Restricted access'); ?>
<div class="search<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
<?php echo $this->loadTemplate('form'); ?>
<?php if(!$this->error && count($this->results) > 0) :
	echo $this->loadTemplate('results');
else :
	echo $this->loadTemplate('error');
endif; ?>
</div>