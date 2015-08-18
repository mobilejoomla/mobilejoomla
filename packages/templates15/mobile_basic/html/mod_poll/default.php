<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php" method="post" name="form2">

<fieldset data-role="controlgroup" class="poll<?php echo $params->get('moduleclass_sfx'); ?>">
<legend><?php echo $poll->title; ?></legend>
<?php for ($i = 0, $n = count($options); $i < $n; $i ++) : ?>
	<input type="radio" name="voteid" id="voteid<?php echo $options[$i]->id;?>" value="<?php echo $options[$i]->id;?>" alt="<?php echo $options[$i]->id;?>" />
	<label for="voteid<?php echo $options[$i]->id;?>"><?php echo $options[$i]->text; ?></label>
<?php endfor; ?>
</fieldset>
<fieldset class="ui-grid-a">
	<div class="ui-block-a"><input data-icon="check" type="submit" name="task_button" class="button" value="<?php echo JText::_('Vote'); ?>" /></div>
	<div class="ui-block-b"><a data-icon="grid" href="<?php echo JRoute::_("index.php?option=com_poll&id=$poll->slug".$itemid); ?>" data-role="button" class="button"><?php echo JText::_('Results'); ?></a></div>
</fieldset>

	<input type="hidden" name="option" value="com_poll" />
	<input type="hidden" name="task" value="vote" />
	<input type="hidden" name="id" value="<?php echo $poll->id;?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>