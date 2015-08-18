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

class MJToolbar
{
    private $title;
    private $buttons = array();
    private $hideBackButton = false;
    private $hideHomeButton = false;

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $pos
     * @param string $url
     * @param string $icon
     * @param string $title
     * @param array $options
     */
    public function appendButton($pos, $url, $icon = '', $title = '', $options = array())
    {
        if (!isset($this->buttons[$pos])) {
            $this->buttons[$pos] = array();
        }
        $this->buttons[$pos][] = $this->makeButton($url, $icon, $title, $options);
    }

    /**
     * @param string $pos
     * @param string $url
     * @param string $icon
     * @param string $title
     * @param array $options
     */
    public function prependButton($pos, $url, $icon = '', $title = '', $options = array())
    {
        if (!isset($this->buttons[$pos])) {
            $this->buttons[$pos] = array();
        }
        array_unshift($this->buttons[$pos], $this->makeButton($url, $icon, $title, $options));
    }

    private function makeButton($url, $icon, $title, $options)
    {
        $button = new stdClass;
        $button->url = $url;
        $button->icon = $icon;
        $button->title = $title;
        $button->options = $options;
        return $button;
    }

    public function getButtons($pos)
    {
        return isset($this->buttons[$pos]) ? $this->buttons[$pos] : null;
    }

    public function hideBackButton($status = true)
    {
        $this->hideBackButton = $status;
    }

    public function isBackButtonHidden()
    {
        return $this->hideBackButton;
    }

    public function hideHomeButton($status = true)
    {
        $this->hideHomeButton = $status;
    }

    public function isHomeButtonHidden()
    {
        return $this->hideHomeButton;
    }
}
