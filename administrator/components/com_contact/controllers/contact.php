<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Controller for a single contact
 *
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 * @since       1.6
 */
class ContactControllerContact extends JControllerForm
{
	/**
	 * Method override to check if you can add a new record.
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
		$categoryId = JArrayHelper::getValue($data, 'catid', $this->input->getInt('filter_category_id'), 'int');
		$allow = null;

		if ($categoryId)
		{
			// If the category has been passed in the URL check it.
			$allow = $user->authorise('core.create', $this->option . '.category.' . $categoryId);
		}

		if ($allow === null)
		{
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd($data);
		}
		else
		{
			return $allow;
		}
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$categoryId = 0;

		if ($recordId)
		{
			$categoryId = (int) $this->getModel()->getItem($recordId)->catid;
		}

		if ($categoryId)
		{
			// The category has been set. Check the category permissions.
			return JFactory::getUser()->authorise('core.edit', $this->option . '.category.' . $categoryId);
		}
		else
		{
			// Since there is no asset tracking, revert to the component permissions.
			return parent::allowEdit($data, $key);
		}
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   2.5
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Contact', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_contact&view=contacts' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
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
		$id = $item->get('id');
		$created_date = $item->created;
		$modified_date = $item->modified;
		$publish_up = $item->publish_up;
		$publish_down = $item->publish_down;
		$title = $item->name;
		$language = $item->language;

		$tags = $validData['tags'];
		$tagsCount = count($tags);

		// Unset old tags, not rise error on save.
		$oldTags = $tags['old'];
		unset ($tags['old']);

		if ($tagsCount > 1)
		{
			$tagsHelper = new JTags;
			$tagsHelper->tagItem($id, 'com_contact.contact', $tags);
		}
		elseif ($tagsCount == 1 && !empty($oldTags))
		{
			// Special case when all tags are unset
			$tagsHelper = new JTags;			
			if (strpos($oldTags, ','))
			{
				$oldTags = explode(',', $oldTags); 			
				foreach ($oldTags as $tag)
				{
					$tagsHelper->unTagItems(array($id), 'com_contact.contact', $tag);
				}			
			}
			else 
			{
				
				$tagsHelper->unTagItems(array($id), 'com_contact.contact',$oldTags);
			}
		}
