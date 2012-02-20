<?php
class MJToolbar extends JObject
{
	var $_title = null;
	var $_buttons = array();
	var $_hideBackButton = false;
	var $_hideHomeButton = false;

	function setTitle($title)
	{
		$this->_title = $title;
	}

	function getTitle()
	{
		return $this->_title;
	}

	function appendButton($pos, $url, $icon = '', $title = '', $options = array())
	{
		if(!isset($this->_buttons[$pos]))
			$this->_buttons[$pos] = array();
		$this->_buttons[$pos][] = $this->_makeButton($url, $icon, $title, $options);
	}

	function prependButton($pos, $url, $icon = '', $title = '', $options = array())
	{
		if(!isset($this->_buttons[$pos]))
			$this->_buttons[$pos] = array();
		array_unshift($this->_buttons[$pos], $this->_makeButton($url, $icon, $title, $options));
	}

	function _makeButton($url, $icon, $title, $options)
	{
		$button = new stdClass;
		$button->url = $url;
		$button->icon = $icon;
		$button->title = $title;
		$button->options = $options;
		return $button;
	}

	function getButtons($pos)
	{
		return isset($this->_buttons[$pos]) ? $this->_buttons[$pos] : null;
	}

	function hideBackButton($status = true)
	{
		$this->_hideBackButton = $status;
	}

	function isBackButtonHidden()
	{
		return $this->_hideBackButton;
	}

	function hideHomeButton($status = true)
	{
		$this->_hideHomeButton = $status;
	}

	function isHomeButtonHidden()
	{
		return $this->_hideHomeButton;
	}
}
