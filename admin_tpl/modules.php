<?php

// load modules list
$db = JFactory::getDBO();

$query = "SELECT id, title, module, position FROM #__modules WHERE client_id=0 AND published=1 AND position<>'' ORDER BY position, ordering, title";
$db->setQuery($query);
$modules = $db->loadObjectList('id');

// load mj settings
$query = 'SELECT * FROM #__mj_modules';
$db->setQuery($query);
$mj_modules = $db->loadObjectList();
if(empty($mj_modules))
	$mj_modules = array();

$modes = array('desktop' => 'Desktop',
				'xhtml' => 'Smartphone',
				'iphone' => 'iPhone',
				'tablet' => 'Tablet',
				'chtml' => 'i-mode',
				'wml'=>'WAP');
foreach($modes as $device=>$title)
{
	if(plgSystemMobileBot::CheckMarkup($device) === false)
		unset($modes[$device]);
}

$table = array();
if(count($modules))
	foreach($modules as $module)
		$table[$module->id] = array('desktop'=>1,'xhtml'=>1,'iphone'=>1,'tablet'=>1,'chtml'=>1,'wml'=>1);

foreach($mj_modules as $module)
	$table[$module->id][$module->markup] = 0;
?>
<html>
<head>
	<link rel="stylesheet" href="components/com_mobilejoomla/css/extmanager.css" />
	<script type="text/javascript">
		mj_extmanager_action = 'set_module_state';
	</script>
<?php if(substr(JVERSION,0,3) == '1.5') : ?>
	<script src="../media/system/js/mootools.js" type="text/javascript"></script>
<?php else : ?>
	<script src="../media/system/js/mootools-core.js" type="text/javascript"></script>
<?php endif; ?>
	<script src="components/com_mobilejoomla/js/extmanager.js" type="text/javascript"></script>
</head>
<body>
	<table>
		<tr>
			<th>Module</th>
			<th>Position</th>
<?php foreach($modes as $device=>$title) : ?>
			<th><div class="vert"><?php echo $title; ?></div></th>
<?php endforeach; ?>
		</tr>
<?php $odd = true; ?>
<?php foreach($modules as $module): ?>
		<tr class="<?php echo $odd ? 'odd' : 'even'; $odd = !$odd; ?>">
			<td>
				<?php echo $module->title; ?><br/>
				<span class="type"><?php echo $module->module; ?></span>
			</td>
			<td><?php echo $module->position; ?></td>
<?php foreach($modes as $device=>$title) : ?>
			<td><a class="link" onclick="change(<?php echo $module->id; ?>, '<?php echo $device; ?>', this);">
				<?php echo MJExtManager::getImage($table[$module->id][$device]); ?>
			</a></td>
<?php endforeach; ?>
		</tr>
<?php endforeach; ?>
	</table>
</body>
</html>