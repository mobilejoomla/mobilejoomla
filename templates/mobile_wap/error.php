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
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

defined('_MJ') or die('Incorrect using of Kuneri Mobile Joomla.');

$MobileJoomla =& MobileJoomla::getInstance();

echo '<p><b>'.$this->error->code.' - '.$this->error->message.'</b></p>';

if($this->debug)
	echo '<p>'.$this->renderBacktrace().'</p>';

$MobileJoomla->showFooter();