<div id="mjmsgarea"></div>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<?php
foreach($config_blobs as $tab_name=>$sections)
{
	$tab_id = preg_replace('/[\W\s]/', '', $tab_name);
	JSubMenuHelper::addEntry(JText::_($tab_name), '#'.$tab_id);
	echo "<div id=\"$tab_id\">";
	foreach($sections as $i=>$subsections)
	{
		echo ($i%2) ? '<div class="width-40 fltrt">' : '<div class="width-50 fltlft">';
		foreach($subsections as $section_title=>$fields)
		{
			echo '<fieldset><legend>'.JText::_($section_title).'</legend><table>';
			foreach($fields as $j=>$row)
			{
				echo "<tr><th>{$row['label_blob']}</th><td>{$row['input_blob']}</td></tr>";
			}
			echo '</table></fieldset>';
		}
		echo ($i%2) ? '</div><div class="clr"></div>' : '</div>';
	}
	echo "</div>";
}
echo JHTML::_('form.token');
?>
<input type="hidden" name="option" value="<?php echo JRequest::getString('option'); ?>"/>
<input type="hidden" name="task" value=""/>
<?php if(substr(JVERSION,0,3) == '1.5') : ?>
<script type="text/javascript" src="<?php echo JURI::root(true);?>/includes/js/overlib_mini.js"></script>
<?php endif; ?>
</form>