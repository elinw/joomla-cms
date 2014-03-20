<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Joomla.Libraries
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Base Display Controller
 *
 * @package     Joomla.Libraries
 * @subpackage  controller
 * @since       3.2
*/
class JControllerUpdatestatelist extends JControllerCmsbase
{

	/*
	 * Prefix for the view and model classes
	 *
	 * @var  string
	 */
	public $prefix;

	/*
	 * Permission needed for the action
	*
	* @var  string
	*/
	public $permission = 'core.edit.state';


	/*
	 * Optional values needed for the model
	 * Note that we need to support some values twice for legacy reasons.
	 *
	 *  @var  array
	 */
	public  $stateOptions = array('published' => 1, 'unpublished' => 0, 'archived' => 2,
				'trashed' => -2, 'reported' => -3, 'publish' => 1, 'unpublish' => 0);

	/**
	 * @return  mixed  A rendered view or true
	 *
	 * @since   3.2
	 */
	public function execute()
	{
		parent::execute();

		$ids = $this->input->get('cid', array(), 'array');

		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
		}
		else
		{
			$modelClassName = ucfirst($this->prefix) . 'Model' . ucfirst($this->viewName);
			$model = new $modelClassName;
			$newState = $this->stateOptions[$this->options[parent::CONTROLLER_CORE_OPTION]];

			// Access check.
			if (!JFactory::getUser()->authorise($this->permission, $model->getState('component.option')))
			{
				$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');

				return;
			}

			// Check in the items.
			$this->app->enqueueMessage(JText::plural('JLIB_CONTROLLER_N_ITEMS_PUBLISHED', $model->publish($ids, $newState)));
		}

		$this->app->redirect('index.php?option=' . $this->input->get('option', 'com_cpanel'));

	}
}