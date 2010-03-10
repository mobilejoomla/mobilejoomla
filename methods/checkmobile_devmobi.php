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
	if(stristr($_SERVER['HTTP_USER_AGENT'],'W3C_Validator'))
		return 'xhtml';
	if(!isset($_SERVER['HTTP_ACCEPT']))
		return '';
	$iMode = array('doco','j-pho','up.b','ddip','port');
	if(in_array(strtolower(substr(trim($_SERVER['HTTP_USER_AGENT']),0,4)), $iMode))
		return 'chtml';
	$browserlist = array(
		 'alav','alca','armv','au-m','aur','avan','blac','blaz','elai','epoc'
		,'eric','eudo','fetc','java','jigs','lge-','maxo','mc21','midp','mits'
		,'mot-','moto','msn','my s','netf','noki','palm','pana','pda','ppc'
		,'prox','qco7','qwap','r380','regk','reqw','sage','sams','sec-','shar'
		,'sie-','siem','smar','sony','symb','tsm-','tung','up.b','upg1','upsi'
		,'wap','wapa','wapi','webc','webp','wind','winw'
	);
	if(!in_array(strtolower(substr(trim($_SERVER['HTTP_USER_AGENT']),0,4)),$browserlist))
		return '';
	$accept = array(
		'xhtml' => 'application/xhtml+xml',
		'html'  => 'text/html',
		'wml'   => 'text/vnd.wap.wml',
		'mhtml' => 'application/vnd.wap.xhtml+xml',
	);
	$c = array();
	foreach($accept as $mime_lang=>$mime_type)
	{
		$c[$mime_lang] = 1;
		if(stristr($_SERVER['HTTP_ACCEPT'], $mime_type))
		{
			$c[$mime_lang]+=1;
			$esc_type = '/'. str_replace( array('/','.','+'), array('\/','\.','\+'), $mime_type) .';q=0(\.[1-9]+)/i';
			if(preg_match($esc_type, $_SERVER['HTTP_ACCEPT'],$matches))
				$c[$mime_lang]-=(float)$matches[1];
		}
	}
	arsort($c,SORT_NUMERIC);
	if(array_sum($c)==count($c)) { unset( $c ); $c['html'] = 1; }
	$max=max($c);
	foreach($c as $type=>$val)
	{
		if($val!=$max){ unset( $c[$type] ); }
	}
	if(array_key_exists('xhtml',$c)){ unset( $c ); $c['xhtml'] = 1; }
	if(array_key_exists('html',$c)) { unset( $c ); $c['html'] = 1; }
	if(array_key_exists('wml',$c))  { unset( $c ); $c['wml'] = 1; }
	if(array_key_exists('mhtml',$c)){ unset( $c ); $c['mhtml'] = 1; }
	$mime = key($c);
	if(($mime=='xhtml')||($mime=='mhtml')) return 'xhtml';
	if($mime=='wml') return 'wml';
	return '';
}