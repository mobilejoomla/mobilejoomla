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
	static $addstyles = false;
	static $forced_width = null;
	static $forced_height = null;
	static $scaledimage_width = null;
	static $scaledimage_height = null;
	static $scaletype = 0;

	function RescaleImages($text, $scaletype = 0, $addstyles = false)
	{
		ImageRescaler::$scaletype = $scaletype;
		ImageRescaler::$addstyles = $addstyles;
		return preg_replace_callback('#<img(\s[^>]*?)\s?/?>#i', array('ImageRescaler','imageParsing'), $text);
	}

	function imageParsing($matches)
	{
		$text = $matches[1];

		ImageRescaler::$forced_width  = 0;
		ImageRescaler::$forced_height = 0;

		// size
		if(preg_match('#[^\w-]width\s*:\s*(\d+)\s*(px|!|;)#i',  $text, $matches))
			ImageRescaler::$forced_width  = intval($matches[1]);
		elseif(preg_match('#\swidth\s*=\s*([\'"]?)(\d+)\1#i',  $text, $matches))
			ImageRescaler::$forced_width  = intval($matches[2]);

		if(preg_match('#[^\w-]height\s*:\s*(\d+)\s*(px|!|;)#i', $text, $matches))
			ImageRescaler::$forced_height = intval($matches[1]);
		elseif(preg_match('#\sheight\s*=\s*([\'"]?)(\d+)\1#i', $text, $matches))
			ImageRescaler::$forced_height = intval($matches[2]);

		// align
		if(preg_match('#[^\w-]float\s*:\s*(left|right)\s*(!|;)#i', $text, $matches))
			$align = $matches[1];
		elseif(preg_match('#\salign\s*=\s*([\'"]?)(left|right)\1#i', $text, $matches))
			$align = $matches[2];

		// remove parsed data
		$text = preg_replace('#\s(width|height)\s*=\s*([\'"]?)\d*%?\2#i', '', $text);
		$text = preg_replace('#\salign\s*=\s*([\'"]?)(left|right)\1#i', '', $text);
		$text = preg_replace('#\sstyle\s*=\s*([\'"]).*?\1#i', '', $text);

		// rescale
		ImageRescaler::$scaledimage_width  = ImageRescaler::$forced_width;
		ImageRescaler::$scaledimage_height = ImageRescaler::$forced_height;
		$text = preg_replace('#\ssrc\s*=\s*(["\']?)(.*?)\1(?=\s|$)#ie',
							 "' src=\"'.ImageRescaler::rescaleImage('\\2').'\"'", $text);

		if(ImageRescaler::$scaledimage_width && ImageRescaler::$scaledimage_height)
		{
			$text = ' width="' .ImageRescaler::$scaledimage_width .'"'.
					' height="'.ImageRescaler::$scaledimage_height.'"'.
					$text;
			if(ImageRescaler::$addstyles)
				$text .= ' style="width:' .ImageRescaler::$scaledimage_width .'px !important;'.
								 'height:'.ImageRescaler::$scaledimage_height.'px !important;"';
		}

		// check resulting size
		$MobileJoomla_Device =& MobileJoomla::getDevice();
		if(ImageRescaler::$scaledimage_width>$MobileJoomla_Device['screenwidth']/2)
		{
			static $included = false;
			if(!$included)
			{
				$included = true;
				$doc =& JFactory::getDocument();
				$doc->addStyleDeclaration('.mjwideimg{display:block;width:100%;text-align:center}');
			}
			$text = '<span class="mjwideimg"><img'.$text.' /></span>';
		}
		else
		{
			if(isset($align))
				$text .= ' align="'.$align.'"';
			$text = '<img'.$text.' />';
		}

		return $text;
	}

	function getmtime($file)
	{
		$time = @filemtime($file);
		if(strtolower(substr(PHP_OS, 0, 3)) !== 'win')
			return $time;
		$fileDST = (date('I', $time) == 1);
		$systemDST = (date('I') == 1);
		if($fileDST==false && $systemDST==true)
			return $time+3600;
		elseif($fileDST==true && $systemDST==false) 
			return $time-3600;
		return $time;
	}

	function rescaleImage($imageurl)
	{
		$imageurl = str_replace(array('\\"','\\\''), array('"','\''), $imageurl);

		if(defined('PATHINFO_FILENAME'))
			$src_imagename = pathinfo($imageurl, PATHINFO_FILENAME);
		else
		{
			$base = basename($imageurl);
			$src_imagename = substr($base, 0, strrpos($base, '.'));
		}
		$src_ext = strtolower(pathinfo($imageurl, PATHINFO_EXTENSION));
		if($src_ext == 'jpeg')
			$src_ext = 'jpg';
		if(!in_array($src_ext, array ('jpg', 'gif', 'png', 'wbmp')))
			return $imageurl;

		$MobileJoomla_Settings =& MobileJoomla::getConfig();
		$base_rel = JURI::base(true).'/';
		$base_abs = JURI::base();
		$imageurl_decoded = urldecode($imageurl);
		if(strpos($imageurl, '//') == false)
		{
			if($imageurl{0}!=='/')
			{
				$src_imagepath = JPATH_SITE.DS.$imageurl_decoded;
				$imageurl = $base_rel.$imageurl;
			}
			else
			{
				if($base_rel != '/')
				{
					if(strpos($imageurl, $base_rel)!==0)
						return $imageurl;
					$src_imagepath = JPATH_SITE.DS.substr($imageurl_decoded, strlen($base_rel));
				}
				else
					$src_imagepath = JPATH_SITE.$imageurl_decoded;
			}
		}
		elseif(strpos($imageurl, $base_abs)===0)
			$src_imagepath = JPATH_SITE.DS.substr($imageurl_decoded, strlen($base_abs));
		elseif($MobileJoomla_Settings['desktop_url'] && strpos($imageurl, $MobileJoomla_Settings['desktop_url'])===0)
			$src_imagepath = JPATH_SITE.DS.substr($imageurl_decoded, strlen($MobileJoomla_Settings['desktop_url']));
		else
			return $imageurl;

		$src_imagepath = implode(DS, explode('/', $src_imagepath));

		if(!file_exists($src_imagepath))
			return $imageurl;

		list($src_width, $src_height) = getimagesize($src_imagepath);
		if($src_width==0 || $src_height==0)
			return $imageurl;

		$MobileJoomla_Device =& MobileJoomla::getDevice();
		$MobileJoomla        =& MobileJoomla::getInstance();

		$markupName = $MobileJoomla->getMarkup();
		$dev_width  = $MobileJoomla_Device['screenwidth'];
		$dev_height = $MobileJoomla_Device['screenheight'];
		$formats    = $MobileJoomla_Device['imageformats'];
		if(!is_array($formats)) //desktop mode
			return $imageurl;

		if($MobileJoomla->getParam('buffer_width') != null)
			$templateBuffer = (int) $MobileJoomla->getParam('buffer_width');
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

		if(ImageRescaler::$scaletype == 1)
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
		$dest_imageuri = $base_rel.implode('/', explode(DS, substr($dest_imagepath, strlen(JPATH_SITE.DS))));
		$dest_imageuri = str_replace(array(' ',   '"',   '#',   '%',   "'",   '+'),
									 array('%20', '%22', '%23', '%25', '%27', '%2B'),
									 $dest_imageuri);

		$src_mtime = ImageRescaler::getmtime($src_imagepath);
		if(file_exists($dest_imagepath))
		{
			$dest_mtime = ImageRescaler::getmtime($dest_imagepath);
			if($src_mtime == $dest_mtime)
				return $dest_imageuri;
		}

		if(!JFolder::exists($dest_imagedir))
		{
			JFolder::create($dest_imagedir);
			$indexhtml = '<html><body bgcolor="#FFFFFF"></body></html>';
			JFile::write($dest_imagedir.DS.'index.html', $indexhtml);
		}

		if(!JFile::copy($src_imagepath, $dest_imagepath))
			return $imageurl;

		switch($src_ext)
		{
			case 'jpg':
				$src_image = @ImageCreateFromJPEG($dest_imagepath);
				break;
			case 'gif':
				$src_image = @ImageCreateFromGIF($dest_imagepath);
				break;
			case 'wbmp':
				$src_image = @ImageCreateFromWBMP($dest_imagepath);
				break;
			case 'png':
				$src_image = @ImageCreateFromPNG($dest_imagepath);
				break;
		}
		JFile::delete($dest_imagepath);

		if($src_image==false)
			return $imageurl;

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
				// Floyd-Steinberg dithering
				$black = ImageColorAllocate($dest_image, 0,0,0);
				$white = ImageColorAllocate($dest_image, 255,255,255);
				$next_err = array_fill(0, $dest_width, 0);
				for($y=0; $y<$dest_height; $y++)
				{
					$cur_err = $next_err;
					$next_err = array(-1=>0, 0=>0);
					for($x=0, $err=0; $x<$dest_width; $x++)
					{
						$rgb = ImageColorAt($dest_image, $x, $y);
						$r = ($rgb >> 16) & 0xFF;
						$g = ($rgb >> 8) & 0xFF;
						$b = $rgb & 0xFF;
						$color = $err + $cur_err[$x] + 0.299*$r + 0.587*$g + 0.114*$b;
						if($color >= 128)
						{
							ImageSetPixel($dest_image, $x, $y, $white);
							$err = $color-255;
						}
						else
						{
							ImageSetPixel($dest_image, $x, $y, $black);
							$err = $color;
						}
						$next_err[$x-1] += $err*3/16;
						$next_err[$x]   += $err*5/16;
						$next_err[$x+1]  = $err/16;
						$err *= 7/16;
					}
				}
				ImageWBMP($dest_image);
				break;
			case 'png':
				ImagePNG($dest_image);
				break;
		}
		$data = ob_get_contents();
		ob_end_clean();
		ImageDestroy($dest_image);
		JFile::write($dest_imagepath, $data);
		@touch($dest_imagepath, $src_mtime);

		return $dest_imageuri;
	}
}
