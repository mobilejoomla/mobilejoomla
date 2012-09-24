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

class MJExtManager
{
	static function changeState($table, $id, $markup)
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT COUNT(*) FROM $table WHERE id=$id AND markup=".$db->Quote($markup);
		$db->setQuery($query);
		$unpublished = $db->loadResult();
		
		if($unpublished)
		{
			$query = "DELETE FROM $table WHERE id=$id AND markup=".$db->Quote($markup);
			$db->setQuery($query);
			$db->query();
			return true;
		}
		else
		{
			$query = "INSERT INTO $table (id, markup) VALUES ($id, ".$db->Quote($markup).")";
			$db->setQuery($query);
			$db->query();
			return false;
		}
	}
	
	static function getImage($published)
	{
		return $published
			? '<img src="components/com_mobilejoomla/images/publ-16.png" width="16" height="16" />'
			: '<img src="components/com_mobilejoomla/images/unpubl-16.png" width="16" height="16" />';
	}
}
