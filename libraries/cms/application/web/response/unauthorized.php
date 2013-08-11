<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */



/**
 * 401 Unauthorized web Response class.
 *
 * @package     Joomla.Libraries
 * @subpackage  Application
 * @since       3.2
 */
class JApplicationWebResponseUnauthorized extends JApplicationWebResponse
{
	/**
	 * @var    string  Response HTTP status code.
	 * @since  3.2
	 */
	protected $status = '401 Unauthorized';
}
