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
		if($mailto)
			$html = '<a href=" mailto:'.$mail.'">'.($text ? $text : $mail).'</a>';
		else
			$html = $mail;

		return $html;
	}
}
