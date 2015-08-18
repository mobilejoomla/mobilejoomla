<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div class="profile<?php echo $this->escape($this->params->get('pageclass_sfx'));?>">
<?php echo nl2br($this->escape($this->params->get('welcome_desc', JText::_( 'WELCOME_DESC' )))); ?>
</div>