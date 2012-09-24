<?php

// load plugins list
$db = JFactory::getDBO();

if(substr(JVERSION,0,3) == '1.5')
{
	$query = "SELECT id, name AS title, folder, element FROM #__plugins WHERE published=1 AND folder NOT IN ('system', 'mobile') ORDER BY folder, ordering, element";
	$db->setQuery($query);
	$plugins = $db->loadObjectList('id');
}
else
{
	$query = "SELECT extension_id AS id, name AS title, folder, element FROM #__extensions WHERE enabled=1 AND type='plugin' AND folder NOT IN ('system', 'mobile') ORDER BY folder, ordering, element";
	$db->setQuery($query);
	$plugins = $db->loadObjectList('id');
	// translate
	$lang = JFactory::getLanguage();
	foreach($plugins as &$item)
	{
		$source = JPATH_PLUGINS.'/'.$item->folder.'/'.$item->element;
		$file = 'plg_'.$item->folder.'_'.$item->element.'.sys';
		$lang->load($file, JPATH_ADMINISTRATOR, null, false, false)
			||	$lang->load($file, $source, null, false, false)
			||	$lang->load($file, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			||	$lang->load($file, $source, $lang->getDefault(), false, false);
		$item->title = JText::_($item->title);
	}
}

// load mj settings
$query = 'SELECT * FROM #__mj_plugins';
$db->setQuery($query);
$mj_plugins = $db->loadObjectList();
if(empty($mj_plugins))
	$mj_plugins = array();

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
if(count($plugins))
	foreach($plugins as $plugin)
		$table[$plugin->id] = array('desktop'=>1,'xhtml'=>1,'iphone'=>1,'tablet'=>1,'chtml'=>1,'wml'=>1);

foreach($mj_plugins as $plugin)
	$table[$plugin->id][$plugin->markup] = 0;

?>
<html>
<head>
	<link rel="stylesheet" href="components/com_mobilejoomla/css/extmanager.css" />
	<script type="text/javascript">
		mj_extmanager_action = 'set_plugin_state';
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
			<th>Plugin</th>
			<th>Type</th>
			<th>Name</th>
<?php foreach($modes as $device=>$title) : ?>
			<th><div class="vert"><?php echo $title; ?></div></th>
<?php endforeach; ?>
		</tr>
<?php $odd = true; ?>
<?php foreach($plugins as $plugin): ?>
		<tr class="<?php echo $odd ? 'odd' : 'even'; $odd = !$odd; ?>">
			<td><?php echo $plugin->title; ?></td>
			<td><?php echo $plugin->folder; ?></td>
			<td><?php echo $plugin->element; ?></td>
<?php foreach($modes as $device=>$title) : ?>
			<td><a class="link" onclick="change(<?php echo $plugin->id; ?>, '<?php echo $device; ?>', this);">
				<?php echo MJExtManager::getImage($table[$plugin->id][$device]); ?>
			</a></td>
<?php endforeach; ?>
		</tr>
<?php endforeach; ?>
	</table>
</body>
</html>