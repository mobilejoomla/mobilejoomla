<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version		###VERSION###
 * @license		###LICENSE###
 * @copyright	###COPYRIGHT###
 * @date        ###DATE###
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

function CheckMobile()
{
	if( (isset($_SERVER['HTTP_USER_AGENT']))&&
		((substr($_SERVER['HTTP_USER_AGENT'],0,10)=='portalmmm/')||
		 (substr($_SERVER['HTTP_USER_AGENT'],0,7)=='DoCoMo/')))
			return 'chtml';
	if(!isset($_SERVER['HTTP_ACCEPT']))
		$mime='';
	else
	{
		$accept = array(
			'xhtml' => 'application/xhtml+xml',
			'html'  => 'text/html',
			'wml'   => 'text/vnd.wap.wml',  
			'mhtml' => 'application/vnd.wap.xhtml+xml');
		$c=array();    
		foreach($accept as $mime_lang=>$mime_type)
		{
			$c[$mime_lang]=1;
			if(stristr($_SERVER['HTTP_ACCEPT'],$mime_type))
			{
				$c[$mime_lang]++;
				if(preg_match('|'.str_replace(array('/','.','+'),array('\/','\.','\+'),$mime_type).';q=0(\.[1-9]+)|i',$_SERVER['HTTP_ACCEPT'],$matches))
					$c[$mime_lang]-=(float)$matches[1];
			}
		}
		arsort($c,SORT_NUMERIC);
		if(array_sum($c)==count($c))
		{
			unset($c);
			$c['html']=1;    
		}
		$max=max($c);
		foreach($c as $type=>$val)
			if($val!=$max) unset($c[$type]);
		if(array_key_exists('xhtml',$c) ){unset($c);$c['xhtml']=1;}        
		elseif(array_key_exists('html',$c)){unset($c);$c['html']=1;}            
		elseif(array_key_exists('wml',$c)){unset($c);$c['wml']=1;}        
		elseif(array_key_exists('mhtml',$c)){unset($c);$c['mhtml']=1; }    
		$mime = key($c);
	}
	$GLOBALS['mobilemime']=$mime;
	if($mime=='wml') return 'wml';
	if(($mime=='mhtml')||($mime=='xhtml')) return 'xhtml';
	return '';
}