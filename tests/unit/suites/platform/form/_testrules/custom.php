<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Form Rule class for the Joomla Framework.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormRuleCustom extends JFormRule
{
	/**
	 * The regular expression to use in testing a form field value.
	 *
	 * @var        string
	 * @since    11.1
	 */
	protected $regex = '^custom';

	/**
	 * The regular expression modifiers to use when testing a form field value.
	 *
	 * @var        string
	 * @since    11.1
	 */
	protected $modifiers = 'i';
}
