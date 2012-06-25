<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version		###VERSION###
 * @license		###LICENSE###
 * @copyright	###COPYRIGHT###
 * @date		###DATE###
 */
defined('_JEXEC') or die('Restricted access');
global $mootools;
?><html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link href="style.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo $mootools; ?>" type="text/javascript"></script>
	<script src="main.js" type="text/javascript"></script>
</head>
<body>

<h1>Installing Scientia Mobile DB-API plugin</h1>
<p>Please do not close your window, your installation might be corrupted.</p>
<ul id="mjstages">
	<li id="mjdownload"><?php echo JText::_('COM_MJ__UPDATE_DOWNLOAD'); ?></li>
	<li id="mjunpack"><?php echo JText::_('COM_MJ__UPDATE_UNPACK'); ?></li>
	<li id="mjinstall"><?php echo JText::_('COM_MJ__UPDATE_INSTALL_PLUGIN'); ?></li>
</ul>
<div class="clear"></div>

<script type="text/javascript">
function mjOnError()
{
	location.href = 'index.php?action=error';
}
function mjAjaxDownload()
{
	$("mjdownload").addClass("highlight");
	$("mjdownload").addClass("ajaxload");
	ajaxGet("index.php?action=ajaxdownload",
		function(data){
			$("mjdownload").removeClass("ajaxload");
			if(data!="ok") {
				$("mjdownload").addClass("error");
				mjOnError();
			} else {
				$("mjdownload").addClass("pass");
				mjAjaxUnpack();
			}
		},
		function(){
			$("mjdownload").removeClass("ajaxload");
			$("mjdownload").addClass("error");
			mjOnError();
		}
	);
}
function mjAjaxUnpack()
{
	$("mjunpack").addClass("highlight");
	$("mjunpack").addClass("ajaxload");
	ajaxGet("index.php?action=ajaxunpack",
		function(data){
			$("mjunpack").removeClass("ajaxload");
			if(data!="ok") {
				$("mjunpack").addClass("error");
				mjOnError();
			} else {
				$("mjunpack").addClass("pass");
				mjAjaxInstall();
			}
		},
		function(){
			$("mjunpack").removeClass("ajaxload");
			$("mjunpack").addClass("error");
			mjOnError();
		}
	);
}
function mjAjaxInstall()
{
	$("mjinstall").addClass("highlight");
	$("mjinstall").addClass("ajaxload");
	ajaxGet("index.php?action=ajaxinstall",
		function(data){
			$("mjinstall").removeClass("ajaxload");
			if(data!="ok") {
				$("mjinstall").addClass("error");
				mjOnError();
			} else {
				$("mjinstall").addClass("pass");
//				window.parent.location.href = '../../../index.php?option=com_mobilejoomla';
				window.parent.SqueezeBox.close();
			}
		},
		function(){
			$("mjinstall").removeClass("ajaxload");
			$("mjinstall").addClass("error");
			mjOnError();
		}
	);
}
window.addEvent('domready', function(){
	try{
		mjAjaxDownload();
	}catch(e){}
});
</script>

</body>
</html>