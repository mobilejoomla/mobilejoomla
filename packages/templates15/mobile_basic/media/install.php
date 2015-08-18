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
	header('Content-Type: text/css');

	$once_flag = dirname(__FILE__).'/once.flag';
	if(!is_file($once_flag)){
        return;
    }

	define( '_JEXEC', 1 );
	define('DS', DIRECTORY_SEPARATOR);
	define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))).DS.'administrator' );
	require_once( JPATH_BASE .'/includes/defines.php' );
	require_once( JPATH_BASE .'/includes/framework.php' );

	jimport('joomla.filesystem.file');

	$once_flag = dirname(__FILE__).'/once.flag';
	JFile::delete($once_flag);
	clearstatcache();
	if(is_file($once_flag)) {
        return;
    }

	$template_path = dirname(dirname(__FILE__)).'/';

	$names = array(
			'css/custom.css',
			'css/custom_preload.txt',
			'js/custom.js',
			'js/custom_preload.txt'
		);
	$data = '';
	foreach($names as $name) {
        if (!is_file($template_path . $name)) {
            JFile::write($template_path . $name, $data);
        }
    }

	$names = array(
			'.htaccess'   => array('56868306b03ba249b6bc4dc4d2912940', '57372b279ee1ccdafb16837339f9036b'),
			'favicon.ico' => array('4682009df58c0fe58ee462247355de56'),
			'logo.png'    => array('c74492c3b4d777f4bd30157d95a757b8'),
			'touch-icon-precomposed-144x144.png' => array(),
			'touch-icon-precomposed-114x114.png' => array(),
			'touch-icon-precomposed-72x72.png'   => array(),
			'touch-icon-precomposed-57x57.png'   => array(),
			'touch-icon-144x144.png' => array('5c17e73ae570a57a7a67c19011c38197'),
			'touch-icon-114x114.png' => array('de886d9bab2b8e1b54e4c743a52c1d77'),
			'touch-icon-72x72.png'   => array('bf4a63caf52b0a96fb651f29372b39b8'),
			'touch-icon-57x57.png'   => array('dd17a227d9a091e35061b69c63f1a45a'),
			'touch-startup-image-320x460.png'   => array('8e2ace1b05e138a16713bfb93ef79701'),
			'touch-startup-image-640x920.png'   => array(),
			'touch-startup-image-640x1096.png'  => array(),
			'touch-startup-image-768x1004.png'  => array(),
			'touch-startup-image-1024x748.png'  => array(),
			'touch-startup-image-1536x2008.png' => array(),
			'touch-startup-image-2048x1496.png' => array()
		);

	$do_copy = true;

	foreach($names as $name=>$hashes)
		if( is_file($template_path.$name) &&
			!in_array( md5(@file_get_contents($template_path.$name)), $hashes, true ) )
		{
			$do_copy = false;
			break;
		}

	if(!$do_copy) {
        return;
    }

	foreach($names as $name=>$hashes) {
        if(is_file($template_path.'media/'.$name))
        {
            if(is_file($template_path.$name)) {
                JFile::delete($template_path.$name);
            }
            JFile::copy($template_path.'media/'.$name, $template_path.$name);
        }
    }
