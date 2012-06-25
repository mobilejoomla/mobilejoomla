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

$app = JFactory::getApplication();
$app->setUserState( "com_mobilejoomla.scientiainstall", false );

?><html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link href="style.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo $mootools; ?>" type="text/javascript"></script>
	<script src="main.js" type="text/javascript"></script>
</head>
<body>

<h1>Upgrade your mobile device database</h1>
<p>Mobile Joomla! uses an offline device detection mechanism to display your mobile website to your visitors. Your current device repository may be outdated, therefore we <strong>highly recommend</strong> you to upgrade your repository with Scientia Mobile DB-API, which includes up-to-date device information.</p>
<p>DB-API is provided by courtesy of <a href="http://scientiamobile.com/" target="_blank">Scientia Mobile</a> under <a href="http://www.gnu.org/licenses/agpl-3.0.html" target="_blank">AGPL license</a>. For more information, please visit <a href="http://scientiamobile.com/" target="_blank">Scientia Mobile website</a>.</p>
<p><label><input type="checkbox" id="confirmbox" onchange="return onConfirmChange();" /> I read and accept <a href="http://www.gnu.org/licenses/agpl-3.0.html" target="_blank">AGPL license</a></label></p>
<div>
	<div class="floatleft"><a href="index.php?action=install" class="button disabled" id="nextbutton" onclick="return $('confirmbox').checked;">Next</a></div>
	<div id="confirmtext">Please read and accept Scientia Mobile DB-API licensing terms and conditions to proceed</div>
</div>
<div class="clear"></div>

</body>
</html>