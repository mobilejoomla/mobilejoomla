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

class MjMarkupGenerator
{
    /** @var MobileJoomla */
    protected $mj;

    /**
     * @param $mj MobileJoomla
     */
    public function __construct($mj)
    {
        $this->mj = $mj;
    }

    public function setHeader()
    {
    }

    public function showFooter()
    {
    }

    public function processPage($text)
    {
        return $text;
    }
}