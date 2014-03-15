<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Tags view class for the Tags package.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_tags
 * @since       3.1
 */
class TagsController extends JControllerLegacy
{

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  This object to support chaining.
	 *
	 * @since   3.1
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Set the default view name and format from the Request.
		$vName = $this->input->get('view', 'tags');

		JLog::add('TagsController is deprecated. Use JControllerDisplay or JControllerDisplayform instead.', JLog::WARNING, 'deprecated');

		if (ucfirst($vName) == 'Tags')
		{
			$controller = new JControllerDisplay;
		}
		elseif (ucfirst($vName) == 'Tag')
		{
			$controller = new JControllerDisplayform;
		}

		return $controller->execute();
	}
}
