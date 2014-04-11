<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Joomla.Libraries
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View for the global configuration
 *
 * @package     Joomla.Libraries
 * @subpackage  view
 * @since       3.2
 */
class JViewHtmlCmsitem extends JViewHtmlCms
{
	public $state;

	/*
	 * The item object
	*
	* @var stdObject
	*/
	public  $item;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  Layout
	 *
	 * @return  void
	 *
	 */
	public function render()
	{
		try
		{
			$user = JFactory::getUser();
			$app  = JFactory::getApplication();
			$lang = JFactory::getLanguage();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		if ($app->isAdmin())
		{
			$this->addToolbar();
			$this->addSubmenu();
		}

		// This allows simple processing of the item data
		$this->doCustomProcessing($this->item);

		return parent::render();
	}


}