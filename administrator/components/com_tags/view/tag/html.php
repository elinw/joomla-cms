<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View for tag editing
 *
 * @package     Joomla.Administrator
 * @subpackage  com_tags
 * @since       3.5
 */
class TagsViewTagHtml extends JViewHtmlCmsform
{

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   3.5
	 */
	protected function addToolbar()
	{
		$app = JFactory::getApplication();
		$app->input->set('hidemainmenu', true);
		$this->id = $app->input->getInt('id');

		$canDo = JHelperContent::getActions(0, 0, 'com_tags');

		JToolbarHelper::title(JText::sprintf('COM_TAGS_MANAGER_TAG', JText::_($this->name)), 'tag');

		// If not checked out, can save the item.
		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::apply('tags.updateitem.tag.apply');
			JToolbarHelper::save('tags.updateitem.tag.save');
		}
		else
		{
			JToolbarHelper::apply('j.create.tag');
			JToolbarHelper::save('j.create.tag');

		}

		if ($canDo->get('core.edit') && !empty($this->id))
		{
			JToolbarHelper::save2new('tags.updateitem.tag.save2new');
			JToolbarHelper::save2copy('tags.updateitem.tag.save2copy');
		}

		JToolbarHelper::cancel('j.cancel', 'JTOOLBAR_CLOSE');
		JToolbarHelper::divider();

		// Get the help information for tag edit.
		JToolbarHelper::help('JHELP_EXTENSIONS_TAGS_MANAGER_EDIT');

		$lang = JFactory::getLanguage();
		JHtmlSidebar::setAction('index.php?option=com_tags');

	}

}
