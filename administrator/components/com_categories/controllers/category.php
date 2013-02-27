<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * The Category Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 * @since       1.6
 */
class CategoriesControllerCategory extends JControllerForm
{
	/**
	 * The extension for which the categories apply.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $extension;

	/**
	 * Constructor.
	 *
	 * @param  array  $config  An optional associative array of configuration settings.
	 *
	 * @since  1.6
	 * @see    JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->extension))
		{
			$this->extension = $this->input->get('extension', 'com_content');
		}
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();
		return ($user->authorise('core.create', $this->extension) || count($user->getAuthorisedCategories($this->extension, 'core.create')));
	}

	/**
	 * Method to check if you can edit a record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowEdit($data = array(), $key = 'parent_id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user = JFactory::getUser();
		$userId = $user->get('id');

		// Check general edit permission first.
		if ($user->authorise('core.edit', $this->extension))
		{
			return true;
		}

		// Check specific edit permission.
		if ($user->authorise('core.edit', $this->extension . '.category.' . $recordId))
		{
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', $this->extension . '.category.' . $recordId) || $user->authorise('core.edit.own', $this->extension))
		{
			// Now test the owner is the user.
			$ownerId = (int) isset($data['created_user_id']) ? $data['created_user_id'] : 0;
			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem($recordId);

				if (empty($record))
				{
					return false;
				}

				$ownerId = $record->created_user_id;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.6
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Category');

		// Preset the redirect
		$this->setRedirect('index.php?option=com_categories&view=categories&extension=' . $this->extension);

		return parent::batch($model);
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&extension=' . $this->extension;

		return $append;
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&extension=' . $this->extension;

		return $append;
	}
	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   JModelLegacy  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 *
	 * @return  void
	 * @since   3.1
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$task = $this->getTask();

		$item = $model->getItem();
		if (isset($item->params) && is_array($item->params))
		{
			$registry = new JRegistry;
			$registry->loadArray($item->params);
			$item->params = (string) $registry;
		}
		if (isset($item->metadata) && is_array($item->metadata))
		{
			$registry = new JRegistry;
			$registry->loadArray($item->metadata);
			$item->metadata = (string) $registry;
		}
		$id =  $item->id;

		$fieldMap = Array(
			'core_title' => $item->title,
			'core_alias' => $item->alias,
			'core_body' => $item->description,
			'core_state' => $item->published,
			'core_checked_out_user_id' => $item->checked_out,
			'core_checked_out_time' => $item->checked_out_time,
			'core_access' => $item->access,
			'core_params' => $item->params,
			'core_featured' => 0,
			'core_metadata' => $item->metadata,
			'core_created_user_id' => $item->created_user_id,
			'core_created_by_alias' => $item->created_by_alias,
			'core_created_time' => $item->created ,
			'core_modified_user_id' => $item->modified_user_id,
			'core_modified_time' => $item->modified ,
			'core_language' => $item->language,
			'core_publish_up' => $item->publish_up,
			'core_publish_down' => $item->publish_down,
			'core_content_item_id' => $item->id,
			'core_type_alias' => $item->extension . '.category',
			'asset_id' => $item->asset_id,
			'core_images' => '',
			'core_urls' => '',
			'core_hits' => $item->hits,
			'core_version' => $item->version,
			'core_ordering' => 0,
			'core_metakey' => $item->metakey,
			'core_metadesc' => $item->metadesc,
			'core_catid' => $item->parent_id,
			'core_xreference' => $item->xreference,
		);

		$tags = $validData['tags'];

		if ($tags)
		{
			$tagsHelper = new JTags;
			$tagsHelper->tagItem($id, $item->extension . '.category', $tags, $fieldMap, $isNew);
		}
	}
}
