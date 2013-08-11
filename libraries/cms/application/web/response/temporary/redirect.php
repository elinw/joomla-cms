<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

/**
 * 307 Temporary Redirect web Response class.
 *
 * @package     Joomla.Libraries
 * @subpackage  Application
 * @since       3.2
 */
class JApplicationWebResponseTemporaryRedirect extends JApplicationWebResponse
{
	/**
	 * @var    string  Response HTTP status code.
	 * @since  3.2
	 */
	protected $status = '307 Temporary Redirect';
}
