<?php defined('_JEXEC') or die('Restricted access'); ?>

<div id="searchresults" class="edgetoedge">
<ul>

<?php foreach ($this->results as $result) : ?>
<li class="searchresult">
	<?php if ( $result->href ) : ?>
		<?php if ($result->browsernav == 1 ) : ?>
			<a href="<?php echo JRoute::_($result->href); ?>" target="_blank" class="searchresult">
		<?php else : ?>
			<a href="<?php echo JRoute::_($result->href); ?>" class="searchresult">
		<?php endif; ?>
	<?php endif; ?>

	<h3>
		<?php echo $this->pagination->limitstart + $result->count.'. '; ?>
		<?php echo $this->escape($result->title); ?>
	</h3>
	<?php if ( $result->section ) : ?>
		<p class="section">(<?php echo $this->escape($result->section); ?>)</p>
	<?php endif; ?>

	<p><?php echo $result->text; ?></p>

	<?php if ( $this->params->get( 'show_date' )) : ?>
		<?php echo $result->created; ?>
	<?php endif; ?>

	<?php if ( $result->href ) : ?>
		</a>
	<?php endif; ?>
</li>
<?php endforeach; ?>

</ul>
</div>
<div class="searchresultsnav">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
