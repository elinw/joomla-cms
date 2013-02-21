<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Weblinks list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 * @since       1.6
 */
class WeblinksControllerWeblinks extends JControllerAdmincontent
{
	/*
	 * @var  string Model
	 * @since  3.1
	*/
	protected $name = 'Weblink';
	/*
	 * @var  string   Model prefix
	 * @since  3.1
	 */
	protected $prefix = 'WeblinksModel';
	/**
	 * The URL option for the component.
	 *
	 * @var    string
	 * @since  3.1
	 */
	protected $option = 'com_weblinks';
	/**
	 * Proxy for getModel.
	 * @since   1.6
	 */
	public function getModel($name = 'Weblink', $prefix = 'WeblinksModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

}
