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
 * Helper class for controllers
 *
 * @package     Joomla.Libraries
 * @subpackage  controller
 * @since       3.2
*/
class JControllerHelper
{

	const CONTROLLER_PREFIX = 0;
	const CONTROLLER_ACTIVITY = 1;
	const CONTROLLER_VIEW_FOLDER = 2;

	/*
	 * Method to parse a controller from a url
	 * Defaults to the base controllers and passes an array of options.
	 *      $options[CONTROLLER_PREFIX] is the location of the controller which defaults to the core libraries (referenced as 'j'
	 *      and then the named folder within the component entry point file.
	 *      $options[CONTROLLER_ACTIVITY] is the name of the controller file,
	 *      $options[CONTROLLER_VIEW_FOLDER] is the name of the folder found in the component controller folder for controllers
	 *      not prefixed with J.
	 *      Additional options maybe added to parameterise the controller.
	 *
	 * @param  JApplication  $app  A JApplication object (could be JApplication or JApplicationWeb)
	 *
	 * @return  JController  A JController object
	 *
	 * @since  3.2
	 */

	public function parseController($app)
	{

		if ($controllerTask = $app->input->get('controller'))
		{
			// Temporary solution
			if (strpos($controllerTask, '/') !== false)
			{
				$tasks = explode('/', $controllerTask);
			}
			else
			{
				$tasks = explode('.', $controllerTask);
			}
		}
		else
		{
			// Checking for old MVC task
			$task = $app->input->get('task');

			// Toolbar expects old style but we are using new style
			// Remove when toolbar can handle either directly
			if (strpos($task, '/') !== false)
			{
				$tasks = explode('/', $task);
			}
			elseif (!empty($task))
			{
				$tasks = explode('.', $task);
			}
			else
			{
				// In the absence of a named controller default to display.
				$tasks = array('j', 'display');
			}
		}

		if (empty($tasks[self::CONTROLLER_PREFIX]) || $tasks[self::CONTROLLER_PREFIX] == 'j')
		{
			$location = 'J';
		}
		else
		{
			$location = ucfirst(strtolower($tasks[self::CONTROLLER_PREFIX]));

			// Load classes
			JLoader::registerPrefix($location, JPATH_COMPONENT_ADMINISTRATOR);
			JLoader::registerPrefix($location . 'site', JPATH_COMPONENT);
		}

		if (empty($tasks[self::CONTROLLER_ACTIVITY]))
		{
			$activity = 'Display';
		}
		else
		{
			$activity = ucfirst(strtolower($tasks[self::CONTROLLER_ACTIVITY]));
		}

		$view = '';

		if (empty($tasks[self::CONTROLLER_VIEW_FOLDER]) && $location != 'J')
		{
			$view = ucfirst(strtolower($app->input->get('view')));
		}
		elseif ($location != 'J')
		{
			$view = ucfirst(strtolower($tasks[self::CONTROLLER_VIEW_FOLDER]));
		}

		$controllerName = $location .  'Controller' . $view . $activity;

		if (!class_exists($controllerName))
		{
			// Log here? Or should it 404?
			$controllerName = 'JControllerDisplay';
			//return false;
		}

		$controller = new $controllerName;
		$controller->options = array();
		$controller->options = $tasks;

		return $controller;
	}
}