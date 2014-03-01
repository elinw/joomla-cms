<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_checkin
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Checkin Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_checkin
 * @since       1.6
 */
class CheckinController extends JControllerLegacy
{
	/**
	 * @var    string  The default view.
	 * @since  1.6
	 * @deprecated  4.0
	 */
	protected $default_view = 'checkin';

	/**
	 * Method to display the view.
	 *
	 * @param   boolean      $cachable   If true, the view output will be cached
	 * @param   array        $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  This object to support chaining.
	 *
	 * @since   1.5
	 * @deprecated  4.0
	 */
	public function display($cachable = false, $urlparams = false)
	{

		include_once JPATH_ADMINISTRATOR . '/components/com_checkin/controller/checkin/display.php';
		$controller = new CheckinControllerCheckinDisplay;

		return $controller->execute();

	}

}
