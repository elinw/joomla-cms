<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Methods supporting editing of a tag record.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_tags
 * @since       3.5
 */
class TagsModelTag extends JModelCmsitem
{
	/**
	 * @var     string  The help screen key for the edit screen.
	 * @since   3.2
	 */
	protected $helpKey = 'JHELP_EXTENSIONS_PLUGIN_MANAGER_EDIT';

	/**
	 * @var   string  The help screen base URL for the module.
	 * @since  3.2
	 */
	protected $helpURL;

	/**
	 * @var   object  The cache for this item
	 * @since  3.2
	 */
	protected $cache;

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 * @since   3.2
	 */
	public function getForm($data = array(), $loadData = true)
	{

		// Get the form.
		$form = $this->loadForm('com_tags.tag', 'tag', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   3.1
	 */
	public function getTable($type = 'Tag', $prefix = 'TagsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}


	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   1.6
	 */
	public function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_tags.edit.tag.data', array());

		if (empty($data))
		{
			$data = $this->getItem($this->id);
		}

		$this->preprocessData('com_tags.tag', $data);

		return $data;
	}

	/**
	 * Method to get a tag.
	 *
	 * @param   integer  $pk  An optional id of the object to get, otherwise the id from the model state is used.
	 *
	 * @return  mixed  Tag data object on success, false on failure.
	 *
	 * @since   3.1
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->state->get('tag.id');
		if ($result = parent::getItem($pk))
		{

			// Prime required properties.
			if (empty($result->id))
			{
				$result->parent_id = $this->getState('tag.parent_id');
			}

			// Convert the created and modified dates to local user time for display in the form.
			$tz = new DateTimeZone(JFactory::getApplication()->getCfg('offset'));

			if ((int) $result->created_time)
			{
				$date = new JDate($result->created_time);
				$date->setTimezone($tz);
				$result->created_time = $date->toSql(true);
			}
			else
			{
				$result->created_time = null;
			}

			if ((int) $result->modified_time)
			{
				$date = new JDate($result->modified_time);
				$date->setTimezone($tz);
				$result->modified_time = $date->toSql(true);
			}
			else
			{
				$result->modified_time = null;
			}
		}

		return $result;
	}


	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('administrator');

		$parentId = $app->input->getInt('parent_id');
		$this->state->set('tag.parent_id', $parentId);

		// Load the User state.
		$id = $app->input->getInt('id');
		$this->state->set($this->getName() . '.id', $id);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_tags');
		$this->state->set('params', $params);
	}


	/**
	 * Method rebuild the entire nested set tree.
	 *
	 * @return  boolean  False on failure or error, true otherwise.
	 *
	 * @since   3.1
	 */
	public function rebuild()
	{
		// Get an instance of the table object.
		$table = $this->getTable();

		if (!$table->rebuild())
		{
			$this->setError($table->getError());
			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 */
	public function save(&$data)
	{
		$dispatcher = JEventDispatcher::getInstance();
		$table = $this->getTable();
		$input = JFactory::getApplication()->input;
		$pk = (isset($data['id'])) ? $data['id'] : (int) $this->state->get('tag.id');
		$isNew = true;

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		// Load the row if saving an existing tag.
		if ($pk > 0)
		{
			$table->load($pk);
			$isNew = false;
		}

		if (empty($data['parent_id']))
		{
			$data['parent_id'] = 1;
		}

		// Set the new parent id if parent id not matched OR while New/Save as Copy .
		if ($table->parent_id != $data['parent_id'] || $data['id'] == 0)
		{
			$table->setLocation($data['parent_id'], 'last-child');
		}

		if (isset($data['images']) && is_array($data['images']))
		{
			$registry = new JRegistry;
			$registry->loadArray($data['images']);
			$data['images'] = (string) $registry;
		}

		if (isset($data['urls']) && is_array($data['urls']))
		{
			$registry = new JRegistry;
			$registry->loadArray($data['urls']);
			$data['urls'] = (string) $registry;
		}

		// Alter the title for save as copy
		if (stristr($input->get('task'),'save2copy') == true || stristr($input->get('controller'),'save2copy') == true)
		{
			list($title, $alias) = $this->generateNewTitle($data['parent_id'], $data['alias'], $data['title']);
			$data['title'] = $title;
			$data['alias'] = $alias;
		}

		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());
			return false;
		}

		// Bind the rules.
		if (isset($data['rules']))
		{
			$rules = new JAccessRules($data['rules']);
			$table->setRules($rules);
		}

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// Trigger the onContentBeforeSave event.
		$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, &$table, $isNew));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());

			return false;
		}

		// Trigger the onContentAfterSave event.
		$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));

		// Rebuild the path for the tag:
		if (!$table->rebuildPath($table->id))
		{
			$this->setError($table->getError());

			return false;
		}

		// Rebuild the paths of the tag's children:
		if (!$table->rebuild($table->id, $table->lft, $table->level, $table->path))
		{
			$this->setError($table->getError());

			return false;
		}

		$this->table = $table;

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to save the reordered nested set tree.
	 * First we save the new order values in the lft values of the changed ids.
	 * Then we invoke the table rebuild to implement the new ordering.
	 *
	 * @param   array    $idArray    An array of primary key ids.
	 * @param   integer  $lft_array  The lft value
	 *
	 * @return  boolean  False on failure or error, True otherwise
	 *
	 * @since   3.5
	 */
	public function saveorder($idArray = null, $lft_array = null)
	{
		// Get an instance of the table object.
		$table = $this->getTable();

		if (!$table->saveorder($idArray, $lft_array))
		{
			$this->setError($table->getError());

			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to change the title & alias.
	 *
	 * @param   integer  $category_id  The id of the category or any parent_id.
	 * @param   string   $alias        The alias.
	 * @param   string   $title        The title.
	 *
	 * @return	array  Contains the modified title and alias.
	 *
	 * @since	12.2
	 */
	protected function generateNewTitle($category_id, $alias, $title)
	{
		// Alter the title & alias
		$table = $this->getTable();
		while ($table->load(array('alias' => $alias, 'parent_id' => $category_id)))
		{
			$title = JString::increment($title);
			$alias = JString::increment($alias, 'dash');
		}

		return array($title, $alias);
	}
}