<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Content component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_content
 * @since       1.6
 */
class ContentHelper extends JHelperContent
{
	public static $extension = 'com_content';

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string	$vName	The name of the active view.
	 *
	 * @return  void
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('JGLOBAL_ARTICLES'),
			'index.php?option=com_content&view=articles',
			$vName == 'articles'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CONTENT_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_content',
			$vName == 'categories');
		JHtmlSidebar::addEntry(
			JText::_('COM_CONTENT_SUBMENU_FEATURED'),
			'index.php?option=com_content&view=featured',
			$vName == 'featured'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   integer  The category ID.
	 * @param   integer  The article ID.
	 *
	 * @return  JObject
	 * @since   1.6
	 */
	public static function getActions($categoryId = 0, $id = 0,  $assetName = '')
	{
		if (empty($id) && empty($categoryId))
		{
			$assetName = 'com_content';
		}
		elseif (empty($id))
		{
			$assetName = 'com_content.category.'.(int) $categoryId;
		}
		else
		{
			$assetName = 'com_content.article.'.(int) $id;
		}

		return parent::getActions($categoryId, $id, $assetName);
	}
}
