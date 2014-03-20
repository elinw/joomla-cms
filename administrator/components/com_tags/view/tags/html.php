<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View for the global configuration
 *
 * @package     Joomla.Administrator
 * @subpackage  com_tags
 * @since       3.2
 */
class TagsViewTagsHtml extends JViewHtmlCmslist
{
	/**
	 * @var     integer  The primary key of the current item, 0 for a new item
	 * @since   3.3
	 */
	protected 	$pk = 0;

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since  3.2
	 */
	protected function addToolbar()
	{
		$canDo = TagsHelperTags::getActions(0, 0, 'com_tags');

		JToolbarHelper::title(JText::_('COM_TAGS_MANAGER_TAGS'), 'tag');

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('j.displayform.tag.edit');
		}


		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::editList('j.displayform.tag.edit','JTOOLBAR_EDIT',true);
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('j.updatestatelist.published', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('j.updatestatelist.unpublished', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::checkin('j.checkin');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'j.delete.tags', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('j.updatestatelist.trashed', 'JTOOLBAR_TRASH');
		}

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_tags');
		}

		JToolbarHelper::help('JHELP_TAGS_MANAGER');

		JHtmlSidebar::setAction('index.php?option=com_tags&view=tags');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
		);

		JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_ACCESS'),
				'filter_access',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);

		JHtmlSidebar::addFilter(
		JText::_('JOPTION_SELECT_LANGUAGE'),
		'filter_language',
		JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
		);

		$this->sidebar = JHtmlSidebar::render();
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
				'ordering' => JText::_('JGRID_HEADING_ORDERING'),
				'a.state' => JText::_('JSTATUS'),
				'access' => JText::_('JGRID_HEADING_ACCESS'),
				'id' => JText::_('JGRID_HEADING_ID')
		);
	}
}