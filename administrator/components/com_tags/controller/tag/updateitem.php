<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Save Controller for tags
 *
 * @package     Joomla.Administrator
 * @subpackage  com_tags
 * @since       3.2
*/
class TagsControllerTagUpdateitem extends JControllerUpdate
{
	const CONTROLLER_PREFIX = 0;
	const CONTROLLER_ACTIVITY = 1;
	const CONTROLLER_VIEW_FOLDER = 2;
	const CONTROLLER_OPTION = 3;

	/**
	 * Application object - Redeclared for proper typehinting
	 *
	 * @var    JApplicationCms
	 * @since  3.2
	 */
	protected $app;

	/*
	 * Option to send to the model.
	*
	* @var  array
	*/
	public $options;

	/**
	 * Method to save tags.
	 *
	 * @return  mixed  Calls $app->redirect() for all cases except JSON
	 *
	 * @since   3.2
	 */
	public function execute()
	{
		parent::execute();

		// Must load after serving service-requests
		$form  = $this->model->getForm();

		// Validate the posted data.
		$return = $this->model->validate($form, $this->data);

		// Check for validation errors.
		if ($return === false)
		{
			 // The validate method enqueued all messages for us, so we just need to redirect back.

			// Save the data in the session.
			$this->app->setUserState('com_tags.tag.data', $data);

			// Redirect back to the edit screen.
			$this->app->redirect(JRoute::_('index.php?option=com_tags&controller=j.displayform.tag, false'));
		}

		$data = $return;
		$option = $this->input->getString('task');

		if (empty($this->options))
		{
			$option = $this->input->getString('controller');
			$this->options = explode('.', $option);
		}

		// Attempt to save the data.
		// Set the redirect based on the task.
		switch ($this->options[self::CONTROLLER_OPTION])
		{
			case 'apply':
				$return = $this->model->save($data);
				// Set the success message.

				if ($return !== false)
				{
					$this->app->enqueueMessage(JText::_('COM_TAGS_SAVE_SUCCESS'));
					$this->postSaveHook($this->model, $this->model->table);
					if ($data[id] != 0)
					{
						$this->app->redirect(JRoute::_('index.php?option=com_tags&view=tag&layout=edit&id='.$this->input->getInt('id'), false));
					}
					else
					{
						$this->app->redirect(JRoute::_('index.php?option=com_tags&view=tag&layout=edit&id=' . $this->model->table->id, false));
					}

					break;
				}

			case 'save2new':
				$return = $this->model->save($data);
				if ($return !== false)
				{
					$this->app->enqueueMessage(JText::_('COM_TAGS_SAVE_SUCCESS'));
					$this->postSaveHook($this->model, $this->model->table);
					$this->app->redirect(JRoute::_('index.php?option=com_tags&view=tag&controller=j.displayform.tag', false));
					break;
				}

			case 'save2copy':

				//  Change the id to 0 to have it treated as a create not an update.
				$data['id'] = 0;
				$this->app->setUserState('com_tags.tag.data', $data);

				$return = $this->model->save($data);
				$this->postSaveHook($this->model, $this->model->table);
				$this->app->redirect(JRoute::_('index.php?option=com_tags&view=tag&layout=edit&id=' . $this->model->table->id, false));

				break;

			case 'save':
			default:
				$return = $this->model->save($data);
				if ($return !== false)
				{
					$this->app->enqueueMessage(JText::_('COM_TAGS_SAVE_SUCCESS'));
					$this->postSaveHook($this->model, $this->model->table);
					$this->app->redirect(JRoute::_('index.php?option=com_tags&view=tags', false));
					break;
				}
		}

		// Check the return value.
		if ($return === false)
		{
			/*
			 * The save method enqueued all messages for us, so we just need to redirect back.
			*/

			// Save the data in the session.
			$this->app->setUserState('com_tags.tag.data', $data);

			// Save failed, go back to the screen and display a notice.
			$this->app->redirect(JRoute::_('index.php?option=com_tags&controller=j.display.tag', false));
		}

	}
	/**
	 * Function that allows child controller access to model data
	 * after the data has been saved.
	 *
	 * @param   JModelLegacy  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 *
	 * @return  void
	 *
	 * @since   3.3
	 */
	protected function postSaveHook($model, $validData = array())
	{
	}
}
