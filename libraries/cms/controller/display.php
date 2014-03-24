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
class JControllerDisplay extends JControllerCmsbase
{
	/*
	 * Prefix for the view and model classes
	 *
	 * @var  string
	 */
	public $prefix = 'Content';

	/*
	 * @var boolean  If true, the view output will be cached
	 */

	public $cacheable = false;

	/*
	 * An array of safe url parameters and their variable types
	 *
	 * @var  array
	 * @note  Ror valid values see JFilterInput::clean().
	 */
	public $urlparams = array();

	/**
	 * @return  mixed  A rendered view or true
	 *
	 * @since   3.2
	 */
	public function execute()
	{
		// Get the application
		$app = $this->getApplication();

		!$app->isAdmin() ? : $this->permission = 'core.manage';

		// Get the document object.
		$document     = JFactory::getDocument();

		$componentFolder = $this->input->getWord('option', 'com_content');
		$this->viewName     = $this->input->getWord('view', 'articles');
		$viewFormat   = $document->getType();
		$layoutName   = $this->input->getWord('layout', 'default');

		// Register the layout paths for the view
		$paths = new SplPriorityQueue;
		$jpath = $app->isAdmin() ? JPATH_ADMINISTRATOR : JPATH_SITE;
		$paths->insert($jpath . '/components/' . $componentFolder . '/view/' . $this->viewName . '/tmpl', 'normal');
		$viewClass  = $this->prefix . 'View' . ucfirst($this->viewName) . ucfirst($viewFormat);
		$modelClass = $this->prefix . 'Model' . ucfirst($this->viewName);

		if (class_exists($viewClass))
		{
			$model = new $modelClass;

			// Access check.
			if (!empty($this->permission) && !JFactory::getUser()->authorise($this->permission, $model->getState('component.option')))
			{
				$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');

				return;
			}

			$view = new $viewClass($model, $paths);
//var_dump($view);
			$view->setLayout($layoutName);

			// Push document object into the view.
			$view->document = $document;

			// Reply for service requests
			if ($viewFormat == 'json')
			{

				return $view->render();
			}

			// Render view.
			echo $view->render();
		}

		return true;
	}
}