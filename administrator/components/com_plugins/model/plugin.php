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
 * Methods supporting editing of a plugin record.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 * @since       3.2
 */
class PluginsModelPlugin extends JModelCmsitem
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
	 * @var  string  The event to trigger after saving the data.
	 * @since   3.2
	 */
	protected $event_after_save = 'onExtensionAfterSave';

	/**
	 * @var     string  The event to trigger after before the data.
	 * @since   3.2
	 */
	protected $event_before_save = 'onExtensionBeforeSave';

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
		// The folder and element variabless are passed when saving the form.
		if (empty($data))
		{
			$item		= $this->getItem();
			$folder		= $item->folder;
			$element	= $item->element;
		}
		else
		{
			$folder		= JArrayHelper::getValue($data, 'folder', '', 'cmd');
			$element	= JArrayHelper::getValue($data, 'element', '', 'cmd');
		}

		// These variables are used to add data from the plugin XML files.
		$this->state->set('item.folder', $folder);
		$this->state->set('item.element', $element);

		// Get the form.
		$form = $this->loadForm('com_plugins.plugin', 'plugin', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('enabled', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('enabled', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_plugins.edit.plugin.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData('com_plugins.plugin', $data);

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed   Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->state->get('plugin.id');

		if (!isset($this->cache[$pk]))
		{
			$false	= false;

			// Get a row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return $false;
			}

			// Convert to the JObject before adding other data.
			$properties = $table->getProperties(1);
			$this->cache[$pk] = JArrayHelper::toObject($properties, 'JObject');

			// Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadString($table->params);
			$this->cache[$pk]->params = $registry->toArray();

			// Get the plugin XML.
			$path = JPath::clean(JPATH_PLUGINS.'/'.$table->folder.'/'.$table->element.'/'.$table->element.'.xml');

			if (file_exists($path))
			{
				$this->cache[$pk]->xml = simplexml_load_file($path);
			} else {
				$this->cache[$pk]->xml = null;
			}
		}

		return $this->cache[$pk];
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable   A database object
	*/
	public function getTable($type = 'Extension', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 * @since   1.6
	 */
	protected function populateState()
	{
		// Execute the parent method.
		parent::populateState();

		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$pk = $app->input->getInt('extension_id');
		$this->state->set('plugin.id', $pk);
	}

	/**
	 * @param   object  $form  A form object.
	 * @param   mixed   $data  The data expected for the form.
	 * @param   group   $roup  The plugin group
	 *
	 * @return  mixed   True if successful.
	 *
	 * @throws  Exception if there is an error in the form event.
	 * @since   3.2
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		jimport('joomla.filesystem.path');

		$folder		= $this->state->get('item.folder');
		$element	= $this->state->get('item.element');
		$lang		= JFactory::getLanguage();

		// Load the core and/or local language sys file(s) for the ordering field.
		$db = JFactory::getDbo();
		$query = 'SELECT element' .
				' FROM #__extensions' .
				' WHERE (type =' .$db->quote('plugin'). 'AND folder='. $db->quote($folder) . ')';
		$db->setQuery($query);
		$elements = $db->loadColumn();

		foreach ($elements as $elementa)
		{
				$lang->load('plg_'.$folder.'_'.$elementa.'.sys', JPATH_ADMINISTRATOR, null, false, false)
			||	$lang->load('plg_'.$folder.'_'.$elementa.'.sys', JPATH_PLUGINS.'/'.$folder.'/'.$elementa, null, false, false)
			||	$lang->load('plg_'.$folder.'_'.$elementa.'.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			||	$lang->load('plg_'.$folder.'_'.$elementa.'.sys', JPATH_PLUGINS.'/'.$folder.'/'.$elementa, $lang->getDefault(), false, false);
		}

		if (empty($folder) || empty($element))
		{
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_('index.php?option=com_plugins&view=plugins', false));
		}

		$formFile = JPath::clean(JPATH_PLUGINS . '/' . $folder . '/' . $element . '/' . $element . '.xml');

		if (!file_exists($formFile))
		{
			throw new Exception(JText::sprintf('COM_PLUGINS_ERROR_FILE_NOT_FOUND', $element . '.xml'));
		}

		// Load the core and/or local language file(s).
			$lang->load('plg_'.$folder.'_'.$element, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load('plg_'.$folder.'_'.$element, JPATH_PLUGINS.'/'.$folder.'/'.$element, null, false, false)
		||	$lang->load('plg_'.$folder.'_'.$element, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load('plg_'.$folder.'_'.$element, JPATH_PLUGINS.'/'.$folder.'/'.$element, $lang->getDefault(), false, false);

		if (file_exists($formFile))
		{
			// Get the plugin form.
			if (!$form->loadFile($formFile, false, '//plugin'))
			{
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}
		}

		// Attempt to load the xml file.
		if (!$xml = simplexml_load_file($formFile))
		{
			throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
		}

		// Get the help data from the XML file if present.
		$help = $xml->xpath('/extension/help');

		if (!empty($help))
		{
			$helpKey = trim((string) $help[0]['key']);
			$helpURL = trim((string) $help[0]['url']);

			$this->helpKey = $helpKey ? $helpKey : $this->helpKey;
			$this->helpURL = $helpURL ? $helpURL : $this->helpURL;
		}

		// Trigger the default form events.
		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   JTable  $table  A table object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   3.2
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'type = ' . $this->_db->quote($table->type);
		$condition[] = 'folder = ' . $this->_db->quote($table->folder);

		return $condition;
	}

	/**
	 * Override method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.2
	 */
	public function save($data)
	{
		// Load the extension plugin group.
		JPluginHelper::importPlugin('extension');

		// Setup type
		$data['type'] = 'plugin';

		return parent::save($data);
	}

	/**
	 * Get the necessary data to load an item help screen.
	 *
	 * @return  object  An object with key, url, and local properties for loading the item help screen.
	 *
	 * @since   3.2
	 */
	public function getHelp()
	{
		return (object) array('key' => $this->helpKey, 'url' => $this->helpURL);
	}

	/**
	 * Custom clean cache method, plugins are cached in 2 places for different clients
	 *
	 * @since   3.2
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_plugins');
	}
}
