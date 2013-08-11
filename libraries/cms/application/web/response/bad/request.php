<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

/**
 * 400 Bad Request web Response class.
 *
 * @package     Joomla.Libraries
 * @subpackage  Application
 * @since       3.2
 */
class JApplicationWebResponseBadRequest extends JApplicationWebResponse
{
	/**
	 * @var    string  Response HTTP status code.
	 * @since  3.2
	 */
	protected $status = '400 Bad Request';
}
