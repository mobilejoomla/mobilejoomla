<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version    ###VERSION###
 * @license    ###LICENSE###
 * @copyright  ###COPYRIGHT###
 * @date       ###DATE###
 */
defined('_JEXEC') or die('Restricted access');

//defined('_MJ') or die('Incorrect usage of Mobile Joomla!');


jQMHelper::process($this->params);

class jQMHelper
{
    public static $params;

    public static function process($params)
    {
        jQMHelper::$params = $params;

        jQMHelper::parseComponentBuffer();

        jQMHelper::parseMessageBuffer();

        JFactory::getApplication()->registerEvent('onAfterRender', 'jQMHelper_onAfterRender');
    }

    private static function parseComponentBuffer()
    {
        /** @var JDocumentHtml $document */
        $document = JFactory::getDocument();
        $content = $document->getBuffer('component');

        // pagination plugin
        if (class_exists('plgContentPagenavigation'))
            $content = preg_replace_callback('#<ul class="pagenav">(.*?)</ul>#s', array('jQMHelper', 'pagenav_replacer'), $content);

        // pagebreak plugin
        if (class_exists('plgContentPagebreak')) {
            $content = preg_replace('#(<div id="article-index">\s*(<h3>.*?</h3>)?)\s*<ul>#s', '\1<ul data-role="listview" data-inset="true">', $content);
            $content = preg_replace_callback('#<div class="pagination">\s*<ul>\s*<li>(.*?)</li>\s*<li>(.*?)</li>\s*</ul>\s*</div>#s', array('jQMHelper', 'pagebreak_replacer'), $content);
        }

        $document->setBuffer($content, 'component');
    }

    private static function parseMessageBuffer()
    {
        /** @var $document JDocumentHTML */
        $document = JFactory::getDocument();
        $message = $document->getBuffer('message');

        $theme_title = $document->params->get('theme_messagetitle');
        $theme_content = $document->params->get('theme_messagetext');
        if ($theme_title)
            $theme_title = ' data-divider-theme="' . $theme_title . '"';
        if ($theme_content)
            $theme_content = ' data-theme="' . $theme_content . '"';

        $message = str_replace(array('<ul>', '</ul>', '</dd>'), '', $message);
        $message = preg_replace('#<dd [^>]*>#', '', $message);
        $message = str_replace('<dt ', '<li data-role="list-divider" ', $message);
        $message = str_replace('</dt>', '</li>', $message);
        $message = str_replace('<dl ', '<ul data-role="listview" data-inset="true"' . $theme_content . $theme_title . ' ', $message);
        $message = str_replace('</dl>', '</ul>', $message);

        $document->setBuffer($message, 'message');
    }

    public static function pagenav_replacer($matches)
    {
        $theme = JFactory::getDocument()->params->get('theme_pagination');
        if ($theme)
            $theme = ' data-theme="' . $theme . '"';
        $inner = $matches[1];
        $inner = preg_replace('#<li class="pagenav-prev">\s*<a href="(.*?)" rel="(?:prev|next)">(.*?)</a>\s*</li>#s', '<a data-role="button" data-inline="true" href="\1" data-direction="reverse">\2</a>', $inner);
        $inner = preg_replace('#<li class="pagenav-next">\s*<a href="(.*?)" rel="(?:prev|next)">(.*?)</a>\s*</li>#s', '<a data-role="button" data-inline="true" href="\1">\2</a>', $inner);
        return '<div data-role="controlgroup" data-type="horizontal"' . $theme . ' class="pagenav">' . $inner . '</div>';
    }

    public static function pagebreak_replacer($matches)
    {
        $theme = JFactory::getDocument()->params->get('theme_pagination');
        if ($theme)
            $theme = ' data-theme="' . $theme . '"';
        $prev = $matches[1];
        $next = $matches[2];
        if (strpos($prev, '<a ') === 0)
            $prev = '<a data-role="button" data-inline="true"' . substr($prev, 2);
        else
            $prev = '<span data-role="button" data-inline="true" class="ui-btn ui-disabled">' . $prev . '</span>';
        if (strpos($next, '<a ') === 0)
            $next = '<a data-role="button" data-inline="true"' . substr($next, 2);
        else
            $next = '<span data-role="button" data-inline="true" class="ui-btn ui-disabled">' . $next . '</span>';
        return '<div data-role="controlgroup" data-type="horizontal"' . $theme . ' class="pagination pagenav">' . $prev . $next . '</div>';
    }
}

function jQMHelper_onAfterRender()
{
    $buffer = JResponse::getBody();

    if ($buffer)
        JResponse::setBody($buffer);

    return true;
}
