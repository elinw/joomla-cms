<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tabstate');

//$input = JFactory::getApplication()->input;

if (!JFactory::getUser()->authorise('core.manage', 'com_tags'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Load classes
JLoader::registerPrefix('Tags', JPATH_COMPONENT_ADMINISTRATOR);

// Tell the browser not to cache this page.
JResponse::setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT', true);

// Application
$app = JFactory::getApplication();

// Set a fallback view
if (!$app->input->get('view'))
{
	$app->input->set('view', 'tags');
}

// Create the controller
$controllerHelper = new JControllerHelper();
$controller = $controllerHelper->parseController($app);

$controller->prefix = 'Tags';

// Perform the Request task
$controller->execute();
