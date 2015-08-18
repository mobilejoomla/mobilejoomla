<?php defined('_JEXEC') or die('Restricted access'); ?>
<ul class="search-results<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" data-role="listview" data-inset="true">
<?php foreach( $this->results as $result ) : ?>
<li>
	<?php if ($result->href) :?>
	<a href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) :?> target="_blank"<?php endif;?>>
	<?php endif; ?>
	<?php if ( $result->section ) : ?>
		<p class="result-category">
			<span class="small<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
				(<?php echo $this->escape($result->section); ?>)
			</span>
		</p>
	<?php endif; ?>
	<h2 class="result-title">
		<?php echo $this->pagination->limitstart + $result->count.'. ';?>
		<?php echo $this->escape($result->title);?>
	</h2>
	<p class="result-text">
		<?php echo $result->text; ?>
	</p>
	<?php if ( $this->params->get( 'show_date' )) : ?>
		<p class="result-created<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
			<i><?php echo $result->created; ?></i>
		</p>
	<?php endif; ?>

	<?php if ($result->href) :?>
	</a>
	<?php endif; ?>
</li>
<?php endforeach; ?>
</ul>
<div class="pagination">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
