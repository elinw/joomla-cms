<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Tags Component Controller
 *
 * @package     Joomla.Site
 * @subpackage  com_tags
 * @since       3.1
 */
class TagsController extends JControllerLegacy
{

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  If true, the view output will be cached
	 * @param   array    An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  This object to support chaining.
	 *
	 * @since   3.1
	 */
	public function display($cachable = true, $urlparams = false)
	{
		// Set the default view name and format from the Request.
		$vName = $this->input->get('view', 'tags');

		JLog::add('TagsController is deprecated. Use JControllerDisplay or JControllerDisplayform instead.', JLog::WARNING, 'deprecated');

		if (ucfirst($vName) == 'Tags')
		{
			$controller = new JControllerDisplaylist;
		}
		elseif (ucfirst($vName) == 'Tag')
		{
			$controller = new JControllerDisplaylist;
		}

		return $controller->execute();

	}
}
