#!/usr/bin/php
<?php
/**
 * @package     Joomla.CLI
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Finder CLI Bootstrap
 *
 * Run the framework bootstrap with a couple of mods based on the script's needs
 */

// We are a valid entry point.
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

// Setup the base path related constant.
define('JPATH_BASE', dirname(dirname(dirname(__FILE__))));

// Grab the site defines
require JPATH_BASE.'/includes/defines.php';

// Import the cms version library if necessary.
if (!class_exists('JVersion')) {
    require JPATH_ROOT.'/includes/version.php';
}

// Get the framework.
require JPATH_BASE.'/libraries/import.php';
jimport('joomla.application.menu');
jimport('joomla.user.user');
jimport('joomla.environment.uri');
jimport('joomla.environment.request');
jimport('joomla.html.html');
jimport('joomla.utilities.utility');
jimport('joomla.event.event');
jimport('joomla.event.dispatcher');
jimport('joomla.language.language');
jimport('joomla.log.log');
jimport('joomla.utilities.string');
jimport('joomla.plugin.helper');
jimport('joomla.utilities.date');
jimport('joomla.plugin.plugin');
jimport('joomla.registry.registry');

// Import the JCli class from the platform.
jimport('joomla.application.cli');

// Import the configuration.
require_once JPATH_CONFIGURATION.'/configuration.php';

// System configuration.
$config = new JConfig();

// Configure error reporting.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set error handling levels
JError::setErrorHandling(E_ALL, 'echo');

// Initialize the application.
$app = JFactory::getApplication('site');

/**
 * A command line cron job to run the Finder indexer.
 *
 * @package     Joomla.CLI
 * @subpackage  com_finder
 * @since       2.5
 */
class FinderCli extends JCli
{
	/**
	 * Start time for the index process
	 *
	 * @var    string
	 * @since  2.5
	 */
	private $_time = null;

	/**
	 * Start time for each batch
	 *
	 * @var    string
	 * @since  2.5
	 */
	private $_qtime = null;

	/**
	 * Entry point for Finder CLI script
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function execute()
	{
		// Print a blank line.
		$this->out('FINDER INDEXER');
		$this->out('============================');
		$this->out();

		$this->_index();

		// Print a blank line at the end.
		$this->out();
	}

	/**
	 * Run the indexer
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	private function _index()
	{
		// initialize the time value
		$this->_time = microtime(true);

		// import library dependencies
		require_once(JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/indexer.php');
		jimport('joomla.application.component.helper');

		// fool the system into thinking we are running as JSite with Finder as the active component
		JFactory::getApplication('site');
		$_SERVER['HTTP_HOST'] = 'domain.com';
		define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR.'/components/com_finder');

		// Disable caching.
		$config = JFactory::getConfig();
		$config->set('caching', 0);
		$config->set('cache_handler', 'file');

		// Reset the indexer state.
		FinderIndexer::resetState();

		// Import the finder plugins.
		JPluginHelper::importPlugin('finder');

		// Starting Indexer.
		$this->out('Starting Indexer', true);

		// Trigger the onStartIndex event.
		JDispatcher::getInstance()->trigger('onStartIndex');

		// Remove the script time limit.
		@set_time_limit(0);

		// Get the indexer state.
		$state = FinderIndexer::getState();

		// Setting up plugins.
		$this->out('Setting up Finder plugins', true);

		// Trigger the onBeforeIndex event.
		JDispatcher::getInstance()->trigger('onBeforeIndex');

		// Startup reporting.
		$this->out('Setup '.$state->totalItems.' items in '.round(microtime(true) - $this->_time, 3).' seconds.', true);

		// Get the number of batches.
		$t = (int)$state->totalItems;
		$c = (int)ceil($t / $state->batchSize);
		$c = $c === 0 ? 1 : $c;

		// Process the batches.
		for ($i = 0; $i < $c; $i++)
		{
			// Set the batch start time.
			$this->_qtime = microtime(true);

			// Reset the batch offset.
			$state->batchOffset = 0;

			// Trigger the onBuildIndex event.
			JDispatcher::getInstance()->trigger('onBuildIndex');

			// Batch reporting.
			$this->out(' * Processed batch '.($i+1).' in '.round(microtime(true) - $this->_qtime, 3).' seconds.', true);
		}

		// Total reporting.
		$this->out('Total Processing Time: '.round(microtime(true) - $this->_time, 3).' seconds.', true);

		// Reset the indexer state.
		FinderIndexer::resetState();
	}
}

// Instantiate the application object, passing the class name to JCli::getInstance
// and use chaining to execute the application.
JCli::getInstance('FinderCli')->execute();