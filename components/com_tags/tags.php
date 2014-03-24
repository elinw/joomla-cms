<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Load classes
JLoader::registerPrefix('Tags', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::registerPrefix('Tagssite', JPATH_COMPONENT_SITE);

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

if ($controller !== false)
{
	$controller->prefix = 'Tagssite';
}

try
{
	// Perform the Request task
	$controller->execute();
}
catch (RuntimeException $e)
{
	$app->enqueueMessage($e->getMessage(), 'error');
}
