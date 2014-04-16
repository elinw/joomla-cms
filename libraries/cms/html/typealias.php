<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class working with content types select lists
 *
 * @package     Joomla.Libraries
 * @subpackage  HTML
 * @since       3.3
 */
abstract class JHtmlTypeAlias
{
	/**
	 * Cached array of the content typee items.
	 *
	 * @var    array
	 * @since  3.3
	 */
	protected static $items = null;

	/**
	 * Get a list of the available content types.
	 *
	 * @return  string
	 *
	 * @see     JFormFieldContentType
	 * @since   3.3
	 */
	public static function existing($all = false, $translate = false)
	{
		if (empty(static::$items))
		{
			// Get the database object and a new query object.
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			// Build the query.
			$query->select('a.type_alias AS value, a.type_title AS text')
				->from('#__content_types AS a')
				->order('a.type_title');

			// Set the query and load the options.
			$db->setQuery($query);
			static::$items = $db->loadObjectList();
		}

		return static::$items;
	}
}
