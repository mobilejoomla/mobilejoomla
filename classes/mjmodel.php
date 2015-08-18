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

abstract class MjModel
{
    /** @var MjJoomlaWrapper */
    protected $joomlaWrapper;

    /**
     * @param $joomlaWrapper MjJoomlaWrapper
     */
    public function __construct($joomlaWrapper)
    {
        $this->joomlaWrapper = $joomlaWrapper;
    }

    /**
     * @param $data array
     * @return boolean
     */
    abstract public function bind($data);

    /**
     * @return boolean
     */
    abstract public function save();
}