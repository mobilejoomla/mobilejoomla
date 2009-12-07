<?php
/**
 * Kuneri Mobile Joomla! for Joomla!1.5
 * http://www.mobilejoomla.com/
 *
 * @version		0.9.0
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	Copyright (C) 2008-2009 Kuneri Ltd. All rights reserved.
 */
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );

header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: ".date("r"));

define( 'JPATH_BASE', implode(DS,array_slice(explode(DS,dirname(__FILE__)),0,-3)) );
require_once( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once( JPATH_BASE .DS.'includes'.DS.'helper.php' );
require_once( JPATH_BASE .DS.'includes'.DS.'toolbar.php' );
$mainframe =& JFactory::getApplication('administrator');
$user =& JFactory::getUser();
if ($user->get('guest')) die();

$lang =& JFactory::getLanguage();
$mosConfig_lang = $lang->getBackwardLang();
$languagepath=JPATH_BASE.DS.'components'.DS.'com_mobilejoomla'.DS.'languages'.DS;
if(is_file($languagepath.$mosConfig_lang.'.php'))
	include($languagepath.$mosConfig_lang.'.php');
elseif(is_file($languagepath.'english.php'))
	include($languagepath.'english.php');
else
	$error_msg="<b>Error:</b> language file '${languagepath}english.php' is not found.";

set_time_limit(600);

$wurflurl='http://wurfl.sourceforge.net/wurfl.zip';
$wurflpatchurl='http://wurfl.sourceforge.net/web_browsers_patch.xml';
$wurflpath=JPATH_SITE .DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'wurfl'.DS.'data'.DS;

function DownloadFile($url,$to,$id)
{
	echo '<b>'.WURFL_DOWNLOAD__DOWNLOADING.' '.$url.'</b><br>';flush();
	$error=false;
	$f1=@fopen($url,'rb');
	$f2=@fopen($to,'wb');
	if(($f1!==FALSE)&&($f2!==FALSE))
	{
		$inf=stream_get_meta_data($f1);
		$remotesize=1024*1024;
		foreach($inf['wrapper_data'] as $v) 
			if(stristr($v,'content-length')) 
			{ 
				$v=explode(":",$v);
				$remotesize=(int)trim($v[1]);
			}
		$size=0;
		$oldpercent=-1;
?><div class="bar"><div id="<?php echo $id; ?>"></div></div><script>var pb=document.getElementById("<?php echo $id; ?>");</script><?php
		flush();
		while(!feof($f1))
		{
			$data=fread($f1,8192);
			if($data==FALSE)
			{
				echo '<b>'.WURFL_DOWNLOAD__ERROR.'</b> '.WURFL_DOWNLOAD__CANNOT_DOWNLOAD_FILE.'<br>';flush();
				$error=true;
				break;
			}
			fwrite($f2,$data);
			$size+=strlen($data);
			$sizekb=(int)round($size/1024);
			$percent=intval($size*100/$remotesize);
			if($percent!=$oldpercent)
			{
				$oldpercent=$percent;
?><script>
pb.innerHTML="<?php echo $sizekb; ?>Kb";
pb.style.width="<?php echo $percent; ?>%";
</script><?php
				flush();
			}
		}
		fclose($f1);
		fclose($f2);
		if(($remotesize!=1024*1024)&&($remotesize!=$size))
			$error=true;
		if(!$error)
		{
?><script>
pb.innerHTML="<?php echo $sizekb; ?>Kb";
pb.style.width="100%";
</script><?php
			flush();
		}
	}
	else
	{
		$error=true;
		if($f1==FALSE) echo '<b>'.WURFL_DOWNLOAD__ERROR.'</b> '.WURFL_DOWNLOAD__CANNOT_OPEN_REMOTE_FILE.'<br>';
		if($f2==FALSE) echo '<b>'.WURFL_DOWNLOAD__ERROR.'</b> '.WURFL_DOWNLOAD__CANNOT_CREATE_LOCAL_FILE.'<br>';
		flush();
	}
	return $error;
}

function ExtractZip($archivename,$extractdir)
{
	require_once( JPATH_SITE .DS.'administrator'.DS.'includes'.DS.'pcl'.DS.'pclzip.lib.php' );
	require_once( JPATH_SITE .DS.'administrator'.DS.'includes'.DS.'pcl'.DS.'pclerror.lib.php' );
	$zipfile = new PclZip( $archivename );
	define('OS_WINDOWS',(substr(PHP_OS,0,3)=='WIN')?1:0);
	$ret = $zipfile->extract( PCLZIP_OPT_PATH, $extractdir );
	if($ret == 0)
	{
		echo '<b>'.WURFL_DOWNLOAD__ERROR.'</b> '.$zipfile->errorName(true);
		return false;
	}
	return true;
}

?>
<html>
<head>
<style>
div.bar {
	background-color:#e0f0ff;
	border:1px solid #06d;
	width:75%;
}
div.bar div {
	height:1.2em;
	line-height:1.2em;
	background-color:#03a;
	border-right:1px solid #03a;
	text-align:right;
	color:#fff;
	width:0;
	overflow:hidden;
	padding-right:5px;
}
</style>
</head>
<body>
<?php
if(isset($error_msg)) echo $error_msg.'<br><br><br>';

echo str_repeat(' ',256),"\n";flush();

$error=false;

$error_wurfl=DownloadFile($wurflurl,$wurflpath.'wurfl.zip','wurfl');
if($error_wurfl)
	$error=true;
else
{
	@rename($wurflpath.'wurfl.xml',$wurflpath.'wurfl.tmp');
	if(ExtractZip($wurflpath.'wurfl.zip',$wurflpath))
	{
		unlink($wurflpath.'wurfl.zip');
		@unlink($wurflpath.'wurfl.tmp');
		touch($wurflpath.'wurfl.xml');
		echo WURFL_DOWNLOAD__OK;flush();
	}
	else
	{
		@rename($wurflpath.'wurfl.tmp',$wurflpath.'wurfl.xml');
		echo '<b>'.WURFL_DOWNLOAD__ERROR.'</b> '.WURFL_DOWNLOAD__WURFLZIP_IS_CORRUPTED.'<br>';flush();
		$error=true;
	}
}

echo '<br><br><br>';

$error_patch=DownloadFile($wurflpatchurl,$wurflpath.'web_browsers_patch.tmp','patch');
if($error_patch)
	$error=true;
else
{
	if(file_exists($wurflpath.'web_browsers_patch.xml'))
		unlink($wurflpath.'web_browsers_patch.xml');
	rename($wurflpath.'web_browsers_patch.tmp',$wurflpath.'web_browsers_patch.xml');
	echo WURFL_DOWNLOAD__OK;flush();
}

if(!$error && (strcmp(PHP_VERSION,'PHP 5.2.0')<0 || error_get_last()===NULL))
	echo '<script>opener.location=opener.location;window.close();</script>';
?>