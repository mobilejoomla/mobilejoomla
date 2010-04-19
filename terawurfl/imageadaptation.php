<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

/*
The function library is developed by Muntasir Mamun Joarder and is FREE to use.
In case you are using the library please update me to svo97_12@yahoo.com so that
I can keep you in the loop to let you update about the next versions.
Thanks to them who will find bug and report to svo97_12@yahoo.com.
*/

/*
The library has been altered by Denis Ryabov for Kuneri Mobile Joomla!
*/

if(!defined('DS')) define('DS',DIRECTORY_SEPARATOR);
include(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php');


function RescaleImages($text,$scaletype)
{
	function img_replacer($text,$scaletype)
	{
//		if(get_magic_quotes_gpc())
			$text = stripslashes($text);
		$text = preg_replace( '# (width|height)\s*=\s*[\'"]?\d*[%]?[\'"]?#i','', $text );
		unset($GLOBALS['scaledimage_width']);
		unset($GLOBALS['scaledimage_height']);
		$text = preg_replace( '# src\s*=\s*"([^"]+)"#ie',   "' src=\"'.convertImage('\\1',$scaletype).'\"'", $text );
		if(!isset($GLOBALS['scaledimage_width']))
			$text = preg_replace( '# src\s*=\s*\'([^\']+)\'#ie',"' src=\''.convertImage('\\1',$scaletype).'\''", $text );
		if(!isset($GLOBALS['scaledimage_width']))
			$text = preg_replace( '# src\s*=\s*([^"\' ]+)#ie',  "' src=\"'.convertImage('\\1',$scaletype).'\"'", $text );
		if( isset($GLOBALS['scaledimage_width'] )&&
			isset($GLOBALS['scaledimage_height']) )
			$text = ' width="'. $GLOBALS['scaledimage_width']. '"'
				  . ' height="'.$GLOBALS['scaledimage_height'].'"'
				  . ' style="width:'. $GLOBALS['scaledimage_width'] .' !important;height:'. $GLOBALS['scaledimage_height'] .' !important;"'
				  . $text;
		return $text;
	}
	return preg_replace( '#<img([^>]*?)>#ie', "'<img'.img_replacer('\\1',$scaletype).'>'", $text );
}

function convertImage($imagepath,$scaletype=0)
{
	if(!in_array(strtolower(substr($imagepath,-4)),array('.jpg','.gif','.png')))
		return $imagepath;
	if(strpos($imagepath,'://')==FALSE)
	{
		if($imagepath{0}=='/')
			$abs_imagepath=JPATH_SITE.$imagepath;
		else
			$abs_imagepath=JPATH_SITE.'/'.$imagepath;
	}
	elseif(strpos($imagepath,JURI::base())==0)
		$abs_imagepath = JPATH_SITE . DS . str_replace (JURI::base(), '', $imagepath);
	else
		return $imagepath;
	if(!file_exists($abs_imagepath)) return $imagepath;
	$uri=convertImageUA($abs_imagepath,$_SERVER['HTTP_USER_AGENT'],$scaletype);
	if($uri=='') return $imagepath;
	return substr_replace($uri,JURI::base(),0,strlen(JPATH_SITE)+1);
}

function convertImageUA($imagepath,$ua,$scaletype=0)
{
	include(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php');
	$scalewidth=$MobileJoomla_Settings['templatewidth'];

	//We need a buffer value, so that if template has padding, images get more smaller
	//and no horizontal scroll is shown
	$MobileJoomla =& MobileJoomla::getInstance();
	$markupName = $MobileJoomla->getMarkup();

	if (isset($MobileJoomla_Settings[$markupName . '_buffer_width']))
	{
		$templateBuffer = (int) $MobileJoomla_Settings[$markupName . '_buffer_width'];
	}
	else
	{
		$templateBuffer = 0;
	}

	list($format,$devwidth,$devheight)=getCAP($ua);
	//so we dont go below 1
	if ($devwidth > $templateBuffer)
	{
		$devwidth -= $templateBuffer;
	}
	if ($devwidth==0)
		return '';
// **********************************
	list($imwidth,$imheight)=getimagesize($imagepath);

	if($scaletype==1)
		$defscale=$devwidth/$scalewidth;
	else
		$defscale=1;
	$maxscalex=$devwidth/$imwidth;
	$maxscaley=$devheight/$imheight;
	$scale=min($defscale,$maxscalex,$maxscaley);
	if($scale>=1)
	{
		$GLOBALS['scaledimage_width']=$imwidth;
		$GLOBALS['scaledimage_height']=$imheight;
		return $imagepath;
	}
	$Rwidth =$GLOBALS['scaledimage_width'] =intval($imwidth*$scale);
	$Rheight=$GLOBALS['scaledimage_height']=intval($imheight*$scale);

	$convD=$Rwidth."X".$Rheight;
	$InputPathArr=explode("/",$imagepath);
	$InputPathArrSize=count($InputPathArr);
	$InputImageFileName=$InputPathArr[$InputPathArrSize-1];
	$farray=explode(".",$InputImageFileName);
	$InputImageName=strtolower($farray[0]);
	$outputImageName=$InputImageName."_".$Rwidth."x".$Rheight;
	$outputFileFullpath="";
	$OutputImageDir="";
	for($i=0;$i<($InputPathArrSize-1);$i++)
		$OutputImageDir.=$InputPathArr[$i]."/";
	$outputFileFullpath=$OutputImageDir."Resized/".$outputImageName;
	$outputImageFull=$outputFileFullpath.".".$format;
	if(file_exists($outputImageFull))
		return $outputImageFull;
	$uri=resizeImage($imagepath,$format,$outputImageName,$Rwidth,$Rheight);
	if($uri===0)
		$uri=$imagepath;
	return $uri;
}

function getCAP($ua)
{
    global $wurflObj;

    if (!isset ($wurflObj))
    {
        require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'terawurfl'.DS.'TeraWurfl.php');

        $wurflObj = new TeraWurfl();
		if(!is_object($wurflObj))
			return array('',0,0);
        $wurflObj->getDeviceCapabilitiesFromAgent($_SERVER['HTTP_USER_AGENT']);
    }

    $maxWidth = $wurflObj->getDeviceCapability('max_image_width');
    $maxHeight = $wurflObj->getDeviceCapability('max_image_height');

	$bestFormat='';
	if ($wurflObj->getDeviceCapability('png'))
		$bestFormat='png';
	else if ($wurflObj->getDeviceCapability('jpg'))
		$bestFormat='jpg';
	else if ($wurflObj->getDeviceCapability('gif'))
		$bestFormat='gif';
	else
		$bestFormat='wbmp';

	$capinfo = array ($bestFormat, $maxWidth, $maxHeight);

	return $capinfo;
}

function resizeImage($InputImage,$OutputFormat,$outputFileName,$Out_X,$Out_Y)
{
/*
How it works: In the folder where the main image resides, this function will Create a new folder named 'Resized' into it and copy the resized image into this folder. So at the end the main image will be unchanged.

Return : In the return the function will provide the final URL of the resized image in specific format.

Capability: This function can work with four types of images: JPG, GIF, PNG and WBMP

Parameters:

$InputImage= Full path of Input Image which is to be resized. Example: 'testImage/test.jpg'
$OutputFormat= What is the output format.Example: 'gif'
$outputFileName= What will be the name of the output file. Must remember that there will be no file extension with this name.Example: file0000. So then in the final version of the resized file the name will be : file0000.gif
$Out_X= Length of the X asis of the resized image.
$Out_Y= Length of the Y asis of the resized image.

Author: Muntasir Mamun (svo97_12@yahoo.com). If anyone find any bug please inform me by mail.
*/
//echo $Out_X."X".$Out_Y;
	include(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mobilejoomla'.DS.'config.php');
	jimport ('joomla.filesystem.file');
	jimport ('joomla.filesystem.folder');

	$InputPathArr=explode("/",$InputImage);
	$InputPathArrSize=count($InputPathArr);
	$InputImageFileName=$InputPathArr[$InputPathArrSize-1];
	$InputImageFileExtension=strtolower(substr("$InputImageFileName",-3));
	$OutputImageDir="";
	for ($i=0; $i < ($InputPathArrSize-1); $i++ )
	{
		$OutputImageDir=$OutputImageDir.$InputPathArr[$i]."/";
	}
	if (!JFolder::exists ($OutputImageDir."Resized/"))
		JFolder::create ($OutputImageDir."Resized/");
		
	$OutputImageFullPath=$OutputImageDir."Resized/".$outputFileName.".".$OutputFormat;
	
	if (!JFile::copy($InputImage,$OutputImageFullPath))
		return 0;
		
	switch ($InputImageFileExtension)
	{
	case "jpg":
		$SRC_IMAGE = ImageCreateFromJPEG($OutputImageFullPath);
		break;
	case "gif":
		$SRC_IMAGE = ImageCreateFromgif($OutputImageFullPath);
		break;
	case "wbmp":
		$SRC_IMAGE = ImageCreateFromwbmp($OutputImageFullPath);
		break;
	case "png":
		$SRC_IMAGE = ImageCreateFrompng($OutputImageFullPath);
		break;
	}
	$SRC_X = ImageSX($SRC_IMAGE);
	$SRC_Y = ImageSY($SRC_IMAGE);
	$DEST_IMAGE = imagecreatetruecolor($Out_X, $Out_Y);

        //Additional operations to preserve transparency on PNG images
        if(strtolower($OutputFormat) == 'png'){
            imagealphablending($DEST_IMAGE, false);
            $color =  imagecolortransparent($DEST_IMAGE, imagecolorallocatealpha($DEST_IMAGE, 0, 0, 0, 127));
            imagefill($DEST_IMAGE, 0, 0, $color);
            imagesavealpha($DEST_IMAGE, true);
        }

	unlink($OutputImageFullPath);
	$OUTPUT_FILE=$OutputImageDir."Resized/".$outputFileName.".".$OutputFormat;
	if(function_exists('imagecopyresampled'))
		$ret=imagecopyresampled($DEST_IMAGE,$SRC_IMAGE,0,0,0,0,$Out_X,$Out_Y,$SRC_X,$SRC_Y);
	else
		$ret=imagecopyresized($DEST_IMAGE,$SRC_IMAGE,0,0,0,0,$Out_X,$Out_Y,$SRC_X,$SRC_Y);
	if(!$ret)
	{
		imagedestroy($SRC_IMAGE);
		imagedestroy($DEST_IMAGE);
		return 0;
	}
	imagedestroy($SRC_IMAGE);
	switch(strtolower($OutputFormat))
	{
	case "jpg":
		$I = ImageJPEG($DEST_IMAGE,$OUTPUT_FILE,$MobileJoomla_Settings['jpegquality']);
		break;
	case "gif":
		$I = Imagegif($DEST_IMAGE,$OUTPUT_FILE);
		break;
	case "wbmp":
		$I = Imagewbmp($DEST_IMAGE,$OUTPUT_FILE);
		break;
	case "png":
		$I = Imagepng($DEST_IMAGE,$OUTPUT_FILE);
		break;
	}
	imagedestroy($DEST_IMAGE);
	return $OUTPUT_FILE;
}
?>