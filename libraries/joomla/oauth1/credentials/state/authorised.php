<?php
/**
 * @package     Joomla.Platform
 * @subpackage  OAuth1
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * OAuth Authorised Credentials class for the Joomla Platform
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth1
 * @since       12.1
 */
class JOAuth1CredentialsStateAuthorised extends JOAuth1CredentialsState
{
	/**
	 * Method to authorise the credentials.  This will persist a temporary credentials set to be authorised by
	 * a resource owner.
	 *
	 * @param   integer  $resourceOwnerId  The id of the resource owner authorizing the temporary credentials.
	 * @param   integer  $lifetime         How long the permanent credentials should be valid (defaults to forever).
	 *
	 * @return  JOAuth1CredentialsState
	 *
	 * @since   12.3
	 * @throws  LogicException
	 */
	public function authorise($resourceOwnerId, $lifetime = 0)
	{
		throw new LogicException('Only temporary credentials can be authorised.');
	}

	/**
	 * Method to convert a set of authorised credentials to token credentials.
	 *
	 * @return  JOAuth1CredentialsState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function convert()
	{
		// Setup the properties for the credentials.
		$this->properties['callback_url'] = '';
		$this->properties['verifier_key'] = '';
		$this->properties['key'] = $this->randomKey();
		$this->properties['secret'] = $this->randomKey(true);
		$this->properties['type'] = JOAuth1Credentials::TOKEN;

		// Persist the object in the database.
		$this->update();

		return new JOAuth1CredentialsStateToken($this->db, $this->properties);
	}

	/**
	 * Method to deny a set of temporary credentials.
	 *
	 * @return  JOAuth1CredentialsState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function deny()
	{
		throw new LogicException('Only temporary credentials can be denied.');
	}

	/**
	 * Method to initialise the credentials.  This will persist a temporary credentials set to be authorised by
	 * a resource owner.
	 *
	 * @param   string   $clientKey    The key of the client requesting the temporary credentials.
	 * @param   string   $callbackUrl  The callback URL to set for the temporary credentials.
	 * @param   integer  $lifetime     How long the credentials are good for.
	 *
	 * @return  JOAuth1CredentialsState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function initialise($clientKey, $callbackUrl, $lifetime = 3600)
	{
		throw new LogicException('Only new credentials can be initialised.');
	}

	/**
	 * Method to revoke a set of token credentials.
	 *
	 * @return  JOAuth1CredentialsState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function revoke()
	{
		throw new LogicException('Only token credentials can be revoked.');
	}
}
