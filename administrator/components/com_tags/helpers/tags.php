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
 * Tags helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_tags
 * @since       3.1
 */
JLog::add('helpers/TagsHelper is deprecated. Use helper/TagsHelperTags instead.', JLog::WARNING, 'deprecated');
include_once JPATH_ADMINISTRATOR . '/components/com_tags/helper/tags.php';

