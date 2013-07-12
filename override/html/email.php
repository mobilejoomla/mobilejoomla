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

abstract class JHtmlEmail
{
	public static function cloak($mail, $mailto = 1, $text = '', $email = 1)
	{
		if(strpos($mail, "'") !== false)
			return $mail;

		if($mailto)
			$html = '<a href="javascript:void(location.href=\'mail\'+\'to:'.str_replace('@', "'+'@'+'", $mail).'\')">'.($text ? $text : str_replace('@', '(at)', $mail)).'</a>';
		else
			$html = str_replace('@', '(at)', $mail);

		return $html;
	}
}
