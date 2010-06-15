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

defined('_MJ') or die('Incorrect usage of Mobile Joomla.');

$MobileJoomla =& MobileJoomla::getInstance();

echo '<p><b>'.$this->error->code.' - '.$this->error->message.'</b></p>';

if($this->debug)
	echo '<p>'.$this->renderBacktrace().'</p>';

$MobileJoomla->showFooter();
