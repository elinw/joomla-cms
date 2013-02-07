<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Tags Component Tag Model
 *
 * @package     Joomla.Site
 * @subpackage  com_tags
 * @since       3.1
 */
class TagsModelTag extends JModelList
{

	/**
	 * The tags that apply.
	 *
	 * @access  protected
	 * @var     object
	 */
	protected $tag = null;

	/**
	 * The list of items associated with the tags.
	 *
	 * @access  protected
	 * @var     array
	 */
	protected $items = null;

	/**
	 * Method to get a list of items for a list of tags.
	 *
	 * @return  mixed  An array of objects on success, false on failure.
	 *
	 * @since   3.1
	 */
	public function getItems()
	{
		// Invoke the parent getItems method to get the main list
		$items = parent::getItems();

		if ($items)
		{
			$contentTypes = new JTagsHelper;
			$types = $contentTypes->getTypes('objectList');

			$typesr= $this->getState('tag.typesr');
			foreach ($types as $type)
			{
				if (!empty($typesr) && !in_array($type->type_id, $typesr))
				{
					continue;
				}
				$tableLookup[$type->alias] = $type->table;

				// We need to create the SELECT clause that correctly maps differently named fields to the common names.
				$fieldsArray = json_decode($type->field_mappings);
				$type->fieldlist = '';

				foreach ($fieldsArray as $common => $specific)
				{
					$newstring = $specific . ' AS ' . $common . ',';
					$type->fieldlist .= $newstring;
				}
				$fieldQuery[$type->table] = $type->fieldlist;

				$tableArray[$type->table][0] = $type->fieldlist;
			}

			// Get the data from the content item source table.
			foreach ($items as $item)
			{
				$tagsHelper = new JTagsHelper;

				$explodedItemName = $tagsHelper->explodeTagItemName($item->item_name);
				if (array_key_exists($explodedItemName[1], $tableLookup))
				{
					$item->content_id = $explodedItemName[2];
					$item->option = $explodedItemName[0];
					$linkPrefix = 'index.php?option=' . $explodedItemName[0] . '&view=' . $explodedItemName[1] . '&id=' ;

					// Keep track of the items we want from each table.
					$item->table = $tableLookup[$explodedItemName[1]];

					// For each view we build up an array of the base link, table, fields for the table, ids from rows in that table which have the tag.
					// These are used to creat the query for that view.
					if (empty($linkArray[$linkPrefix]))
					{
						$linkArray[$linkPrefix][0] = $item->table;
						$linkArray[$linkPrefix][1] = $tableArray[$item->table][0];
						$linkArray[$linkPrefix][2] = $item->content_id . ',';
					}
					else
					{
						$linkArray[$linkPrefix][2] .= $item->content_id . ',';
					}
				}
			}

			if (empty($linkArray))
			{
				return false;
			}

			// Initialize some variables.
			$db = JFactory::getDbo();

			$queryt = $db->getQuery(true);

			// Let's get the select query we need for each view and add it to the array.
			foreach ($linkArray as $link => $values)
			{
				$idlist = (string) rtrim($values[2], ',');
				$fieldlist = (string) rtrim($values[1], ',');

				$queryt->clear();
				if (!empty($idlist) && !empty($fieldlist))
				{
					$queryt->select($fieldlist . ',' . $db->quote($link) . ' AS ' . $db->quoteName('urlprefix'));
					$queryt->from($db->quoteName($values[0]) . ' WHERE ' . $db->quoteName('id') . ' IN ( ' . ltrim($idlist, ',') . ' )');

					$queryString = $queryt->__toString();
					$tablequeries[] = $queryString;
				}
			}

			// Now we want to put the queries for each table together using Union.
			$queryu = $db->getQuery(true);

			foreach ($tablequeries as $i => $uquery)
			{
				if ($i > 0)
				{
					$queryu->union($uquery);
				}
			}

			if ($queryu->union)
			{
				$unionString  = $queryu->union->__toString();
				$queryStringu = $tablequeries[0] . $unionString . 'ORDER BY' . $db->qn($this->state->params->get('orderby', 'title')) . ' ' . $this->state->params->get('orderby_direction', 'ASC') . ' LIMIT 0,' . $this->state->params->get('maximum', 5);
			}
			else
			{
				$queryStringu = $tablequeries[0] . ' ORDER BY' . $db->qn($this->state->params->get('orderby', 'title')) . ' ' . $this->state->params->get('orderby_direction', 'ASC') . ' LIMIT 0,' . $this->state->params->get('maximum', 5);
			}

			// Initialize some variables.
			$dbf = JFactory::getDbo();

			$queryf =  $dbf->getQuery(true);

			// Until we have UNION ALL in the platform.
			$queryStringu = str_replace('UNION ', 'UNION ALL ', $queryStringu);
			$dbf->setQuery($queryStringu);
			$itemsData = $dbf->loadObjectList();

			return $itemsData;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to build an SQL query to load the list data of all items with a given tag.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   3.1
	 */
	protected function getListQuery()
	{
		$user	= JFactory::getUser();

		// Need to decide on a model for access .. maybe just do an access IN groups condition
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$tagId = $this->getState('tag.id');
		$tagCount	= substr_count($tagId, ',') + 1;
		$tagTreeArray = '';

		if ($this->state->params->get('include_children') == 1)
		{
			$tagIdArray = explode(',', $tagId);
			foreach ($tagIdArray as $tag)
			{
				$tagTreeArray = implode(',', $this->getTagTreeArray($tag));
			}
		}
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select required fields from the tags.
		$query->select(array('*', ' COUNT(*) AS tagcount'));
		$query->group($db->quoteName('a.item_name'));
		$query->from($db->quoteName('#__contentitem_tag_map') . ' AS a ');

		// Modify the query based on whether or not items with child tags should be included.
		if ($this->state->params->get('include_children') == 1)
		{
			$query->where($db->quoteName('a.tag_id') . ' IN (' . $tagTreeArray . ')');
		}
		else
		{
			$query->where($db->quoteName('a.tag_id') . ' IN (' .  $tagId . ')' );
		}

		// For AND search make sure the number matches, but if there is just one tag do not bother.
		if ($this->state->params->get('return_any_or_all') == 0 && $tagCount > 1)
		{
			$query->having('tagcount = ' . $tagCount);
		}

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   3.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app    = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getObject('id');
		$pk = (array) $pk;
		$pkString = '';
		foreach ($pk as $id)
		{
			$pkString .= (int) $id . ',';
		}
		$pkString = rtrim($pkString, ',');

		$this->setState('tag.id', $pkString);

		$typesr = $app->input->getObject('types');
		if ($typesr)
		{
			$typesr = (array) $typesr;
			$this->setState('tag.typesr', $typesr);
		}

		$offset = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$user = JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_tags')) &&  (!$user->authorise('core.edit', 'com_tags')))
		{
			$this->setState('filter.published', 1);
		}
	}

	/**
	 * Method to get tag data for the current tag or tags
	 *
	 * @param   integer  An optional ID
	 *
	 * @return  object
	 * @since   3.1
	 */
	public function getItem($pk = null)
	{
		if (!isset($this->item) ||$this->item === null)
		{
			$this->item = false;

			if (empty($id))
			{
				$id = $this->getState('tag.id');
			}

			// Get a level row instance.
			$table = JTable::getInstance('Tag', 'TagsTable');

			$idsArray = explode(',', $id);
			// Attempt to load the rows into an array.
			foreach ($idsArray as $id)
			{
				try
				{
					$table->load($id);

					// Check published state.
					if ($published = $this->getState('filter.published'))
					{
						if ($table->published != $published) {
							return $this->item;
						}
					}

					// Convert the JTable to a clean JObject.
					$properties = $table->getProperties(1);
					$this->item[] = JArrayHelper::toObject($properties, 'JObject');
				}
				catch (RuntimeException $e)
				{
					$this->setError($e->getMessage());
					return false;
				}
			}
		}

		return $this->item;
	}

	/**
	 * Method to get an array of tag ids for the current tag and its children
	 *
	 * @param   integer  $id  An optional ID
	 *
	 * @return  object
	 * @since   3.1
	 */
	public function getTagTreeArray($id = null)
	{
			if (empty($id))
			{
				$id = $this->getState('tag.id');
			}

			// Get a level row instance.
			$table = JTable::getInstance('Tag', 'TagsTable');

			$tagTree = $table->getTree($id);
			// Attempt to load the tree
			if ($tagTree)
			{
				foreach ($tagTree as $tag)
				{
					// Check published state.
					if ($published = $this->getState('filter.published'))
					{
						if ($tag->published == $published)
						{
							$this->tagTreeArray[] = $tag->id;
						}
					}
				}
			}
			elseif ($error = $table->getError())
			{
				$this->setError($error);
			}

		return $this->tagTreeArray;
	}
}
