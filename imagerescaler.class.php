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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class ImageRescaler
{
	static $thumbdir = 'Resized';
	static $forced_width = null;
	static $forced_height = null;
	static $scaledimage_width = null;
	static $scaledimage_height = null;

	function RescaleImages($text, $scaletype)
	{
		return preg_replace('#<img(\s[^>]*)>#ie', "'<img'.ImageRescaler::imageParsing('\\1',$scaletype).'>'", $text);
	}

	function imageParsing($text, $scaletype)
	{
		$text = stripslashes($text);

		ImageRescaler::$forced_width = 0;
		ImageRescaler::$forced_height = 0;
		// img attribules
		if(preg_match('#\swidth\s*=\s*([\'"]?)(\d+)\1#i', $text, $matches))
			ImageRescaler::$forced_width = intval($matches[2]);
		if(preg_match('#\sheight\s*=\s*([\'"]?)(\d+)\1#i', $text, $matches))
			ImageRescaler::$forced_height = intval($matches[2]);
		// styles
		if(preg_match('#\swidth\s*:\s*(\d+)\s*(px|!|;)#i', $text, $matches))
			ImageRescaler::$forced_width = intval($matches[1]);
		if(preg_match('#\sheight\s*:\s*(\d+)\s*(px|!|;)#i', $text, $matches))
			ImageRescaler::$forced_height = intval($matches[1]);

		$text = preg_replace('#\s(width|height)\s*=\s*([\'"]?)\d*%?\2#i', '', $text);
		$text = preg_replace('#\s(width|height)\s*:\s*\d+[^;]*;#i', '', $text);
		$text = preg_replace('#\sstyle\s*=\s*([\'"]).*?\1#i', '', $text);

		ImageRescaler::$scaledimage_width = null;
		ImageRescaler::$scaledimage_height = null;
		$text = preg_replace('#\ssrc\s*=\s*(["\']?)(.*?)\1(?=\s|$)#ie',
							 "' src=\"'.ImageRescaler::rescaleImage('\\2',$scaletype).'\"'", $text);
		if(ImageRescaler::$scaledimage_width && ImageRescaler::$scaledimage_height)
			$text = ' width="'.ImageRescaler::$scaledimage_width.'"'.
					' height="'.ImageRescaler::$scaledimage_height.'"'.
					' style="width:'.ImageRescaler::$scaledimage_width.'px !important;'.
							'height:'.ImageRescaler::$scaledimage_height.'px !important;"'.
					$text;

		return $text;
	}

	function rescaleImage($imageurl, $scaletype = 0)
	{
		$src_imagename = pathinfo($imageurl, PATHINFO_FILENAME);
		$src_ext = strtolower(pathinfo($imageurl, PATHINFO_EXTENSION));
		if($src_ext == 'jpeg')
			$src_ext = 'jpg';
		if(!in_array($src_ext, array ('jpg', 'gif', 'png', 'wbmp')))
			return $imageurl;

		$base_rel = JURI::base(true).'/';
		$base_abs = JURI::base();
		if(strpos($imageurl, '://') == false)
		{
			if($imageurl{0}!=='/')
			{
				$src_imagepath = JPATH_SITE.DS.$imageurl;
				$imageurl = $base_rel.$imageurl;
			}
			else
			{
				if($base_rel != '/')
				{
					if(strpos($imageurl, $base_rel)!==0)
						return $imageurl;
					$src_imagepath = JPATH_SITE.DS.substr($imageurl, strlen($base_rel));
				}
				else
					$src_imagepath = JPATH_SITE.$imageurl;
			}
		}
		elseif(strpos($imageurl, $base_abs)===0)
			$src_imagepath = JPATH_SITE.DS.substr($imageurl, strlen($base_abs));
		else
			return $imageurl;

		$src_imagepath = implode(DS, explode('/', $src_imagepath));

		if(!file_exists($src_imagepath))
			return $imageurl;

		list($src_width, $src_height) = getimagesize($src_imagepath);
		if($src_width==0 || $src_height==0)
			return $imageurl;

		$MobileJoomla_Settings =& MobileJoomla::getConfig();
		$MobileJoomla_Device   =& MobileJoomla::getDevice();
		$MobileJoomla          =& MobileJoomla::getInstance();

		$markupName = $MobileJoomla->getMarkup();
		$dev_width  = $MobileJoomla_Device['screenwidth'];
		$dev_height = $MobileJoomla_Device['screenheight'];
		$formats    = $MobileJoomla_Device['imageformats'];

		if(isset($MobileJoomla_Settings[$markupName.'_buffer_width']))
			$templateBuffer = (int) $MobileJoomla_Settings[$markupName.'_buffer_width'];
		else
			$templateBuffer = 0;

		$dev_width -= $templateBuffer;
		if($dev_width < 16)
			$dev_width = 16;

		$forced_width  = ImageRescaler::$forced_width;
		$forced_height = ImageRescaler::$forced_height;
		if($forced_width==0)
		{
			if($forced_height==0)
			{
				$forced_width  = $src_width;
				$forced_height = $src_height;
			}
			else
			{
				$forced_width = round($src_width*$forced_height/$src_height);
				if($forced_width==0)
					$forced_width = 1;
			}
		}
		elseif($forced_height==0)
		{
			$forced_height = round($src_height*$forced_width/$src_width);
			if($forced_height==0)
				$forced_height = 1;
		}

		if($scaletype == 1)
		{
			$scalewidth = $MobileJoomla_Settings['templatewidth'];
			$defscale = $dev_width/$scalewidth;
		}
		else
			$defscale = 1;

		$maxscalex = $dev_width/$forced_width;
		$maxscaley = $dev_height/$forced_height;
		$scale = min($defscale, $maxscalex, $maxscaley);
		if($scale >= 1 && in_array($src_ext, $formats) &&
			$forced_width==$src_width && $forced_height==$src_height)
		{
			ImageRescaler::$scaledimage_width  = $src_width;
			ImageRescaler::$scaledimage_height = $src_height;
			return $imageurl;
		}
		$dest_width  = ImageRescaler::$scaledimage_width  = round($forced_width *$scale);
		$dest_height = ImageRescaler::$scaledimage_height = round($forced_height*$scale);
		if($dest_width ==0) $dest_width  = 1;
		if($dest_height==0) $dest_height = 1;

		if(in_array($src_ext, $formats))
			$dest_ext = $src_ext;
		else
			$dest_ext = $formats[0];

		$dest_imagedir = dirname($src_imagepath).DS.ImageRescaler::$thumbdir;
		$dest_imagepath = $dest_imagedir.DS.$src_imagename.'_'.$dest_width.'x'.$dest_height.'.'.$dest_ext;
		$dest_imageuri = $base_rel.implode('/', explode(DS, substr($dest_imagepath, strlen(JPATH_SITE))));
		if(file_exists($dest_imagepath))
			return $dest_imageuri;

		if(!JFolder::exists($dest_imagedir))
		{
			JFolder::create($dest_imagedir);
			JFile::write($dest_imagedir.DS.'index.html', '<html><body bgcolor="#FFFFFF"></body></html>');
		}

		if(!JFile::copy($src_imagepath, $dest_imagepath))
			return $imageurl;

		switch($src_ext)
		{
			case 'jpg':
				$src_image = ImageCreateFromJPEG($dest_imagepath);
				break;
			case 'gif':
				$src_image = ImageCreateFromGIF($dest_imagepath);
				break;
			case 'wbmp':
				$src_image = ImageCreateFromWBMP($dest_imagepath);
				break;
			case 'png':
				$src_image = ImageCreateFromPNG($dest_imagepath);
				break;
		}
		JFile::delete($dest_imagepath);

		$dest_image = ImageCreateTrueColor($dest_width, $dest_height);

		//Additional operations to preserve transparency on images
		switch($dest_ext)
		{
		case 'png':
		case 'gif':
			ImageAlphaBlending($dest_image, false);
			$color = ImageColorTransparent($dest_image, ImageColorAllocateAlpha($dest_image, 0, 0, 0, 127));
			ImageFilledRectangle($dest_image, 0, 0, $dest_width, $dest_height, $color);
			ImageSaveAlpha($dest_image, true);
			break;
		default:
			$color = ImageColorAllocate($dest_image, 255, 255, 255);
			ImageFilledRectangle($dest_image, 0, 0, $dest_width, $dest_height, $color);
			break;
		}

		if(function_exists('imagecopyresampled'))
			$ret = ImageCopyResampled($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
		else
			$ret = ImageCopyResized($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
		if(!$ret)
		{
			ImageDestroy($src_image);
			ImageDestroy($dest_image);
			return $imageurl;
		}
		ImageDestroy($src_image);

		ob_start();
		switch($dest_ext)
		{
			case 'jpg':
				ImageJPEG($dest_image, null, $MobileJoomla_Settings['jpegquality']);
				break;
			case 'gif':
				ImageTrueColorToPalette($dest_image, true, 256);
				ImageGIF($dest_image);
				break;
			case 'wbmp':
				ImageTrueColorToPalette($dest_image, true, 2);
				$c0 = ImageColorsForIndex($dest_image, 0);
				$c1 = ImageColorsForIndex($dest_image, 1);
				$i0 = 0.3*$c0->red + 0.59*$c0->green + 0.11*$c0->blue;
				$i1 = 0.3*$c1->red + 0.59*$c1->green + 0.11*$c1->blue;
				$foreground = $i0>$i1 ? 0 : 1;
				ImageWBMP($dest_image, null, $foreground);
				break;
			case 'png':
				ImagePNG($dest_image);
				break;
		}
		$data = ob_get_contents();
		ob_end_clean();
		ImageDestroy($dest_image);
		JFile::write($dest_imagepath, $data);

		return $dest_imageuri;
	}
}
