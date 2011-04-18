<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div class="h3 componentheading">
	<?php echo $this->escape($this->message->title) ; ?>
</div>

<div class="message">
	<p class="textview"><?php echo $this->escape($this->message->text) ; ?></p>
</div>
