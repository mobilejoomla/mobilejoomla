<?php // no direct access
defined('_JEXEC') or die('Restricted access');

if ($showmode == 0 || $showmode == 2) :
	if ($count['guest'] != 0 || $count['user'] != 0) :
		echo JText::_('We have') . ' ';
		if ($count['guest'] == 1) :
			echo JText::sprintf('guest', '1');
		else :
			if ($count['guest'] > 1) :
				echo JText::sprintf('guests', $count['guest']);
			endif;
		endif;

		if ($count['guest'] != 0 && $count['user'] != 0) :
			echo ' ' . JText::_('and') . ' ';
		endif;

		if ($count['user'] == 1) :
			echo JText::sprintf('member', '1');
		else :
			if ($count['user'] > 1) :
				echo JText::sprintf('members', $count['user']);
			endif;
		endif;
		echo ' ' . JText::_('online');
	endif;
endif;

if(($showmode > 0) && count($names)) : ?>
	<ul data-role="listview">
<?php foreach($names as $name) : ?>
		<li><strong><?php echo $name->username; ?></strong></li>
<?php endforeach;  ?>
	</ul>
<?php endif;