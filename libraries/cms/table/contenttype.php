<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Tags table
 *
 * @package     Joomla.Libraries
 * @subpackage  Table
 * @since       3.1
 */
class JTableContenttype extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  A database connector object
	 *
	 * @since   3.1
	 */
	public function __construct($db)
	{
		parent::__construct('#__content_types', 'type_id', $db);
	}

	/**
	 * Overloaded check method to ensure data integrity.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 * @throws  UnexpectedValueException
	 */
	public function check()
	{
		// Check for valid name.
		if (trim($this->type_title) == '')
		{
			throw new UnexpectedValueException(sprintf('The title is empty'));
		}

		$this->type_title = ucfirst($this->type_title);

		if (empty($this->type_alias))
		{
			throw new UnexpectedValueException(sprintf('The type_alias is empty'));
		}

		return true;
	}

	/**
	 * Overridden JTable::store.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 */
	public function store($updateNulls = false)
	{
		// Verify that the alias is unique
		$table = JTable::getInstance('Contenttype', 'JTable');

		if ($table->load(array('type_alias' => $this->type_alias)) && ($table->type_id != $this->type_id || $this->type_id == 0))
		{
			$this->setError(JText::_('COM_TAGS_ERROR_UNIQUE_ALIAS'));

			return false;
		}

		return parent::store($updateNulls);
	}

	/**
	 * Method to expand the field mapping
	 *
	 * @param   boolean  $assoc  True to return an associative array.
	 *
	 * @return  mixed  Array or object with field mappings. Defaults to object.
	 *
	 * @since   3.1
	 */
	public function fieldmapExpand($assoc = true)
	{
		return $this->fieldmap = json_decode($this->fieldmappings, $assoc);
	}

	/**
	 * Method to get the id given the type alias
	 *
	 * @param   string  $typeAlias  Content type alias (for example, 'com_content.article').
	 *
	 * @return  mixed     type_id for this alias if successful, otherwise null.
	 *
	 * @since   3.2
	 */
	public function getTypeId($typeAlias)
	{
		$db = $this->_db;
		$query = $db->getQuery(true);
		$query->select($db->quoteName('type_id'))
			->from($db->quoteName('#__content_types'))
			->where($db->quoteName('type_alias') . ' = ' . $db->quote($typeAlias));
		$db->setQuery($query);

		return $db->loadResult($query);
	}

	/**
	 * Method to get the JTable object for the content type from the table object.
	 *
	 * @return  mixed     JTable object on success, otherwise false.
	 *
	 * @since   3.2
	 */
	public function getContentTable()
	{
		$result = false;
		$tableInfo = json_decode($this->table);

		if (is_object($tableInfo) && isset($tableInfo->special))
		{
			if (is_object($tableInfo->special) && isset($tableInfo->special->type) && isset($tableInfo->special->prefix))
			{
				$result = JTable::getInstance($tableInfo->special->type, $tableInfo->special->prefix);
			}
		}

		return $result;
	}

	/**
	 * Magic method to get the name of the field mapped to a ucm field (core_something).
	 *
	 * @param   string  $ucmField  The name of the field in JTableCorecontent
	 *
	 * @return  string  The name mapped to the $ucmField for a given content type
	 *
	 * @since   3.2
	 */
	public function __get($ucmField, $typeAlias)
	{
		$this->fieldmapExpand(false);

		return isset($this->$ucmField) ? isset($this->$ucmField) : null;
	}

}
