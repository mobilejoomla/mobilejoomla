<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version        ###VERSION###
 * @license        ###LICENSE###
 * @copyright    ###COPYRIGHT###
 * @date        ###DATE###
 */
defined('_JEXEC') or die('Restricted access');

abstract class JHtmlEmail
{
    public static function cloak($mail, $mailto = 1, $text = '', $email = 1)
    {
        if (strpos($mail, "'") !== false) {
            return $mail;
        }

        if (empty($text)) {
            $text = $mail;
        }

        if ($email) {
            $text = str_replace(array('@', '.'),
                array('&#8203;<bdo dir=\'ltr\'>&#64;<bdo>&#8203;', '&#46;'),
                $text);
        }

        if ($mailto) {
            $html = '<a href=\'javascript:void(location.href=&quot;mai&quot;+&quot;lto:'
                . str_replace(array('@', '.'),
                    array('&quot;+&quot;&#92;100&quot;+&quot;', '.&quot;+&quot;'),
                    $mail)
                . '&quot;)\'>' . $text . '</a>';
        } else {
            $html = $text;
        }

        return $html;
    }
}
