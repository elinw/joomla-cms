<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


/**
 * Abstract JSON service class.
 *
 * @package     Joomla.Libraries
 * @subpackage  Service
 * @since       3.2
 */
abstract class JServiceJson extends Service
{
	/**
	 * Create a new WebResponse object by type.
	 *
	 * @param   string  $type  The type of web response object to create.
	 *
	 * @return  WebResponse
	 *
	 * @since   3.2
	 * @throws  InvalidArgumentException
	 */
	protected function createResponse($type)
	{
		$response = parent::createResponse($type);

		$response->setContentType('application/json');

		return $response;
	}

	/**
	 * Set a WebResponse object to the application with a JSON encoded body.  The second argument can be used to
	 * set the response status and code.  If none is set 200 OK will be used.
	 *
	 * @param   mixed   $body  The value to have JSON encoded to the body of the response object.
	 * @param   string  $type  The type of web response object to create.  Defaults to 200 OK.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	protected function setResponse($body, $type = 'Ok')
	{
		parent::setResponse(json_encode($body), $type);
	}
}
